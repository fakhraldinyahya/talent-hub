<?php
require_once 'config/config.php';

// تعيين عنوان الصفحة
$page_title = 'الصفحة غير موجودة - 404';

require_once 'includes/header.php';
?>

<div class="container mt-3">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="card shadow-sm">
                <div class="card-body py-5">
                    <h1 class="display-1 text-muted">404</h1>
                    <h2 class="mb-4">عذراً، الصفحة غير موجودة</h2>
                    <p class="lead">لم نتمكن من العثور على الصفحة التي تبحث عنها. ربما تم نقلها أو تغيير عنوانها أو حذفها.</p>
                    <div class="mt-4">
                        <a href="<?php echo URL_ROOT; ?>" class="btn btn-primary me-2">العودة للصفحة الرئيسية</a>
                        <button class="btn btn-outline-secondary" onclick="history.back()">العودة للصفحة السابقة</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>