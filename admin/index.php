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
$stats = $admin->getDashboardStats();

$page_title = 'لوحة تحكم المشرف';

require_once '../includes/header.php';
?>

<div class="container mt-3">
    <div class="row">
        
        
        <!-- المحتوى الرئيسي -->
        <div class="col-lg-12">
            <h2 class="mb-4">لوحة التحكم</h2>
            
            <!-- بطاقات الإحصائيات -->
            <div class="row mb-4">
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="fs-1 text-primary me-3">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div>
                                    <h6 class="card-title text-muted mb-0">إجمالي المستخدمين</h6>
                                    <h2 class="display-6 mb-0"><?php echo $stats['users_count']; ?></h2>
                                    <small class="text-success">
                                        <i class="fas fa-arrow-up me-1"></i>+ <?php echo $stats['new_users_today']; ?> اليوم
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="fs-1 text-success me-3">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                                <div>
                                    <h6 class="card-title text-muted mb-0">إجمالي المنشورات</h6>
                                    <h2 class="display-6 mb-0"><?php echo $stats['posts_count']; ?></h2>
                                    <small class="text-success">
                                        <i class="fas fa-arrow-up me-1"></i>+<?php echo $stats['new_posts_today']; ?> اليوم
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="fs-1 text-warning me-3">
                                    <i class="fas fa-layer-group"></i>
                                </div>
                                <div>
                                    <h6 class="card-title text-muted mb-0">إجمالي المجموعات</h6>
                                    <h2 class="display-6 mb-0"><?php echo $stats['groups_count']; ?></h2>
                                    <small class="text-success">
                                        <i class="fas fa-arrow-up me-1"></i>+<?php echo $stats['new_groups_today']; ?> اليوم
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                
            </div>
            
            <div class="row">
                <!-- أحدث المستخدمين -->
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">أحدث المستخدمين</h5>
                            <a href="<?php echo URL_ROOT; ?>/admin/users.php" class="btn btn-sm btn-primary">عرض الكل</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>المستخدم</th>
                                            <th>البريد الإلكتروني</th>
                                            <th>تاريخ التسجيل</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($stats['recent_users'] as $user_item): ?>
                                        <tr>
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
                                                <td><?php echo date('d/m/Y', strtotime($user_item->created_at)); ?></td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="<?php echo URL_ROOT; ?>/profile.php?username=<?php echo $user_item->username; ?>" class="btn btn-outline-primary" title="عرض الملف الشخصي">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="<?php echo URL_ROOT; ?>/admin/users.php?action=edit&id=<?php echo $user_item->id; ?>" class="btn btn-outline-secondary" title="تعديل">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
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
                
                <!-- أحدث المنشورات -->
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">أحدث المنشورات</h5>
                            <a href="<?php echo URL_ROOT; ?>/admin/posts.php" class="btn btn-sm btn-primary">عرض الكل</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>العنوان</th>
                                            <th>المستخدم</th>
                                            <th>النوع</th>
                                            <th>التاريخ</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($stats['recent_posts'] as $post_item): ?>
                                        <tr>
                                                <td><?php echo strlen($post_item->title) > 30 ? substr($post_item->title, 0, 30) . '...' : $post_item->title; ?></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="<?php echo URL_ROOT; ?>/assets/uploads/profile/<?php echo $post_item->profile_picture; ?>" class="rounded-circle me-2" width="24" height="24" alt="صورة المستخدم">
                                                        <span><?php echo $post_item->username; ?></span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php if ($post_item->media_type === 'text'): ?>
                                                        <span class="badge bg-secondary">نص</span>
                                                    <?php elseif ($post_item->media_type === 'image'): ?>
                                                        <span class="badge bg-success">صورة</span>
                                                    <?php elseif ($post_item->media_type === 'video'): ?>
                                                        <span class="badge bg-danger">فيديو</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo date('d/m/Y', strtotime($post_item->created_at)); ?></td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="<?php echo URL_ROOT; ?>/posts/view.php?id=<?php echo $post_item->id; ?>" class="btn btn-outline-primary" title="عرض">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="<?php echo URL_ROOT; ?>/admin/posts.php?action=delete&id=<?php echo $post_item->id; ?>" class="btn btn-outline-danger" title="حذف" onclick="return confirm('هل أنت متأكد من رغبتك في حذف هذا المنشور؟');">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </a>
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
            
            <div class="row">
                <!-- أكثر المنشورات إعجابًا -->
                <div class="col-md-12 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">أكثر المنشورات إعجابًا</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>العنوان</th>
                                            <th>المستخدم</th>
                                            <th>النوع</th>
                                            <th>الإعجابات</th>
                                            <th>التعليقات</th>
                                            <th>التاريخ</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($stats['popular_posts'] as $post_item): ?>
                                            <tr>
                                                <td><?php echo strlen($post_item->title) > 30 ? substr($post_item->title, 0, 30) . '...' : $post_item->title; ?></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="<?php echo URL_ROOT; ?>/assets/uploads/profile/<?php echo $post_item->profile_picture; ?>" class="rounded-circle me-2" width="24" height="24" alt="صورة المستخدم">
                                                        <span><?php echo $post_item->username; ?></span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php if ($post_item->media_type === 'text'): ?>
                                                        <span class="badge bg-secondary">نص</span>
                                                    <?php elseif ($post_item->media_type === 'image'): ?>
                                                        <span class="badge bg-success">صورة</span>
                                                    <?php elseif ($post_item->media_type === 'video'): ?>
                                                        <span class="badge bg-danger">فيديو</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><i class="fas fa-heart text-danger me-1"></i> <?php echo $post_item->likes_count; ?></td>
                                                <td><i class="fas fa-comment text-primary me-1"></i> <?php echo $post_item->comments_count; ?></td>
                                                <td><?php echo date('d/m/Y', strtotime($post_item->created_at)); ?></td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="<?php echo URL_ROOT; ?>/posts/view.php?id=<?php echo $post_item->id; ?>" class="btn btn-outline-primary" title="عرض">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="<?php echo URL_ROOT; ?>/admin/posts.php?action=delete&id=<?php echo $post_item->id; ?>" class="btn btn-outline-danger" title="حذف" onclick="return confirm('هل أنت متأكد من رغبتك في حذف هذا المنشور؟');">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </a>
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
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>