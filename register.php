<?php
require_once 'config/config.php';
require_once 'config/db.php';
require_once 'classes/User.php';

// التحقق إذا كان المستخدم مسجل الدخول بالفعل
if (isLoggedIn()) {
    redirect('index.php');
}

// إنشاء كائن قاعدة البيانات
$database = new Database();
$user = new User($database);

$errors = [];

// التحقق مما إذا كان النموذج مرسل
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // الحصول على بيانات النموذج
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $password = sanitize($_POST['password']);
    $confirm_password = sanitize($_POST['confirm_password']);
    $full_name = sanitize($_POST['full_name']);
    $category = sanitize($_POST['category'] ?? '');
    
    // التحقق من الحقول المطلوبة
    if (empty($username)) {
        $errors[] = 'اسم المستخدم مطلوب';
    } elseif (strlen($username) < 3 || strlen($username) > 50) {
        $errors[] = 'اسم المستخدم يجب أن يكون بين 3 و 50 حرفًا';
    } elseif ($user->findUserByUsername($username)) {
        $errors[] = 'اسم المستخدم مستخدم بالفعل';
    }
    if (empty($category)) {
        $errors[] = 'يرجى اختيار فئة الموهبة.';
    }
    if (empty($email)) {
        $errors[] = 'البريد الإلكتروني مطلوب';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'البريد الإلكتروني غير صالح';
    } elseif ($user->findUserByEmail($email)) {
        $errors[] = 'البريد الإلكتروني مستخدم بالفعل';
    }
    
    if (empty($password)) {
        $errors[] = 'كلمة المرور مطلوبة';
    } elseif (strlen($password) < 6) {
        $errors[] = 'كلمة المرور يجب أن تكون على الأقل 6 أحرف';
    }
    
    if ($password !== $confirm_password) {
        $errors[] = 'كلمة المرور وتأكيد كلمة المرور غير متطابقين';
    }
    
    if (empty($full_name)) {
        $errors[] = 'الاسم الكامل مطلوب';
    }
    
    // معالجة صورة الملف الشخصي إذا تم تحميلها
    $profile_picture = 'default.jpeg';
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 2 * 1024 * 1024; // 2MB
        
        if (!in_array($_FILES['profile_picture']['type'], $allowed_types)) {
            $errors[] = 'نوع الملف غير مدعوم. الأنواع المدعومة هي: JPEG، PNG، GIF.';
        } elseif ($_FILES['profile_picture']['size'] > $max_size) {
            $errors[] = 'حجم الملف يتجاوز 2 ميجابايت';
        } else {
            $filename = uniqid() . '_' . $_FILES['profile_picture']['name'];
            $upload_path = PROFILE_PIC_DIR . $filename;
            
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_path)) {
                $profile_picture = $filename;
            } else {
                $errors[] = 'حدث خطأ أثناء تحميل الصورة';
            }
        }
    }
    
    // إذا لم تكن هناك أخطاء، قم بتسجيل المستخدم
    if (empty($errors)) {
        $user_data = [
            'username' => $username,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'full_name' => $full_name,
            'profile_picture' => $profile_picture,
            'category' => $category,
            'role' => 'user'
        ];
        
        if ($user->register($user_data)) {
            flash('تم التسجيل بنجاح! يمكنك الآن تسجيل الدخول.', 'success');
            redirect('login.php');
        } else {
            $errors[] = 'حدث خطأ أثناء التسجيل. يرجى المحاولة مرة أخرى.';
        }
    }
}
?>

<?php require_once 'includes/header.php'; ?>

<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="text-center mb-0">إنشاء حساب جديد</h4>
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
                    
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label">اسم المستخدم</label>
                                <input type="text" name="username" id="username" class="form-control" value="<?php echo isset($_POST['username']) ? $_POST['username'] : ''; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">البريد الإلكتروني</label>
                                <input type="email" name="email" id="email" class="form-control" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="full_name" class="form-label">الاسم الكامل</label>
                            <input type="text" name="full_name" id="full_name" class="form-control" value="<?php echo isset($_POST['full_name']) ? $_POST['full_name'] : ''; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="category" class="form-label">فئة الموهبة *</label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">-- اختر فئة --</option>
                                <option value="programming" <?= (($_POST['category'] ?? '') == 'programming') ? 'selected' : '' ?>>البرمجة</option>
                                <option value="engineering" <?= (($_POST['category'] ?? '') == 'civil_engineering') ? 'selected' : '' ?>>الهندسة </option>
                                <option value="artificial_intelligence" <?= (($_POST['category'] ?? '') == 'artificial_intelligence') ? 'selected' : '' ?>> الذكاء الاصطناعي</option>
                                <option value="design" <?= (($_POST['category'] ?? '') == 'design') ? 'selected' : '' ?>> التصميم</option>
                                <option value="drawing" <?= (($_POST['category'] ?? '') == 'drawing') ? 'selected' : '' ?>>الرسم</option>
                                <option value="music" <?= (($_POST['category'] ?? '') == 'music') ? 'selected' : '' ?>>الموسيقى</option>
                                <option value="photography" <?= (($_POST['category'] ?? '') == 'photography') ? 'selected' : '' ?>>التصوير</option>
                                <option value="writing" <?= (($_POST['category'] ?? '') == 'writing') ? 'selected' : '' ?>>الكتابة</option>
                                <option value="acting" <?= (($_POST['category'] ?? '') == 'acting') ? 'selected' : '' ?>>التمثيل</option>
                                <option value="other" <?= (($_POST['category'] ?? '') == 'other') ? 'selected' : '' ?>>أخرى</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">كلمة المرور</label>
                                <input type="password" name="password" id="password" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="confirm_password" class="form-label">تأكيد كلمة المرور</label>
                                <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="profile_picture" class="form-label">الصورة الشخصية (اختياري)</label>
                            <input type="file" name="profile_picture" id="profile_picture" class="form-control">
                            <small class="text-muted">الأنواع المدعومة: JPEG، PNG، GIF. الحد الأقصى للحجم: 2 ميجابايت.</small>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">إنشاء حساب</button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <p class="mb-0">لديك حساب بالفعل؟ <a href="login.php">تسجيل الدخول</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>