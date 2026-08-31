<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>تسجيل حساب جديد - ALERTIQA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preload" href="/font/static/Cairo-Regular.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="/font/static/Cairo-Bold.woff2" as="font" type="font/woff2" crossorigin>
    <style>
        @font-face {
            font-family: 'Cairo';
            src: url('/font/static/Cairo-Regular.woff2') format('woff2'),
                 url('/font/static/Cairo-Regular.ttf') format('truetype');
            font-weight: 400; font-style: normal; font-display: swap;
        }
        @font-face {
            font-family: 'Cairo';
            src: url('/font/static/Cairo-Medium.woff2') format('woff2'),
                 url('/font/static/Cairo-Medium.ttf') format('truetype');
            font-weight: 500; font-style: normal; font-display: swap;
        }
        @font-face {
            font-family: 'Cairo';
            src: url('/font/static/Cairo-SemiBold.woff2') format('woff2'),
                 url('/font/static/Cairo-SemiBold.ttf') format('truetype');
            font-weight: 600; font-style: normal; font-display: swap;
        }
        @font-face {
            font-family: 'Cairo';
            src: url('/font/static/Cairo-Bold.woff2') format('woff2'),
                 url('/font/static/Cairo-Bold.ttf') format('truetype');
            font-weight: 700; font-style: normal; font-display: swap;
        }
        * { font-family: 'Cairo', sans-serif; box-sizing: border-box; }
        h1, h2, h3, .font-bold { font-family: 'Cairo', sans-serif; font-weight: 700; }

        :root {
            --navy:   #0A5A86;
            --navy2:  #0D6FA6;
            --blue:   #0D6FA6;
            --blue2:  #0A5A86;
            --light:  #eff6ff;
        }

        body { background: #f9fafb; margin: 0; }

        /* ── Animations ── */
        @keyframes pulse-ring { 0%{transform:scale(.8);opacity:1} 100%{transform:scale(1.4);opacity:0} }
        @keyframes spin-slow   { from{transform:rotate(0deg)} to{transform:rotate(360deg)} }
        @keyframes fadeSlideIn { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }
        .pulse-ring  { animation: pulse-ring 1.5s ease-out infinite; }
        .spin-slow   { animation: spin-slow 3s linear infinite; }
        .fade-in     { animation: fadeSlideIn .35s ease both; }

        /* ── Steps ── */
        .step { display: none; }
        .step.active { display: block; }

        /* ── Input base ── */
        .field-input {
            width: 100%; padding: 12px 16px;
            border: 1px solid #e5e7eb; border-radius: 8px;
            background: #f9fafb; font-size: 14px; color: #111827;
            outline: none; transition: border-color .2s, box-shadow .2s, background .2s;
            font-family: 'Cairo', sans-serif;
        }
        .field-input:focus {
            background: #fff;
            border-color: transparent;
            box-shadow: 0 0 0 2px #3b82f6;
        }
        .field-label {
            display: block; font-size: 14px; font-weight: 500;
            color: #374151; margin-bottom: 8px;
        }
        .field-error { font-size: 11px; color: #dc2626; margin-top: 4px; display: none; }

        /* ── Section card ── */
        .section-card {
            border-radius: 14px; padding: 16px;
            margin-bottom: 14px;
            background: #f0f4ff; border: 1px solid #c7d2fe;
        }
        .section-card.green  { background: #f0fdf4; border: 1px solid #bbf7d0; }
        .section-card.blue   { background: #f0f4ff; border: 1px solid #c7d2fe; }
        .section-card.purple { background: #f0f4ff; border: 1px solid #c7d2fe; }
        .section-card.navy   { background: #f0f4ff; border: 1px solid #c7d2fe; }

        .section-title {
            font-size: 11px; font-weight: 700; letter-spacing: .06em;
            text-transform: uppercase; margin-bottom: 12px;
        }

        /* ── Step dots ── */
        .step-dot {
            width: 36px; height: 36px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 13px; transition: all .3s;
        }
        .step-line { height: 3px; width: 48px; border-radius: 3px; transition: background .4s; }

        /* ── Upload zone ── */
        .upload-zone {
            border: 2px dashed #cbd5e1; border-radius: 8px;
            background: #fff; min-height: 88px;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            cursor: pointer; padding: 10px; transition: border-color .2s;
        }
        .upload-zone:hover { border-color: #0D6FA6; }

        /* ── Primary button ── */
        .btn-primary {
            width: 100%; padding: 14px 16px;
            background-color: #0D6FA6;
            color: white; font-weight: 700; font-size: 15px;
            border: none; border-radius: 8px; cursor: pointer;
            transition: background-color .2s;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,.1);
        }
        .btn-primary:hover { background-color: #0A5A86; }
        .btn-primary:disabled { opacity: .6; cursor: not-allowed; }
    </style>
</head>
<body>

<div style="min-height:100vh; display:flex;">

    {{-- ══ Left Panel: Branded (matches login) ══ --}}
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden"
         style="background: linear-gradient(135deg, #0A5A86 0%, #0D6FA6 55%, #1283c0 100%);">

        {{-- Decorative shapes --}}
        <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full" style="background: rgba(255,255,255,0.06);"></div>
        <div class="absolute -bottom-32 -left-20 w-96 h-96 rounded-full" style="background: rgba(255,255,255,0.05);"></div>
        <div class="absolute top-1/3 left-10 w-32 h-32 rounded-full" style="background: rgba(255,255,255,0.04);"></div>

        {{-- Content: Logo + tagline --}}
        <div class="relative z-10 flex flex-col justify-center items-center text-white p-12 w-full text-center">
            <div class="bg-white rounded-3xl px-8 py-6 shadow-2xl mb-8">
                <img src="{{ asset('images/logo.svg') }}" alt="ALERTIQA" style="height: 96px; width: auto;">
            </div>
            <h1 class="text-3xl font-bold mb-3">منصة الإرتقاء التعليمية</h1>
            <p class="text-base text-white/80 max-w-sm leading-relaxed">
                تابع دوراتك وجلساتك التدريبية وجدولك الدراسي في مكان واحد
            </p>
        </div>
    </div>

    {{-- ══ Right Panel (Form) ══ --}}
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 overflow-y-auto" style="background:#ffffff;">
        <div style="width:100%; max-width:520px; padding: 8px 0;">

            {{-- Logo at top --}}
            <div class="text-center mb-8">
                <img src="{{ asset('images/logo.svg') }}" alt="ALERTIQA"
                     class="mx-auto" style="height:72px;width:auto;">
            </div>

            {{-- ── Step Indicator ── --}}
            <div style="display:flex;align-items:center;justify-content:center;gap:0;margin-bottom:28px;">
                <div style="display:flex;align-items:center;gap:0;">
                    <div style="display:flex;flex-direction:column;align-items:center;gap:4px;">
                        <div id="step-dot-1" class="step-dot" style="background:linear-gradient(135deg,#0A5A86,#1283c0);color:white;box-shadow:0 4px 12px rgba(13,111,166,.3);">1</div>
                        <span style="font-size:10px;font-weight:600;color:#0A5A86;">التحقق</span>
                    </div>
                    <div id="step-line-1" class="step-line" style="background:#e2e8f0;margin:0 4px;margin-bottom:18px;"></div>
                </div>
                <div style="display:flex;align-items:center;gap:0;">
                    <div style="display:flex;flex-direction:column;align-items:center;gap:4px;">
                        <div id="step-dot-2" class="step-dot" style="background:#e2e8f0;color:#94a3b8;">2</div>
                        <span style="font-size:10px;font-weight:600;color:#94a3b8;">رمز الجوال</span>
                    </div>
                    <div id="step-line-2" class="step-line" style="background:#e2e8f0;margin:0 4px;margin-bottom:18px;"></div>
                </div>
                <div style="display:flex;flex-direction:column;align-items:center;gap:4px;">
                    <div id="step-dot-3" class="step-dot" style="background:#e2e8f0;color:#94a3b8;">3</div>
                    <span style="font-size:10px;font-weight:600;color:#94a3b8;">البيانات</span>
                </div>
            </div>

            {{-- ══════════ STEP 1 ══════════ --}}
            <div id="step1" class="step active fade-in">
                <div style="background:white; border-radius:20px; padding:28px; box-shadow:0 1px 3px rgba(0,0,0,.05); border:1px solid #e5e7eb;">
                    <div style="text-align:center; margin-bottom:24px;">
                        <div style="width:52px;height:52px;border-radius:14px;background:linear-gradient(135deg,#0A5A86,#1283c0);display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                            <svg style="width:24px;height:24px;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                        <h2 style="font-size:20px;font-weight:700;color:#0A5A86;margin:0 0 6px;">إنشاء حساب جديد</h2>
                        <p style="font-size:13px;color:#64748b;margin:0;">أدخل رقم الجوال ورقم الهوية للمتابعة</p>
                    </div>

                    <form id="step1-form" style="display:flex;flex-direction:column;gap:16px;">
                        <div>
                            <label class="field-label">رقم الجوال <span style="color:#ef4444;">*</span></label>
                            <input type="text" id="phone" name="phone" placeholder="05XXXXXXXX" maxlength="10" required class="field-input" dir="ltr">
                            <p id="phone-error" class="field-error"></p>
                        </div>
                        <div>
                            <label class="field-label">رقم الهوية الوطنية <span style="color:#ef4444;">*</span></label>
                            <input type="text" id="national_id" name="national_id" placeholder="1XXXXXXXXX" maxlength="10" required class="field-input" dir="ltr">
                            <p id="national_id-error" class="field-error"></p>
                        </div>
                        <div id="step1-error" style="display:none;background:#fff7ed;border:1px solid #fde68a;border-right:3px solid #f59e0b;border-radius:10px;padding:12px 14px;font-size:13px;color:#92400e;"></div>
                        <button type="submit" id="step1-btn" class="btn-primary">
                            <span id="step1-btn-text">متابعة</span>
                            <svg id="step1-spinner" class="hidden spin-slow" style="width:18px;height:18px;display:inline;vertical-align:middle;margin-right:6px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                        </button>
                    </form>
                    <p style="text-align:center;margin-top:18px;font-size:13px;color:#64748b;">
                        لديك حساب؟
                        <a href="{{ route('login') }}" style="color:#0A5A86;font-weight:700;text-decoration:none;">تسجيل الدخول</a>
                    </p>
                </div>
            </div>

            {{-- ══════════ STEP 3 ══════════ --}}
            <div id="step2" class="step">
                <div style="background:white;border-radius:20px;padding:28px;box-shadow:0 1px 3px rgba(0,0,0,.05);border:1px solid #e5e7eb;text-align:center;">
                    <div style="width:52px;height:52px;border-radius:14px;background:linear-gradient(135deg,#0A5A86,#1283c0);display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                        <svg style="width:24px;height:24px;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                    <h2 style="font-size:20px;font-weight:700;color:#0A5A86;margin:0 0 6px;">تحقق من رقم الجوال</h2>
                    <p style="font-size:13px;color:#64748b;margin:0 0 20px;">أرسلنا رمزاً من 6 أرقام إلى <strong id="otp-phone" dir="ltr"></strong></p>
                    <form id="step2-form" style="display:flex;flex-direction:column;gap:14px;">
                        <input id="otp" name="otp" type="text" inputmode="numeric" autocomplete="one-time-code" maxlength="6" placeholder="------" required class="field-input" dir="ltr" style="text-align:center;font-size:24px;letter-spacing:10px;font-weight:700;">
                        <p id="otp-error" class="field-error"></p>
                        <div id="step2-error" style="display:none;background:#fff7ed;border:1px solid #fde68a;border-right:3px solid #f59e0b;border-radius:10px;padding:12px 14px;font-size:13px;color:#92400e;"></div>
                        <button type="submit" id="step2-btn" class="btn-primary"><span id="step2-btn-text">تحقق ومتابعة</span><svg id="step2-spinner" class="hidden spin-slow" style="width:18px;height:18px;display:none;vertical-align:middle;margin-right:6px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg></button>
                        <button type="button" id="resend-otp" style="border:0;background:transparent;color:#0D6FA6;font-size:13px;font-weight:600;cursor:pointer;">إعادة إرسال الرمز</button>
                        <button type="button" onclick="goToStep(1)" style="border:0;background:transparent;color:#64748b;font-size:12px;cursor:pointer;">تغيير رقم الجوال</button>
                    </form>
                </div>
            </div>

            <div id="step3" class="step">
                {{-- Header bar --}}
                <div style="background:linear-gradient(135deg,#0A5A86,#1283c0);border-radius:20px 20px 0 0;padding:20px 24px;display:flex;align-items:center;gap:14px;">
                    <div style="width:40px;height:40px;border-radius:50%;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg style="width:20px;height:20px;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 style="color:white;font-size:16px;font-weight:700;margin:0 0 2px;">تم التحقق بنجاح!</h2>
                        <p style="color:rgba(255,255,255,.7);font-size:12px;margin:0;">أكمل بياناتك لإنشاء حسابك</p>
                    </div>
                    <div style="margin-right:auto;">
                        {{-- Verified badge --}}
                        <div style="background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.3);border-radius:20px;padding:4px 12px;display:flex;align-items:center;gap:6px;">
                            <svg style="width:13px;height:13px;color:#86efac;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span style="font-size:11px;color:white;font-weight:600;">SMS</span>
                        </div>
                    </div>
                </div>

                {{-- Verified info strip --}}
                <div style="background:#f0fdf4;border-right:4px solid #22c55e;padding:10px 16px;display:flex;align-items:center;gap:12px;">
                    <svg style="width:16px;height:16px;color:#16a34a;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    <span id="display-phone" style="font-size:12px;font-weight:700;color:#166534;" dir="ltr">—</span>
                    <span style="color:#bbf7d0;">|</span>
                    <svg style="width:16px;height:16px;color:#16a34a;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/>
                    </svg>
                    <span id="display-national-id" style="font-size:12px;font-weight:700;color:#166534;" dir="ltr">—</span>
                </div>

                <div style="background:white;border-radius:0 0 20px 20px;box-shadow:0 1px 3px rgba(0,0,0,.05);border:1px solid #e5e7eb;border-top:none;">
                    <form id="step3-form" style="max-height:60vh;overflow-y:auto;padding:20px;">
                        <input type="hidden" id="step3-phone" name="phone">
                        <input type="hidden" id="step3-national-id" name="national_id">

                        {{-- ── Section: Personal Info ── --}}
                        <div style="margin-bottom:20px;">
                            <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;padding-bottom:10px;border-bottom:2px solid #e2e8f0;">
                                <div style="width:30px;height:30px;border-radius:8px;background:linear-gradient(135deg,#0A5A86,#1283c0);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <svg style="width:15px;height:15px;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <span style="font-size:13px;font-weight:700;color:#0A5A86;">البيانات الشخصية</span>
                            </div>
                            <div style="display:flex;flex-direction:column;gap:12px;">
                                <div>
                                    <label class="field-label">الاسم الكامل <span style="color:#ef4444;">*</span></label>
                                    <input type="text" id="name" name="name" placeholder="الاسم الرباعي" required class="field-input">
                                    <p id="name-error" class="field-error"></p>
                                </div>
                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                                    <div>
                                        <label class="field-label">تاريخ الميلاد <span style="color:#ef4444;">*</span></label>
                                        <input type="date" id="date_of_birth" name="date_of_birth" required class="field-input">
                                        <p id="dob-error" class="field-error"></p>
                                    </div>
                                    <div>
                                        <label class="field-label">الجنس <span style="color:#ef4444;">*</span></label>
                                        <select id="gender" name="gender" required class="field-input">
                                            <option value="">اختر</option>
                                            <option value="male">ذكر</option>
                                            <option value="female">أنثى</option>
                                        </select>
                                        <p id="gender-error" class="field-error"></p>
                                    </div>
                                </div>
                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                                    <div>
                                        <label class="field-label">الجنسية <span style="color:#ef4444;">*</span></label>
                                        <select id="nationality" name="nationality" required class="field-input">
                                            <option value="">اختر الجنسية</option>
                                            <option>سعودي</option><option>إماراتي</option><option>كويتي</option>
                                            <option>بحريني</option><option>قطري</option><option>عُماني</option>
                                            <option>يمني</option><option>مصري</option><option>أردني</option>
                                            <option>سوري</option><option>لبناني</option><option>عراقي</option>
                                            <option>فلسطيني</option><option>سوداني</option><option>مغربي</option>
                                            <option>جزائري</option><option>تونسي</option><option>ليبي</option>
                                            <option>باكستاني</option><option>هندي</option><option>بنغلاديشي</option>
                                            <option>فلبيني</option><option>إندونيسي</option><option>أخرى</option>
                                        </select>
                                        <p id="nationality-error" class="field-error"></p>
                                    </div>
                                    <div>
                                        <label class="field-label">البريد الإلكتروني <span style="color:#ef4444;">*</span></label>
                                        <input type="email" id="email" name="email" placeholder="example@email.com" required class="field-input" dir="ltr">
                                        <p id="email-error" class="field-error"></p>
                                    </div>
                                </div>
                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                                    <div>
                                        <label class="field-label">كلمة المرور <span style="color:#ef4444;">*</span></label>
                                        <input type="password" id="password" name="password" placeholder="8 أحرف على الأقل" minlength="8" required class="field-input">
                                        <p id="password-error" class="field-error"></p>
                                    </div>
                                    <div>
                                        <label class="field-label">تأكيد كلمة المرور <span style="color:#ef4444;">*</span></label>
                                        <input type="password" id="password_confirmation" name="password_confirmation" minlength="8" required class="field-input">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ── Section: Academic Info ── --}}
                        <div style="margin-bottom:20px;">
                            <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;padding-bottom:10px;border-bottom:2px solid #e2e8f0;">
                                <div style="width:30px;height:30px;border-radius:8px;background:linear-gradient(135deg,#0A5A86,#1283c0);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <svg style="width:15px;height:15px;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                                    </svg>
                                </div>
                                <span style="font-size:13px;font-weight:700;color:#0A5A86;">البيانات الأكاديمية </span>
                            </div>
                            <div style="display:flex;flex-direction:column;gap:12px;">
                                <div>
                                    <label class="field-label">المؤهل التعليمي <span style="color:#ef4444;">*</span></label>
                                    <select id="specialization_type" name="specialization_type" required class="field-input">
                                        <option value="">اختر</option>
                                        <option value="primary">ابتدائي</option>
                                        <option value="middle">متوسط</option>
                                        <option value="secondary">ثانوي</option>
                                        <option value="diploma">دبلوم</option>
                                        <option value="bachelor">بكالوريوس</option>
                                        <option value="master">ماجستير</option>
                                        <option value="phd">دكتوراه</option>
                                        <option value="training">تدريب مهني</option>
                                    </select>
                                    <p id="spec-type-error" class="field-error"></p>
                                </div>
                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                                    <div>
                                        <label class="field-label">نوع المؤهل<span style="color:#ef4444;">*</span></label>
                                        <input type="text" id="specialization" name="specialization" placeholder="مثال: علوم الحاسب، إدارة الأعمال" required class="field-input">
                                        <p id="spec-error" class="field-error"></p>
                                    </div>
                                    <div>
                                        <label class="field-label">تاريخ التخرج <span style="color:#ef4444;">*</span></label>
                                        <input type="date" id="date_of_graduation" name="date_of_graduation" required class="field-input">
                                        <p id="grad-error" class="field-error"></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ── Section: Documents ── --}}
                        <div style="margin-bottom:20px;">
                            <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;padding-bottom:10px;border-bottom:2px solid #e2e8f0;">
                                <div style="width:30px;height:30px;border-radius:8px;background:linear-gradient(135deg,#0A5A86,#1283c0);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <svg style="width:15px;height:15px;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <span style="font-size:13px;font-weight:700;color:#0A5A86;">المستندات المطلوبة</span>
                            </div>
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px;">
                                <div>
                                    <label class="field-label">الهوية — الوجه الأمامي <span style="color:#ef4444;">*</span></label>
                                    <label id="front-label" for="national_id_front" class="upload-zone"
                                           ondragover="event.preventDefault();this.style.borderColor='#0D6FA6'"
                                           ondragleave="this.style.borderColor='#94a3b8'"
                                           ondrop="handleDrop(event,'national_id_front','front-preview','front-label')">
                                        <div id="front-preview" style="text-align:center;">
                                            <svg style="width:26px;height:26px;color:#60a5fa;margin:0 auto 4px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <p style="font-size:11px;color:#0D6FA6;font-weight:600;margin:0;">اضغط أو اسحب</p>
                                            <p style="font-size:10px;color:#94a3b8;margin:2px 0 0;">JPG, PNG, PDF</p>
                                        </div>
                                        <input type="file" id="national_id_front" name="national_id_front" accept=".jpg,.jpeg,.png,.pdf" required class="hidden" onchange="previewFile(this,'front-preview','front-label')">
                                    </label>
                                    <p id="front-error" class="field-error"></p>
                                </div>
                                <div>
                                    <label class="field-label">الهوية — الوجه الخلفي <span style="color:#ef4444;">*</span></label>
                                    <label id="back-label" for="national_id_back" class="upload-zone"
                                           ondragover="event.preventDefault();this.style.borderColor='#0D6FA6'"
                                           ondragleave="this.style.borderColor='#94a3b8'"
                                           ondrop="handleDrop(event,'national_id_back','back-preview','back-label')">
                                        <div id="back-preview" style="text-align:center;">
                                            <svg style="width:26px;height:26px;color:#60a5fa;margin:0 auto 4px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <p style="font-size:11px;color:#0D6FA6;font-weight:600;margin:0;">اضغط أو اسحب</p>
                                            <p style="font-size:10px;color:#94a3b8;margin:2px 0 0;">JPG, PNG, PDF</p>
                                        </div>
                                        <input type="file" id="national_id_back" name="national_id_back" accept=".jpg,.jpeg,.png,.pdf" required class="hidden" onchange="previewFile(this,'back-preview','back-label')">
                                    </label>
                                    <p id="back-error" class="field-error"></p>
                                </div>
                            </div>
                            <div>
                                <label class="field-label">الشهادة <span style="color:#ef4444;">*</span></label>
                                <label id="cert-label" for="certificate" class="upload-zone" style="min-height:72px;"
                                       ondragover="event.preventDefault();this.style.borderColor='#0D6FA6'"
                                       ondragleave="this.style.borderColor='#94a3b8'"
                                       ondrop="handleDrop(event,'certificate','cert-preview','cert-label')">
                                    <div id="cert-preview" style="text-align:center;">
                                        <svg style="width:24px;height:24px;color:#60a5fa;margin:0 auto 4px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                        <p style="font-size:11px;color:#0D6FA6;font-weight:600;margin:0;">ارفع الشهادة (PDF)</p>
                                    </div>
                                    <input type="file" id="certificate" name="certificate" accept=".pdf" required class="hidden" onchange="previewFile(this,'cert-preview','cert-label')">
                                </label>
                                <p id="cert-error" class="field-error"></p>
                            </div>
                        </div>

                        {{-- ── Agreements ── --}}
                        <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:14px;">
                            <label id="confirm-box" style="display:flex;align-items:flex-start;gap:10px;cursor:pointer;padding:12px;border-radius:10px;border:1.5px solid #e2e8f0;background:#f8fafc;transition:border-color .2s;">
                                <input type="checkbox" id="is_confirm_user" name="is_confirm_user" value="1"
                                       style="width:16px;height:16px;flex-shrink:0;margin-top:2px;accent-color:#0A5A86;cursor:pointer;"
                                       onchange="toggleCheckbox(this,'confirm-box')">
                                <span style="font-size:12px;color:#374151;line-height:1.6;">أقر بأن جميع البيانات المدخلة صحيحة ومطابقة للهوية الرسمية.</span>
                            </label>
                            <p id="confirm-error" class="field-error" style="padding-right:4px;"></p>

                            <label id="terms-box" style="display:flex;align-items:flex-start;gap:10px;cursor:pointer;padding:12px;border-radius:10px;border:1.5px solid #e2e8f0;background:#f8fafc;transition:border-color .2s;">
                                <input type="checkbox" id="is_terms" name="is_terms" value="1"
                                       style="width:16px;height:16px;flex-shrink:0;margin-top:2px;accent-color:#0A5A86;cursor:pointer;"
                                       onchange="toggleCheckbox(this,'terms-box')">
                                <span style="font-size:12px;color:#374151;line-height:1.6;">أوافق على الشروط والأحكام وسياسات الخصوصية.</span>
                            </label>
                            <p id="terms-error" class="field-error" style="padding-right:4px;"></p>
                        </div>

                        <div id="step3-error" style="display:none;background:#fff7ed;border:1px solid #fde68a;border-right:3px solid #f59e0b;border-radius:10px;padding:12px 14px;font-size:13px;color:#92400e;margin-bottom:12px;"></div>

                        <button type="submit" id="step3-btn" class="btn-primary">
                            <span id="step3-btn-text">إنشاء الحساب</span>
                            <svg id="step3-spinner" class="hidden spin-slow" style="width:18px;height:18px;display:inline;vertical-align:middle;margin-right:6px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>

            {{-- ══════════ SUCCESS ══════════ --}}
            <div id="step-success" class="step fade-in">
                <div style="background:white;border-radius:20px;padding:40px 28px;box-shadow:0 1px 3px rgba(0,0,0,.05);border:1px solid #e5e7eb;text-align:center;">
                    <div style="width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,#16a34a,#22c55e);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;box-shadow:0 8px 24px rgba(34,197,94,.3);">
                        <svg style="width:34px;height:34px;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <h2 style="font-size:22px;font-weight:700;color:#0A5A86;margin:0 0 8px;">تم إنشاء حسابك بنجاح!</h2>
                    <p style="font-size:13px;color:#64748b;margin:0 0 24px;">سيتم مراجعة بياناتك من قِبل الإدارة. يمكنك تسجيل الدخول الآن.</p>
                    <a href="{{ route('login') }}"
                       style="display:inline-flex;align-items:center;gap:8px;padding:12px 28px;background:linear-gradient(135deg,#0A5A86,#1283c0);color:white;font-weight:700;font-size:14px;border-radius:12px;text-decoration:none;box-shadow:0 4px 14px rgba(13,111,166,.3);">
                        <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14"/>
                        </svg>
                        تسجيل الدخول
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

// ── Step navigation ──
function goToStep(step) {
    document.querySelectorAll('.step').forEach(el => el.classList.remove('active'));
    const target = step === 'success' ? 'step-success' : 'step' + step;
    const el = document.getElementById(target);
    el.classList.add('active');
    el.classList.remove('fade-in');
    void el.offsetWidth;
    el.classList.add('fade-in');

    if (step === 3 || step === 'success') {
        const p = document.getElementById('phone').value.trim();
        const n = document.getElementById('national_id').value.trim();
        const dp = document.getElementById('display-phone');
        const dn = document.getElementById('display-national-id');
        if (dp) dp.textContent = p || '—';
        if (dn) dn.textContent = n || '—';
    }

    // Only 2 dots now: step-dot-1 (التحقق) and step-dot-3 (البيانات)
    const dotMap = { 1: 'step-dot-1', 2: 'step-dot-2', 3: 'step-dot-3' };
    const numericStep = step === 'success' ? 3 : step;
    for (const [num, dotId] of Object.entries(dotMap)) {
        const dot = document.getElementById(dotId);
        if (!dot) continue;
        const n = parseInt(num);
        if (n < numericStep) {
            dot.style.background = 'linear-gradient(135deg,#16a34a,#22c55e)';
            dot.style.color = 'white';
            dot.style.boxShadow = '0 4px 12px rgba(34,197,94,.3)';
        } else if (n == numericStep) {
            dot.style.background = 'linear-gradient(135deg,#0A5A86,#1283c0)';
            dot.style.color = 'white';
            dot.style.boxShadow = '0 4px 12px rgba(13,111,166,.3)';
        } else {
            dot.style.background = '#e2e8f0';
            dot.style.color = '#94a3b8';
            dot.style.boxShadow = 'none';
        }
    }
    const line1 = document.getElementById('step-line-1');
    if (line1) line1.style.background = numericStep >= 2 ? '#22c55e' : '#e2e8f0';
    const line2 = document.getElementById('step-line-2');
    if (line2) line2.style.background = numericStep >= 3 ? '#22c55e' : '#e2e8f0';
}

// ── Step 1 ──
document.getElementById('step1-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    ['phone-error','national_id-error'].forEach(id => { document.getElementById(id).style.display='none'; });
    document.getElementById('step1-error').style.display = 'none';

    const phone = document.getElementById('phone').value.trim();
    const nationalId = document.getElementById('national_id').value.trim();

    if (!/^(05|5)\d{8}$/.test(phone)) {
        showFieldError('phone-error', 'رقم الجوال غير صالح (مثال: 05XXXXXXXX)'); return;
    }
    if (!/^\d{10}$/.test(nationalId)) {
        showFieldError('national_id-error', 'رقم الهوية يجب أن يكون 10 أرقام'); return;
    }

    await sendRegistrationOtp(phone, nationalId);
});

async function sendRegistrationOtp(phone, nationalId) {
    setBtn('step1-btn', 'step1-btn-text', 'step1-spinner', 'جاري الإرسال...', true);
    try {
        const res = await fetch('/register/otp/send', {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'Content-Type': 'application/json'},
            body: JSON.stringify({phone, national_id: nationalId}),
        });
        const data = await res.json();
        if (!res.ok || !data.success) throw new Error(data.message || Object.values(data.errors || {}).flat()[0] || 'تعذر إرسال الرمز');
        document.getElementById('step3-phone').value = phone;
        document.getElementById('step3-national-id').value = nationalId;
        document.getElementById('otp-phone').textContent = phone;
        document.getElementById('otp').value = '';
        goToStep(2);
        document.getElementById('otp').focus();
    } catch (error) {
        const el = document.getElementById('step1-error'); el.textContent = error.message; el.style.display = 'block';
    } finally {
        setBtn('step1-btn', 'step1-btn-text', 'step1-spinner', 'متابعة', false);
    }
}

document.getElementById('step2-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const otp = document.getElementById('otp').value.trim();
    document.getElementById('step2-error').style.display = 'none';
    if (!/^\d{6}$/.test(otp)) { showFieldError('otp-error', 'أدخل رمز التحقق المكون من 6 أرقام'); return; }
    document.getElementById('otp-error').style.display = 'none';
    setBtn('step2-btn', 'step2-btn-text', 'step2-spinner', 'جاري التحقق...', true);
    try {
        const res = await fetch('/register/otp/verify', {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'Content-Type': 'application/json'},
            body: JSON.stringify({otp}),
        });
        const data = await res.json();
        if (!res.ok || !data.success) throw new Error(data.message || 'رمز التحقق غير صحيح');
        goToStep(3);
    } catch (error) {
        const el = document.getElementById('step2-error'); el.textContent = error.message; el.style.display = 'block';
    } finally {
        setBtn('step2-btn', 'step2-btn-text', 'step2-spinner', 'تحقق ومتابعة', false);
    }
});

document.getElementById('resend-otp').addEventListener('click', () => sendRegistrationOtp(
    document.getElementById('phone').value.trim(), document.getElementById('national_id').value.trim()
));

// ── File helpers ──
function previewFile(input, previewId, labelId) {
    const preview = document.getElementById(previewId);
    const label   = document.getElementById(labelId);
    if (!input.files || !input.files[0]) return;
    const file = input.files[0];
    const name = file.name.length > 22 ? file.name.substring(0, 22) + '…' : file.name;
    if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.innerHTML = `<img src="${e.target.result}" style="width:100%;height:60px;object-fit:cover;border-radius:8px;margin-bottom:4px;"><p style="font-size:10px;color:#0D6FA6;font-weight:600;margin:0;">${name}</p>`;
        };
        reader.readAsDataURL(file);
    } else {
        preview.innerHTML = `<svg style="width:22px;height:22px;color:#0D6FA6;margin:0 auto 4px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg><p style="font-size:10px;color:#0D6FA6;font-weight:600;margin:0;">${name}</p>`;
    }
    label.style.borderColor = '#0D6FA6'; label.style.borderStyle = 'solid';
}

function handleDrop(event, inputId, previewId, labelId) {
    event.preventDefault();
    const input = document.getElementById(inputId);
    if (event.dataTransfer.files.length) {
        const transfer = new DataTransfer();
        transfer.items.add(event.dataTransfer.files[0]);
        input.files = transfer.files;
        previewFile(input, previewId, labelId);
    }
    document.getElementById(labelId).style.borderColor = '#0D6FA6';
}

function toggleCheckbox(cb, boxId) {
    const box = document.getElementById(boxId);
    box.style.borderColor = cb.checked ? '#0A5A86' : '#e2e8f0';
    box.style.background  = cb.checked ? '#f0f4ff' : '#f8fafc';
}

// ── Step 3 submit ──
document.getElementById('step3-form').addEventListener('submit', async function(e) {
    e.preventDefault();

    const errorIds = ['name-error','dob-error','gender-error','nationality-error','email-error','password-error',
        'spec-error','spec-type-error','grad-error','front-error','back-error','cert-error','confirm-error','terms-error'];
    errorIds.forEach(id => { const el = document.getElementById(id); if(el){ el.textContent=''; el.style.display='none'; }});
    document.getElementById('step3-error').style.display = 'none';

    let valid = true;
    const v = (id, errId, msg) => {
        const el = document.getElementById(id); if (!el) return;
        const val = el.type === 'checkbox' ? el.checked : el.value.trim();
        if (!val) { showFieldError(errId, msg); valid = false; }
    };

    v('name',                'name-error',        'الاسم الكامل مطلوب');
    v('date_of_birth',       'dob-error',         'تاريخ الميلاد مطلوب');
    v('gender',              'gender-error',      'الجنس مطلوب');
    v('nationality',         'nationality-error', 'الجنسية مطلوبة');
    v('email',               'email-error',       'البريد الإلكتروني مطلوب');
    v('specialization',      'spec-error',        'نوع المؤهل مطلوب');
    v('specialization_type', 'spec-type-error',   'المؤهل التعليمي مطلوب');
    v('date_of_graduation',  'grad-error',        'تاريخ التخرج مطلوب');

    const pw = document.getElementById('password').value;
    const pwc = document.getElementById('password_confirmation').value;
    if (pw.length < 8) { showFieldError('password-error', 'كلمة المرور 8 أحرف على الأقل'); valid = false; }
    else if (pw !== pwc) { showFieldError('password-error', 'تأكيد كلمة المرور غير متطابق'); valid = false; }

    if (!document.getElementById('national_id_front').files.length) { showFieldError('front-error', 'صورة الهوية الأمامية مطلوبة'); valid = false; }
    if (!document.getElementById('national_id_back').files.length)  { showFieldError('back-error',  'صورة الهوية الخلفية مطلوبة'); valid = false; }
    if (!document.getElementById('certificate').files.length)        { showFieldError('cert-error',  'الشهادة مطلوبة'); valid = false; }
    if (!document.getElementById('is_confirm_user').checked) { showFieldError('confirm-error', 'يجب الإقرار بصحة البيانات'); valid = false; }
    if (!document.getElementById('is_terms').checked)         { showFieldError('terms-error',   'يجب الموافقة على الشروط'); valid = false; }

    if (!valid) return;

    setBtn('step3-btn', 'step3-btn-text', 'step3-spinner', 'جاري إنشاء الحساب...', true);

    try {
        const res  = await fetch('/register/complete', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: new FormData(document.getElementById('step3-form')),
        });
        const data = await res.json();

        if (!res.ok || !data.success) {
            let msg = data.message || 'حدث خطأ';
            if (data.errors) msg = Object.values(data.errors).flat().join('<br>');
            const el = document.getElementById('step3-error');
            el.innerHTML = msg; el.style.display = 'block';
            setBtn('step3-btn', 'step3-btn-text', 'step3-spinner', 'إنشاء الحساب', false);
            return;
        }
        goToStep('success');
    } catch {
        const el = document.getElementById('step3-error');
        el.textContent = 'حدث خطأ في الاتصال. حاول مرة أخرى.';
        el.style.display = 'block';
        setBtn('step3-btn', 'step3-btn-text', 'step3-spinner', 'إنشاء الحساب', false);
    }
});

function setBtn(btnId, textId, spinnerId, label, loading) {
    document.getElementById(btnId).disabled = loading;
    document.getElementById(textId).textContent = label;
    const sp = document.getElementById(spinnerId);
    sp.style.display = loading ? 'inline' : 'none';
}

function showFieldError(id, msg) {
    const el = document.getElementById(id);
    if (!el) return;
    el.textContent = msg; el.style.display = 'block';
}
</script>
</body>
</html>
