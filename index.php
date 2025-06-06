<?php
require_once 'config/config.php';
require_once 'config/db.php';

// إنشاء كائنات الفئات
$database = new Database();


// تعيين عنوان الصفحة
$page_title = 'الصفحة الرئيسية';

require_once 'includes/header.php';
?>


<section id="hero" class="hero section text-center overflow-hidden py-5">

  <!-- الصورة الأولى -->
  <div class="mb-4">
    <img src="assets/img/hero-1.jpeg" alt=""
      class="img-fluid w-100"
      style="object-fit: cover; max-height: 90vh;"
      data-aos="fade-in">
  </div>

  <!-- النص -->
  <div class="container my-5" data-aos="zoom-out" data-aos-delay="100">
    <div class="row justify-content-center">
      <div class="col-lg-10">
        <h1 class="display-5 fw-bold  mb-4">Talent Hub</h1>
        <p class="fs-5 fw-bold">
          منصتنا تهدف إلى اكتشاف ودعم الموهوبين في مختلف المجالات من خلال توفير بيئة رقمية تُمكّنهم
          من عرض مهاراتهم والانضمام إلى دورات تدريبية متخصصة تُنمّي قدراتهم. كما نتيح لأصحاب الأعمال والباحثين عن الكفاءات الوصول إلى مجموعة متميزة من المواهب، مما يساهم في خلق فرص عمل حقيقية وتبادل مثمر بين الموهبة وسوق العمل.
        </p>
      </div>
    </div>
  </div>

  <!-- الصورة الأخيرة -->
  <div>
    <img src="assets/img/hero-2.jpeg" alt=""
      class="img-fluid w-100"
      style="object-fit: cover; max-height: 90vh;"
      data-aos="fade-in">
  </div>

</section>


<!-- القسم الترحيبي -->
<div class="container mt-3">





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