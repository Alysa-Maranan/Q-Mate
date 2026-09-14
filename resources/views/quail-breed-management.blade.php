@extends('layouts.app')

@section('content')
<style>
    body {
        background: linear-gradient(135deg, #f5f0eb 0%, #efe8e4 50%, #e8dcd6 100%);
        min-height: 100vh;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }
    
    .container {
        max-width: 1000px;
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
    
    .section-card {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 8px 32px rgba(161, 136, 127, 0.12);
        border: 1px solid rgba(161, 136, 127, 0.1);
        margin-bottom: 2rem;
    }
    
    .current-breed-card {
        background: linear-gradient(135deg, #fffdfa 0%, #f5eee6 100%);
        padding: 1.5rem;
        border-radius: 12px;
        border: 2px solid #d7ccc8;
        box-shadow: 0 4px 12px rgba(141,110,99,0.1);
    }
    
    .breed-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1rem;
        margin-top: 1.5rem;
    }
    
    .breed-option {
        padding: 1rem;
        border: 2px solid #d7ccc8;
        border-radius: 10px;
        cursor: pointer;
        background: white;
        transition: all 0.2s;
    }
    
    .breed-option:hover {
        border-color: #a1887f;
        box-shadow: 0 4px 12px rgba(141,110,99,0.1);
    }
    
    .breed-option.selected {
        border-color: #6d4c41;
        background: #f5eee6;
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
    
    .alert {
        padding: 0.75rem 1rem;
        border-radius: 8px;
        margin-bottom: 1rem;
        font-weight: 600;
    }
    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
</style>

@include('components.sidebar')

<div id="main-content-wrap" class="sidebar-content-wrap">
<div class="container" style="padding-top:2.5rem;">
    <div class="page-header">
        <h1 class="page-title">🐤 Quail Breed Management</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success">✅ {{ session('success') }}</div>
    @endif

    <!-- Current Breed Section -->
    <div class="section-card">
        <h3 style="color: #6d4c41; margin: 0 0 1.5rem 0; font-size: 1.2rem;">Current Active Breed</h3>
        
        @if($currentBreed)
        <div class="current-breed-card">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                <div style="flex: 1;">
                    <h3 style="margin: 0 0 0.5rem 0; color: #6d4c41; font-weight: 700; font-size: 1.1rem;">{{ $currentBreed->name }}</h3>
                    <p style="margin: 0; color: #8d6e63; font-size: 0.95rem; line-height: 1.5;">{{ $currentBreed->description }}</p>
                </div>
            </div>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-top: 1rem; font-size: 0.85rem;">
                <div><strong style="color: #6d4c41;">Scientific Name:</strong> {{ $currentBreed->scientific_name }}</div>
                <div><strong style="color: #6d4c41;">Egg Production:</strong> {{ $currentBreed->egg_production_rate }} eggs/year</div>
                <div><strong style="color: #6d4c41;">Mature Weight:</strong> {{ $currentBreed->mature_weight }}g</div>
                <div><strong style="color: #6d4c41;">Maturity Age:</strong> {{ $currentBreed->maturity_age }} days</div>
            </div>
        </div>
        @else
        <div class="current-breed-card" style="text-align: center;">
            <p style="margin: 0; color: #8d6e63;">No breed selected. Please select a breed below.</p>
        </div>
        @endif
    </div>

    <!-- Available Breeds Section -->
    <div class="section-card">
        <h3 style="color: #6d4c41; margin: 0 0 1.5rem 0; font-size: 1.2rem;">Available Breeds</h3>
        
        <form method="POST" action="{{ route('quail-breed.set-current') }}" id="breedForm">
            @csrf
            <input type="hidden" name="breed_id" id="selected_breed_id" value="{{ $currentBreed ? $currentBreed->id : '' }}">
            
            <div class="breed-grid">
                @foreach($breeds as $breed)
                <div class="breed-option {{ $currentBreed && $currentBreed->id == $breed->id ? 'selected' : '' }}" 
                     onclick="selectBreed({{ $breed->id }}, this)"
                     data-breed-id="{{ $breed->id }}">
                    <div style="display: flex; justify-content: space-between; align-items: start;">
                        <div style="flex: 1;">
                            <h4 style="margin: 0 0 0.25rem 0; color: #6d4c41; font-weight: 700;">{{ $breed->name }}</h4>
                            <p style="margin: 0 0 0.5rem 0; color: #8d6e63; font-size: 0.85rem; font-style: italic;">{{ $breed->scientific_name }}</p>
                            <p style="margin: 0 0 0.75rem 0; color: #6d4c41; font-size: 0.9rem;">{{ $breed->description }}</p>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; font-size: 0.8rem;">
                                <div><strong>Eggs/Year:</strong> {{ $breed->egg_production_rate }}</div>
                                <div><strong>Weight:</strong> {{ $breed->mature_weight }}g</div>
                                <div><strong>Maturity:</strong> {{ $breed->maturity_age }} days</div>
                            </div>
                        </div>
                        @if($currentBreed && $currentBreed->id == $breed->id)
                        <div style="display: flex; align-items: center; justify-content: center; width: 24px; height: 24px; background: #6d4c41; border-radius: 50%; color: white; font-weight: 700;">✓</div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1.5rem;" id="saveBtn">
                Save Selected Breed
            </button>
        </form>
    </div>
</div>
</div>

<script>
function selectBreed(breedId, element) {
    // Remove selected class from all options
    document.querySelectorAll('.breed-option').forEach(option => {
        option.classList.remove('selected');
        // Remove checkmark
        const checkmark = option.querySelector('div[style*="background: #6d4c41"]');
        if (checkmark) {
            checkmark.remove();
        }
    });
    
    // Add selected class to clicked option
    element.classList.add('selected');
    
    // Add checkmark
    const checkmark = document.createElement('div');
    checkmark.style.cssText = 'display: flex; align-items: center; justify-content: center; width: 24px; height: 24px; background: #6d4c41; border-radius: 50%; color: white; font-weight: 700;';
    checkmark.textContent = '✓';
    element.querySelector('div').appendChild(checkmark);
    
    // Update hidden input
    document.getElementById('selected_breed_id').value = breedId;
}
</script>
@endsection