@extends('customer.layout')
@section('page-title', 'My Profile')
@section('active-nav', 'profile')
@section('header-action')
<style>
.content-header .content-title { display: none; }
</style>
@endsection
@section('customer-content')
<style>
.hidden { display: none; }
.form-group { margin-bottom: 1.5rem; }
.form-label { display: block; font-size: 0.8rem; color: #a1887f; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.5rem; }
.form-input { width: 100%; padding: 0.75rem; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 0.9rem; font-family: inherit; transition: all 0.2s; background: #faf8f6; color: #4e342e; font-weight: 500; }
.form-input:focus { outline: none; border-color: #a1887f; box-shadow: 0 0 0 3px rgba(161,136,127,0.1); background: white; }
.info-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; }
.profile-section { margin-bottom: 2rem; }
.form-actions { display: flex; gap: 1rem; margin-top: 2rem; justify-content: flex-end; }
.btn { padding: 0.75rem 1.5rem; border: none; border-radius: 8px; font-size: 0.9rem; font-weight: 600; cursor: pointer; transition: all 0.2s; }
.btn-primary { background: linear-gradient(135deg, #6d4c41 0%, #4e342e 100%); color: white; }
.btn-primary:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(109,76,65,0.3); }
.btn-secondary { background: #f5f0eb; color: #6d4c41; border: 1px solid #d7ccc8; }
.btn-secondary:hover { background: #efebe9; }
.edit-btn { padding: 0.5rem 1rem; background: linear-gradient(135deg, #6d4c41 0%, #4e342e 100%); color: white; border: none; border-radius: 8px; font-size: 0.9rem; font-weight: 600; cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; gap: 0.5rem; }
.edit-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(109,76,65,0.3); }
.content-body { padding: 2rem; }
.page-header { padding: 2rem; text-align: center; border-bottom: 1px solid #efebe9; }
.page-title { font-size: 2rem; font-weight: 700; color: #4e342e; margin-bottom: 0.5rem; }
.page-subtitle { text-align: center; color: #8d6e63; }
@media (max-width: 768px) { .info-grid { grid-template-columns: 1fr; } }
</style>

<div style="padding: 1.5rem;">
    <!-- Page Header -->
    <div style="background: linear-gradient(135deg, #6d4c41 0%, #4e342e 100%); border-radius: 16px; padding: 1.5rem 2rem; margin-bottom: 2rem; color: white;">
        <h1 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.75rem;">
            My Profile
        </h1>
        <p style="font-size: 0.9rem; opacity: 0.9; margin: 0;">Manage your personal information and delivery address</p>
    </div>

    <div style="padding: 0 0 1.5rem 0; border-bottom: 1px solid #efebe9; display: flex; justify-content: flex-end;">
        <button class="edit-btn" id="editBtn" onclick="toggleEdit()">
            <i class="ph-bold ph-pencil-simple"></i>
            Edit Profile
        </button>
    </div>

<div class="content-body">
    <div id="viewMode">
        <div style="background: white; border-radius: 16px; padding: 0; margin-bottom: 1.5rem; border: 1px solid rgba(161,136,127,0.2); overflow: hidden; box-shadow: 0 2px 8px rgba(109,76,65,0.08);">
            <div style="background: linear-gradient(135deg, #f8f5f1 0%, #efebe9 100%); padding: 1.25rem 1.5rem; border-bottom: 1px solid rgba(161,136,127,0.15);">
                <h2 style="font-size: 1.2rem; font-weight: 700; color: #4e342e; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="ph-bold ph-user-circle" style="font-size: 1.5rem; color: #6d4c41;"></i>
                    Personal Information
                </h2>
            </div>
            <div style="padding: 1.5rem;">
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
                    <div>
                        <p style="font-size: 0.8rem; color: #a1887f; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.5rem;">Full Name</p>
                        <p style="font-size: 1.05rem; font-weight: 600; color: #4e342e; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="ph-bold ph-user" style="font-size: 1.2rem; color: #6d4c41;"></i>
                            {{ auth('customer')->user()->name }}
                        </p>
                    </div>
                    <div>
                        <p style="font-size: 0.8rem; color: #a1887f; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.5rem;">Email Address</p>
                        <p style="font-size: 1.05rem; font-weight: 600; color: #4e342e; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="ph-bold ph-envelope" style="font-size: 1.2rem; color: #6d4c41;"></i>
                            {{ auth('customer')->user()->email }}
                        </p>
                    </div>
                    <div>
                        <p style="font-size: 0.8rem; color: #a1887f; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.5rem;">Phone Number</p>
                        <p style="font-size: 1.05rem; font-weight: 600; color: #4e342e; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="ph-bold ph-phone" style="font-size: 1.2rem; color: #6d4c41;"></i>
                            {{ auth('customer')->user()->phone ?? 'Not provided' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div style="background: white; border-radius: 16px; padding: 0; margin-bottom: 1.5rem; border: 1px solid rgba(161,136,127,0.2); overflow: hidden; box-shadow: 0 2px 8px rgba(109,76,65,0.08);">
            <div style="background: linear-gradient(135deg, #f8f5f1 0%, #efebe9 100%); padding: 1.25rem 1.5rem; border-bottom: 1px solid rgba(161,136,127,0.15);">
                <h2 style="font-size: 1.2rem; font-weight: 700; color: #4e342e; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="ph-bold ph-map-pin" style="font-size: 1.5rem; color: #6d4c41;"></i>
                    Address Information
                </h2>
            </div>
            <div style="padding: 1.5rem;">
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
                    <div style="grid-column: 1 / -1;">
                        <p style="font-size: 0.8rem; color: #a1887f; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.5rem;">Complete Address</p>
                        <p style="font-size: 1.05rem; font-weight: 600; color: #4e342e; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="ph-bold ph-house" style="font-size: 1.2rem; color: #6d4c41;"></i>
                            {{ auth('customer')->user()->address ?? 'Not provided' }}
                        </p>
                    </div>
                    <div>
                        <p style="font-size: 0.8rem; color: #a1887f; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.5rem;">Barangay</p>
                        <p style="font-size: 1.05rem; font-weight: 600; color: #4e342e; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="ph-bold ph-map-pin-line" style="font-size: 1.2rem; color: #6d4c41;"></i>
                            {{ auth('customer')->user()->barangay ?? 'Not provided' }}
                        </p>
                    </div>
                    <div>
                        <p style="font-size: 0.8rem; color: #a1887f; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.5rem;">Municipality</p>
                        <p style="font-size: 1.05rem; font-weight: 600; color: #4e342e; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="ph-bold ph-buildings" style="font-size: 1.2rem; color: #6d4c41;"></i>
                            {{ auth('customer')->user()->municipality ?? 'Not provided' }}
                        </p>
                    </div>
                    <div>
                        <p style="font-size: 0.8rem; color: #a1887f; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.5rem;">Province</p>
                        <p style="font-size: 1.05rem; font-weight: 600; color: #4e342e; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="ph-bold ph-globe" style="font-size: 1.2rem; color: #6d4c41;"></i>
                            {{ auth('customer')->user()->province ?? 'Not provided' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="editMode" class="hidden">
        <form method="POST" action="{{ route('customer.profile.update') }}">
            @csrf
            @method('PUT')

            <div style="background: white; border-radius: 16px; padding: 0; margin-bottom: 1.5rem; border: 1px solid rgba(161,136,127,0.2); overflow: hidden; box-shadow: 0 2px 8px rgba(109,76,65,0.08);">
                <div style="background: linear-gradient(135deg, #f8f5f1 0%, #efebe9 100%); padding: 1.25rem 1.5rem; border-bottom: 1px solid rgba(161,136,127,0.15);">
                    <h2 style="font-size: 1.2rem; font-weight: 700; color: #4e342e; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="ph-bold ph-user-circle" style="font-size: 1.5rem; color: #6d4c41;"></i>
                        Personal Information
                    </h2>
                </div>
                <div style="padding: 1.5rem;">
                    <div class="info-grid">
                        <div class="form-group">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-input" value="{{ auth('customer')->user()->name }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-input" value="{{ auth('customer')->user()->email }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone" class="form-input" value="{{ auth('customer')->user()->phone }}">
                        </div>
                    </div>
                </div>
            </div>

            <div style="background: white; border-radius: 16px; padding: 0; margin-bottom: 1.5rem; border: 1px solid rgba(161,136,127,0.2); overflow: hidden; box-shadow: 0 2px 8px rgba(109,76,65,0.08);">
                <div style="background: linear-gradient(135deg, #f8f5f1 0%, #efebe9 100%); padding: 1.25rem 1.5rem; border-bottom: 1px solid rgba(161,136,127,0.15);">
                    <h2 style="font-size: 1.2rem; font-weight: 700; color: #4e342e; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="ph-bold ph-map-pin" style="font-size: 1.5rem; color: #6d4c41;"></i>
                        Address Information
                    </h2>
                </div>
                <div style="padding: 1.5rem;">
                    <div class="info-grid">
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label class="form-label">Complete Address</label>
                            <input type="text" name="address" class="form-input" value="{{ auth('customer')->user()->address }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Barangay</label>
                            <input type="text" name="barangay" class="form-input" value="{{ auth('customer')->user()->barangay }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Municipality</label>
                            <input type="text" name="municipality" class="form-input" value="{{ auth('customer')->user()->municipality }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Province</label>
                            <input type="text" name="province" class="form-input" value="{{ auth('customer')->user()->province }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="toggleEdit()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleEdit() {
    const viewMode = document.getElementById('viewMode');
    const editMode = document.getElementById('editMode');
    const editBtn = document.getElementById('editBtn');
    
    if (viewMode.classList.contains('hidden')) {
        viewMode.classList.remove('hidden');
        editMode.classList.add('hidden');
        editBtn.innerHTML = '<i class="ph-bold ph-pencil-simple"></i> Edit Profile';
    } else {
        viewMode.classList.add('hidden');
        editMode.classList.remove('hidden');
        editBtn.innerHTML = '<i class="ph-bold ph-x"></i> Cancel';
    }
}
</script>
</div>
@endsection