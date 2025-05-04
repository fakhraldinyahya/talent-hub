<?php
require_once '../config/config.php';
require_once '../includes/header.php';

// تعيين عنوان الصفحة
$page_title = 'قسم القراءة';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">




            <!-- نبذة عن القسم -->
            <div class="card shadow-sm mb-5">
                <div class="card-body">



                    <div class="card shadow-sm mt-3">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"> إذا لم تجد قسم خاص بموهبتك تواصل معنا </h5>
                        </div>
                        <div class="card-body">
                            <a href="<?php echo URL_ROOT; ?>/contact.php" class="btn  btn-primary">تواصل معنا </a>
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