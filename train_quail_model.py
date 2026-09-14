#!/usr/bin/env python3
"""
Quail Breed Detection Model Trainer
Trains a custom TensorFlow model to classify 4 quail breeds
Fixed to properly handle WebP images
"""

import os
import sys
import tensorflow as tf
from tensorflow import keras
from tensorflow.keras import layers, models
import numpy as np
from pathlib import Path
from PIL import Image
from sklearn.model_selection import train_test_split
import json
from datetime import datetime

# Configuration
DATASET_PATH = 'datasets/quail_images'
MODEL_OUTPUT_PATH = 'public/models'
BREEDS = [
    'japanese_quail',
    'japanese_coturnix_crossbreed_taiwan',
    'pharaoh_quail',
    'english_white_quail'
]
IMAGE_SIZE = (224, 224)
BATCH_SIZE = 8
EPOCHS = 50

def check_dataset():
    """Verify dataset structure and count images"""
    print("="*50)
    print("DATASET VERIFICATION")
    print("="*50)
    
    total_images = 0
    breed_counts = {}
    
    for breed in BREEDS:
        breed_path = os.path.join(DATASET_PATH, breed)
        if not os.path.exists(breed_path):
            print(f"❌ Folder not found: {breed_path}")
            return False
        
        images = [f for f in os.listdir(breed_path) 
                 if f.lower().endswith(('.jpg', '.jpeg', '.png', '.webp'))]
        count = len(images)
        breed_counts[breed] = count
        total_images += count
        
        status = "✓" if count > 0 else "✗"
        print(f"{status} {breed}: {count} images")
    
    print(f"\nTotal images: {total_images}")
    
    if total_images < 16:  # Minimum 4 images per class
        print("❌ Not enough images! Need at least 4 per breed.")
        return False
    
    return True

def load_images_manually():
    """Load and preprocess all images using PIL (handles WebP properly)"""
    print("\n" + "="*50)
    print("LOADING AND PREPARING DATA (PIL + WebP Support)")
    print("="*50)
    
    images = []
    labels = []
    class_indices = {}
    
    for idx, breed in enumerate(BREEDS):
        breed_path = os.path.join(DATASET_PATH, breed)
        class_indices[breed] = idx
        
        image_files = [f for f in os.listdir(breed_path) 
                      if f.lower().endswith(('.jpg', '.jpeg', '.png', '.webp'))]
        
        print(f"\nLoading {breed}... ({len(image_files)} images)")
        
        for img_file in image_files:
            try:
                img_path = os.path.join(breed_path, img_file)
                # Open image with PIL (supports WebP natively)
                img = Image.open(img_path).convert('RGB')
                # Resize to target size
                img = img.resize(IMAGE_SIZE)
                # Convert to numpy array and normalize
                img_array = np.array(img, dtype=np.float32) / 255.0
                
                images.append(img_array)
                labels.append(idx)
            except Exception as e:
                print(f"  ⚠ Error loading {img_file}: {str(e)}")
    
    x_data = np.array(images)
    y_data = np.array(labels)
    
    # Convert labels to one-hot
    y_categorical = keras.utils.to_categorical(y_data, len(BREEDS))
    
    print(f"\n✓ Total images loaded: {len(images)}")
    print(f"  Shape: {x_data.shape}")
    print(f"  Labels shape: {y_categorical.shape}")
    
    return x_data, y_categorical, class_indices

def split_data(x_data, y_data):
    """Split into training and validation sets"""
    print("\n" + "="*50)
    print("SPLITTING DATA")
    print("="*50)
    
    x_train, x_val, y_train, y_val = train_test_split(
        x_data, y_data,
        test_size=0.2,
        random_state=42,
        stratify=np.argmax(y_data, axis=1)
    )
    
    print(f"Training samples: {len(x_train)}")
    print(f"Validation samples: {len(x_val)}")
    
    return x_train, x_val, y_train, y_val

def build_model():
    """Build transfer learning model using MobileNetV2"""
    print("\n" + "="*50)
    print("BUILDING MODEL")
    print("="*50)
    
    # Load pre-trained MobileNetV2
    base_model = keras.applications.MobileNetV2(
        input_shape=IMAGE_SIZE + (3,),
        include_top=False,
        weights='imagenet'
    )
    
    # Freeze base model layers
    base_model.trainable = False
    
    # Create custom top layers
    model = models.Sequential([
        base_model,
        layers.GlobalAveragePooling2D(),
        layers.Dense(256, activation='relu'),
        layers.Dropout(0.5),
        layers.Dense(128, activation='relu'),
        layers.Dropout(0.3),
        layers.Dense(len(BREEDS), activation='softmax')
    ])
    
    # Compile model
    model.compile(
        optimizer=keras.optimizers.Adam(learning_rate=1e-3),
        loss='categorical_crossentropy',
        metrics=['accuracy']
    )
    
    print(f"Model created with {len(BREEDS)} output classes")
    return model

def train_model(model, x_train, y_train, x_val, y_val):
    """Train the model"""
    print("\n" + "="*50)
    print("TRAINING MODEL")
    print("="*50)
    
    callbacks = [
        keras.callbacks.EarlyStopping(
            monitor='val_loss',
            patience=10,
            restore_best_weights=True,
            verbose=1
        ),
        keras.callbacks.ReduceLROnPlateau(
            monitor='val_loss',
            factor=0.5,
            patience=5,
            min_lr=1e-7,
            verbose=1
        )
    ]
    
    history = model.fit(
        x_train, y_train,
        validation_data=(x_val, y_val),
        epochs=EPOCHS,
        batch_size=BATCH_SIZE,
        callbacks=callbacks,
        verbose=1
    )
    
    return history

def save_model(model, class_indices):
    """Save trained model and metadata"""
    print("\n" + "="*50)
    print("SAVING MODEL")
    print("="*50)
    
    # Create output directory
    os.makedirs(MODEL_OUTPUT_PATH, exist_ok=True)
    
    # Save model in Keras format (native format)
    model_path = os.path.join(MODEL_OUTPUT_PATH, 'quail_breed_model.keras')
    model.save(model_path)
    print(f"✓ Model saved to: {model_path}")
    
    # Create class name mapping
    reverse_indices = {v: k for k, v in class_indices.items()}
    
    # Save metadata as JSON
    metadata = {
        'classes': class_indices,
        'class_names': reverse_indices,
        'breeds': BREEDS,
        'image_size': IMAGE_SIZE,
        'trained_at': datetime.now().isoformat(),
        'model_type': 'MobileNetV2 Transfer Learning'
    }
    
    metadata_path = os.path.join(MODEL_OUTPUT_PATH, 'model_metadata.json')
    with open(metadata_path, 'w') as f:
        json.dump(metadata, f, indent=2)
    print(f"✓ Metadata saved to: {metadata_path}")
    
    return model_path

def main():
    """Main training pipeline"""
    print("\n🔬 QUAIL BREED DETECTION MODEL TRAINER 🔬\n")
    
    # Check dataset
    if not check_dataset():
        print("\n❌ Dataset check failed!")
        sys.exit(1)
    
    # Load images with PIL (WebP support)
    x_data, y_data, class_indices = load_images_manually()
    
    # Split data
    x_train, x_val, y_train, y_val = split_data(x_data, y_data)
    
    # Build model
    model = build_model()
    
    # Train model
    history = train_model(model, x_train, y_train, x_val, y_val)
    
    # Save model
    save_model(model, class_indices)
    
    # Print results
    print("\n" + "="*50)
    print("TRAINING COMPLETE ✓")
    print("="*50)
    print(f"\nFinal Training Accuracy: {history.history['accuracy'][-1]:.2%}")
    print(f"Final Validation Accuracy: {history.history['val_accuracy'][-1]:.2%}")
    print("\n✅ Model is ready for deployment!")
    print("Next step: Update quail-detection.blade.php to use the new model")

if __name__ == '__main__':
    main()
