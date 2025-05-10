<?php
require_once 'config/config.php';
require_once 'config/db.php';
require_once 'classes/User.php';

// تهيئة الكائنات
$database = new Database();
$user = new User($database);
$errors = [];
$success = "";
$showForm = false;

// جلب التوكن من الرابط
$token = isset($_GET['token']) ? $_GET['token'] : null;

// التحقق من التوكن
if ($token) {
    $userData = $user->verifyPasswordResetToken($token);

    if ($userData) {
        $showForm = true;

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $password = sanitize($_POST['password']);
            $confirmPassword = sanitize($_POST['confirm_password']);

            // التحقق من الحقول
            if (empty($password) || empty($confirmPassword)) {
                $errors[] = "يرجى إدخال كلمة المرور وتأكيدها.";
            } elseif ($password !== $confirmPassword) {
                $errors[] = "كلمتا المرور غير متطابقتين.";
            } elseif (strlen($password) < 6) {
                $errors[] = "كلمة المرور يجب أن تكون 6 أحرف على الأقل.";
            }

            // إذا لم توجد أخطاء، قم بالتحديث
            if (empty($errors)) {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $updateSuccess = $user->updatePassword($userData->id, $hashed);

                if ($updateSuccess) {
                    $success = "تم تحديث كلمة المرور بنجاح. يمكنك الآن تسجيل الدخول.";
                    $showForm = false;
                } else {
                    $errors[] = "حدث خطأ أثناء تحديث كلمة المرور.";
                }
            }
        }
    } else {
        $errors[] = "الرابط غير صالح أو منتهي الصلاحية.";
    }
} else {
    $errors[] = "رمز إعادة تعيين كلمة المرور غير موجود.";
}
?>

<?php require_once 'includes/header.php'; ?>



<div class="container">
    <h2>إعادة تعيين كلمة المرور</h2>

    <!-- عرض الأخطاء -->
    <?php if (!empty($errors)): ?>
        <div class="error" style="color: red; border: 1px solid red; padding: 10px; margin-bottom: 20px; border-radius: 5px;">
            <?php foreach ($errors as $err) echo "<p>$err</p>"; ?>
        </div>
    <?php endif; ?>

    <!-- عرض النجاح -->
    <?php if (!empty($success)): ?>
        <div class="success" style="color: green; border: 1px solid green; padding: 10px; margin-bottom: 20px; border-radius: 5px;">
            <p><?= $success ?></p>
            <p><a href="login.php" style="color: blue;">تسجيل الدخول</a></p>
        </div>
    <?php endif; ?>

    <!-- عرض نموذج إعادة تعيين كلمة المرور -->
    <?php if ($showForm): ?>
        <form method="POST" style="max-width: 400px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;">
            <div style="margin-bottom: 15px;">
                <label for="password" style="display: block; font-weight: bold;">كلمة المرور الجديدة:</label>
                <input type="password" name="password" id="password" required style="width: 100%; padding: 8px; margin-top: 5px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label for="confirm_password" style="display: block; font-weight: bold;">تأكيد كلمة المرور:</label>
                <input type="password" name="confirm_password" id="confirm_password" required style="width: 100%; padding: 8px; margin-top: 5px;">
            </div>

            <div>
                <input type="submit" value="تحديث كلمة المرور" style="background-color: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">
            </div>
        </form>
    <?php endif; ?>
</div>


<?php require_once 'includes/footer.php'; ?>