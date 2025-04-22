<?php
require_once '../config/config.php';
require_once '../config/db.php';
require_once '../classes/Post.php';
require_once '../classes/User.php';

// التحقق مما إذا كان المستخدم مسجل الدخول
if (!isLoggedIn()) {
    redirect('login.php');
}

// إنشاء كائنات الفئات
$database = new Database();
$post = new Post($database);
$user = new User($database);

// التحقق مما إذا كان هناك بحث
$search_query = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$category = isset($_GET['category']) ? sanitize($_GET['category']) : '';
// الحصول على المنشورات بناءً على المعايير
if (!empty($search_query) || !empty($category)) {
    $posts = $post->searchPosts($search_query, $category ?? 'all');
    $page_title = 'نتائج البحث';
    if (!empty($search_query)) {
        $page_title .= ' عن: ' . $search_query;
    }
    if (!empty($category) && $category !== 'all') {
        $page_title .= ' - نوع: ' . $category;
    }
} else {
    $posts = $post->getAllPosts();
    $page_title = 'استكشاف المواهب';
}


require_once '../includes/header.php';
?>

<div class="container mt-3">
    <div class="row">
        <!-- قائمة التصفية -->
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">تصفية المنشورات</h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET">
                        <div class="mb-3">
                            <label for="search" class="form-label">بحث</label>
                            <input type="text" name="search" id="search" class="form-control" value="<?php echo $search_query; ?>" placeholder="ابحث عن منشورات...">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">نوع المحتوى</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="category" id="all" value="" <?php echo empty($category) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="all">الكل</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="category" id="text" value="text" <?php echo $category === 'text' ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="text">نصوص</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="category" id="image" value="image" <?php echo $category === 'image' ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="image">صور</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="category" id="video" value="video" <?php echo $category === 'video' ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="video">فيديوهات</label>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">تطبيق الفلتر</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

        <!-- عرض المنشورات -->
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0"><?php echo $page_title; ?></h2>
                <a href="<?php echo URL_ROOT; ?>/posts/create.php" class="btn btn-primary">
                    <i class="fas fa-plus-circle me-1"></i>إضافة منشور
                </a>
            </div>

            <?php if (empty($posts)): ?>
                <div class="alert alert-info">
                    لا توجد منشورات متاحة. كن أول من يضيف منشورًا!
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($posts as $post_item): ?>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 shadow-sm">
                                <?php if ($post_item->media_type == 'image'): ?>
                                    <img src="<?php echo URL_ROOT; ?>/assets/uploads/posts/<?php echo $post_item->media_url; ?>" class="card-img-top" alt="صورة المنشور">
                                <?php elseif ($post_item->media_type == 'video'): ?>
                                    <div class="embed-responsive embed-responsive-16by9">
                                        <video class="card-img-top" controls>
                                            <source src="<?php echo URL_ROOT; ?>/assets/uploads/posts/<?php echo $post_item->media_url; ?>" type="video/mp4">
                                            المتصفح الخاص بك لا يدعم عنصر الفيديو.
                                        </video>
                                    </div>
                                <?php endif; ?>
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="<?php echo URL_ROOT; ?>/assets/uploads/profile/<?php echo $post_item->profile_picture; ?>" class="rounded-circle me-2" width="32" height="32" alt="صورة المستخدم">
                                        <a href="<?php echo URL_ROOT; ?>/profile.php?username=<?php echo $post_item->username; ?>" class="text-decoration-none"><?php echo $post_item->username; ?></a>
                                        <small class="text-muted ms-auto"><?php echo date('d/m/Y', strtotime($post_item->created_at)); ?></small>
                                    </div>
                                    <h5 class="card-title"><?php echo $post_item->title; ?></h5>
                                    <p class="card-text"><?php echo strlen($post_item->content) > 100 ? substr($post_item->content, 0, 100) . '...' : $post_item->content; ?></p>
                                </div>
                                <div class="card-footer bg-transparent d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="far fa-heart text-danger"></i> <?php echo $post_item->likes_count; ?>
                                        <i class="far fa-comment text-primary ms-3"></i> <?php echo $post_item->comments_count; ?>
                                    </div>
                                    <a href="<?php echo URL_ROOT; ?>/posts/view.php?id=<?php echo $post_item->id; ?>" class="btn btn-sm btn-outline-primary">عرض المزيد</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>