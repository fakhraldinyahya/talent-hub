<?php
require_once 'config/config.php';
require_once 'config/db.php';
require_once 'classes/User.php';
require_once 'classes/Chat.php';

// التحقق إذا كان المستخدم مسجل الدخول بالفعل
if (isLoggedIn()) {
    redirect('index.php');
}

// إنشاء كائن قاعدة البيانات
$database = new Database();
$user = new User($database);
$chat = new Chat($database);
$errors = [];

// التحقق مما إذا كان النموذج مرسل
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // الحصول على بيانات النموذج
    $email = sanitize($_POST['email']);
    $password = sanitize($_POST['password']);

    // التحقق من الحقول المطلوبة
    if (empty($email)) {
        $errors[] = 'البريد الإلكتروني مطلوب';
    }

    if (empty($password)) {
        $errors[] = 'كلمة المرور مطلوبة';
    }

    // إذا لم تكن هناك أخطاء، حاول تسجيل الدخول
    if (empty($errors)) {
        $login_result = $user->login($email, $password);

        if ($login_result) {
            // إنشاء جلسة وتوجيه المستخدم
            $_SESSION['user_id'] = $login_result->id;
            $_SESSION['username'] = $login_result->username;
            $_SESSION['user_role'] = $login_result->role;
            $chat->updateUserStatus($_SESSION['user_id'], true);
            if ($login_result->role === 'admin') {
                redirect('admin/index.php');
            } else {
                redirect('index.php');
            }
        } else {
            $errors[] = 'البريد الإلكتروني أو كلمة المرور غير صحيحة';
        }
    }
}
?>

<?php require_once 'includes/header.php'; ?>

<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="text-center mb-0">تسجيل الدخول</h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                        <div class="mb-3">
                            <label for="email" class="form-label">البريد الإلكتروني</label>
                            <input type="email" name="email" id="email" class="form-control" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">كلمة المرور</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">تسجيل الدخول</button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <p class="mb-0">ليس لديك حساب؟ <a href="register.php">إنشاء حساب جديد</a></p>
                </div>

                <div class="text-center mt-3">
                    <a href="forget.php" class="btn btn-link">Forgot Password?</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>