<?php
require_once '../config/config.php';
require_once '../config/db.php';
require_once '../classes/Group.php';
require_once '../classes/User.php';

// التحقق مما إذا كان المستخدم مسجل الدخول ومسؤول
if (!isLoggedIn() || !isAdmin()) {
    redirect('index.php');
}

// إنشاء كائنات الفئات
$database = new Database();
$group = new Group($database);
$user = new User($database);

// معالجة الإجراءات
if (isset($_GET['action'])) {
    $action = sanitize($_GET['action']);
    
    // حذف مجموعة
    if ($action === 'delete' && isset($_GET['id'])) {
        $id = sanitize($_GET['id']);
        
        if ($group->deleteGroup($id)) {
            flash('تم حذف المجموعة بنجاح', 'success');
        } else {
            flash('حدث خطأ أثناء حذف المجموعة', 'danger');
        }
        
        redirect('admin/groups.php');
    }
}

// البحث عن المجموعات
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';

if (!empty($search)) {
    $groups_list = $group->searchGroups($search);
} else {
    $groups_list = $group->getAllGroups();
}

// تعيين عنوان الصفحة
$page_title = 'إدارة المجموعات';

require_once '../includes/header.php';
?>

<div class="container-fluid mt-5">
    <div class="row">
        <!-- المحتوى الرئيسي -->
        <div class="col-lg-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><?php echo $page_title; ?></h2>
                <form class="d-flex">
                    <input class="form-control me-2" type="search" name="search" placeholder="البحث عن مجموعات..." aria-label="Search" value="<?php echo $search; ?>">
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
                                    <th>اسم المجموعة</th>
                                    <th>منشئ المجموعة</th>
                                    <th>عدد الأعضاء</th>
                                    <th>تاريخ الإنشاء</th>
                                    <th>آخر تحديث</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($groups_list)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4">لا توجد مجموعات متطابقة مع معايير البحث</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($groups_list as $index => $group_item): ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo $group_item->name; ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="<?php echo URL_ROOT; ?>/assets/uploads/profile/<?php echo $group_item->profile_picture; ?>" class="rounded-circle me-2" width="24" height="24" alt="صورة المستخدم">
                                                    <a href="<?php echo URL_ROOT; ?>/profile.php?username=<?php echo $group_item->username; ?>" class="text-decoration-none"><?php echo $group_item->username; ?></a>
                                                </div>
                                            </td>
                                            <td><?php echo $group_item->members_count; ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($group_item->created_at)); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($group_item->updated_at)); ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="<?php echo URL_ROOT; ?>/groups/view.php?id=<?php echo $group_item->id; ?>" class="btn btn-outline-primary" title="عرض">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="<?php echo URL_ROOT; ?>/admin/groups.php?action=delete&id=<?php echo $group_item->id; ?>" class="btn btn-outline-danger" title="حذف" onclick="return confirm('هل أنت متأكد من رغبتك في حذف هذه المجموعة؟');">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>