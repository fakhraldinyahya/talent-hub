<?php
require_once '../config/config.php';
require_once '../config/db.php';
require_once '../classes/Post.php';

// التحقق مما إذا كان المستخدم مسجل الدخول
if (!isLoggedIn()) {
    redirect('login.php');
}

// إنشاء كائنات الفئات
$database = new Database();
$post = new Post($database);

$errors = [];

// معالجة إرسال النموذج
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // الحصول على بيانات النموذج
    $title = sanitize($_POST['title']);
    $content = sanitize($_POST['content']);
    $media_type = isset($_POST['media_type']) ? sanitize($_POST['media_type']) : 'text';
    
    // التحقق من الحقول المطلوبة
    if (empty($title)) {
        $errors[] = 'عنوان المنشور مطلوب';
    }
    
    if ($media_type === 'text' && empty($content)) {
        $errors[] = 'محتوى المنشور مطلوب';
    }
    
    // معالجة الصورة أو الفيديو إذا تم تحميله
    $media_url = '';
    if ($media_type !== 'text') {
        if (!isset($_FILES['media']) || $_FILES['media']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'يرجى تحميل ملف وسائط';
        } else {
            $allowed_types = [];
            $max_size = 10 * 1024 * 1024; // 10MB
            
            if ($media_type === 'image') {
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            } elseif ($media_type === 'video') {
                $allowed_types = ['video/mp4', 'video/webm', 'video/ogg'];
            }
            if (!file_exists(POSTS_MEDIA_DIR)) {
                mkdir(POSTS_MEDIA_DIR, 0777, true);
            }
            if (!in_array($_FILES['media']['type'], $allowed_types)) {
                $errors[] = 'نوع الملف غير مدعوم';
            } if ($_FILES['media']['size'] > $max_size) {
                $errors[] = 'حجم الملف يتجاوز 10 ميجابايت';
            } else {
                $filename = uniqid() . '_' . $_FILES['media']['name'];
                $upload_path = POSTS_MEDIA_DIR . $filename;
                
                if (move_uploaded_file($_FILES['media']['tmp_name'], $upload_path)) {
                    $media_url = $filename;
                } else {
                    $errors[] = 'حدث خطأ أثناء تحميل الملف';
                }
            }
        }
    }
    
    // إذا لم تكن هناك أخطاء، قم بإنشاء المنشور
    if (empty($errors)) {
        $post_data = [
            'user_id' => $_SESSION['user_id'],
            'title' => $title,
            'content' => $content,
            'media_type' => $media_type,
            'media_url' => $media_url
        ];
        
        if ($post->createPost($post_data)) {
            flash('تم إنشاء المنشور بنجاح', 'success');
            redirect('posts/index.php');
        } else {
            $errors[] = 'حدث خطأ أثناء إنشاء المنشور';
        }
    }
}
?>

<?php require_once '../includes/header.php'; ?>

<div class="container mt-3">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">إنشاء منشور جديد</h4>
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
                    
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="title" class="form-label">العنوان</label>
                            <input type="text" name="title" id="title" class="form-control" value="<?php echo isset($_POST['title']) ? $_POST['title'] : ''; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">نوع المحتوى</label>
                            <div class="d-flex">
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="radio" name="media_type" id="type_text" value="text" checked onchange="toggleMediaFields()">
                                    <label class="form-check-label" for="type_text">نص</label>
                                </div>
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="radio" name="media_type" id="type_image" value="image" onchange="toggleMediaFields()">
                                    <label class="form-check-label" for="type_image">صورة</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="media_type" id="type_video" value="video" onchange="toggleMediaFields()">
                                    <label class="form-check-label" for="type_video">فيديو</label>
                                </div>
                            </div>
                        </div>
                        
                        <div id="content_field" class="mb-3">
                            <label for="content" class="form-label">المحتوى</label>
                            <textarea name="content" id="content" class="form-control" rows="6"><?php echo isset($_POST['content']) ? $_POST['content'] : ''; ?></textarea>
                        </div>
                        
                        <div id="media_field" class="mb-3 d-none">
                            <label for="media" class="form-label">الوسائط</label>
                            <input type="file" name="media" id="media" class="form-control">
                            <small class="text-muted">للصور: JPEG، PNG، GIF. للفيديو: MP4، WebM، Ogg. الحد الأقصى للحجم: 10 ميجابايت.</small>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">نشر</button>
                            <a href="index.php" class="btn btn-secondary">إلغاء</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleMediaFields() {
    const contentField = document.getElementById('content_field');
    const mediaField = document.getElementById('media_field');
    const contentTextarea = document.getElementById('content');
    
    if (document.getElementById('type_text').checked) {
        contentField.classList.remove('d-none');
        mediaField.classList.add('d-none');
        contentTextarea.setAttribute('required', 'required');
    } else {
        contentField.classList.remove('d-none');
        mediaField.classList.remove('d-none');
        contentTextarea.removeAttribute('required');
    }
}
</script>

<?php require_once '../includes/footer.php'; ?>