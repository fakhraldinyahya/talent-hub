<?php
require_once 'config/config.php';
require_once 'config/db.php';
require_once 'classes/User.php';



// إنشاء كائنات النظام
$database = new Database();
$user = new User($database);
$errors = [];

// استخدام PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

// معالجة طلب استعادة كلمة المرور
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = sanitize($_POST["email"]);

    // التحقق من صحة البريد الإلكتروني
    if (empty($email)) {
        $errors[] = "البريد الإلكتروني مطلوب";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "صيغة البريد الإلكتروني غير صالحة";
    }

    // إذا لم تكن هناك أخطاء
    if (empty($errors)) {
        // استخدام الدالة الجديدة بدلاً من القديمة
        $userData = $user->findUserByEmail($email);

        if ($userData) {
            $token = $user->createPasswordResetToken($email);

            if ($token) {
                // إعداد رابط إعادة تعيين كلمة المرور

                // في ملف forgot-password.php
                $resetURL = URL_ROOT . "/reset-password.php?token=" . urlencode($token);

                // إعداد البريد الإلكتروني
                $mail = new PHPMailer(true);

                try {
                    // إعداد خادم SMTP
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'hameedmansor39@gmail.com';
                    $mail->Password = 'mmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmm';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                    $mail->Port = 465;

                    // إعداد المرسل والمستلم
                    $mail->setFrom('hameedmansor39@gmail.com', 'Talent Hub');
                    $mail->addAddress($userData->email, $userData->username);

                    // محتوى البريد الإلكتروني
                    $mail->isHTML(true);
                    $mail->Subject = 'Recover your password- Talent Hub';
                    $mail->Body = "
                        <html dir='rtl'>
                        <head>
                            <title>استعادة كلمة المرور</title>
                            <style>
                                body { font-family: Arial, sans-serif; line-height: 1.6; }
                                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                                .button {
                                    display: inline-block;
                                    background-color: #cb8670;
                                    color: white;
                                    padding: 10px 20px;
                                    text-decoration: none;
                                    border-radius: 5px;
                                }
                            </style>
                        </head>
                        <body>
                            <div class='container'>
                                <h2>استعادة كلمة المرور</h2>
                                <p>مرحباً </p>
                                <p>لقد تلقينا طلباً لإعادة تعيين كلمة المرور الخاصة بحسابك.</p>
                                <p>الرجاء النقر على الزر أدناه لإعادة تعيين كلمة المرور:</p>
                                <p><a href='{$resetURL}' class='button'>إعادة تعيين كلمة المرور</a></p>
                                <p>إذا لم تطلب إعادة تعيين كلمة المرور، يمكنك تجاهل هذا البريد الإلكتروني.</p>
                                <p>ينتهي صلاحية هذا الرابط خلال ساعة واحدة.</p>
                                <p>شكراً لك،<br>فريق Talent Hub </p>
                            </div>
                        </body>
                        </html>
                    ";

                    $mail->send();
                    $_SESSION['success'] = "تم إرسال رابط استعادة كلمة المرور إلى بريدك الإلكتروني.";
                    redirect('login.php');
                } catch (Exception $e) {
                    $errors[] = "حدث خطأ أثناء إرسال البريد الإلكتروني. يرجى المحاولة مرة أخرى.";
                }
            } else {
                $errors[] = "حدث خطأ أثناء إنشاء رابط الاستعادة. يرجى المحاولة مرة أخرى.";
            }
        } else {
            $errors[] = "البريد الإلكتروني غير مسجل في النظام";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>استعادة كلمة المرور - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .forget-container {
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
    </style>
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <!-- Breadcrumb Area -->
    <section class="breadcumb-area bg-img d-flex align-items-center justify-content-center"
        style="background-image: url(img/bg-img/bg-3.jpg); height: 300px; background-size: cover; background-position: center;">
        <div class="bradcumbContent">
            <h2 style="color: white; font-size: 36px; font-weight: bold; text-transform: uppercase;">استعادة كلمة المرور</h2>
        </div>
    </section>

    <!-- Forget Password Form -->
    <div class="container forget-container">
        <div class="card shadow">
            <div class="card-header text-center py-3">
                <h4 class="mb-0">استعادة كلمة المرور</h4>
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

                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">البريد الإلكتروني</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                        <div class="form-text">أدخل البريد الإلكتروني المرتبط بحسابك</div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary py-2">إرسال رابط الاستعادة</button>
                    </div>
                </form>

                <div class="text-center mt-3">
                    <a href="login.php" class="text-decoration-none">العودة إلى صفحة تسجيل الدخول</a>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>