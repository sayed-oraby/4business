@extends('layouts.frontend.master')

@section('title', __('frontend.pages.about_title'))
@section('body-class', 'page-static page-about')

@push('styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/pages/static.css') }}">
    <style>
        .p-about__hero { background: linear-gradient(135deg, var(--c-primary) 0%, var(--c-primary-dark) 100%); color: white; padding: var(--space-48) 0; margin-bottom: var(--space-32); border-radius: var(--radius-xl); text-align: center; }
        .p-about__hero-title { font-size: var(--font-size-3xl); font-weight: var(--font-weight-bold); margin-bottom: var(--space-12); }
        .p-about__hero-subtitle { font-size: var(--font-size-lg); opacity: 0.9; max-width: 600px; margin: 0 auto; }
        
        .p-about__section { margin-bottom: var(--space-40); }
        .p-about__section-title { font-size: var(--font-size-xl); font-weight: var(--font-weight-bold); color: var(--c-primary); margin-bottom: var(--space-20); padding-bottom: var(--space-8); border-bottom: 2px solid var(--c-primary-light); display: flex; align-items: center; gap: var(--space-12); }
        .p-about__section-icon { width: 32px; height: 32px; background: var(--c-primary-light); border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; }
        .p-about__section-icon svg { width: 18px; height: 18px; color: var(--c-primary); }
        
        .p-about__text { line-height: 2; color: var(--c-text-soft); margin-bottom: var(--space-16); font-size: var(--font-size-md); }
        
        .p-about__stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: var(--space-20); margin: var(--space-32) 0; }
        .p-about__stat { background: var(--c-bg-white); padding: var(--space-24); border-radius: var(--radius-lg); text-align: center; box-shadow: var(--shadow-card); transition: transform 0.2s ease; }
        .p-about__stat:hover { transform: translateY(-4px); }
        .p-about__stat-value { font-size: var(--font-size-3xl); font-weight: var(--font-weight-bold); color: var(--c-primary); margin-bottom: var(--space-8); }
        .p-about__stat-label { font-size: var(--font-size-sm); color: var(--c-muted); }
        
        .p-about__features { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: var(--space-24); margin: var(--space-32) 0; }
        .p-about__feature { background: var(--c-bg-white); padding: var(--space-24); border-radius: var(--radius-lg); box-shadow: var(--shadow-card); }
        .p-about__feature-icon { width: 56px; height: 56px; background: var(--c-primary-light); border-radius: var(--radius-lg); display: flex; align-items: center; justify-content: center; margin-bottom: var(--space-16); }
        .p-about__feature-icon svg { width: 28px; height: 28px; color: var(--c-primary); }
        .p-about__feature-title { font-size: var(--font-size-lg); font-weight: var(--font-weight-semibold); margin-bottom: var(--space-8); }
        .p-about__feature-desc { font-size: var(--font-size-sm); color: var(--c-muted); line-height: 1.7; }
        
        .p-about__highlight { background: linear-gradient(135deg, var(--c-primary-light) 0%, #e0f2fe 100%); padding: var(--space-32); border-radius: var(--radius-xl); margin: var(--space-32) 0; border-right: 4px solid var(--c-primary); }
        .p-about__highlight-title { font-size: var(--font-size-xl); font-weight: var(--font-weight-bold); color: var(--c-primary); margin-bottom: var(--space-16); }
        
        .p-about__cta { background: var(--c-primary); color: white; padding: var(--space-40); border-radius: var(--radius-xl); text-align: center; margin-top: var(--space-40); }
        .p-about__cta-title { font-size: var(--font-size-2xl); font-weight: var(--font-weight-bold); margin-bottom: var(--space-12); }
        .p-about__cta-text { opacity: 0.9; margin-bottom: var(--space-24); max-width: 500px; margin-left: auto; margin-right: auto; }
        .p-about__cta-btn { display: inline-flex; align-items: center; gap: var(--space-8); background: white; color: var(--c-primary); padding: var(--space-12) var(--space-24); border-radius: var(--radius-lg); font-weight: var(--font-weight-semibold); text-decoration: none; transition: transform 0.2s ease; }
        .p-about__cta-btn:hover { transform: scale(1.05); }
        .p-about__cta-btn svg { width: 20px; height: 20px; }
        
        @media (max-width: 640px) {
            .p-about__stats { grid-template-columns: repeat(2, 1fr); }
            .p-about__hero { padding: var(--space-32) var(--space-16); }
            .p-about__hero-title { font-size: var(--font-size-2xl); }
        }
    </style>
@endpush

@section('content')
    <main class="p-static">
        <div class="l-container">
            
            <!-- Hero Section -->
            <div class="p-about__hero">
                <h1 class="p-about__hero-title">من نحن</h1>
                <p class="p-about__hero-subtitle">منصتك الأولى للبحث عن العقارات في الكويت</p>
            </div>

            <div class="p-static__content">
                <div class="p-static__card">
                    
                    <!-- عن دليل العقار -->
                    <section class="p-about__section">
                        <h2 class="p-about__section-title">
                            <span class="p-about__section-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                    <polyline points="9 22 9 12 15 12 15 22"/>
                                </svg>
                            </span>
                            عن دليل العقار
                        </h2>
                        <p class="p-about__text">
                            دليل العقار هو أكبر موقع وتطبيق عقاري مجاني في الكويت، وهو عبارة عن محرك للبحث والإعلان عن العقارات، يسهّل التقاء البائع بالمشتري أو المؤجر بالمستأجر عبر الموقع الإلكتروني <a href="https://dalaqar.com" target="_blank" style="color: var(--c-primary); font-weight: 600;">https://dalaqar.com</a> أو التطبيقات التابعة له في متجر أبل أو جوجل بلاي.
                        </p>
                        <p class="p-about__text">
                            يحتوي دليل العقار على آلاف الإعلانات العقارية المعروضة لغرض البيع أو الإيجار أو البدل في أقسام متعددة مثل الشقق، البيوت، الفلل، الأراضي، العمارات، المحلات، المكاتب التجارية وحتى المزارع وغيرها.
                        </p>
                        <p class="p-about__text">
                            يُمكّن دليل العقار الباحثين عن عقارات من الوصول إلى العقارات غير المدرجة في المنصات التقليدية مثل الصحف أو اللوحات الإعلانية. خلال السنوات الأخيرة، توجه أغلب ملاك العقارات والمكاتب العقارية إلى عرض عقاراتهم في دليل العقار لما يتمتع به من كفاءة وقدرة على الوصول لجمهور أوسع.
                        </p>
                    </section>

                    <!-- إحصائياتنا -->
                    <section class="p-about__section">
                        <h2 class="p-about__section-title">
                            <span class="p-about__section-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="18" y1="20" x2="18" y2="10"/>
                                    <line x1="12" y1="20" x2="12" y2="4"/>
                                    <line x1="6" y1="20" x2="6" y2="14"/>
                                </svg>
                            </span>
                            إحصائياتنا في الكويت
                        </h2>
                        
                        <div class="p-about__stats">
                            <div class="p-about__stat">
                                <div class="p-about__stat-value">+500K</div>
                                <div class="p-about__stat-label">تحميل للتطبيق</div>
                            </div>
                            <div class="p-about__stat">
                                <div class="p-about__stat-value">+25K</div>
                                <div class="p-about__stat-label">مستخدم مسجل</div>
                            </div>
                            <div class="p-about__stat">
                                <div class="p-about__stat-value">+90K</div>
                                <div class="p-about__stat-label">زائر نشط شهرياً</div>
                            </div>
                            <div class="p-about__stat">
                                <div class="p-about__stat-value">+5K</div>
                                <div class="p-about__stat-label">إعلان جديد شهرياً</div>
                            </div>
                            <div class="p-about__stat">
                                <div class="p-about__stat-value">+4K</div>
                                <div class="p-about__stat-label">اتصال يومياً</div>
                            </div>
                        </div>
                    </section>

                    <!-- تاريخنا -->
                    <section class="p-about__section">
                        <h2 class="p-about__section-title">
                            <span class="p-about__section-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <polyline points="12 6 12 12 16 14"/>
                                </svg>
                            </span>
                            تاريخنا
                        </h2>
                        <p class="p-about__text">
                            بدأ دليل العقار نشاطه في دولة الكويت بهدف تقديم منصة عقارية متطورة تلبي احتياجات السوق الكويتي. وبعد تزايد أعداد الزوار وتطور الإعلانات العقارية وتحقيق نجاح فاق التوقعات، قررنا التوسع والتطوير المستمر ليكون دليل العقار أول دليل عقاري شامل في الكويت.
                        </p>
                        <p class="p-about__text">
                            نسعى باستمرار لتحسين خدماتنا وتوفير تجربة مستخدم استثنائية، مع الحفاظ على البساطة والفعالية في الوصول إلى العقارات المناسبة.
                        </p>
                    </section>

                    <!-- هدفنا -->
                    <section class="p-about__section">
                        <h2 class="p-about__section-title">
                            <span class="p-about__section-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <circle cx="12" cy="12" r="6"/>
                                    <circle cx="12" cy="12" r="2"/>
                                </svg>
                            </span>
                            هدفنا
                        </h2>
                        
                        <div class="p-about__highlight">
                            <h3 class="p-about__highlight-title">رؤيتنا للمستقبل</h3>
                            <p class="p-about__text" style="margin-bottom: 0;">
                                يُعد قطاع العقارات في الكويت أحد المكونات الأساسية للاقتصاد الوطني، وقد شهد نموًا مستمرًا على مر السنين ومن المتوقع أن يستمر في النمو بفضل المبادرات الحكومية ومشاريع البنية التحتية. ورغم التحديات، فإن مستقبل القطاع العقاري في الكويت يبدو واعدًا.
                            </p>
                        </div>
                        
                        <p class="p-about__text">
                            يسعى دليل العقار لتسهيل التقاء البائع بالمشتري أو المالك بالمستأجر سواء بشكل مباشر أو عبر المكاتب العقارية. لذلك، نلتزم بخطة تسويقية فعالة وحملات إعلانية مستمرة لجذب آلاف الزوار والمُلاك الراغبين في بيع أو تأجير عقاراتهم، وربطهم بمن يبحث عن الشراء أو الإيجار.
                        </p>
                        <p class="p-about__text">
                            تم تصميم دليل العقار بطريقة بسيطة وعملية لتسهيل عملية الإعلان مجانًا لأصحاب العقارات أو الوسطاء، مع إمكانية إرفاق الصور ومقاطع الفيديو وكافة التفاصيل التي تساعد المشترين أو المستأجرين في اتخاذ القرار المناسب.
                        </p>
                    </section>

                    <!-- لماذا تختارنا -->
                    <section class="p-about__section">
                        <h2 class="p-about__section-title">
                            <span class="p-about__section-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                </svg>
                            </span>
                            لماذا تختار دليل العقار؟
                        </h2>
                        
                        <div class="p-about__features">
                            <div class="p-about__feature">
                                <div class="p-about__feature-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"/>
                                        <line x1="12" y1="8" x2="12" y2="16"/>
                                        <line x1="8" y1="12" x2="16" y2="12"/>
                                    </svg>
                                </div>
                                <h3 class="p-about__feature-title">إعلان مجاني</h3>
                                <p class="p-about__feature-desc">أضف إعلانك العقاري مجاناً بدون أي رسوم، مع إمكانية إرفاق الصور والتفاصيل الكاملة.</p>
                            </div>
                            
                            <div class="p-about__feature">
                                <div class="p-about__feature-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                        <circle cx="9" cy="7" r="4"/>
                                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                    </svg>
                                </div>
                                <h3 class="p-about__feature-title">جمهور واسع</h3>
                                <p class="p-about__feature-desc">الوصول إلى آلاف الباحثين عن عقارات في جميع مناطق الكويت.</p>
                            </div>
                            
                            <div class="p-about__feature">
                                <div class="p-about__feature-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
                                        <line x1="8" y1="21" x2="16" y2="21"/>
                                        <line x1="12" y1="17" x2="12" y2="21"/>
                                    </svg>
                                </div>
                                <h3 class="p-about__feature-title">تصميم سهل الاستخدام</h3>
                                <p class="p-about__feature-desc">واجهة بسيطة وعملية تسهل عملية البحث والإعلان عن العقارات.</p>
                            </div>
                            
                            <div class="p-about__feature">
                                <div class="p-about__feature-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                    </svg>
                                </div>
                                <h3 class="p-about__feature-title">أمان وموثوقية</h3>
                                <p class="p-about__feature-desc">نحرص على توفير بيئة آمنة وموثوقة لجميع المستخدمين.</p>
                            </div>
                        </div>
                    </section>

                    <!-- التزامنا -->
                    <section class="p-about__section">
                        <h2 class="p-about__section-title">
                            <span class="p-about__section-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                            </span>
                            التزامنا
                        </h2>
                        <p class="p-about__text">
                            نسعى إلى تعزيز مكانتنا كأكبر منصة لتسويق العقارات في الكويت، وتحسين تجربة المستخدمين من خلال استخدام أحدث لغات وتقنيات البرمجة، وتوفير مستوى عالٍ من الدعم والحلول الفعّالة التي تلبّي احتياجاتهم ومتطلباتهم بما يتماشى مع تطورات سوق العقار الكويتي.
                        </p>
                    </section>

                    <!-- CTA -->
                    <div class="p-about__cta">
                        <h2 class="p-about__cta-title">ابدأ الآن مع دليل العقار</h2>
                        <p class="p-about__cta-text">انضم إلى آلاف المستخدمين واستفد من خدماتنا المجانية</p>
                        <a href="{{ route('frontend.posts.create') }}" class="p-about__cta-btn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            أضف إعلانك مجاناً
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </main>
@endsection
