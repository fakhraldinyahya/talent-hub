<?php
require_once '../config/config.php';
require_once '../config/db.php';
require_once '../classes/User.php';
require_once '../classes/Group.php';

// التحقق مما إذا كان المستخدم مسجل الدخول
if (!isLoggedIn()) {
    redirect('login.php');
}

// إنشاء كائنات الفئات
$database = new Database();
$user = new User($database);
$group = new Group($database);

// معالجة إجراءات المجموعات
if (isset($_GET['action'])) {
    $action = sanitize($_GET['action']);

    // الانضمام إلى مجموعة
    if ($action === 'join' && isset($_GET['id'])) {
        $group_id = sanitize($_GET['id']);

        if ($group->addMember($group_id, $_SESSION['user_id'], 'member')) {
            flash('تم الانضمام إلى المجموعة بنجاح', 'success');
        } else {
            flash('حدث خطأ أثناء الانضمام إلى المجموعة', 'danger');
        }

        redirect('groups/view.php?id=' . $group_id);
    }

    // مغادرة مجموعة
    if ($action === 'leave' && isset($_GET['id'])) {
        $group_id = sanitize($_GET['id']);

        // التحقق مما إذا كان المستخدم هو منشئ المجموعة
        $group_data = $group->getGroupById($group_id);
        if ($group_data && $group_data->created_by == $_SESSION['user_id']) {
            flash('لا يمكن لمنشئ المجموعة مغادرتها، يمكنك حذف المجموعة بدلاً من ذلك', 'warning');
            redirect('groups/view.php?id=' . $group_id);
        }

        if ($group->removeMember($group_id, $_SESSION['user_id'])) {
            flash('تمت مغادرة المجموعة بنجاح', 'success');
        } else {
            flash('حدث خطأ أثناء مغادرة المجموعة', 'danger');
        }

        redirect('groups/index.php');
    }
}

// الحصول على المجموعات التي ينتمي إليها المستخدم
$user_groups = $group->getUserGroups($_SESSION['user_id']);

// الحصول على المجموعات الأخرى التي لا ينتمي إليها المستخدم
$other_groups = $group->getOtherGroups($_SESSION['user_id']);

// البحث عن مجموعات
$search_query = isset($_GET['search']) ? sanitize($_GET['search']) : '';
if (!empty($search_query)) {
    $search_results = $group->searchGroups($search_query);
}

// تعيين عنوان الصفحة
$page_title = 'المجموعات';

require_once '../includes/header.php';
?>

<div class="container mt-3">
    <div class="row">
        <div class="col-lg-3 mb-4">
            <!-- بطاقة الإجراءات -->
            <?php if (isLoggedIn() && $user->isAdmin($_SESSION['user_id'])): ?>
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">الإجراءات</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="<?php echo URL_ROOT; ?>/groups/create.php" class="btn btn-primary">
                                <i class="fas fa-plus-circle me-1"></i>إنشاء مجموعة جديدة
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- بطاقة البحث -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">بحث عن مجموعات</h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET">
                        <div class="mb-3">
                            <input type="text" name="search" class="form-control" placeholder="اسم المجموعة أو الوصف..." value="<?php echo $search_query; ?>" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">بحث</button>
                            <?php if (!empty($search_query)): ?>
                                <a href="<?php echo URL_ROOT; ?>/groups/index.php" class="btn btn-outline-secondary mt-2">إلغاء البحث</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
            <?php if (isLoggedIn() && !$user->isAdmin($_SESSION['user_id'])): ?>
                <div class="card shadow-sm mt-3">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"> لم تجد مجموعة لموهبتك
                            تواصل معنا</h5>
                    </div>
                    <div class="card-body">
                        <a href="<?php echo URL_ROOT; ?>/contact.php" class="btn  btn-primary">تواصل معنا </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-lg-9">
            <?php if (!empty($search_query)): ?>
                <!-- نتائج البحث -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">نتائج البحث: "<?php echo htmlspecialchars($search_query); ?>"</h5>
                        <span class="badge bg-primary"><?php echo count($search_results); ?> نتيجة</span>
                    </div>
                    <div class="card-body">
                        <?php if (empty($search_results)): ?>
                            <p class="text-center text-muted">لا توجد مجموعات تطابق بحثك.</p>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach ($search_results as $search_group): ?>
                                    <div class="col-md-6 mb-4">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center mb-3">
                                                    <?php if (!empty($search_group->image)): ?>
                                                        <img src="<?php echo URL_ROOT; ?>/assets/uploads/profile/<?php echo $search_group->image; ?>"
                                                            class="rounded-circle me-3 object-fit-cover"
                                                            style="width: 50px; height: 50px;"
                                                            alt="<?php echo htmlspecialchars($search_group->name); ?>">
                                                    <?php else: ?>
                                                        <div class="bg-primary text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                            <i class="fas fa-users fa-lg"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div>
                                                        <h5 class="card-title mb-0"><?php echo $search_group->name; ?></h5>
                                                        <small class="text-muted"><?php echo $search_group->members_count; ?> عضو</small>
                                                    </div>
                                                </div>
                                                <p class="card-text"><?php echo strlen($search_group->description) > 100 ? substr($search_group->description, 0, 100) . '...' : $search_group->description; ?></p>
                                                <p class="card-text">
                                                    <small class="text-muted">
                                                        <i class="fas fa-user me-1"></i>المنشئ: <?php echo $search_group->username; ?>
                                                    </small>
                                                </p>
                                            </div>
                                            <div class="card-footer bg-white">
                                                <div class="d-grid">
                                                    <?php if ($group->isMember($search_group->id, $_SESSION['user_id'])): ?>
                                                        <a href="<?php echo URL_ROOT; ?>/groups/view.php?id=<?php echo $search_group->id; ?>" class="btn btn-outline-primary btn-sm">فتح المجموعة</a>
                                                    <?php else: ?>
                                                        <a href="<?php echo URL_ROOT; ?>/groups/index.php?action=join&id=<?php echo $search_group->id; ?>" class="btn btn-primary btn-sm">الانضمام للمجموعة</a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <!-- مجموعات المستخدم -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">مجموعاتي</h5>
                        <span class="badge bg-primary"><?php echo count($user_groups); ?> مجموعة</span>
                    </div>
                    <div class="card-body">
                        <?php if (empty($user_groups)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <h5>لم تنضم لأي مجموعة بعد</h5>
                                <p class="text-muted">انضم إلى المجموعات الموجودة أدناه أو قم بإنشاء مجموعتك الخاصة.</p>
                                <a href="<?php echo URL_ROOT; ?>/groups/create.php" class="btn btn-primary mt-2">إنشاء مجموعة جديدة</a>
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach ($user_groups as $user_group): ?>
                                    <div class="col-md-6 mb-4">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center mb-3">
                                                    <?php if (!empty($user_group->image)): ?>
                                                        <img src="<?php echo URL_ROOT; ?>/assets/uploads/profile/<?php echo $user_group->image; ?>"
                                                            class="rounded-circle me-3 object-fit-cover"
                                                            style="width: 50px; height: 50px;"
                                                            alt="<?php echo htmlspecialchars($user_group->name); ?>">
                                                    <?php else: ?>
                                                        <div class="bg-primary text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                            <i class="fas fa-users fa-lg"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div>
                                                        <h5 class="card-title mb-0"><?php echo $user_group->name; ?></h5>
                                                        <div>
                                                            <span class="badge bg-light text-dark me-1"><?php echo $user_group->members_count; ?> عضو</span>
                                                            <?php if ($user_group->unread_count > 0): ?>
                                                                <span class="badge bg-danger"><?php echo $user_group->unread_count; ?> جديد</span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <p class="card-text"><?php echo strlen($user_group->description) > 100 ? substr($user_group->description, 0, 100) . '...' : $user_group->description; ?></p>
                                                <p class="card-text">
                                                    <small class="text-muted">
                                                        <i class="fas fa-user me-1"></i>المنشئ: <?php echo $user_group->username; ?>
                                                    </small>
                                                </p>
                                                <?php if ($user_group->last_message): ?>
                                                    <div class="mt-2 p-2 bg-light rounded">
                                                        <small class="text-muted">آخر رسالة: <?php echo strlen($user_group->last_message) > 50 ? substr($user_group->last_message, 0, 50) . '...' : $user_group->last_message; ?></small>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="card-footer bg-white">
                                                <div class="d-flex gap-2">
                                                    <a href="<?php echo URL_ROOT; ?>/groups/view.php?id=<?php echo $user_group->id; ?>" class="btn btn-primary btn-sm flex-grow-1">فتح المجموعة</a>
                                                    <?php if ($user_group->user_role === 'admin'): ?>
                                                        <a href="<?php echo URL_ROOT; ?>/groups/manage.php?id=<?php echo $user_group->id; ?>" class="btn btn-outline-secondary btn-sm">
                                                            <i class="fas fa-cog"></i>
                                                        </a>
                                                    <?php else: ?>
                                                        <a href="<?php echo URL_ROOT; ?>/groups/index.php?action=leave&id=<?php echo $user_group->id; ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('هل أنت متأكد من رغبتك في مغادرة هذه المجموعة؟');">
                                                            <i class="fas fa-sign-out-alt"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- المجموعات الأخرى -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">مجموعات اكتشفها</h5>
                        <span class="badge bg-primary"><?php echo count($other_groups); ?> مجموعة</span>
                    </div>
                    <div class="card-body">
                        <?php if (empty($other_groups)): ?>
                            <p class="text-center text-muted">لا توجد مجموعات أخرى متاحة حاليًا.</p>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach ($other_groups as $other_group): ?>
                                    <div class="col-md-6 mb-4">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center mb-3">
                                                    <?php if (!empty($other_group->image)): ?>
                                                        <img src="<?php echo URL_ROOT; ?>/assets/uploads/profile/<?php echo $other_group->image; ?>"
                                                            class="rounded-circle me-3 object-fit-cover"
                                                            style="width: 50px; height: 50px;"
                                                            alt="<?php echo htmlspecialchars($other_group->name); ?>">
                                                    <?php else: ?>
                                                        <div class="bg-secondary text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                            <i class="fas fa-users fa-lg"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div>
                                                        <h5 class="card-title mb-0"><?php echo $other_group->name; ?></h5>
                                                        <small class="text-muted"><?php echo $other_group->members_count; ?> عضو</small>
                                                    </div>
                                                </div>
                                                <p class="card-text"><?php echo strlen($other_group->description) > 100 ? substr($other_group->description, 0, 100) . '...' : $other_group->description; ?></p>
                                                <p class="card-text">
                                                    <small class="text-muted">
                                                        <i class="fas fa-user me-1"></i>المنشئ: <?php echo $other_group->username; ?>
                                                    </small>
                                                </p>
                                            </div>
                                            <div class="card-footer bg-white">
                                                <div class="d-grid">
                                                    <a href="<?php echo URL_ROOT; ?>/groups/index.php?action=join&id=<?php echo $other_group->id; ?>" class="btn btn-primary btn-sm">انضم للمجموعة</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>