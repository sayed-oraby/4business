@extends('layouts.frontend.master')

@section('title', __('frontend.pages.privacy_title'))
@section('body-class', 'page-static page-privacy')

@push('styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/pages/static.css') }}">
    <style>
        .p-privacy__section { margin-bottom: var(--space-32); }
        .p-privacy__section-title { font-size: var(--font-size-xl); font-weight: var(--font-weight-bold); color: var(--c-primary); margin-bottom: var(--space-16); padding-bottom: var(--space-8); border-bottom: 2px solid var(--c-primary-light); }
        .p-privacy__subsection { margin-bottom: var(--space-20); }
        .p-privacy__subsection-title { font-size: var(--font-size-lg); font-weight: var(--font-weight-semibold); margin-bottom: var(--space-12); color: var(--c-text); }
        .p-privacy__list { list-style: none; padding: 0; margin: 0; }
        .p-privacy__list li { position: relative; padding-right: var(--space-24); margin-bottom: var(--space-12); line-height: 1.8; }
        .p-privacy__list li::before { content: "•"; position: absolute; right: 0; color: var(--c-primary); font-weight: bold; }
        .p-privacy__text { line-height: 1.9; color: var(--c-text-soft); margin-bottom: var(--space-16); }
        .p-privacy__highlight { background: var(--c-primary-light); padding: var(--space-16); border-radius: var(--radius-lg); margin: var(--space-20) 0; border-right: 4px solid var(--c-primary); }
        .p-privacy__contact { background: var(--c-bg-white); padding: var(--space-24); border-radius: var(--radius-lg); box-shadow: var(--shadow-card); text-align: center; margin-top: var(--space-32); }
        .p-privacy__contact-email { font-size: var(--font-size-lg); color: var(--c-primary); font-weight: var(--font-weight-semibold); }
        .p-privacy__update { font-size: var(--font-size-sm); color: var(--c-muted); text-align: center; margin-top: var(--space-32); padding-top: var(--space-20); border-top: 1px solid var(--c-border); }
    </style>
@endpush

@section('content')
    <main class="p-static">
        <div class="l-container">
            <div class="p-static__header">
                <h1 class="p-static__title">سياسة الخصوصية</h1>
                <p class="p-static__subtitle">نلتزم بحماية خصوصيتك وبياناتك الشخصية</p>
            </div>

            <div class="p-static__content">
                <div class="p-static__card">

                    <!-- مقدمة -->
                    <section class="p-privacy__section">
                        <h2 class="p-privacy__section-title">مقدمة</h2>
                        <p class="p-privacy__text">
                            نرحب بكم في منصة دليل العقار (https://dalaqar.com)، والتي تمتلكها وتديرها شركة دليل العقار التابعة لأد وايز. نحن نقدر ثقتكم بنا ونلتزم بحماية خصوصيتكم وبياناتكم الشخصية. توضح سياسة الخصوصية هذه كيفية جمع واستخدام وحماية معلوماتكم الشخصية عند استخدام موقعنا الإلكتروني أو تطبيقاتنا.
                        </p>
                        <p class="p-privacy__text">
                            باستخدامك لمنصة دليل العقار، فإنك توافق على جمع واستخدام معلوماتك وفقاً لهذه السياسة. إذا كنت لا توافق على أي جزء من هذه السياسة، يرجى عدم استخدام خدماتنا.
                        </p>
                    </section>

                    <!-- المعلومات التي نجمعها -->
                    <section class="p-privacy__section">
                        <h2 class="p-privacy__section-title">المعلومات التي نجمعها</h2>

                        <div class="p-privacy__subsection">
                            <h3 class="p-privacy__subsection-title">المعلومات الشخصية</h3>
                            <p class="p-privacy__text">عند التسجيل أو استخدام خدماتنا، قد نجمع المعلومات التالية:</p>
                            <ul class="p-privacy__list">
                                <li>الاسم الكامل</li>
                                <li>رقم الهاتف المحمول</li>
                                <li>البريد الإلكتروني</li>
                                <li>العنوان (في حال كنت مكتباً عقارياً)</li>
                                <li>معلومات الحساب وبيانات تسجيل الدخول</li>
                            </ul>
                        </div>

                        <div class="p-privacy__subsection">
                            <h3 class="p-privacy__subsection-title">معلومات الإعلانات</h3>
                            <p class="p-privacy__text">عند نشر إعلان عقاري، نجمع:</p>
                            <ul class="p-privacy__list">
                                <li>تفاصيل العقار (النوع، الموقع، السعر، المواصفات)</li>
                                <li>الصور والوسائط المرفقة</li>
                                <li>معلومات الاتصال المخصصة للإعلان</li>
                            </ul>
                        </div>

                        <div class="p-privacy__subsection">
                            <h3 class="p-privacy__subsection-title">المعلومات التقنية</h3>
                            <p class="p-privacy__text">نجمع تلقائياً بعض المعلومات التقنية مثل:</p>
                            <ul class="p-privacy__list">
                                <li>عنوان IP الخاص بك</li>
                                <li>نوع المتصفح ونظام التشغيل</li>
                                <li>صفحات الموقع التي تزورها</li>
                                <li>وقت وتاريخ الزيارة</li>
                                <li>معرّفات الجهاز (للتطبيقات)</li>
                            </ul>
                        </div>
                    </section>

                    <!-- كيفية استخدام المعلومات -->
                    <section class="p-privacy__section">
                        <h2 class="p-privacy__section-title">كيفية استخدام المعلومات</h2>
                        <p class="p-privacy__text">نستخدم المعلومات التي نجمعها للأغراض التالية:</p>
                        <ul class="p-privacy__list">
                            <li>توفير وتحسين خدماتنا ومنصتنا</li>
                            <li>إدارة حسابك والتحقق من هويتك</li>
                            <li>نشر وعرض إعلاناتك العقارية</li>
                            <li>تمكين التواصل بين المعلنين والباحثين عن عقارات</li>
                            <li>إرسال إشعارات مهمة تتعلق بحسابك أو إعلاناتك</li>
                            <li>تحسين تجربة المستخدم وتخصيص المحتوى</li>
                            <li>تحليل استخدام المنصة وقياس الأداء</li>
                            <li>منع الاحتيال وحماية أمن المنصة</li>
                            <li>الامتثال للمتطلبات القانونية</li>
                        </ul>
                    </section>

                    <!-- مشاركة المعلومات -->
                    <section class="p-privacy__section">
                        <h2 class="p-privacy__section-title">مشاركة المعلومات</h2>

                        <div class="p-privacy__highlight">
                            <p class="p-privacy__text" style="margin-bottom: 0;">
                                <strong>التزامنا:</strong> لا نبيع أو نؤجر معلوماتك الشخصية لأطراف ثالثة لأغراض تسويقية.
                            </p>
                        </div>

                        <p class="p-privacy__text">قد نشارك معلوماتك في الحالات التالية فقط:</p>
                        <ul class="p-privacy__list">
                            <li><strong>معلومات الإعلان:</strong> يتم عرض معلومات الاتصال التي تقدمها في إعلاناتك للزوار الراغبين في التواصل معك.</li>
                            <li><strong>مقدمو الخدمات:</strong> نتعاون مع شركات موثوقة لتقديم خدمات مثل معالجة المدفوعات، استضافة البيانات، وتحليلات الموقع. هؤلاء الشركاء ملزمون بحماية معلوماتك.</li>
                            <li><strong>المتطلبات القانونية:</strong> قد نفصح عن معلوماتك إذا طُلب منا ذلك بموجب القانون أو أمر قضائي.</li>
                            <li><strong>حماية الحقوق:</strong> قد نشارك المعلومات لحماية حقوقنا أو سلامة المستخدمين أو الجمهور.</li>
                        </ul>
                    </section>

                    <!-- ملفات تعريف الارتباط (الكوكيز) -->
                    <section class="p-privacy__section">
                        <h2 class="p-privacy__section-title">ملفات تعريف الارتباط (الكوكيز)</h2>
                        <p class="p-privacy__text">
                            نستخدم ملفات تعريف الارتباط وتقنيات مشابهة لتحسين تجربتك على منصتنا. تساعدنا هذه الملفات في:
                        </p>
                        <ul class="p-privacy__list">
                            <li>تذكر تفضيلاتك وإعداداتك</li>
                            <li>الحفاظ على تسجيل دخولك</li>
                            <li>تحليل كيفية استخدام المنصة</li>
                            <li>تخصيص المحتوى والإعلانات</li>
                        </ul>
                        <p class="p-privacy__text">
                            يمكنك التحكم في ملفات تعريف الارتباط من خلال إعدادات متصفحك. ومع ذلك، قد يؤثر تعطيلها على بعض وظائف المنصة.
                        </p>
                    </section>

                    <!-- أمن البيانات -->
                    <section class="p-privacy__section">
                        <h2 class="p-privacy__section-title">أمن البيانات</h2>
                        <p class="p-privacy__text">
                            نتخذ إجراءات أمنية مناسبة لحماية معلوماتك الشخصية من الوصول غير المصرح به أو التغيير أو الإفشاء أو الإتلاف. تشمل هذه الإجراءات:
                        </p>
                        <ul class="p-privacy__list">
                            <li>تشفير البيانات الحساسة</li>
                            <li>استخدام بروتوكولات آمنة (HTTPS)</li>
                            <li>تقييد الوصول إلى البيانات للموظفين المخولين فقط</li>
                            <li>مراقبة النظام بشكل منتظم لاكتشاف الثغرات</li>
                        </ul>
                        <p class="p-privacy__text">
                            على الرغم من جهودنا، لا يمكن ضمان أمان نقل البيانات عبر الإنترنت بنسبة 100%. أنت مسؤول عن الحفاظ على سرية بيانات حسابك.
                        </p>
                    </section>

                    <!-- الاحتفاظ بالبيانات -->
                    <section class="p-privacy__section">
                        <h2 class="p-privacy__section-title">الاحتفاظ بالبيانات</h2>
                        <p class="p-privacy__text">
                            نحتفظ بمعلوماتك الشخصية طالما كان حسابك نشطاً أو حسب الحاجة لتقديم خدماتنا. قد نحتفظ ببعض المعلومات لفترة أطول للامتثال للالتزامات القانونية أو حل النزاعات أو إنفاذ اتفاقياتنا.
                        </p>
                        <p class="p-privacy__text">
                            يمكنك طلب حذف حسابك وبياناتك الشخصية في أي وقت من خلال إعدادات الحساب أو التواصل معنا مباشرة.
                        </p>
                    </section>

                    <!-- حقوقك -->
                    <section class="p-privacy__section">
                        <h2 class="p-privacy__section-title">حقوقك</h2>
                        <p class="p-privacy__text">لديك الحقوق التالية فيما يتعلق ببياناتك الشخصية:</p>
                        <ul class="p-privacy__list">
                            <li><strong>الوصول:</strong> حق طلب نسخة من بياناتك الشخصية التي نحتفظ بها.</li>
                            <li><strong>التصحيح:</strong> حق طلب تصحيح أي معلومات غير دقيقة.</li>
                            <li><strong>الحذف:</strong> حق طلب حذف بياناتك الشخصية.</li>
                            <li><strong>الاعتراض:</strong> حق الاعتراض على معالجة بياناتك لأغراض معينة.</li>
                            <li><strong>إلغاء الاشتراك:</strong> حق إلغاء الاشتراك في الاتصالات التسويقية.</li>
                        </ul>
                        <p class="p-privacy__text">
                            لممارسة أي من هذه الحقوق، يرجى التواصل معنا عبر البريد الإلكتروني المذكور أدناه.
                        </p>
                    </section>

                    <!-- خصوصية الأطفال -->
                    <section class="p-privacy__section">
                        <h2 class="p-privacy__section-title">خصوصية الأطفال</h2>
                        <p class="p-privacy__text">
                            خدماتنا غير موجهة للأشخاص الذين تقل أعمارهم عن 18 عاماً. لا نجمع عن قصد معلومات شخصية من القاصرين. إذا علمنا أننا جمعنا معلومات من طفل دون موافقة ولي الأمر، سنتخذ خطوات لحذف تلك المعلومات.
                        </p>
                    </section>

                    <!-- الروابط الخارجية -->
                    <section class="p-privacy__section">
                        <h2 class="p-privacy__section-title">الروابط الخارجية</h2>
                        <p class="p-privacy__text">
                            قد تحتوي منصتنا على روابط لمواقع إلكترونية أو خدمات تابعة لأطراف ثالثة. لسنا مسؤولين عن ممارسات الخصوصية لهذه المواقع. ننصحك بمراجعة سياسات الخصوصية لأي موقع تزوره.
                        </p>
                    </section>

                    <!-- التغييرات على سياسة الخصوصية -->
                    <section class="p-privacy__section">
                        <h2 class="p-privacy__section-title">التغييرات على سياسة الخصوصية</h2>
                        <p class="p-privacy__text">
                            قد نقوم بتحديث سياسة الخصوصية هذه من وقت لآخر. سنقوم بإخطارك بأي تغييرات جوهرية عن طريق نشر السياسة الجديدة على هذه الصفحة وتحديث تاريخ "آخر تحديث". ننصحك بمراجعة هذه السياسة بشكل دوري.
                        </p>
                    </section>

                    <!-- اتصل بنا -->
                    <section class="p-privacy__section">
                        <h2 class="p-privacy__section-title">اتصل بنا</h2>
                        <p class="p-privacy__text">
                            إذا كان لديك أي أسئلة أو استفسارات حول سياسة الخصوصية هذه أو ممارساتنا المتعلقة بالبيانات، يرجى التواصل معنا:
                        </p>

                        <div class="p-privacy__contact">
                            <p style="margin-bottom: var(--space-8);">البريد الإلكتروني:</p>
                            <a href="mailto:privacy@dalaqar.com" class="p-privacy__contact-email">privacy@dalaqar.com</a>
                        </div>
                    </section>

                    <p class="p-privacy__update">آخر تحديث: يناير 2026</p>

                </div>
            </div>
        </div>
    </main>
@endsection
