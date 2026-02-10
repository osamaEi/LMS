@extends('layouts.front')

@section('title', 'الالتزام بالأنظمة واللوائح الوطنية')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header Card -->
        <div class="bg-gradient-to-r from-lime-600 to-green-700 rounded-t-2xl p-8 text-white">
            <h1 class="text-3xl font-bold">الالتزام بالأنظمة واللوائح الوطنية</h1>
            <p class="mt-2 text-lime-100">تعهد بالامتثال الكامل للأنظمة السعودية</p>
            <div class="mt-3 flex items-center gap-2 text-sm text-lime-200">
                <span>معيار NELC: 1.1.3</span>
                <span>|</span>
                <span>آخر تحديث: {{ date('Y-m-d') }}</span>
            </div>
        </div>

        <!-- Content Card -->
        <div class="bg-white rounded-b-2xl shadow-xl p-8 space-y-8">

            <section>
                <h2 class="text-2xl font-bold text-gray-800 mb-4 pb-2 border-b-2 border-lime-500">تعهد الالتزام</h2>
                <div class="bg-lime-50 border-r-4 border-lime-600 p-6 rounded-lg">
                    <p class="text-gray-800 leading-relaxed font-medium">
                        تتعهد منصتنا التعليمية بالالتزام الكامل بجميع الأنظمة واللوائح والسياسات الصادرة من الجهات الرسمية المختلفة في المملكة العربية السعودية، وتتحمل المسؤولية الكاملة عن ضمان سلامة جميع المحتويات المعروضة في بيئة التعليم الإلكتروني والتأكد من عدم وجود ما يتعارض مع الدين الإسلامي والثقافة السعودية والسياسة العامة للمملكة.
                    </p>
                </div>
            </section>

            <section>
                <h2 class="text-2xl font-bold text-gray-800 mb-4 pb-2 border-b-2 border-lime-500">الأنظمة واللوائح الملتزم بها</h2>

                <div class="space-y-4">
                    <div class="bg-white border border-lime-200 rounded-lg p-5 hover:shadow-md transition-shadow">
                        <h3 class="text-lg font-bold text-gray-800 mb-2 flex items-center gap-2">
                            <span class="w-8 h-8 bg-lime-100 rounded-lg flex items-center justify-center text-lime-700 font-bold">1</span>
                            نظام التعليم العالي
                        </h3>
                        <p class="text-gray-700">
                            الالتزام بنظام التعليم العالي ولوائحه التنفيذية الصادرة عن وزارة التعليم.
                        </p>
                    </div>

                    <div class="bg-white border border-lime-200 rounded-lg p-5 hover:shadow-md transition-shadow">
                        <h3 class="text-lg font-bold text-gray-800 mb-2 flex items-center gap-2">
                            <span class="w-8 h-8 bg-lime-100 rounded-lg flex items-center justify-center text-lime-700 font-bold">2</span>
                            معايير المركز الوطني للتعليم الإلكتروني (NELC)
                        </h3>
                        <p class="text-gray-700">
                            تطبيق جميع معايير NELC الإلزامية والاختيارية للتعليم الإلكتروني والتدريب الإلكتروني.
                        </p>
                    </div>

                    <div class="bg-white border border-lime-200 rounded-lg p-5 hover:shadow-md transition-shadow">
                        <h3 class="text-lg font-bold text-gray-800 mb-2 flex items-center gap-2">
                            <span class="w-8 h-8 bg-lime-100 rounded-lg flex items-center justify-center text-lime-700 font-bold">3</span>
                            نظام حماية البيانات الشخصية
                        </h3>
                        <p class="text-gray-700">
                            الامتثال الكامل لنظام حماية البيانات الشخصية الصادر بالمرسوم الملكي رقم (م/19) والالتزام بلوائحه التنفيذية.
                        </p>
                    </div>

                    <div class="bg-white border border-lime-200 rounded-lg p-5 hover:shadow-md transition-shadow">
                        <h3 class="text-lg font-bold text-gray-800 mb-2 flex items-center gap-2">
                            <span class="w-8 h-8 bg-lime-100 rounded-lg flex items-center justify-center text-lime-700 font-bold">4</span>
                            نظام مكافحة جرائم المعلوماتية
                        </h3>
                        <p class="text-gray-700">
                            الالتزام بنظام مكافحة جرائم المعلوماتية وعدم استخدام المنصة لأي أغراض غير قانونية.
                        </p>
                    </div>

                    <div class="bg-white border border-lime-200 rounded-lg p-5 hover:shadow-md transition-shadow">
                        <h3 class="text-lg font-bold text-gray-800 mb-2 flex items-center gap-2">
                            <span class="w-8 h-8 bg-lime-100 rounded-lg flex items-center justify-center text-lime-700 font-bold">5</span>
                            نظام حماية حقوق المؤلف
                        </h3>
                        <p class="text-gray-700">
                            احترام حقوق الملكية الفكرية والالتزام بنظام حماية حقوق المؤلف السعودي.
                        </p>
                    </div>

                    <div class="bg-white border border-lime-200 rounded-lg p-5 hover:shadow-md transition-shadow">
                        <h3 class="text-lg font-bold text-gray-800 mb-2 flex items-center gap-2">
                            <span class="w-8 h-8 bg-lime-100 rounded-lg flex items-center justify-center text-lime-700 font-bold">6</span>
                            سياسات الذكاء الاصطناعي وأخلاقيات البيانات
                        </h3>
                        <p class="text-gray-700">
                            الامتثال لسياسات الهيئة السعودية للبيانات والذكاء الاصطناعي (SDAIA) ومبادئ أخلاقيات الذكاء الاصطناعي.
                        </p>
                    </div>

                    <div class="bg-white border border-lime-200 rounded-lg p-5 hover:shadow-md transition-shadow">
                        <h3 class="text-lg font-bold text-gray-800 mb-2 flex items-center gap-2">
                            <span class="w-8 h-8 bg-lime-100 rounded-lg flex items-center justify-center text-lime-700 font-bold">7</span>
                            نظام مزاولة المهن الصحية
                        </h3>
                        <p class="text-gray-700">
                            (عند تقديم برامج صحية) الالتزام بنظام مزاولة المهن الصحية ومعايير الهيئة السعودية للتخصصات الصحية.
                        </p>
                    </div>

                    <div class="bg-white border border-lime-200 rounded-lg p-5 hover:shadow-md transition-shadow">
                        <h3 class="text-lg font-bold text-gray-800 mb-2 flex items-center gap-2">
                            <span class="w-8 h-8 bg-lime-100 rounded-lg flex items-center justify-center text-lime-700 font-bold">8</span>
                            ضوابط المحتوى الإعلامي
                        </h3>
                        <p class="text-gray-700">
                            الالتزام بضوابط هيئة الإذاعة والتلفزيون ومبادئ المحتوى الإعلامي المسؤول.
                        </p>
                    </div>
                </div>
            </section>

            <section>
                <h2 class="text-2xl font-bold text-gray-800 mb-4 pb-2 border-b-2 border-lime-500">مراجعة المحتوى</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    تلتزم المنصة بمراجعة جميع المحتويات التعليمية المنشورة بشكل دوري ومستمر للتأكد من:
                </p>
                <ul class="list-disc list-inside space-y-2 text-gray-700 mr-4">
                    <li>عدم تعارض المحتوى مع تعاليم الدين الإسلامي</li>
                    <li>احترام القيم والثقافة السعودية</li>
                    <li>عدم التطرق للمواضيع السياسية الحساسة</li>
                    <li>خلو المحتوى من الدعوات المخالفة للنظام العام</li>
                    <li>عدم نشر محتوى ينتهك حقوق الآخرين</li>
                    <li>الالتزام بمعايير اللغة العربية السليمة</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-bold text-gray-800 mb-4 pb-2 border-b-2 border-lime-500">آلية الإبلاغ</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    في حال ملاحظة أي محتوى مخالف للأنظمة واللوائح، يمكنكم الإبلاغ عنه فوراً عبر:
                </p>
                <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-lime-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                        </svg>
                        <span class="text-gray-700"><strong>البريد الإلكتروني:</strong> compliance@platform.sa</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-lime-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-2 0c0 .993-.241 1.929-.668 2.754l-1.524-1.525a3.997 3.997 0 00.078-2.183l1.562-1.562C15.802 8.249 16 9.1 16 10zm-5.165 3.913l1.58 1.58A5.98 5.98 0 0110 16a5.976 5.976 0 01-2.516-.552l1.562-1.562a4.006 4.006 0 001.789.027zm-4.677-2.796a4.002 4.002 0 01-.078-2.183l1.562-1.562C5.802 8.249 6 9.1 6 10c0 .993-.241 1.929-.668 2.754l-1.524-1.525a3.997 3.997 0 00.078-2.183l1.562-1.562z" clip-rule="evenodd"></path></svg>
                        <span class="text-gray-700"><strong>نظام الدعم:</strong> <a href="{{ route('contact') }}" class="text-lime-600 hover:underline">تقديم بلاغ</a></span>
                    </div>
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-lime-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                        </svg>
                        <span class="text-gray-700"><strong>الهاتف:</strong> 920000000</span>
                    </div>
                </div>
                <p class="text-sm text-gray-600 mt-4 italic">
                    * نضمن سرية جميع البلاغات ومعالجتها خلال 48 ساعة كحد أقصى
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-bold text-gray-800 mb-4 pb-2 border-b-2 border-lime-500">التحديثات والتعديلات</h2>
                <p class="text-gray-700 leading-relaxed">
                    تلتزم المنصة بمتابعة أي تحديثات أو تعديلات تطرأ على الأنظمة واللوائح الوطنية، وتطبيقها فوراً على المنصة والمحتوى التعليمي. سيتم إشعار جميع المستخدمين بأي تغييرات جوهرية عبر البريد الإلكتروني وإشعارات المنصة.
                </p>
            </section>

        </div>
    </div>
</div>
@endsection
