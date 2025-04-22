<?php
require_once '../config/config.php';
require_once '../includes/header.php';

// تعيين عنوان الصفحة
$page_title = 'قسم الإلقاء والشعر';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- عنوان القسم -->
            <div class="text-center mb-4">
                <h1 class="display-4 text-primary">
                    <i class="fas fa-microphone-alt"></i> قسم الإلقاء والشعر
                </h1>

            </div>

            <!-- صورة القسم -->
            <div class="mb-5 text-center">
                <img src="<?php echo URL_ROOT; ?>/assets/img/Recitation and Poetry Group.jpg"
                    alt="قسم الإلقاء والشعر"
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

                        <p>في هذا القسم، نساعد أصحاب الصوت والتعبير على تطوير مهاراتهم من خلال دورات تجريبية تركز على الأداء، والتفاعل مع الجمهور، وكتابة الشعر.</p>

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
                                    <iframe src="https://www.youtube.com/embed/videoseries?list=PL0VjGd7nigDsCC1OrJgvFLRVTmKWOcusP" title="دورة فن الإلقاء الشعري" allowfullscreen></iframe>
                                </div>
                                <div class="card-body">
                                    <h3 class="h5 card-title">دورة فن الإلقاء الشعري</h3>
                                    <p class="card-text text-muted small">إتقان مهارات الإلقاء والتعبير عن المشاعر</p>
                                </div>
                                <div class="card-footer bg-white">
                                    <a href="https://youtube.com/playlist?list=PL0VjGd7nigDsCC1OrJgvFLRVTmKWOcusP" target="_blank" class="btn btn-primary btn-sm w-100">
                                        <i class="fas fa-play me-1"></i> ابدأ المشاهدة
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-img-top ratio ratio-16x9">
                                <iframe width="560" height="315" src="https://www.youtube.com/embed/videoseries?si=G9I8TYwCKeqE0zVz&amp;list=PLa2QD4cvP6Ed9fUAqOmXe0e6Y56i3ZoJp" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>                                
                                </div>
                                <div class="card-body">
                                    <h3 class="h5 card-title">دورة مهارات أساسية في الخطابة والإلقاء
                                    </h3>
                                    <p class="card-text text-muted small">دورة مهارات أساسية في الخطابة والإلقاء
                                    </p>
                                </div>
                                <div class="card-footer bg-white">
                                    <a href="https://www.youtube.com/playlist?list=PLa2QD4cvP6Ed9fUAqOmXe0e6Y56i3ZoJp" target="_blank" class="btn btn-primary btn-sm w-100">
                                        <i class="fas fa-play me-1"></i> ابدأ المشاهدة
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-img-top ratio ratio-16x9">
                                <iframe width="560" height="315" src="https://www.youtube.com/embed/1Q4Hi_Yy6PE?si=4TZFBBxwopBntF5c" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                                    </div>

                                    <div class="card-body">
                                    <h3 class="h5 card-title">ريقة كتابة الشعر النبطي
                                    </h3>
                                    <p class="card-text text-muted small">
                                    تعلم كتابة الشعر النبطي - كيف تكتب قصيدة موزونة بإسهل طريقة
                                    </p>
                                </div>
                                <div class="card-footer bg-white">
                                    <a href="https://www.youtube.com/watch?si=Qn_WOGpzerwXH6yx&v=1Q4Hi_Yy6PE&feature=youtu.be" target="_blank" class="btn btn-primary btn-sm w-100">
                                        <i class="fas fa-play me-1"></i> ابدأ المشاهدة
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- مادة 2 -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-img-top ratio ratio-16x9 bg-light d-flex align-items-center justify-content-center">
                                    <i class="fas fa-pen-fancy fa-3x text-secondary"></i>
                                </div>
                                <div class="card-body">
                                    <h3 class="h5 card-title">كتابة الشعر العمودي</h3>
                                    <p class="card-text text-muted small">أساسيات كتابة الشعر وفق الأوزان الخليلية</p>
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
                                    <i class="fas fa-book-open fa-3x text-secondary"></i>
                                </div>
                                <div class="card-body">
                                    <h3 class="h5 card-title">تحليل القصائد</h3>
                                    <p class="card-text text-muted small">فنون فهم وتحليل النصوص الشعرية</p>
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
                                    <i class="fas fa-theater-masks fa-3x text-secondary"></i>
                                </div>
                                <div class="card-body">
                                    <h3 class="h5 card-title">الشعر المسرحي</h3>
                                    <p class="card-text text-muted small">فنون كتابة الشعر الدرامي والمسرحي</p>
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