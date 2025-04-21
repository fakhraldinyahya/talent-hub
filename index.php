<?php
require_once 'config/config.php';
require_once 'config/db.php';
require_once 'classes/Post.php';
require_once 'classes/User.php';

// إنشاء كائنات الفئات
$database = new Database();
$post = new Post($database);
$user = new User($database);

// الحصول على أحدث المنشورات للصفحة الرئيسية
$posts = $post->getLimitedPosts();

// الحصول على المستخدمين المميزين
$featured_users = null;
// $featured_users = $user->getFeaturedUsers(4);

// تعيين عنوان الصفحة
$page_title = 'الصفحة الرئيسية';

require_once 'includes/header.php';
?>


<!-- Hero Section -->
<section id="hero" class="hero section">

    <img src="assets/img/hero-bg.jpg" alt="" data-aos="fade-in">

    <div class="container text-center" data-aos="zoom-out" data-aos-delay="100">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h1 class="display-5 fw-bold">اكتشف المواهب وتواصل مع المبدعين</h1>
                <p>المواهب هي كنز ثمين، ويجب اكتشافها ودعمها لتحقيق مستقبل مشرق.</p>
            </div>
            <div class="col-lg-6">
                <div class="welcome-text">
                    <p class="lead">منصة <?php echo SITE_NAME; ?> تجمع الموهوبين من مختلف المجالات لعرض إبداعاتهم والتواصل مع الجمهور وتبادل الخبرات.</p>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-start mt-4">

                        <?php if (!isLoggedIn()): ?>
                            <a href="register.php" class="btn btn-primary btn-lg px-4 me-md-2">انضم الآن</a>
                            <a href="posts/index.php" class="btn btn-outline-secondary btn-lg px-4">استكشف المواهب</a>
                        <?php else: ?>
                            <a href="posts/create.php" class="btn btn-primary btn-lg px-4 me-md-2">أضف موهبتك</a>
                            <a href="posts/index.php" class="btn btn-outline-secondary btn-lg px-4">استكشف المواهب</a>
                        <?php endif; ?>
                        <a href="about.php" class="btn-get-started btn btn-lg px-4 me-md-2">من نحن</a>

                    </div>
                </div>
            </div>
        </div>
    </div>

</section>

<!-- القسم الترحيبي -->
<div class="container mt-5">





    <!-- قسم التسجيل -->
    <?php if (!isLoggedIn()): ?>
        <div class="row">
            <div class="col-12">
                <div class="bg-primary text-white p-5 rounded-3 text-center">
                    <h2 class="fw-bold">انضم إلينا الآن</h2>
                    <p class="lead">سجل معنا وابدأ في عرض موهبتك والتواصل مع المبدعين الآخرين</p>
                    <div class="d-grid gap-2 d-sm-flex justify-content-sm-center mt-4">
                        <a href="register.php" class="btn btn-light btn-lg px-4 me-sm-3">إنشاء حساب</a>
                        <a href="login.php" class="btn btn-outline-light btn-lg px-4">تسجيل الدخول</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>