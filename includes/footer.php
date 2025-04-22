<footer class=" light-background footer py-4 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <h5><?php echo SITE_NAME; ?></h5>
                <p>منصة لعرض المواهب والتواصل مع أصحاب المواهب المختلفة حول العالم.</p>
            </div>
            <div class="col-md-4">
                <h5>روابط سريعة</h5>
                <ul class="list-unstyled">
                    <li><a href="<?php echo URL_ROOT; ?>">الرئيسية</a></li>
                    <!-- <li><a href="<?php echo URL_ROOT; ?>/posts/index.php">استكشاف المواهب</a></li> -->
                    <li><a href="<?php echo URL_ROOT; ?>/about.php">من نحن</a></li>
                    <li><a href="<?php echo URL_ROOT; ?>/contact.php">اتصل بنا</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h5>تواصل معنا</h5>
                <ul class="list-unstyled">
                    <li><i class="fas fa-envelope me-2"></i>talenthubksa.1@gmail.com</li>
                    <li><i class="fas fa-phone me-2"></i>+966591159136</li>
                    <li class="mt-3 social-links d-flex">
                        <!-- <a href="#" class=" me-2"><i class="fab fa-facebook fa-lg"></i></a> -->
                        <!-- X (تويتر) -->
                        <a href="https://twitter.com/talenthub123" target="_blank" class="social-icon" title="تابعنا على X" style="color: #000;">
                            <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor">
                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                            </svg>
                        </a>
                        <!-- <a href="#" class=" me-2"><i class="fab fa-instagram fa-lg"></i></a> -->
                        <!-- <a href="#"><i class="fab fa-linkedin fa-lg"></i></a> -->
                    </li>
                </ul>
            </div>
        </div>
        <hr>
        <div class="text-center">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. جميع الحقوق محفوظة.</p>
        </div>
    </div>
</footer>

<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Custom JS -->
<script src="<?php echo URL_ROOT; ?>/assets/js/main.js"></script>
<button id="back-to-top" class="btn scroll-top btn-sm rounded-circle position-fixed bottom-0 end-0 m-4" style="display: none;">
    <i class="fas fa-arrow-up"></i>
</button>
<?php if (isset($page_scripts)): ?>
    <?php foreach ($page_scripts as $script): ?>
        <script src="<?php echo URL_ROOT; ?>/assets/js/<?php echo $script; ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>
<script>
    // زر العودة لأعلى الصفحة
    $(window).scroll(function() {
        if ($(this).scrollTop() > 300) {
            $('#back-to-top').fadeIn();
        } else {
            $('#back-to-top').fadeOut();
        }
    });

    $('#back-to-top').click(function() {
        $('html, body').animate({
            scrollTop: 0
        }, 300);
        return false;
    });

    // إخفاء رسائل النظام بعد فترة
    window.setTimeout(function() {
        $(".alert").not('.alert-permanent').fadeTo(500, 0).slideUp(500, function() {
            $(this).remove();
        });
    }, 5000);
</script>
</body>

</html>