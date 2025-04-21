<?php
require_once 'config/config.php';
require_once 'config/db.php';
require_once 'classes/User.php';
require_once 'classes/Post.php';
require_once 'classes/Like.php';

// التحقق مما إذا كان المستخدم مسجل الدخول
if (!isLoggedIn()) {
    redirect('login.php');
}

// التحقق من وجود اسم المستخدم في الاستعلام
if (!isset($_GET['username']) || empty($_GET['username'])) {
    // إذا لم يتم تحديد اسم المستخدم، استخدم المستخدم الحالي
    $username = $_SESSION['username'];
} else {
    $username = sanitize($_GET['username']);
}

// إنشاء كائنات الفئات
$database = new Database();
$user = new User($database);
$post = new Post($database);
$like = new Like($database);

// الحصول على معلومات المستخدم
$user_data = $user->findUserByUsername1($username);

if (!$user_data) {
    flash('المستخدم غير موجود', 'danger');
    redirect('index.php');
}

// الحصول على منشورات المستخدم
$user_posts = $post->getPostsByUser($user_data->id);

// الحصول على عدد المنشورات والإعجابات والمتابعين
$posts_count = count($user_posts);
$likes_count = $user->getTotalLikes($user_data->id);


// تعيين عنوان الصفحة
$page_title = 'الملف الشخصي: ' . $user_data->full_name;

require_once 'includes/header.php';
?>

<div class="container mt-5">
    <!-- معلومات الملف الشخصي -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <img src="<?php echo URL_ROOT; ?>/assets/uploads/profile/<?php echo $user_data->profile_picture; ?>" class="rounded-circle img-fluid mb-3" style="max-width: 150px;" alt="صورة الملف الشخصي">
                            
                            <?php if ($_SESSION['user_id'] !== $user_data->id): ?>
                                <a href="<?php echo URL_ROOT; ?>/chat/private.php?user=<?php echo $username; ?>" class="btn btn-outline-success d-block">
                                    <i class="fas fa-comment-dots me-1"></i>مراسلة
                                </a>
                            <?php else: ?>
                                <a href="<?php echo URL_ROOT; ?>/edit_profile.php" class="btn btn-outline-primary d-block mb-2">
                                    <i class="fas fa-user-edit me-1"></i>تعديل الملف الشخصي
                                </a>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-9">
                            <div class="d-flex align-items-center mb-3">
                                <h2 class="mb-0"><?php echo $user_data->full_name; ?></h2>
                                <?php if ($user_data->role === 'admin'): ?>
                                    <span class="badge bg-danger ms-2">مشرف</span>
                                <?php endif; ?>
                            </div>
                            <p class="text-muted">@<?php echo $user_data->username; ?></p>
                            
                            <div class="mb-3">
                                <h5>نبذة</h5>
                                <p><?php echo !empty($user_data->bio) ? nl2br($user_data->bio) : 'لا توجد نبذة شخصية.'; ?></p>
                            </div>
                            
                            <div class="d-flex mb-3">
                                <div class="me-4">
                                    <strong class="d-block"><?php echo $posts_count; ?></strong>
                                    <span class="text-muted">منشورات</span>
                                </div>
                                <div class="me-4">
                                    <strong class="d-block"><?php echo $likes_count; ?></strong>
                                    <span class="text-muted">إعجابات</span>
                                </div>
                                
                            </div>
                            
                            <div>
                                <span><i class="far fa-calendar-alt me-1"></i>انضم في <?php echo date('d/m/Y', strtotime($user_data->created_at)); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- منشورات المستخدم -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <ul class="nav nav-tabs card-header-tabs" id="profileTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="posts-tab" data-bs-toggle="tab" data-bs-target="#posts" type="button" role="tab" aria-controls="posts" aria-selected="true">
                                <i class="fas fa-th-large me-1"></i>منشورات
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="likes-tab" data-bs-toggle="tab" data-bs-target="#likes" type="button" role="tab" aria-controls="likes" aria-selected="false">
                                <i class="fas fa-heart me-1"></i>إعجابات
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="profileTabsContent">
                        <!-- قسم المنشورات -->
                        <div class="tab-pane fade show active" id="posts" role="tabpanel" aria-labelledby="posts-tab">
                            <?php if (empty($user_posts)): ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                    <p class="lead">لا توجد منشورات حتى الآن.</p>
                                    <?php if ($_SESSION['user_id'] === $user_data->id): ?>
                                        <a href="<?php echo URL_ROOT; ?>/posts/create.php" class="btn btn-primary">
                                            <i class="fas fa-plus-circle me-1"></i>إضافة منشور جديد
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div class="row">
                                    <?php foreach ($user_posts as $user_post): ?>
                                        <div class="col-md-6 col-lg-4 mb-4">
                                            <div class="card h-100 shadow-sm">
                                                <div class="card-header bg-white">
                                                    <h5 class="card-title mb-0"><?php echo $user_post->title; ?></h5>
                                                    <small class="text-muted"><?php echo date('d/m/Y', strtotime($user_post->created_at)); ?></small>
                                                </div>
                                                <?php if ($user_post->media_type == 'image'): ?>
                                                    <img src="<?php echo URL_ROOT; ?>/assets/uploads/posts/<?php echo $user_post->media_url; ?>" class="card-img-top" alt="صورة المنشور">
                                                <?php elseif ($user_post->media_type == 'video'): ?>
                                                    <div class="ratio ratio-16x9">
                                                        <video controls>
                                                            <source src="<?php echo URL_ROOT; ?>/assets/uploads/posts/<?php echo $user_post->media_url; ?>" type="video/mp4">
                                                            المتصفح الخاص بك لا يدعم عنصر الفيديو.
                                                        </video>
                                                    </div>
                                                <?php endif; ?>
                                                <div class="card-body">
                                                    <p class="card-text"><?php echo strlen($user_post->content) > 100 ? substr($user_post->content, 0, 100) . '...' : $user_post->content; ?></p>
                                                </div>
                                                <div class="card-footer bg-white">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <i class="far fa-heart text-danger"></i> <?php echo $user_post->likes_count; ?>
                                                            <i class="far fa-comment text-primary ms-3"></i> <?php echo $user_post->comments_count; ?>
                                                        </div>
                                                        <a href="<?php echo URL_ROOT; ?>/posts/view.php?id=<?php echo $user_post->id; ?>" class="btn btn-sm btn-outline-primary">عرض</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- قسم الإعجابات -->
                        <div class="tab-pane fade" id="likes" role="tabpanel" aria-labelledby="likes-tab">
                            <?php
                            $liked_posts = $like->getPostsLikedByUser($user_data->id);
                            if (empty($liked_posts)): ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-heart fa-3x text-muted mb-3"></i>
                                    <p class="lead">لم يعجب المستخدم بأي منشور حتى الآن.</p>
                                </div>
                            <?php else: ?>
                                <div class="row">
                                    <?php foreach ($liked_posts as $liked_post): ?>
                                        <div class="col-md-6 col-lg-4 mb-4">
                                            <div class="card h-100 shadow-sm">
                                                <div class="card-header bg-white">
                                                    <div class="d-flex align-items-center">
                                                        <img src="<?php echo URL_ROOT; ?>/assets/uploads/profile/<?php echo $liked_post->profile_picture; ?>" class="rounded-circle me-2" width="32" height="32" alt="صورة المستخدم">
                                                        <a href="<?php echo URL_ROOT; ?>/profile/index.php?username=<?php echo $liked_post->username; ?>" class="text-decoration-none">
                                                            <?php echo $liked_post->username; ?>
                                                        </a>
                                                        <small class="text-muted ms-auto"><?php echo date('d/m/Y', strtotime($liked_post->created_at)); ?></small>
                                                    </div>
                                                </div>
                                                <?php if ($liked_post->media_type == 'image'): ?>
                                                    <img src="<?php echo URL_ROOT; ?>/assets/uploads/posts/<?php echo $liked_post->media_url; ?>" class="card-img-top" alt="صورة المنشور">
                                                <?php elseif ($liked_post->media_type == 'video'): ?>
                                                    <div class="ratio ratio-16x9">
                                                        <video controls>
                                                            <source src="<?php echo URL_ROOT; ?>/assets/uploads/posts/<?php echo $liked_post->media_url; ?>" type="video/mp4">
                                                            المتصفح الخاص بك لا يدعم عنصر الفيديو.
                                                        </video>
                                                    </div>
                                                <?php endif; ?>
                                                <div class="card-body">
                                                    <h5 class="card-title"><?php echo $liked_post->title; ?></h5>
                                                    <p class="card-text"><?php echo strlen($liked_post->content) > 100 ? substr($liked_post->content, 0, 100) . '...' : $liked_post->content; ?></p>
                                                </div>
                                                <div class="card-footer bg-white">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <i class="fas fa-heart text-danger"></i> <?php echo $liked_post->likes_count; ?>
                                                            <i class="far fa-comment text-primary ms-3"></i> <?php echo $liked_post->comments_count; ?>
                                                        </div>
                                                        <a href="<?php echo URL_ROOT; ?>/posts/view.php?id=<?php echo $liked_post->id; ?>" class="btn btn-sm btn-outline-primary">عرض</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>