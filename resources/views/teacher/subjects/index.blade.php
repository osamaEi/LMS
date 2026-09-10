@extends('layouts.dashboard')

@section('title', 'مقرراتي')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">مقرراتي</h1>
    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">عرض وإدارة المقررات التدريبية المسندة إليك</p>
</div>

@if(session('success'))
<div class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-600 dark:bg-green-900 dark:text-green-200">
    {{ session('success') }}
</div>
@endif

<form method="GET" action="{{ route('teacher.my-subjects.index') }}" class="mb-6 flex flex-wrap items-end gap-3">
    <div class="w-full sm:w-64">
        <label for="class_id" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">الكلاس</label>
        <select id="class_id" name="class_id" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
            <option value="">كل الكلاسات</option>
            @foreach($classes as $class)
                <option value="{{ $class->id }}" @selected((string) $selectedClassId === (string) $class->id)>{{ $class->name }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="rounded-lg px-4 py-2 text-sm font-medium text-white" style="background-color: #0071AA;">تصفية</button>
    @if($selectedClassId !== null)
        <a href="{{ route('teacher.my-subjects.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-600 dark:border-gray-700 dark:text-gray-300">إلغاء الفلتر</a>
    @endif
</form>

<div class="overflow-x-auto rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
    <table class="w-full text-start text-sm">
        <caption class="sr-only">المقررات المسندة إليك والكلاسات المرتبطة بها</caption>
        <thead class="bg-gray-50 text-gray-600 dark:bg-gray-800 dark:text-gray-300">
            <tr>
                <th scope="col" class="px-5 py-4 text-start">المادة</th>
                <th scope="col" class="px-5 py-4 text-start">الكلاسات</th>
                <th scope="col" class="px-5 py-4 text-start">البرنامج / الفصل الدراسي</th>
                <th scope="col" class="px-5 py-4 text-center">المحاضرات</th>
                <th scope="col" class="px-5 py-4 text-center">الحالة</th>
                <th scope="col" class="px-5 py-4 text-center">الإجراءات</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
            @forelse($subjects as $subject)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                    <th scope="row" class="px-5 py-4 text-start">
                        <a href="{{ route('teacher.my-subjects.show', $subject->id) }}" class="font-semibold text-gray-900 hover:underline dark:text-white">{{ $subject->name }}</a>
                        <div class="mt-1 text-xs font-normal text-gray-500 dark:text-gray-400">{{ $subject->code }}</div>
                    </th>
                    <td class="px-5 py-4">
                        <div class="flex flex-wrap gap-2">
                            @forelse($classes->only($subjectClassIds[$subject->id]->all()) as $class)
                                <span class="rounded-lg bg-gray-100 px-2 py-1 text-xs font-medium text-gray-700 dark:bg-gray-800 dark:text-gray-300">{{ $class->name }}</span>
                            @empty
                                <span class="text-gray-500 dark:text-gray-400">غير مرتبط بكلاس</span>
                            @endforelse
                        </div>
                    </td>
                    <td class="px-5 py-4 text-gray-600 dark:text-gray-400">
                        <div>{{ $subject->program?->name ?? $subject->term?->program?->name ?? '-' }}</div>
                        <div class="mt-1 text-xs">{{ $subject->term?->name ?? '-' }}</div>
                    </td>
                    <td class="px-5 py-4 text-center text-gray-600 dark:text-gray-400">{{ $subject->sessions_count }}</td>
                    <td class="px-5 py-4 text-center whitespace-nowrap">
                        @if($subject->status === 'active')
                            <span class="rounded-full bg-success-50 px-3 py-1 text-xs font-medium text-success-600 dark:bg-success-900 dark:text-success-200">نشط</span>
                        @elseif($subject->status === 'completed')
                            <span class="rounded-full bg-gray-50 px-3 py-1 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400">مكتمل</span>
                        @else
                            <span class="rounded-full bg-yellow-50 px-3 py-1 text-xs font-medium text-yellow-600 dark:bg-yellow-900 dark:text-yellow-200">غير نشط</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-center whitespace-nowrap">
                        <a href="{{ route('teacher.my-subjects.show', $subject->id) }}" class="inline-flex rounded-lg px-4 py-2 text-sm font-medium text-white" style="background-color: #0071AA;">عرض المقرر والمحاضرات</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-5 py-12 text-center">
                        <p class="font-medium text-gray-900 dark:text-white">{{ $selectedClassId !== null ? 'لا توجد مقررات مسندة إليك في هذا الكلاس' : 'لا توجد مقررات مسندة إليك' }}</p>
                        <p class="mt-1 text-gray-500 dark:text-gray-400">{{ $selectedClassId !== null ? 'اختر كلاسًا آخر أو ألغِ الفلتر لعرض كل المقررات.' : 'تواصل مع الإدارة لإسناد مقررات دراسية.' }}</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
