#!/usr/bin/env python3
"""
Q-MATE Quail Breed Detection Model Trainer

4-class quail breed classifier using:
- MobileNetV2 Transfer Learning
- MobileNetV2 preprocessing
- Controlled data augmentation
- Class-weight balancing
- Two-stage training
- Fine-tuning
- Best validation checkpoint
- Per-breed evaluation
- Confusion matrix
"""

import os
import sys
import json
from datetime import datetime

import numpy as np
import tensorflow as tf
from tensorflow import keras
from tensorflow.keras import layers, models
from PIL import Image
from sklearn.model_selection import train_test_split
from sklearn.utils.class_weight import compute_class_weight
from sklearn.metrics import confusion_matrix, classification_report


# ============================================================
# CONFIGURATION
# ============================================================

DATASET_PATH = "datasets/quail_images"
MODEL_OUTPUT_PATH = "public/models"

BREEDS = [
    "japanese_quail",
    "japanese_coturnix_crossbreed_taiwan",
    "pharaoh_quail",
]

IMAGE_SIZE = (224, 224)
BATCH_SIZE = 8

HEAD_EPOCHS = 30
FINE_TUNE_EPOCHS = 40

# Smaller fine-tuning range is safer for a very small dataset.
FINE_TUNE_LAYERS = 20

RANDOM_SEED = 42


# ============================================================
# REPRODUCIBILITY
# ============================================================

np.random.seed(RANDOM_SEED)
tf.random.set_seed(RANDOM_SEED)


# ============================================================
# DATASET VERIFICATION
# ============================================================

def check_dataset():

    print("=" * 60)
    print("DATASET VERIFICATION")
    print("=" * 60)

    total_images = 0
    breed_counts = {}

    for breed in BREEDS:

        breed_path = os.path.join(
            DATASET_PATH,
            breed
        )

        if not os.path.exists(breed_path):

            print(
                f"❌ Folder not found: {breed_path}"
            )

            return False

        image_files = [
            f for f in os.listdir(breed_path)
            if f.lower().endswith(
                (".jpg", ".jpeg", ".png", ".webp")
            )
        ]

        count = len(image_files)

        breed_counts[breed] = count
        total_images += count

        status = "✓" if count > 0 else "✗"

        print(
            f"{status} {breed}: {count} images"
        )

    print()
    print(
        f"Total images: {total_images}"
    )

    if any(
        count < 2
        for count in breed_counts.values()
    ):

        print()
        print(
            "❌ Each breed needs at least 2 images."
        )

        return False

    return True


# ============================================================
# LOAD IMAGES
# ============================================================

def load_images_manually():

    print()
    print("=" * 60)
    print("LOADING DATASET")
    print("=" * 60)

    images = []
    labels = []

    class_indices = {
        breed: index
        for index, breed in enumerate(BREEDS)
    }

    for breed in BREEDS:

        breed_path = os.path.join(
            DATASET_PATH,
            breed
        )

        image_files = [
            f for f in os.listdir(breed_path)
            if f.lower().endswith(
                (".jpg", ".jpeg", ".png", ".webp")
            )
        ]

        print()
        print(
            f"Loading {breed}... ({len(image_files)} images)"
        )

        for img_file in image_files:

            try:

                img_path = os.path.join(
                    breed_path,
                    img_file
                )

                img = Image.open(
                    img_path
                ).convert("RGB")

                img = img.resize(
                    IMAGE_SIZE,
                    Image.Resampling.LANCZOS
                )

                img_array = np.array(
                    img,
                    dtype=np.float32
                )

                images.append(img_array)
                labels.append(
                    class_indices[breed]
                )

            except Exception as e:

                print(
                    f"  ⚠ Error loading {img_file}: {e}"
                )

    x_data = np.array(
        images,
        dtype=np.float32
    )

    y_data = np.array(
        labels,
        dtype=np.int32
    )

    y_categorical = keras.utils.to_categorical(
        y_data,
        len(BREEDS)
    )

    print()
    print(
        f"✓ Total images loaded: {len(images)}"
    )

    print(
        f"  Image shape: {x_data.shape}"
    )

    print(
        f"  Label shape: {y_categorical.shape}"
    )

    return (
        x_data,
        y_categorical,
        class_indices
    )


# ============================================================
# SPLIT DATA
# ============================================================

def split_data(
    x_data,
    y_data
):

    print()
    print("=" * 60)
    print("SPLITTING DATA")
    print("=" * 60)

    class_labels = np.argmax(
        y_data,
        axis=1
    )

    x_train, x_val, y_train, y_val = train_test_split(
        x_data,
        y_data,
        test_size=0.20,
        random_state=RANDOM_SEED,
        stratify=class_labels
    )

    print(
        f"Training samples: {len(x_train)}"
    )

    print(
        f"Validation samples: {len(x_val)}"
    )

    print()
    print("Training distribution:")

    train_labels = np.argmax(
        y_train,
        axis=1
    )

    for index, breed in enumerate(BREEDS):

        count = np.sum(
            train_labels == index
        )

        print(
            f"  {breed}: {count}"
        )

    print()
    print("Validation distribution:")

    val_labels = np.argmax(
        y_val,
        axis=1
    )

    for index, breed in enumerate(BREEDS):

        count = np.sum(
            val_labels == index
        )

        print(
            f"  {breed}: {count}"
        )

    return (
        x_train,
        x_val,
        y_train,
        y_val
    )


# ============================================================
# CLASS WEIGHTS
# ============================================================

def calculate_class_weights(
    y_train
):

    print()
    print("=" * 60)
    print("CALCULATING CLASS WEIGHTS")
    print("=" * 60)

    labels = np.argmax(
        y_train,
        axis=1
    )

    classes = np.unique(
        labels
    )

    weights = compute_class_weight(
        class_weight="balanced",
        classes=classes,
        y=labels
    )

    class_weights = {
        int(class_id): float(weight)
        for class_id, weight
        in zip(classes, weights)
    }

    for index, breed in enumerate(BREEDS):

        print(
            f"  {breed}: "
            f"{class_weights.get(index, 1.0):.4f}"
        )

    return class_weights


# ============================================================
# DATA AUGMENTATION
# ============================================================

def create_data_augmentation():

    return keras.Sequential(
        [

            layers.RandomFlip(
                "horizontal"
            ),

            layers.RandomRotation(
                0.04
            ),

            layers.RandomZoom(
                height_factor=0.08,
                width_factor=0.08
            ),

            layers.RandomContrast(
                0.08
            ),

        ],
        name="data_augmentation"
    )


# ============================================================
# BUILD MODEL
# ============================================================

def build_model():

    print()
    print("=" * 60)
    print("BUILDING MOBILE NET V2 MODEL")
    print("=" * 60)

    inputs = keras.Input(
        shape=IMAGE_SIZE + (3,),
        name="image"
    )

    augmentation = create_data_augmentation()

    x = augmentation(
        inputs,
        training=True
    )

    # Correct MobileNetV2 preprocessing.
    x = keras.applications.mobilenet_v2.preprocess_input(
        x
    )

    base_model = keras.applications.MobileNetV2(
        input_shape=IMAGE_SIZE + (3,),
        include_top=False,
        weights="imagenet"
    )

    base_model.trainable = False

    x = base_model(
        x,
        training=False
    )

    x = layers.GlobalAveragePooling2D(
        name="global_average_pooling"
    )(x)

    x = layers.Dense(
        128,
        activation="relu",
        name="dense_128"
    )(x)

    x = layers.BatchNormalization(
        name="batch_normalization"
    )(x)

    x = layers.Dropout(
        0.35,
        name="dropout"
    )(x)

    outputs = layers.Dense(
        len(BREEDS),
        activation="softmax",
        name="breed_prediction"
    )(x)

    model = models.Model(
        inputs=inputs,
        outputs=outputs,
        name="Q_MATE_Quail_Breed_Classifier"
    )

    model.compile(
        optimizer=keras.optimizers.Adam(
            learning_rate=3e-4
        ),
        loss="categorical_crossentropy",
        metrics=[
            "accuracy"
        ]
    )

    print()
    print(
        f"✓ Model created with {len(BREEDS)} output classes"
    )

    print(
        f"✓ MobileNetV2 input size: {IMAGE_SIZE}"
    )

    print(
        "✓ MobileNetV2 preprocessing enabled"
    )

    print(
        "✓ Controlled data augmentation enabled"
    )

    print(
        "✓ Class balancing enabled"
    )

    return (
        model,
        base_model
    )


# ============================================================
# CALLBACKS
# ============================================================

def create_callbacks():

    os.makedirs(
        MODEL_OUTPUT_PATH,
        exist_ok=True
    )

    best_model_path = os.path.join(
        MODEL_OUTPUT_PATH,
        "quail_breed_model.keras"
    )

    return [

        keras.callbacks.ModelCheckpoint(
            best_model_path,
            monitor="val_accuracy",
            mode="max",
            save_best_only=True,
            verbose=1
        ),

        keras.callbacks.EarlyStopping(
            monitor="val_accuracy",
            mode="max",
            patience=10,
            restore_best_weights=True,
            verbose=1
        ),

        keras.callbacks.ReduceLROnPlateau(
            monitor="val_loss",
            factor=0.5,
            patience=4,
            min_lr=1e-7,
            verbose=1
        )

    ]


# ============================================================
# STAGE 1
# ============================================================

def train_classification_head(
    model,
    x_train,
    y_train,
    x_val,
    y_val,
    class_weights
):

    print()
    print("=" * 60)
    print("STAGE 1: TRAINING CLASSIFICATION HEAD")
    print("=" * 60)

    history = model.fit(

        x_train,
        y_train,

        validation_data=(
            x_val,
            y_val
        ),

        epochs=HEAD_EPOCHS,

        batch_size=BATCH_SIZE,

        class_weight=class_weights,

        callbacks=create_callbacks(),

        verbose=1

    )

    return history


# ============================================================
# STAGE 2
# ============================================================

def fine_tune_model(
    model,
    base_model,
    x_train,
    y_train,
    x_val,
    y_val,
    class_weights
):

    print()
    print("=" * 60)
    print("STAGE 2: FINE-TUNING MOBILENETV2")
    print("=" * 60)

    base_model.trainable = False

    total_layers = len(
        base_model.layers
    )

    fine_tune_start = max(
        0,
        total_layers - FINE_TUNE_LAYERS
    )

    for layer in base_model.layers[
        fine_tune_start:
    ]:

        if isinstance(
            layer,
            layers.BatchNormalization
        ):

            layer.trainable = False

        else:

            layer.trainable = True

    trainable_count = sum(
        1
        for layer in base_model.layers
        if layer.trainable
    )

    print(
        f"MobileNetV2 total layers: {total_layers}"
    )

    print(
        f"Fine-tuning layers: {trainable_count}"
    )

    model.compile(

        optimizer=keras.optimizers.Adam(
            learning_rate=5e-6
        ),

        loss="categorical_crossentropy",

        metrics=[
            "accuracy"
        ]

    )

    history = model.fit(

        x_train,
        y_train,

        validation_data=(
            x_val,
            y_val
        ),

        epochs=FINE_TUNE_EPOCHS,

        batch_size=BATCH_SIZE,

        class_weight=class_weights,

        callbacks=create_callbacks(),

        verbose=1

    )

    return history


# ============================================================
# EVALUATION
# ============================================================

def evaluate_model(
    model,
    x_train,
    y_train,
    x_val,
    y_val
):

    print()
    print("=" * 60)
    print("FINAL MODEL EVALUATION")
    print("=" * 60)

    train_loss, train_accuracy = model.evaluate(
        x_train,
        y_train,
        verbose=0
    )

    val_loss, val_accuracy = model.evaluate(
        x_val,
        y_val,
        verbose=0
    )

    print()
    print(
        f"Training Accuracy:   {train_accuracy:.2%}"
    )

    print(
        f"Validation Accuracy: {val_accuracy:.2%}"
    )

    print(
        f"Training Loss:       {train_loss:.4f}"
    )

    print(
        f"Validation Loss:     {val_loss:.4f}"
    )

    # --------------------------------------------------------
    # Detailed validation predictions
    # --------------------------------------------------------

    predictions = model.predict(
        x_val,
        verbose=0
    )

    predicted_labels = np.argmax(
        predictions,
        axis=1
    )

    actual_labels = np.argmax(
        y_val,
        axis=1
    )

    print()
    print("=" * 60)
    print("PER-BREED VALIDATION RESULTS")
    print("=" * 60)

    report = classification_report(
        actual_labels,
        predicted_labels,
        labels=list(range(len(BREEDS))),
        target_names=BREEDS,
        zero_division=0
    )

    print()
    print(report)

    print("=" * 60)
    print("CONFUSION MATRIX")
    print("=" * 60)

    matrix = confusion_matrix(
        actual_labels,
        predicted_labels,
        labels=list(range(len(BREEDS)))
    )

    print()

    print(
        "Rows = Actual"
    )

    print(
        "Columns = Predicted"
    )

    print()

    print(
        "                "
        + " | ".join(
            f"{i:^6}"
            for i in range(len(BREEDS))
        )
    )

    for index, row in enumerate(matrix):

        print(
            f"{index:^15}"
            + " | ".join(
                f"{value:^6}"
                for value in row
            )
        )

    return (
        train_accuracy,
        val_accuracy,
        report,
        matrix
    )


# ============================================================
# SAVE METADATA
# ============================================================

def save_metadata(
    class_indices,
    train_accuracy,
    val_accuracy
):

    print()
    print("=" * 60)
    print("SAVING MODEL METADATA")
    print("=" * 60)

    os.makedirs(
        MODEL_OUTPUT_PATH,
        exist_ok=True
    )

    reverse_indices = {
        str(value): key
        for key, value
        in class_indices.items()
    }

    metadata = {

        "classes": class_indices,

        "class_names": reverse_indices,

        "breeds": BREEDS,

        "image_size": list(
            IMAGE_SIZE
        ),

        "trained_at":
            datetime.now().isoformat(),

        "model_type":
            "MobileNetV2 Transfer Learning + Fine Tuning",

        "preprocessing":
            "keras.applications.mobilenet_v2.preprocess_input",

        "training_accuracy":
            float(train_accuracy),

        "validation_accuracy":
            float(val_accuracy),

        "dataset_size":
            int(
                sum(
                    len([
                        f
                        for f in os.listdir(
                            os.path.join(
                                DATASET_PATH,
                                breed
                            )
                        )
                        if f.lower().endswith(
                            (
                                ".jpg",
                                ".jpeg",
                                ".png",
                                ".webp"
                            )
                        )
                    ])
                    for breed in BREEDS
                )
            )

    }

    metadata_path = os.path.join(
        MODEL_OUTPUT_PATH,
        "model_metadata.json"
    )

    with open(
        metadata_path,
        "w",
        encoding="utf-8"
    ) as file:

        json.dump(
            metadata,
            file,
            indent=2
        )

    print(
        f"✓ Metadata saved to: {metadata_path}"
    )


# ============================================================
# LOAD BEST MODEL
# ============================================================

def load_best_model():

    model_path = os.path.join(
        MODEL_OUTPUT_PATH,
        "quail_breed_model.keras"
    )

    if not os.path.exists(
        model_path
    ):

        raise RuntimeError(
            "Best model was not created."
        )

    print()
    print(
        "Loading best saved model..."
    )

    best_model = keras.models.load_model(
        model_path
    )

    print(
        "✓ Best model loaded successfully."
    )

    return best_model


# ============================================================
# MAIN
# ============================================================

def main():

    print()

    print(
        "╔════════════════════════════════════════════════════╗"
    )

    print(
        "║      Q-MATE QUAIL SPECIES CLASSIFIER TRAINER      ║"
    )

    print(
        "╚════════════════════════════════════════════════════╝"
    )

    print()

    # --------------------------------------------------------
    # 1. Verify dataset
    # --------------------------------------------------------

    if not check_dataset():

        print()
        print(
            "❌ Dataset verification failed."
        )

        sys.exit(1)

    # --------------------------------------------------------
    # 2. Load images
    # --------------------------------------------------------

    (
        x_data,
        y_data,
        class_indices
    ) = load_images_manually()

    # --------------------------------------------------------
    # 3. Split
    # --------------------------------------------------------

    (
        x_train,
        x_val,
        y_train,
        y_val
    ) = split_data(
        x_data,
        y_data
    )

    # --------------------------------------------------------
    # 4. Class weights
    # --------------------------------------------------------

    class_weights = calculate_class_weights(
        y_train
    )

    # --------------------------------------------------------
    # 5. Build model
    # --------------------------------------------------------

    (
        model,
        base_model
    ) = build_model()

    # --------------------------------------------------------
    # 6. Stage 1
    # --------------------------------------------------------

    train_classification_head(
        model,
        x_train,
        y_train,
        x_val,
        y_val,
        class_weights
    )

    # --------------------------------------------------------
    # 7. Stage 2
    # --------------------------------------------------------

    fine_tune_model(
        model,
        base_model,
        x_train,
        y_train,
        x_val,
        y_val,
        class_weights
    )

    # --------------------------------------------------------
    # 8. Load best model
    # --------------------------------------------------------

    best_model = load_best_model()

    # --------------------------------------------------------
    # 9. Evaluate
    # --------------------------------------------------------

    (
        train_accuracy,
        val_accuracy,
        report,
        matrix
    ) = evaluate_model(
        best_model,
        x_train,
        y_train,
        x_val,
        y_val
    )

    # --------------------------------------------------------
    # 10. Save metadata
    # --------------------------------------------------------

    save_metadata(
        class_indices,
        train_accuracy,
        val_accuracy
    )

    # --------------------------------------------------------
    # 11. Final
    # --------------------------------------------------------

    print()
    print("=" * 60)
    print("TRAINING COMPLETE ✓")
    print("=" * 60)

    print()

    print(
        f"Final Training Accuracy:   {train_accuracy:.2%}"
    )

    print(
        f"Final Validation Accuracy: {val_accuracy:.2%}"
    )

    print()

    print(
        "Model:"
    )

    print(
        "public/models/quail_breed_model.keras"
    )

    print()

    print(
        "Metadata:"
    )

    print(
        "public/models/model_metadata.json"
    )

    print()

    print(
        "✓ Species classifier model is ready."
    )


# ============================================================
# RUN
# ============================================================

if __name__ == "__main__":
    main()