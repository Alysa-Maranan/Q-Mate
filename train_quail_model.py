#!/usr/bin/env python3
"""Q-MATE 2-class quail classifier trainer.

Required dataset:
datasets/quail_images/
    japanese_quail_group/  # Japanese Quail + Japanese Coturnix Crossbreed (Taiwan)
    pharaoh_quail/

Run from the project root: python train_quail_model.py
"""
import os
import sys
import json
from datetime import datetime

import numpy as np
import tensorflow as tf
from tensorflow import keras
from tensorflow.keras import layers, models
from PIL import Image, UnidentifiedImageError
from sklearn.model_selection import train_test_split
from sklearn.utils.class_weight import compute_class_weight
from sklearn.metrics import confusion_matrix, classification_report

DATASET_PATH = "datasets/quail_images"
MODEL_OUTPUT_PATH = "public/models"
MODEL_FILENAME = "quail_breed_model.keras"
METADATA_FILENAME = "model_metadata.json"

BREEDS = ["japanese_quail_group", "pharaoh_quail"]
DISPLAY_NAMES = {
    "japanese_quail_group": "Japanese Quail Group",
    "pharaoh_quail": "Pharaoh Quail",
}
IMAGE_SIZE = (224, 224)
BATCH_SIZE = 8
HEAD_EPOCHS = 30
FINE_TUNE_EPOCHS = 30
FINE_TUNE_LAYERS = 20
RANDOM_SEED = 42
VALIDATION_SPLIT = 0.20
EXTENSIONS = (".jpg", ".jpeg", ".png", ".webp", ".bmp")

np.random.seed(RANDOM_SEED)
tf.random.set_seed(RANDOM_SEED)


def image_files(folder):
    if not os.path.isdir(folder):
        return []
    return sorted(
        name for name in os.listdir(folder)
        if name.lower().endswith(EXTENSIONS)
        and os.path.isfile(os.path.join(folder, name))
    )


def check_dataset():
    print("=" * 60)
    print("DATASET VERIFICATION")
    print("=" * 60)
    for breed in BREEDS:
        folder = os.path.join(DATASET_PATH, breed)
        if not os.path.isdir(folder):
            print(f"ERROR: Missing folder: {folder}")
            return False
        count = len(image_files(folder))
        print(f"{DISPLAY_NAMES[breed]}: {count} images")
        if count < 2:
            print(f"ERROR: Add at least 2 images to {folder}")
            return False
    return True


def load_images():
    images, labels = [], []
    class_indices = {name: index for index, name in enumerate(BREEDS)}

    for breed in BREEDS:
        folder = os.path.join(DATASET_PATH, breed)
        print(f"\nLoading {DISPLAY_NAMES[breed]}...")
        for filename in image_files(folder):
            path = os.path.join(folder, filename)
            try:
                with Image.open(path) as source:
                    img = source.convert("RGB").resize(
                        IMAGE_SIZE, Image.Resampling.LANCZOS
                    )
                    images.append(np.asarray(img, dtype=np.float32))
                    labels.append(class_indices[breed])
            except (UnidentifiedImageError, OSError, ValueError) as exc:
                print(f"WARNING: Skipping unreadable image {path}: {exc}")

    if not images:
        raise RuntimeError("No readable images were loaded.")

    y = np.asarray(labels, dtype=np.int32)
    counts = np.bincount(y, minlength=len(BREEDS))
    for i, count in enumerate(counts):
        print(f"Loaded {BREEDS[i]}: {int(count)}")
        if count < 2:
            raise RuntimeError(f"Class {BREEDS[i]} has fewer than 2 readable images.")

    x = np.asarray(images, dtype=np.float32)
    print(f"Images shape: {x.shape}")
    return x, y, class_indices


def split_data(x, y):
    x_train, x_val, y_train, y_val = train_test_split(
        x, y, test_size=VALIDATION_SPLIT,
        random_state=RANDOM_SEED, stratify=y
    )
    print(f"\nTraining images: {len(x_train)}")
    print(f"Validation images: {len(x_val)}")
    return x_train, x_val, y_train, y_val


def get_class_weights(y_train):
    classes = np.unique(y_train)
    weights = compute_class_weight(
        class_weight="balanced", classes=classes, y=y_train
    )
    result = {int(k): float(v) for k, v in zip(classes, weights)}
    print("Class weights:", result)
    return result


def build_model():
    inputs = keras.Input(shape=IMAGE_SIZE + (3,), name="image")
    x = keras.Sequential([
        layers.RandomFlip("horizontal"),
        layers.RandomRotation(0.04),
        layers.RandomZoom(0.08),
        layers.RandomContrast(0.08),
    ], name="data_augmentation")(inputs)
    x = keras.applications.mobilenet_v2.preprocess_input(x)

    base = keras.applications.MobileNetV2(
        input_shape=IMAGE_SIZE + (3,), include_top=False, weights="imagenet"
    )
    base.trainable = False
    x = base(x, training=False)
    x = layers.GlobalAveragePooling2D()(x)
    x = layers.Dense(128, activation="relu")(x)
    x = layers.BatchNormalization()(x)
    x = layers.Dropout(0.35)(x)
    outputs = layers.Dense(len(BREEDS), activation="softmax", name="breed_prediction")(x)

    model = models.Model(inputs, outputs, name="Q_MATE_Quail_Breed_Classifier")
    model.compile(
        optimizer=keras.optimizers.Adam(learning_rate=3e-4),
        loss="sparse_categorical_crossentropy",
        metrics=["accuracy"],
    )
    return model, base


def callbacks():
    os.makedirs(MODEL_OUTPUT_PATH, exist_ok=True)
    path = os.path.join(MODEL_OUTPUT_PATH, MODEL_FILENAME)
    return [
        keras.callbacks.ModelCheckpoint(
            path, monitor="val_accuracy", mode="max",
            save_best_only=True, verbose=1
        ),
        keras.callbacks.EarlyStopping(
            monitor="val_loss", mode="min", patience=8,
            restore_best_weights=True, verbose=1
        ),
        keras.callbacks.ReduceLROnPlateau(
            monitor="val_loss", factor=0.5, patience=3,
            min_lr=1e-7, verbose=1
        ),
    ]


def train_head(model, x_train, y_train, x_val, y_val, weights):
    print("\nSTAGE 1: CLASSIFICATION HEAD")
    model.fit(
        x_train, y_train, validation_data=(x_val, y_val),
        epochs=HEAD_EPOCHS, batch_size=BATCH_SIZE,
        class_weight=weights, callbacks=callbacks(), verbose=1
    )


def fine_tune(model, base, x_train, y_train, x_val, y_val, weights):
    print("\nSTAGE 2: FINE-TUNING")
    base.trainable = True
    start = max(0, len(base.layers) - FINE_TUNE_LAYERS)
    for layer in base.layers[:start]:
        layer.trainable = False
    for layer in base.layers[start:]:
        layer.trainable = not isinstance(layer, layers.BatchNormalization)

    model.compile(
        optimizer=keras.optimizers.Adam(learning_rate=5e-6),
        loss="sparse_categorical_crossentropy",
        metrics=["accuracy"],
    )
    model.fit(
        x_train, y_train, validation_data=(x_val, y_val),
        epochs=FINE_TUNE_EPOCHS, batch_size=BATCH_SIZE,
        class_weight=weights, callbacks=callbacks(), verbose=1
    )


def evaluate(model, x_train, y_train, x_val, y_val):
    train_loss, train_acc = model.evaluate(x_train, y_train, verbose=0)
    val_loss, val_acc = model.evaluate(x_val, y_val, verbose=0)
    print(f"\nTraining accuracy: {train_acc:.2%}")
    print(f"Validation accuracy: {val_acc:.2%}")

    probabilities = model.predict(x_val, verbose=0)
    predicted = np.argmax(probabilities, axis=1)
    print("\nPER-CLASS VALIDATION RESULTS")
    print(classification_report(
        y_val, predicted, labels=[0, 1],
        target_names=[DISPLAY_NAMES[b] for b in BREEDS],
        zero_division=0
    ))
    print("CONFUSION MATRIX (rows=actual, columns=predicted)")
    print(confusion_matrix(y_val, predicted, labels=[0, 1]))
    return float(train_acc), float(val_acc)


def save_metadata(class_indices, train_acc, val_acc, dataset_size):
    metadata = {
        "classes": class_indices,
        "class_names": {str(index): breed for breed, index in class_indices.items()},
        "breeds": BREEDS,
        "display_names": DISPLAY_NAMES,
        "image_size": list(IMAGE_SIZE),
        "trained_at": datetime.now().isoformat(),
        "model_type": "MobileNetV2 Transfer Learning + Fine Tuning",
        "preprocessing": "keras.applications.mobilenet_v2.preprocess_input",
        "training_accuracy": train_acc,
        "validation_accuracy": val_acc,
        "dataset_size": int(dataset_size),
        "num_classes": len(BREEDS),
    }
    os.makedirs(MODEL_OUTPUT_PATH, exist_ok=True)
    path = os.path.join(MODEL_OUTPUT_PATH, METADATA_FILENAME)
    with open(path, "w", encoding="utf-8") as f:
        json.dump(metadata, f, indent=2, ensure_ascii=False)
    print(f"Metadata saved: {path}")


def main():
    print("Q-MATE 2-CLASS QUAIL CLASSIFIER TRAINER")
    if not check_dataset():
        sys.exit(1)

    x, y, class_indices = load_images()
    x_train, x_val, y_train, y_val = split_data(x, y)
    weights = get_class_weights(y_train)

    model, base = build_model()
    train_head(model, x_train, y_train, x_val, y_val, weights)
    fine_tune(model, base, x_train, y_train, x_val, y_val, weights)

    model_path = os.path.join(MODEL_OUTPUT_PATH, MODEL_FILENAME)
    if not os.path.isfile(model_path):
        raise RuntimeError("No best model was saved; check the training output.")

    print("\nLoading best saved model...")
    best_model = keras.models.load_model(model_path)
    train_acc, val_acc = evaluate(best_model, x_train, y_train, x_val, y_val)
    save_metadata(class_indices, train_acc, val_acc, len(x))
    print("\nTRAINING COMPLETE")
    print(f"Model: {model_path}")
    print(f"Metadata: {os.path.join(MODEL_OUTPUT_PATH, METADATA_FILENAME)}")
    print(f"Validation accuracy: {val_acc:.2%}")
    print("Test with unseen images before using this model in production.")


if __name__ == "__main__":
    main()
