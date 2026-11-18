#!/usr/bin/env python3
"""
Q-MATE Quail Breed Classifier
Loads the trained MobileNetV2 model and classifies an image.

IMPORTANT:
- Preprocessing must match train_quail_model.py:
  keras.applications.mobilenet_v2.preprocess_input()
- Class indices and breed names come from model_metadata.json.
- Supports both a two-class softmax head and a single-unit sigmoid head.
- A prediction is not guaranteed to be correct; model quality depends on
  training data and validation performance.
"""

import json
import os
import sys

import numpy as np
from PIL import Image
import tensorflow as tf

DEFAULT_IMAGE_SIZE = [224, 224]


def normalized_class_labels(metadata):
    """Return {class_index: raw_label} using the saved metadata order."""
    class_names = metadata.get("class_names", {})
    if not isinstance(class_names, dict) or not class_names:
        return None

    labels = {}
    for key, label in class_names.items():
        try:
            index = int(key)
        except (TypeError, ValueError):
            continue
        labels[index] = str(label)

    return labels or None


def probabilities_from_output(raw_predictions, class_labels):
    """Convert model output to probabilities aligned with metadata indices.

    Binary sigmoid: a single output unit represents class index 1, so the
    complementary probability belongs to class index 0.

    Two-class (or larger) softmax: each output unit already corresponds to
    one metadata class index.
    """
    values = np.asarray(raw_predictions[0], dtype=np.float64).reshape(-1)
    output_count = int(values.size)

    if output_count == 1:
        if 0 not in class_labels or 1 not in class_labels:
            raise ValueError(
                "Binary sigmoid model requires class indices 0 and 1 in "
                "model_metadata.json."
            )
        positive = float(np.clip(values[0], 0.0, 1.0))
        return np.array([1.0 - positive, positive], dtype=np.float64), "binary_sigmoid"

    expected_indices = set(range(output_count))
    missing = expected_indices.difference(class_labels)
    if missing:
        raise ValueError(
            "Model output classes do not match model_metadata.json. "
            f"Missing class index/indices: {', '.join(str(i) for i in sorted(missing))}"
        )

    return values, "multiclass_softmax"


def classify_image(image_path, model_path, metadata_path):
    """Classify an image and return the predicted quail breed as JSON-ready data."""

    try:
        # Validate input files before loading.
        if not os.path.isfile(image_path):
            return {"success": False, "error": f"Image file not found: {image_path}"}
        if not os.path.isfile(model_path):
            return {"success": False, "error": f"Model file not found: {model_path}"}
        if not os.path.isfile(metadata_path):
            return {"success": False, "error": f"Metadata file not found: {metadata_path}"}

        # Load model and metadata.
        model = tf.keras.models.load_model(model_path)
        with open(metadata_path, "r", encoding="utf-8") as file:
            metadata = json.load(file)

        # Class order and labels are read from the file saved during training.
        class_labels = normalized_class_labels(metadata)
        if class_labels is None:
            return {
                "success": False,
                "error": "Metadata is missing a valid 'class_names' mapping."
            }

        # Read image and resize to the training image size.
        image_size = metadata.get("image_size", DEFAULT_IMAGE_SIZE)
        if (
            not isinstance(image_size, (list, tuple))
            or len(image_size) != 2
            or not all(isinstance(v, (int, float)) and int(v) > 0 for v in image_size)
        ):
            image_size = DEFAULT_IMAGE_SIZE
        height, width = int(image_size[0]), int(image_size[1])

        with Image.open(image_path) as image_file:
            image = image_file.convert("RGB")
            image = image.resize((width, height), Image.Resampling.LANCZOS)
            image_array = np.asarray(image, dtype=np.float32)

        # CRITICAL FIX:
        # train_quail_model.py uses MobileNetV2 preprocess_input on raw
        # 0..255 pixel values. Do the exact same preprocessing at inference.
        image_array = tf.keras.applications.mobilenet_v2.preprocess_input(image_array)

        # Add batch dimension and predict.
        image_batch = np.expand_dims(image_array, axis=0)
        predictions = model.predict(image_batch, verbose=0)

        if predictions.ndim != 2 or predictions.shape[0] != 1:
            return {"success": False, "error": "Unexpected model prediction output shape."}

        probabilities, output_format = probabilities_from_output(predictions, class_labels)

        predicted_class = int(np.argmax(probabilities))
        confidence = float(probabilities[predicted_class])

        # Convert the predicted class index using the saved metadata mapping.
        class_name = class_labels[predicted_class]
        display_names = metadata.get("display_names", {})
        if not isinstance(display_names, dict):
            display_names = {}
        breed_name = str(display_names.get(class_name, class_name))

        all_predictions = {
            class_labels.get(index, f"Class {index}"): float(probability)
            for index, probability in enumerate(probabilities)
        }

        return {
            "success": True,
            "predicted_class": predicted_class,
            "class_index": predicted_class,
            "class_name": class_name,
            "breed_name": breed_name,
            "confidence": confidence,
            "all_predictions": all_predictions,
            "breeds": metadata.get("breeds", []),
            "display_names": display_names,
            "model_type": metadata.get("model_type"),
            "preprocessing": metadata.get("preprocessing"),
            "output_format": output_format,
        }

    except Exception as error:
        return {
            "success": False,
            "error": str(error)
        }


if __name__ == "__main__":
    if len(sys.argv) < 4:
        print(json.dumps({
            "success": False,
            "error": "Usage: classify_quail_breed.py <image_path> <model_path> <metadata_path>"
        }))
        sys.exit(1)

    image_path = sys.argv[1]
    model_path = sys.argv[2]
    metadata_path = sys.argv[3]
    result = classify_image(image_path, model_path, metadata_path)
    print(json.dumps(result))
