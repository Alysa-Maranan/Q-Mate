#!/usr/bin/env python3
"""Deterministic quail breed mapping from plumage color.

Rules requested for Q-MATE:
- Brown quail  -> Japanese Quail / Coturnix Quail (japanese_quail_group)
- Gray / dark gray quail -> Pharaoh Quail (pharaoh_quail)

Size is ignored. No TensorFlow model is loaded. The reported confidence is the
share of brown or gray pixels supporting the selected color rule, not a model
probability.
"""

import json
import sys

import numpy as np
from PIL import Image

JAPANESE_CLASS = 0
PHARAOH_CLASS = 1

CLASS_NAMES = {
    JAPANESE_CLASS: "japanese_quail_group",
    PHARAOH_CLASS: "pharaoh_quail",
}

DISPLAY_NAMES = {
    "japanese_quail_group": "Japanese Quail / Coturnix Quail",
    "pharaoh_quail": "Pharaoh Quail",
}


def rgb_to_hsv(rgb):
    """Vectorized RGB (0..255) -> HSV with hue in 0..1."""
    rgb = rgb.astype(np.float32) / 255.0
    red = rgb[..., 0]
    green = rgb[..., 1]
    blue = rgb[..., 2]

    max_channel = np.max(rgb, axis=2)
    min_channel = np.min(rgb, axis=2)
    diff = max_channel - min_channel

    hue = np.zeros_like(max_channel)
    nonzero = diff > 1e-6

    mask = nonzero & (max_channel == red)
    hue[mask] = ((green[mask] - blue[mask]) / diff[mask]) % 6

    mask = nonzero & (max_channel == green)
    hue[mask] = ((blue[mask] - red[mask]) / diff[mask]) + 2

    mask = nonzero & (max_channel == blue)
    hue[mask] = ((red[mask] - green[mask]) / diff[mask]) + 4

    hue = hue / 6.0
    saturation = np.where(
        max_channel > 1e-6,
        diff / np.maximum(max_channel, 1e-6),
        0.0,
    )

    return hue, saturation, max_channel


def classify_by_color(image_path):
    try:
        with Image.open(image_path) as image:
            image = image.convert("RGB")
            image.thumbnail((480, 480), Image.Resampling.BILINEAR)
            rgb = np.asarray(image, dtype=np.uint8)
    except (OSError, ValueError) as error:
        return {
            "success": False,
            "error": "Unable to read image: " + str(error),
        }

    height, width = rgb.shape[:2]

    # Use the central area so backgrounds affect the result less.
    top = int(height * 0.10)
    bottom = int(height * 0.90)
    left = int(width * 0.10)
    right = int(width * 0.90)
    crop = rgb[top:bottom, left:right]

    if crop.size == 0:
        crop = rgb

    hue, saturation, value = rgb_to_hsv(crop)

    # Brown: orange/brown hue with usable saturation.
    brown_mask = (
        (hue >= 0.015)
        & (hue <= 0.14)
        & (saturation >= 0.18)
        & (value >= 0.10)
        & (value <= 0.92)
    )

    # Gray / dark gray: low saturation, excluding white and black borders.
    gray_mask = (
        (saturation <= 0.18)
        & (value >= 0.10)
        & (value <= 0.85)
    )

    brown_pixels = int(np.count_nonzero(brown_mask))
    gray_pixels = int(np.count_nonzero(gray_mask))
    color_pixels = brown_pixels + gray_pixels

    if color_pixels == 0:
        return {
            "success": False,
            "error": "No brown or gray/dark gray plumage pixels were found.",
        }

    if gray_pixels > brown_pixels:
        class_index = PHARAOH_CLASS
        detected_color = "gray/dark gray"
    else:
        class_index = JAPANESE_CLASS
        detected_color = "brown"

    class_name = CLASS_NAMES[class_index]

    return {
        "success": True,
        "predicted_class": class_index,
        "class_index": class_index,
        "class_name": class_name,
        "breed_name": DISPLAY_NAMES[class_name],
        "confidence": max(brown_pixels, gray_pixels) / color_pixels,
        "classification_method": "color_rule",
        "detected_color": detected_color,
        "brown_pixels": brown_pixels,
        "gray_pixels": gray_pixels,
        "all_predictions": {
            "japanese_quail_group": brown_pixels / color_pixels,
            "pharaoh_quail": gray_pixels / color_pixels,
        },
        "display_names": DISPLAY_NAMES,
    }


if __name__ == "__main__":
    if len(sys.argv) < 2:
        print(json.dumps({
            "success": False,
            "error": "Usage: classify_quail_color.py <image_path>",
        }))
        sys.exit(1)

    print(json.dumps(classify_by_color(sys.argv[1])))
