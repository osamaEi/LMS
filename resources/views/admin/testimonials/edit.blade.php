@extends('layouts.dashboard')
@section('title', 'تعديل تقييم')

@section('content')
<div class="p-6 max-w-2xl mx-auto">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.testimonials.index') }}"
           class="text-gray-400 hover:text-gray-600 transition-colors" style="text-decoration:none;">
            <i class="bi bi-arrow-right-circle-fill text-2xl"></i>
        </a>
        <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">تعديل تقييم — {{ $testimonial->author_ar }}</h1>
    </div>

    @if($errors->any())
    <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.testimonials.update', $testimonial) }}" method="POST"
          class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-5">
        @csrf @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-200 mb-1">اسم المتدرب (عربي) *</label>
                <input type="text" name="author_ar" value="{{ old('author_ar', $testimonial->author_ar) }}" required
                       class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 dark:bg-gray-700 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-200 mb-1">اسم المتدرب (إنجليزي)</label>
                <input type="text" name="author_en" value="{{ old('author_en', $testimonial->author_en) }}"
                       class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 dark:bg-gray-700 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-200 mb-1">التخصص / البرنامج (عربي)</label>
                <input type="text" name="role_ar" value="{{ old('role_ar', $testimonial->role_ar) }}"
                       class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 dark:bg-gray-700 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-200 mb-1">التخصص / البرنامج (إنجليزي)</label>
                <input type="text" name="role_en" value="{{ old('role_en', $testimonial->role_en) }}"
                       class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 dark:bg-gray-700 dark:text-white">
            </div>
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 dark:text-gray-200 mb-1">نص التقييم (عربي) *</label>
            <textarea name="text_ar" rows="4" required maxlength="600"
                      class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 dark:bg-gray-700 dark:text-white resize-none">{{ old('text_ar', $testimonial->text_ar) }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 dark:text-gray-200 mb-1">نص التقييم (إنجليزي)</label>
            <textarea name="text_en" rows="4" maxlength="600"
                      class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 dark:bg-gray-700 dark:text-white resize-none">{{ old('text_en', $testimonial->text_en) }}</textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-200 mb-1">التقييم ⭐</label>
                <select name="rating"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 dark:bg-gray-700 dark:text-white">
                    @for($i=5;$i>=1;$i--)
                    <option value="{{ $i }}" {{ old('rating',$testimonial->rating) == $i ? 'selected' : '' }}>{{ $i }} نجوم</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-200 mb-1">الترتيب</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $testimonial->sort_order) }}" min="0"
                       class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 dark:bg-gray-700 dark:text-white">
            </div>
            <div class="flex flex-col justify-end">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1"
                           {{ old('is_active', $testimonial->is_active) ? 'checked' : '' }}
                           class="w-5 h-5 rounded accent-blue-600">
                    <span class="text-sm font-bold text-gray-700 dark:text-gray-200">نشط (يظهر في الموقع)</span>
                </label>
            </div>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit"
                    class="px-6 py-2.5 rounded-xl font-bold text-white text-sm"
                    style="background:#0071AA;border:none;cursor:pointer;">
                <i class="bi bi-check-circle-fill me-1"></i> حفظ التغييرات
            </button>
            <a href="{{ route('admin.testimonials.index') }}"
               class="px-6 py-2.5 rounded-xl font-bold text-sm border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300"
               style="text-decoration:none;">إلغاء</a>
        </div>
    </form>
</div>
@endsection
