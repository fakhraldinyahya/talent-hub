<?php
require_once '../config/config.php';
require_once '../config/db.php';
require_once '../classes/Post.php';

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

// الحصول على بيانات المنشور
$post_data = $post->getPostById($post_id);

// التحقق مما إذا كان المنشور موجودًا
if (!$post_data) {
    flash('المنشور غير موجود', 'danger');
    redirect('posts/index.php');
}

// التحقق من صلاحية المستخدم لتعديل المنشور
if ($post_data->user_id !== $_SESSION['user_id'] && !isAdmin()) {
    flash('ليس لديك صلاحية لتعديل هذا المنشور', 'danger');
    redirect('posts/index.php');
}

// تعيين عنوان الصفحة
$page_title = 'تعديل منشور: ' . $post_data->title;

$errors = [];

// معالجة إرسال النموذج
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // الحصول على بيانات النموذج
    $title = sanitize($_POST['title']);
    $content = sanitize($_POST['content']);
    $media_type = $post_data->media_type; // لا نسمح بتغيير نوع الوسائط
    
    // التحقق من الحقول المطلوبة
    if (empty($title)) {
        $errors[] = 'عنوان المنشور مطلوب';
    }
    
    if ($media_type === 'text' && empty($content)) {
        $errors[] = 'محتوى المنشور مطلوب';
    }
    
    // معالجة الصورة أو الفيديو إذا تم تحميله
    $media_url = $post_data->media_url;
    if ($media_type !== 'text' && isset($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = [];
        $max_size = 10 * 1024 * 1024; // 10MB
        
        if ($media_type === 'image') {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        } elseif ($media_type === 'video') {
            $allowed_types = ['video/mp4', 'video/webm', 'video/ogg'];
        }
        
        if (!in_array($_FILES['media']['type'], $allowed_types)) {
            $errors[] = 'نوع الملف غير مدعوم';
        } elseif ($_FILES['media']['size'] > $max_size) {
            $errors[] = 'حجم الملف يتجاوز 10 ميجابايت';
        } else {
            // حذف الملف القديم إذا كان موجودًا
            if (!empty($post_data->media_url) && file_exists(POSTS_MEDIA_DIR . $post_data->media_url)) {
                unlink(POSTS_MEDIA_DIR . $post_data->media_url);
            }
            
            $filename = uniqid() . '_' . $_FILES['media']['name'];
            $upload_path = POSTS_MEDIA_DIR . $filename;
            
            if (move_uploaded_file($_FILES['media']['tmp_name'], $upload_path)) {
                $media_url = $filename;
            } else {
                $errors[] = 'حدث خطأ أثناء تحميل الملف';
            }
        }
    }
    
    // إذا لم تكن هناك أخطاء، قم بتحديث المنشور
    if (empty($errors)) {
        $update_data = [
            'id' => $post_id,
            'user_id' => $_SESSION['user_id'],
            'title' => $title,
            'content' => $content,
            'media_type' => $media_type,
            'media_url' => $media_url
        ];
        
        if ($post->updatePost($update_data)) {
            flash('تم تحديث المنشور بنجاح', 'success');
            redirect('posts/view.php?id=' . $post_id);
        } else {
            $errors[] = 'حدث خطأ أثناء تحديث المنشور';
        }
    }
}

require_once '../includes/header.php';
?>

<div class="container mt-3">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">تعديل المنشور</h4>
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
                    
                    <form action="<?php echo $_SERVER['PHP_SELF'] . '?id=' . $post_id; ?>" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="title" class="form-label">العنوان</label>
                            <input type="text" name="title" id="title" class="form-control" value="<?php echo $post_data->title; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="content" class="form-label">المحتوى</label>
                            <textarea name="content" id="content" class="form-control" rows="6"><?php echo $post_data->content; ?></textarea>
                        </div>
                        
                        <?php if ($post_data->media_type !== 'text'): ?>
                            <div class="mb-3">
                                <label class="form-label">الوسائط الحالية</label>
                                <?php if ($post_data->media_type === 'image'): ?>
                                    <div class="mb-2">
                                        <img src="<?php echo URL_ROOT; ?>/assets/uploads/posts/<?php echo $post_data->media_url; ?>" class="img-fluid rounded" style="max-height: 200px;" alt="صورة المنشور">
                                    </div>
                                <?php elseif ($post_data->media_type === 'video'): ?>
                                    <div class="mb-2">
                                        <video class="img-fluid rounded" style="max-height: 200px;" controls>
                                            <source src="<?php echo URL_ROOT; ?>/assets/uploads/posts/<?php echo $post_data->media_url; ?>" type="video/mp4">
                                            المتصفح الخاص بك لا يدعم عنصر الفيديو.
                                        </video>
                                    </div>
                                <?php endif; ?>
                                
                                <label for="media" class="form-label">تغيير الوسائط (اختياري)</label>
                                <input type="file" name="media" id="media" class="form-control">
                                <small class="text-muted">
                                    <?php if ($post_data->media_type === 'image'): ?>
                                        الأنواع المدعومة: JPEG، PNG، GIF. الحد الأقصى للحجم: 10 ميجابايت.
                                    <?php elseif ($post_data->media_type === 'video'): ?>
                                        الأنواع المدعومة: MP4، WebM، Ogg. الحد الأقصى للحجم: 10 ميجابايت.
                                    <?php endif; ?>
                                </small>
                            </div>
                        <?php endif; ?>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                            <a href="<?php echo URL_ROOT; ?>/posts/view.php?id=<?php echo $post_id; ?>" class="btn btn-secondary">إلغاء</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>