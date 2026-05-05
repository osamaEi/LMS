@extends('layouts.dashboard')
@section('title', 'إعدادات الفوتر')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">إعدادات الفوتر</h1>
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">تحكم في روابط، وسائل التواصل، ومعلومات التذييل</p>
</div>

@if(session('success'))
<div class="mb-4 rounded-lg bg-green-50 border border-green-200 p-4 text-sm text-green-700">{{ session('success') }}</div>
@endif

<form action="{{ route('admin.footer-settings.update') }}" method="POST">
@csrf
@method('PUT')

{{-- ══ معلومات التواصل ══ --}}
<div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 mb-6">
    <div class="p-4 border-b border-gray-200 dark:border-gray-800" style="background:linear-gradient(135deg,#0071aa,#005a8a);border-radius:12px 12px 0 0;">
        <h2 class="text-base font-bold text-white flex items-center gap-2">
            <i class="bi bi-telephone-fill"></i> معلومات التواصل
        </h2>
    </div>
    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">رقم الهاتف</label>
            <input type="text" name="phone" value="{{ $settings['phone'] ?? '' }}"
                   class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                   placeholder="9200343222" dir="ltr">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">البريد الإلكتروني</label>
            <input type="email" name="email" value="{{ $settings['email'] ?? '' }}"
                   class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                   placeholder="help@alertiqa.edu.sa" dir="ltr">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">العنوان (عربي)</label>
            <input type="text" name="address_ar" value="{{ $settings['address_ar'] ?? '' }}"
                   class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">العنوان (إنجليزي)</label>
            <input type="text" name="address_en" value="{{ $settings['address_en'] ?? '' }}"
                   class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white" dir="ltr">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">أوقات العمل (عربي)</label>
            <input type="text" name="working_hours_ar" value="{{ $settings['working_hours_ar'] ?? '' }}"
                   class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">أوقات العمل (إنجليزي)</label>
            <input type="text" name="working_hours_en" value="{{ $settings['working_hours_en'] ?? '' }}"
                   class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white" dir="ltr">
        </div>
    </div>
</div>

{{-- ══ وصف الفوتر ══ --}}
<div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 mb-6">
    <div class="p-4 border-b border-gray-200 dark:border-gray-800" style="background:linear-gradient(135deg,#374151,#1f2937);border-radius:12px 12px 0 0;">
        <h2 class="text-base font-bold text-white flex items-center gap-2">
            <i class="bi bi-text-paragraph"></i> نص الفوتر والحقوق
        </h2>
    </div>
    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">وصف المعهد (عربي)</label>
            <textarea name="footer_description_ar" rows="3"
                      class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">{{ $settings['footer_description_ar'] ?? '' }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">وصف المعهد (إنجليزي)</label>
            <textarea name="footer_description_en" rows="3" dir="ltr"
                      class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">{{ $settings['footer_description_en'] ?? '' }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">نص حقوق النشر (عربي)</label>
            <input type="text" name="copyright_ar" value="{{ $settings['copyright_ar'] ?? '' }}"
                   class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">نص حقوق النشر (إنجليزي)</label>
            <input type="text" name="copyright_en" value="{{ $settings['copyright_en'] ?? '' }}"
                   class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white" dir="ltr">
        </div>
    </div>
</div>

{{-- ══ وسائل التواصل الاجتماعي ══ --}}
<div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 mb-6">
    <div class="p-4 border-b border-gray-200 dark:border-gray-800" style="background:linear-gradient(135deg,#7c3aed,#5b21b6);border-radius:12px 12px 0 0;">
        <h2 class="text-base font-bold text-white flex items-center gap-2">
            <i class="bi bi-share-fill"></i> وسائل التواصل الاجتماعي
        </h2>
    </div>
    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
        @foreach([
            ['key'=>'social_twitter',   'label'=>'تويتر / X',    'icon'=>'bi-twitter-x',    'placeholder'=>'https://x.com/...'],
            ['key'=>'social_instagram', 'label'=>'إنستقرام',      'icon'=>'bi-instagram',    'placeholder'=>'https://instagram.com/...'],
            ['key'=>'social_linkedin',  'label'=>'لينكد إن',      'icon'=>'bi-linkedin',     'placeholder'=>'https://linkedin.com/...'],
            ['key'=>'social_youtube',   'label'=>'يوتيوب',        'icon'=>'bi-youtube',      'placeholder'=>'https://youtube.com/...'],
            ['key'=>'social_facebook',  'label'=>'فيسبوك',        'icon'=>'bi-facebook',     'placeholder'=>'https://facebook.com/...'],
            ['key'=>'social_snapchat',  'label'=>'سناب شات',      'icon'=>'bi-snapchat',     'placeholder'=>'https://snapchat.com/...'],
            ['key'=>'social_whatsapp',  'label'=>'واتساب',        'icon'=>'bi-whatsapp',     'placeholder'=>'https://wa.me/...'],
        ] as $social)
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                <i class="bi {{ $social['icon'] }} ml-1"></i> {{ $social['label'] }}
            </label>
            <input type="url" name="{{ $social['key'] }}" value="{{ $settings[$social['key']] ?? '' }}"
                   class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                   placeholder="{{ $social['placeholder'] }}" dir="ltr">
        </div>
        @endforeach
    </div>
</div>

<div class="flex justify-end mb-8">
    <button type="submit" class="rounded-lg px-8 py-2.5 text-sm font-bold text-white"
            style="background:linear-gradient(135deg,#0071aa,#005a8a);">
        <i class="bi bi-save ml-1"></i> حفظ الإعدادات
    </button>
</div>
</form>

{{-- ══ روابط الفوتر ══ --}}
<div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 mb-6">
    <div class="p-4 border-b border-gray-200 dark:border-gray-800" style="background:linear-gradient(135deg,#059669,#047857);border-radius:12px 12px 0 0;">
        <h2 class="text-base font-bold text-white flex items-center gap-2">
            <i class="bi bi-link-45deg"></i> روابط الفوتر
        </h2>
    </div>
    <div class="p-6">

        {{-- Add link form --}}
        <form action="{{ route('admin.footer-settings.links.store') }}" method="POST"
              class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-6 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
            @csrf
            <div class="col-span-2 md:col-span-1">
                <input type="text" name="label_ar" placeholder="الاسم (عربي)" required
                       class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white">
            </div>
            <div class="col-span-2 md:col-span-1">
                <input type="text" name="label_en" placeholder="Name (English)" dir="ltr"
                       class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white">
            </div>
            <div class="col-span-2 md:col-span-1">
                <input type="text" name="url" placeholder="/page/slug أو رابط" required dir="ltr"
                       class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white">
            </div>
            <div>
                <select name="section" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="quick">روابط سريعة</option>
                    <option value="services">الخدمات</option>
                </select>
            </div>
            <div>
                <button type="submit" class="w-full rounded-lg px-4 py-2 text-sm font-bold text-white"
                        style="background:#059669;">+ إضافة</button>
            </div>
        </form>

        {{-- Quick Links --}}
        <div class="mb-6">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                <span class="inline-block w-3 h-3 rounded-full bg-blue-500"></span> الروابط السريعة
            </h3>
            <div class="divide-y divide-gray-100 dark:divide-gray-700 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                @forelse($quickLinks as $link)
                <div class="flex items-center justify-between px-4 py-3 bg-white dark:bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800">
                    <div class="flex items-center gap-3">
                        <span class="font-medium text-sm text-gray-900 dark:text-white">{{ $link->label_ar }}</span>
                        @if($link->label_en)
                        <span class="text-xs text-gray-400">({{ $link->label_en }})</span>
                        @endif
                        <span class="text-xs text-gray-400 font-mono" dir="ltr">{{ $link->url }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-block px-2 py-0.5 text-xs rounded-full {{ $link->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $link->is_active ? 'نشط' : 'مخفي' }}
                        </span>
                        <form action="{{ route('admin.footer-settings.links.toggle', $link) }}" method="POST" class="inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="text-xs text-blue-500 hover:text-blue-700">تبديل</button>
                        </form>
                        <form action="{{ route('admin.footer-settings.links.destroy', $link) }}" method="POST" class="inline"
                              onsubmit="return confirm('حذف هذا الرابط؟')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-red-500 hover:text-red-700">حذف</button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="px-4 py-6 text-center text-sm text-gray-400">لا توجد روابط سريعة</div>
                @endforelse
            </div>
        </div>

        {{-- Services Links --}}
        <div>
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                <span class="inline-block w-3 h-3 rounded-full bg-green-500"></span> روابط الخدمات
            </h3>
            <div class="divide-y divide-gray-100 dark:divide-gray-700 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                @forelse($serviceLinks as $link)
                <div class="flex items-center justify-between px-4 py-3 bg-white dark:bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800">
                    <div class="flex items-center gap-3">
                        <span class="font-medium text-sm text-gray-900 dark:text-white">{{ $link->label_ar }}</span>
                        @if($link->label_en)
                        <span class="text-xs text-gray-400">({{ $link->label_en }})</span>
                        @endif
                        <span class="text-xs text-gray-400 font-mono" dir="ltr">{{ $link->url }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-block px-2 py-0.5 text-xs rounded-full {{ $link->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $link->is_active ? 'نشط' : 'مخفي' }}
                        </span>
                        <form action="{{ route('admin.footer-settings.links.toggle', $link) }}" method="POST" class="inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="text-xs text-blue-500 hover:text-blue-700">تبديل</button>
                        </form>
                        <form action="{{ route('admin.footer-settings.links.destroy', $link) }}" method="POST" class="inline"
                              onsubmit="return confirm('حذف هذا الرابط؟')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-red-500 hover:text-red-700">حذف</button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="px-4 py-6 text-center text-sm text-gray-400">لا توجد روابط خدمات</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
