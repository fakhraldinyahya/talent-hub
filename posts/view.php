<?php
require_once '../config/config.php';
require_once '../config/db.php';
require_once '../classes/Post.php';
require_once '../classes/Comment.php';
require_once '../classes/Like.php';
require_once '../classes/User.php';

// التحقق مما إذا كان المستخدم مسجل الدخول
if (!isLoggedIn()) {
    redirect('login.php');
}

// التحقق من معرف المنشور
if (!isset($_GET['id']) || empty($_GET['id'])) {
    flash('المنشور غير موجود', 'danger');
    redirect('posts/index.php');
}

$post_id = sanitize($_GET['id']);

// إنشاء كائنات الفئات
$database = new Database();
$post = new Post($database);
$comment = new Comment($database);
$like = new Like($database);
$user = new User($database);

// الحصول على بيانات المنشور
$post_data = $post->getPostById($post_id);

// التحقق مما إذا كان المنشور موجودًا
if (!$post_data) {
    flash('المنشور غير موجود', 'danger');
    redirect('posts/index.php');
}

// التحقق مما إذا كان المستخدم قد أعجب بالمنشور
$has_liked = $like->hasUserLikedPost($_SESSION['user_id'], $post_id);

// الحصول على التعليقات
$comments = $comment->getCommentsByPostId($post_id);

// معالجة إضافة تعليق
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment'])) {
    $comment_content = sanitize($_POST['comment_content']);
    
    if (empty($comment_content)) {
        flash('محتوى التعليق مطلوب', 'danger');
    } else {
        $comment_data = [
            'user_id' => $_SESSION['user_id'],
            'post_id' => $post_id,
            'content' => $comment_content
        ];
        
        if ($comment->addComment($comment_data)) {
            flash('تمت إضافة التعليق بنجاح', 'success');
            redirect('posts/view.php?id=' . $post_id);
        } else {
            flash('حدث خطأ أثناء إضافة التعليق', 'danger');
        }
    }
}

// معالجة الإعجاب / إلغاء الإعجاب
if (isset($_GET['action']) && $_GET['action'] === 'toggle_like') {
    if ($has_liked) {
        $like->unlikePost($_SESSION['user_id'], $post_id);
    } else {
        $like->likePost($_SESSION['user_id'], $post_id);
    }
    redirect('posts/view.php?id=' . $post_id);
}

// معالجة حذف المنشور
if (isset($_GET['action']) && $_GET['action'] === 'delete' && ($_SESSION['user_id'] === $post_data->user_id || isAdmin())) {
    if ($post->deletePost($post_id, $_SESSION['user_id'])) {
        flash('تم حذف المنشور بنجاح', 'success');
        redirect('posts/index.php');
    } else {
        flash('حدث خطأ أثناء حذف المنشور', 'danger');
    }
}

// تعيين عنوان الصفحة
$page_title = $post_data->title;

require_once '../includes/header.php';
?>

<div class="container mt-3">
    <div class="row">
        <div class="col-md-8">
            <!-- بطاقة المنشور -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <div class="d-flex align-items-center">
                        <img src="<?php echo URL_ROOT; ?>/assets/uploads/profile/<?php echo $post_data->profile_picture; ?>" class="rounded-circle me-2" width="40" height="40" alt="صورة المستخدم">
                        <div>
                            <a href="<?php echo URL_ROOT; ?>/profile.php?username=<?php echo $post_data->username; ?>" class="text-decoration-none fw-bold"><?php echo $post_data->full_name; ?></a>
                            <small class="text-muted d-block">@<?php echo $post_data->username; ?> - <?php echo date('d/m/Y H:i', strtotime($post_data->created_at)); ?></small>
                        </div>
                        
                        <?php if ($_SESSION['user_id'] === $post_data->user_id || isAdmin()): ?>
                            <div class="dropdown ms-auto">
                                <button class="btn btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <?php if ($_SESSION['user_id'] === $post_data->user_id): ?>
                                        <li><a class="dropdown-item" href="<?php echo URL_ROOT; ?>/posts/edit.php?id=<?php echo $post_id; ?>"><i class="fas fa-edit me-1"></i>تعديل</a></li>
                                    <?php endif; ?>
                                    <li>
                                        <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#deletePostModal">
                                            <i class="fas fa-trash-alt me-1"></i>حذف
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php if ($post_data->media_type == 'image'): ?>
                    <img src="<?php echo URL_ROOT; ?>/assets/uploads/posts/<?php echo $post_data->media_url; ?>" class="card-img-top" alt="صورة المنشور">
                <?php elseif ($post_data->media_type == 'video'): ?>
                    <div class="ratio ratio-16x9">
                        <video controls>
                            <source src="<?php echo URL_ROOT; ?>/assets/uploads/posts/<?php echo $post_data->media_url; ?>" type="video/mp4">
                            المتصفح الخاص بك لا يدعم عنصر الفيديو.
                        </video>
                    </div>
                <?php endif; ?>
                
                <div class="card-body">
                    <h4 class="card-title"><?php echo $post_data->title; ?></h4>
                    <p class="card-text"><?php echo nl2br($post_data->content); ?></p>
                </div>
                
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <a href="<?php echo URL_ROOT; ?>/posts/view.php?id=<?php echo $post_id; ?>&action=toggle_like" class="btn btn-sm <?php echo $has_liked ? 'btn-danger' : 'btn-outline-danger'; ?>">
                                <i class="<?php echo $has_liked ? 'fas' : 'far'; ?> fa-heart me-1"></i>
                                <?php echo $post_data->likes_count; ?> إعجاب
                            </a>
                            <a href="#comments" class="btn btn-sm btn-outline-primary ms-2">
                                <i class="far fa-comment me-1"></i>
                                <?php echo $post_data->comments_count; ?> تعليق
                            </a>
                        </div>
                        <div>
                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#shareModal">
                                <i class="fas fa-share-alt me-1"></i>مشاركة
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- قسم التعليقات -->
            <div class="card shadow-sm" id="comments">
                <div class="card-header bg-white">
                    <h5 class="mb-0">التعليقات (<?php echo $post_data->comments_count; ?>)</h5>
                </div>
                <div class="card-body">
                    <!-- نموذج إضافة تعليق -->
                    <form action="<?php echo $_SERVER['PHP_SELF'] . '?id=' . $post_id; ?>" method="POST" class="mb-4">
                        <div class="d-flex">
                            <img src="<?php echo URL_ROOT; ?>/assets/uploads/profile/<?php echo $_SESSION['profile_picture'] ?? 'default.jpeg'; ?>" class="rounded-circle me-2" width="40" height="40" alt="صورة المستخدم">
                            <div class="flex-grow-1">
                                <textarea name="comment_content" class="form-control" rows="2" placeholder="أضف تعليقًا..." required></textarea>
                                <div class="d-flex justify-content-end mt-2">
                                    <button type="submit" name="add_comment" class="btn btn-primary">تعليق</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    
                    <!-- عرض التعليقات -->
                    <?php if (empty($comments)): ?>
                        <div class="text-center py-4">
                            <p class="text-muted">لا توجد تعليقات حتى الآن. كن أول من يعلق!</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($comments as $comment_item): ?>
                            <div class="d-flex mb-3 pb-3 border-bottom">
                                <img src="<?php echo URL_ROOT; ?>/assets/uploads/profile/<?php echo $comment_item->profile_picture; ?>" class="rounded-circle me-2" width="40" height="40" alt="صورة المستخدم">
                                <div>
                                    <div class="d-flex align-items-center">
                                        <a href="<?php echo URL_ROOT; ?>/profile.php?username=<?php echo $comment_item->username; ?>" class="text-decoration-none fw-bold"><?php echo $comment_item->username; ?></a>
                                        <small class="text-muted ms-2"><?php echo date('d/m/Y H:i', strtotime($comment_item->created_at)); ?></small>
                                        
                                        <?php if ($_SESSION['user_id'] === $comment_item->user_id || isAdmin()): ?>
                                            <div class="dropdown ms-2">
                                                <button class="btn btn-sm py-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item text-danger" href="<?php echo URL_ROOT; ?>/posts/view.php?id=<?php echo $post_id; ?>&comment_id=<?php echo $comment_item->id; ?>&action=delete_comment">
                                                            <i class="fas fa-trash-alt me-1"></i>حذف
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <p class="mb-0 mt-1"><?php echo $comment_item->content; ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- معلومات الناشر -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">عن الناشر</h5>
                </div>
                <div class="card-body text-center">
                    <img src="<?php echo URL_ROOT; ?>/assets/uploads/profile/<?php echo $post_data->profile_picture; ?>" class="rounded-circle mb-3" width="80" height="80" alt="صورة المستخدم">
                    <h5><?php echo $post_data->full_name; ?></h5>
                    <p class="text-muted">@<?php echo $post_data->username; ?></p>
                    <div class="d-grid gap-2">
                        <a href="<?php echo URL_ROOT; ?>/profile.php?username=<?php echo $post_data->username; ?>" class="btn btn-outline-primary">عرض الملف الشخصي</a>
                        <?php if ($_SESSION['user_id'] !== $post_data->user_id): ?>
                            <a href="<?php echo URL_ROOT; ?>/chat/private.php?user=<?php echo $post_data->username; ?>" class="btn btn-outline-success">
                                <i class="fas fa-comment-dots me-1"></i>مراسلة
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- منشورات مشابهة -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">منشورات مشابهة</h5>
                </div>
                <div class="card-body">
                    <?php
                    $similar_posts = $post->getSimilarPosts($post_id, $post_data->user_id);
                    if (empty($similar_posts)): ?>
                        <p class="text-muted text-center">لا توجد منشورات مشابهة حاليًا.</p>
                    <?php else: ?>
                        <?php foreach ($similar_posts as $similar_post): ?>
                            <div class="d-flex mb-3 pb-3 border-bottom">
                                <?php if ($similar_post->media_type == 'image'): ?>
                                    <img src="<?php echo URL_ROOT; ?>/assets/uploads/posts/<?php echo $similar_post->media_url; ?>" class="rounded me-2" width="60" height="60" alt="صورة المنشور" style="object-fit: cover;">
                                <?php elseif ($similar_post->media_type == 'video'): ?>
                                    <div class="position-relative me-2" style="width: 60px; height: 60px;">
                                        <img src="<?php echo URL_ROOT; ?>/assets/img/video-thumbnail.jpg" class="rounded" width="60" height="60" alt="صورة الفيديو" style="object-fit: cover;">
                                        <div class="position-absolute top-50 start-50 translate-middle text-white">
                                            <i class="fas fa-play-circle fa-lg"></i>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="bg-light rounded me-2 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                        <i class="fas fa-file-alt fa-lg text-secondary"></i>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <a href="<?php echo URL_ROOT; ?>/posts/view.php?id=<?php echo $similar_post->id; ?>" class="text-decoration-none">
                                        <h6 class="mb-1"><?php echo $similar_post->title; ?></h6>
                                    </a>
                                    <small class="text-muted"><?php echo date('d/m/Y', strtotime($similar_post->created_at)); ?></small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- مودال حذف المنشور -->
<div class="modal fade" id="deletePostModal" tabindex="-1" aria-labelledby="deletePostModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deletePostModalLabel">تأكيد الحذف</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                هل أنت متأكد من رغبتك في حذف هذا المنشور؟ هذا الإجراء لا يمكن التراجع عنه.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <a href="<?php echo URL_ROOT; ?>/posts/view.php?id=<?php echo $post_id; ?>&action=delete" class="btn btn-danger">حذف</a>
            </div>
        </div>
    </div>
</div>

<!-- مودال المشاركة -->
<div class="modal fade" id="shareModal" tabindex="-1" aria-labelledby="shareModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="shareModalLabel">مشاركة المنشور</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>شارك هذا المنشور مع أصدقائك:</p>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="shareUrl" value="<?php echo URL_ROOT; ?>/posts/view.php?id=<?php echo $post_id; ?>" readonly>
                    <button class="btn btn-outline-secondary" type="button" onclick="copyShareUrl()">نسخ</button>
                </div>
                <div class="d-flex justify-content-center mt-3">
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(URL_ROOT . '/posts/view.php?id=' . $post_id); ?>" target="_blank" class="btn btn-outline-primary mx-1">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(URL_ROOT . '/posts/view.php?id=' . $post_id); ?>&text=<?php echo urlencode($post_data->title); ?>" target="_blank" class="btn btn-outline-info mx-1">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="https://api.whatsapp.com/send?text=<?php echo urlencode($post_data->title . ' - ' . URL_ROOT . '/posts/view.php?id=' . $post_id); ?>" target="_blank" class="btn btn-outline-success mx-1">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                    <a href="https://t.me/share/url?url=<?php echo urlencode(URL_ROOT . '/posts/view.php?id=' . $post_id); ?>&text=<?php echo urlencode($post_data->title); ?>" target="_blank" class="btn btn-outline-primary mx-1">
                        <i class="fab fa-telegram-plane"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyShareUrl() {
    var copyText = document.getElementById("shareUrl");
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    document.execCommand("copy");
    
    // إظهار رسالة النجاح
    var button = copyText.nextElementSibling;
    var originalText = button.innerText;
    button.innerText = "تم النسخ!";
    button.classList.remove("btn-outline-secondary");
    button.classList.add("btn-success");
    
    setTimeout(function() {
        button.innerText = originalText;
        button.classList.remove("btn-success");
        button.classList.add("btn-outline-secondary");
    }, 2000);
}
</script>

<?php require_once '../includes/footer.php'; ?>