<?php
require_once '../config/config.php';
require_once '../config/db.php';
require_once '../classes/User.php';
require_once '../classes/Chat.php';

// التحقق مما إذا كان المستخدم مسجل الدخول
if (!isLoggedIn()) {
    redirect('login.php');
}

// إنشاء كائنات الفئات
$database = new Database();
$user = new User($database);
$chat = new Chat($database);

// الحصول على آخر المحادثات الخاصة
$private_chats = $chat->getPrivateChats($_SESSION['user_id']);

// الحصول على المجموعات التي ينتمي إليها المستخدم
$groups = $chat->getUserGroups($_SESSION['user_id']);

$chat_user = $user->getUserById($_SESSION['user_id']);

if (!$chat_user) {
    flash('المستخدم غير موجود', 'danger');
    redirect('chat/index.php');
}
// تعيين عنوان الصفحة
$page_title = 'الدردشات';

// تمكين ملفات JavaScript الخاصة
$page_scripts = ['chat.js'];

require_once '../includes/header.php';
?>

<div class="container mt-3">
    <div class="row">
        <div class="col-lg-3 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">الدردشات</h5>
                    <div class="dropdown">
                        <?php if (isLoggedIn() && $user->isAdmin($_SESSION['user_id'])): ?>
                            <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-plus"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#newChatModal"><i class="fas fa-user me-2"></i>دردشة جديدة</a></li>
                                <li><a class="dropdown-item" href="<?php echo URL_ROOT; ?>/groups/create.php"><i class="fas fa-users me-2"></i>إنشاء مجموعة</a></li>
                            </ul>
                        <?php else: ?>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#newChatModal"><i class="fas fa-user me-2"></i>دردشة جديدة</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body p-0">
                    <ul class="nav nav-tabs" id="chatTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="private-tab" data-bs-toggle="tab" data-bs-target="#private" type="button" role="tab" aria-controls="private" aria-selected="true">
                                <i class="fas fa-user me-1"></i>الخاصة
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="groups-tab" data-bs-toggle="tab" data-bs-target="#groups" type="button" role="tab" aria-controls="groups" aria-selected="false">
                                <i class="fas fa-users me-1"></i>المجموعات
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content p-0" id="chatTabsContent">
                        <!-- قائمة الدردشات الخاصة -->
                        <div class="tab-pane fade show active" id="private" role="tabpanel" aria-labelledby="private-tab">
                            <div class="list-group list-group-flush chat-list">
                                <?php if (empty($private_chats)): ?>
                                    <div class="text-center py-4">
                                        <p class="text-muted mb-0">لا توجد محادثات خاصة حتى الآن.</p>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($private_chats as $chat_item): ?>
                                        <a href="<?php echo URL_ROOT; ?>/chat/private.php?user=<?php echo $chat_item->username; ?>" data-chat-id="<?php echo $chat_item->id; ?>" class="list-group-item chat-item list-group-item-action d-flex align-items-center">
                                            <div class="position-relative">
                                                <img src="<?php echo URL_ROOT; ?>/assets/uploads/profile/<?php echo $chat_item->profile_picture; ?>" class="rounded-circle me-2" width="40" height="40" alt="صورة المستخدم">
                                                <?php if ($chat_item->is_online): ?>
                                                    <span class="position-absolute bottom-0 end-0 bg-success rounded-circle" style="width: 10px; height: 10px;"></span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0"><?php echo $chat_item->full_name; ?></h6>
                                                <small class="text-muted"><?php echo $chat_item->last_message ? (strlen($chat_item->last_message) > 20 ? substr($chat_item->last_message, 0, 20) . '...' : $chat_item->last_message) : 'لا توجد رسائل'; ?></small>
                                            </div>
                                            <?php if ($chat_item->unread_count > 0): ?>
                                                <span class="badge bg-danger rounded-pill unread-count"><?php echo $chat_item->unread_count; ?></span>
                                            <?php else : ?>
                                                <span class="badge bg-danger rounded-pill unread-count"></span>
                                            <?php endif; ?>
                                            <small class="text-muted  ms-2 message-time"><?php echo $chat_item->last_message_time ? date('H:i', strtotime($chat_item->last_message_time)) : ''; ?></small>
                                        </a>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- قائمة المجموعات -->
                        <div class="tab-pane fade" id="groups" role="tabpanel" aria-labelledby="groups-tab">
                            <div class="list-group list-group-flush chat-list">
                                <?php if (empty($groups)): ?>
                                    <div class="text-center py-4">
                                        <p class="text-muted mb-0">لا توجد مجموعات حتى الآن.</p>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($groups as $group): ?>
                                        <a href="../groups/view.php?id=<?php echo $group->id; ?>" class="list-group-item list-group-item-action d-flex align-items-center">
                                            <div class="bg-primary text-white rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="fas fa-users"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0"><?php echo $group->name; ?></h6>
                                                <small class="text-muted"><?php echo $group->last_message ? (strlen($group->last_message) > 20 ? substr($group->last_message, 0, 20) . '...' : $group->last_message) : 'لا توجد رسائل'; ?></small>
                                            </div>
                                            <?php if ($group->unread_count > 0): ?>
                                                <span class="badge bg-primary rounded-pill"><?php echo $group->unread_count; ?></span>
                                            <?php endif; ?>
                                            <small class="text-muted ms-2"><?php echo $group->last_message_time ? date('H:i', strtotime($group->last_message_time)) : ''; ?></small>
                                        </a>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="card shadow-sm">
                <div class="card-body d-flex align-items-center justify-content-center" style="min-height: 400px;">
                    <div class="text-center">
                        <i class="fas fa-comments fa-4x text-muted mb-3"></i>
                        <h4>اختر محادثة للبدء</h4>
                        <p class="text-muted">يمكنك البدء بالدردشة مع أصدقائك أو إنشاء مجموعة جديدة.</p>
                        <div class="mt-3">
                            <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#newChatModal">
                                <i class="fas fa-user me-1"></i>دردشة جديدة
                            </button>
                            <?php if (isLoggedIn() && $user->isAdmin($_SESSION['user_id'])): ?>
                                <a href="<?php echo URL_ROOT; ?>/groups/create.php" class="btn btn-outline-primary">
                                    <i class="fas fa-users me-1"></i>إنشاء مجموعة
                                </a>
                            <?php else: ?>
                                <a href="<?php echo URL_ROOT; ?>/groups/index.php" class="btn btn-outline-primary">
                                    <i class="fas fa-users me-1"></i> المجموعات
                                </a>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- مودال الدردشة الجديدة -->
<div class="modal fade" id="newChatModal" tabindex="-1" aria-labelledby="newChatModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newChatModalLabel">بدء محادثة جديدة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <input type="text" class="form-control" id="searchUser" placeholder="ابحث عن مستخدم...">
                </div>
                <div id="userSearchResults" class="list-group">
                    <!-- نتائج البحث ستظهر هنا -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchUser = document.getElementById('searchUser');
        const userSearchResults = document.getElementById('userSearchResults');
        const currentUserId = <?php echo $_SESSION['user_id']; ?>;
        const chatUserId = <?php echo $chat_user->id; ?>;
        const wsUrl = 'ws://<?php echo WS_HOST; ?>:<?php echo WS_PORT; ?>';
        const URL_ROOT = "<?php echo 'http://localhost/talent-hub'; ?>";

        // initializeChat(wsUrl, currentUserId, chatUserId);
        window.chatApp = new ChatApp(currentUserId, chatUserId, wsUrl);
        searchUser.addEventListener('input', function() {
            const query = this.value.trim();
            if (query.length < 2) {
                userSearchResults.innerHTML = '';
                return;
            }

            // إرسال طلب البحث إلى الخادم
            fetch(`${URL_ROOT}/search_users.php?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    userSearchResults.innerHTML = '';

                    if (data.length === 0) {
                        userSearchResults.innerHTML = '<div class="text-center py-3 text-muted">لا توجد نتائج</div>';
                        return;
                    }

                    data.forEach(user => {
                        if (user.id == <?php echo $_SESSION['user_id']; ?>) return;

                        const item = document.createElement('a');
                        item.href = `${URL_ROOT}/chat/private.php?user=${user.username}`;
                        item.className = 'list-group-item list-group-item-action d-flex align-items-center';

                        item.innerHTML = `
                        <img src="${URL_ROOT}/assets/uploads/profile/${user.profile_picture}" class="rounded-circle me-2" width="40" height="40" alt="صورة المستخدم">
                        <div>
                            <h6 class="mb-0">${user.full_name}</h6>
                            <small class="text-muted">@${user.username}</small>
                        </div>
                    `;

                        userSearchResults.appendChild(item);
                    });
                })
                .catch(error => {
                    console.error('Error searching users:', error);
                    userSearchResults.innerHTML = '<div class="text-center py-3 text-danger">حدث خطأ أثناء البحث</div>';
                });
        });
    });
</script>

<?php require_once '../includes/footer.php'; ?>