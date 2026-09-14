# Quail Classifier Service

This is a small FastAPI service that loads the TensorFlow/Keras model once and serves `/classify` for faster inference.

Requirements

- Python 3.8+

Install (production with TensorFlow)

```powershell
pip install -r services/quail_classifier/requirements.txt
```

Install (lightweight demo/mock mode — no TensorFlow)

```powershell
pip install -r services/quail_classifier/requirements-light.txt
```

Run

```powershell
# Mock/demo mode (fast, no TF required)
setx MOCK_CLASSIFIER 1
uvicorn services.quail_classifier.app:app --host 127.0.0.1 --port 8000 --reload

# Production mode (requires TensorFlow and model files present)
setx MOCK_CLASSIFIER 0
uvicorn services.quail_classifier.app:app --host 127.0.0.1 --port 8000
```

Then ensure the Laravel app uses `QUAIL_CLASSIFIER_URL` environment variable if different from the default `http://127.0.0.1:8000/classify`.
