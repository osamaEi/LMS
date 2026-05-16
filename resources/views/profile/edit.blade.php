@extends('layouts.dashboard')

@section('title', __('Profile'))

@section('content')
<div style="direction:rtl;max-width:820px;margin:0 auto;padding:0 4px">

{{-- Flash --}}
@if(session('success'))
<div style="background:#f0fdf4;border:1px solid #bbf7d0;border-right:4px solid #22c55e;border-radius:10px;padding:12px 16px;margin-bottom:16px;color:#15803d;font-size:.875rem;font-weight:600;display:flex;align-items:center;gap:8px">
    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div style="background:#fef2f2;border:1px solid #fecaca;border-right:4px solid #ef4444;border-radius:10px;padding:12px 16px;margin-bottom:16px;color:#dc2626;font-size:.875rem;font-weight:600;display:flex;align-items:center;gap:8px">
    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    {{ session('error') }}
</div>
@endif

{{-- Header --}}
<div style="margin-bottom:20px">
    <h1 style="font-size:1.25rem;font-weight:800;color:#111827;margin:0">{{ __('Profile') }}</h1>
    <p style="font-size:.8rem;color:#6b7280;margin:4px 0 0">{{ auth()->user()->email }} · {{ __('Joined') }} {{ auth()->user()->created_at->format('Y/m/d') }}</p>
</div>

{{-- Avatar + Info --}}
<div style="background:#fff;border:1.5px solid #f1f5f9;border-radius:14px;padding:20px;margin-bottom:16px;display:flex;align-items:center;gap:20px;flex-wrap:wrap">
    {{-- Avatar --}}
    <div style="position:relative;flex-shrink:0">
        @if(auth()->user()->avatar)
            <img id="avatar-img" src="{{ asset('storage/avatars/' . auth()->user()->avatar) }}"
                 style="width:72px;height:72px;border-radius:50%;object-fit:cover;border:2px solid #e5e7eb">
        @else
            <div id="avatar-img" style="width:72px;height:72px;border-radius:50%;background:#e5e7eb;display:flex;align-items:center;justify-content:center;font-size:1.5rem;font-weight:800;color:#374151">
                {{ mb_substr(auth()->user()->name, 0, 1) }}
            </div>
        @endif
    </div>

    {{-- Name & Role --}}
    <div style="flex:1;min-width:0">
        <p style="font-size:1rem;font-weight:800;color:#111827;margin:0">{{ auth()->user()->name }}</p>
        <p style="font-size:.8rem;color:#6b7280;margin:2px 0 10px">{{ auth()->user()->getRoleDisplayName() }}</p>
        <form action="{{ route('profile.update-avatar') }}" method="POST" enctype="multipart/form-data" id="avatar-form" style="display:inline">
            @csrf
            <label style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.78rem;font-weight:700;color:#374151;cursor:pointer;background:#f9fafb">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                {{ __('Change Photo') }}
                <input type="file" name="avatar" accept="image/*" class="sr-only"
                       onchange="previewAvatar(event); this.form.submit()">
            </label>
            @error('avatar')<p style="color:#dc2626;font-size:.75rem;margin-top:4px">{{ $message }}</p>@enderror
        </form>
    </div>
</div>

{{-- Personal Info --}}
<div style="background:#fff;border:1.5px solid #f1f5f9;border-radius:14px;overflow:hidden;margin-bottom:16px">
    <div style="padding:14px 18px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:10px">
        <div style="width:8px;height:8px;border-radius:50%;background:#0071AA"></div>
        <h2 style="font-size:.9rem;font-weight:700;color:#111827;margin:0">{{ __('Personal Information') }}</h2>
    </div>
    <form action="{{ route('profile.update') }}" method="POST" style="padding:20px">
        @csrf
        @method('PUT')
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:14px;margin-bottom:16px">
            <div>
                <label style="display:block;font-size:.8rem;font-weight:600;color:#374151;margin-bottom:4px">{{ __('Name') }}</label>
                <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}"
                       style="width:100%;padding:8px 12px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.875rem;color:#111827;box-sizing:border-box"
                       class="@error('name') border-red-400 @enderror">
                @error('name')<p style="color:#dc2626;font-size:.75rem;margin-top:3px">{{ $message }}</p>@enderror
            </div>
            <div>
                <label style="display:block;font-size:.8rem;font-weight:600;color:#374151;margin-bottom:4px">{{ __('Email') }}</label>
                <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}"
                       style="width:100%;padding:8px 12px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.875rem;color:#111827;box-sizing:border-box"
                       class="@error('email') border-red-400 @enderror">
                @error('email')<p style="color:#dc2626;font-size:.75rem;margin-top:3px">{{ $message }}</p>@enderror
            </div>
            <div>
                <label style="display:block;font-size:.8rem;font-weight:600;color:#374151;margin-bottom:4px">{{ __('Phone') }}</label>
                <input type="text" name="phone" value="{{ old('phone', auth()->user()->phone) }}"
                       style="width:100%;padding:8px 12px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.875rem;color:#111827;box-sizing:border-box">
            </div>
            <div>
                <label style="display:block;font-size:.8rem;font-weight:600;color:#374151;margin-bottom:4px">{{ __('National ID') }}</label>
                <input type="text" name="national_id" value="{{ old('national_id', auth()->user()->national_id) }}"
                       style="width:100%;padding:8px 12px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.875rem;color:#111827;box-sizing:border-box">
            </div>
        </div>
        <div style="text-align:left">
            <button type="submit"
                    style="padding:9px 22px;border-radius:8px;border:none;font-size:.875rem;font-weight:700;color:#fff;background:linear-gradient(135deg,#0071AA,#005a88);cursor:pointer">
                {{ __('Save Changes') }}
            </button>
        </div>
    </form>
</div>

{{-- Password --}}
<div style="background:#fff;border:1.5px solid #f1f5f9;border-radius:14px;overflow:hidden">
    <div style="padding:14px 18px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:10px">
        <div style="width:8px;height:8px;border-radius:50%;background:#dc2626"></div>
        <h2 style="font-size:.9rem;font-weight:700;color:#111827;margin:0">{{ __('Change Password') }}</h2>
    </div>
    <form action="{{ route('profile.update-password') }}" method="POST" style="padding:20px">
        @csrf
        @method('PUT')
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:14px;margin-bottom:16px">
            <div style="grid-column:1/-1">
                <label style="display:block;font-size:.8rem;font-weight:600;color:#374151;margin-bottom:4px">{{ __('Current Password') }}</label>
                <input type="password" name="current_password"
                       style="width:100%;padding:8px 12px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.875rem;color:#111827;box-sizing:border-box;max-width:340px"
                       class="@error('current_password') border-red-400 @enderror">
                @error('current_password')<p style="color:#dc2626;font-size:.75rem;margin-top:3px">{{ $message }}</p>@enderror
            </div>
            <div>
                <label style="display:block;font-size:.8rem;font-weight:600;color:#374151;margin-bottom:4px">{{ __('New Password') }}</label>
                <input type="password" name="password"
                       style="width:100%;padding:8px 12px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.875rem;color:#111827;box-sizing:border-box"
                       class="@error('password') border-red-400 @enderror">
                @error('password')<p style="color:#dc2626;font-size:.75rem;margin-top:3px">{{ $message }}</p>@enderror
            </div>
            <div>
                <label style="display:block;font-size:.8rem;font-weight:600;color:#374151;margin-bottom:4px">{{ __('Confirm Password') }}</label>
                <input type="password" name="password_confirmation"
                       style="width:100%;padding:8px 12px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.875rem;color:#111827;box-sizing:border-box">
            </div>
        </div>
        <p style="font-size:.75rem;color:#6b7280;margin:0 0 14px">{{ __('At least 8 characters, preferably letters and numbers.') }}</p>
        <div style="text-align:left">
            <button type="submit"
                    style="padding:9px 22px;border-radius:8px;border:none;font-size:.875rem;font-weight:700;color:#fff;background:linear-gradient(135deg,#dc2626,#b91c1c);cursor:pointer">
                {{ __('Update Password') }}
            </button>
        </div>
    </form>
</div>

</div>

<script>
function previewAvatar(event) {
    const file = event.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => { document.getElementById('avatar-img').src = e.target.result; };
    reader.readAsDataURL(file);
}
</script>
@endsection
