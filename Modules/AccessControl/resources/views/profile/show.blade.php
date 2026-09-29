@extends('layouts/contentNavbarLayout')

@section('title', __('My Profile') . ' - ' . config('variables.templateName'))

@section('page-style')
<style>
.profile-hero-card {
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    border-radius: 1rem;
    position: relative;
    overflow: hidden;
    color: #fff;
}
.profile-hero-card::after {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 320px;
    height: 320px;
    background: radial-gradient(circle, rgba(105, 108, 255, 0.25) 0%, transparent 70%);
    pointer-events: none;
}
.profile-avatar-wrapper {
    position: relative;
    width: 110px;
    height: 110px;
    border-radius: 50%;
    border: 4px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 24px rgba(0,0,0,0.25);
    background: #2b3548;
}
.profile-avatar-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
}
.profile-status-dot {
    position: absolute;
    bottom: 6px;
    right: 6px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 3px solid #1e293b;
}
.avatar-upload-box {
    border: 2px dashed #d9dee3;
    border-radius: 0.75rem;
    padding: 1rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s ease;
    background: #fafbfc;
}
.avatar-upload-box:hover {
    border-color: #696cff;
    background: #f4f5fb;
}
[dir="rtl"] .profile-hero-card::after {
    right: auto;
    left: -20%;
}
[dir="rtl"] .profile-status-dot {
    right: auto;
    left: 6px;
}
</style>
@endsection

@section('content')
<!-- Breadcrumbs -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div class="d-flex align-items-center gap-2">
        <a href="{{ url('/') }}" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1 rounded-pill px-3">
            <i class="icon-base bx bx-home font-15"></i>
            <span>{{ __('Dashboard') }}</span>
        </a>
        <span class="text-muted small">/</span>
        <span class="fw-medium small text-primary">{{ __('My Profile') }}</span>
    </div>
    <div class="d-flex align-items-center gap-2">
        @php
            $activeTpls = $printTemplates ?? \Modules\Settings\Models\PrintTemplate::where('is_active', true)->get();
        @endphp
        <div class="btn-group">
            <a href="{{ route('profile.print') }}" target="_blank" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1 shadow-xs">
                <i class="icon-base bx bx-printer font-15"></i>
                <span>{{ __('Print') }}</span>
            </a>
            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false" title="{{ __('Print Templates') }}">
                <span class="visually-hidden">{{ __('Print Templates') }}</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow">
                <li><h6 class="dropdown-header text-uppercase small">{{ __('اختر قالب الطباعة:') }}</h6></li>
                @forelse($activeTpls as $tpl)
                    <li>
                        <a class="dropdown-item d-flex align-items-center justify-content-between py-2" target="_blank" 
                           href="{{ route('profile.print') }}?template_id={{ $tpl->id }}">
                            <span>
                                <i class="bx bx-file me-2 text-primary"></i> {{ $tpl->display_name }}
                                <small class="text-muted d-block ps-4">({{ $tpl->page_size }} - {{ ucfirst($tpl->orientation) }})</small>
                            </span>
                            @if($tpl->is_default)
                                <span class="badge bg-label-primary small">{{ __('افتراضي') }}</span>
                            @endif
                        </a>
                    </li>
                @empty
                    <li><span class="dropdown-item text-muted">{{ __('لا توجد قوالب نشطة') }}</span></li>
                @endforelse
            </ul>
        </div>
        <span class="badge {{ $user->is_active ? 'bg-label-success' : 'bg-label-danger' }} fs-tiny px-3 py-2">
            <i class="icon-base bx {{ $user->is_active ? 'bx-check-circle' : 'bx-x-circle' }} me-1"></i>
            {{ $user->is_active ? __('Active Account') : __('Inactive Account') }}
        </span>
    </div>
</div>

<!-- Hero Banner Card -->
<div class="card profile-hero-card p-4 mb-4 border-0 shadow-sm">
    <div class="d-flex flex-column flex-md-row align-items-center gap-4 position-relative" style="z-index: 1;">
        <div class="profile-avatar-wrapper flex-shrink-0">
            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" id="heroAvatarPreview">
            <span class="profile-status-dot {{ $user->is_active ? 'bg-success' : 'bg-danger' }}"></span>
        </div>
        <div class="text-center text-md-start flex-grow-1">
            <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-start gap-2 mb-1">
                <h3 class="text-white mb-0 fw-bold">{{ $user->name }}</h3>
                @if($user->username)
                    <span class="badge bg-white bg-opacity-10 text-white font-12 fw-normal">@lang('@'){{ $user->username }}</span>
                @endif
            </div>
            <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-start gap-3 text-white-50 small mb-3">
                <span><i class="icon-base bx bx-envelope me-1"></i>{{ $user->email }}</span>
                @if($user->phone)
                    <span><i class="icon-base bx bx-phone me-1"></i>{{ $user->phone }}</span>
                @endif
                @if($user->created_at)
                    <span><i class="icon-base bx bx-calendar me-1"></i>{{ __('Joined') }} {{ $user->created_at->format('M Y') }}</span>
                @endif
            </div>
            <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-start gap-2">
                @foreach($user->roles as $role)
                    <span class="badge bg-primary px-3 py-2 font-12">
                        <i class="icon-base bx bx-shield-quarter me-1"></i>{{ $role->display_name ?? __($role->name) }}
                    </span>
                @endforeach
                @if($user->user_type)
                    <span class="badge bg-label-secondary text-white px-3 py-2 font-12">
                        <i class="icon-base bx bx-user-pin me-1"></i>{{ is_object($user->user_type) ? $user->user_type->name : $user->user_type }}
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: User Summary & Info -->
    <div class="col-xl-4 col-lg-5 col-md-12">
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header border-bottom pb-3">
                <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-id-card text-primary font-20"></i>
                    <span>{{ __('Account Details') }}</span>
                </h5>
            </div>
            <div class="card-body pt-4">
                <ul class="list-unstyled mb-0 d-flex flex-column gap-3">
                    <li class="d-flex align-items-center justify-content-between pb-2 border-bottom">
                        <span class="text-muted d-flex align-items-center gap-2">
                            <i class="icon-base bx bx-user font-18 text-secondary"></i>
                            {{ __('Full Name') }}
                        </span>
                        <span class="fw-semibold text-heading">{{ $user->name }}</span>
                    </li>
                    <li class="d-flex align-items-center justify-content-between pb-2 border-bottom">
                        <span class="text-muted d-flex align-items-center gap-2">
                            <i class="icon-base bx bx-at font-18 text-secondary"></i>
                            {{ __('Username') }}
                        </span>
                        <span class="fw-semibold text-heading">{{ $user->username ?? '-' }}</span>
                    </li>
                    <li class="d-flex align-items-center justify-content-between pb-2 border-bottom">
                        <span class="text-muted d-flex align-items-center gap-2">
                            <i class="icon-base bx bx-envelope font-18 text-secondary"></i>
                            {{ __('Email') }}
                        </span>
                        <span class="fw-semibold text-heading font-13">{{ $user->email }}</span>
                    </li>
                    <li class="d-flex align-items-center justify-content-between pb-2 border-bottom">
                        <span class="text-muted d-flex align-items-center gap-2">
                            <i class="icon-base bx bx-phone font-18 text-secondary"></i>
                            {{ __('Phone') }}
                        </span>
                        <span class="fw-semibold text-heading">{{ $user->phone ?? '-' }}</span>
                    </li>
                    <li class="d-flex align-items-center justify-content-between pb-2 border-bottom">
                        <span class="text-muted d-flex align-items-center gap-2">
                            <i class="icon-base bx bx-badge-check font-18 text-secondary"></i>
                            {{ __('Status') }}
                        </span>
                        <span class="badge {{ $user->is_active ? 'bg-label-success' : 'bg-label-danger' }}">
                            {{ $user->is_active ? __('Active') : __('Inactive') }}
                        </span>
                    </li>
                    <li class="d-flex align-items-center justify-content-between">
                        <span class="text-muted d-flex align-items-center gap-2">
                            <i class="icon-base bx bx-time-five font-18 text-secondary"></i>
                            {{ __('Last Login') }}
                        </span>
                        <span class="fw-semibold text-heading small">
                            {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : __('Never') }}
                        </span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Assigned Roles -->
        <div class="card shadow-sm border-0">
            <div class="card-header border-bottom pb-3">
                <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-shield-alt-2 text-primary font-20"></i>
                    <span>{{ __('Assigned Roles') }}</span>
                </h5>
            </div>
            <div class="card-body pt-4">
                <div class="d-flex flex-wrap gap-2">
                    @forelse($user->roles as $role)
                        <span class="badge bg-label-primary px-3 py-2 font-13">
                            <i class="icon-base bx bx-check-shield me-1"></i>{{ $role->display_name ?? __($role->name) }}
                        </span>
                    @empty
                        <span class="text-muted small">{{ __('No roles assigned.') }}</span>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Settings Tabs -->
    <div class="col-xl-8 col-lg-7 col-md-12">
        <div class="nav-align-top">
            <ul class="nav nav-tabs nav-fill mb-3" role="tablist">
                <li class="nav-item">
                    <button type="button" class="nav-link active d-flex align-items-center justify-content-center gap-2 py-3" role="tab" data-bs-toggle="tab" data-bs-target="#tab-profile" aria-controls="tab-profile" aria-selected="true">
                        <i class="icon-base bx bx-user font-18"></i>
                        <span class="fw-medium">{{ __('Edit Profile') }}</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link d-flex align-items-center justify-content-center gap-2 py-3" role="tab" data-bs-toggle="tab" data-bs-target="#tab-password" aria-controls="tab-password" aria-selected="false">
                        <i class="icon-base bx bx-lock-alt font-18"></i>
                        <span class="fw-medium">{{ __('Security & Password') }}</span>
                    </button>
                </li>
            </ul>

            <div class="tab-content p-0 bg-transparent shadow-none border-0">
                <!-- Tab 1: Edit Profile -->
                <div class="tab-pane fade show active" id="tab-profile" role="tabpanel">
                    <div class="card shadow-sm border-0">
                        <div class="card-header border-bottom">
                            <h5 class="card-title mb-0">{{ __('Profile Information') }}</h5>
                            <small class="text-muted">{{ __('Update your personal information and profile picture.') }}</small>
                        </div>
                        <div class="card-body pt-4">
                            @if($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <ul class="mb-0 ps-3">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <!-- Avatar Upload Box -->
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">{{ __('Profile Photo') }}</label>
                                    <div class="d-flex align-items-center gap-4">
                                        <div class="avatar-upload-box flex-grow-1" onclick="document.getElementById('avatarInput').click();">
                                            <div class="d-flex flex-column align-items-center justify-content-center py-2">
                                                <i class="icon-base bx bx-cloud-upload text-primary font-32 mb-2"></i>
                                                <span class="fw-semibold font-14">{{ __('Click to upload new photo') }}</span>
                                                <span class="text-muted font-12">{{ __('Allowed formats: JPG, PNG, WEBP (Max 2MB)') }}</span>
                                            </div>
                                            <input type="file" name="avatar" id="avatarInput" class="d-none" accept="image/*" onchange="previewProfileAvatar(this);">
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label" for="profileName">{{ __('Full Name') }} <span class="text-danger">*</span></label>
                                        <div class="input-group input-group-merge">
                                            <span class="input-group-text"><i class="icon-base bx bx-user"></i></span>
                                            <input type="text" id="profileName" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label" for="profileUsername">{{ __('Username') }}</label>
                                        <div class="input-group input-group-merge">
                                            <span class="input-group-text">@</span>
                                            <input type="text" id="profileUsername" name="username" class="form-control" value="{{ old('username', $user->username) }}" placeholder="johndoe">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label" for="profileEmail">{{ __('Email') }}</label>
                                        <div class="input-group input-group-merge">
                                            <span class="input-group-text"><i class="icon-base bx bx-envelope"></i></span>
                                            <input type="email" id="profileEmail" class="form-control bg-light" value="{{ $user->email }}" readonly disabled>
                                        </div>
                                        <small class="text-muted font-11">{{ __('Email is linked to account authentication and cannot be edited directly.') }}</small>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label" for="profilePhone">{{ __('Phone Number') }}</label>
                                        <div class="input-group input-group-merge">
                                            <span class="input-group-text"><i class="icon-base bx bx-phone"></i></span>
                                            <input type="text" id="profilePhone" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" placeholder="+966 50 000 0000">
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-2 px-4 shadow-sm">
                                        <i class="icon-base bx bx-save font-18"></i>
                                        <span>{{ __('Save Changes') }}</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Tab 2: Security & Password -->
                <div class="tab-pane fade" id="tab-password" role="tabpanel">
                    <div class="card shadow-sm border-0">
                        <div class="card-header border-bottom">
                            <h5 class="card-title mb-0">{{ __('Change Password') }}</h5>
                            <small class="text-muted">{{ __('Ensure your account is using a long, random password to stay secure.') }}</small>
                        </div>
                        <div class="card-body pt-4">
                            <form action="{{ route('profile.update-password') }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row g-3 mb-4">
                                    <div class="col-12 form-password-toggle">
                                        <label class="form-label" for="current_password">{{ __('Current Password') }} <span class="text-danger">*</span></label>
                                        <div class="input-group input-group-merge">
                                            <input type="password" id="current_password" name="current_password" class="form-control" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" required>
                                            <span class="input-group-text cursor-pointer"><i class="icon-base bx bx-hide"></i></span>
                                        </div>
                                    </div>

                                    <div class="col-md-6 form-password-toggle">
                                        <label class="form-label" for="password">{{ __('New Password') }} <span class="text-danger">*</span></label>
                                        <div class="input-group input-group-merge">
                                            <input type="password" id="password" name="password" class="form-control" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" required minlength="6">
                                            <span class="input-group-text cursor-pointer"><i class="icon-base bx bx-hide"></i></span>
                                        </div>
                                        <small class="text-muted font-11">{{ __('Minimum 6 characters.') }}</small>
                                    </div>

                                    <div class="col-md-6 form-password-toggle">
                                        <label class="form-label" for="password_confirmation">{{ __('Confirm New Password') }} <span class="text-danger">*</span></label>
                                        <div class="input-group input-group-merge">
                                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" required minlength="6">
                                            <span class="input-group-text cursor-pointer"><i class="icon-base bx bx-hide"></i></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-2 px-4 shadow-sm">
                                        <i class="icon-base bx bx-key font-18"></i>
                                        <span>{{ __('Update Password') }}</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
function previewProfileAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('heroAvatarPreview');
            if (preview) {
                preview.src = e.target.result;
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
