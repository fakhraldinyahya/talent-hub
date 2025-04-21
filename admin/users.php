<?php
require_once '../config/config.php';
require_once '../config/db.php';
require_once '../classes/User.php';

// التحقق مما إذا كان المستخدم مسجل الدخول ومسؤول
if (!isLoggedIn() || !isAdmin()) {
    redirect('index.php');
}

// إنشاء كائنات الفئات
$database = new Database();
$user = new User($database);

// معالجة الإجراءات
if (isset($_GET['action'])) {
    $action = sanitize($_GET['action']);
    
    // حذف مستخدم
    if ($action === 'delete' && isset($_GET['id'])) {
        $id = sanitize($_GET['id']);
        
        // التحقق مما إذا كان المستخدم هو المشرف نفسه
        if ($id == $_SESSION['user_id']) {
            flash('لا يمكنك حذف حسابك الخاص', 'danger');
            redirect('admin/users.php');
        }
        
        if ($user->deleteUser($id)) {
            flash('تم حذف المستخدم بنجاح', 'success');
        } else {
            flash('حدث خطأ أثناء حذف المستخدم', 'danger');
        }
        
        redirect('admin/users.php');
    }
    
    // تغيير دور المستخدم
    if ($action === 'change_role' && isset($_GET['id']) && isset($_GET['role'])) {
        $id = sanitize($_GET['id']);
        $role = sanitize($_GET['role']);
        
        // التحقق مما إذا كان المستخدم هو المشرف نفسه
        if ($id == $_SESSION['user_id']) {
            flash('لا يمكنك تغيير دور حسابك الخاص', 'danger');
            redirect('admin/users.php');
        }
        
        if ($user->changeUserRole($id, $role)) {
            flash('تم تغيير دور المستخدم بنجاح', 'success');
        } else {
            flash('حدث خطأ أثناء تغيير دور المستخدم', 'danger');
        }
        
        redirect('admin/users.php');
    }
}

// البحث عن المستخدمين
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
if (!empty($search)) {
    $users_list = $user->searchUsers($search);
} else {
    $users_list = $user->getAllUsers();
}

// تعيين عنوان الصفحة
$page_title = 'إدارة المستخدمين';

require_once '../includes/header.php';
?>

<div class="container-fluid mt-3">
    <div class="row">
        
        <!-- المحتوى الرئيسي -->
        <div class="col-lg-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><?php echo $page_title; ?></h2>
                <form class="d-flex">
                    <input class="form-control me-2" type="search" name="search" placeholder="البحث عن مستخدمين..." aria-label="Search" value="<?php echo $search; ?>">
                    <button class="btn btn-outline-primary" type="submit">بحث</button>
                </form>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>المستخدم</th>
                                    <th>البريد الإلكتروني</th>
                                    <th>الدور</th>
                                    <th>تاريخ التسجيل</th>
                                    <th>آخر تحديث</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users_list as $index => $user_item): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="<?php echo URL_ROOT; ?>/assets/uploads/profile/<?php echo $user_item->profile_picture; ?>" class="rounded-circle me-2" width="32" height="32" alt="صورة المستخدم">
                                                <div>
                                                    <div><?php echo $user_item->full_name; ?></div>
                                                    <small class="text-muted">@<?php echo $user_item->username; ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo $user_item->email; ?></td>
                                        <td>
                                            <?php if ($user_item->role === 'admin'): ?>
                                                <span class="badge bg-danger">مشرف</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">مستخدم</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo date('d/m/Y', strtotime($user_item->created_at)); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($user_item->updated_at)); ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?php echo URL_ROOT; ?>/profile.php?username=<?php echo $user_item->username; ?>" class="btn btn-outline-primary" title="عرض الملف الشخصي">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                
                                                <?php if ($user_item->id !== $_SESSION['user_id']): ?>
                                                    <?php if ($user_item->role === 'user'): ?>
                                                        <a href="<?php echo URL_ROOT; ?>/admin/users.php?action=change_role&id=<?php echo $user_item->id; ?>&role=admin" class="btn btn-outline-warning" title="جعله مشرفًا" onclick="return confirm('هل أنت متأكد من رغبتك في جعل هذا المستخدم مشرفًا؟');">
                                                            <i class="fas fa-user-shield"></i>
                                                        </a>
                                                    <?php else: ?>
                                                        <a href="<?php echo URL_ROOT; ?>/admin/users.php?action=change_role&id=<?php echo $user_item->id; ?>&role=user" class="btn btn-outline-info" title="جعله مستخدمًا عاديًا" onclick="return confirm('هل أنت متأكد من رغبتك في جعل هذا المستخدم عاديًا؟');">
                                                            <i class="fas fa-user"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    
                                                    <a href="<?php echo URL_ROOT; ?>/admin/users.php?action=delete&id=<?php echo $user_item->id; ?>" class="btn btn-outline-danger" title="حذف" onclick="return confirm('هل أنت متأكد من رغبتك في حذف هذا المستخدم؟ سيتم حذف جميع منشوراته وبياناته أيضًا.');">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>