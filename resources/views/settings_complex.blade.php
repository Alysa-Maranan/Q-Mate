@extends('layouts.app')

@section('content')
@include('components.sidebar')

<style>
:root {
    --primary-color: #6d4c41;
    --secondary-color: #a1887f;
    --light-bg: #fffdfa;
    --border-color: #d7ccc8;
    --text-gray: #8d6e63;
}

body {
    background: linear-gradient(135deg, #f5f0eb 0%, #efe8e4 50%, #e8dcd6 100%);
    min-height: 100vh;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
}

.sidebar-content-wrap {
    margin-left: 260px;
    padding: 2rem 3rem;
}

.settings-container {
    max-width: 1000px;
    margin: 0 auto;
}

.page-header {
    text-align: center;
    margin-bottom: 3rem;
}

.page-title {
    font-size: 2.5rem;
    font-weight: 800;
    color: var(--primary-color);
    margin: 0;
}

.page-subtitle {
    color: var(--text-gray);
    font-size: 1rem;
    margin-top: 0.5rem;
}

/* ===== CONTAINERS ===== */
.container-section {
    background: white;
    border-radius: 20px;
    padding: 2.5rem;
    margin-bottom: 2.5rem;
    box-shadow: 0 8px 32px rgba(161, 136, 127, 0.12);
    border: 1px solid rgba(161, 136, 127, 0.1);
}

.section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 2rem;
    border-bottom: 2px solid var(--border-color);
    padding-bottom: 1rem;
}

.section-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary-color);
    margin: 0;
}

.section-subtitle {
    color: var(--text-gray);
    font-size: 0.9rem;
    margin-top: 0.25rem;
}

/* ===== FORM ELEMENTS ===== */
.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    margin-bottom: 1.5rem;
}

.form-row.full {
    grid-template-columns: 1fr;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--primary-color);
    margin-bottom: 0.5rem;
}

.form-input {
    padding: 0.75rem 1rem;
    border: 1.5px solid var(--border-color);
    border-radius: 10px;
    font-size: 0.95rem;
    background: #f9fafb;
    transition: all 0.3s ease;
}

.form-input:focus {
    outline: none;
    border-color: var(--secondary-color);
    background: white;
    box-shadow: 0 0 0 3px rgba(161, 136, 127, 0.1);
}

/* ===== SUCCESS MESSAGE ===== */
.success-message {
    background: #d4edda;
    color: #155724;
    padding: 1rem;
    border-radius: 10px;
    margin-bottom: 1.5rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    border: 1px solid #c3e6cb;
}

/* ===== BREED DISPLAY CARD ===== */
.breed-card {
    background: linear-gradient(135deg, var(--light-bg) 0%, #fff7f2 100%);
    padding: 2rem;
    border-radius: 15px;
    border: 2px solid var(--border-color);
    margin-bottom: 2rem;
}

.breed-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1.5rem;
}

.breed-card-title {
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--primary-color);
    margin: 0;
}

.breed-card-description {
    color: var(--text-gray);
    font-size: 0.95rem;
    margin-top: 0.25rem;
    line-height: 1.5;
}

.breed-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-top: 1.5rem;
}

.breed-info-item {
    background: white;
    padding: 1rem;
    border-radius: 10px;
    border: 1px solid var(--border-color);
}

.breed-info-label {
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--primary-color);
    text-transform: uppercase;
    margin-bottom: 0.5rem;
}

.breed-info-value {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--secondary-color);
}

/* ===== BUTTONS ===== */
.btn {
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    border: none;
    font-size: 0.95rem;
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(161, 136, 127, 0.3);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(161, 136, 127, 0.4);
}

.btn-secondary {
    background: var(--light-bg);
    color: var(--primary-color);
    border: 2px solid var(--border-color);
}

.btn-secondary:hover {
    background: #f5eee6;
}

.form-buttons {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    margin-top: 2rem;
}

/* ===== MODAL ===== */
.modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    z-index: 9999;
    align-items: center;
    justify-content: center;
}

.modal-content {
    background: white;
    padding: 2.5rem;
    border-radius: 20px;
    max-width: 700px;
    width: 90%;
    max-height: 80vh;
    overflow-y: auto;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    border: 1px solid var(--border-color);
}

.modal-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary-color);
    margin: 0 0 1.5rem 0;
}

/* ===== BREED OPTIONS ===== */
.breed-options-list {
    display: grid;
    gap: 1rem;
    margin-bottom: 2rem;
}

.breed-option {
    padding: 1.5rem;
    border: 2px solid var(--border-color);
    border-radius: 12px;
    background: white;
    cursor: pointer;
    transition: all 0.2s ease;
}

.breed-option:hover {
    border-color: var(--secondary-color);
    box-shadow: 0 4px 12px rgba(141, 110, 99, 0.1);
}

.breed-option.selected {
    border-color: var(--secondary-color);
    background: linear-gradient(135deg, #fff9f5 0%, #fff7f2 100%);
}

.breed-option-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.75rem;
}

.breed-option-name {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--primary-color);
    margin: 0;
}

.breed-option-scientific {
    font-size: 0.85rem;
    color: var(--text-gray);
    font-style: italic;
    margin: 0;
}

.breed-option-desc {
    font-size: 0.9rem;
    color: var(--primary-color);
    margin: 0;
}

.breed-checkmark {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    background: var(--primary-color);
    border-radius: 50%;
    color: white;
    font-weight: 700;
    font-size: 1.2rem;
}

/* ===== PREVIEW CARD IN MODAL ===== */
.preview-card {
    display: none;
    background: linear-gradient(135deg, #fff9f5 0%, #fff7f2 100%);
    padding: 1.5rem;
    border-radius: 12px;
    border-left: 4px solid var(--secondary-color);
    margin-bottom: 1.5rem;
}

.preview-card.visible {
    display: block;
}

.preview-title {
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--primary-color);
    margin: 0 0 0.5rem 0;
}

.preview-desc {
    color: var(--text-gray);
    font-size: 0.95rem;
    margin: 0 0 1rem 0;
    line-height: 1.5;
}

.preview-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    font-size: 0.85rem;
}

.preview-item strong {
    color: var(--primary-color);
}

/* ===== MODAL BUTTONS ===== */
.modal-buttons {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    margin-top: 1.5rem;
}

.modal-buttons .btn {
    min-width: 120px;
}

@media (max-width: 768px) {
    .sidebar-content-wrap {
        margin-left: 0;
        padding: 1.5rem 1rem;
    }

    .form-row {
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    .section-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .modal-content {
        width: 95%;
        padding: 1.5rem;
    }

    .breed-info-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="sidebar-content-wrap">
    <div class="settings-container">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">Settings</h1>
            <p class="page-subtitle">Manage your farm information and quail breed</p>
        </div>

        <!-- ===== FARM INFORMATION CONTAINER ===== -->
        <div class="container-section">
            <div class="section-header">
                <div>
                    <h2 class="section-title">Farm Information</h2>
                    <p class="section-subtitle">Update your farm details</p>
                </div>
            </div>

            @if(session('farm_saved'))
                <div class="success-message">
                    ✅ Farm info saved successfully!
                </div>
            @endif

            <form method="POST" action="{{ route('settings.farm-info') }}" id="farmSettingsForm">
                @csrf
                <input type="hidden" name="quail_breed_id" id="quail_breed_id_hidden" value="{{ $currentBreed ? $currentBreed->id : '' }}">

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Farm Name</label>
                        <input type="text" name="farm_name" class="form-input" value="{{ $farm['name'] }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Contact Number</label>
                        <input type="tel" name="farm_phone" class="form-input" value="{{ $farm['phone'] }}">
                    </div>
                </div>

                <div class="form-row full">
                    <div class="form-group">
                        <label class="form-label">Address</label>
                        <input type="text" name="farm_address" class="form-input" value="{{ $farm['address'] }}">
                    </div>
                </div>

                <div class="form-row full">
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="farm_email" class="form-input" value="{{ $farm['email'] }}">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1.5rem;">Save Farm Information</button>
            </form>
        </div>

        <!-- ===== QUAIL BREED MANAGEMENT CONTAINER ===== -->
        <div class="container-section">
            <div class="section-header">
                <div>
                    <h2 class="section-title">Quail Breed Management</h2>
                    <p class="section-subtitle">Select and manage your quail breed</p>
                </div>
            </div>

            @if($currentBreed)
                <div class="breed-card">
                    <div class="breed-card-header">
                        <div>
                            <h3 class="breed-card-title" id="display-breed-name">{{ $currentBreed->name }}</h3>
                            <p class="breed-card-description" id="display-breed-desc">{{ $currentBreed->description }}</p>
                        </div>
                    </div>

                    <div class="breed-info-grid">
                        <div class="breed-info-item">
                            <div class="breed-info-label">Scientific Name</div>
                            <div class="breed-info-value" id="display-scientific">{{ $currentBreed->scientific_name }}</div>
                        </div>
                        <div class="breed-info-item">
                            <div class="breed-info-label">Egg Production</div>
                            <div class="breed-info-value" id="display-eggs">{{ $currentBreed->egg_production_rate }}<span style="font-size: 0.6rem;"> eggs/year</span></div>
                        </div>
                        <div class="breed-info-item">
                            <div class="breed-info-label">Mature Weight</div>
                            <div class="breed-info-value" id="display-weight">{{ $currentBreed->mature_weight }}<span style="font-size: 0.6rem;">g</span></div>
                        </div>
                        <div class="breed-info-item">
                            <div class="breed-info-label">Maturity Age</div>
                            <div class="breed-info-value" id="display-maturity">{{ $currentBreed->maturity_age }}<span style="font-size: 0.6rem;"> days</span></div>
                        </div>
                    </div>
                </div>
            @else
                <div class="breed-card" style="text-align: center; padding: 3rem 2rem;">
                    <p style="color: var(--text-gray); font-size: 1rem;">No breed selected yet. Click below to choose one.</p>
                </div>
            @endif

            <button type="button" onclick="showBreedSelectorModal()" class="btn btn-secondary" style="width: 100%;">{{ $currentBreed ? 'Change Breed' : 'Select Breed' }}</button>
        </div>
    </div>
</div>

<!-- ===== BREED SELECTOR MODAL ===== -->
<div id="breedSelectorModal" class="modal-overlay">
    <div class="modal-content">
        <h3 class="modal-title">Select Your Quail Breed</h3>

        <div class="breed-options-list">
            @foreach($breeds as $breed)
            <div class="breed-option" onclick="selectBreedPreview(this)" 
                 data-breed-id="{{ $breed->id }}"
                 data-breed-name="{{ $breed->name }}"
                 data-breed-desc="{{ $breed->description }}"
                 data-breed-scientific="{{ $breed->scientific_name }}"
                 data-breed-eggs="{{ $breed->egg_production_rate }}"
                 data-breed-weight="{{ $breed->mature_weight }}"
                 data-breed-maturity="{{ $breed->maturity_age }}">
                
                <div class="breed-option-header">
                    <div style="flex: 1;">
                        <h4 class="breed-option-name">{{ $breed->name }}</h4>
                        <p class="breed-option-scientific">{{ $breed->scientific_name }}</p>
                    </div>
                    <div class="breed-checkmark" style="display: none;">✓</div>
                </div>
                <p class="breed-option-desc">{{ $breed->description }}</p>
            </div>
            @endforeach
        </div>

        <!-- Preview Card -->
        <div id="previewCard" class="preview-card">
            <h4 class="preview-title" id="preview-breed-name">Breed Name</h4>
            <p class="preview-desc" id="preview-breed-desc"></p>
            <div class="preview-grid">
                <div><strong>Scientific:</strong> <span id="preview-scientific"></span></div>
                <div><strong>Eggs/Year:</strong> <span id="preview-eggs"></span></div>
                <div><strong>Weight:</strong> <span id="preview-weight"></span>g</div>
                <div><strong>Maturity:</strong> <span id="preview-maturity"></span> days</div>
            </div>
        </div>

        <div class="modal-buttons">
            <button type="button" onclick="closeBreedSelectorModal()" class="btn btn-secondary">Cancel</button>
            <button type="button" onclick="saveBreedSelection()" class="btn btn-primary" id="confirmBreedBtn" disabled>Confirm Selection</button>
        </div>
    </div>
</div>

<script>
let selectedBreedId = null;

function showBreedSelectorModal() {
    document.getElementById('breedSelectorModal').style.display = 'flex';
    selectedBreedId = document.getElementById('quail_breed_id_hidden').value || null;
    
    // Highlight currently selected breed
    if (selectedBreedId) {
        const allBreedOptions = document.querySelectorAll('.breed-option');
        allBreedOptions.forEach(option => {
            const checkmark = option.querySelector('.breed-checkmark');
            if (option.dataset.breedId == selectedBreedId) {
                option.classList.add('selected');
                if (checkmark) checkmark.style.display = 'flex';
            } else {
                option.classList.remove('selected');
                if (checkmark) checkmark.style.display = 'none';
            }
        });
    }
}

function closeBreedSelectorModal() {
    document.getElementById('breedSelectorModal').style.display = 'none';
    document.getElementById('previewCard').classList.remove('visible');
    document.getElementById('confirmBreedBtn').disabled = true;
}

function selectBreedPreview(element) {
    const breedId = element.dataset.breedId;
    const name = element.dataset.breedName;
    const desc = element.dataset.breedDesc;
    const scientific = element.dataset.breedScientific;
    const eggs = element.dataset.breedEggs;
    const weight = element.dataset.breedWeight;
    const maturity = element.dataset.breedMaturity;
    
    selectedBreedId = breedId;
    
    // Update all breed options
    document.querySelectorAll('.breed-option').forEach(opt => {
        opt.classList.remove('selected');
        const checkmark = opt.querySelector('.breed-checkmark');
        if (checkmark) checkmark.style.display = 'none';
    });
    
    // Highlight selected
    element.classList.add('selected');
    const thisCheckmark = element.querySelector('.breed-checkmark');
    if (thisCheckmark) thisCheckmark.style.display = 'flex';
    
    // Update preview
    document.getElementById('preview-breed-name').textContent = name;
    document.getElementById('preview-breed-desc').textContent = desc;
    document.getElementById('preview-scientific').textContent = scientific;
    document.getElementById('preview-eggs').textContent = eggs;
    document.getElementById('preview-weight').textContent = weight;
    document.getElementById('preview-maturity').textContent = maturity;
    
    document.getElementById('previewCard').classList.add('visible');
    document.getElementById('confirmBreedBtn').disabled = false;
}

function saveBreedSelection() {
    if (selectedBreedId) {
        document.getElementById('quail_breed_id_hidden').value = selectedBreedId;
        closeBreedSelectorModal();
        document.getElementById('farmSettingsForm').submit();
    }
}

// Close modal when clicking outside
document.getElementById('breedSelectorModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeBreedSelectorModal();
    }
});
</script>

@endsection
