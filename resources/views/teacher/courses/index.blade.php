@extends('layouts.dashboard')

@section('title', 'دوراتي')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">دوراتي</h1>
    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">عرض وإدارة الدورات وبرامج الإنجليزي المسندة إليك</p>
</div>

@if(session('success'))
<div class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-600 dark:bg-green-900 dark:text-green-200">
    {{ session('success') }}
</div>
@endif

<form method="GET" action="{{ route('teacher.my-courses.index') }}" class="mb-6 flex flex-wrap items-end gap-3">
    <div class="w-full sm:w-64">
        <label for="type" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">النوع</label>
        <select id="type" name="type" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
            <option value="">كل الأنواع</option>
            <option value="course" @selected($selectedType === 'course')>دورة</option>
            <option value="english" @selected($selectedType === 'english')>إنجليزي</option>
        </select>
    </div>
    <button type="submit" class="rounded-lg px-4 py-2 text-sm font-medium text-white" style="background-color: #0071AA;">تصفية</button>
    @if($selectedType !== null)
        <a href="{{ route('teacher.my-courses.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-600 dark:border-gray-700 dark:text-gray-300">إلغاء الفلتر</a>
    @endif
</form>

<div class="overflow-x-auto rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
    <table class="w-full text-start text-sm">
        <caption class="sr-only">الدورات وبرامج الإنجليزي المسندة إليك</caption>
        <thead class="bg-gray-50 text-gray-600 dark:bg-gray-800 dark:text-gray-300">
            <tr>
                <th scope="col" class="px-5 py-4 text-start">الدورة / البرنامج</th>
                <th scope="col" class="px-5 py-4 text-start">النوع</th>
                <th scope="col" class="px-5 py-4 text-center">المحاضرات</th>
                <th scope="col" class="px-5 py-4 text-center">المتدربون</th>
                <th scope="col" class="px-5 py-4 text-center">الحالة</th>
                <th scope="col" class="px-5 py-4 text-center">الإجراءات</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
            @forelse($programs as $program)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                    <th scope="row" class="px-5 py-4 text-start">
                        <a href="{{ route('teacher.my-courses.show', $program->id) }}" class="font-semibold text-gray-900 hover:underline dark:text-white">{{ $program->name_ar }}</a>
                        @if($program->name_en)
                            <div class="mt-1 text-xs font-normal text-gray-500 dark:text-gray-400">{{ $program->name_en }}</div>
                        @endif
                    </th>
                    <td class="px-5 py-4 text-gray-600 dark:text-gray-400">{{ $program->type === 'english' ? 'إنجليزي' : 'دورة' }}</td>
                    <td class="px-5 py-4 text-center text-gray-600 dark:text-gray-400">{{ $program->sessions_count }}</td>
                    <td class="px-5 py-4 text-center text-gray-600 dark:text-gray-400">{{ $program->class_student_count }}</td>
                    <td class="px-5 py-4 text-center whitespace-nowrap">
                        @if($program->status === 'active')
                            <span class="rounded-full bg-success-50 px-3 py-1 text-xs font-medium text-success-600 dark:bg-success-900 dark:text-success-200">نشط</span>
                        @elseif($program->status === 'completed')
                            <span class="rounded-full bg-gray-50 px-3 py-1 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400">مكتمل</span>
                        @else
                            <span class="rounded-full bg-yellow-50 px-3 py-1 text-xs font-medium text-yellow-600 dark:bg-yellow-900 dark:text-yellow-200">غير نشط</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-center whitespace-nowrap">
                        <a href="{{ route('teacher.my-courses.show', $program->id) }}" class="inline-flex rounded-lg px-4 py-2 text-sm font-medium text-white" style="background-color: #0071AA;">عرض الدورة والمحاضرات</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-5 py-12 text-center">
                        <p class="font-medium text-gray-900 dark:text-white">{{ $selectedType !== null ? 'لا توجد برامج مسندة إليك من هذا النوع' : 'لا توجد دورات مسندة إليك' }}</p>
                        <p class="mt-1 text-gray-500 dark:text-gray-400">{{ $selectedType !== null ? 'اختر نوعًا آخر أو ألغِ الفلتر لعرض كل البرامج.' : 'تواصل مع الإدارة لإسناد دورات.' }}</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
