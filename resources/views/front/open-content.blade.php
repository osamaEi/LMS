@extends('layouts.front')

@section('title', 'المحتوى التعليمي المفتوح')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-emerald-50 to-green-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="bg-gradient-to-r from-emerald-600 via-green-600 to-teal-700 rounded-2xl shadow-2xl p-8 text-white mb-8 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-72 h-72 bg-white opacity-10 rounded-full -mr-36 -mt-36"></div>
            <div class="absolute bottom-0 left-0 w-56 h-56 bg-white opacity-10 rounded-full -ml-28 -mb-28"></div>

            <div class="relative z-10">
                <div class="flex items-center gap-4 mb-3">
                    <div class="w-16 h-16 rounded-xl bg-white bg-opacity-20 flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-9 h-9" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-4xl font-bold">المحتوى التعليمي المفتوح</h1>
                        <p class="text-emerald-100 text-lg mt-1">موارد تعليمية متاحة للجميع</p>
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-2 text-sm text-emerald-100">
                    <span class="px-3 py-1 bg-white bg-opacity-20 rounded-full backdrop-blur-sm">معيار NELC: 2.1.5</span>
                    <span>|</span>
                    <span>آخر تحديث: {{ date('Y-m-d') }}</span>
                </div>
            </div>
        </div>

        <!-- Introduction Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-100 to-green-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <h2 class="text-2xl font-bold text-gray-800 mb-3">عن المحتوى المفتوح</h2>
                    <p class="text-gray-600 leading-relaxed mb-4">
                        تلتزم منصتنا بإثراء المحتوى التعليمي المفتوح والمساهمة في تنمية مهارات أفراد المجتمع من خلال إتاحة نسبة من محتوى برامجنا التعليمية للجميع دون قيود. هذا المحتوى متاح بالمجان ويمكن الوصول إليه دون الحاجة للتسجيل أو الدخول على النظام.
                    </p>
                    <div class="bg-emerald-50 border-r-4 border-emerald-500 p-4 rounded-lg">
                        <p class="text-sm text-emerald-800 font-medium">
                            <strong>ملاحظة:</strong> جميع المحتويات المتاحة هنا محمية بحقوق الملكية الفكرية، ويُسمح باستخدامها للأغراض التعليمية الشخصية فقط مع ضرورة الإشارة إلى المصدر.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">

            <!-- Content Card 1 -->
            <div class="bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden group">
                <div class="bg-gradient-to-br from-blue-500 to-cyan-600 h-48 relative overflow-hidden">
                    <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-10 transition-opacity"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <svg class="w-20 h-20 text-white opacity-80" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"></path>
                        </svg>
                    </div>
                    <span class="absolute top-4 right-4 px-3 py-1 bg-white bg-opacity-90 text-blue-700 text-xs font-bold rounded-full">فيديو</span>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-2">مقدمة في البرمجة</h3>
                    <p class="text-gray-600 text-sm mb-4">تعلم أساسيات البرمجة من الصفر وحتى الاحتراف مع أمثلة عملية وتطبيقات تفاعلية.</p>
                    <div class="mb-4">
                        <h4 class="text-xs font-bold text-gray-700 mb-2">الأهداف التعليمية:</h4>
                        <ul class="text-xs text-gray-600 space-y-1">
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                فهم المفاهيم الأساسية للبرمجة
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                كتابة أول برنامج لك
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                حل المشكلات البرمجية البسيطة
                            </li>
                        </ul>
                    </div>
                    <button class="w-full px-4 py-2 bg-gradient-to-r from-blue-500 to-cyan-600 text-white rounded-lg font-semibold hover:from-blue-600 hover:to-cyan-700 transition-all">
                        عرض المحتوى
                    </button>
                </div>
            </div>

            <!-- Content Card 2 -->
            <div class="bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden group">
                <div class="bg-gradient-to-br from-purple-500 to-pink-600 h-48 relative overflow-hidden">
                    <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-10 transition-opacity"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <svg class="w-20 h-20 text-white opacity-80" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"></path>
                        </svg>
                    </div>
                    <span class="absolute top-4 right-4 px-3 py-1 bg-white bg-opacity-90 text-purple-700 text-xs font-bold rounded-full">كتاب PDF</span>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-2">أساسيات إدارة المشاريع</h3>
                    <p class="text-gray-600 text-sm mb-4">دليل شامل لإدارة المشاريع الناجحة من التخطيط وحتى التنفيذ والتقييم.</p>
                    <div class="mb-4">
                        <h4 class="text-xs font-bold text-gray-700 mb-2">الأهداف التعليمية:</h4>
                        <ul class="text-xs text-gray-600 space-y-1">
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                فهم دورة حياة المشروع
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                تطبيق منهجيات الإدارة الحديثة
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                إدارة المخاطر والموارد
                            </li>
                        </ul>
                    </div>
                    <button class="w-full px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-600 text-white rounded-lg font-semibold hover:from-purple-600 hover:to-pink-700 transition-all">
                        تحميل الكتاب
                    </button>
                </div>
            </div>

            <!-- Content Card 3 -->
            <div class="bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden group">
                <div class="bg-gradient-to-br from-orange-500 to-red-600 h-48 relative overflow-hidden">
                    <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-10 transition-opacity"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <svg class="w-20 h-20 text-white opacity-80" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M18 3a1 1 0 00-1.196-.98l-10 2A1 1 0 006 5v9.114A4.369 4.369 0 005 14c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V7.82l8-1.6v5.894A4.37 4.37 0 0015 12c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V3z"></path>
                        </svg>
                    </div>
                    <span class="absolute top-4 right-4 px-3 py-1 bg-white bg-opacity-90 text-orange-700 text-xs font-bold rounded-full">ملف صوتي</span>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-2">مهارات التواصل الفعّال</h3>
                    <p class="text-gray-600 text-sm mb-4">سلسلة محاضرات صوتية حول تطوير مهارات التواصل الشخصي والمهني.</p>
                    <div class="mb-4">
                        <h4 class="text-xs font-bold text-gray-700 mb-2">الأهداف التعليمية:</h4>
                        <ul class="text-xs text-gray-600 space-y-1">
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                إتقان فن الإنصات الفعّال
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                تحسين لغة الجسد والحضور
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                التعامل مع الصراعات
                            </li>
                        </ul>
                    </div>
                    <button class="w-full px-4 py-2 bg-gradient-to-r from-orange-500 to-red-600 text-white rounded-lg font-semibold hover:from-orange-600 hover:to-red-700 transition-all">
                        الاستماع الآن
                    </button>
                </div>
            </div>

            <!-- Content Card 4 -->
            <div class="bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden group">
                <div class="bg-gradient-to-br from-teal-500 to-cyan-600 h-48 relative overflow-hidden">
                    <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-10 transition-opacity"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <svg class="w-20 h-20 text-white opacity-80" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0z"></path>
                        </svg>
                    </div>
                    <span class="absolute top-4 right-4 px-3 py-1 bg-white bg-opacity-90 text-teal-700 text-xs font-bold rounded-full">فيديو</span>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-2">التعلم الإلكتروني الفعّال</h3>
                    <p class="text-gray-600 text-sm mb-4">استراتيجيات ونصائح للاستفادة القصوى من بيئة التعلم الإلكتروني.</p>
                    <div class="mb-4">
                        <h4 class="text-xs font-bold text-gray-700 mb-2">الأهداف التعليمية:</h4>
                        <ul class="text-xs text-gray-600 space-y-1">
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                تنظيم الوقت في التعلم عن بعد
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                استخدام الأدوات الرقمية بكفاءة
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                التفاعل الفعّال مع المحتوى
                            </li>
                        </ul>
                    </div>
                    <button class="w-full px-4 py-2 bg-gradient-to-r from-teal-500 to-cyan-600 text-white rounded-lg font-semibold hover:from-teal-600 hover:to-cyan-700 transition-all">
                        عرض المحتوى
                    </button>
                </div>
            </div>

            <!-- Content Card 5 -->
            <div class="bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden group">
                <div class="bg-gradient-to-br from-indigo-500 to-purple-600 h-48 relative overflow-hidden">
                    <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-10 transition-opacity"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <svg class="w-20 h-20 text-white opacity-80" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <span class="absolute top-4 right-4 px-3 py-1 bg-white bg-opacity-90 text-indigo-700 text-xs font-bold rounded-full">كتاب PDF</span>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-2">إدارة الوقت والإنتاجية</h3>
                    <p class="text-gray-600 text-sm mb-4">دليل عملي لتنظيم الوقت وزيادة الإنتاجية الشخصية والمهنية.</p>
                    <div class="mb-4">
                        <h4 class="text-xs font-bold text-gray-700 mb-2">الأهداف التعليمية:</h4>
                        <ul class="text-xs text-gray-600 space-y-1">
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                تطبيق تقنيات إدارة الوقت
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                تحديد الأولويات بفعالية
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                التغلب على مضيعات الوقت
                            </li>
                        </ul>
                    </div>
                    <button class="w-full px-4 py-2 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-lg font-semibold hover:from-indigo-600 hover:to-purple-700 transition-all">
                        تحميل الكتاب
                    </button>
                </div>
            </div>

            <!-- Content Card 6 -->
            <div class="bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden group">
                <div class="bg-gradient-to-br from-pink-500 to-rose-600 h-48 relative overflow-hidden">
                    <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-10 transition-opacity"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <svg class="w-20 h-20 text-white opacity-80" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path>
                        </svg>
                    </div>
                    <span class="absolute top-4 right-4 px-3 py-1 bg-white bg-opacity-90 text-pink-700 text-xs font-bold rounded-full">فيديو</span>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-2">العمل الجماعي والقيادة</h3>
                    <p class="text-gray-600 text-sm mb-4">تطوير مهارات القيادة والعمل ضمن فريق لتحقيق الأهداف المشتركة.</p>
                    <div class="mb-4">
                        <h4 class="text-xs font-bold text-gray-700 mb-2">الأهداف التعليمية:</h4>
                        <ul class="text-xs text-gray-600 space-y-1">
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                فهم ديناميكيات العمل الجماعي
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                تطوير المهارات القيادية
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                تحفيز وإدارة الفرق
                            </li>
                        </ul>
                    </div>
                    <button class="w-full px-4 py-2 bg-gradient-to-r from-pink-500 to-rose-600 text-white rounded-lg font-semibold hover:from-pink-600 hover:to-rose-700 transition-all">
                        عرض المحتوى
                    </button>
                </div>
            </div>

        </div>

        <!-- OERx Platform Link -->
        <div class="bg-gradient-to-r from-blue-600 to-cyan-700 rounded-2xl shadow-xl p-8 text-white">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-14 h-14 rounded-xl bg-white bg-opacity-20 flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold">منصة الموارد التعليمية المفتوحة (OERx)</h2>
                    <p class="text-blue-100 mt-1">اكتشف آلاف الموارد التعليمية المفتوحة من مختلف الجهات التعليمية</p>
                </div>
            </div>
            <p class="text-blue-100 mb-6">
                يمكنك زيارة منصة OERx الوطنية التابعة للمركز الوطني للتعليم الإلكتروني للوصول إلى مكتبة ضخمة من الموارد التعليمية المفتوحة في مختلف التخصصات والمجالات.
            </p>
            <a href="https://oerx.nelc.gov.sa" target="_blank" class="inline-flex items-center gap-2 px-6 py-3 bg-white text-blue-700 rounded-lg font-bold hover:bg-blue-50 transition-all shadow-lg">
                <span>زيارة منصة OERx</span>
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M11 3a1 1 0 100 2h2.586l-6.293 6.293a1 1 0 101.414 1.414L15 6.414V9a1 1 0 102 0V4a1 1 0 00-1-1h-5z"></path>
                    <path d="M5 5a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-3a1 1 0 10-2 0v3H5V7h3a1 1 0 000-2H5z"></path>
                </svg>
            </a>
        </div>

    </div>
</div>
@endsection
