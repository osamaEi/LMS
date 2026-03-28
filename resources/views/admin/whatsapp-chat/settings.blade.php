@extends('layouts.dashboard')

@section('title', 'إعدادات الذكاء الاصطناعي')

@push('styles')
<style>
    .ai-header {
        background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%);
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 1.5rem;
        color: white;
        position: relative;
        overflow: hidden;
    }
    .ai-header::before {
        content: '';
        position: absolute;
        top: -40px; right: -40px;
        width: 220px; height: 220px;
        background: radial-gradient(circle, rgba(255,255,255,0.07) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    /* Tab nav */
    .tab-nav {
        display: flex;
        gap: .5rem;
        background: white;
        border-radius: 14px;
        padding: .4rem;
        box-shadow: 0 1px 8px rgba(0,0,0,0.07);
        margin-bottom: 1.25rem;
        overflow-x: auto;
    }
    .tab-btn {
        flex-shrink: 0;
        padding: .5rem 1.1rem;
        border-radius: 10px;
        font-size: .82rem;
        font-weight: 600;
        cursor: pointer;
        border: none;
        background: transparent;
        color: #64748b;
        transition: all .15s;
        font-family: inherit;
    }
    .tab-btn.active {
        background: #7c3aed;
        color: white;
    }

    /* Cards */
    .section-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 1px 8px rgba(0,0,0,0.07);
        padding: 1.5rem;
        margin-bottom: 1.25rem;
        display: none;
    }
    .section-card.active { display: block; }

    .field-label {
        font-size: .82rem;
        font-weight: 700;
        color: #374151;
        margin-bottom: .4rem;
        display: block;
    }
    .field-hint {
        font-size: .75rem;
        color: #94a3b8;
        margin-bottom: .6rem;
    }
    .field-input {
        width: 100%;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        padding: .6rem .9rem;
        font-size: .85rem;
        outline: none;
        font-family: inherit;
        transition: border-color .15s;
        background: white;
    }
    .field-input:focus { border-color: #7c3aed; }
    textarea.field-input { resize: vertical; }

    /* Pill radio */
    .pill-group { display: flex; gap: .5rem; flex-wrap: wrap; }
    .pill-label { position: relative; cursor: pointer; }
    .pill-label input { position: absolute; opacity: 0; width: 0; }
    .pill-box {
        padding: .45rem 1.1rem;
        border-radius: 10px;
        border: 2px solid #e2e8f0;
        font-size: .8rem;
        font-weight: 600;
        color: #64748b;
        transition: all .15s;
        user-select: none;
    }
    .pill-label input:checked + .pill-box {
        border-color: #7c3aed;
        background: #ede9fe;
        color: #6d28d9;
    }

    /* Step rows */
    .step-row {
        display: flex;
        align-items: center;
        gap: .5rem;
        margin-bottom: .5rem;
        animation: fadeIn .15s ease;
    }
    .step-num {
        width: 26px; height: 26px;
        background: #ede9fe; color: #7c3aed;
        border-radius: 50%;
        font-size: .75rem; font-weight: 700;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }

    /* FAQ rows */
    .faq-row {
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        padding: .9rem 1rem;
        margin-bottom: .6rem;
        background: #fafafa;
        animation: fadeIn .15s ease;
    }

    /* Forbidden rows */
    .forbidden-row {
        display: flex;
        align-items: center;
        gap: .5rem;
        margin-bottom: .5rem;
        animation: fadeIn .15s ease;
    }
    .forbidden-input {
        flex: 1;
        border: 1.5px solid #fee2e2;
        border-radius: 10px;
        padding: .5rem .85rem;
        font-size: .85rem;
        outline: none;
        font-family: inherit;
        background: #fff8f8;
        transition: border-color .15s;
    }
    .forbidden-input:focus { border-color: #ef4444; }

    /* Buttons */
    .btn-add {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .4rem .9rem;
        border-radius: 8px;
        font-size: .78rem;
        font-weight: 600;
        cursor: pointer;
        border: none;
        transition: background .15s;
        margin-top: .5rem;
        font-family: inherit;
    }
    .btn-add-purple { background: #ede9fe; color: #6d28d9; }
    .btn-add-purple:hover { background: #ddd6fe; }
    .btn-add-red { background: #fee2e2; color: #dc2626; }
    .btn-add-red:hover { background: #fecaca; }
    .btn-remove {
        width: 28px; height: 28px;
        border: none;
        background: #f1f5f9; color: #94a3b8;
        border-radius: 7px;
        cursor: pointer;
        font-size: 1rem;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        transition: all .15s;
    }
    .btn-remove:hover { background: #fee2e2; color: #dc2626; }

    /* Save bar */
    .save-bar {
        position: sticky;
        bottom: 1rem;
        background: white;
        border-radius: 14px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.12);
        padding: .85rem 1.25rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 1rem;
    }
    .save-btn {
        background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%);
        color: white;
        border: none;
        border-radius: 10px;
        padding: .65rem 2rem;
        font-size: .88rem;
        font-weight: 700;
        cursor: pointer;
        transition: opacity .15s;
        font-family: inherit;
    }
    .save-btn:hover { opacity: .9; }

    /* Preview */
    .preview-box {
        background: #0f172a;
        color: #94a3b8;
        border-radius: 12px;
        padding: 1.1rem 1.25rem;
        font-size: .78rem;
        font-family: monospace;
        line-height: 1.7;
        white-space: pre-wrap;
        max-height: 320px;
        overflow-y: auto;
        margin-top: .75rem;
    }
    .preview-box .hl-label { color: #a78bfa; font-weight: 700; }
    .preview-box .hl-value { color: #6ee7b7; }

    /* Info tip */
    .tip { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: .65rem 1rem; font-size: .78rem; color: #15803d; margin-bottom: 1rem; }
    .tip-blue { background: #eff6ff; border-color: #bfdbfe; color: #1d4ed8; }
    .tip-purple { background: #faf5ff; border-color: #e9d5ff; color: #6d28d9; }

    .divider { border: none; border-top: 1px solid #f1f5f9; margin: 1.1rem 0; }

    @keyframes fadeIn { from { opacity:0; transform:translateY(-4px); } to { opacity:1; transform:none; } }

    /* Char counter */
    .char-counter { font-size: .72rem; color: #94a3b8; text-align: left; margin-top: .3rem; }
    .char-counter.warn { color: #f59e0b; }
    .char-counter.over { color: #ef4444; }
</style>
@endpush

@section('content')
<div class="p-4 md:p-6 max-w-3xl mx-auto">

    {{-- Header --}}
    <div class="ai-header">
        <div class="relative z-10 flex items-center gap-3">
            <a href="{{ route('admin.whatsapp-chat.index') }}"
               class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white w-9 h-9 flex items-center justify-center rounded-xl transition flex-shrink-0 text-lg">←</a>
            <div>
                <h1 class="text-xl font-bold">اعدادات الذكاء الاصطناعي</h1>
                <p class="text-sm opacity-75 mt-0.5">تحكّم في كيفية رد الـ AI على كل سؤال</p>
            </div>
            <div class="mr-auto text-left">
                <div class="text-xs opacity-60">الموديل</div>
                <div class="text-sm font-bold">Claude Sonnet</div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm font-medium flex items-center gap-2">
        <span>&#10003;</span> {{ session('success') }}
    </div>
    @endif

    {{-- Tab nav --}}
    <div class="tab-nav">
        <button type="button" class="tab-btn active" onclick="switchTab('platform', this)">المنصة</button>
        <button type="button" class="tab-btn" onclick="switchTab('identity', this)">الشخصية</button>
        <button type="button" class="tab-btn" onclick="switchTab('steps', this)">خطوات الرد</button>
        <button type="button" class="tab-btn" onclick="switchTab('faqs', this)">أسئلة وأجوبة</button>
        <button type="button" class="tab-btn" onclick="switchTab('forbidden', this)">ممنوعات</button>
        <button type="button" class="tab-btn" onclick="switchTab('links', this)">روابط ذكية</button>
        <button type="button" class="tab-btn" onclick="switchTab('preview', this)">معاينة</button>
    </div>

    <form action="{{ route('admin.whatsapp-chat.save-prompt') }}" method="POST" id="aiForm">
    @csrf

    {{-- ════ TAB: المنصة ════ --}}
    <div class="section-card active" id="tab-platform">
        <p class="field-label" style="font-size:.95rem;margin-bottom:.25rem;">معلومات المنصة</p>
        <p class="field-hint" style="margin-bottom:1rem;">اكتب كل حاجة عن منصتك — الـ AI هيستخدمها عند أي سؤال عام. كلما كانت أدق كلما كانت الردود أفضل.</p>

        <div class="tip tip-blue">
            اكتب: اسم المنصة، البرامج المتاحة، الأسعار، طريقة التسجيل، بيانات التواصل، الشهادات، أوقات العمل.
        </div>

        <textarea name="ai_platform_info" id="platformInfo" rows="12"
                  class="field-input"
                  maxlength="5000"
                  placeholder="مثال:
اسم المنصة: منصة نور التعليمية
ما تقدمه: برامج دبلوم وشهادات مهنية عبر الإنترنت
البرامج المتاحة:
- دبلوم تقنية المعلومات: 3 اشهر - 1500 ريال
- دبلوم المحاسبة: 4 اشهر - 1800 ريال
طريقة التسجيل: الموقع www.noor.sa او الواتساب 0501234567
الشهادات: معتمدة من وزارة التعليم
التواصل: واتساب 0501234567 - info@noor.sa">{{ $settings['ai_platform_info'] ?? '' }}</textarea>
        <div class="char-counter" id="platformCounter">0 / 5000</div>
    </div>

    {{-- ════ TAB: الشخصية ════ --}}
    <div class="section-card" id="tab-identity">
        <p class="field-label" style="font-size:.95rem;margin-bottom:1rem;">شخصية الـ AI</p>

        <div class="mb-5">
            <label class="field-label">اسم الـ AI</label>
            <p class="field-hint">الاسم اللي هيعرّف بيه نفسه للطلاب</p>
            <input type="text" name="ai_name" value="{{ $settings['ai_name'] }}"
                   class="field-input"
                   placeholder="مثال: مساعد منصة الارتقاء">
        </div>

        <hr class="divider">

        <div class="mb-5">
            <label class="field-label">نبرة الكلام</label>
            <p class="field-hint">كيف يتكلم مع الطالب</p>
            <div class="pill-group">
                <label class="pill-label">
                    <input type="radio" name="ai_tone" value="friendly" {{ $settings['ai_tone'] === 'friendly' ? 'checked' : '' }}>
                    <div class="pill-box">ودّي وبسيط</div>
                </label>
                <label class="pill-label">
                    <input type="radio" name="ai_tone" value="formal" {{ $settings['ai_tone'] === 'formal' ? 'checked' : '' }}>
                    <div class="pill-box">رسمي ومهني</div>
                </label>
                <label class="pill-label">
                    <input type="radio" name="ai_tone" value="neutral" {{ $settings['ai_tone'] === 'neutral' ? 'checked' : '' }}>
                    <div class="pill-box">محايد</div>
                </label>
            </div>
        </div>

        <hr class="divider">

        <div>
            <label class="field-label">لغة الرد</label>
            <p class="field-hint">باي لغة يرد على الطلاب</p>
            <div class="pill-group">
                <label class="pill-label">
                    <input type="radio" name="ai_language" value="ar" {{ $settings['ai_language'] === 'ar' ? 'checked' : '' }}>
                    <div class="pill-box">عربي فقط</div>
                </label>
                <label class="pill-label">
                    <input type="radio" name="ai_language" value="en" {{ $settings['ai_language'] === 'en' ? 'checked' : '' }}>
                    <div class="pill-box">English only</div>
                </label>
                <label class="pill-label">
                    <input type="radio" name="ai_language" value="both" {{ $settings['ai_language'] === 'both' ? 'checked' : '' }}>
                    <div class="pill-box">حسب لغة الطالب</div>
                </label>
            </div>
        </div>
    </div>

    {{-- ════ TAB: خطوات الرد ════ --}}
    <div class="section-card" id="tab-steps">
        <p class="field-label" style="font-size:.95rem;margin-bottom:.25rem;">خطوات الرد</p>
        <p class="field-hint" style="margin-bottom:1rem;">حدّد كيف يتصرف الـ AI مع كل رسالة — بالترتيب</p>

        <div class="tip">
            مثال: "رحّب بالطالب باسمه" ثم "اسأل عن طبيعة مشكلته" ثم "قدّم الحل بشكل مختصر"
        </div>

        <div id="stepsContainer">
            @forelse($settings['ai_steps'] as $i => $step)
            <div class="step-row" data-step>
                <div class="step-num step-counter">{{ $i + 1 }}</div>
                <input type="text" name="ai_steps[]" value="{{ $step }}"
                       class="field-input" style="margin:0" placeholder="اكتب الخطوة...">
                <button type="button" class="btn-remove" onclick="removeRow(this)">×</button>
            </div>
            @empty
            <div class="step-row" data-step>
                <div class="step-num step-counter">1</div>
                <input type="text" name="ai_steps[]" class="field-input" style="margin:0" placeholder="مثال: رحّب بالطالب باسمه">
                <button type="button" class="btn-remove" onclick="removeRow(this)">×</button>
            </div>
            @endforelse
        </div>

        <button type="button" class="btn-add btn-add-purple" onclick="addStep()">+ أضف خطوة</button>
    </div>

    {{-- ════ TAB: أسئلة وأجوبة ════ --}}
    <div class="section-card" id="tab-faqs">
        <p class="field-label" style="font-size:.95rem;margin-bottom:.25rem;">أسئلة وأجوبة محددة</p>
        <p class="field-hint" style="margin-bottom:1rem;">لو الطالب سأل كده، الـ AI يرد بالضبط بكده — إجاباتك الحرفية</p>

        <div class="tip tip-purple">
            استخدمها للأسئلة اللي بتتكرر كتير — طريقة التسجيل، الأسعار، الشهادات، أوقات المحاضرات
        </div>

        <div id="faqsContainer">
            @forelse($settings['ai_faqs'] as $i => $faq)
            <div class="faq-row" data-faq>
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.6rem">
                    <span style="font-size:.75rem;font-weight:700;color:#7c3aed;">سؤال {{ $i + 1 }}</span>
                    <button type="button" class="btn-remove" onclick="removeRow(this)">×</button>
                </div>
                <label class="field-label" style="font-size:.75rem">السؤال</label>
                <input type="text" name="ai_faqs[{{ $i }}][q]" value="{{ $faq['q'] ?? '' }}"
                       class="field-input mb-2" placeholder="مثال: كيف أسجّل في المنصة؟">
                <label class="field-label" style="font-size:.75rem;margin-top:.4rem">الجواب</label>
                <textarea name="ai_faqs[{{ $i }}][a]" rows="2"
                          class="field-input"
                          placeholder="اكتب الجواب الحرفي الذي سيرده الـ AI">{{ $faq['a'] ?? '' }}</textarea>
            </div>
            @empty
            <div class="faq-row" data-faq>
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.6rem">
                    <span style="font-size:.75rem;font-weight:700;color:#7c3aed;">سؤال 1</span>
                    <button type="button" class="btn-remove" onclick="removeRow(this)">×</button>
                </div>
                <label class="field-label" style="font-size:.75rem">السؤال</label>
                <input type="text" name="ai_faqs[0][q]" class="field-input mb-2" placeholder="مثال: كيف أسجّل في المنصة؟">
                <label class="field-label" style="font-size:.75rem;margin-top:.4rem">الجواب</label>
                <textarea name="ai_faqs[0][a]" rows="2" class="field-input"
                          placeholder="اكتب الجواب الحرفي الذي سيرده الـ AI"></textarea>
            </div>
            @endforelse
        </div>

        <button type="button" class="btn-add btn-add-purple" onclick="addFaq()">+ أضف سؤال وجواب</button>
    </div>

    {{-- ════ TAB: ممنوعات ════ --}}
    <div class="section-card" id="tab-forbidden">
        <p class="field-label" style="font-size:.95rem;margin-bottom:.25rem;">موضوعات ممنوعة</p>
        <p class="field-hint" style="margin-bottom:1rem;">الـ AI لن يتكلم في هذه الموضوعات تحت أي ظرف حتى لو الطالب أصرّ</p>

        <div id="forbiddenContainer">
            @forelse($settings['ai_forbidden'] as $topic)
            <div class="forbidden-row" data-forbidden>
                <span style="color:#ef4444;flex-shrink:0;font-size:.85rem">ممنوع</span>
                <input type="text" name="ai_forbidden[]" value="{{ $topic }}"
                       class="forbidden-input" placeholder="مثال: أسعار المنافسين">
                <button type="button" class="btn-remove" onclick="removeRow(this)">×</button>
            </div>
            @empty
            <div class="forbidden-row" data-forbidden>
                <span style="color:#ef4444;flex-shrink:0;font-size:.85rem">ممنوع</span>
                <input type="text" name="ai_forbidden[]"
                       class="forbidden-input" placeholder="مثال: أسعار المنافسين">
                <button type="button" class="btn-remove" onclick="removeRow(this)">×</button>
            </div>
            @endforelse
        </div>

        <button type="button" class="btn-add btn-add-red" onclick="addForbidden()">+ أضف موضوع ممنوع</button>

        <hr class="divider" style="margin-top:1.25rem">

        <div>
            <label class="field-label">جملة الرد عند سؤال ممنوع</label>
            <p class="field-hint">الـ AI يقول هذه الجملة حرفياً لو سأله أحد عن موضوع ممنوع</p>
            <input type="text" name="ai_forbidden_reply"
                   value="{{ $settings['ai_forbidden_reply'] }}"
                   class="field-input"
                   placeholder="مثال: هذا الموضوع خارج نطاق مساعدتي. تواصل مع الدعم على 0501234567">
        </div>
    </div>

    {{-- ════ TAB: روابط ذكية ════ --}}
    <div class="section-card" id="tab-links">
        <p class="field-label" style="font-size:.95rem;margin-bottom:.25rem;">روابط ذكية</p>
        <p class="field-hint" style="margin-bottom:1rem;">لما الطالب يسأل عن موضوع معين، الـ AI يبعتله الرابط المناسب تلقائياً في ردّه</p>

        <div class="tip tip-blue">
            مثال: لو سأل عن "تذكرة دعم" → يبعتله رابط صفحة إنشاء التذكرة. لو سأل عن "التسجيل" → يبعتله رابط صفحة البرامج.
        </div>

        <div id="linksContainer">
            @php
                $quickLinks = [
                    ['topic' => 'تذكرة دعم فني أو مشكلة تقنية', 'url' => url('student/tickets/create'), 'label' => 'إنشاء تذكرة دعم'],
                    ['topic' => 'التسجيل في برنامج أو دورة', 'url' => url('student/enroll-program'), 'label' => 'صفحة التسجيل'],
                    ['topic' => 'الدرجات والنتائج', 'url' => url('student/grades'), 'label' => 'صفحة الدرجات'],
                    ['topic' => 'الدفع والأقساط', 'url' => url('student/payments'), 'label' => 'صفحة المدفوعات'],
                    ['topic' => 'الحضور والغياب', 'url' => url('student/attendance'), 'label' => 'صفحة الحضور'],
                    ['topic' => 'الجدول والمحاضرات', 'url' => url('student/schedule'), 'label' => 'الجدول الدراسي'],
                ];
            @endphp

            {{-- Quick add chips --}}
            <div style="margin-bottom:1rem">
                <p style="font-size:.75rem;color:#64748b;margin-bottom:.5rem;font-weight:600">إضافة سريعة:</p>
                <div style="display:flex;flex-wrap:wrap;gap:.4rem">
                    @foreach($quickLinks as $ql)
                    <button type="button"
                            onclick="addQuickLink('{{ addslashes($ql['topic']) }}','{{ $ql['url'] }}','{{ addslashes($ql['label']) }}')"
                            style="background:#f1f5f9;border:1px solid #e2e8f0;border-radius:8px;padding:.3rem .75rem;font-size:.75rem;cursor:pointer;color:#475569;font-family:inherit;transition:background .15s"
                            onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">
                        + {{ $ql['label'] }}
                    </button>
                    @endforeach
                </div>
            </div>

            @forelse($settings['ai_smart_links'] as $i => $link)
            <div class="link-row" data-link style="border:1.5px solid #e0e7ff;border-radius:12px;padding:.85rem 1rem;margin-bottom:.6rem;background:#fafbff;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.6rem">
                    <span style="font-size:.75rem;font-weight:700;color:#4f46e5;">رابط {{ $i + 1 }}</span>
                    <button type="button" class="btn-remove" onclick="removeRow(this)">×</button>
                </div>
                <label class="field-label" style="font-size:.75rem">لما يسأل عن</label>
                <input type="text" name="ai_smart_links[{{ $i }}][topic]" value="{{ $link['topic'] ?? '' }}"
                       class="field-input mb-2" placeholder="مثال: تذكرة دعم فني">
                <label class="field-label" style="font-size:.75rem;margin-top:.3rem">الرابط</label>
                <input type="text" name="ai_smart_links[{{ $i }}][url]" value="{{ $link['url'] ?? '' }}"
                       class="field-input mb-2" placeholder="مثال: http://127.0.0.1:8000/student/tickets/create" dir="ltr">
                <label class="field-label" style="font-size:.75rem;margin-top:.3rem">اسم الرابط (اختياري)</label>
                <input type="text" name="ai_smart_links[{{ $i }}][label]" value="{{ $link['label'] ?? '' }}"
                       class="field-input" placeholder="مثال: إنشاء تذكرة دعم">
            </div>
            @empty
            <p style="text-align:center;color:#94a3b8;font-size:.82rem;padding:1rem 0">
                لا توجد روابط محفوظة — استخدم الإضافة السريعة أو أضف يدوياً
            </p>
            @endforelse
        </div>

        <button type="button" class="btn-add btn-add-purple" onclick="addLink()">+ أضف رابط يدوياً</button>
    </div>

    {{-- ════ TAB: معاينة ════ --}}
    <div class="section-card" id="tab-preview">
        <p class="field-label" style="font-size:.95rem;margin-bottom:.25rem;">معاينة الاعدادات الحالية</p>
        <p class="field-hint" style="margin-bottom:1rem;">هذا ما يستلمه الـ AI بالفعل عند كل رسالة من الإعدادات المحفوظة</p>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;margin-bottom:1rem">
            <div style="background:#f8fafc;border-radius:10px;padding:.85rem 1rem">
                <div style="font-size:.7rem;color:#94a3b8;margin-bottom:.2rem">اسم الـ AI</div>
                <div style="font-size:.88rem;font-weight:700;color:#1e293b">{{ $settings['ai_name'] ?: 'المساعد الذكي' }}</div>
            </div>
            <div style="background:#f8fafc;border-radius:10px;padding:.85rem 1rem">
                <div style="font-size:.7rem;color:#94a3b8;margin-bottom:.2rem">النبرة</div>
                <div style="font-size:.88rem;font-weight:700;color:#1e293b">
                    {{ $settings['ai_tone'] === 'formal' ? 'رسمي' : ($settings['ai_tone'] === 'friendly' ? 'ودّي' : 'محايد') }}
                </div>
            </div>
            <div style="background:#f8fafc;border-radius:10px;padding:.85rem 1rem">
                <div style="font-size:.7rem;color:#94a3b8;margin-bottom:.2rem">اللغة</div>
                <div style="font-size:.88rem;font-weight:700;color:#1e293b">
                    {{ $settings['ai_language'] === 'ar' ? 'عربي فقط' : ($settings['ai_language'] === 'en' ? 'English only' : 'حسب لغة الطالب') }}
                </div>
            </div>
            <div style="background:#f8fafc;border-radius:10px;padding:.85rem 1rem">
                <div style="font-size:.7rem;color:#94a3b8;margin-bottom:.2rem">الخطوات / الأسئلة</div>
                <div style="font-size:.88rem;font-weight:700;color:#1e293b">
                    {{ count($settings['ai_steps']) }} خطوة &nbsp;·&nbsp; {{ count($settings['ai_faqs']) }} سؤال
                </div>
            </div>
        </div>

        <div style="font-size:.8rem;font-weight:700;color:#374151;margin-bottom:.4rem">معلومات المنصة المحفوظة</div>
        <div style="background:#f8fafc;border-radius:10px;padding:1rem;font-size:.8rem;color:#475569;line-height:1.7;white-space:pre-wrap;max-height:200px;overflow-y:auto;direction:rtl">{{ $settings['ai_platform_info'] ?: '(لا توجد معلومات محفوظة)' }}</div>

        @if(!empty($settings['ai_steps']))
        <div style="margin-top:1rem;font-size:.8rem;font-weight:700;color:#374151;margin-bottom:.4rem">الخطوات المحفوظة</div>
        <div style="background:#faf5ff;border-radius:10px;padding:1rem">
            @foreach($settings['ai_steps'] as $i => $step)
            <div style="font-size:.82rem;color:#4c1d95;margin-bottom:.3rem">{{ $i+1 }}. {{ $step }}</div>
            @endforeach
        </div>
        @endif

        @if(!empty($settings['ai_faqs']))
        <div style="margin-top:1rem;font-size:.8rem;font-weight:700;color:#374151;margin-bottom:.4rem">الأسئلة والأجوبة المحفوظة</div>
        @foreach($settings['ai_faqs'] as $faq)
        <div style="background:#f0fdf4;border-radius:10px;padding:.75rem 1rem;margin-bottom:.5rem;font-size:.8rem">
            <div style="color:#166534;font-weight:700;margin-bottom:.25rem">س: {{ $faq['q'] }}</div>
            <div style="color:#374151">ج: {{ $faq['a'] }}</div>
        </div>
        @endforeach
        @endif

        @if(!empty($settings['ai_forbidden']))
        <div style="margin-top:1rem;font-size:.8rem;font-weight:700;color:#374151;margin-bottom:.4rem">الموضوعات الممنوعة</div>
        <div style="display:flex;flex-wrap:wrap;gap:.4rem">
            @foreach($settings['ai_forbidden'] as $topic)
            <span style="background:#fee2e2;color:#dc2626;border-radius:7px;padding:.25rem .7rem;font-size:.75rem;font-weight:600">{{ $topic }}</span>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Sticky save bar --}}
    <div class="save-bar">
        <span style="font-size:.8rem;color:#94a3b8">التغييرات تُطبَّق فور الحفظ على المحادثات الجديدة</span>
        <button type="submit" class="save-btn">حفظ الإعدادات</button>
    </div>

    </form>
</div>
@endsection

@push('scripts')
<script>
    // ── Tabs ───────────────────────────────────────
    function switchTab(name, btn) {
        document.querySelectorAll('.section-card').forEach(c => c.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.getElementById('tab-' + name).classList.add('active');
        btn.classList.add('active');
    }

    // ── Char counter ───────────────────────────────
    const platformInfo    = document.getElementById('platformInfo');
    const platformCounter = document.getElementById('platformCounter');
    function updateCounter() {
        const len = platformInfo.value.length;
        platformCounter.textContent = len + ' / 5000';
        platformCounter.className = 'char-counter' + (len > 4500 ? ' over' : len > 4000 ? ' warn' : '');
    }
    platformInfo.addEventListener('input', updateCounter);
    updateCounter();

    // ── Steps ──────────────────────────────────────
    function addStep() {
        const c = document.getElementById('stepsContainer');
        const div = document.createElement('div');
        div.className = 'step-row';
        div.setAttribute('data-step', '');
        div.innerHTML = `
            <div class="step-num step-counter">${c.querySelectorAll('[data-step]').length + 1}</div>
            <input type="text" name="ai_steps[]" class="field-input" style="margin:0" placeholder="اكتب الخطوة...">
            <button type="button" class="btn-remove" onclick="removeRow(this)">×</button>`;
        c.appendChild(div);
        reorderSteps();
    }
    function reorderSteps() {
        document.querySelectorAll('#stepsContainer .step-counter').forEach((el, i) => el.textContent = i + 1);
    }

    // ── FAQs ───────────────────────────────────────
    function addFaq() {
        const c = document.getElementById('faqsContainer');
        const idx = c.querySelectorAll('[data-faq]').length;
        const div = document.createElement('div');
        div.className = 'faq-row';
        div.setAttribute('data-faq', '');
        div.innerHTML = `
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.6rem">
                <span style="font-size:.75rem;font-weight:700;color:#7c3aed;">سؤال ${idx + 1}</span>
                <button type="button" class="btn-remove" onclick="removeRow(this)">×</button>
            </div>
            <label class="field-label" style="font-size:.75rem">السؤال</label>
            <input type="text" name="ai_faqs[${idx}][q]" class="field-input mb-2" placeholder="مثال: كيف أسجّل في المنصة؟">
            <label class="field-label" style="font-size:.75rem;margin-top:.4rem">الجواب</label>
            <textarea name="ai_faqs[${idx}][a]" rows="2" class="field-input" placeholder="اكتب الجواب الحرفي..."></textarea>`;
        c.appendChild(div);
    }

    // ── Forbidden ──────────────────────────────────
    function addForbidden() {
        const c = document.getElementById('forbiddenContainer');
        const div = document.createElement('div');
        div.className = 'forbidden-row';
        div.setAttribute('data-forbidden', '');
        div.innerHTML = `
            <span style="color:#ef4444;flex-shrink:0;font-size:.85rem">ممنوع</span>
            <input type="text" name="ai_forbidden[]" class="forbidden-input" placeholder="مثال: أسعار المنافسين">
            <button type="button" class="btn-remove" onclick="removeRow(this)">×</button>`;
        c.appendChild(div);
    }

    // ── Smart Links ────────────────────────────────
    function addLink(topic='', url='', label='') {
        const c = document.getElementById('linksContainer');
        // Remove empty state text if present
        c.querySelectorAll('p').forEach(p => p.remove());
        const idx = c.querySelectorAll('[data-link]').length;
        const div = document.createElement('div');
        div.className = 'link-row';
        div.setAttribute('data-link', '');
        div.style.cssText = 'border:1.5px solid #e0e7ff;border-radius:12px;padding:.85rem 1rem;margin-bottom:.6rem;background:#fafbff;';
        div.innerHTML = `
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.6rem">
                <span style="font-size:.75rem;font-weight:700;color:#4f46e5;">رابط ${idx + 1}</span>
                <button type="button" class="btn-remove" onclick="removeRow(this)">×</button>
            </div>
            <label class="field-label" style="font-size:.75rem">لما يسأل عن</label>
            <input type="text" name="ai_smart_links[${idx}][topic]" value="${topic}" class="field-input mb-2" placeholder="مثال: تذكرة دعم فني">
            <label class="field-label" style="font-size:.75rem;margin-top:.3rem">الرابط</label>
            <input type="text" name="ai_smart_links[${idx}][url]" value="${url}" class="field-input mb-2" placeholder="http://..." dir="ltr">
            <label class="field-label" style="font-size:.75rem;margin-top:.3rem">اسم الرابط (اختياري)</label>
            <input type="text" name="ai_smart_links[${idx}][label]" value="${label}" class="field-input" placeholder="مثال: إنشاء تذكرة دعم">`;
        c.appendChild(div);
    }

    function addQuickLink(topic, url, label) {
        // Check if already exists
        const existing = [...document.querySelectorAll('[data-link] input[name*="[topic]"]')]
            .find(i => i.value === topic);
        if (existing) { existing.closest('[data-link]').style.outline = '2px solid #7c3aed'; setTimeout(() => existing.closest('[data-link]').style.outline='', 1500); return; }
        addLink(topic, url, label);
    }

    // ── Remove ─────────────────────────────────────
    function removeRow(btn) {
        btn.closest('[data-step],[data-faq],[data-forbidden],[data-link]')?.remove();
        reorderSteps();
    }
</script>
@endpush
