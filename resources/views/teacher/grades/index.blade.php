@extends('layouts.dashboard')

@section('title', 'إدارة الدرجات')

@push('styles')
<style>
    .grades-page { max-width: 1200px; margin: 0 auto; }

    /* Header */
    .grades-header {
        background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);
        border-radius: 24px;
        padding: 2rem 2.5rem;
        color: #fff;
        position: relative;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }
    .grades-header::before {
        content: '';
        position: absolute;
        top: -40%;
        left: -10%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
        border-radius: 50%;
    }
    .grades-header::after {
        content: '';
        position: absolute;
        bottom: -50%;
        right: -5%;
        width: 250px;
        height: 250px;
        background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 70%);
        border-radius: 50%;
    }

    /* Card */
    .grades-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.06);
        overflow: hidden;
    }
    .dark .grades-card { background: #1f2937; }

    .grades-card-head {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .dark .grades-card-head { border-color: #374151; }

    .grades-card-title {
        font-size: 1rem;
        font-weight: 800;
        color: #111827;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .dark .grades-card-title { color: #f9fafb; }

    .grades-card-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
    }
    .empty-icon {
        width: 100px;
        height: 100px;
        border-radius: 28px;
        background: linear-gradient(135deg, #f3e8ff, #ede9fe);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
    }
    .empty-icon svg {
        width: 48px;
        height: 48px;
        color: #8b5cf6;
    }
    .empty-title {
        font-size: 1.25rem;
        font-weight: 800;
        color: #111827;
        margin-bottom: 0.5rem;
    }
    .dark .empty-title { color: #f9fafb; }
    .empty-desc {
        font-size: 0.9rem;
        color: #6b7280;
        max-width: 400px;
        margin: 0 auto;
        line-height: 1.6;
    }
    .dark .empty-desc { color: #9ca3af; }

    /* Subject List */
    .subject-item {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #f8fafc;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: background 0.15s;
    }
    .dark .subject-item { border-color: #1f2937; }
    .subject-item:hover { background: #f8fafc; }
    .dark .subject-item:hover { background: #111827; }
    .subject-item:last-child { border-bottom: none; }

    .subject-color {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .subject-info { flex: 1; min-width: 0; }
    .subject-name {
        font-weight: 700;
        font-size: 0.95rem;
        color: #111827;
        margin-bottom: 0.25rem;
    }
    .dark .subject-name { color: #f9fafb; }
    .subject-meta {
        font-size: 0.8rem;
        color: #6b7280;
    }
    .dark .subject-meta { color: #9ca3af; }

    .subject-btn {
        padding: 0.6rem 1.25rem;
        border-radius: 10px;
        font-size: 0.85rem;
        font-weight: 700;
        text-decoration: none;
        transition: opacity 0.15s;
        background: linear-gradient(135deg, #8b5cf6, #6d28d9);
        color: #fff;
    }
    .subject-btn:hover { opacity: 0.9; color: #fff; }
</style>
@endpush

@section('content')
<div class="grades-page space-y-6">
    <!-- Header -->
    <div class="grades-header">
        <div class="flex items-center gap-4 relative z-10">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center" style="background: rgba(255,255,255,0.15);">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-extrabold">إدارة الدرجات</h1>
                <p class="opacity-75 text-sm mt-0.5">إضافة وتعديل درجات الطلاب</p>
            </div>
        </div>
    </div>

    <!-- Subjects List -->
    <div class="grades-card">
        <div class="grades-card-head">
            <div class="grades-card-title">
                <div class="grades-card-icon" style="background: linear-gradient(135deg, #8b5cf6, #6d28d9);">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                المواد الدراسية
            </div>
        </div>

        <!-- Empty State -->
        <div class="empty-state">
            <div class="empty-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
            </div>
            <h3 class="empty-title">إدارة الدرجات قريباً</h3>
            <p class="empty-desc">
                سيتم تفعيل نظام الدرجات قريباً. ستتمكن من إضافة وتعديل درجات طلابك في كل مادة.
            </p>
        </div>

        {{--
        <!-- Subject Items - Uncomment when data is available -->
        <div class="subject-item">
            <div class="subject-color" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <div class="subject-info">
                <div class="subject-name">أساسيات البرمجة</div>
                <div class="subject-meta">25 طالب مسجل</div>
            </div>
            <a href="#" class="subject-btn">
                إدارة الدرجات
            </a>
        </div>
        --}}
    </div>

    <!-- Help Card -->
    <div class="grades-card">
        <div class="grades-card-head">
            <div class="grades-card-title">
                <div class="grades-card-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                إرشادات
            </div>
        </div>
        <div class="p-6">
            <div class="space-y-4 text-sm text-gray-600 dark:text-gray-400">
                <div class="flex items-start gap-3">
                    <div class="w-6 h-6 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <p>اختر المادة لإضافة أو تعديل درجات الطلاب</p>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-6 h-6 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <p>يمكنك تصدير الدرجات إلى ملف Excel</p>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-6 h-6 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <p>سيتم إشعار الطلاب عند إضافة درجات جديدة</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
