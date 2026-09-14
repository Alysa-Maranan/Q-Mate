@extends('layouts.app')

@section('content')
<style>
    body {
        background: linear-gradient(135deg, #f5f0eb 0%, #efe8e4 50%, #e8dcd6 100%);
        min-height: 100vh;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }
    
    .container {
        max-width: 800px;
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
        box-sizing: border-box;
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
        <h1 class="page-title">🏡 Farm Information</h1>
    </div>

    <div class="section-card">
        @if(session('success'))
            <div class="alert alert-success">✅ {{ session('success') }}</div>
        @endif
        
        <form method="POST" action="{{ route('farm-info.update') }}">
            @csrf
            <div class="input-group">
                <label class="input-label">Farm Name</label>
                <input type="text" name="farm_name" class="input-field" value="{{ $farm['name'] }}" required>
                @error('farm_name')
                    <div style="color: #dc3545; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="input-group">
                <label class="input-label">Address</label>
                <input type="text" name="farm_address" class="input-field" value="{{ $farm['address'] }}" required>
                @error('farm_address')
                    <div style="color: #dc3545; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="input-group">
                <label class="input-label">Contact Number</label>
                <input type="text" name="farm_phone" class="input-field" value="{{ $farm['phone'] }}" required>
                @error('farm_phone')
                    <div style="color: #dc3545; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="input-group">
                <label class="input-label">Email</label>
                <input type="email" name="farm_email" class="input-field" value="{{ $farm['email'] }}" required>
                @error('farm_email')
                    <div style="color: #dc3545; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Save Farm Information</button>
        </form>
    </div>
</div>
</div>
@endsection