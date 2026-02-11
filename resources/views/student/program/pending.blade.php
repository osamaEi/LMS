@extends('layouts.dashboard')

@section('title', 'طلب التسجيل في البرنامج - في انتظار الموافقة')

@push('styles')
<style>
    /* Professional Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
            opacity: 1;
        }
        50% {
            transform: scale(1.05);
            opacity: 0.9;
        }
    }

    @keyframes slideProgress {
        from {
            transform: scaleX(0);
            opacity: 0;
        }
        to {
            transform: scaleX(1);
            opacity: 1;
        }
    }

    @keyframes shimmer {
        0% {
            background-position: -200% center;
        }
        100% {
            background-position: 200% center;
        }
    }

    /* Content Animation */
    .animate-fade-in {
        animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .animate-fade-delay-1 {
        animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) 0.15s both;
    }

    .animate-fade-delay-2 {
        animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) 0.3s both;
    }

    .animate-fade-delay-3 {
        animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) 0.45s both;
    }

    /* Progress Animation */
    .progress-line {
        transform-origin: right center;
        animation: slideProgress 1.2s cubic-bezier(0.16, 1, 0.3, 1) 0.5s both;
    }

    /* Professional Card Hover */
    .card-professional {
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .card-professional:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 20px 40px -15px rgba(59, 130, 246, 0.3);
    }

    /* Step Circle Enhancement */
    .step-circle {
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
    }

    .step-circle::after {
        content: '';
        position: absolute;
        inset: -4px;
        border-radius: 50%;
        opacity: 0;
        transition: opacity 0.4s;
    }

    .step-circle:hover {
        transform: scale(1.15);
    }

    .step-circle:hover::after {
        opacity: 1;
        box-shadow: 0 0 0 4px currentColor;
    }

    /* Active Step Pulse */
    .step-active {
        animation: pulse 2.5s ease-in-out infinite;
    }

    /* Gradient Text */
    .text-gradient-impressive {
        background: linear-gradient(135deg, #1e40af 0%, #3b82f6 25%, #60a5fa 50%, #3b82f6 75%, #1e40af 100%);
        background-size: 200% auto;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        animation: shimmer 3s linear infinite;
    }

    /* Button Enhancement */
    .btn-impressive {
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
        background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);
    }

    .btn-impressive::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.5s;
    }

    .btn-impressive:hover::before {
        left: 100%;
    }

    .btn-impressive:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.5);
    }

    /* Info Box Glow */
    .info-glow {
        transition: all 0.3s ease;
        position: relative;
    }

    .info-glow::before {
        content: '';
        position: absolute;
        inset: -2px;
        border-radius: inherit;
        background: linear-gradient(135deg, #3b82f6, #8b5cf6);
        opacity: 0;
        z-index: -1;
        transition: opacity 0.3s;
    }

    .info-glow:hover::before {
        opacity: 0.2;
    }

    /* Status Badge Shine */
    .status-shine {
        position: relative;
        overflow: hidden;
    }

    .status-shine::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        bottom: -50%;
        left: -50%;
        background: linear-gradient(to bottom, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 0.3) 50%, rgba(255, 255, 255, 0) 100%);
        transform: rotate(45deg) translateX(-100%);
        transition: transform 0.6s;
    }

    .status-shine:hover::after {
        transform: rotate(45deg) translateX(100%);
    }

    /* Header Background Animation */
    .header-animated {
        position: relative;
        background: linear-gradient(135deg, #1e40af 0%, #3b82f6 50%, #4f46e5 100%);
        overflow: hidden;
    }

    .header-animated::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(45deg, transparent 30%, rgba(255, 255, 255, 0.1) 50%, transparent 70%);
        background-size: 200% 200%;
        animation: shimmer 8s ease-in-out infinite;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-8 bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-gray-800">
    <div class="max-w-4xl w-full">
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl overflow-hidden animate-fade-in">

            <!-- Professional Gradient Header -->
            <div class="header-animated px-8 py-16 text-center relative">
                <div class="relative z-10">
                    <div class="w-24 h-24 mx-auto mb-6 bg-white/20 backdrop-blur-lg rounded-3xl flex items-center justify-center shadow-2xl">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h1 class="text-4xl font-black text-white mb-3 drop-shadow-lg">طلب التسجيل قيد المراجعة</h1>
                    <p class="text-blue-100 text-lg font-medium">سيتم إعلامك فور الموافقة على طلبك</p>
                </div>
            </div>

            <!-- Content -->
            <div class="px-8 py-12">

                <!-- Success Message -->
                <div class="text-center mb-12 animate-fade-delay-1">
                    <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-600 rounded-2xl flex items-center justify-center shadow-xl transform hover:scale-110 transition-transform duration-300">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <h2 class="text-3xl font-black mb-4 text-gradient-impressive">
                        شكراً لاختيارك البرنامج
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400 max-w-2xl mx-auto leading-relaxed text-lg">
                        تم استلام طلب التسجيل في البرنامج بنجاح. طلبك الآن قيد المراجعة من قبل فريق الإدارة.
                    </p>
                </div>

                <!-- Enhanced Progress Steps -->
                <div class="mb-12 max-w-3xl mx-auto animate-fade-delay-2">
                    <div class="relative flex items-center justify-between">

                        <!-- Step 1: Completed -->
                        <div class="flex flex-col items-center flex-1 z-10">
                            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-green-400 to-emerald-600 flex items-center justify-center shadow-lg mb-4 step-circle" style="color: rgba(34, 197, 94, 0.3);">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <span class="text-sm font-bold text-gray-700 dark:text-gray-300">اختيار البرنامج</span>
                            <span class="text-xs text-green-600 dark:text-green-400 mt-2 px-3 py-1 bg-green-100 dark:bg-green-900/30 rounded-full font-semibold">مكتمل ✓</span>
                        </div>

                        <!-- Progress Line 1 -->
                        <div class="flex-1 h-1 bg-gradient-to-r from-green-500 to-blue-600 -mt-20 mx-6 rounded-full progress-line relative overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white to-transparent opacity-30 animate-shimmer" style="background-size: 200% 100%;"></div>
                        </div>

                        <!-- Step 2: Current -->
                        <div class="flex flex-col items-center flex-1 z-10">
                            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg mb-4 step-circle step-active" style="color: rgba(59, 130, 246, 0.3);">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <span class="text-sm font-bold text-gray-700 dark:text-gray-300">قيد المراجعة</span>
                            <span class="text-xs text-blue-600 dark:text-blue-400 mt-2 px-3 py-1 bg-blue-100 dark:bg-blue-900/30 rounded-full font-semibold">جاري العمل...</span>
                        </div>

                        <!-- Progress Line 2 -->
                        <div class="flex-1 h-1 bg-gray-300 dark:bg-gray-600 -mt-20 mx-6 rounded-full"></div>

                        <!-- Step 3: Pending -->
                        <div class="flex flex-col items-center flex-1 z-10">
                            <div class="w-16 h-16 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center shadow-lg mb-4 step-circle" style="color: rgba(156, 163, 175, 0.3);">
                                <svg class="w-8 h-8 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <span class="text-sm font-bold text-gray-500 dark:text-gray-400">الموافقة</span>
                            <span class="text-xs text-gray-500 dark:text-gray-500 mt-2 px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded-full font-semibold">قريباً</span>
                        </div>
                    </div>
                </div>

                <!-- Information Box -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border-l-4 border-blue-600 rounded-2xl p-8 mb-10 animate-fade-delay-3 info-glow">
                    <div class="flex items-start gap-5">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="text-right flex-1">
                            <h3 class="text-xl font-black text-gray-900 dark:text-gray-100 mb-3">الخطوات التالية</h3>
                            <p class="text-gray-700 dark:text-gray-300 leading-relaxed text-base">
                                سيتم مراجعة طلب التسجيل في البرنامج من قبل فريق الإدارة في أقرب وقت ممكن.
                                بمجرد الموافقة على طلبك، ستتمكن من الوصول إلى جميع المواد والجلسات الدراسية.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- User Information -->
                <div class="bg-gradient-to-br from-gray-50 to-blue-50 dark:from-gray-700/50 dark:to-gray-800 rounded-2xl p-8 mb-10">
                    <h3 class="text-xl font-black text-gray-900 dark:text-gray-100 mb-6 flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        معلومات الطلب
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex justify-between items-center p-4 bg-white dark:bg-gray-600 rounded-xl shadow-sm hover:shadow-md transition-shadow">
                            <span class="text-gray-600 dark:text-gray-400 font-semibold">الاسم:</span>
                            <span class="font-bold text-gray-900 dark:text-gray-100">{{ auth()->user()->name }}</span>
                        </div>
                        <div class="flex justify-between items-center p-4 bg-white dark:bg-gray-600 rounded-xl shadow-sm hover:shadow-md transition-shadow">
                            <span class="text-gray-600 dark:text-gray-400 font-semibold">البريد:</span>
                            <span class="font-bold text-gray-900 dark:text-gray-100">{{ auth()->user()->email }}</span>
                        </div>
                        @if(auth()->user()->phone)
                        <div class="flex justify-between items-center p-4 bg-white dark:bg-gray-600 rounded-xl shadow-sm hover:shadow-md transition-shadow">
                            <span class="text-gray-600 dark:text-gray-400 font-semibold">الجوال:</span>
                            <span class="font-bold text-gray-900 dark:text-gray-100">{{ auth()->user()->phone }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between items-center p-4 bg-white dark:bg-gray-600 rounded-xl shadow-sm hover:shadow-md transition-shadow">
                            <span class="text-gray-600 dark:text-gray-400 font-semibold">تاريخ الطلب:</span>
                            <span class="font-bold text-gray-900 dark:text-gray-100">{{ auth()->user()->updated_at->format('Y-m-d') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Action Button -->
                <div class="text-center">
                    <a href="{{ route('student.dashboard') }}"
                       class="inline-flex items-center justify-center gap-3 px-10 py-4 text-white font-bold rounded-xl shadow-xl btn-impressive">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span class="text-lg">العودة إلى الرئيسية</span>
                    </a>
                </div>

            </div>
        </div>

        <!-- Enhanced Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-10">
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border-2 border-transparent hover:border-green-500 card-professional status-shine">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-green-400 to-emerald-600 rounded-xl flex items-center justify-center shadow-md flex-shrink-0">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1 text-right">
                        <h4 class="font-black text-lg text-gray-900 dark:text-gray-100 mb-1">تم الإرسال</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">طلبك قيد المعالجة</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border-2 border-transparent hover:border-blue-500 card-professional status-shine">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-md flex-shrink-0">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1 text-right">
                        <h4 class="font-black text-lg text-gray-900 dark:text-gray-100 mb-1">قيد المراجعة</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">فريق الإدارة يراجع طلبك</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border-2 border-transparent hover:border-purple-500 card-professional status-shine">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center shadow-md flex-shrink-0">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <div class="flex-1 text-right">
                        <h4 class="font-black text-lg text-gray-900 dark:text-gray-100 mb-1">إشعار تلقائي</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">سنخبرك عند الموافقة</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
