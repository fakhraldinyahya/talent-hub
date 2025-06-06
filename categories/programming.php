<?php
require_once '../config/config.php';
require_once '../includes/header.php';

// تعيين عنوان الصفحة
$page_title = 'قسم البرمجة';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- عنوان القسم -->
            <div class="text-center mb-4">
                <h1 class="display-4 text-primary">
                    <i class="fas fa-code"></i> قسم البرمجة
                </h1>

            </div>

            <!-- صورة القسم -->
            <div class="mb-5 text-center">
                <img src="<?php echo URL_ROOT; ?>/assets/img/Programming group image.jpg"
                    alt="قسم البرمجة"
                    class="img-fluid rounded shadow"
                    style="max-height: 300px; object-fit: cover;">

            </div>

            <!-- نبذة عن القسم -->
            <div class="card shadow-sm mb-5">
                <div class="card-body">
                    <h2 class="card-title h4 mb-3">
                        <i class="fas fa-info-circle text-primary me-2"></i>نبذة عن القسم
                    </h2>
                    <div class="card-text">
                        <p>في منصتنا، نتيح للمبرمجين فرص تعلّم وتطوير من خلال دورات تجريبية تساعدهم على تنمية مهاراتهم في البرمجة وبناء مشاريع تقنية مميزة.</p>
                    </div>
                </div>
            </div>

            <!-- الدورات التدريبية -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="card-title h4 mb-3">
                        <i class="fas fa-video text-primary me-2"></i>الدورات التدريبية
                    </h2>
                    <p class="card-text mb-4">اختر الدورة التي تناسبك وابدأ رحلتك في تعلم البرمجة:</p>

                    <div class="row">
                        <!-- دورة 1 -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-img-top ratio ratio-16x9">
                                    <iframe src="https://www.youtube.com/embed/videoseries?list=PLoP3S2S1qTfBCtTYJ2dyy3mpn7aWAAjdN" title="دورة تعلم البرمجة للمبتدئين" allowfullscreen></iframe>
                                </div>
                                <div class="card-body">
                                    <h3 class="h5 card-title">دورة البرمجة للمبتدئين</h3>
                                    <p class="card-text text-muted small">تعلم أساسيات البرمجة من الصفر حتى الاحتراف</p>
                                </div>
                                <div class="card-footer bg-white">
                                    <a href="https://youtube.com/playlist?list=PLoP3S2S1qTfBCtTYJ2dyy3mpn7aWAAjdN" target="_blank" class="btn btn-primary btn-sm w-100">
                                        <i class="fas fa-play me-1"></i> ابدأ المشاهدة
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-img-top ratio ratio-16x9">
                                <iframe width="560" height="315" src="https://www.youtube.com/embed/videoseries?si=50Ba2JuVnl5M1h91&amp;list=PLknwEmKsW8OuTqUDaFRBiAViDZ5uI3VcE" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                                                                </div>
                                <div class="card-body">
                                    <h3 class="h5 card-title">كورس جافا سكريبت كامل</h3>
                                    <p class="card-text text-muted small">تعلم جافا سكربت  من الصفر حتى الاحتراف</p>
                                </div>
                                <div class="card-footer bg-white">
                                    <a href="https://www.youtube.com/playlist?list=PLknwEmKsW8OuTqUDaFRBiAViDZ5uI3VcE" target="_blank" class="btn btn-primary btn-sm w-100">
                                        <i class="fas fa-play me-1"></i> ابدأ المشاهدة
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-img-top ratio ratio-16x9">
                                <iframe width="560" height="315" src="https://www.youtube.com/embed/videoseries?si=37528f-7AUdSYKtQ&amp;list=PLknwEmKsW8OtLRQPTLms79499meY2D6ij" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                                </div>
                                <div class="card-body">
                                    <h3 class="h5 card-title">كورس html كامل بالعربي </h3>
                                    <p class="card-text text-muted small">تعلم html   من الصفر حتى الاحتراف</p>
                                </div>
                                <div class="card-footer bg-white">
                                    <a href="https://www.youtube.com/playlist?list=PLknwEmKsW8OtLRQPTLms79499meY2D6ij" target="_blank" class="btn btn-primary btn-sm w-100">
                                        <i class="fas fa-play me-1"></i> ابدأ المشاهدة
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- دورة 2 -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-img-top ratio ratio-16x9 bg-light d-flex align-items-center justify-content-center">
                                    <i class="fas fa-laptop-code fa-3x text-secondary"></i>
                                </div>
                                <div class="card-body">
                                    <h3 class="h5 card-title">دورة تطوير الويب</h3>
                                    <p class="card-text text-muted small">تعلم HTML, CSS, JavaScript وإنشاء مواقع ويب متكاملة</p>
                                </div>
                                <div class="card-footer bg-white">
                                    <button class="btn btn-outline-secondary btn-sm w-100" disabled>
                                        قريباً
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- دورة 3 -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-img-top ratio ratio-16x9 bg-light d-flex align-items-center justify-content-center">
                                    <i class="fab fa-python fa-3x text-secondary"></i>
                                </div>
                                <div class="card-body">
                                    <h3 class="h5 card-title">دورة بايثون</h3>
                                    <p class="card-text text-muted small">تعلم لغة بايثون من الأساسيات إلى التطبيقات المتقدمة</p>
                                </div>
                                <div class="card-footer bg-white">
                                    <button class="btn btn-outline-secondary btn-sm w-100" disabled>
                                        قريباً
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- دورة 4 -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-img-top ratio ratio-16x9 bg-light d-flex align-items-center justify-content-center">
                                    <i class="fas fa-mobile-alt fa-3x text-secondary"></i>
                                </div>
                                <div class="card-body">
                                    <h3 class="h5 card-title">دورة تطوير التطبيقات</h3>
                                    <p class="card-text text-muted small">تعلم بناء تطبيقات الهاتف باستخدام Flutter</p>
                                </div>
                                <div class="card-footer bg-white">
                                    <button class="btn btn-outline-secondary btn-sm w-100" disabled>
                                        قريباً
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?php
require_once '../includes/footer.php';
?>