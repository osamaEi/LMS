<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
@php($ar = app()->getLocale() === 'ar')
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $ar ? 'استعادة كلمة المرور' : 'Reset password' }} — ALERTIQA</title>
    <style>
        @font-face{font-family:Cairo;src:url('/font/static/Cairo-Regular.woff2') format('woff2');font-display:swap}
        *{box-sizing:border-box}body{margin:0;background:#f3f7fb;color:#17324d;font-family:Cairo,sans-serif;line-height:1.7}
        main{min-height:100vh;display:grid;place-items:center;padding:32px 18px}.card{width:100%;max-width:490px;background:#fff;border:1px solid #e0e8f0;border-radius:24px;padding:36px;box-shadow:0 16px 60px #17324d0a}
        .logo{display:block;height:64px;max-width:100%;margin:0 auto 24px}.steps{display:flex;gap:8px;margin-bottom:24px}.steps span{height:4px;flex:1;border-radius:4px;background:#e5edf4}.steps .active{background:#0071aa}
        h1{font-size:24px;margin:0 0 8px}p{font-size:14px;color:#64748b;margin:0 0 22px}label{display:block;font-size:13px;font-weight:bold;margin:16px 0 7px}input{font:inherit;font-size:15px;width:100%;border:1px solid #cbd5e1;background:#f9fbfd;border-radius:10px;padding:12px 14px;color:#17324d}input:focus{outline:3px solid #0071aa22;border-color:#0071aa}button{font:inherit;cursor:pointer}button.primary{width:100%;margin-top:24px;border:0;border-radius:10px;padding:13px;background:#0071aa;color:#fff;font-weight:bold}button:disabled{opacity:.55;cursor:wait}.link{color:#0071aa;text-decoration:none;font-size:13px}a:focus-visible,button:focus-visible{outline:3px solid #38bdf8;outline-offset:3px}.footer{display:flex;justify-content:space-between;gap:12px;margin-top:24px}.notice{padding:12px 14px;border-radius:10px;font-size:13px;margin-bottom:16px;background:#ecfdf5;color:#166534}.errors{background:#fff1f2;color:#9f1239}.errors ul{margin:0;padding-inline-start:18px}.code{letter-spacing:8px;text-align:center;font-size:24px}.resend{margin-top:20px;padding-top:18px;border-top:1px solid #e5edf4}.resend button{border:0;background:transparent;padding:8px 0;color:#0071aa}.hint{font-size:12px;margin-top:8px}.toggle{border:0;background:transparent;color:#0071aa;padding:8px 0;font-size:12px}
        @media(max-width:480px){.card{padding:24px 20px;border-radius:18px}main{padding:18px 12px}h1{font-size:21px}}
    </style>
</head>
<body>
<main>
    <section class="card" aria-labelledby="page-title">
        <img class="logo" src="{{ asset('images/logo.svg') }}" alt="ALERTIQA">
        <div class="steps" aria-hidden="true"><span class="active"></span><span class="{{ $sent ? 'active' : '' }}"></span></div>
        <h1 id="page-title">{{ $ar ? ($sent ? 'تعيين كلمة مرور جديدة' : 'نسيت كلمة المرور؟') : ($sent ? 'Set a new password' : 'Forgot password?') }}</h1>
        <p>{{ $ar ? ($sent ? 'أدخل رمز التحقق المرسل إلى جوالك، ثم اختر كلمة مرور جديدة.' : 'أدخل بريدك الإلكتروني أو رقم الجوال المسجّل لإرسال رمز التحقق إلى جوالك.') : ($sent ? 'Enter the code sent to your phone and choose a new password.' : 'Enter your registered email or mobile number to receive a code by SMS.') }}</p>
        @if(session('status'))<div class="notice" role="status">{{ session('status') }}</div>@endif
        @if($errors->any())
        <div class="notice errors" role="alert"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif
        @if($sent)
        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <label for="otp">{{ $ar ? 'رمز التحقق' : 'Verification code' }}</label>
            <input class="code" id="otp" name="otp" dir="ltr" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" minlength="6" maxlength="6" required autofocus aria-describedby="code-hint">
            <p class="hint" id="code-hint">{{ $ar ? 'الرمز مكوّن من 6 أرقام وصالح لمدة 5 دقائق.' : 'The 6-digit code expires after 5 minutes.' }}</p>
            <label for="password">{{ $ar ? 'كلمة المرور الجديدة' : 'New password' }}</label>
            <input id="password" name="password" type="password" autocomplete="new-password" minlength="8" required aria-describedby="password-hint">
            <p class="hint" id="password-hint">{{ $ar ? '8 أحرف على الأقل.' : 'At least 8 characters.' }}</p>
            <label for="password_confirmation">{{ $ar ? 'تأكيد كلمة المرور' : 'Confirm password' }}</label>
            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" minlength="8" required>
            <button class="toggle" type="button" id="toggle-password" aria-pressed="false">{{ $ar ? 'إظهار كلمة المرور' : 'Show password' }}</button>
            <button class="primary" type="submit">{{ $ar ? 'حفظ كلمة المرور' : 'Save password' }}</button>
        </form>
        @endif
        <form method="POST" action="{{ route('password.otp.send') }}" class="{{ $sent ? 'resend' : '' }}">
            @csrf
            <label for="identifier">{{ $ar ? 'البريد الإلكتروني أو رقم الجوال' : 'Email or mobile number' }}</label>
            <input id="identifier" name="identifier" type="text" dir="ltr" autocomplete="username" maxlength="255" required value="{{ old('identifier', session('password_recovery.identifier')) }}" placeholder="name@example.com / 05xxxxxxxx">
            <button class="{{ $sent ? '' : 'primary' }}" type="submit">{{ $ar ? ($sent ? 'إرسال رمز جديد' : 'إرسال رمز التحقق') : ($sent ? 'Send a new code' : 'Send verification code') }}</button>
        </form>
        <div class="footer">
            <a class="link" href="{{ route('login') }}">{{ $ar ? 'العودة لتسجيل الدخول' : 'Back to login' }}</a>
            <a class="link" href="{{ route('lang.switch', $ar ? 'en' : 'ar') }}">{{ $ar ? 'English' : 'العربية' }}</a>
        </div>
    </section>
</main>
<script>
    document.querySelectorAll('form').forEach(form => form.addEventListener('submit', () => {
        form.querySelector('button[type="submit"]').disabled = true;
    }));
    document.getElementById('toggle-password')?.addEventListener('click', function () {
        const show = document.getElementById('password').type === 'password';
        ['password', 'password_confirmation'].forEach(id => document.getElementById(id).type = show ? 'text' : 'password');
        this.setAttribute('aria-pressed', String(show));
        this.textContent = show ? @json($ar ? 'إخفاء كلمة المرور' : 'Hide password') : @json($ar ? 'إظهار كلمة المرور' : 'Show password');
    });
</script>
</body>
</html>
