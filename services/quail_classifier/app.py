from fastapi import FastAPI, File, UploadFile, HTTPException
from fastapi.responses import JSONResponse
import uvicorn
from PIL import Image
import numpy as np
import json
import io
import os

# Support a mock mode for presentations or environments without TensorFlow available.
MOCK_MODE = os.environ.get('MOCK_CLASSIFIER', '0') in ['1', 'true', 'True']

if not MOCK_MODE:
    try:
        import tensorflow as tf
    except Exception as e:
        raise RuntimeError(f"TensorFlow import failed: {e}")

app = FastAPI(title="Quail Classifier")

PROJECT_ROOT = os.path.abspath(os.path.join(os.path.dirname(__file__), '..', '..'))
MODEL_PATH = os.path.join(PROJECT_ROOT, 'public', 'models', 'quail_breed_model.keras')
METADATA_PATH = os.path.join(PROJECT_ROOT, 'public', 'models', 'model_metadata.json')

# Load model and metadata at startup
model = None
metadata = {}

@app.on_event("startup")
async def load_model():
    global model, metadata
    if MOCK_MODE:
        # In mock mode, we don't load the TensorFlow model; use demo metadata if available
        metadata = {'class_names': {'0': 'japanese_quail', '1': 'taiwan_cross', '2': 'pharaoh_quail', '3': 'english_white_quail'}, 'breeds': []}
        return

    if not os.path.exists(MODEL_PATH):
        raise RuntimeError(f"Model not found at {MODEL_PATH}")
    model = tf.keras.models.load_model(MODEL_PATH)

    if os.path.exists(METADATA_PATH):
        with open(METADATA_PATH, 'r') as f:
            metadata = json.load(f)
    else:
        metadata = {}


def preprocess_image(file_bytes):
    img = Image.open(io.BytesIO(file_bytes)).convert('RGB')
    img = img.resize((224, 224))
    arr = np.array(img, dtype=np.float32) / 255.0
    arr = np.expand_dims(arr, axis=0)
    return arr


@app.post("/classify")
async def classify(image: UploadFile = File(...)):
    try:
        contents = await image.read()

        if MOCK_MODE:
            # Return a deterministic demo response for presentations
            demo_preds = np.array([[0.85, 0.05, 0.05, 0.05]], dtype=float)
            predicted_class = int(np.argmax(demo_preds[0]))
            confidence = float(np.max(demo_preds[0]))
            class_names = metadata.get('class_names', {})
            class_name = class_names.get(str(predicted_class), f'Class {predicted_class}')
            all_predictions = {class_names.get(str(i), f'Class {i}'): float(p) for i, p in enumerate(demo_preds[0])}

            return JSONResponse({
                'success': True,
                'predicted_class': predicted_class,
                'class_name': class_name,
                'confidence': confidence,
                'all_predictions': all_predictions,
                'breeds': metadata.get('breeds', [])
            })

        img_array = preprocess_image(contents)
        preds = model.predict(img_array, verbose=0)

        predicted_class = int(np.argmax(preds[0]))
        confidence = float(np.max(preds[0]))

        class_names = metadata.get('class_names', {})
        class_name = class_names.get(str(predicted_class), f'Class {predicted_class}')

        all_predictions = {class_names.get(str(i), f'Class {i}'): float(p) for i, p in enumerate(preds[0])}

        return JSONResponse({
            'success': True,
            'predicted_class': predicted_class,
            'class_name': class_name,
            'confidence': confidence,
            'all_predictions': all_predictions,
            'breeds': metadata.get('breeds', [])
        })
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))


if __name__ == '__main__':
    uvicorn.run('app:app', host='127.0.0.1', port=8000, log_level='info')
