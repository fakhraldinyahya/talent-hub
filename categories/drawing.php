<?php
require_once '../config/config.php';
require_once '../includes/header.php';

// تعيين عنوان الصفحة
$page_title = 'قسم الرسم';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- عنوان القسم -->
            <div class="text-center mb-4">
                <h1 class="display-4 text-primary">
                    <i class="fas fa-paint-brush"></i> قسم الرسم
                </h1>

            </div>

            <!-- صورة القسم -->
            <div class="mb-5 text-center">
                <img src="<?php echo URL_ROOT; ?>/assets/img/Drawing group picture.jpg"
                    alt="قسم الرسم"
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

                        <p> نمنح الرسامين بيئة محفزة للإبداع، مع دورات تجريبية تدعمهم في تحسين تقنياتهم وتوسيع خيالهم الفني.
                        </p>

                    </div>
                </div>
            </div>

            <!-- الدورات التدريبية -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="card-title h4 mb-3">
                        <i class="fas fa-video text-primary me-2"></i>الدورات التدريبية
                    </h2>
                    <p class="card-text mb-4">اختر الدورة التي تناسبك وابدأ رحلتك في تعلم الرسم:</p>

                    <div class="row">
                        <!-- دورة 1 -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-img-top ratio ratio-16x9">
                                    <iframe src="https://www.youtube.com/embed/videoseries?list=PLGZpo1d-ULrnSwwtwsirDbIq9MGnwEyRk" title="دورة تعلم الرسم للمبتدئين" allowfullscreen></iframe>
                                </div>
                                <div class="card-body">
                                    <h3 class="h5 card-title">دورة الرسم للمبتدئين</h3>
                                    <p class="card-text text-muted small">تعلم أساسيات الرسم من الصفر حتى الاحتراف</p>
                                </div>
                                <div class="card-footer bg-white">
                                    <a href="https://youtube.com/playlist?list=PLGZpo1d-ULrnSwwtwsirDbIq9MGnwEyRk" target="_blank" class="btn btn-primary btn-sm w-100">
                                        <i class="fas fa-play me-1"></i> ابدأ المشاهدة
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-img-top ratio ratio-16x9">
                                <iframe width="560" height="315" src="https://www.youtube.com/embed/videoseries?si=yNsQiwStTasHT8sa&amp;list=PLszusRlhWcGeVGCWH29Pd60x8qN4hL4p5" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>                                
                                </div>
                                <div class="card-body">
                                    <h3 class="h5 card-title">كورس رسم من الصفر للاحتراف في شهر</h3>
                                    <p class="card-text text-muted small">كورس رسم من الصفر للاحتراف في شهر</p>
                                </div>
                                <div class="card-footer bg-white">
                                    <a href="https://www.youtube.com/playlist?list=PLszusRlhWcGeVGCWH29Pd60x8qN4hL4p5" target="_blank" class="btn btn-primary btn-sm w-100">
                                        <i class="fas fa-play me-1"></i> ابدأ المشاهدة
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-img-top ratio ratio-16x9">
                                <iframe width="560" height="315" src="https://www.youtube.com/embed/videoseries?si=9u0-7KWeSHKLRZ8m&amp;list=PLybkHkyYYaxSZk2xf1sssISEW10-3PizV" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>                                
                                </div>
                                <div class="card-body">
                                    <h3 class="h5 card-title">كورس تعلم الرسم بالالوان المائية</h3>
                                    <p class="card-text text-muted small">كورس تعلم الرسم بالالوان المائية</p>
                                </div>
                                <div class="card-footer bg-white">
                                    <a href="https://www.youtube.com/playlist?list=PLybkHkyYYaxSZk2xf1sssISEW10-3PizV" target="_blank" class="btn btn-primary btn-sm w-100">
                                        <i class="fas fa-play me-1"></i> ابدأ المشاهدة
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- دورة 2 -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-img-top ratio ratio-16x9 bg-light d-flex align-items-center justify-content-center">
                                    <i class="fas fa-palette fa-3x text-secondary"></i>
                                </div>
                                <div class="card-body">
                                    <h3 class="h5 card-title">دورة الألوان المائية</h3>
                                    <p class="card-text text-muted small">إتقان تقنيات الرسم بالألوان المائية</p>
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
                                    <i class="fas fa-desktop fa-3x text-secondary"></i>
                                </div>
                                <div class="card-body">
                                    <h3 class="h5 card-title">دورة الرسم الرقمي</h3>
                                    <p class="card-text text-muted small">تعلم استخدام برامج الرسم الرقمي</p>
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
                                    <i class="fas fa-portrait fa-3x text-secondary"></i>
                                </div>
                                <div class="card-body">
                                    <h3 class="h5 card-title">دورة رسم البورتريه</h3>
                                    <p class="card-text text-muted small">تعلم رسم الوجوه والتعبيرات بشكل احترافي</p>
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