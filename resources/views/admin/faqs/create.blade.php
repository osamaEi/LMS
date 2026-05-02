@extends('layouts.dashboard')

@section('title', 'إضافة سؤال شائع')

@push('styles')
<style>
/* ── Base inputs ── */
.fi { width:100%; border:1.5px solid #e2e8f0; border-radius:10px; padding:10px 14px; font-size:.875rem; background:#f8fafc; color:#1e293b; outline:none; transition:border-color .15s,box-shadow .15s,background .15s; resize:vertical; font-family:inherit; }
.fi:focus { border-color:#0071AA; box-shadow:0 0 0 3px rgba(0,113,170,.12); background:#fff; }
.fi.is-error { border-color:#f87171; background:#fff5f5; }
.fi::placeholder { color:#94a3b8; }
.fi[dir="ltr"] { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
.fl { display:block; font-size:.78rem; font-weight:700; color:#475569; margin-bottom:6px; letter-spacing:.01em; }
.fl .req { color:#ef4444; margin-right:2px; }
.err-msg { color:#ef4444; font-size:.72rem; margin-top:5px; display:flex; align-items:center; gap:4px; }

/* ── Panel card ── */
.panel { background:#fff; border-radius:14px; border:1px solid #e2e8f0; overflow:hidden; }
.panel-head { padding:14px 18px; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; gap:10px; }
.panel-head-icon { width:34px; height:34px; border-radius:9px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.panel-head-icon svg { width:16px; height:16px; }
.panel-head-title { font-size:.85rem; font-weight:800; color:#1e293b; }
.panel-head-sub { font-size:.72rem; color:#94a3b8; margin-top:1px; }
.panel-body { padding:18px; }

/* ── Language tab switcher ── */
.lang-tabs { display:flex; background:#f1f5f9; border-radius:10px; padding:4px; gap:3px; margin-bottom:16px; width:fit-content; }
.lang-tab { padding:7px 18px; border-radius:7px; font-size:.8rem; font-weight:700; cursor:pointer; border:none; background:transparent; color:rgba(255,255,255,0.6); transition:all .15s; }
.lang-tab.active { background:#fff; color:#0071AA; box-shadow:0 1px 4px rgba(0,0,0,.1); }
.lang-panel { display:none; }
.lang-panel.active { display:block; }

/* ── Category cards ── */
.cat-grid { display:grid; grid-template-columns:1fr 1fr; gap:8px; }
.cat-card { position:relative; border:2px solid #e2e8f0; border-radius:11px; padding:12px 10px; cursor:pointer; transition:all .15s; background:#fff; display:flex; flex-direction:column; align-items:center; gap:5px; text-align:center; user-select:none; }
.cat-card:hover { border-color:#93c5fd; background:#f0f9ff; }
.cat-card.selected { border-color:#0071AA; background:#f0f9ff; box-shadow:0 0 0 3px rgba(0,113,170,.12); }
.cat-card input[type=radio] { position:absolute; opacity:0; pointer-events:none; }
.cat-card .cat-emoji { font-size:1.4rem; line-height:1; }
.cat-card .cat-name { font-size:.72rem; font-weight:700; color:#374151; line-height:1.3; }
.cat-card.selected .cat-name { color:#0071AA; }
.cat-card .cat-check { position:absolute; top:6px; left:6px; width:16px; height:16px; border-radius:50%; background:#0071AA; display:none; align-items:center; justify-content:center; }
.cat-card.selected .cat-check { display:flex; }
.cat-card .cat-check svg { width:10px; height:10px; color:#fff; stroke:white; }

/* ── Status toggle ── */
.status-wrap { display:flex; gap:8px; }
.status-opt { flex:1; position:relative; }
.status-opt input[type=radio] { position:absolute; opacity:0; pointer-events:none; }
.status-opt label { display:flex; align-items:center; gap:8px; padding:10px 12px; border:2px solid #e2e8f0; border-radius:10px; cursor:pointer; transition:all .15s; font-size:.8rem; font-weight:700; color:#64748b; }
.status-opt label:hover { border-color:#93c5fd; background:#f8fafc; }
.status-opt input:checked + label { border-color:transparent; }
.status-opt.active input:checked + label { border-color:#059669; background:#f0fdf4; color:#065f46; }
.status-opt.inactive input:checked + label { border-color:#dc2626; background:#fef2f2; color:#991b1b; }
.status-dot { width:8px; height:8px; border-radius:50%; flex-shrink:0; }
.status-opt.active .status-dot { background:#10b981; }
.status-opt.inactive .status-dot { background:#ef4444; }

/* ── Sort order input ── */
.sort-wrap { display:flex; align-items:center; border:1.5px solid #e2e8f0; border-radius:10px; overflow:hidden; background:#f8fafc; transition:border-color .15s,box-shadow .15s; }
.sort-wrap:focus-within { border-color:#0071AA; box-shadow:0 0 0 3px rgba(0,113,170,.12); background:#fff; }
.sort-wrap input { flex:1; border:none; background:transparent; padding:10px 14px; font-size:.875rem; color:#1e293b; outline:none; text-align:center; font-weight:700; }
.sort-btn { width:38px; height:38px; border:none; background:transparent; cursor:pointer; color:#64748b; font-size:1.1rem; line-height:1; display:flex; align-items:center; justify-content:center; transition:background .12s; flex-shrink:0; }
.sort-btn:hover { background:#e2e8f0; color:#0071AA; }

/* ── Live preview ── */
.preview-box { border:1.5px solid #e0f2fe; border-radius:12px; overflow:hidden; background:#fff; }
.preview-q { padding:14px 16px; background:#f0f9ff; border-bottom:1.5px solid #e0f2fe; display:flex; align-items:flex-start; gap:10px; cursor:default; }
.preview-q .pq-icon { width:26px; height:26px; border-radius:7px; background:#0071AA; display:flex; align-items:center; justify-content:center; flex-shrink:0; margin-top:1px; }
.preview-q .pq-icon span { color:#fff; font-size:.7rem; font-weight:800; }
.preview-q .pq-text { font-size:.84rem; font-weight:700; color:#0c4a6e; flex:1; }
.preview-a { padding:14px 16px; font-size:.8rem; color:#475569; line-height:1.75; }
.preview-empty { font-size:.75rem; color:#cbd5e1; font-style:italic; }

/* ── Char counter ── */
.char-counter { font-size:.7rem; color:#94a3b8; text-align: left; margin-top:4px; }
.char-counter.warn { color:#f59e0b; }
.char-counter.over { color:#ef4444; }

/* ── Buttons ── */
.btn-save { display:inline-flex; align-items:center; gap:8px; padding:11px 28px; background:linear-gradient(135deg,#0071AA,#005a88); color:#fff; border:none; border-radius:10px; font-size:.875rem; font-weight:800; cursor:pointer; transition:opacity .15s,transform .1s; letter-spacing:.01em; }
.btn-save:hover { opacity:.9; transform:translateY(-1px); }
.btn-save:active { transform:translateY(0); }
.btn-save svg { width:16px; height:16px; }
.btn-cancel { display:inline-flex; align-items:center; gap:8px; padding:11px 20px; background:#fff; color:#64748b; border:1.5px solid #e2e8f0; border-radius:10px; font-size:.875rem; font-weight:700; cursor:pointer; transition:all .15s; text-decoration:none; }
.btn-cancel:hover { background:#f1f5f9; border-color:#cbd5e1; color:#475569; }

/* ── Hero banner ── */
.hero-banner { background:linear-gradient(135deg,#0071AA 0%,#005a88 55%,#003d5c 100%); border-radius:16px; padding:22px 26px; color:#fff; position:relative; overflow:hidden; margin-bottom:24px; }
.hero-banner::before { content:''; position:absolute; inset:0; opacity:.06; background-image:radial-gradient(circle,#fff 1px,transparent 1px); background-size:20px 20px; }
.hero-banner .inner { position:relative; z-index:1; display:flex; align-items:center; gap:14px; }
.hero-back { width:36px; height:36px; border-radius:10px; background:rgba(255,255,255,.15); border:none; cursor:pointer; display:flex; align-items:center; justify-content:center; flex-shrink:0; text-decoration:none; transition:background .15s; }
.hero-back:hover { background:rgba(255,255,255,.25); }
.hero-back svg { width:16px; height:16px; color:#fff; }
.hero-step { display:inline-flex; align-items:center; gap:5px; background:rgba(255,255,255,.15); border-radius:20px; padding:4px 12px; font-size:.72rem; font-weight:700; margin-bottom:6px; width:fit-content; }

/* ── Section divider ── */
.sec-divider { display:flex; align-items:center; gap:10px; margin:0 0 14px; }
.sec-divider-line { flex:1; height:1px; background:#f1f5f9; }
.sec-divider-label { font-size:.7rem; font-weight:800; color:#94a3b8; text-transform:uppercase; letter-spacing:.06em; white-space:nowrap; }

/* ── Tip box ── */
.tip-box { background:#f0f9ff; border:1px solid #bae6fd; border-radius:9px; padding:10px 12px; display:flex; gap:8px; align-items:flex-start; }
.tip-box svg { width:15px; height:15px; color:#0284c7; flex-shrink:0; margin-top:1px; }
.tip-box p { font-size:.73rem; color:#0c4a6e; line-height:1.55; margin:0; }
</style>
@endpush

@section('content')
<div class="p-6 max-w-5xl mx-auto">

    {{-- ── Hero ── --}}
    <div class="hero-banner">
        <div class="inner">
            <a href="{{ route('admin.faqs.index') }}" class="hero-back">
                <svg style="width:16px;height:16px;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <div class="hero-step">
                    <svg style="width:11px;height:11px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    الأسئلة الشائعة
                </div>
                <h1 style="font-size:1.25rem;font-weight:800;margin:0 0 3px;">إضافة سؤال شائع جديد</h1>
                <p style="font-size:.8rem;opacity:.75;margin:0;">أدخل السؤال والإجابة ثم احفظ — سيظهر فوراً في صفحة FAQ</p>
            </div>
        </div>
    </div>

    {{-- ── Global errors ── --}}
    @if($errors->any())
    <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4">
        <div class="flex items-center gap-2 mb-2">
            <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <p style="font-size:.8rem;font-weight:700;color:#991b1b;">يوجد أخطاء في النموذج:</p>
        </div>
        <ul style="list-style:disc;padding-right:18px;font-size:.78rem;color:#b91c1c;display:flex;flex-direction:column;gap:3px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.faqs.store') }}" method="POST" id="faqForm">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            {{-- ════════════════════════════ LEFT — Main content ════════════════════════════ --}}
            <div class="lg:col-span-2 flex flex-col gap-5">

                {{-- ── Q&A Card ── --}}
                <div class="panel">
                    <div class="panel-head" style="background:linear-gradient(135deg,#0071AA,#005a88);border-radius:14px 14px 0 0;">
                        <div class="panel-head-icon" style="background:rgba(255,255,255,.2);">
                            <svg fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="panel-head-title" style="color:#fff;">السؤال والإجابة</div>
                            <div class="panel-head-sub" style="color:rgba(255,255,255,.65);">أدخل المحتوى بالعربية (مطلوب) والإنجليزية (اختياري)</div>
                        </div>

                        {{-- lang tabs in header --}}
                        <div style="margin-right:auto;">
                            <div class="lang-tabs" style="background:rgba(255,255,255,.15);margin-bottom:0;">
                                <button type="button" class="lang-tab active" id="tab-ar" onclick="switchLang('ar')"
                                        style="color:#fff;">
                                    عربي
                                </button>
                                <button type="button" class="lang-tab" id="tab-en" onclick="switchLang('en')"
                                        style="color:rgba(255,255,255,.6);">
                                    English
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="panel-body">

                        {{-- Arabic panel --}}
                        <div class="lang-panel active" id="panel-ar">
                            <div style="margin-bottom:16px;">
                                <label class="fl">
                                    <span class="req">*</span>
                                    السؤال بالعربية
                                </label>
                                <input type="text" name="question_ar" id="question_ar"
                                       value="{{ old('question_ar') }}"
                                       class="fi {{ $errors->has('question_ar') ? 'is-error' : '' }}"
                                       placeholder="مثال: كيف أتسجل في المعهد؟"
                                       maxlength="500"
                                       oninput="updateCounter(this,'cnt-qar',500);syncPreview()">
                                <div style="display:flex;justify-content:space-between;align-items:center;margin-top:4px;">
                                    @error('question_ar')
                                    <span class="err-msg">
                                        <svg style="width:12px;height:12px" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                        {{ $message }}
                                    </span>
                                    @else
                                    <span></span>
                                    @enderror
                                    <span class="char-counter" id="cnt-qar">0 / 500</span>
                                </div>
                            </div>

                            <div>
                                <label class="fl">
                                    <span class="req">*</span>
                                    الإجابة بالعربية
                                </label>
                                <textarea name="answer_ar" id="answer_ar" rows="6"
                                          class="fi {{ $errors->has('answer_ar') ? 'is-error' : '' }}"
                                          placeholder="اكتب الإجابة بوضوح وشمولية..."
                                          oninput="syncPreview()">{{ old('answer_ar') }}</textarea>
                                @error('answer_ar')
                                <span class="err-msg">
                                    <svg style="width:12px;height:12px" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                    {{ $message }}
                                </span>
                                @enderror
                            </div>
                        </div>

                        {{-- English panel --}}
                        <div class="lang-panel" id="panel-en">
                            <div style="margin-bottom:16px;">
                                <label class="fl">Question in English</label>
                                <input type="text" name="question_en" id="question_en"
                                       value="{{ old('question_en') }}"
                                       class="fi"
                                       dir="ltr"
                                       placeholder="e.g. How do I register at the institute?"
                                       maxlength="500"
                                       oninput="updateCounter(this,'cnt-qen',500)">
                                <div style="display:flex;justify-content:flex-start;margin-top:4px;">
                                    <span class="char-counter" id="cnt-qen">0 / 500</span>
                                </div>
                            </div>

                            <div>
                                <label class="fl">Answer in English</label>
                                <textarea name="answer_en" rows="6"
                                          class="fi"
                                          dir="ltr"
                                          placeholder="Write a clear and complete answer in English...">{{ old('answer_en') }}</textarea>
                                <div class="tip-box" style="margin-top:10px;">
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <p>الإنجليزية اختيارية. إذا تركتها فارغة، ستُعرض الإجابة العربية للزوار الإنجليز تلقائياً.</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- ── Live Preview ── --}}
                <div class="panel">
                    <div class="panel-head">
                        <div class="panel-head-icon" style="background:#f0fdf4;">
                            <svg fill="none" viewBox="0 0 24 24" stroke="#059669" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="panel-head-title">معاينة مباشرة</div>
                            <div class="panel-head-sub">هكذا سيبدو السؤال على صفحة الموقع</div>
                        </div>
                        <span id="previewLangBadge"
                              style="margin-right:auto;font-size:.7rem;font-weight:700;background:#f1f5f9;color:#64748b;padding:3px 10px;border-radius:20px;">
                            عربي
                        </span>
                    </div>
                    <div class="panel-body">
                        <div class="preview-box">
                            <div class="preview-q">
                                <div class="pq-icon"><span>س</span></div>
                                <div class="pq-text" id="previewQ">
                                    <span class="preview-empty">سيظهر نص السؤال هنا...</span>
                                </div>
                            </div>
                            <div class="preview-a" id="previewA">
                                <span class="preview-empty">سيظهر نص الإجابة هنا...</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ════════════════════════════ RIGHT — Settings sidebar ════════════════════════════ --}}
            <div class="flex flex-col gap-5">

                {{-- ── Category ── --}}
                <div class="panel">
                    <div class="panel-head">
                        <div class="panel-head-icon" style="background:#fff7ed;">
                            <svg fill="none" viewBox="0 0 24 24" stroke="#ea580c" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="panel-head-title">الفئة</div>
                            <div class="panel-head-sub">اختر قسم السؤال</div>
                        </div>
                    </div>
                    <div class="panel-body">
                        @if($errors->has('category'))
                        <span class="err-msg" style="margin-bottom:10px;display:flex;">
                            <svg style="width:12px;height:12px" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $errors->first('category') }}
                        </span>
                        @endif
                        <div class="cat-grid">
                            @foreach($categories as $key => $cat)
                            @php $isSelected = old('category', 'registration') === $key; @endphp
                            <label class="cat-card {{ $isSelected ? 'selected' : '' }}" onclick="selectCat(this)">
                                <input type="radio" name="category" value="{{ $key }}" {{ $isSelected ? 'checked' : '' }}>
                                <div class="cat-check">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <span class="cat-emoji">{{ $cat['icon'] }}</span>
                                <span class="cat-name">{{ $cat['label'] }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- ── Sort Order ── --}}
                <div class="panel">
                    <div class="panel-head">
                        <div class="panel-head-icon" style="background:#f5f3ff;">
                            <svg fill="none" viewBox="0 0 24 24" stroke="#7c3aed" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"/>
                            </svg>
                        </div>
                        <div>
                            <div class="panel-head-title">الترتيب</div>
                            <div class="panel-head-sub">رقم أصغر = يظهر أولاً</div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="sort-wrap">
                            <button type="button" class="sort-btn" onclick="changeSort(-1)">−</button>
                            <input type="number" name="sort_order" id="sortInput"
                                   value="{{ old('sort_order', 0) }}"
                                   min="0" max="999">
                            <button type="button" class="sort-btn" onclick="changeSort(1)">+</button>
                        </div>
                        <p style="font-size:.72rem;color:#94a3b8;margin-top:8px;text-align:center;">0 = أول قائمة، 999 = آخر قائمة</p>
                    </div>
                </div>

                {{-- ── Status ── --}}
                <div class="panel">
                    <div class="panel-head">
                        <div class="panel-head-icon" style="background:#f0fdf4;">
                            <svg fill="none" viewBox="0 0 24 24" stroke="#059669" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="panel-head-title">الحالة</div>
                            <div class="panel-head-sub">هل يظهر في الموقع؟</div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="status-wrap">
                            <div class="status-opt active">
                                <input type="radio" name="status" id="status_active" value="active"
                                       {{ old('status','active') === 'active' ? 'checked' : '' }}>
                                <label for="status_active">
                                    <span class="status-dot"></span>
                                    نشط
                                </label>
                            </div>
                            <div class="status-opt inactive">
                                <input type="radio" name="status" id="status_inactive" value="inactive"
                                       {{ old('status') === 'inactive' ? 'checked' : '' }}>
                                <label for="status_inactive">
                                    <span class="status-dot"></span>
                                    مخفي
                                </label>
                            </div>
                        </div>
                        <div id="statusHint" style="margin-top:10px;font-size:.73rem;padding:8px 10px;border-radius:8px;"></div>
                    </div>
                </div>

                {{-- ── Submit ── --}}
                <div style="display:flex;flex-direction:column;gap:8px;">
                    <button type="submit" class="btn-save" style="width:100%;justify-content:center;">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        حفظ السؤال
                    </button>
                    <a href="{{ route('admin.faqs.index') }}" class="btn-cancel" style="justify-content:center;">
                        <svg style="width:15px;height:15px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        إلغاء
                    </a>
                </div>

            </div>
        </div>
    </form>

</div>

<script>
/* ── Language switcher ── */
let activeLang = 'ar';
function switchLang(lang) {
    activeLang = lang;
    document.querySelectorAll('.lang-panel').forEach(p => p.classList.remove('active'));
    document.getElementById('panel-' + lang).classList.add('active');

    const tabAr = document.getElementById('tab-ar');
    const tabEn = document.getElementById('tab-en');

    if (lang === 'ar') {
        tabAr.classList.add('active');
        tabEn.classList.remove('active');
        tabAr.style.color = '#fff';
        tabEn.style.color = 'rgba(255,255,255,.6)';
    } else {
        tabEn.classList.add('active');
        tabAr.classList.remove('active');
        tabEn.style.color = '#fff';
        tabAr.style.color = 'rgba(255,255,255,.6)';
    }
    document.getElementById('previewLangBadge').textContent = lang === 'ar' ? 'عربي' : 'English';
    syncPreview();
}

/* ── Live preview sync ── */
function syncPreview() {
    const qEl = document.getElementById('previewQ');
    const aEl = document.getElementById('previewA');
    const q = document.getElementById('question_ar').value.trim();
    const a = document.getElementById('answer_ar').value.trim();

    qEl.innerHTML = q ? q : '<span class="preview-empty">سيظهر نص السؤال هنا...</span>';
    aEl.innerHTML = a ? a.replace(/\n/g,'<br>') : '<span class="preview-empty">سيظهر نص الإجابة هنا...</span>';
}

/* ── Char counter ── */
function updateCounter(el, counterId, max) {
    const len = el.value.length;
    const el2 = document.getElementById(counterId);
    el2.textContent = len + ' / ' + max;
    el2.className = 'char-counter' + (len > max * .9 ? (len >= max ? ' over' : ' warn') : '');
}

/* ── Category card selection ── */
function selectCat(card) {
    document.querySelectorAll('.cat-card').forEach(c => c.classList.remove('selected'));
    card.classList.add('selected');
    card.querySelector('input').checked = true;
}

/* ── Sort spinner ── */
function changeSort(delta) {
    const inp = document.getElementById('sortInput');
    const val = parseInt(inp.value || 0) + delta;
    inp.value = Math.max(0, Math.min(999, val));
}

/* ── Status hint ── */
function updateStatusHint() {
    const hint = document.getElementById('statusHint');
    const active = document.getElementById('status_active').checked;
    if (active) {
        hint.style.background = '#f0fdf4';
        hint.style.color = '#065f46';
        hint.textContent = '✅ السؤال سيظهر للزوار على صفحة الأسئلة الشائعة.';
    } else {
        hint.style.background = '#fef2f2';
        hint.style.color = '#991b1b';
        hint.textContent = '🔴 السؤال مخفي ولن يظهر للزوار حتى تغيّر الحالة.';
    }
}
document.querySelectorAll('input[name=status]').forEach(r => r.addEventListener('change', updateStatusHint));
updateStatusHint();

/* ── Init counters ── */
const qArInput = document.getElementById('question_ar');
if (qArInput.value) updateCounter(qArInput,'cnt-qar',500);
const qEnInput = document.getElementById('question_en');
if (qEnInput && qEnInput.value) updateCounter(qEnInput,'cnt-qen',500);
syncPreview();
</script>
@endsection
