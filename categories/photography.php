<?php
require_once '../config/config.php';
require_once '../includes/header.php';

// تعيين عنوان الصفحة
$page_title = 'قسم التصوير الفوتوغرافي';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- عنوان القسم -->
            <div class="text-center mb-4">
                <h1 class="display-4 text-primary">
                    <i class="fas fa-camera"></i> قسم التصوير الفوتوغرافي
                </h1>

            </div>

            <!-- صورة القسم -->
            <div class="mb-5 text-center">
                <img src="<?php echo URL_ROOT; ?>/assets/img/Photography group photo.jpg"
                    alt="قسم التصوير الفوتوغرافي"
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

                        <p> ندعم المصورين في تنمية حسهم البصري من خلال دورات تجريبية تعزز مهاراتهم في استخدام الكاميرا، الإضاءة، وتحرير الصور</p>

                    </div>
                </div>
            </div>

            <!-- الدورات التدريبية -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="card-title h4 mb-3">
                        <i class="fas fa-video text-primary me-2"></i>الدورات التدريبية
                    </h2>
                    <p class="card-text mb-4">اختر الدورة التي تناسبك وابدأ رحلتك في تعلم التصوير الفوتوغرافي:</p>

                    <div class="row">
                        <!-- دورة 1 -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-img-top ratio ratio-16x9">
                                    <iframe src="https://www.youtube.com/embed/videoseries?list=PL7l5DODoBYhEr10E3nZBzRIEJ4IlFHMK7" title="دورة التصوير الفوتوغرافي" allowfullscreen></iframe>
                                </div>
                                <div class="card-body">
                                    <h3 class="h5 card-title">دورة التصوير للمبتدئين</h3>
                                    <p class="card-text text-muted small">تعلم أساسيات التصوير الفوتوغرافي من الصفر</p>
                                </div>
                                <div class="card-footer bg-white">
                                    <a href="https://youtube.com/playlist?list=PL7l5DODoBYhEr10E3nZBzRIEJ4IlFHMK7" target="_blank" class="btn btn-primary btn-sm w-100">
                                        <i class="fas fa-play me-1"></i> ابدأ المشاهدة
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-img-top ratio ratio-16x9">
                                <iframe width="560" height="315" src="https://www.youtube.com/embed/KHTokQKSkQA?si=R433wLUwoarSKfoB" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                                                                    </div>
                                <div class="card-body">
                                    <h3 class="h5 card-title">كورس التصوير الفوتوغرافي للمبتدئين</h3>
                                    <p class="card-text text-muted small">كورس التصوير الفوتوغرافي للمبتدئين</p>
                                </div>
                                <div class="card-footer bg-white">
                                    <a href="https://www.youtube.com/playlist?list=PLPQigXQMQTuTbHxJSCmSxGIqfyJ002TS3" target="_blank" class="btn btn-primary btn-sm w-100">
                                        <i class="fas fa-play me-1"></i> ابدأ المشاهدة
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-img-top ratio ratio-16x9">
                                <iframe width="560" height="315" src="https://www.youtube.com/embed/iXKhnuQpE2E?si=Zhj8Dkek0hWrAbXc" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                                </div>
                                <div class="card-body">
                                    <h3 class="h5 card-title">اساسيات التصوير بالجوال بشكل احترافي</h3>
                                    <p class="card-text text-muted small">اساسيات التصوير بالجوال بشكل احترافي</p>
                                </div>
                                <div class="card-footer bg-white">
                                    <a href="https://www.youtube.com/watch?v=iXKhnuQpE2E" target="_blank" class="btn btn-primary btn-sm w-100">
                                        <i class="fas fa-play me-1"></i> ابدأ المشاهدة
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- دورة 2 -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-img-top ratio ratio-16x9 bg-light d-flex align-items-center justify-content-center">
                                    <i class="fas fa-camera-retro fa-3x text-secondary"></i>
                                </div>
                                <div class="card-body">
                                    <h3 class="h5 card-title">دورة تصوير البورتريه</h3>
                                    <p class="card-text text-muted small">تعلم فن تصوير الأشخاص بشكل احترافي</p>
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
                                    <i class="fas fa-landscape fa-3x text-secondary"></i>
                                </div>
                                <div class="card-body">
                                    <h3 class="h5 card-title">دورة تصوير الطبيعة</h3>
                                    <p class="card-text text-muted small">إتقان تصوير المناظر الطبيعية والهندسة المعمارية</p>
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
                                    <i class="fas fa-magic fa-3x text-secondary"></i>
                                </div>
                                <div class="card-body">
                                    <h3 class="h5 card-title">دورة معالجة الصور</h3>
                                    <p class="card-text text-muted small">تعلم استخدام برامج تحرير الصور بشكل احترافي</p>
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