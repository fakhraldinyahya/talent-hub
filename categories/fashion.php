<?php
require_once '../config/config.php';
require_once '../includes/header.php';

// تعيين عنوان الصفحة
$page_title = 'قسم تصميم الأزياء';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- عنوان القسم -->
            <div class="text-center mb-4">
                <h1 class="display-4 text-primary">
                    <i class="fas fa-tshirt"></i> قسم تصميم الأزياء
                </h1>

            </div>

            <!-- صورة القسم -->
            <div class="mb-5 text-center">
                <img src="<?php echo URL_ROOT; ?>/assets/img/Fashion design.jpg"
                    alt="قسم تصميم الأزياء"
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



                        <p>للمهتمين بالموضة، نوفر في هذا القسم دورات تجريبية تساعدهم على تطوير ذوقهم الفني وتعزيز مهاراتهم في التصميم والخياطة وتنسيق الألوان.</p>

                    </div>
                </div>
            </div>

            <!-- الدورات والمواد التعليمية -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="card-title h4 mb-3">
                        <i class="fas fa-video text-primary me-2"></i>المواد التعليمية
                    </h2>
                    <p class="card-text mb-4">اختر المادة التي تناسبك وابدأ رحلتك في عالم تصميم الأزياء:</p>

                    <div class="row">
                        <!-- مادة 1 -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-img-top ratio ratio-16x9">
                                    <iframe src="https://www.youtube.com/embed/videoseries?list=PLf18s3UpkwsANazye5p1vK2Trd0_JBjBt" title="دورة تصميم الأزياء للمبتدئين" allowfullscreen></iframe>
                                </div>
                                <div class="card-body">
                                    <h3 class="h5 card-title">دورة تصميم الأزياء</h3>
                                    <p class="card-text text-muted small">تعلم أساسيات تصميم الأزياء من الصفر</p>
                                </div>
                                <div class="card-footer bg-white">
                                    <a href="https://youtube.com/playlist?list=PLf18s3UpkwsANazye5p1vK2Trd0_JBjBt" target="_blank" class="btn btn-primary btn-sm w-100">
                                        <i class="fas fa-play me-1"></i> ابدأ المشاهدة
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-img-top ratio ratio-16x9">
                                <iframe width="560" height="315" src="https://www.youtube.com/embed/videoseries?si=mscoOi7PljJtwgs9&amp;list=PL4xf0iYXx-zj0BQ1xqRFt847zQEIBuqQ0" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                                                                </div>
                                <div class="card-body">
                                    <h3 class="h5 card-title"> تصميم الأزياء</h3>
                                    <p class="card-text text-muted small">تصميم الأزياء- منصة إدراك- جميع فيديوهات الوحدات
                                    </p>
                                </div>
                                <div class="card-footer bg-white">
                                    <a href="https://www.youtube.com/playlist?list=PL4xf0iYXx-zj0BQ1xqRFt847zQEIBuqQ0" target="_blank" class="btn btn-primary btn-sm w-100">
                                        <i class="fas fa-play me-1"></i> ابدأ المشاهدة
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-img-top ratio ratio-16x9">
                                <iframe width="560" height="315" src="https://www.youtube.com/embed/rJFXwl3VRXA?si=KRuWdBS9XaasdoNR" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                                                                                                </div>
                                <div class="card-body">
                                    <h3 class="h5 card-title"> تصميم الأزياء</h3>
                                    <p class="card-text text-muted small">تصميم الأزياء الرقمي ( الديجيتال ) علي برنامج procreate ✍️ الادوات ايباد مدعوم بقلم

                                    </p>
                                </div>
                                <div class="card-footer bg-white">
                                    <a href="https://www.youtube.com/watch?v=rJFXwl3VRXA" target="_blank" class="btn btn-primary btn-sm w-100">
                                        <i class="fas fa-play me-1"></i> ابدأ المشاهدة
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- مادة 2 -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-img-top ratio ratio-16x9 bg-light d-flex align-items-center justify-content-center">
                                    <i class="fas fa-cut fa-3x text-secondary"></i>
                                </div>
                                <div class="card-body">
                                    <h3 class="h5 card-title">دورة الخياطة الأساسية</h3>
                                    <p class="card-text text-muted small">تعلم تقنيات الخياطة الأساسية والمتقدمة</p>
                                </div>
                                <div class="card-footer bg-white">
                                    <button class="btn btn-outline-secondary btn-sm w-100" disabled>
                                        قريباً
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- مادة 3 -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-img-top ratio ratio-16x9 bg-light d-flex align-items-center justify-content-center">
                                    <i class="fas fa-vest fa-3x text-secondary"></i>
                                </div>
                                <div class="card-body">
                                    <h3 class="h5 card-title">تصميم الأزياء الرجالية</h3>
                                    <p class="card-text text-muted small">أساسيات تصميم الملابس الرجالية</p>
                                </div>
                                <div class="card-footer bg-white">
                                    <button class="btn btn-outline-secondary btn-sm w-100" disabled>
                                        قريباً
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- مادة 4 -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-img-top ratio ratio-16x9 bg-light d-flex align-items-center justify-content-center">
                                    <i class="fas fa-umbrella-beach fa-3x text-secondary"></i>
                                </div>
                                <div class="card-body">
                                    <h3 class="h5 card-title">تصميم الأزياء الصيفية</h3>
                                    <p class="card-text text-muted small">أحدث صيحات الموضة الصيفية</p>
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