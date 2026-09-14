@extends('layouts.app')

@section('content')
@include('components.sidebar')

<style>
    /* Font Family - Main */
    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }

    .page-header {
        background: white;
        border-radius: 24px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 8px 32px rgba(161, 136, 127, 0.12);
        border: 1px solid rgba(161, 136, 127, 0.1);
        text-align: center;
    }
    .page-title {
        font-size: 2rem;
        font-weight: 800;
        color: #6d4c41;
        margin: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1rem;
    }
    .page-title svg {
        color: #6d4c41;
        flex-shrink: 0;
    }
    .page-subtitle {
        color: #8d6e63;
        font-size: 0.95rem;
        margin-top: 0.5rem;
        font-weight: 500;
    }
    
    /* Sidebar content wrapper - matching dashboard behavior */
    .sidebar-content-wrap {
        margin-left: 300px;
        transition: margin-left 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        padding-top: 0;
    }
    .sidebar-content-wrap.expanded {
        margin-left: 0;
        padding-top: 80px;
    }

    /* Expanded state - Full width content */
    .sidebar-content-wrap.expanded .container {
        max-width: 1600px;
        margin: 0 auto;
        padding-left: 2rem;
        padding-right: 2rem;
    }

    /* Container and content styling */
    .container {
        max-width: 100%;
        margin: 0 auto;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }

    @media (max-width: 768px) {
        .sidebar-content-wrap {
            margin-left: 0 !important;
            padding-top: 70px;
        }
        .container { padding: 1.5rem 1rem; }
    }

    /* Section styling */
    .settings-section-title {
        color: #6d4c41;
        margin-bottom: 1rem;
        font-size: 1.3rem;
        font-weight: 700;
    }

    .settings-card {
        background: linear-gradient(135deg, #fffdfa 0%, #f5eee6 100%);
        padding: 2rem;
        border-radius: 12px;
        border: 2px solid #d7ccc8;
        box-shadow: 0 4px 12px rgba(141, 110, 99, 0.1);
        margin-bottom: 2rem;
    }

    .form-group {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .form-group.full {
        grid-template-columns: 1fr;
    }

    .form-field label {
        display: block;
        font-size: 0.875rem;
        font-weight: 600;
        color: #6d4c41;
        margin-bottom: 0.5rem;
    }

    .form-field input,
    .form-field select {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1.5px solid #d7ccc8;
        border-radius: 10px;
        font-size: 0.95rem;
        background: #f9fafb;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        transition: all 0.2s ease;
    }

    .form-field input:focus,
    .form-field select:focus {
        outline: none;
        border-color: #a1887f;
        background: white;
        box-shadow: 0 0 0 3px rgba(161, 136, 127, 0.1);
    }

    .btn-primary {
        background: linear-gradient(135deg, #6d4c41 0%, #a1887f 100%);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        cursor: pointer;
        font-weight: 600;
        width: 100%;
        transition: all 0.3s ease;
        font-size: 0.95rem;
    }

    .btn-primary:hover {
        box-shadow: 0 6px 16px rgba(141, 110, 99, 0.2);
        transform: translateY(-2px);
    }

    .success-message {
        background: #d4edda;
        color: #155724;
        padding: 0.75rem 1rem;
        border-radius: 8px;
        margin-bottom: 1rem;
        font-weight: 600;
    }

    /* Tab Navigation - matching feeder nav style */
    .tab-navigation {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 2rem;
        background: transparent;
    }

    .tab-btn {
        padding: 0.75rem 1.5rem;
        background: #fffdfa;
        border: 2px solid #a1887f;
        border-radius: 10px;
        cursor: pointer;
        font-size: 1rem;
        font-weight: 700;
        color: #6d4c41;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(141, 110, 99, 0.10);
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .tab-btn:hover {
        background: #f8ede3;
        box-shadow: 0 6px 16px rgba(141, 110, 99, 0.18);
    }

    .tab-btn.active {
        background: #a1887f;
        color: #fffdfa;
        box-shadow: 0 6px 16px rgba(141, 110, 99, 0.18), 0 0 20px rgba(161, 136, 127, 0.5);
    }

    .tab-content {
        display: none;
        animation: fadeIn 0.3s ease;
    }

    .tab-content.active {
        display: block;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<div id="main-content-wrap" class="sidebar-content-wrap">
<div class="container" style="padding: 2rem;">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            Farm Settings
        </h1>
        <p class="page-subtitle">Manage your farm information and quail breed</p>
    </div>

    <!-- Tab Navigation -->
    <div class="tab-navigation">
        <button class="tab-btn active" onclick="openTab('farm-info')" id="tab-btn-farm-info">
            Farm Information
        </button>
        <button class="tab-btn" onclick="openTab('quail-breed')" id="tab-btn-quail-breed">
            Quail Breed Management
        </button>
        <button class="tab-btn" onclick="openTab('feeder-schedule')" id="tab-btn-feeder-schedule">
            Feeder Schedule Settings
        </button>
    </div>

    <!-- Farm Information Tab -->
    <div class="tab-content active" id="tab-farm-info">
        <h2 class="settings-section-title">Farm Information</h2>

        <div class="settings-card">
            @if(session('farm_saved'))
                <div class="success-message">✅ Farm info saved successfully!</div>
            @endif

            <form method="POST" action="{{ route('settings.farm-info') }}" id="farmSettingsForm">
                @csrf
                <input type="hidden" name="quail_breed_id_1" id="quail_breed_id_1_hidden" value="{{ $currentBreeds && isset($currentBreeds[0]) ? $currentBreeds[0]->id : '' }}">
                <input type="hidden" name="quail_breed_id_2" id="quail_breed_id_2_hidden" value="{{ $currentBreeds && isset($currentBreeds[1]) ? $currentBreeds[1]->id : '' }}">
                <input type="hidden" name="breed_start_date" id="breed_start_date_hidden" value="{{ $farm['breed_start_date'] }}">
                <input type="hidden" name="breed_end_date" id="breed_end_date_hidden" value="{{ $farm['breed_end_date'] }}">

                <div class="form-group">
                    <div class="form-field">
                        <label>Farm Name</label>
                        <input type="text" name="farm_name" value="{{ $farm['name'] }}" required>
                    </div>
                    <div class="form-field">
                        <label>Contact Number</label>
                        <input type="tel" name="farm_phone" value="{{ $farm['phone'] }}">
                    </div>
                </div>

                <div class="form-group full">
                    <div class="form-field">
                        <label>Address</label>
                        <input type="text" name="farm_address" value="{{ $farm['address'] }}">
                    </div>
                </div>

                <div class="form-group full">
                    <div class="form-field">
                        <label>Email</label>
                        <input type="email" name="farm_email" value="{{ $farm['email'] }}">
                    </div>
                </div>

                <button type="submit" class="btn-primary">Save Farm Information</button>
            </form>
        </div>
    </div>

    <!-- Quail Breed Management Tab -->
    <div class="tab-content" id="tab-quail-breed">
        <h2 class="settings-section-title">Quail Breed Management</h2>

        @if($currentBreeds && count($currentBreeds) > 0)
        @php
            // Get unique breeds to avoid showing duplicates
            $uniqueBreeds = [];
            $breedIds = [];
            foreach ($currentBreeds as $breed) {
                if (!in_array($breed->id, $breedIds)) {
                    $uniqueBreeds[] = $breed;
                    $breedIds[] = $breed->id;
                }
            }
        @endphp
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-bottom: 1rem;">
            @foreach($uniqueBreeds as $breed)
            <div class="settings-card" style="margin: 0;">
                <h3 style="margin: 0 0 0.5rem 0; color: #6d4c41; font-weight: 700;">{{ $breed->name }}</h3>
                <p style="margin: 0 0 1rem 0; color: #8d6e63; font-size: 0.9rem;">{{ $breed->description }}</p>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; font-size: 0.85rem;">
                    <div><strong style="color: #6d4c41;">Scientific Name:</strong><br><span style="color: #8d6e63;">{{ $breed->scientific_name }}</span></div>
                    <div><strong style="color: #6d4c41;">Egg Production:</strong><br><span style="color: #8d6e63;">{{ $breed->egg_production_rate }} eggs/year</span></div>
                    <div><strong style="color: #6d4c41;">Mature Weight:</strong><br><span style="color: #8d6e63;">{{ $breed->mature_weight }}g</span></div>
                    <div><strong style="color: #6d4c41;">Maturity Age:</strong><br><span style="color: #8d6e63;">{{ $breed->maturity_age }} days</span></div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="settings-card" style="text-align: center;">
            <p style="margin: 0; color: #8d6e63;">No breeds selected yet. Click below to choose breeds.</p>
        </div>
        @endif

        @if($farm['breed_start_date'] || $farm['breed_end_date'])
        <div class="settings-card" style="margin-bottom: 1rem; padding: 1rem 1.5rem;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; font-size: 0.9rem;">
                <div>
                    <strong style="color: #6d4c41;">Start Date:</strong><br>
                    <span style="color: #8d6e63;">{{ $farm['breed_start_date'] ? \Carbon\Carbon::parse($farm['breed_start_date'])->format('F d, Y') : '—' }}</span>
                </div>
                <div>
                    <strong style="color: #6d4c41;">End Date:</strong><br>
                    <span style="color: #8d6e63;">{{ $farm['breed_end_date'] ? \Carbon\Carbon::parse($farm['breed_end_date'])->format('F d, Y') : '—' }}</span>
                </div>
            </div>
        </div>
        @endif

        <button type="button" onclick="showBreedSelectorModal()" class="btn-primary">{{ $currentBreeds && count($currentBreeds) > 0 ? 'Change Breeds' : 'Select Two Breeds' }}</button>
    </div>

    <!-- Feeder Schedule Tab -->
    <div class="tab-content" id="tab-feeder-schedule">
        <h2 class="settings-section-title">Feeder Schedule Settings</h2>
        
        <div class="settings-card">
            @if(session('schedule_saved'))
                <div class="success-message">✅ Feeding schedule saved successfully!</div>
            @endif
            @if(session('error'))
                <div style="background: #f8d7da; color: #721c24; padding: 0.75rem 1rem; border-radius: 8px; margin-bottom: 1rem; font-weight: 600;">
                    ❌ {{ session('error') }}
                </div>
            @endif
            
            <p style="margin: 0 0 1.5rem 0; color: #8d6e63; font-size: 0.95rem;">Set the feeding times for your quail birds. You can configure three feeding times per day: morning, afternoon, and evening.</p>

            <form method="POST" action="{{ route('settings.feeder-schedule') }}" id="feederScheduleForm">
                @csrf
                
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                    <!-- Morning Feed Time -->
                    <div class="form-field">
                        <label style="display: flex; align-items: center; gap: 0.5rem;">
                            <span>🌅 Morning Feed</span>
                        </label>
                        <input type="time" name="feed_time_morning" value="{{ $feedTimes['morning'] ?? '08:00' }}" required style="font-size: 1rem;">
                        <p style="margin: 0.5rem 0 0 0; color: #8d6e63; font-size: 0.8rem;">Recommended: 08:00</p>
                    </div>

                    <!-- Afternoon Feed Time -->
                    <div class="form-field">
                        <label style="display: flex; align-items: center; gap: 0.5rem;">
                            <span>☀️ Afternoon Feed</span>
                        </label>
                        <input type="time" name="feed_time_afternoon" value="{{ $feedTimes['afternoon'] ?? '12:00' }}" required style="font-size: 1rem;">
                        <p style="margin: 0.5rem 0 0 0; color: #8d6e63; font-size: 0.8rem;">Recommended: 12:00</p>
                    </div>

                    <!-- Evening Feed Time -->
                    <div class="form-field">
                        <label style="display: flex; align-items: center; gap: 0.5rem;">
                            <span>🌙 Evening Feed</span>
                        </label>
                        <input type="time" name="feed_time_evening" value="{{ $feedTimes['evening'] ?? '17:00' }}" required style="font-size: 1rem;">
                        <p style="margin: 0.5rem 0 0 0; color: #8d6e63; font-size: 0.8rem;">Recommended: 17:00</p>
                    </div>
                </div>

                <button type="submit" class="btn-primary">Save Feeding Schedule</button>
            </form>

            <!-- Display Current Schedule -->
            <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 2px solid #efebe9;">
                <p style="margin: 0 0 1rem 0; color: #6d4c41; font-weight: 600; font-size: 0.95rem;">Current Feeding Schedule:</p>
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                    <div style="padding: 0.75rem; background: linear-gradient(135deg, #fff9f5 0%, #fff7f2 100%); border-radius: 8px; border-left: 3px solid #a1887f; text-align: center;">
                        <p style="margin: 0 0 0.25rem 0; color: #8d6e63; font-size: 0.75rem; font-weight: 600;">Morning</p>
                        <p style="margin: 0; color: #6d4c41; font-size: 1.2rem; font-weight: 700;">{{ $feedTimes['morning'] ?? '08:00' }}</p>
                    </div>
                    <div style="padding: 0.75rem; background: linear-gradient(135deg, #fff9f5 0%, #fff7f2 100%); border-radius: 8px; border-left: 3px solid #a1887f; text-align: center;">
                        <p style="margin: 0 0 0.25rem 0; color: #8d6e63; font-size: 0.75rem; font-weight: 600;">Afternoon</p>
                        <p style="margin: 0; color: #6d4c41; font-size: 1.2rem; font-weight: 700;">{{ $feedTimes['afternoon'] ?? '12:00' }}</p>
                    </div>
                    <div style="padding: 0.75rem; background: linear-gradient(135deg, #fff9f5 0%, #fff7f2 100%); border-radius: 8px; border-left: 3px solid #a1887f; text-align: center;">
                        <p style="margin: 0 0 0.25rem 0; color: #8d6e63; font-size: 0.75rem; font-weight: 600;">Evening</p>
                        <p style="margin: 0; color: #6d4c41; font-size: 1.2rem; font-weight: 700;">{{ $feedTimes['evening'] ?? '17:00' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<!-- Breed Selector Modal -->
<div class="confirm-modal-overlay" id="breedSelectorModal">
    <div class="confirm-modal" style="max-width: 700px; width: 90%; max-height: 80vh; overflow-y: auto;">
        <div class="confirm-modal-title" style="text-align: center;">Select Two Quail Breeds</div>

        <div style="display: grid; gap: 1rem; margin: 1.5rem 0;">
            @foreach($breeds as $breed)
            <div class="breed-option" onclick="selectBreedOption(this)" 
                 data-breed-id="{{ $breed->id }}"
                 data-breed-name="{{ $breed->name }}"
                 data-breed-desc="{{ $breed->description }}"
                 data-breed-scientific="{{ $breed->scientific_name }}"
                 data-breed-eggs="{{ $breed->egg_production_rate }}"
                 data-breed-weight="{{ $breed->mature_weight }}"
                 data-breed-maturity="{{ $breed->maturity_age }}"
                 style="padding: 1rem; border: 2px solid #d7ccc8; border-radius: 10px; cursor: pointer; background: white; transition: all 0.2s; display: flex; justify-content: space-between; align-items: center;" 
                 onmouseover="this.style.borderColor='#a1887f'; this.style.boxShadow='0 4px 12px rgba(141,110,99,0.1)'" 
                 onmouseout="this.style.borderColor='#d7ccc8'; this.style.boxShadow='none'">
                
                <div>
                    <h4 style="margin: 0 0 0.25rem 0; color: #6d4c41; font-weight: 700;">{{ $breed->name }}</h4>
                    <p style="margin: 0 0 0.5rem 0; color: #8d6e63; font-size: 0.85rem; font-style: italic;">{{ $breed->scientific_name }}</p>
                    <p style="margin: 0; color: #6d4c41; font-size: 0.9rem;">{{ $breed->description }}</p>
                </div>
                <div class="breed-selection-number" style="display: none; flex-shrink: 0; margin-left: 1rem; width: 40px; height: 40px; background: #6d4c41; border-radius: 50%; color: white; font-weight: 700; font-size: 1.1rem; display: flex; align-items: center; justify-content: center;">1</div>
            </div>
            @endforeach
        </div>

        <!-- Preview Cards for Selected Breeds -->
        <div id="previewCards" style="display: none; margin-bottom: 1rem;">
            <h4 style="color: #6d4c41; margin-bottom: 0.75rem;">Selected Breeds:</h4>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div id="previewCard1" style="padding: 1rem; background: linear-gradient(135deg, #fff9f5 0%, #fff7f2 100%); border-radius: 10px; border-left: 4px solid #a1887f; display: none;">
                    <h5 style="margin: 0 0 0.5rem 0; color: #6d4c41; font-weight: 700; font-size: 0.95rem;">Breed #1:</h5>
                    <p id="preview-breed-name-1" style="margin: 0 0 0.25rem 0; color: #6d4c41; font-weight: 600;">-</p>
                    <p id="preview-breed-desc-1" style="margin: 0; color: #8d6e63; font-size: 0.8rem;">-</p>
                </div>
                <div id="previewCard2" style="padding: 1rem; background: linear-gradient(135deg, #fff9f5 0%, #fff7f2 100%); border-radius: 10px; border-left: 4px solid #a1887f; display: none;">
                    <h5 style="margin: 0 0 0.5rem 0; color: #6d4c41; font-weight: 700; font-size: 0.95rem;">Breed #2:</h5>
                    <p id="preview-breed-name-2" style="margin: 0 0 0.25rem 0; color: #6d4c41; font-weight: 600;">-</p>
                    <p id="preview-breed-desc-2" style="margin: 0; color: #8d6e63; font-size: 0.8rem;">-</p>
                </div>
            </div>
        </div>

        <!-- Date Fields -->
        <div id="breedDateFields" style="display: none; margin-bottom: 1rem;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-field">
                    <label style="display:block; font-size:0.875rem; font-weight:600; color:#6d4c41; margin-bottom:0.5rem;">Start Date <span style="color:#c0392b;">*</span></label>
                    <input type="date" id="breed_start_date_input" style="width:100%; padding:0.75rem 1rem; border:1.5px solid #d7ccc8; border-radius:10px; font-size:0.95rem; background:#f9fafb;">
                </div>
                <div class="form-field">
                    <label style="display:block; font-size:0.875rem; font-weight:600; color:#6d4c41; margin-bottom:0.5rem;">End Date <span style="color:#c0392b;">*</span></label>
                    <input type="date" id="breed_end_date_input" style="width:100%; padding:0.75rem 1rem; border:1.5px solid #d7ccc8; border-radius:10px; font-size:0.95rem; background:#f9fafb;">
                </div>
            </div>
            <p style="font-size:0.8rem; color:#a1887f; margin-top:0.5rem;">Select the start date when you began raising these breeds and the expected end date of the care period.</p>
        </div>

        <div class="confirm-modal-buttons">
            <button type="button" class="confirm-modal-btn cancel" onclick="closeBreedSelectorModal()">Cancel</button>
            <button type="button" class="confirm-modal-btn confirm" onclick="saveBreedSelection()" id="confirmBreedBtn" disabled>Confirm Selection</button>
        </div>
    </div>
</div>

<script>
let selectedBreeds = [null, null]; // Two breed selections

// Tab functionality
function openTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });

    // Deactivate all tab buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });

    // Show the selected tab content
    document.getElementById('tab-' + tabName).classList.add('active');

    // Activate the selected tab button
    document.getElementById('tab-btn-' + tabName).classList.add('active');
}

function showBreedSelectorModal() {
    document.getElementById('breedSelectorModal').classList.add('show');
    selectedBreeds = [
        document.getElementById('quail_breed_id_1_hidden').value || null,
        document.getElementById('quail_breed_id_2_hidden').value || null
    ];
    
    // Highlight currently selected breeds
    const allBreedOptions = document.querySelectorAll('.breed-option');
    allBreedOptions.forEach(option => {
        const selectionNumber = option.querySelector('.breed-selection-number');
        const breedId = option.dataset.breedId;
        
        if (breedId == selectedBreeds[0]) {
            option.style.borderColor = '#a1887f';
            option.style.boxShadow = '0 4px 12px rgba(141,110,99,0.1)';
            option.style.background = 'linear-gradient(135deg, #fff9f5 0%, #fff7f2 100%)';
            if (selectionNumber) {
                selectionNumber.style.display = 'flex';
                selectionNumber.textContent = '1';
            }
        } else if (breedId == selectedBreeds[1]) {
            option.style.borderColor = '#a1887f';
            option.style.boxShadow = '0 4px 12px rgba(141,110,99,0.1)';
            option.style.background = 'linear-gradient(135deg, #fff9f5 0%, #fff7f2 100%)';
            if (selectionNumber) {
                selectionNumber.style.display = 'flex';
                selectionNumber.textContent = '2';
            }
        } else {
            option.style.borderColor = '#d7ccc8';
            option.style.background = 'white';
            if (selectionNumber) selectionNumber.style.display = 'none';
        }
    });
    
    updatePreviewCards();
}

function closeBreedSelectorModal() {
    document.getElementById('breedSelectorModal').classList.remove('show');
    document.getElementById('previewCards').style.display = 'none';
    document.getElementById('breedDateFields').style.display = 'none';
    document.getElementById('confirmBreedBtn').disabled = true;
}

function selectBreedOption(element) {
    const breedId = element.dataset.breedId;
    
    // If either slot is empty, fill it
    if (!selectedBreeds[0]) {
        selectedBreeds[0] = breedId;
    } else if (!selectedBreeds[1] && breedId != selectedBreeds[0]) {
        selectedBreeds[1] = breedId;
    } else if (selectedBreeds[0] == breedId) {
        // Deselect first breed
        selectedBreeds[0] = selectedBreeds[1];
        selectedBreeds[1] = null;
    } else if (selectedBreeds[1] == breedId) {
        // Deselect second breed
        selectedBreeds[1] = null;
    } else {
        // Replace second breed if both are selected
        selectedBreeds[1] = breedId;
    }
    
    updateBreedOptions();
    updatePreviewCards();
}

function updateBreedOptions() {
    const allBreedOptions = document.querySelectorAll('.breed-option');
    allBreedOptions.forEach(option => {
        const selectionNumber = option.querySelector('.breed-selection-number');
        const breedId = option.dataset.breedId;
        
        if (breedId == selectedBreeds[0]) {
            option.style.borderColor = '#a1887f';
            option.style.boxShadow = '0 4px 12px rgba(141,110,99,0.1)';
            option.style.background = 'linear-gradient(135deg, #fff9f5 0%, #fff7f2 100%)';
            if (selectionNumber) {
                selectionNumber.style.display = 'flex';
                selectionNumber.textContent = '1';
            }
        } else if (breedId == selectedBreeds[1]) {
            option.style.borderColor = '#a1887f';
            option.style.boxShadow = '0 4px 12px rgba(141,110,99,0.1)';
            option.style.background = 'linear-gradient(135deg, #fff9f5 0%, #fff7f2 100%)';
            if (selectionNumber) {
                selectionNumber.style.display = 'flex';
                selectionNumber.textContent = '2';
            }
        } else {
            option.style.borderColor = '#d7ccc8';
            option.style.boxShadow = 'none';
            option.style.background = 'white';
            if (selectionNumber) selectionNumber.style.display = 'none';
        }
    });
}

function updatePreviewCards() {
    const breeds = [];
    const allBreedOptions = document.querySelectorAll('.breed-option');
    
    allBreedOptions.forEach(option => {
        if (option.dataset.breedId == selectedBreeds[0] || option.dataset.breedId == selectedBreeds[1]) {
            breeds.push({
                id: option.dataset.breedId,
                name: option.dataset.breedName,
                desc: option.dataset.breedDesc
            });
        }
    });
    
    if (breeds.length > 0) {
        document.getElementById('breedDateFields').style.display = 'block';
        document.getElementById('previewCards').style.display = 'block';
        
        // Pre-fill existing dates if any
        const existingStart = document.getElementById('breed_start_date_hidden').value;
        const existingEnd   = document.getElementById('breed_end_date_hidden').value;
        if (existingStart) document.getElementById('breed_start_date_input').value = existingStart;
        if (existingEnd)   document.getElementById('breed_end_date_input').value   = existingEnd;
        
        // Update preview for first breed
        if (breeds[0]) {
            document.getElementById('previewCard1').style.display = 'block';
            document.getElementById('preview-breed-name-1').textContent = breeds[0].name;
            document.getElementById('preview-breed-desc-1').textContent = breeds[0].desc;
        } else {
            document.getElementById('previewCard1').style.display = 'none';
        }
        
        // Update preview for second breed
        if (breeds[1]) {
            document.getElementById('previewCard2').style.display = 'block';
            document.getElementById('preview-breed-name-2').textContent = breeds[1].name;
            document.getElementById('preview-breed-desc-2').textContent = breeds[1].desc;
        } else {
            document.getElementById('previewCard2').style.display = 'none';
        }
        
        // Enable confirm button if at least one breed is selected
        if (selectedBreeds[0]) {
            document.getElementById('confirmBreedBtn').disabled = false;
        } else {
            document.getElementById('confirmBreedBtn').disabled = true;
        }
    } else {
        document.getElementById('breedDateFields').style.display = 'none';
        document.getElementById('previewCards').style.display = 'none';
        document.getElementById('confirmBreedBtn').disabled = true;
    }
}

function saveBreedSelection() {
    // Allow saving with at least one breed selected
    if (selectedBreeds[0]) {
        const startDate = document.getElementById('breed_start_date_input').value;
        const endDate   = document.getElementById('breed_end_date_input').value;

        if (!startDate || !endDate) {
            alert('Pakiusap, piliin ang Start Date at End Date.');
            return;
        }
        if (endDate < startDate) {
            alert('Ang End Date ay hindi dapat mas maaga kaysa sa Start Date.');
            return;
        }

        document.getElementById('breed_start_date_hidden').value = startDate;
        document.getElementById('breed_end_date_hidden').value   = endDate;
        document.getElementById('quail_breed_id_1_hidden').value = selectedBreeds[0];
        document.getElementById('quail_breed_id_2_hidden').value = selectedBreeds[1] || ''; // Allow empty second breed
        
        // Get breed names from preview
        const breedName1 = document.getElementById('preview-breed-name-1')?.textContent || 'Breed 1';
        const breedName2 = document.getElementById('preview-breed-name-2')?.textContent || '';
        
        // Show notification
        let notificationMessage = `Saving breed: ${breedName1}`;
        if (breedName2) {
            notificationMessage = `Saving breeds: ${breedName1} and ${breedName2}`;
        }
        
        if (window.notify) {
            window.notify.info(notificationMessage + '...', '📝');
        }
        
        closeBreedSelectorModal();
        
        // Add submit listener for success notification
        const form = document.getElementById('farmSettingsForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                if (window.notify) {
                    let successMsg = `✓ Breed saved: ${breedName1}`;
                    if (breedName2) {
                        successMsg = `✓ Breeds saved: ${breedName1} & ${breedName2}`;
                    }
                    window.notify.success(successMsg, '✅');
                }
            }, { once: true });
        }
        
        document.getElementById('farmSettingsForm').submit();
    } else {
        alert('Pakiusap, pumili ng hindi bababa sa isang breed.');
    }
}

// Close modal when clicking outside
document.getElementById('breedSelectorModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeBreedSelectorModal();
    }
});

// Show success notification when page loads with farm_saved session
document.addEventListener('DOMContentLoaded', function() {
    // Check URL hash for direct tab linking
    const hash = window.location.hash;
    if (hash && hash.startsWith('#tab-')) {
        const tabName = hash.replace('#tab-', '');
        openTab(tabName);
    }

    // Handle farm info success message
    const farmSuccessMessage = document.querySelector('.success-message');
    if (farmSuccessMessage && document.querySelector('input[name="farm_name"]')) {
        const farmName = document.querySelector('input[name="farm_name"]')?.value || 'Farm Settings';
        if (window.notify) {
            window.notify.success(`✓ ${farmName} information updated successfully!`, '✅');
        }
        // Switch to farm-info tab on success
        openTab('farm-info');
        // Hide the old HTML success message
        setTimeout(() => {
            farmSuccessMessage.style.display = 'none';
        }, 500);
    }

    // Handle schedule success messages (all .success-message divs)
    const successMessages = document.querySelectorAll('.success-message');
    successMessages.forEach(msg => {
        if (window.notify) {
            const messageText = msg.textContent.trim();
            if (messageText.includes('Feeding schedule')) {
                window.notify.success('✓ Feeding schedule saved successfully!', '⏰');
                // Switch to feeder-schedule tab on success
                openTab('feeder-schedule');
            } else if (messageText.includes('Farm info')) {
                // Already handled above
                return;
            }
        }
        // Hide HTML success message after 3 seconds
        setTimeout(() => {
            msg.style.display = 'none';
        }, 3000);
    });

    // Add feeder schedule form submission listener
    const feederScheduleForm = document.getElementById('feederScheduleForm');
    if (feederScheduleForm) {
        feederScheduleForm.addEventListener('submit', function(e) {
            const morningTime = document.querySelector('input[name="feed_time_morning"]').value;
            const afternoonTime = document.querySelector('input[name="feed_time_afternoon"]').value;
            const eveningTime = document.querySelector('input[name="feed_time_evening"]').value;
            
            if (window.notify) {
                window.notify.info(`Saving feeding times: ${morningTime}, ${afternoonTime}, ${eveningTime}...`, '⏰');
            }
        });
    }
});
</script>

@endsection
