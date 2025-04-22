<?php
require_once '../config/config.php';
require_once '../config/db.php';
require_once '../classes/User.php';
require_once '../classes/Chat.php';

// التحقق مما إذا كان المستخدم مسجل الدخول
if (!isLoggedIn()) {
    redirect('login.php');
}

// التحقق من وجود اسم المستخدم في الاستعلام
if (!isset($_GET['user']) || empty($_GET['user'])) {
    redirect('chat/index.php');
}

$username = sanitize($_GET['user']);

// إنشاء كائنات الفئات
$database = new Database();
$user = new User($database);
$chat = new Chat($database);

// الحصول على معلومات المستخدم المراد الدردشة معه
$chat_user = $user->findUserByUsername1($username);

if (!$chat_user) {
    flash('المستخدم غير موجود', 'danger');
    redirect('chat/index.php');
}

// لا يمكن إجراء محادثة مع النفس
if ($chat_user->id === $_SESSION['user_id']) {
    redirect('chat/index.php');
}

// الحصول على المحادثة بين المستخدمين
$messages = $chat->getPrivateMessages($_SESSION['user_id'], $chat_user->id);

// تحديث حالة الرسائل إلى مقروءة
$chat->markMessagesAsRead($chat_user->id, $_SESSION['user_id']);

// الحصول على آخر المحادثات الخاصة (للقائمة الجانبية)
$private_chats = $chat->getPrivateChats($_SESSION['user_id']);

// الحصول على المجموعات التي ينتمي إليها المستخدم (للقائمة الجانبية)
$groups = $chat->getUserGroups($_SESSION['user_id']);

// تعيين عنوان الصفحة
$page_title = 'محادثة مع ' . $chat_user->full_name;

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
                                        <a href="<?php echo URL_ROOT; ?>/chat/private.php?user=<?php echo $chat_item->username; ?>" data-chat-id="<?php echo $chat_item->id; ?>" class="list-group-item chat-item list-group-item-action d-flex align-items-center <?php echo $chat_item->username === $username ? 'active' : ''; ?>">
                                            <div class="position-relative">
                                                <img src="<?php echo URL_ROOT; ?>/assets/uploads/profile/<?php echo $chat_item->profile_picture; ?>" class="rounded-circle me-2" width="40" height="40" alt="صورة المستخدم">
                                                <?php if ($chat_item->is_online): ?>
                                                    <span class="position-absolute bottom-0 end-0 bg-success rounded-circle" style="width: 10px; height: 10px;"></span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0"><?php echo $chat_item->full_name; ?></h6>
                                                <small class="last-message"><?php echo $chat_item->last_message ? (strlen($chat_item->last_message) > 20 ? substr($chat_item->last_message, 0, 20) . '...' : $chat_item->last_message) : 'لا توجد رسائل'; ?></small>
                                            </div>
                                            <?php if ($chat_item->unread_count > 0 && $chat_item->username !== $username): ?>
                                                <span class="badge bg-danger rounded-pill unread-count"><?php echo $chat_item->unread_count; ?></span>
                                            <?php else : ?>
                                                <span class="badge bg-danger rounded-pill unread-count"></span>
                                            <?php endif; ?>
                                            <small class="ms-2 message-time"><?= !empty($chat_item->last_message_time) ? formatTimeArabic($chat_item->last_message_time) : '' ?></small>
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
                                            <small class="ms-2"><?php echo formatTimeArabic(isset($chat_item->last_message_time) ? $chat_item->last_message_time : ''); ?></small>
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
                <div class="card-header bg-white py-2">
                    <div class="d-flex align-items-center">
                        <div class="position-relative">
                            <img src="<?php echo URL_ROOT; ?>/assets/uploads/profile/<?php echo $chat_user->profile_picture; ?>" class="rounded-circle me-2" width="40" height="40" alt="صورة المستخدم">
                            <span id="userStatus" class="position-absolute bottom-0 end-0 <?php echo $chat_user->is_online ? 'bg-success' : 'bg-secondary'; ?> rounded-circle" style="width: 10px; height: 10px;"></span>
                        </div>
                        <div>
                        <input type="hidden" id="receiverId_heide" value="<?php echo $chat_user->id; ?>">

                            <h5 class="mb-0"><?php echo $chat_user->full_name; ?></h5>
                            <small class="text-muted">@<?php echo $chat_user->username; ?> - <span id="statusText"><?php echo $chat_user->is_online ? 'متصل الآن' : 'غير متصل'; ?></span></small>
                        </div>
                        <div id="typingIndicator" style="display: none;">يكتب...</div>

                        <div class="ms-auto">
                            <a href="<?php echo URL_ROOT; ?>/profile.php?username=<?php echo $chat_user->username; ?>" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-user me-1"></i>عرض الملف الشخصي
                            </a>
                        </div>

                    </div>
                </div>
                <div class="card-body" style="height: 400px; overflow-y: auto;" id="chatMessages">
                    <div id="messagesContainer">
                        <?php if (empty($messages)): ?>
                            <div class="text-center py-4">
                                <p class="text-muted">ابدأ المحادثة مع <?php echo $chat_user->full_name; ?></p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($messages as $message): ?>
                                <div class="mb-3 <?php echo $message->sender_id === $_SESSION['user_id'] ? 'text-end' : ''; ?>">
                                    <div class="d-inline-block p-3 rounded-3 <?php echo $message->sender_id === $_SESSION['user_id'] ? 'bg-primary text-white' : 'bg-light'; ?>" style="max-width: 75%;">
                                        <?php if ($message->media_type !== 'text'): ?>
                                            <?php if ($message->media_type === 'image'): ?>
                                                <div class="mb-2">
                                                    <img src="<?php echo URL_ROOT; ?>/assets/uploads/chat/<?php echo $message->media_url; ?>" class="img-fluid rounded" alt="صورة">
                                                </div>
                                            <?php elseif ($message->media_type === 'video'): ?>
                                                <div class="mb-2">
                                                    <video controls class="img-fluid rounded">
                                                        <source src="<?php echo URL_ROOT; ?>/assets/uploads/chat/<?php echo $message->media_url; ?>" type="video/mp4">
                                                        المتصفح الخاص بك لا يدعم عنصر الفيديو.
                                                    </video>
                                                </div>
                                            <?php elseif ($message->media_type === 'audio'): ?>
                                                <div class="mb-2">
                                                    <audio controls>
                                                        <source src="<?php echo URL_ROOT; ?>/assets/uploads/chat/<?php echo $message->media_url; ?>" type="audio/mpeg">
                                                        المتصفح الخاص بك لا يدعم عنصر الصوت.
                                                    </audio>
                                                </div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($message->message)): ?>
                                            <p class="mb-0"><?php echo nl2br($message->message); ?></p>
                                        <?php endif; ?>
                                        
                                        <small class="<?php echo $message->sender_id === $_SESSION['user_id'] ? 'text-white-50' : 'text-muted'; ?> d-block text-end">
                                            <?php echo formatTimeArabic($message->created_at); ?>
                                            <?php if ($message->sender_id === $_SESSION['user_id']): ?>
                                                <?php if ($message->is_read): ?>
                                                    <i class="fas fa-check-double ms-1"></i>
                                                <?php else: ?>
                                                    <i class="fas fa-check ms-1"></i>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <form id="messageForm">
                        <div class="input-group">
                            <button type="button" class="btn btn-outline-secondary" id="attachmentBtn" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-paperclip"></i>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="attachmentBtn">
                                <li><a class="dropdown-item" href="#" id="imageAttachment"><i class="fas fa-image me-2"></i>صورة</a></li>
                                <li><a class="dropdown-item" href="#" id="videoAttachment"><i class="fas fa-video me-2"></i>فيديو</a></li>
                                <li><a class="dropdown-item" href="#" id="audioAttachment"><i class="fas fa-microphone me-2"></i>صوت</a></li>
                            </ul>
                            <input type="text" id="messageInput" class="form-control" placeholder="اكتب رسالتك هنا..." aria-label="Message" aria-describedby="sendMessageBtn">
                            <button class="btn btn-primary" type="submit" id="sendMessageBtn">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                        <div id="attachmentPreview" class="mt-2 d-none">
                            <div class="d-flex align-items-center p-2 bg-light rounded">
                                <i id="attachmentIcon" class="fas fa-file me-2"></i>
                                <span id="attachmentName" class="flex-grow-1"></span>
                                <button type="button" class="btn-close" id="removeAttachment"></button>
                            </div>
                        </div>
                        <input type="file" id="fileInput" class="d-none">
                        <input type="hidden" id="receiverId" value="<?php echo $chat_user->id; ?>">
                    </form>
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
    // تمرير متغيرات PHP إلى JavaScript
    const currentUserId = <?php echo $_SESSION['user_id']; ?>;
    const chatUserId = <?php echo $chat_user->id; ?>;
    const wsUrl = 'ws://<?php echo WS_HOST; ?>:<?php echo WS_PORT; ?>';
    const URL_ROOT = "<?php echo 'http://localhost/talent-hub'; ?>";
    
    // التمرير إلى سكريبت الدردشة
    // initializeChat(wsUrl, currentUserId, chatUserId);
    window.chatApp = new ChatApp(currentUserId, chatUserId, wsUrl);

    // تمرير معلومات إضافية
    const chatMessagesDiv = document.getElementById('chatMessages');
    chatMessagesDiv.scrollTop = chatMessagesDiv.scrollHeight;
    
    // البحث عن المستخدمين
    const searchUser = document.getElementById('searchUser');
    const userSearchResults = document.getElementById('userSearchResults');
    
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
                    if (user.id == currentUserId) return;
                    
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