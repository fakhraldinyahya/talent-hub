<?php
require_once 'config/config.php';
require_once 'config/db.php';
require_once 'classes/User.php';
require_once 'classes/Chat.php';
?>


<?php require_once 'includes/header.php'; ?>

<section id="hero" class="hero section position-relative overflow-hidden">

    <img src="assets/img/hero-bg.jpg" alt=""
        class="w-100 position-absolute top-0 start-0"
        style="z-index: -1; object-fit: contain; height: 90vh;"
        data-aos="fade-in">

    <div class="container text-center py-5" data-aos="zoom-out" data-aos-delay="100">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h1 class="display-5 fw-bold text-white">من نحن</h1>
                <p style="color: black !important;">
                    منصتنا تهدف إلى اكتشاف ودعم الموهوبين في مختلف المجالات من خلال توفير بيئة رقمية تُمكّنهم
                    من عرض مهاراتهم والانضمام إلى دورات تدريبية متخصصة تُنمّي قدراتهم.
                    كما نتيح لأصحاب الأعمال والباحثين عن الكفاءات الوصول إلى مجموعة متميزة من
                    المواهب، مما يساهم في خلق فرص عمل حقيقية وتبادل مثمر بين الموهبة وسوق العمل .
                </p>
            </div>
        </div>
    </div>

</section>




<?php require_once 'includes/footer.php'; ?>