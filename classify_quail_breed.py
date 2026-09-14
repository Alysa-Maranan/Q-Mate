#!/usr/bin/env python3
"""
Quail Breed Classifier
Loads trained model and classifies images
"""

import sys
import json
import numpy as np
from PIL import Image
import tensorflow as tf

def classify_image(image_path, model_path, metadata_path):
    """Classify quail breed from image"""
    
    try:
        # Load model
        model = tf.keras.models.load_model(model_path)
        
        # Load metadata
        with open(metadata_path, 'r') as f:
            metadata = json.load(f)
        
        # Load and preprocess image
        img = Image.open(image_path).convert('RGB')
        img = img.resize((224, 224))
        img_array = np.array(img, dtype=np.float32) / 255.0
        
        # Add batch dimension
        img_array = np.expand_dims(img_array, axis=0)
        
        # Predict
        predictions = model.predict(img_array, verbose=0)
        
        # Get predicted class and confidence
        predicted_class = int(np.argmax(predictions[0]))
        confidence = float(np.max(predictions[0]))
        
        # Get class name
        class_names = metadata.get('class_names', {})
        class_name = class_names.get(str(predicted_class), f'Unknown (Class {predicted_class})')
        
        # Return result
        return {
            'success': True,
            'predicted_class': predicted_class,
            'class_name': class_name,
            'confidence': confidence,
            'all_predictions': {class_names.get(str(i), f'Class {i}'): float(pred) 
                               for i, pred in enumerate(predictions[0])},
            'breeds': metadata.get('breeds', [])
        }
        
    except Exception as e:
        return {
            'success': False,
            'error': str(e)
        }

if __name__ == '__main__':
    if len(sys.argv) < 4:
        print(json.dumps({'success': False, 'error': 'Missing arguments'}))
        sys.exit(1)
    
    image_path = sys.argv[1]
    model_path = sys.argv[2]
    metadata_path = sys.argv[3]
    
    result = classify_image(image_path, model_path, metadata_path)
    print(json.dumps(result))
