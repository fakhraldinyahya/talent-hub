<?php
require_once 'config/config.php';
require_once 'config/db.php';
require_once 'classes/User.php';
require_once 'classes/Chat.php';
?>

<?php include 'includes/header.php'; ?>

<div class="container my-5 text-center">
    <!-- Font Awesome 6.5.0 CDN -->

    <h2 class="mb-4">تواصل معنا</h2>
    <p class="mb-4">نحن سعداء بتواصلك معنا، اختر الطريقة الأنسب لك من الطرق التالية:</p>

    <div class="d-flex justify-content-center gap-4 fs-2">
        <!-- البريد الإلكتروني -->
        <a href="mailto:talenthubksa.1@gmail.com" class="text-dark" title="راسلنا عبر البريد">
            <i class="fas fa-envelope"></i>
        </a>

        <!-- الواتساب -->
        <a href="https://wa.me/966591159136" target="_blank" class="text-success" title="راسلنا عبر واتساب">
            <i class="fab fa-whatsapp"></i>
        </a>




        <!-- X (تويتر) -->
        <a href="https://twitter.com/talenthub123" target="_blank" class="social-icon" title="تابعنا على X" style="color: #000;">
            <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor">
                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
            </svg>
        </a>


        <!-- اتصال مباشر -->
        <a href="tel:+966591159136" class="text-danger" title="اتصل بنا">
            <i class="fas fa-phone"></i>
        </a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>