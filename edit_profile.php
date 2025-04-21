<?php
require_once 'config/config.php';
require_once 'config/db.php';
require_once 'classes/User.php';

// التحقق مما إذا كان المستخدم مسجل الدخول
if (!isLoggedIn()) {
    redirect('login.php');
}

// إنشاء كائنات الفئات
$database = new Database();
$user = new User($database);

// الحصول على بيانات المستخدم الحالي
$user_data = $user->getUserById($_SESSION['user_id']);

if (!$user_data) {
    flash('حدث خطأ. يرجى تسجيل الدخول مرة أخرى', 'danger');
    redirect('logout.php');
}

// تعيين عنوان الصفحة
$page_title = 'تعديل الملف الشخصي';

$errors = [];

// معالجة إرسال النموذج
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // الحصول على بيانات النموذج
    $full_name = sanitize($_POST['full_name']);
    $bio = sanitize($_POST['bio']);
    
    // التحقق من الحقول المطلوبة
    if (empty($full_name)) {
        $errors[] = 'الاسم الكامل مطلوب';
    }
    
    // معالجة الصورة الشخصية إذا تم تحميلها
    $profile_picture = $user_data->profile_picture;
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 2 * 1024 * 1024; // 2MB
        
        if (!in_array($_FILES['profile_picture']['type'], $allowed_types)) {
            $errors[] = 'نوع الملف غير مدعوم. الأنواع المدعومة هي: JPEG، PNG، GIF.';
        } elseif ($_FILES['profile_picture']['size'] > $max_size) {
            $errors[] = 'حجم الملف يتجاوز 2 ميجابايت';
        } else {
            // حذف الصورة القديمة إذا لم تكن الصورة الافتراضية
            if ($profile_picture !== 'default.jpeg' && file_exists(PROFILE_PIC_DIR . $profile_picture)) {
                unlink(PROFILE_PIC_DIR . $profile_picture);
            }
            
            $filename = uniqid() . '_' . $_FILES['profile_picture']['name'];
            $upload_path = PROFILE_PIC_DIR . $filename;
            
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_path)) {
                $profile_picture = $filename;
            } else {
                $errors[] = 'حدث خطأ أثناء تحميل الصورة';
            }
        }
    }
    
    // إذا لم تكن هناك أخطاء، قم بتحديث الملف الشخصي
    if (empty($errors)) {
        $update_data = [
            'id' => $_SESSION['user_id'],
            'full_name' => $full_name,
            'bio' => $bio,
            'profile_picture' => $profile_picture
        ];
        
        if ($user->updateUser($update_data)) {
            // تحديث معلومات الجلسة
            $_SESSION['profile_picture'] = $profile_picture;
            
            flash('تم تحديث الملف الشخصي بنجاح', 'success');
            redirect('profile.php?username=' . $user_data->username);
        } else {
            $errors[] = 'حدث خطأ أثناء تحديث الملف الشخصي';
        }
    }
}

require_once 'includes/header.php';
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">تعديل الملف الشخصي</h4>
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
                        <div class="mb-4 text-center">
                            <img src="<?php echo URL_ROOT; ?>/assets/uploads/profile/<?php echo $user_data->profile_picture; ?>" class="rounded-circle" width="120" height="120" alt="صورة الملف الشخصي">
                        </div>
                        <div class="mb-3">
                            <label for="username" class="form-label">اسم المستخدم</label>
                            <input type="text" class="form-control" value="<?php echo $user_data->username; ?>" disabled readonly>
                            <small class="text-muted">لا يمكن تغيير اسم المستخدم.</small>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">البريد الإلكتروني</label>
                            <input type="email" class="form-control" value="<?php echo $user_data->email; ?>" disabled readonly>
                        </div>
                        <div class="mb-3">
                            <label for="full_name" class="form-label">الاسم الكامل</label>
                            <input type="text" name="full_name" id="full_name" class="form-control" value="<?php echo $user_data->full_name; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="bio" class="form-label">نبذة عني</label>
                            <textarea name="bio" id="bio" class="form-control" rows="4"><?php echo $user_data->bio; ?></textarea>
                            <small class="text-muted">أخبرنا عن نفسك وعن مواهبك (حتى 500 حرف).</small>
                        </div>
                        <div class="mb-3">
                            <label for="profile_picture" class="form-label">تغيير الصورة الشخصية</label>
                            <input type="file" name="profile_picture" id="profile_picture" class="form-control">
                            <small class="text-muted">الأنواع المدعومة: JPEG، PNG، GIF. الحد الأقصى للحجم: 2 ميجابايت.</small>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                            <a href="<?php echo URL_ROOT; ?>/profile.php?username=<?php echo $user_data->username; ?>" class="btn btn-secondary">إلغاء</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>