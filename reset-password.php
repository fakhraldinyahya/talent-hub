<?php
// بدء الجلسة بشكل صحيح
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/config.php';
require_once 'config/db.php';
require_once 'classes/User.php';

// تسجيل الأخطاء
error_reporting(E_ALL);
ini_set('display_errors', 1);

$database = new Database();
$user = new User($database);
$errors = [];

// التحقق من وجود التوكن في الرابط
if (!isset($_GET['token'])) {
    error_log("Token missing in URL");
    $_SESSION['error'] = "رابط إعادة تعيين كلمة المرور غير صالح";
    header("Location: " . URL_ROOT . "/forgot-password.php");
    exit();
}

$token = sanitize($_GET['token']);
error_log("Processing token: " . $token);

// معالجة إرسال النموذج
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = sanitize($_POST["password"]);
    $confirm_password = sanitize($_POST["confirm_password"]);

    // التحقق من صحة المدخلات
    if (empty($password)) {
        $errors[] = "كلمة المرور مطلوبة";
    } elseif (strlen($password) < 8) {
        $errors[] = "يجب أن تتكون كلمة المرور من 8 أحرف على الأقل";
    }

    if ($password !== $confirm_password) {
        $errors[] = "كلمتا المرور غير متطابقتين";
    }

    // إذا لم تكن هناك أخطاء
    if (empty($errors)) {
        error_log("Validating token: " . $token);
        $userData = $user->verifyPasswordResetToken($token);

        if ($userData) {
            error_log("Token valid for user: " . $userData->email);

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            if ($user->updatePassword($userData->id, $hashed_password)) {
                $user->clearResetToken($userData->id);
                $_SESSION['success'] = "تم تحديث كلمة المرور بنجاح";
                header("Location: " . URL_ROOT . "/login.php");
                exit();
            } else {
                $errors[] = "فشل في تحديث كلمة المرور";
            }
        } else {
            error_log("Invalid or expired token: " . $token);
            $_SESSION['error'] = "الرابط غير صالح أو منتهي الصلاحية";
            header("Location: " . URL_ROOT . "/forgot-password.php");
            exit();
        }
    }
}

// التحقق من التوكن قبل عرض الصفحة
error_log("Verifying token before display: " . $token);
$userData = $user->verifyPasswordResetToken($token);
if (!$userData) {
    error_log("Invalid token when displaying page: " . $token);
    $_SESSION['error'] = "الرابط غير صالح أو منتهي الصلاحية";
    header("Location: " . URL_ROOT . "/forgot-password.php");
    exit();
}
?>



<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إعادة تعيين كلمة المرور - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .reset-container {
            max-width: 500px;
            margin: 50px auto;
        }

        .card-header {
            background-color: #cb8670;
            color: white;
        }

        .btn-primary {
            background-color: #cb8670;
            border-color: #cb8670;
        }

        .btn-primary:hover {
            background-color: #b57360;
            border-color: #b57360;
        }

        .password-strength {
            height: 5px;
            margin-top: 5px;
            margin-bottom: 10px;
            display: none;
        }

        .strength-weak {
            background-color: #dc3545;
            width: 25%;
        }

        .strength-medium {
            background-color: #ffc107;
            width: 50%;
        }

        .strength-strong {
            background-color: #28a745;
            width: 75%;
        }

        .strength-very-strong {
            background-color: #28a745;
            width: 100%;
        }
    </style>
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <!-- Breadcrumb Area -->
    <section class="breadcumb-area bg-img d-flex align-items-center justify-content-center"
        style="background-image: url(img/bg-img/bg-3.jpg); height: 300px; background-size: cover; background-position: center;">
        <div class="bradcumbContent">
            <h2 style="color: white; font-size: 36px; font-weight: bold; text-transform: uppercase;">إعادة تعيين كلمة المرور</h2>
        </div>
    </section>

    <!-- Reset Password Form -->
    <div class="container reset-container">
        <div class="card shadow">
            <div class="card-header text-center py-3">
                <h4 class="mb-0">إعادة تعيين كلمة المرور</h4>
            </div>
            <div class="card-body p-4">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?token=' . $token; ?>" method="POST" id="resetForm">
                    <div class="mb-3">
                        <label for="password" class="form-label">كلمة المرور الجديدة</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <div class="password-strength" id="passwordStrength"></div>
                        <div class="form-text">يجب أن تتكون كلمة المرور من 8 أحرف على الأقل</div>
                    </div>

                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">تأكيد كلمة المرور الجديدة</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        <div class="form-text">أعد إدخال كلمة المرور الجديدة</div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary py-2">تحديث كلمة المرور</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // مؤشر قوة كلمة المرور
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('passwordStrength');

            if (password.length === 0) {
                strengthBar.style.display = 'none';
                return;
            }

            strengthBar.style.display = 'block';

            // إعادة تعيين الألوان
            strengthBar.className = 'password-strength';

            // حساب القوة
            let strength = 0;

            // التحقق من الطول
            if (password.length >= 8) strength += 1;
            if (password.length >= 12) strength += 1;

            // تنوع الأحرف
            if (/[A-Z]/.test(password)) strength += 1;
            if (/[a-z]/.test(password)) strength += 1;
            if (/[0-9]/.test(password)) strength += 1;
            if (/[^A-Za-z0-9]/.test(password)) strength += 1;

            // تحديث شريط القوة
            if (strength <= 2) {
                strengthBar.classList.add('strength-weak');
            } else if (strength <= 4) {
                strengthBar.classList.add('strength-medium');
            } else if (strength <= 6) {
                strengthBar.classList.add('strength-strong');
            } else {
                strengthBar.classList.add('strength-very-strong');
            }
        });

        // التحقق من صحة الاستمارة
        document.getElementById('resetForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            if (password !== confirmPassword) {
                e.preventDefault();
                alert('كلمتا المرور غير متطابقتين');
                return false;
            }

            if (password.length < 8) {
                e.preventDefault();
                alert('يجب أن تتكون كلمة المرور من 8 أحرف على الأقل');
                return false;
            }

            return true;
        });
    </script>
</body>

</html>