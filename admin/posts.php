<?php
require_once '../config/config.php';
require_once '../config/db.php';
require_once '../classes/Post.php';
require_once '../classes/User.php';

// التحقق مما إذا كان المستخدم مسجل الدخول ومسؤول
if (!isLoggedIn() || !isAdmin()) {
    redirect('index.php');
}

// إنشاء كائنات الفئات
$database = new Database();
$post = new Post($database);
$user = new User($database);

// معالجة الإجراءات
if (isset($_GET['action'])) {
    $action = sanitize($_GET['action']);
    
    // حذف منشور
    if ($action === 'delete' && isset($_GET['id'])) {
        $id = sanitize($_GET['id']);
        
        if ($post->deletePost($id, $_SESSION['user_id'])) {
            flash('تم حذف المنشور بنجاح', 'success');
        } else {
            flash('حدث خطأ أثناء حذف المنشور', 'danger');
        }
        
        redirect('admin/posts.php');
    }
}

// الفلترة حسب نوع الوسائط
$media_type = isset($_GET['media_type']) ? sanitize($_GET['media_type']) : '';

// البحث عن المنشورات
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';

if (!empty($search)) {
    $posts_list = $post->searchPosts($search);
} elseif (!empty($media_type)) {
    $posts_list = $post->getPostsByMediaType($media_type);
} else {
    $posts_list = $post->getAllPosts();
}

// تعيين عنوان الصفحة
$page_title = 'إدارة المنشورات';

require_once '../includes/header.php';
?>

<div class="container mt-3">
    <div class="row">
        
        <!-- المحتوى الرئيسي -->
        <div class="col-lg-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><?php echo $page_title; ?></h2>
                <div class="d-flex">
                    <div class="btn-group me-2">
                        <a href="<?php echo URL_ROOT; ?>/admin/posts.php" class="btn btn-outline-primary <?php echo empty($media_type) ? 'active' : ''; ?>">الكل</a>
                        <a href="<?php echo URL_ROOT; ?>/admin/posts.php?media_type=text" class="btn btn-outline-primary <?php echo $media_type === 'text' ? 'active' : ''; ?>">النصوص</a>
                        <a href="<?php echo URL_ROOT; ?>/admin/posts.php?media_type=image" class="btn btn-outline-primary <?php echo $media_type === 'image' ? 'active' : ''; ?>">الصور</a>
                        <a href="<?php echo URL_ROOT; ?>/admin/posts.php?media_type=video" class="btn btn-outline-primary <?php echo $media_type === 'video' ? 'active' : ''; ?>">الفيديوهات</a>
                    </div>
                    <form class="d-flex">
                        <input class="form-control me-2" type="search" name="search" placeholder="البحث في المنشورات..." aria-label="Search" value="<?php echo $search; ?>">
                        <button class="btn btn-outline-primary" type="submit">بحث</button>
                    </form>
                </div>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
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
                                <?php if (empty($posts_list)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-4">لا توجد منشورات متطابقة مع معايير البحث</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($posts_list as $index => $post_item): ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo strlen($post_item->title) > 30 ? substr($post_item->title, 0, 30) . '...' : $post_item->title; ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="<?php echo URL_ROOT; ?>/assets/uploads/profile/<?php echo $post_item->profile_picture; ?>" class="rounded-circle me-2" width="24" height="24" alt="صورة المستخدم">
                                                    <a href="<?php echo URL_ROOT; ?>/profile.php?username=<?php echo $post_item->username; ?>" class="text-decoration-none"><?php echo $post_item->username; ?></a>
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