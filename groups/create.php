<?php
require_once '../config/config.php';
require_once '../config/db.php';
require_once '../classes/User.php';
require_once '../classes/Group.php';

// التحقق من تسجيل الدخول وصلاحية المستخدم
if (!isLoggedIn()) {
    redirect('login.php');
}
if (!isAdmin()) {
    flash('ليس لديك صلاحية الوصول لهذه الصفحة', 'danger');
    redirect('index.php');
}

// إنشاء كائنات الفئات
$database = new Database();
$user = new User($database);
$group = new Group($database);

// تعيين عنوان الصفحة
$page_title = 'إنشاء مجموعة جديدة';

$errors = [];

// معالجة إرسال النموذج
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // الحصول على بيانات النموذج
    $name = sanitize($_POST['name']);
    $description = sanitize($_POST['description']);
    $members = isset($_POST['members']) ? $_POST['members'] : [];
    $image_name = null;

    // التحقق من الحقول المطلوبة
    if (empty($name)) {
        $errors[] = 'اسم المجموعة مطلوب';
    }

    // معالجة رفع الصورة
    if (!empty($_FILES['group_image']['name'])) {
        $image = $_FILES['group_image'];
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $image_extension = pathinfo($image['name'], PATHINFO_EXTENSION);

        if (!in_array(strtolower($image_extension), $allowed_extensions)) {
            $errors[] = 'صيغة الصورة غير مدعومة. يرجى رفع صورة بصيغ (JPG, JPEG, PNG, GIF).';
        } elseif ($image['size'] > 2 * 1024 * 1024) { // 2 ميجابايت
            $errors[] = 'حجم الصورة يجب ألا يتجاوز 2 ميجابايت.';
        } else {
            $image_name = uniqid('group_', true) . '.' . $image_extension;
            $upload_path = '../assets/uploads/profile/' . $image_name;

            if (!move_uploaded_file($image['tmp_name'], $upload_path)) {
                $errors[] = 'حدث خطأ أثناء رفع الصورة.';
            }
        }
    }

    // إذا لم تكن هناك أخطاء، قم بإنشاء المجموعة
    if (empty($errors)) {
        $group_data = [
            'name' => $name,
            'description' => $description,
            'created_by' => $_SESSION['user_id'],
            'image' => $image_name
        ];

        $group_id = $group->createGroup($group_data);

        if ($group_id) {
            // إضافة المستخدم الحالي كمشرف
            $group->addMember($group_id, $_SESSION['user_id'], 'admin');

            // إضافة الأعضاء المحددين
            foreach ($members as $member_id) {
                $group->addMember($group_id, $member_id, 'member');
            }

            flash('تم إنشاء المجموعة بنجاح', 'success');
            redirect('groups/view.php?id=' . $group_id);
        } else {
            $errors[] = 'حدث خطأ أثناء إنشاء المجموعة';
        }
    }
}

// الحصول على قائمة المستخدمين للاختيار منهم
$available_users = $user->getAvailableUsers($_SESSION['user_id']);

require_once '../includes/header.php';
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">إنشاء مجموعة جديدة</h4>
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
                        <div class="mb-3">
                            <label for="name" class="form-label">اسم المجموعة</label>
                            <input type="text" name="name" id="name" class="form-control" value="<?php echo isset($_POST['name']) ? $_POST['name'] : ''; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">وصف المجموعة</label>
                            <textarea name="description" id="description" class="form-control" rows="3"><?php echo isset($_POST['description']) ? $_POST['description'] : ''; ?></textarea>
                            <small class="text-muted">وصف مختصر للمجموعة والغرض منها (اختياري).</small>
                        </div>

                        <div class="mb-3">
                            <label for="group_image" class="form-label">صورة المجموعة</label>
                            <input type="file" name="group_image" id="group_image" class="form-control">
                            <small class="text-muted">يمكنك رفع صورة للمجموعة (اختياري).</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">أعضاء المجموعة</label>
                            <div class="input-group mb-2">
                                <input type="text" id="memberSearch" class="form-control" placeholder="ابحث عن مستخدمين لإضافتهم...">
                                <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div id="searchResults" class="list-group mb-3" style="max-height: 200px; overflow-y: auto;"></div>

                            <div id="selectedMembers" class="border rounded p-2" style="min-height: 100px;">
                                <p class="text-muted text-center mb-0" id="noMembersText">لم يتم اختيار أعضاء بعد.</p>
                                <div id="membersList" class="d-flex flex-wrap"></div>
                            </div>
                            <small class="text-muted">اختر الأعضاء الذين تريد إضافتهم إلى المجموعة.</small>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">إنشاء المجموعة</button>
                            <a href="<?php echo URL_ROOT; ?>/groups/index.php" class="btn btn-secondary">إلغاء</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>