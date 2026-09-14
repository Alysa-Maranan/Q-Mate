@extends('layouts.app')

@section('content')
<style>
    body {
        background: linear-gradient(135deg, #f5f0eb 0%, #efe8e4 50%, #e8dcd6 100%);
        min-height: 100vh;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }
    
    .nav-bar {
        background: linear-gradient(135deg, #fffdfa 0%, #f5eee6 100%);
        border-bottom: 2px solid #d7ccc8;
        box-shadow: 0 4px 20px rgba(121, 85, 72, 0.08);
        padding: 1.25rem 3rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1000;
        box-sizing: border-box;
    }
    .logo-section {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .logo {
        width: 50px;
        height: 50px;
        border-radius: 14px;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(121, 85, 72, 0.18);
        border: 2px solid #a1887f;
        overflow: hidden;
    }
    .logo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .logo-text {
        color: #6d4c41;
        font-size: 1.5rem;
        font-weight: 800;
        letter-spacing: 0.5px;
    }
    .nav-menu {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }
    .nav-item {
        color: #6d4c41;
        text-decoration: none;
        font-weight: 700;
        font-size: 1rem;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        transition: all 0.3s ease;
        background: #fffdfa;
        border: 2px solid #a1887f;
        box-shadow: 0 4px 12px rgba(141, 110, 99, 0.10);
        position: relative;
        overflow: hidden;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    .nav-item:hover {
        background: #f8ede3;
        box-shadow: 0 6px 16px rgba(141, 110, 99, 0.18);
    }
    .nav-item.active {
        background: #a1887f;
        color: #fffdfa;
        box-shadow: 0 6px 16px rgba(141, 110, 99, 0.18), 0 0 20px rgba(161, 136, 127, 0.5);
    }

    .ripple {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.6);
        transform: scale(0);
        animation: ripple-animation 0.6s ease-out;
        pointer-events: none;
    }
    @keyframes ripple-animation {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
    
    .container {
        max-width: 100%;
        margin: 0 auto;
        padding: 2.5rem 3rem;
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
    }
    
    .settings-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
    }
    .section-card {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 8px 32px rgba(161, 136, 127, 0.12);
        border: 1px solid rgba(161, 136, 127, 0.1);
    }
    .section-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #6d4c41;
        margin-bottom: 1.5rem;
    }
    
    .input-group {
        margin-bottom: 1.25rem;
    }
    .input-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 600;
        color: #6d4c41;
        margin-bottom: 0.5rem;
    }
    .input-field {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1.5px solid #d7ccc8;
        border-radius: 10px;
        font-size: 0.95rem;
        background: #f9fafb;
        transition: all 0.3s ease;
    }
    .input-field:focus {
        outline: none;
        border-color: #a1887f;
        background: white;
    }
    
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
        background: linear-gradient(135deg, #efebe9 0%, #d7ccc8 100%);
        color: #6d4c41;
        box-shadow: 0 4px 12px rgba(161, 136, 127, 0.3);
    }
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(161, 136, 127, 0.4);
    }
    
    .toggle-switch {
        position: relative;
        width: 60px;
        height: 30px;
        background: #d7ccc8;
        border-radius: 15px;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .toggle-switch.active {
        background: #a1887f;
    }
    .toggle-slider {
        position: absolute;
        top: 3px;
        left: 3px;
        width: 24px;
        height: 24px;
        background: white;
        border-radius: 50%;
        transition: all 0.3s ease;
    }
    .toggle-switch.active .toggle-slider {
        left: 33px;
    }
    
    @media (max-width: 768px) {
        .nav-menu {
            display: none;
        }
        .container {
            padding: 1.5rem 1rem;
        }
        .settings-grid {
            grid-template-columns: 1fr;
        }
    }

    /* 🔔 Notification Bell Styles */
    .notification-bell {
        position: relative;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .bell-icon {
        transition: all 0.3s ease;
        animation: bellRing 0.5s ease-in-out infinite;
    }

    .notification-bell:hover .bell-icon {
        stroke-width: 2.5;
    }

    @keyframes bellRing {
        0%, 100% {
            transform: rotate(0deg);
        }
        25% {
            transform: rotate(-10deg);
        }
        75% {
            transform: rotate(10deg);
        }
    }

    .notification-badge {
        position: absolute;
        top: -8px;
        right: -8px;
        background: linear-gradient(135deg, #ff6b6b 0%, #ff5252 100%);
        color: white;
        border-radius: 50%;
        width: 22px;
        height: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 700;
        box-shadow: 0 2px 8px rgba(255, 107, 107, 0.3);
        animation: badgePulse 2s infinite;
    }

    @keyframes badgePulse {
        0%, 100% {
            box-shadow: 0 2px 8px rgba(255, 107, 107, 0.3);
        }
        50% {
            box-shadow: 0 2px 12px rgba(255, 107, 107, 0.5);
        }
    }

    .notification-dropdown {
        position: absolute;
        top: 60px;
        right: 0;
        width: 380px;
        max-height: 450px;
        overflow-y: auto;
        background: linear-gradient(135deg, #fffdfa 0%, #f5f0eb 100%);
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(109,76,65,0.15), 0 0 1px rgba(109,76,65,0.1);
        border: 2px solid #efebe9;
        z-index: 1001;
        display: none;
    }

    .notification-dropdown.show {
        display: block;
        animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
        from { 
            opacity: 0; 
            transform: translateY(-15px) scale(0.95);
        }
        to { 
            opacity: 1; 
            transform: translateY(0) scale(1);
        }
    }

    .notification-header {
        padding: 1.25rem 1.5rem;
        background: linear-gradient(135deg, #efebe9 0%, #e8ddd8 100%);
        border-bottom: 2px solid #d7ccc8;
        border-radius: 14px 14px 0 0;
        font-weight: 700;
        color: #6d4c41;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 1rem;
    }

    .notification-item {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #efebe9;
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        transition: all 0.2s ease;
        position: relative;
        background: #fffdfa;
    }

    .notification-item:hover {
        background: #fdfcfb;
        padding-left: 1.75rem;
    }

    .notification-item:last-child {
        border-bottom: none;
    }

    .notification-item.success {
        border-left: 4px solid #10b981;
        background: linear-gradient(135deg, rgba(16,185,129,0.05) 0%, transparent 100%);
    }

    .notification-item.info {
        border-left: 4px solid #3b82f6;
        background: linear-gradient(135deg, rgba(59,130,246,0.05) 0%, transparent 100%);
    }

    .notification-item.warning {
        border-left: 4px solid #f59e0b;
        background: linear-gradient(135deg, rgba(245,158,11,0.05) 0%, transparent 100%);
    }

    .notification-item.error {
        border-left: 4px solid #ef4444;
        background: linear-gradient(135deg, rgba(239,68,68,0.05) 0%, transparent 100%);
    }

    .notification-icon {
        font-size: 1.5rem;
        flex-shrink: 0;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: #f0ebe6;
    }

    .notification-item.success .notification-icon {
        background: rgba(16,185,129,0.1);
    }

    .notification-item.info .notification-icon {
        background: rgba(59,130,246,0.1);
    }

    .notification-item.warning .notification-icon {
        background: rgba(245,158,11,0.1);
    }

    .notification-item.error .notification-icon {
        background: rgba(239,68,68,0.1);
    }

    .notification-content {
        flex: 1;
    }

    .notification-text {
        font-size: 0.9rem;
        color: #4e342e;
        font-weight: 500;
        line-height: 1.4;
    }

    .notification-time {
        font-size: 0.75rem;
        color: #a1887f;
        margin-top: 0.35rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .notification-empty {
        padding: 3rem 2rem;
        text-align: center;
        color: #8d6e63;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .notification-empty p {
        margin: 0;
        color: #6d4c41;
        font-weight: 600;
    }

    .clear-notifications {
        font-size: 0.8rem;
        color: #ef4444;
        cursor: pointer;
        text-decoration: none;
        font-weight: 600;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        transition: all 0.2s ease;
    }

    .clear-notifications:hover {
        background: rgba(239,68,68,0.1);
    }

    /* Scrollbar styling */
    .notification-dropdown::-webkit-scrollbar {
        width: 6px;
    }

    .notification-dropdown::-webkit-scrollbar-track {
        background: #f0ebe6;
        border-radius: 10px;
    }

    .notification-dropdown::-webkit-scrollbar-thumb {
        background: #d7ccc8;
        border-radius: 10px;
    }

    .notification-dropdown::-webkit-scrollbar-thumb:hover {
        background: #a1887f;
    }
</style>

@include('components.sidebar')

<div id="main-content-wrap" class="sidebar-content-wrap">
<div class="container" style="padding-top:2.5rem;">
    <div class="page-header">
        <h1 class="page-title">⚙️ {{ __('settings.title') }}</h1>
    </div>

    <div class="settings-grid">
        <!-- Profile Settings -->
        <div class="section-card">
            <h3 class="section-title">👤 {{ __('settings.profile_settings') }}</h3>
            <div class="input-group">
                <label class="input-label">{{ __('settings.full_name') }}</label>
                <input type="text" class="input-field" value="{{ auth()->user()->name }}">
            </div>
            <div class="input-group">
                <label class="input-label">{{ __('settings.email_address') }}</label>
                <input type="email" class="input-field" value="{{ auth()->user()->email }}">
            </div>
            <button class="btn btn-primary" style="width: 100%;">{{ __('settings.update_profile') }}</button>
        </div>

        <!-- Change Password -->
        <div class="section-card">
            <h3 class="section-title">🔒 {{ __('settings.change_password') }}</h3>
            <div class="input-group">
                <label class="input-label">{{ __('settings.current_password') }}</label>
                <input type="password" class="input-field" placeholder="••••••••">
            </div>
            <div class="input-group">
                <label class="input-label">{{ __('settings.new_password') }}</label>
                <input type="password" class="input-field" placeholder="••••••••">
            </div>
            <div class="input-group">
                <label class="input-label">{{ __('settings.confirm_new_password') }}</label>
                <input type="password" class="input-field" placeholder="••••••••">
            </div>
            <button class="btn btn-primary" style="width: 100%;">{{ __('settings.change_password') }}</button>
        </div>
    </div>

    <!-- Farm Information -->
    <div style="margin-top: 2rem;">
        <div class="section-card">
            <h3 class="section-title">🏡 Farm Information</h3>
            @if(session('farm_saved'))
                <div style="background:#d4edda;color:#155724;padding:0.75rem 1rem;border-radius:8px;margin-bottom:1rem;font-weight:600;">✅ Farm info saved!</div>
            @endif
            <form method="POST" action="{{ route('settings.farm-info') }}" id="farmSettingsForm">
                @csrf
                <input type="hidden" name="quail_breed_id" id="quail_breed_id_hidden" value="{{ $currentBreed ? $currentBreed->id : '' }}">
                <div class="input-group">
                    <label class="input-label">Farm Name</label>
                    <input type="text" name="farm_name" class="input-field" value="{{ $farm['name'] }}">
                </div>
                <div class="input-group">
                    <label class="input-label">Address</label>
                    <input type="text" name="farm_address" class="input-field" value="{{ $farm['address'] }}">
                </div>
                <div class="input-group">
                    <label class="input-label">Contact Number</label>
                    <input type="text" name="farm_phone" class="input-field" value="{{ $farm['phone'] }}">
                </div>
                <div class="input-group">
                    <label class="input-label">Email</label>
                    <input type="email" name="farm_email" class="input-field" value="{{ $farm['email'] }}">
                </div>

                <!-- Quail Breed Selection Section -->
                <div style="margin-top: 2rem; padding-top: 2rem; border-top: 2px solid #d7ccc8;">
                    <h3 style="color: #6d4c41; margin: 0 0 1.5rem 0; font-size: 1.2rem;">🐤 Quail Breed Management</h3>
                    
                    @if($currentBreed)
                    <!-- Current Breed Card -->
                    <div style="background: linear-gradient(135deg, #fffdfa 0%, #f5eee6 100%); padding: 1.5rem; border-radius: 12px; margin-bottom: 1.5rem; border: 2px solid #d7ccc8; box-shadow: 0 4px 12px rgba(141,110,99,0.1);">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                            <div style="flex: 1;">
                                <h3 id="display-breed-name" style="margin: 0 0 0.5rem 0; color: #6d4c41; font-weight: 700; font-size: 1.1rem;">{{ $currentBreed->name }}</h3>
                                <p id="display-breed-desc" style="margin: 0; color: #8d6e63; font-size: 0.95rem; line-height: 1.5;">{{ $currentBreed->description }}</p>
                            </div>
                            <div>
                                <button type="button" onclick="showBreedSelectorModal()" style="background: #6d4c41; color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.2s;">Change Breed</button>
                            </div>
                        </div>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-top: 1rem; font-size: 0.85rem;">
                            <div><strong style="color: #6d4c41;">Scientific Name:</strong> <span id="display-scientific">{{ $currentBreed->scientific_name }}</span></div>
                            <div><strong style="color: #6d4c41;">Egg Production:</strong> <span id="display-eggs">{{ $currentBreed->egg_production_rate }}</span> eggs/year</div>
                            <div><strong style="color: #6d4c41;">Mature Weight:</strong> <span id="display-weight">{{ $currentBreed->mature_weight }}</span>g</div>
                            <div><strong style="color: #6d4c41;">Maturity Age:</strong> <span id="display-maturity">{{ $currentBreed->maturity_age }}</span> days</div>
                        </div>
                    </div>
                    @else
                    <div style="background: linear-gradient(135deg, #fffdfa 0%, #f5eee6 100%); padding: 1.5rem; border-radius: 12px; margin-bottom: 1.5rem; border: 2px solid #d7ccc8; text-align: center;">
                        <p style="margin: 0; color: #8d6e63;">No breed selected. Click below to select a breed.</p>
                    </div>
                    @endif

                    <input type="hidden" name="quail_breed_id" id="quail_breed_id_hidden" value="{{ $currentBreed ? $currentBreed->id : '' }}">
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1.5rem;">Save Farm Info</button>
            </form>
        </div>
    </div>

    <!-- Breed Selector Modal -->
    <div id="breedSelectorModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: linear-gradient(135deg, #fffdfa 0%, #f5eee6 100%); padding: 2rem; border-radius: 12px; max-width: 600px; width: 90%; max-height: 80vh; overflow-y: auto; border: 2px solid #d7ccc8; box-shadow: 0 10px 40px rgba(141,110,99,0.2);">
            <h3 style="margin: 0 0 1.5rem 0; color: #6d4c41; font-size: 1.3rem;">🐤 Select Quail Breed</h3>
            
            <div style="display: grid; gap: 1rem; margin-bottom: 1.5rem;">
                @foreach($breeds as $breed)
                <div class="breed-option" onclick="selectBreedPreview(this)" 
                     data-breed-id="{{ $breed->id }}"
                     data-breed-name="{{ $breed->name }}"
                     data-breed-desc="{{ $breed->description }}"
                     data-breed-scientific="{{ $breed->scientific_name }}"
                     data-breed-eggs="{{ $breed->egg_production_rate }}"
                     data-breed-weight="{{ $breed->mature_weight }}"
                     data-breed-maturity="{{ $breed->maturity_age }}"
                     style="padding: 1rem; border: 2px solid #d7ccc8; border-radius: 10px; cursor: pointer; background: white; transition: all 0.2s;" onmouseover="this.style.borderColor='#a1887f'; this.style.boxShadow='0 4px 12px rgba(141,110,99,0.1)'" onmouseout="this.style.borderColor='#d7ccc8'; this.style.boxShadow='none'">
                    <div style="display: flex; justify-content: space-between; align-items: start;">
                        <div style="flex: 1;">
                            <h4 style="margin: 0 0 0.25rem 0; color: #6d4c41; font-weight: 700;">{{ $breed->name }}</h4>
                            <p style="margin: 0 0 0.5rem 0; color: #8d6e63; font-size: 0.85rem; font-style: italic;">{{ $breed->scientific_name }}</p>
                            <p style="margin: 0; color: #6d4c41; font-size: 0.9rem;">{{ $breed->description }}</p>
                        </div>
                        <div class="breed-checkmark" style="display: none; align-items: center; justify-content: center; width: 24px; height: 24px; background: #6d4c41; border-radius: 50%; color: white; font-weight: 700; margin-left: 1rem;">✓</div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Preview Card -->
            <div id="previewCard" style="padding: 1.5rem; background: linear-gradient(135deg, #fff9f5 0%, #fff7f2 100%); border-radius: 10px; border-left: 4px solid #a1887f; margin-bottom: 1rem; display: none;">
                <h4 id="preview-breed-name" style="margin: 0 0 0.5rem 0; color: #6d4c41; font-weight: 700;">Breed Name</h4>
                <p id="preview-breed-desc" style="margin: 0 0 0.75rem 0; color: #8d6e63; font-size: 0.95rem;"></p>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; font-size: 0.85rem;">
                    <div><strong style="color: #6d4c41;">Scientific:</strong> <span id="preview-scientific"></span></div>
                    <div><strong style="color: #6d4c41;">Eggs/Year:</strong> <span id="preview-eggs"></span></div>
                    <div><strong style="color: #6d4c41;">Weight:</strong> <span id="preview-weight"></span>g</div>
                    <div><strong style="color: #6d4c41;">Maturity:</strong> <span id="preview-maturity"></span> days</div>
                </div>
            </div>

            <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                <button type="button" onclick="closeBreedSelectorModal()" style="background: #efebe9; color: #6d4c41; border: none; padding: 0.75rem 1.5rem; border-radius: 8px; cursor: pointer; font-weight: 600;">Cancel</button>
                <button type="button" onclick="saveBreedSelection()" style="background: #6d4c41; color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 8px; cursor: pointer; font-weight: 600;" id="confirmBreedBtn" disabled>Confirm</button>
            </div>
        </div>
    </div>

</div>

<script>
let selectedBreedId = null;

function showBreedSelectorModal() {
    document.getElementById('breedSelectorModal').style.display = 'flex';
    selectedBreedId = document.getElementById('quail_breed_id_hidden').value || null;
    
    // Show checkmark for currently selected breed
    if (selectedBreedId) {
        const allBreedOptions = document.querySelectorAll('.breed-option');
        allBreedOptions.forEach(option => {
            const checkmark = option.querySelector('.breed-checkmark');
            if (option.dataset.breedId == selectedBreedId) {
                if (checkmark) checkmark.style.display = 'flex';
                option.style.borderColor = '#a1887f';
            } else {
                if (checkmark) checkmark.style.display = 'none';
                option.style.borderColor = '#d7ccc8';
            }
        });
    }
}

function closeBreedSelectorModal() {
    document.getElementById('breedSelectorModal').style.display = 'none';
    document.getElementById('previewCard').style.display = 'none';
    document.getElementById('confirmBreedBtn').disabled = true;
    selectedBreedId = null;
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
    console.log('Breed selected: ' + name + ' (ID: ' + breedId + ')');
    
    // Remove checkmark from all breed options
    const allBreedOptions = document.querySelectorAll('.breed-option');
    allBreedOptions.forEach(option => {
        const checkmark = option.querySelector('.breed-checkmark');
        if (checkmark) {
            checkmark.style.display = 'none';
        }
        option.style.borderColor = '#d7ccc8';
    });
    
    // Add checkmark to this breed option and highlight it
    const thisCheckmark = element.querySelector('.breed-checkmark');
    if (thisCheckmark) {
        thisCheckmark.style.display = 'flex';
    }
    element.style.borderColor = '#a1887f';
    
    // Update preview card
    document.getElementById('preview-breed-name').textContent = name;
    document.getElementById('preview-breed-desc').textContent = desc;
    document.getElementById('preview-scientific').textContent = scientific;
    document.getElementById('preview-eggs').textContent = eggs;
    document.getElementById('preview-weight').textContent = weight;
    document.getElementById('preview-maturity').textContent = maturity;
    
    document.getElementById('previewCard').style.display = 'block';
    document.getElementById('confirmBreedBtn').disabled = false;
}

function saveBreedSelection() {
    alert('Function called! Selected Breed ID: ' + selectedBreedId);
    
    if (selectedBreedId) {
        alert('Setting breed to: ' + selectedBreedId);
        
        const hiddenInput = document.getElementById('quail_breed_id_hidden');
        alert('Hidden input element found: ' + (hiddenInput ? 'YES' : 'NO'));
        
        if (hiddenInput) {
            hiddenInput.value = selectedBreedId;
            alert('Hidden input value set to: ' + hiddenInput.value);
        }
        
        // Close modal
        document.getElementById('breedSelectorModal').style.display = 'none';
        document.getElementById('previewCard').style.display = 'none';
        document.getElementById('confirmBreedBtn').disabled = true;
        
        // Submit form immediately
        const form = document.getElementById('farmSettingsForm');
        alert('Form found: ' + (form ? 'YES' : 'NO'));
        
        if (form) {
            alert('Submitting form now!');
            form.submit();
        }
    } else {
        alert('Please select a breed first');
    }
}

// Close modal when clicking outside
document.getElementById('breedSelectorModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeBreedSelectorModal();
    }
});

function createRipple(event) {
    const button = event.currentTarget;
    const ripple = document.createElement('span');
    const rect = button.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    const x = event.clientX - rect.left - size / 2;
    const y = event.clientY - rect.top - size / 2;
    
    ripple.style.width = ripple.style.height = size + 'px';
    ripple.style.left = x + 'px';
    ripple.style.top = y + 'px';
    ripple.classList.add('ripple');
    
    button.appendChild(ripple);
    
    setTimeout(() => {
        ripple.remove();
    }, 600);
}
</script>

<script>
// ─── 🔔 NOTIFICATION BELL SYSTEM (shared via localStorage) ───
const NOTIFICATION_STORAGE_KEY = 'squifm_notifications';

function getNotifications() {
    try { return JSON.parse(localStorage.getItem(NOTIFICATION_STORAGE_KEY) || '[]'); }
    catch { return []; }
}

function saveNotifications(notifications) {
    localStorage.setItem(NOTIFICATION_STORAGE_KEY, JSON.stringify(notifications));
}

function getSeenCount() {
    return parseInt(localStorage.getItem('squifm_notifications_seen') || '0', 10);
}

function setSeenCount(count) {
    localStorage.setItem('squifm_notifications_seen', count.toString());
}

function renderNotifications() {
    const list = document.getElementById('notification-list');
    const badge = document.getElementById('notification-badge');
    let notifications = getNotifications();
    if (!list || !badge) return;

    if (notifications.length === 0) {
        list.innerHTML = '<div class="notification-empty">No notifications yet.</div>';
        badge.style.display = 'none';
        badge.classList.remove('has-notifications');
        return;
    }

    const seenCount = getSeenCount();
    const unseenCount = Math.max(0, notifications.length - seenCount);
    
    if (unseenCount > 0) {
        badge.style.display = 'flex';
        badge.textContent = unseenCount;
        badge.classList.add('has-notifications');
    } else {
        badge.style.display = 'none';
        badge.classList.remove('has-notifications');
    }

    list.innerHTML = '';
    notifications.forEach(n => {
        const item = document.createElement('div');
        item.className = 'notification-item ' + (n.type || 'success');
        const isOrderNotif = n.message && n.message.includes('Order');
        item.style.cssText = 'display: flex; align-items: flex-start; gap: 0.75rem; padding: 0.75rem; cursor: ' + (isOrderNotif ? 'pointer' : 'default') + ';';
        item.innerHTML = `
            <span class="notification-icon">${n.icon || (n.type === 'success' ? '✅' : '📅')}</span>
            <div class="notification-content">
                <div class="notification-text">${n.message}</div>
                <div class="notification-time">${n.time}</div>
            </div>
        `;
        if (isOrderNotif) {
            item.onclick = function() {
                goToOrdersTab();
            };
        }
        list.appendChild(item);
    });
}

// Go to Order tab (redirects to inventory page)
function goToOrdersTab() {
    window.location.href = '/inventory?tab=orders';
}

function toggleNotificationDropdown() {
    const dropdown = document.getElementById('notification-dropdown');
    const badge = document.getElementById('notification-badge');
    if (dropdown) {
        dropdown.classList.toggle('show');
        // Mark all notifications as seen when dropdown is opened
        if (dropdown.classList.contains('show')) {
            const notifications = getNotifications();
            setSeenCount(notifications.length);
            if (badge) {
                badge.textContent = '0';
                badge.style.display = 'none';
                badge.classList.remove('has-notifications');
            }
        }
    }
}

function clearNotifications(event) {
    event.stopPropagation();
    saveNotifications([]);
    renderNotifications();
}

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    const bell = document.getElementById('notification-bell');
    const dropdown = document.getElementById('notification-dropdown');
    if (bell && dropdown && !bell.contains(e.target)) {
        dropdown.classList.remove('show');
    }
});

// Render on page load
document.addEventListener('DOMContentLoaded', renderNotifications);

function updateMessagesBadge() {
    try {
        const n    = JSON.parse(localStorage.getItem(NOTIFICATION_STORAGE_KEY) || '[]');
        const seen = parseInt(localStorage.getItem('squifm_messages_seen') || '0', 10);
        const unread = Math.max(0, n.length - seen);
        const b = document.getElementById('messages-nav-badge');
        if (!b) return;
        if (unread > 0) { b.textContent = unread; b.style.display = 'inline-flex'; }
        else { b.style.display = 'none'; }
    } catch(e) {}
}
document.addEventListener('DOMContentLoaded', updateMessagesBadge);
setInterval(updateMessagesBadge, 4000);
</script>
@endsection

