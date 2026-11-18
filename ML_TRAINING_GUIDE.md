# 🔬 Quail Breed Detection Model Training Guide

## Prerequisites

1. **Python 3.8+** installed on your system
2. **pip** (Python package manager)
3. **20 images per breed** already in `/datasets/quail_images/` folders

## Setup Instructions

### Step 1: Install Python Dependencies
```powershell
pip install -r ml_requirements.txt
```

### Step 2: Verify Dataset
Make sure you have this structure:
```
datasets/quail_images/
├── japanese_quail/                          (20 images)
├── japanese_coturnix_crossbreed_taiwan/    (20 images)
├── pharaoh_quail/                           (20 images)
```

### Step 3: Run Training Script
```powershell
python train_quail_model.py
```

### What the Script Does:
1. ✓ Verifies dataset integrity
2. ✓ Loads and preprocesses images
3. ✓ Builds transfer learning model (MobileNetV2)
4. ✓ Trains for up to 50 epochs
5. ✓ Saves trained model and metadata
6. ✓ Displays accuracy metrics

### Training Time:
- **Estimated**: 5-15 minutes (depending on CPU/GPU)
- With GPU: Faster
- With CPU: Slower but will work

## Output

After training completes:
- Model saved: `public/models/quail_breed_model/`
- Metadata saved: `public/models/model_metadata.json`

## Next Steps

The current web detector uses `classify_quail_color.py`, not the MobileNetV2
model produced by this training script. It maps brown plumage to Japanese
Quail / Coturnix and gray or dark-gray plumage to Pharaoh Quail, regardless of
bird size. The classifier resizes images before analysis and does not require
the `.keras` model or its metadata.

To switch the web detector to a newly trained MobileNetV2 model, the inference
flow in `QuailDetectionController` must be intentionally changed and tested.

## Troubleshooting

**Error: "Not enough images"**
- Need at least 4 images per breed minimum (you have 20, so OK)

**Error: "Folder not found"**
- Check folder names match exactly:
  - `japanese_quail`
  - `japanese_coturnix_crossbreed_taiwan`
  - `pharaoh_quail`

**Error: "CUDA/GPU not found"**
- Script will automatically use CPU - slower but will work

**Training too slow?**
- Install GPU drivers (NVIDIA CUDA for TensorFlow)
- Use fewer epochs temporarily for testing

## Tips for Better Accuracy

1. **More images**: 50-100 per breed = better accuracy
2. **Good lighting**: Clear, well-lit photos
3. **Different angles**: Photos from multiple angles
4. **Different backgrounds**: Various environments
5. **Real conditions**: Photos as they'd appear in your farm

---

**Questions?** Refer to TensorFlow documentation or ask for help!
