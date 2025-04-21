<?php
// تعريف مسار القاعدة
define('BASE_PATH', __DIR__);

// استيراد ملفات الإعدادات
require_once 'config/config.php';
require_once 'config/db.php';

// استيراد الفئات
require_once 'classes/User.php';
require_once 'classes/Post.php';
require_once 'classes/Group.php';

// تهيئة الفئات
$database = new Database();
$userClass = new User($database);
$postClass = new Post($database);
$groupClass = new Group($database);

// الحصول على استعلام البحث
$query = sanitize($_GET['q'] ?? '');
$category = sanitize($_GET['category'] ?? '');

// الصفحة الحالية
$page = max(1, intval($_GET['page'] ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;

// نتائج البحث
$users = [];
$posts = [];
$groups = [];
$total_users = 0;
$total_posts = 0;
$total_groups = 0;

// البحث فقط إذا كان هناك استعلام
if (!empty($query) || !empty($category)) {
    // استعلام البحث
    $searchQuery = !empty($query) ? $query : '';
    
    // البحث حسب التصنيف
    if (!empty($category)) {
        switch ($category) {
            case 'users':
                // البحث في المستخدمين فقط
                $users = $userClass->search_users($searchQuery, $limit, $offset);
                $total_users = $userClass->get_users_count($searchQuery);
                break;
                
            case 'posts':
                // البحث في المنشورات فقط
                $posts = $postClass->search_posts($searchQuery, $limit, $offset, isLoggedIn() ? $_SESSION['user_id'] : null);
                $total_posts = $postClass->get_posts_count(isLoggedIn() ? $_SESSION['user_id'] : null, null, $searchQuery);
                break;
                
            case 'groups':
                // البحث في المجموعات فقط
                $groups = $groupClass->get_groups($limit, $offset, isLoggedIn() ? $_SESSION['user_id'] : null, $searchQuery);
                $total_groups = $groupClass->get_groups_count($searchQuery);
                break;
                
            default:
                // البحث في جميع الفئات
                $users = $userClass->search_users($searchQuery, 5, 0);
                $posts = $postClass->search_posts($searchQuery, 5, 0, isLoggedIn() ? $_SESSION['user_id'] : null);
                $groups = $groupClass->get_groups(5, 0, isLoggedIn() ? $_SESSION['user_id'] : null, $searchQuery);
                
                $total_users = $userClass->get_users_count($searchQuery);
                $total_posts = $postClass->get_posts_count(isLoggedIn() ? $_SESSION['user_id'] : null, null, $searchQuery);
                $total_groups = $groupClass->get_groups_count($searchQuery);
                break;
        }
    } else {
        // البحث في جميع الفئات
        $users = $userClass->search_users($searchQuery, 5, 0);
        $posts = $postClass->search_posts($searchQuery, 5, 0, isLoggedIn() ? $_SESSION['user_id'] : null);
        $groups = $groupClass->get_groups(5, 0, isLoggedIn() ? $_SESSION['user_id'] : null, $searchQuery);
        
        $total_users = $userClass->get_users_count($searchQuery);
        $total_posts = $postClass->get_posts_count(isLoggedIn() ? $_SESSION['user_id'] : null, null, $searchQuery);
        $total_groups = $groupClass->get_groups_count($searchQuery);
    }
}

// عنوان الصفحة
$page_title = 'البحث: ' . (!empty($query) ? $query : (!empty($category) ? $category : ''));

// تضمين رأس الصفحة
include 'includes/header.php';
?>

<div class="container py-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- نموذج البحث -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">البحث <?php echo $total_users?> </h5>
                    <form method="GET" action="<?= generate_url('search.php') ?>">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="q" placeholder="ابحث عن مواهب، منشورات، أشخاص..." value="<?= htmlspecialchars($query) ?>">
                            <select class="form-select" name="category" style="max-width: 150px;">
                                <option value="" <?= empty($category) ? 'selected' : '' ?>>الكل</option>
                                <option value="users" <?= $category === 'users' ? 'selected' : '' ?>>المستخدمين</option>
                                <option value="posts" <?= $category === 'posts' ? 'selected' : '' ?>>المنشورات</option>
                                <option value="groups" <?= $category === 'groups' ? 'selected' : '' ?>>المجموعات</option>
                            </select>
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search me-1"></i> بحث
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <?php if (empty($query) && empty($category)): ?>
                <!-- صفحة البحث الأولية -->
                <div class="card text-center p-5">
                    <div class="card-body">
                        <i class="fas fa-search fa-4x text-muted mb-3"></i>
                        <h3>ابحث في منصة المواهب</h3>
                        <p class="text-muted">ابحث عن المواهب، المنشورات، المستخدمين، والمجموعات</p>
                    </div>
                </div>
            <?php elseif (empty($users) && empty($posts) && empty($groups)): ?>
                <!-- لا توجد نتائج -->
                <div class="card text-center p-5">
                    <div class="card-body">
                        <i class="fas fa-search fa-4x text-muted mb-3"></i>
                        <h3>لا توجد نتائج</h3>
                        <p class="text-muted">لم يتم العثور على نتائج تطابق استعلام البحث: "<?= htmlspecialchars($query) ?>"</p>
                        <a href="<?= generate_url('search.php') ?>" class="btn btn-primary mt-2">العودة للبحث</a>
                    </div>
                </div>
            <?php else: ?>
                <!-- نتائج البحث -->
                
                <?php if (!empty($users) && ($category === 'users' || empty($category))): ?>
                    <!-- المستخدمين -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center bg-white">
                            <h5 class="mb-0">المستخدمين</h5>
                            <?php if ($total_users > count($users) && empty($category)): ?>
                                <a href="<?= generate_url('search.php', ['q' => $query, 'category' => 'users']) ?>" class="btn btn-sm btn-outline-primary">عرض الكل (<?= $total_users ?>)</a>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php foreach ($users as $user): ?>
                                    <div class="col-lg-6 mb-3">
                                        <div class="d-flex align-items-center">
                                            <a href="<?= generate_url('profile.php', ['username' => $user['username']]) ?>" class="me-3">
                                                <img src="<?= SITE_URL ?>/assets/uploads/avatars/<?= $user['avatar'] ?>" alt="<?= $user['full_name'] ?>" class="rounded-circle" style="width: 64px; height: 64px; object-fit: cover;">
                                            </a>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">
                                                    <a href="<?= generate_url('profile.php', ['username' => $user['username']]) ?>" class="text-decoration-none">
                                                        <?= $user['full_name'] ?>
                                                    </a>
                                                </h6>
                                                <p class="text-muted mb-0">@<?= $user['username'] ?></p>
                                                
                                                <?php if (!empty($user['bio'])): ?>
                                                    <small class="text-muted d-block text-truncate" style="max-width: 250px;"><?= $user['bio'] ?></small>
                                                <?php endif; ?>
                                                
                                                <?php if (isLoggedIn() && $_SESSION['user_id'] != $user['id']): ?>
                                                    <?php
                                                    $is_following = $userClass->is_following($_SESSION['user_id'], $user['id']);
                                                    ?>
                                                    <button class="btn btn-sm <?= $is_following ? 'btn-secondary' : 'btn-primary' ?> mt-2 follow-btn" data-user-id="<?= $user['id'] ?>">
                                                        <i class="fas <?= $is_following ? 'fa-user-check' : 'fa-user-plus' ?> me-1"></i>
                                                        <?= $is_following ? 'إلغاء المتابعة' : 'متابعة' ?>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <?php if ($category === 'users' && $total_users > $limit): ?>
                                <!-- ترقيم الصفحات -->
                                <nav aria-label="Page navigation" class="mt-3">
                                    <ul class="pagination justify-content-center">
                                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                            <a class="page-link" href="<?= generate_url('search.php', ['q' => $query, 'category' => $category, 'page' => $page - 1]) ?>" aria-label="Previous">
                                                <span aria-hidden="true">&laquo;</span>
                                            </a>
                                        </li>
                                        
                                        <?php
                                        $total_pages = ceil($total_users / $limit);
                                        for ($i = 1; $i <= min(5, $total_pages); $i++):
                                        ?>
                                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                                <a class="page-link" href="<?= generate_url('search.php', ['q' => $query, 'category' => $category, 'page' => $i]) ?>"><?= $i ?></a>
                                            </li>
                                        <?php endfor; ?>
                                        
                                        <?php if ($total_pages > 5): ?>
                                            <li class="page-item disabled">
                                                <span class="page-link">...</span>
                                            </li>
                                            <li class="page-item">
                                                <a class="page-link" href="<?= generate_url('search.php', ['q' => $query, 'category' => $category, 'page' => $total_pages]) ?>"><?= $total_pages ?></a>
                                            </li>
                                        <?php endif; ?>
                                        
                                        <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                                            <a class="page-link" href="<?= generate_url('search.php', ['q' => $query, 'category' => $category, 'page' => $page + 1]) ?>" aria-label="Next">
                                                <span aria-hidden="true">&raquo;</span>
                                            </a>
                                        </li>
                                    </ul>
                                </nav>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($posts) && ($category === 'posts' || empty($category))): ?>
                    <!-- المنشورات -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center bg-white">
                            <h5 class="mb-0">المنشورات</h5>
                            <?php if ($total_posts > count($posts) && empty($category)): ?>
                                <a href="<?= generate_url('search.php', ['q' => $query, 'category' => 'posts']) ?>" class="btn btn-sm btn-outline-primary">عرض الكل (<?= $total_posts ?>)</a>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <div id="post-feed">
                                <?php foreach ($posts as $post): ?>
                                    <div class="card post-card mb-3" data-post-id="<?= $post['id'] ?>">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <a href="<?= generate_url('profile.php', ['username' => $post['username']]) ?>">
                                                    <img src="<?= SITE_URL ?>/assets/uploads/avatars/<?= $post['avatar'] ?>" alt="صورة المستخدم" class="post-avatar me-3" style="width: 40px; height: 40px;">
                                                </a>
                                                <div>
                                                    <h6 class="card-title mb-0">
                                                        <a href="<?= generate_url('profile.php', ['username' => $post['username']]) ?>" class="text-decoration-none">
                                                            <?= $post['full_name'] ?>
                                                        </a>
                                                    </h6>
                                                    <small class="text-muted"><?= time_elapsed($post['created_at']) ?></small>
                                                </div>
                                            </div>
                                            <p class="card-text"><?= $post['content'] ?></p>
                                            
                                            <!-- عرض وسائط المنشور -->
                                            <?php if (!empty($post['media'])): ?>
                                                <?php foreach ($post['media'] as $media): ?>
                                                    <?php if ($media['file_type'] == 'image'): ?>
                                                        <img src="<?= SITE_URL ?>/assets/uploads/posts/<?= $media['file_path'] ?>" alt="صورة المنشور" class="post-image">
                                                    <?php elseif ($media['file_type'] == 'video'): ?>
                                                        <video src="<?= SITE_URL ?>/assets/uploads/posts/<?= $media['file_path'] ?>" controls class="post-video"></video>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                            
                                            <div class="d-flex justify-content-between mt-3">
                                                <a href="<?= generate_url('post.php', ['id' => $post['id']]) ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye me-1"></i> عرض المنشور
                                                </a>
                                                <span class="text-muted">
                                                    <i class="far fa-heart me-1"></i> <?= $post['likes_count'] ?>
                                                    <i class="far fa-comment ms-2 me-1"></i> <?= $post['comments_count'] ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <?php if ($category === 'posts' && $total_posts > $limit): ?>
                                <!-- ترقيم الصفحات -->
                                <nav aria-label="Page navigation" class="mt-3">
                                    <ul class="pagination justify-content-center">
                                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                            <a class="page-link" href="<?= generate_url('search.php', ['q' => $query, 'category' => $category, 'page' => $page - 1]) ?>" aria-label="Previous">
                                                <span aria-hidden="true">&laquo;</span>
                                            </a>
                                        </li>
                                        
                                        <?php
                                        $total_pages = ceil($total_posts / $limit);
                                        for ($i = 1; $i <= min(5, $total_pages); $i++):
                                        ?>
                                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                                <a class="page-link" href="<?= generate_url('search.php', ['q' => $query, 'category' => $category, 'page' => $i]) ?>"><?= $i ?></a>
                                            </li>
                                        <?php endfor; ?>
                                        
                                        <?php if ($total_pages > 5): ?>
                                            <li class="page-item disabled">
                                                <span class="page-link">...</span>
                                            </li>
                                            <li class="page-item">
                                                <a class="page-link" href="<?= generate_url('search.php', ['q' => $query, 'category' => $category, 'page' => $total_pages]) ?>"><?= $total_pages ?></a>
                                            </li>
                                        <?php endif; ?>
                                        
                                        <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                                            <a class="page-link" href="<?= generate_url('search.php', ['q' => $query, 'category' => $category, 'page' => $page + 1]) ?>" aria-label="Next">
                                                <span aria-hidden="true">&raquo;</span>
                                            </a>
                                        </li>
                                    </ul>
                                </nav>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($groups) && ($category === 'groups' || empty($category))): ?>
                    <!-- المجموعات -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center bg-white">
                            <h5 class="mb-0">المجموعات</h5>
                            <?php if ($total_groups > count($groups) && empty($category)): ?>
                                <a href="<?= generate_url('search.php', ['q' => $query, 'category' => 'groups']) ?>" class="btn btn-sm btn-outline-primary">عرض الكل (<?= $total_groups ?>)</a>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php foreach ($groups as $group): ?>
                                    <div class="col-md-6 mb-3">
                                        <div class="card h-100">
                                            <div class="card-header bg-<?= getGroupColor($group['id']) ?> text-white">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h5 class="mb-0"><?= $group['name'] ?></h5>
                                                    <span class="badge bg-light text-dark"><?= $group['members_count'] ?> عضوًا</span>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <p class="card-text"><?= mb_substr($group['description'], 0, 100) . (mb_strlen($group['description']) > 100 ? '...' : '') ?></p>
                                                <div class="d-flex mb-3">
                                                    <?php
                                                    // الحصول على بعض الأعضاء
                                                    $members = $groupClass->get_members($group['id'], 4, 0);
                                                    foreach ($members as $member):
                                                    ?>
                                                        <img src="<?= SITE_URL ?>/assets/uploads/avatars/<?= $member['avatar'] ?>" class="user-avatar me-1" title="<?= $member['full_name'] ?>" style="width: 30px; height: 30px;">
                                                    <?php endforeach; ?>
                                                    
                                                    <?php if ($group['members_count'] > 4): ?>
                                                        <div class="user-avatar d-flex align-items-center justify-content-center bg-light me-1" style="width: 30px; height: 30px;">+<?= $group['members_count'] - 4 ?></div>
                                                    <?php endif; ?>
                                                </div>
                                                <p class="text-muted mb-0"><small>تم الإنشاء: <?= date('d M Y', strtotime($group['created_at'])) ?></small></p>
                                            </div>
                                            <div class="card-footer bg-white">
                                                <div class="d-grid">
                                                    <?php if (isLoggedIn()): ?>
                                                        <?php if (isset($group['user_status']) && $group['user_status'] === 'approved'): ?>
                                                            <a href="<?= generate_url('group.php', ['id' => $group['id']]) ?>" class="btn btn-outline-success">
                                                                <i class="fas fa-door-open me-1"></i> دخول المجموعة
                                                            </a>
                                                        <?php elseif (isset($group['user_status']) && $group['user_status'] === 'pending'): ?>
                                                            <button class="btn btn-outline-secondary" disabled>
                                                                <i class="fas fa-clock me-1"></i> طلب الانضمام قيد المراجعة
                                                            </button>
                                                        <?php else: ?>
                                                            <button class="btn btn-outline-primary join-group-btn" data-group-id="<?= $group['id'] ?>">
                                                                <i class="fas fa-users me-1"></i> الانضمام للمجموعة
                                                            </button>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <a href="<?= generate_url('login.php', ['redirect' => 'group.php?id=' . $group['id']]) ?>" class="btn btn-outline-primary">
                                                            <i class="fas fa-sign-in-alt me-1"></i> تسجيل الدخول للانضمام
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <?php if ($category === 'groups' && $total_groups > $limit): ?>
                                <!-- ترقيم الصفحات -->
                                <nav aria-label="Page navigation" class="mt-3">
                                    <ul class="pagination justify-content-center">
                                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                            <a class="page-link" href="<?= generate_url('search.php', ['q' => $query, 'category' => $category, 'page' => $page - 1]) ?>" aria-label="Previous">
                                                <span aria-hidden="true">&laquo;</span>
                                            </a>
                                        </li>
                                        
                                        <?php
                                        $total_pages = ceil($total_groups / $limit);
                                        for ($i = 1; $i <= min(5, $total_pages); $i++):
                                        ?>
                                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                                <a class="page-link" href="<?= generate_url('search.php', ['q' => $query, 'category' => $category, 'page' => $i]) ?>"><?= $i ?></a>
                                            </li>
                                        <?php endfor; ?>
                                        
                                        <?php if ($total_pages > 5): ?>
                                            <li class="page-item disabled">
                                                <span class="page-link">...</span>
                                            </li>
                                            <li class="page-item">
                                                <a class="page-link" href="<?= generate_url('search.php', ['q' => $query, 'category' => $category, 'page' => $total_pages]) ?>"><?= $total_pages ?></a>
                                            </li>
                                        <?php endif; ?>
                                        
                                        <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                                            <a class="page-link" href="<?= generate_url('search.php', ['q' => $query, 'category' => $category, 'page' => $page + 1]) ?>" aria-label="Next">
                                                <span aria-hidden="true">&raquo;</span>
                                            </a>
                                        </li>
                                    </ul>
                                </nav>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// دالة للحصول على لون قائمة المجموعة
function getGroupColor($groupId) {
    $colors = ['primary', 'success', 'info', 'warning', 'danger', 'secondary'];
    return $colors[$groupId % count($colors)];
}

// تضمين تذييل الصفحة
include 'includes/footer.php';
?>

<script>
$(document).ready(function() {
    // متابعة/إلغاء متابعة المستخدم
    $('.follow-btn').on('click', function() {
        const btn = $(this);
        const userId = btn.data('user-id');
        const isFollowing = btn.hasClass('btn-secondary');
        
        $.ajax({
            url: '<?= SITE_URL ?>/api/users.php',
            type: 'POST',
            data: {
                action: isFollowing ? 'unfollow' : 'follow',
                user_id: userId,
                csrf_token: '<?= $_SESSION['csrf_token'] ?? '' ?>'
            },
            beforeSend: function() {
                btn.prop('disabled', true);
            },
            success: function(response) {
                if (response.status) {
                    if (isFollowing) {
                        btn.removeClass('btn-secondary').addClass('btn-primary');
                        btn.html('<i class="fas fa-user-plus me-1"></i> متابعة');
                    } else {
                        btn.removeClass('btn-primary').addClass('btn-secondary');
                        btn.html('<i class="fas fa-user-check me-1"></i> إلغاء المتابعة');
                    }
                } else {
                    alert(response.message || 'حدث خطأ أثناء تحديث المتابعة');
                }
            },
            error: function() {
                alert('حدث خطأ في الاتصال بالخادم');
            },
            complete: function() {
                btn.prop('disabled', false);
            }
        });
    });

    // الانضمام للمجموعة
    $('.join-group-btn').on('click', function() {
        const btn = $(this);
        const groupId = btn.data('group-id');
        
        $.ajax({
            url: '<?= SITE_URL ?>/api/groups.php',
            type: 'POST',
            data: {
                action: 'join',
                group_id: groupId,
                csrf_token: '<?= $_SESSION['csrf_token'] ?? '' ?>'
            },
            beforeSend: function() {
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> جاري المعالجة...');
            },
            success: function(response) {
                if (response.status) {
                    if (response.is_pending) {
                        btn.removeClass('btn-outline-primary').addClass('btn-outline-secondary');
                        btn.html('<i class="fas fa-clock me-1"></i> طلب الانضمام قيد المراجعة');
                        btn.prop('disabled', true);
                    } else {
                        btn.removeClass('btn-outline-primary').addClass('btn-outline-success');
                        btn.html('<i class="fas fa-door-open me-1"></i> دخول المجموعة');
                        btn.prop('disabled', false);
                        btn.off('click');
                        
                        // تحويل الزر إلى رابط للمجموعة
                        btn.wrap(`<a href="<?= SITE_URL ?>/group.php?id=${groupId}"></a>`);
                    }
                } else {
                    alert(response.message || 'حدث خطأ أثناء الانضمام للمجموعة');
                    btn.prop('disabled', false).html('<i class="fas fa-users me-1"></i> الانضمام للمجموعة');
                }
            },
            error: function() {
                alert('حدث خطأ في الاتصال بالخادم');
                btn.prop('disabled', false).html('<i class="fas fa-users me-1"></i> الانضمام للمجموعة');
            }
        });
    });
});
</script>