@extends('layouts.dashboard')

@section('title', 'تعديل سؤال شائع')

@push('styles')
<style>
    .form-hero { background: linear-gradient(135deg, #0071AA 0%, #005a88 50%, #003d5c 100%); border-radius: 1.25rem; position: relative; overflow: hidden; }
    .form-hero::before { content: ''; position: absolute; inset: 0; background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E'); }
    .form-label { display: block; font-size: 0.8rem; font-weight: 600; color: #374151; margin-bottom: 0.35rem; }
    .form-input { width: 100%; border: 1.5px solid #e2e8f0; border-radius: 0.75rem; padding: 0.6rem 0.9rem; font-size: 0.875rem; background: #f8fafc; color: #1e293b; transition: border-color 0.15s, box-shadow 0.15s; outline: none; }
    .form-input:focus { border-color: #0071AA; box-shadow: 0 0 0 3px rgba(0,113,170,0.1); background: #fff; }
    .form-input.error { border-color: #f87171; }
    .section-card { background: #fff; border-radius: 1rem; border: 1px solid #e2e8f0; overflow: hidden; }
    .section-header { padding: 0.9rem 1.25rem; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; gap: 0.5rem; background: #f8fafc; }
    .section-header svg { width: 16px; height: 16px; color: #0071AA; }
    .section-header span { font-size: 0.85rem; font-weight: 700; color: #1e293b; }
    .section-body { padding: 1.25rem; }
    .btn-primary { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.65rem 1.75rem; background: #0071AA; color: #fff !important; border: none; border-radius: 0.75rem; font-size: 0.875rem; font-weight: 700; cursor: pointer; transition: background 0.15s; text-decoration: none; }
    .btn-primary:hover { background: #005a88; }
    .btn-secondary { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.65rem 1.5rem; background: #fff; color: #64748b !important; border: 1.5px solid #e2e8f0; border-radius: 0.75rem; font-size: 0.875rem; font-weight: 600; cursor: pointer; transition: all 0.15s; text-decoration: none; }
    .btn-secondary:hover { background: #f1f5f9; border-color: #cbd5e1; }
</style>
@endpush

@section('content')
<div class="p-6 max-w-4xl mx-auto">

    <div class="form-hero p-5 mb-6 text-white">
        <div class="relative z-10 flex items-center gap-4">
            <a href="{{ route('admin.faqs.index') }}"
               class="flex-shrink-0 w-9 h-9 rounded-xl bg-white bg-opacity-15 flex items-center justify-center hover:bg-opacity-25 transition-all">
                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-bold">تعديل سؤال شائع</h1>
                <p class="text-blue-200 text-xs mt-0.5">عدّل بيانات السؤال ثم احفظ التغييرات</p>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.faqs.update', $faq) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        {{-- Questions --}}
        <div class="section-card">
            <div class="section-header">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>السؤال</span>
            </div>
            <div class="section-body">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">السؤال بالعربية <span style="color:#ef4444">*</span></label>
                        <input type="text" name="question_ar" value="{{ old('question_ar', $faq->question_ar) }}"
                               class="form-input {{ $errors->has('question_ar') ? 'error' : '' }}"
                               placeholder="أدخل السؤال بالعربية">
                        @error('question_ar') <p style="color:#ef4444;font-size:0.75rem;margin-top:4px">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">السؤال بالإنجليزية</label>
                        <input type="text" name="question_en" value="{{ old('question_en', $faq->question_en) }}"
                               class="form-input"
                               placeholder="Enter question in English" dir="ltr">
                    </div>
                </div>
            </div>
        </div>

        {{-- Answers --}}
        <div class="section-card">
            <div class="section-header">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h12"/>
                </svg>
                <span>الإجابة</span>
            </div>
            <div class="section-body space-y-4">
                <div>
                    <label class="form-label">الإجابة بالعربية <span style="color:#ef4444">*</span></label>
                    <textarea name="answer_ar" rows="5"
                              class="form-input {{ $errors->has('answer_ar') ? 'error' : '' }}"
                              placeholder="اكتب الإجابة بالعربية...">{{ old('answer_ar', $faq->answer_ar) }}</textarea>
                    @error('answer_ar') <p style="color:#ef4444;font-size:0.75rem;margin-top:4px">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">الإجابة بالإنجليزية</label>
                    <textarea name="answer_en" rows="4"
                              class="form-input"
                              placeholder="Write answer in English..." dir="ltr">{{ old('answer_en', $faq->answer_en) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Settings --}}
        <div class="section-card">
            <div class="section-header">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span>الإعدادات</span>
            </div>
            <div class="section-body">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="form-label">الفئة <span style="color:#ef4444">*</span></label>
                        <select name="category" class="form-input {{ $errors->has('category') ? 'error' : '' }}">
                            @foreach($categories as $key => $cat)
                            <option value="{{ $key }}" {{ old('category', $faq->category) === $key ? 'selected' : '' }}>
                                {{ $cat['icon'] }} {{ $cat['label'] }}
                            </option>
                            @endforeach
                        </select>
                        @error('category') <p style="color:#ef4444;font-size:0.75rem;margin-top:4px">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">ترتيب العرض</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', $faq->sort_order) }}"
                               class="form-input" min="0" placeholder="0">
                    </div>
                    <div>
                        <label class="form-label">الحالة</label>
                        <select name="status" class="form-input">
                            <option value="active"   {{ old('status', $faq->status) === 'active'   ? 'selected' : '' }}>✅ نشط — يظهر في الموقع</option>
                            <option value="inactive" {{ old('status', $faq->status) === 'inactive' ? 'selected' : '' }}>🔴 غير نشط — مخفي</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="section-card">
            <div class="section-body">
                <div class="flex items-center gap-3">
                    <button type="submit" class="btn-primary">
                        <svg style="width:16px;height:16px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        حفظ التغييرات
                    </button>
                    <a href="{{ route('admin.faqs.index') }}" class="btn-secondary">
                        <svg style="width:16px;height:16px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        إلغاء
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
