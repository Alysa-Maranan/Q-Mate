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

After training:
1. Copy trained model to web-accessible location
2. Update `quail-detection.blade.php` to use new model
3. Test detection with your trained model

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
