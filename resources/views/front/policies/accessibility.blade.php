@extends('layouts.front')

@section('title', 'سياسة إمكانية الوصول')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header Card -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-700 rounded-t-2xl p-8 text-white">
            <h1 class="text-3xl font-bold">سياسة إمكانية الوصول</h1>
            <p class="mt-2 text-indigo-100">دعم ذوي الإعاقة والتصميم الشامل</p>
            <div class="mt-3 flex items-center gap-2 text-sm text-indigo-200">
                <span>معيار NELC: 1.2.9</span>
                <span>|</span>
                <span>آخر تحديث: {{ date('Y-m-d') }}</span>
            </div>
        </div>

        <!-- Content Card -->
        <div class="bg-white rounded-b-2xl shadow-xl p-8 space-y-8">

            <section>
                <h2 class="text-2xl font-bold text-gray-800 mb-4 pb-2 border-b-2 border-indigo-500">التزامنا</h2>
                <p class="text-gray-700 leading-relaxed">
                    تلتزم منصتنا التعليمية بتوفير بيئة تعليمية شاملة ومتاحة لجميع المتعلمين بمختلف قدراتهم وإعاقاتهم. نؤمن بأن التعليم حق للجميع، ونسعى لتطبيق مبادئ التصميم الشامل (Universal Design) لضمان وصول الجميع إلى المحتوى والخدمات التعليمية دون عوائق.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-bold text-gray-800 mb-4 pb-2 border-b-2 border-indigo-500">الميزات والأدوات المتوفرة</h2>

                <div class="space-y-4">
                    <div class="bg-indigo-50 rounded-lg p-4">
                        <h3 class="text-lg font-bold text-gray-800 mb-2 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                            قارئات الشاشة
                        </h3>
                        <p class="text-gray-700">
                            جميع عناصر المنصة متوافقة مع برامج قراءة الشاشة مثل JAWS وNVDA وVoiceOver، مع توفير نصوص بديلة (Alt Text) لجميع الصور والعناصر المرئية.
                        </p>
                    </div>

                    <div class="bg-indigo-50 rounded-lg p-4">
                        <h3 class="text-lg font-bold text-gray-800 mb-2 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                            التباين والألوان
                        </h3>
                        <p class="text-gray-700">
                            تصميم يراعي التباين العالي بين النصوص والخلفيات لتسهيل القراءة لضعاف البصر وأصحاب عمى الألوان، مع إمكانية التبديل إلى الوضع الليلي (Dark Mode).
                        </p>
                    </div>

                    <div class="bg-indigo-50 rounded-lg p-4">
                        <h3 class="text-lg font-bold text-gray-800 mb-2 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 01.894 1.79l-1.233.616 1.738 5.42a1 1 0 01-.285 1.05A3.989 3.989 0 0115 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.715-5.349L11 6.477V16h2a1 1 0 110 2H7a1 1 0 110-2h2V6.477L6.237 7.582l1.715 5.349a1 1 0 01-.285 1.05A3.989 3.989 0 015 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.738-5.42-1.233-.617a1 1 0 01.894-1.788l1.599.799L9 4.323V3a1 1 0 011-1z"></path></svg>
                            تكبير النصوص
                        </h3>
                        <p class="text-gray-700">
                            إمكانية تكبير النصوص حتى 200% دون فقدان المحتوى أو الوظائف، مع دعم خاصية Zoom في جميع المتصفحات.
                        </p>
                    </div>

                    <div class="bg-indigo-50 rounded-lg p-4">
                        <h3 class="text-lg font-bold text-gray-800 mb-2 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.707.707L4.586 13H2a1 1 0 01-1-1V8a1 1 0 011-1h2.586l3.707-3.707a1 1 0 011.09-.217zM14.657 2.929a1 1 0 011.414 0A9.972 9.972 0 0119 10a9.972 9.972 0 01-2.929 7.071 1 1 0 01-1.414-1.414A7.971 7.971 0 0017 10c0-2.21-.894-4.208-2.343-5.657a1 1 0 010-1.414zm-2.829 2.828a1 1 0 011.415 0A5.983 5.983 0 0115 10a5.984 5.984 0 01-1.757 4.243 1 1 0 01-1.415-1.415A3.984 3.984 0 0013 10a3.983 3.983 0 00-1.172-2.828 1 1 0 010-1.415z" clip-rule="evenodd"></path></svg>
                            النصوص المفرغة والترجمة
                        </h3>
                        <p class="text-gray-700">
                            توفير تفريغ نصي (Transcripts) لجميع المحاضرات المسجلة والمقاطع الصوتية، مع ترجمة مرئية (Subtitles) للصم وضعاف السمع.
                        </p>
                    </div>

                    <div class="bg-indigo-50 rounded-lg p-4">
                        <h3 class="text-lg font-bold text-gray-800 mb-2 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path></svg>
                            التنقل بلوحة المفاتيح
                        </h3>
                        <p class="text-gray-700">
                            إمكانية التنقل الكامل في المنصة باستخدام لوحة المفاتيح فقط (Keyboard Navigation) مع مؤشرات واضحة للعنصر النشط (Focus Indicators).
                        </p>
                    </div>
                </div>
            </section>

            <section>
                <h2 class="text-2xl font-bold text-gray-800 mb-4 pb-2 border-b-2 border-indigo-500">طلب الدعم الخاص</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    في حال انضمام متعلمين من ذوي الإعاقة إلى برامجنا، تتعهد الجهة بتوفير جميع الأدوات والبرامج الداعمة حسب نوع الإعاقة وشدتها، بما في ذلك:
                </p>
                <ul class="list-disc list-inside space-y-2 text-gray-700 mr-4">
                    <li>برامج قراءة الشاشة المتقدمة (JAWS, NVDA, VoiceOver)</li>
                    <li>لوحات مفاتيح بديلة ومتخصصة</li>
                    <li>أدوات التعرف على الصوت (Speech Recognition)</li>
                    <li>شاشات Braille للمكفوفين</li>
                    <li>ترجمة لغة الإشارة للصم</li>
                    <li>محتوى بصيغ متعددة (نصي، صوتي، مرئي)</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-bold text-gray-800 mb-4 pb-2 border-b-2 border-indigo-500">الامتثال للمعايير الدولية</h2>
                <p class="text-gray-700 leading-relaxed">
                    تلتزم منصتنا بتطبيق المعايير الدولية لإمكانية الوصول، بما في ذلك:
                </p>
                <ul class="list-disc list-inside space-y-2 text-gray-700 mr-4 mt-3">
                    <li><strong>WCAG 2.1</strong> (Web Content Accessibility Guidelines) - المستوى AA كحد أدنى</li>
                    <li><strong>Section 508</strong> - معايير إمكانية الوصول الأمريكية</li>
                    <li><strong>EN 301 549</strong> - المعيار الأوروبي لإمكانية الوصول</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-bold text-gray-800 mb-4 pb-2 border-b-2 border-indigo-500">التواصل معنا</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    نرحب بملاحظاتكم واقتراحاتكم لتحسين إمكانية الوصول في منصتنا. يمكنكم التواصل معنا عبر:
                </p>
                <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                    <p class="text-gray-700"><strong>البريد الإلكتروني:</strong> accessibility@platform.sa</p>
                    <p class="text-gray-700"><strong>نظام التذاكر:</strong> <a href="{{ route('contact') }}" class="text-indigo-600 hover:underline">تقديم طلب دعم</a></p>
                </div>
            </section>

        </div>
    </div>
</div>
@endsection
