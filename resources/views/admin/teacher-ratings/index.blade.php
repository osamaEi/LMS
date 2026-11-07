@extends('layouts.dashboard')

@section('title', 'تقييمات المدربين')

@php
    $starPath = 'M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z';
    $ratingColor = function ($v) {
        if ($v >= 4.5) return 'text-green-600 dark:text-green-400';
        if ($v >= 3.5) return 'text-brand-600 dark:text-brand-400';
        if ($v >= 2.5) return 'text-yellow-600 dark:text-yellow-400';
        return 'text-red-600 dark:text-red-400';
    };
    $barColor = function ($v) {
        if ($v >= 4.5) return 'bg-green-500';
        if ($v >= 3.5) return 'bg-brand-500';
        if ($v >= 2.5) return 'bg-yellow-500';
        return 'bg-red-500';
    };
    $criteria = [
        'avg_teaching'      => 'جودة التدريس',
        'avg_communication' => 'التواصل',
        'avg_punctuality'   => 'الالتزام بالمواعيد',
        'avg_delivery'      => 'إيصال المحتوى',
    ];
    $ratingCriteria = [
        'teaching_quality' => 'جودة التدريس',
        'communication'    => 'التواصل',
        'punctuality'      => 'الالتزام بالمواعيد',
        'content_delivery' => 'إيصال المحتوى',
    ];
@endphp

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">تقييمات المدربين</h2>
            <p class="text-gray-600 dark:text-gray-400">إدارة ومتابعة تقييمات المدربين من قبل المتدربين (معيار NELC 2.4.9)</p>
        </div>
        <a href="{{ route('admin.teacher-ratings.report') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-brand-500 rounded-lg hover:bg-brand-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            تقرير التقييمات
        </a>
    </div>

    @if(session('success'))
    <div class="rounded-lg bg-green-50 border border-green-200 p-4 text-green-700 dark:bg-green-900/30 dark:border-green-800 dark:text-green-300">
        {{ session('success') }}
    </div>
    @endif

    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-500 dark:text-gray-400">متوسط التقييم العام</span>
                <span class="w-10 h-10 rounded-lg bg-yellow-100 dark:bg-yellow-900/40 flex items-center justify-center">
                    <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path d="{{ $starPath }}"/></svg>
                </span>
            </div>
            <div class="mt-3 flex items-baseline gap-1">
                <span class="text-3xl font-bold {{ $stats['avg'] > 0 ? $ratingColor($stats['avg']) : 'text-gray-400' }}">{{ number_format($stats['avg'], 2) }}</span>
                <span class="text-sm text-gray-400">/ 5</span>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-500 dark:text-gray-400">التقييمات المعتمدة</span>
                <span class="w-10 h-10 rounded-lg bg-green-100 dark:bg-green-900/40 flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="mt-3 text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total']) }}</div>
        </div>

        <a href="#pending" class="bg-white dark:bg-gray-800 rounded-xl border p-5 transition hover:shadow-md {{ $stats['pending'] > 0 ? 'border-yellow-300 dark:border-yellow-700' : 'border-gray-200 dark:border-gray-700' }}">
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-500 dark:text-gray-400">بانتظار الاعتماد</span>
                <span class="w-10 h-10 rounded-lg bg-orange-100 dark:bg-orange-900/40 flex items-center justify-center">
                    <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="mt-3 text-3xl font-bold {{ $stats['pending'] > 0 ? 'text-orange-500' : 'text-gray-900 dark:text-white' }}">{{ number_format($stats['pending']) }}</div>
        </a>

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-500 dark:text-gray-400">المدربون المُقيَّمون</span>
                <span class="w-10 h-10 rounded-lg bg-brand-100 dark:bg-brand-900/40 flex items-center justify-center">
                    <svg class="w-5 h-5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </span>
            </div>
            <div class="mt-3 flex items-baseline gap-1">
                <span class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['rated_teachers'] }}</span>
                <span class="text-sm text-gray-400">من {{ $stats['teachers'] }}</span>
            </div>
        </div>
    </div>

    <!-- Pending Approvals -->
    @if($pendingRatings->count() > 0)
    <div id="pending" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden"
         x-data="{ selected: [], all: @js($pendingRatings->pluck('id')->values()), open: null,
                   toggleAll() { this.selected = this.selected.length === this.all.length ? [] : [...this.all] } }">
        <form method="POST" action="{{ route('admin.teacher-ratings.bulk') }}" x-ref="bulkForm">
            @csrf
            <input type="hidden" name="action" x-ref="bulkAction">
            <template x-for="id in selected" :key="id"><input type="hidden" name="ids[]" :value="id"></template>
        </form>

        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex flex-wrap items-center justify-between gap-3">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                <span class="flex items-center justify-center min-w-6 h-6 px-1.5 bg-orange-100 text-orange-700 rounded-full text-xs font-bold">{{ $pendingRatings->total() }}</span>
                تقييمات بانتظار الاعتماد
            </h3>
            <div class="flex items-center gap-2" x-show="selected.length" x-cloak>
                <span class="text-sm text-gray-500" x-text="`تم تحديد ${selected.length}`"></span>
                <button type="button" @click="$refs.bulkAction.value='approve'; $refs.bulkForm.submit()"
                        class="px-3 py-1.5 text-xs font-medium text-white bg-green-500 rounded-lg hover:bg-green-600">اعتماد المحدد</button>
                <button type="button" @click="if (confirm('هل أنت متأكد من رفض التقييمات المحددة؟')) { $refs.bulkAction.value='reject'; $refs.bulkForm.submit() }"
                        class="px-3 py-1.5 text-xs font-medium text-white bg-red-500 rounded-lg hover:bg-red-600">رفض المحدد</button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 w-10">
                            <input type="checkbox" class="rounded border-gray-300 text-brand-500"
                                   :checked="selected.length === all.length" @change="toggleAll()">
                        </th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400">المتدرب</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400">المدرب</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400">المقرر</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400">التقييم</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400">التاريخ</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($pendingRatings as $rating)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50" :class="selected.includes({{ $rating->id }}) && 'bg-brand-50/50 dark:bg-brand-900/20'">
                        <td class="px-4 py-4">
                            <input type="checkbox" value="{{ $rating->id }}" x-model.number="selected" class="rounded border-gray-300 text-brand-500">
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-2">
                                <span class="font-medium text-gray-900 dark:text-white">{{ $rating->student->name ?? 'غير معروف' }}</span>
                                @if($rating->is_anonymous)
                                <span class="px-1.5 py-0.5 text-[10px] rounded bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">مجهول</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-4 text-gray-900 dark:text-white">{{ $rating->teacher->name ?? 'غير معروف' }}</td>
                        <td class="px-4 py-4 text-gray-600 dark:text-gray-400">{{ $rating->subject->name ?? '-' }}</td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-0.5">
                                @for($i = 1; $i <= 5; $i++)
                                <svg class="w-4 h-4 {{ $i <= $rating->overall_rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20"><path d="{{ $starPath }}"/></svg>
                                @endfor
                                <span class="mr-1 text-sm font-medium {{ $ratingColor($rating->overall_rating) }}">{{ $rating->overall_rating }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap" title="{{ $rating->created_at->format('Y-m-d H:i') }}">
                            {{ $rating->created_at->diffForHumans() }}
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-2">
                                <button type="button" @click="open = open === {{ $rating->id }} ? null : {{ $rating->id }}"
                                        class="px-2.5 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200">
                                    <span x-text="open === {{ $rating->id }} ? 'إخفاء' : 'التفاصيل'"></span>
                                </button>
                                <form action="{{ route('admin.teacher-ratings.approve', $rating) }}" method="POST">
                                    @csrf
                                    <button type="submit" title="اعتماد" class="p-1.5 text-white bg-green-500 rounded-lg hover:bg-green-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    </button>
                                </form>
                                <form action="{{ route('admin.teacher-ratings.reject', $rating) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من رفض هذا التقييم؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="رفض" class="p-1.5 text-white bg-red-500 rounded-lg hover:bg-red-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <tr x-show="open === {{ $rating->id }}" x-cloak class="bg-gray-50 dark:bg-gray-900/40">
                        <td colspan="7" class="px-6 py-4">
                            <div class="grid md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    @foreach($ratingCriteria as $field => $label)
                                    <div class="flex items-center gap-3 text-sm">
                                        <span class="w-32 text-gray-600 dark:text-gray-400">{{ $label }}</span>
                                        <div class="flex-1 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                            <div class="h-full {{ $barColor($rating->$field) }}" style="width: {{ $rating->$field * 20 }}%"></div>
                                        </div>
                                        <span class="w-6 text-left font-medium text-gray-900 dark:text-white">{{ $rating->$field }}</span>
                                    </div>
                                    @endforeach
                                </div>
                                <div class="space-y-3 text-sm">
                                    <div>
                                        <div class="font-medium text-gray-700 dark:text-gray-300 mb-1">التعليق</div>
                                        <p class="text-gray-600 dark:text-gray-400 whitespace-pre-line">{{ $rating->comment ?: '—' }}</p>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-700 dark:text-gray-300 mb-1">مقترحات التحسين</div>
                                        <p class="text-gray-600 dark:text-gray-400 whitespace-pre-line">{{ $rating->improvement_suggestions ?: '—' }}</p>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($pendingRatings->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $pendingRatings->links() }}
        </div>
        @endif
    </div>
    @endif

    <!-- Teachers List -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex flex-wrap items-center justify-between gap-3">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">ترتيب المدربين حسب التقييم</h3>
            <form method="GET" class="flex flex-wrap items-center gap-2">
                <div class="relative">
                    <input type="text" name="search" value="{{ $search }}" placeholder="بحث بالاسم أو البريد..."
                           class="w-56 pr-9 pl-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-brand-500 focus:border-brand-500">
                    <svg class="w-4 h-4 text-gray-400 absolute right-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <select name="sort" onchange="this.form.submit()"
                        class="py-2 pl-8 pr-3 text-sm rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="rating" @selected($sort === 'rating')>الأعلى تقييماً</option>
                    <option value="count" @selected($sort === 'count')>الأكثر تقييمات</option>
                    <option value="name" @selected($sort === 'name')>الاسم</option>
                </select>
                <button type="submit" class="px-3 py-2 text-sm font-medium text-white bg-brand-500 rounded-lg hover:bg-brand-600">بحث</button>
                @if($search !== '' || $sort !== 'rating')
                <a href="{{ route('admin.teacher-ratings.index') }}" class="px-3 py-2 text-sm text-gray-600 dark:text-gray-300 hover:underline">مسح</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 w-12">#</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400">المدرب</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400">متوسط التقييم</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 hidden lg:table-cell">تفاصيل المعايير</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400">عدد التقييمات</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($teachers as $teacher)
                    @php
                        $rank = $teachers->firstItem() + $loop->index;
                        $avg = (float) $teacher->avg_rating;
                    @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-6 py-4">
                            @if($sort === 'rating' && $avg > 0 && $rank <= 3)
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full text-xs font-bold
                                {{ $rank === 1 ? 'bg-yellow-100 text-yellow-700' : ($rank === 2 ? 'bg-gray-200 text-gray-700' : 'bg-orange-100 text-orange-700') }}">{{ $rank }}</span>
                            @else
                            <span class="text-sm text-gray-500 dark:text-gray-400 pr-2">{{ $rank }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($teacher->name) }}&background=6366f1&color=fff"
                                     class="w-10 h-10 rounded-full" alt="" loading="lazy">
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $teacher->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $teacher->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($avg > 0)
                            <div class="flex items-center gap-2">
                                <div class="flex items-center gap-0.5">
                                    @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 {{ $i <= round($avg) ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20"><path d="{{ $starPath }}"/></svg>
                                    @endfor
                                </div>
                                <span class="text-sm font-bold {{ $ratingColor($avg) }}">{{ number_format($avg, 2) }}</span>
                            </div>
                            @else
                            <span class="text-sm text-gray-400">لا توجد تقييمات</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 hidden lg:table-cell">
                            @if($avg > 0)
                            <div class="grid grid-cols-2 gap-x-4 gap-y-1 min-w-[260px]">
                                @foreach($criteria as $field => $label)
                                @php $v = (float) $teacher->$field; @endphp
                                <div class="flex items-center gap-2 text-xs">
                                    <span class="w-24 text-gray-500 dark:text-gray-400 truncate">{{ $label }}</span>
                                    <div class="flex-1 h-1.5 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                        <div class="h-full {{ $barColor($v) }}" style="width: {{ $v * 20 }}%"></div>
                                    </div>
                                    <span class="w-6 text-gray-700 dark:text-gray-300">{{ number_format($v, 1) }}</span>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <span class="text-sm text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-1 items-start">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    {{ $teacher->ratings_count }} تقييم
                                </span>
                                @if($teacher->pending_count > 0)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-300">
                                    {{ $teacher->pending_count }} معلق
                                </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-left">
                            <a href="{{ route('admin.teacher-ratings.show', $teacher) }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-brand-600 bg-brand-50 rounded-lg hover:bg-brand-100 dark:bg-brand-900 dark:text-brand-200">
                                عرض التفاصيل
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <p class="mt-2">{{ $search !== '' ? 'لا توجد نتائج مطابقة للبحث' : 'لا يوجد مدربون مسجلون' }}</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($teachers->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $teachers->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
