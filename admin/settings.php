<?php
require_once '../config/config.php';
require_once '../config/db.php';
require_once '../classes/Admin.php';

// التحقق مما إذا كان المستخدم مسجل الدخول ومسؤول
if (!isLoggedIn() || !isAdmin()) {
    redirect('index.php');
}

// إنشاء كائنات الفئات
$database = new Database();
$admin = new Admin($database);

// الحصول على الإعدادات الحالية
$settings = $admin->getSettings();

// معالجة تحديث الإعدادات العامة
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_general'])) {
    $site_name = sanitize($_POST['site_name']);
    $site_description = sanitize($_POST['site_description']);
    $site_email = sanitize($_POST['site_email']);
    $allow_registrations = isset($_POST['allow_registrations']) ? 1 : 0;
    $enable_file_uploads = isset($_POST['enable_file_uploads']) ? 1 : 0;
    $max_file_size = sanitize($_POST['max_file_size']);
    
    $general_settings = [
        'site_name' => $site_name,
        'site_description' => $site_description,
        'site_email' => $site_email,
        'allow_registrations' => $allow_registrations,
        'enable_file_uploads' => $enable_file_uploads,
        'max_file_size' => $max_file_size
    ];
    
    if ($admin->updateSettings($general_settings)) {
        flash('تم تحديث الإعدادات العامة بنجاح', 'success');
        redirect('admin/settings.php');
    } else {
        flash('حدث خطأ أثناء تحديث الإعدادات', 'danger');
    }
}

// معالجة تحديث إعدادات الأمان
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_security'])) {
    $password_min_length = sanitize($_POST['password_min_length']);
    $account_lockout_attempts = sanitize($_POST['account_lockout_attempts']);
    $account_lockout_time = sanitize($_POST['account_lockout_time']);
    $require_email_verification = isset($_POST['require_email_verification']) ? 1 : 0;
    $enable_two_factor = isset($_POST['enable_two_factor']) ? 1 : 0;
    
    $security_settings = [
        'password_min_length' => $password_min_length,
        'account_lockout_attempts' => $account_lockout_attempts,
        'account_lockout_time' => $account_lockout_time,
        'require_email_verification' => $require_email_verification,
        'enable_two_factor' => $enable_two_factor
    ];
    
    if ($admin->updateSettings($security_settings)) {
        flash('تم تحديث إعدادات الأمان بنجاح', 'success');
        redirect('admin/settings.php');
    } else {
        flash('حدث خطأ أثناء تحديث الإعدادات', 'danger');
    }
}

// معالجة تحديث إعدادات المحتوى
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_content'])) {
    $allow_comments = isset($_POST['allow_comments']) ? 1 : 0;
    $moderate_comments = isset($_POST['moderate_comments']) ? 1 : 0;
    $allowed_file_types = isset($_POST['allowed_file_types']) ? implode(',', $_POST['allowed_file_types']) : '';
    $max_post_length = sanitize($_POST['max_post_length']);
    $enable_reporting = isset($_POST['enable_reporting']) ? 1 : 0;
    
    $content_settings = [
        'allow_comments' => $allow_comments,
        'moderate_comments' => $moderate_comments,
        'allowed_file_types' => $allowed_file_types,
        'max_post_length' => $max_post_length,
        'enable_reporting' => $enable_reporting
    ];
    
    if ($admin->updateSettings($content_settings)) {
        flash('تم تحديث إعدادات المحتوى بنجاح', 'success');
        redirect('admin/settings.php');
    } else {
        flash('حدث خطأ أثناء تحديث الإعدادات', 'danger');
    }
}

// تعيين عنوان الصفحة
$page_title = 'إعدادات الموقع';

require_once '../includes/header.php';
?>

<div class="container-fluid mt-5">
    <div class="row">
        <!-- القائمة الجانبية -->
        <div class="col-lg-2 mb-4">
            <?php require_once 'includes/sidebar.php'; ?>
        </div>
        
        <!-- المحتوى الرئيسي -->
        <div class="col-lg-10">
            <h2 class="mb-4"><?php echo $page_title; ?></h2>
            
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <ul class="nav nav-tabs card-header-tabs" id="settingsTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab" aria-controls="general" aria-selected="true">إعدادات عامة</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button" role="tab" aria-controls="security" aria-selected="false">إعدادات الأمان</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="content-tab" data-bs-toggle="tab" data-bs-target="#content" type="button" role="tab" aria-controls="content" aria-selected="false">إعدادات المحتوى</button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="settingsTabsContent">
                        <!-- إعدادات عامة -->
                        <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                                <div class="mb-3">
                                    <label for="site_name" class="form-label">اسم الموقع</label>
                                    <input type="text" class="form-control" id="site_name" name="site_name" value="<?php echo $settings['site_name'] ?? SITE_NAME; ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="site_description" class="form-label">وصف الموقع</label>
                                    <textarea class="form-control" id="site_description" name="site_description" rows="3"><?php echo $settings['site_description'] ?? ''; ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="site_email" class="form-label">البريد الإلكتروني للموقع</label>
                                    <input type="email" class="form-control" id="site_email" name="site_email" value="<?php echo $settings['site_email'] ?? ''; ?>">
                                </div>
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="allow_registrations" name="allow_registrations" <?php echo (isset($settings['allow_registrations']) && $settings['allow_registrations']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="allow_registrations">السماح بالتسجيلات الجديدة</label>
                                </div>
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="enable_file_uploads" name="enable_file_uploads" <?php echo (isset($settings['enable_file_uploads']) && $settings['enable_file_uploads']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="enable_file_uploads">تمكين تحميل الملفات</label>
                                </div>
                                <div class="mb-3">
                                    <label for="max_file_size" class="form-label">الحد الأقصى لحجم الملف (بالميجابايت)</label>
                                    <input type="number" class="form-control" id="max_file_size" name="max_file_size" value="<?php echo $settings['max_file_size'] ?? 10; ?>" min="1" max="100">
                                </div>
                                <button type="submit" name="update_general" class="btn btn-primary">حفظ الإعدادات</button>
                            </form>
                        </div>
                        
                        <!-- إعدادات الأمان -->
                        <div class="tab-pane fade" id="security" role="tabpanel" aria-labelledby="security-tab">
                            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                                <div class="mb-3">
                                    <label for="password_min_length" class="form-label">الحد الأدنى لطول كلمة المرور</label>
                                    <input type="number" class="form-control" id="password_min_length" name="password_min_length" value="<?php echo $settings['password_min_length'] ?? 6; ?>" min="6" max="20">
                                </div>
                                <div class="mb-3">
                                    <label for="account_lockout_attempts" class="form-label">عدد محاولات تسجيل الدخول الفاشلة قبل قفل الحساب</label>
                                    <input type="number" class="form-control" id="account_lockout_attempts" name="account_lockout_attempts" value="<?php echo $settings['account_lockout_attempts'] ?? 5; ?>" min="3" max="10">
                                </div>
                                <div class="mb-3">
                                    <label for="account_lockout_time" class="form-label">مدة قفل الحساب (بالدقائق)</label>
                                    <input type="number" class="form-control" id="account_lockout_time" name="account_lockout_time" value="<?php echo $settings['account_lockout_time'] ?? 30; ?>" min="5" max="1440">
                                </div>
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="require_email_verification" name="require_email_verification" <?php echo (isset($settings['require_email_verification']) && $settings['require_email_verification']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="require_email_verification">طلب التحقق من البريد الإلكتروني</label>
                                </div>
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="enable_two_factor" name="enable_two_factor" <?php echo (isset($settings['enable_two_factor']) && $settings['enable_two_factor']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="enable_two_factor">تمكين المصادقة الثنائية</label>
                                </div>
                                <button type="submit" name="update_security" class="btn btn-primary">حفظ الإعدادات</button>
                            </form>
                        </div>
                        
                        <!-- إعدادات المحتوى -->
                        <div class="tab-pane fade" id="content" role="tabpanel" aria-labelledby="content-tab">
                            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="allow_comments" name="allow_comments" <?php echo (isset($settings['allow_comments']) && $settings['allow_comments']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="allow_comments">السماح بالتعليقات</label>
                                </div>
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="moderate_comments" name="moderate_comments" <?php echo (isset($settings['moderate_comments']) && $settings['moderate_comments']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="moderate_comments">مراجعة التعليقات قبل النشر</label>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">أنواع الملفات المسموح بها</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="allowed_file_types[]" id="type_image" value="image" <?php echo (isset($settings['allowed_file_types']) && strpos($settings['allowed_file_types'], 'image') !== false) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="type_image">صور (JPEG, PNG, GIF)</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="allowed_file_types[]" id="type_video" value="video" <?php echo (isset($settings['allowed_file_types']) && strpos($settings['allowed_file_types'], 'video') !== false) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="type_video">فيديو (MP4, WebM, Ogg)</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="allowed_file_types[]" id="type_audio" value="audio" <?php echo (isset($settings['allowed_file_types']) && strpos($settings['allowed_file_types'], 'audio') !== false) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="type_audio">صوت (MP3, WAV, Ogg)</label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="max_post_length" class="form-label">الحد الأقصى لطول المنشور (بالأحرف)</label>
                                    <input type="number" class="form-control" id="max_post_length" name="max_post_length" value="<?php echo $settings['max_post_length'] ?? 5000; ?>" min="1000" max="50000">
                                </div>
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="enable_reporting" name="enable_reporting" <?php echo (isset($settings['enable_reporting']) && $settings['enable_reporting']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="enable_reporting">تمكين الإبلاغ عن المحتوى</label>
                                </div>
                                <button type="submit" name="update_content" class="btn btn-primary">حفظ الإعدادات</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>