<?php
require_once '../config/config.php';
require_once '../includes/header.php';

// تعيين عنوان الصفحة
$page_title = 'قسم القراءة';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- عنوان القسم -->
            <div class="text-center mb-4">
                <h1 class="display-4 text-primary">
                    <i class="fas fa-book-open"></i> قسم القراءة
                </h1>

            </div>

            <!-- صورة القسم -->
            <div class="mb-5 text-center">
                <img src="<?php echo URL_ROOT; ?>/assets/img/Reading group.jpg"
                    alt="قسم القراءة"
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

                        <p>نوفر لمحبي القراءة دورات تجريبية تساعدهم على تنمية مهارات التحليل، الفهم العميق، والنقاش البنّاء حول المحتوى المقروء.
                        </p>

                    </div>
                </div>
            </div>

            <!-- الدورات والمواد التعليمية -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="card-title h4 mb-3">
                        <i class="fas fa-video text-primary me-2"></i>المواد التعليمية
                    </h2>
                    <p class="card-text mb-4">اختر المادة التي تناسبك واغتنم فرصة التعلّم:</p>

                    <div class="row">
                        <!-- مادة 1 -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-img-top ratio ratio-16x9">
                                    <iframe src="https://www.youtube.com/embed/M0daKVq6xbg" title="تعلم القراءة السريعة" allowfullscreen></iframe>
                                </div>
                                <div class="card-body">
                                    <h3 class="h5 card-title">دورة القراءة السريعة</h3>
                                    <p class="card-text text-muted small">تعلم كيف تقرأ بسرعة وفعالية</p>
                                </div>
                                <div class="card-footer bg-white">
                                    <a href="https://youtu.be/M0daKVq6xbg" target="_blank" class="btn btn-primary btn-sm w-100">
                                        <i class="fas fa-play me-1"></i> ابدأ المشاهدة
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-img-top ratio ratio-16x9">
                                <iframe width="560" height="315" src="https://www.youtube.com/embed/1POFF5RQjyM?si=XWMW5bbcMFBjOk5r" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                                </div>
                                <div class="card-body">
                                    <h3 class="h5 card-title">كيف تقرأ وتفهم وتنقد الكتب - علي وكتاب                                    </h3>
                                    <p class="card-text text-muted small">كيف تقرأ وتفهم وتنقد الكتب - علي وكتاب
                                    </p>
                                </div>
                                <div class="card-footer bg-white">
                                    <a href="https://www.youtube.com/watch?si=7ihVJr7o4cUDn-Kh&v=1POFF5RQjyM&feature=youtu.be" target="_blank" class="btn btn-primary btn-sm w-100">
                                        <i class="fas fa-play me-1"></i> ابدأ المشاهدة
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-img-top ratio ratio-16x9">
                                <iframe width="560" height="315" src="https://www.youtube.com/embed/j5WRlRxeOMA?si=ehYxdXqJYoNse8bb" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                                                                </div>
                                <div class="card-body">
                                    <h3 class="h5 card-title">افضل 5 كتب 📖 يجب ان تقرأهم الأن مفيدين جدا - ناصر العقيل -
                                    </h3>
                                    <p class="card-text text-muted small">افضل 5 كتب 📖 يجب ان تقرأهم الأن مفيدين جدا - ناصر العقيل -

                                    </p>
                                </div>
                                <div class="card-footer bg-white">
                                    <a href="https://www.youtube.com/watch?v=j5WRlRxeOMA" target="_blank" class="btn btn-primary btn-sm w-100">
                                        <i class="fas fa-play me-1"></i> ابدأ المشاهدة
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- مادة 2 -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-img-top ratio ratio-16x9 bg-light d-flex align-items-center justify-content-center">
                                    <i class="fas fa-book fa-3x text-secondary"></i>
                                </div>
                                <div class="card-body">
                                    <h3 class="h5 card-title">ملخصات الكتب</h3>
                                    <p class="card-text text-muted small">أهم الأفكار من أفضل الكتب العالمية</p>
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
                                    <i class="fas fa-glasses fa-3x text-secondary"></i>
                                </div>
                                <div class="card-body">
                                    <h3 class="h5 card-title">تحليل النصوص الأدبية</h3>
                                    <p class="card-text text-muted small">فنون فهم وتحليل الأعمال الأدبية</p>
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
                                    <i class="fas fa-child fa-3x text-secondary"></i>
                                </div>
                                <div class="card-body">
                                    <h3 class="h5 card-title">القراءة للأطفال</h3>
                                    <p class="card-text text-muted small">كيف تختار الكتب المناسبة لأطفالك</p>
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