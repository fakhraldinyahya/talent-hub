<?php
require_once '../config/config.php';
require_once '../config/db.php';
require_once '../classes/User.php';
require_once '../classes/Group.php';

// التحقق مما إذا كان المستخدم مسجل الدخول
if (!isLoggedIn()) {
    redirect('login.php');
}

// التحقق من معرف المجموعة
if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('groups/index.php');
}

$group_id = sanitize($_GET['id']);

// إنشاء كائنات الفئات
$database = new Database();
$user = new User($database);
$group = new Group($database);

// الحصول على معلومات المجموعة
$group_data = $group->getGroupById($group_id);

if (!$group_data) {
    flash('المجموعة غير موجودة', 'danger');
    redirect('groups/index.php');
}

// التحقق مما إذا كان المستخدم مشرفًا في المجموعة
if (!$group->isGroupAdmin($group_id, $_SESSION['user_id'])) {
    flash('غير مصرح لك بإدارة هذه المجموعة', 'danger');
    redirect('groups/view.php?id=' . $group_id);
}

// معالجة إجراءات إدارة المجموعة
if (isset($_GET['action'])) {
    $action = sanitize($_GET['action']);

    // إضافة عضو
    if ($action === 'add_member' && isset($_GET['user_id'])) {
        $user_id = sanitize($_GET['user_id']);

        if ($group->addMember($group_id, $user_id, 'member')) {
            flash('تمت إضافة العضو بنجاح', 'success');
        } else {
            flash('حدث خطأ أثناء إضافة العضو', 'danger');
        }

        redirect('groups/manage.php?id=' . $group_id);
    }

    // إزالة عضو
    if ($action === 'remove_member' && isset($_GET['user_id'])) {
        $user_id = sanitize($_GET['user_id']);

        // التحقق مما إذا كان هذا هو منشئ المجموعة
        if ($user_id == $group_data->created_by) {
            flash('لا يمكن إزالة منشئ المجموعة', 'danger');
            redirect('groups/manage.php?id=' . $group_id);
        }

        if ($group->removeMember($group_id, $user_id)) {
            flash('تمت إزالة العضو بنجاح', 'success');
        } else {
            flash('حدث خطأ أثناء إزالة العضو', 'danger');
        }

        redirect('groups/manage.php?id=' . $group_id);
    }

    // جعل عضو مشرفًا
    if ($action === 'make_admin' && isset($_GET['user_id'])) {
        $user_id = sanitize($_GET['user_id']);

        if ($group->changeMemberRole($group_id, $user_id, 'admin')) {
            flash('تم تعيين العضو كمشرف بنجاح', 'success');
        } else {
            flash('حدث خطأ أثناء تعيين العضو كمشرف', 'danger');
        }

        redirect('groups/manage.php?id=' . $group_id);
    }

    // إزالة صلاحيات المشرف
    if ($action === 'remove_admin' && isset($_GET['user_id'])) {
        $user_id = sanitize($_GET['user_id']);

        // التحقق مما إذا كان هذا هو منشئ المجموعة
        if ($user_id == $group_data->created_by) {
            flash('لا يمكن إزالة صلاحيات المشرف من منشئ المجموعة', 'danger');
            redirect('groups/manage.php?id=' . $group_id);
        }

        if ($group->changeMemberRole($group_id, $user_id, 'member')) {
            flash('تمت إزالة صلاحيات المشرف بنجاح', 'success');
        } else {
            flash('حدث خطأ أثناء إزالة صلاحيات المشرف', 'danger');
        }

        redirect('groups/manage.php?id=' . $group_id);
    }

    // حذف المجموعة
    if ($action === 'delete_group') {
        // التحقق مما إذا كان المستخدم هو منشئ المجموعة
        if ($_SESSION['user_id'] != $group_data->created_by) {
            flash('لا يمكن حذف المجموعة إلا بواسطة منشئها', 'danger');
            redirect('groups/manage.php?id=' . $group_id);
        }

        if ($group->deleteGroup($group_id)) {
            flash('تم حذف المجموعة بنجاح', 'success');
            redirect('groups/index.php');
        } else {
            flash('حدث خطأ أثناء حذف المجموعة', 'danger');
            redirect('groups/manage.php?id=' . $group_id);
        }
    }
}

// معالجة تحديث معلومات المجموعة
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $description = sanitize($_POST['description']);
    $image_name = $group_data->image; // الافتراضية الحالية
    if (isset($_FILES['group_image']) && $_FILES['group_image']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($_FILES['group_image']['type'], $allowed_types)) {
            $ext = pathinfo($_FILES['group_image']['name'], PATHINFO_EXTENSION);
            $new_name = uniqid('group_') . '.' . $ext;
            $upload_path = '../assets/uploads/profile/' . $new_name;
            if (move_uploaded_file($_FILES['group_image']['tmp_name'], $upload_path)) {
                $image_name = $new_name;
            } else {
                flash('فشل في رفع الصورة', 'danger');
            }
        } else {
            flash('نوع الصورة غير مدعوم. الرجاء استخدام JPG أو PNG أو GIF.', 'danger');
        }
    }else{
        flash('نوع الصورة غير مدعوم. الرجاء ggggاستخدام JPG أو PNG أو GIF.', 'danger');

    }
    if (empty($name)) {
        flash('اسم المجموعة مطلوب', 'danger');
    } else {
        $update_data = [
            'id' => $group_id,
            'name' => $name,
            'description' => $description,
            'image' => $image_name
        ];

        if ($group->updateGroup($update_data)) {
            flash('تم تحديث معلومات المجموعة بنجاح', 'success');

            // إعادة تحميل بيانات المجموعة
            $group_data = $group->getGroupById($group_id);
        } else {
            flash('حدث خطأ أثناء تحديث معلومات المجموعة', 'danger');
        }
    }
}

// الحصول على أعضاء المجموعة
$members = $group->getGroupMembers($group_id);

// تعيين عنوان الصفحة
$page_title = 'إدارة المجموعة: ' . $group_data->name;

require_once '../includes/header.php';
?>

<div class="container mt-3">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><?php echo $page_title; ?></h2>
                <a href="<?php echo URL_ROOT; ?>/groups/view.php?id=<?php echo $group_id; ?>" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-1"></i>العودة إلى المجموعة
                </a>
            </div>

            <!-- تحديث معلومات المجموعة -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">معلومات المجموعة</h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo $_SERVER['PHP_SELF'] . '?id=' . $group_id; ?>" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="name" class="form-label">اسم المجموعة</label>
                            <input type="text" name="name" id="name" class="form-control" value="<?php echo $group_data->name; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">وصف المجموعة</label>
                            <textarea name="description" id="description" class="form-control" rows="3"><?php echo $group_data->description; ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="group_image" class="form-label">صورة المجموعة</label>
                            <input type="file" name="group_image" id="group_image" class="form-control">
                            <?php if (!empty($group_data->image)): ?>
                                <img src="<?php echo URL_ROOT; ?>/assets/uploads/profile/<?php echo $group_data->image; ?>" alt="صورة المجموعة" class="mt-2" width="100">
                            <?php endif; ?>
                        </div>
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                            <?php if ($_SESSION['user_id'] == $group_data->created_by): ?>
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteGroupModal">
                                    <i class="fas fa-trash-alt me-1"></i>حذف المجموعة
                                </button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <!-- إدارة الأعضاء -->
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">إدارة الأعضاء (<?php echo count($members); ?>)</h5>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                        <i class="fas fa-user-plus me-1"></i>إضافة أعضاء
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>المستخدم</th>
                                    <th>الدور</th>
                                    <th>تاريخ الانضمام</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($members as $member): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="<?php echo URL_ROOT; ?>/assets/uploads/profile/<?php echo $member->profile_picture; ?>" class="rounded-circle me-2" width="32" height="32" alt="صورة المستخدم">
                                                <div>
                                                    <div><?php echo $member->full_name; ?></div>
                                                    <small class="text-muted">@<?php echo $member->username; ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($member->id == $group_data->created_by): ?>
                                                <span class="badge bg-secondary">منشئ</span>
                                            <?php endif; ?>

                                            <?php if ($member->role === 'admin'): ?>
                                                <span class="badge bg-primary">مشرف</span>
                                            <?php else: ?>
                                                <span class="badge bg-light text-dark">عضو</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo date('d/m/Y', strtotime($member->joined_at)); ?></td>
                                        <td>
                                            <?php if ($member->id === $_SESSION['user_id']): ?>
                                                <span class="text-muted">أنت</span>
                                            <?php else: ?>
                                                <div class="btn-group btn-group-sm">
                                                    <?php if ($member->role === 'member'): ?>
                                                        <a href="<?php echo URL_ROOT; ?>/groups/manage.php?id=<?php echo $group_id; ?>&action=make_admin&user_id=<?php echo $member->id; ?>" class="btn btn-outline-primary" title="جعله مشرفًا">
                                                            <i class="fas fa-user-shield"></i>
                                                        </a>
                                                    <?php elseif ($member->id !== $group_data->created_by): ?>
                                                        <a href="<?php echo URL_ROOT; ?>/groups/manage.php?id=<?php echo $group_id; ?>&action=remove_admin&user_id=<?php echo $member->id; ?>" class="btn btn-outline-warning" title="إزالة صلاحيات المشرف">
                                                            <i class="fas fa-user-minus"></i>
                                                        </a>
                                                    <?php endif; ?>

                                                    <?php if ($member->id !== $group_data->created_by): ?>
                                                        <a href="<?php echo URL_ROOT; ?>/groups/manage.php?id=<?php echo $group_id; ?>&action=remove_member&user_id=<?php echo $member->id; ?>" class="btn btn-outline-danger" title="إزالة من المجموعة" onclick="return confirm('هل أنت متأكد من رغبتك في إزالة هذا العضو؟');">
                                                            <i class="fas fa-user-times"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
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

<!-- مودال إضافة أعضاء -->
<div class="modal fade" id="addMemberModal" tabindex="-1" aria-labelledby="addMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addMemberModalLabel">إضافة أعضاء جدد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <input type="text" id="searchUser" class="form-control" placeholder="ابحث عن مستخدمين لإضافتهم...">
                </div>
                <div id="searchResults" class="list-group">
                    <!-- نتائج البحث ستظهر هنا -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- مودال حذف المجموعة -->
<div class="modal fade" id="deleteGroupModal" tabindex="-1" aria-labelledby="deleteGroupModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteGroupModalLabel">تأكيد حذف المجموعة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>هل أنت متأكد من رغبتك في حذف هذه المجموعة؟ سيتم حذف جميع الرسائل والبيانات المرتبطة بها.</p>
                <p class="text-danger">هذا الإجراء لا يمكن التراجع عنه.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <a href="<?php echo URL_ROOT; ?>/groups/manage.php?id=<?php echo $group_id; ?>&action=delete_group" class="btn btn-danger">حذف المجموعة</a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // البحث عن المستخدمين لإضافتهم
        const searchUser = document.getElementById('searchUser');
        const searchResults = document.getElementById('searchResults');

        searchUser.addEventListener('input', function() {
            const query = this.value.trim();
            if (query.length < 2) {
                searchResults.innerHTML = '';
                return;
            }

            // الحصول على قائمة الأعضاء الحاليين
            const currentMembers = [
                <?php foreach ($members as $member): ?>
                    <?php echo $member->id; ?>,
                <?php endforeach; ?>
            ];

            // بحث المستخدمين
            fetch(`${URL_ROOT}/search_users.php?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    searchResults.innerHTML = '';

                    // تصفية المستخدمين الذين ليسوا أعضاء بالفعل
                    const filteredUsers = data.filter(user => !currentMembers.includes(parseInt(user.id)));

                    if (filteredUsers.length === 0) {
                        searchResults.innerHTML = '<div class="text-center py-3 text-muted">لا توجد نتائج أو جميع المستخدمين أعضاء بالفعل</div>';
                        return;
                    }

                    filteredUsers.forEach(user => {
                        const item = document.createElement('a');
                        item.href = `${URL_ROOT}/groups/manage.php?id=${<?php echo $group_id; ?>}&action=add_member&user_id=${user.id}`;
                        item.className = 'list-group-item list-group-item-action d-flex align-items-center';

                        item.innerHTML = `
                        <img src="${URL_ROOT}/assets/uploads/profile/${user.profile_picture}" class="rounded-circle me-2" width="32" height="32" alt="صورة المستخدم">
                        <div>
                            <h6 class="mb-0">${user.full_name}</h6>
                            <small class="text-muted">@${user.username}</small>
                        </div>
                        <button class="btn btn-sm btn-outline-primary ms-auto">إضافة</button>
                    `;

                        searchResults.appendChild(item);
                    });
                })
                .catch(error => {
                    console.error('Error searching users:', error);
                    searchResults.innerHTML = '<div class="text-center py-3 text-danger">حدث خطأ أثناء البحث</div>';
                });
        });
    });
</script>

<?php require_once '../includes/footer.php'; ?>