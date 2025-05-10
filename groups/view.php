<?php
require_once '../config/config.php';
require_once '../config/db.php';
require_once '../classes/User.php';
require_once '../classes/Group.php';
require_once '../classes/Chat.php';

// التحقق مما إذا كان المستخدم مسجل الدخول
if (!isLoggedIn()) {
    redirect('login.php');
}

// التحقق من معرف المجموعة
if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('groups/index.php');
}

$group_id = sanitize($_GET['id']);

// إنشاء كائنات الفئات
$database = new Database();
$user = new User($database);
$group = new Group($database);
$chat = new Chat($database);

// الحصول على معلومات المجموعة
$group_data = $group->getGroupById($group_id);

if (!$group_data) {
    flash('المجموعة غير موجودة', 'danger');
    redirect('groups/index.php');
}

// التحقق مما إذا كان المستخدم عضوًا في المجموعة
if (!$group->isMember($group_id, $_SESSION['user_id'])) {
    flash('غير مصرح لك بعرض هذه المجموعة', 'danger');
    redirect('groups/index.php');
}

// الحصول على أعضاء المجموعة
$members = $group->getGroupMembers($group_id);

// الحصول على رسائل المجموعة
$messages = $group->getGroupMessages($group_id);

// تحديث حالة الرسائل إلى مقروءة
$group->markGroupMessagesAsRead($group_id, $_SESSION['user_id']);

// التحقق من دور المستخدم في المجموعة
$is_admin = $group->isGroupAdmin($group_id, $_SESSION['user_id']);

// تعيين عنوان الصفحة
$page_title = $group_data->name;

// تمكين ملفات JavaScript الخاصة
$page_scripts = ['chat.js'];

require_once '../includes/header.php';
?>

<div class="container ">
    <div class="row">
        <div class="col-lg-8">
            <!-- بطاقة الدردشة -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-2">
                    <div class="d-flex align-items-center">
                    <?php if (!empty($group_data->image)): ?>
                        <img src="<?php echo URL_ROOT; ?>/assets/uploads/profile/<?php echo $group_data->image; ?>"
                            class="rounded-circle me-3 object-fit-cover"
                            style="width: 50px; height: 50px;"
                            alt="<?php echo htmlspecialchars($group_data->name); ?>">
                    <?php else: ?>
                        <div class="bg-primary text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fas fa-users fa-lg"></i>
                        </div>
                    <?php endif; ?>
                        <div>
                            <h5 class="mb-0"><?php echo $group_data->name; ?></h5>
                            <small class="text-muted"><?php echo count($members); ?> عضو</small>
                        </div>
                        <div class="ms-auto">
                            <?php if ($is_admin): ?>
                                <a href="<?php echo URL_ROOT; ?>/groups/manage.php?id=<?php echo $group_id; ?>" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-cog me-1"></i>إدارة المجموعة
                                </a>
                            <?php else: ?>
                                <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#leaveGroupModal">
                                    <i class="fas fa-sign-out-alt me-1"></i>مغادرة المجموعة
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="card-body" style="height: 450px; overflow-y: auto;" id="chatMessages">
                    <div id="messagesContainer">
                        <?php if (empty($messages)): ?>
                            <div class="text-center py-4">
                                <p class="text-muted">لا توجد رسائل في هذه المجموعة حتى الآن. كن أول من يرسل رسالة!</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($messages as $message): ?>
                                <div class="mb-3 <?php echo $message->user_id === $_SESSION['user_id'] ? 'text-end' : ''; ?>">
                                    <?php if ($message->user_id !== $_SESSION['user_id']): ?>
                                        <div class="d-flex align-items-center mb-1">
                                            <img src="<?php echo URL_ROOT; ?>/assets/uploads/profile/<?php echo $message->profile_picture; ?>" class="rounded-circle me-1" width="24" height="24" alt="صورة المستخدم">
                                            <small><?php echo $message->username; ?></small>
                                        </div>
                                    <?php endif; ?>
                                    <div class="d-inline-block p-3 rounded-3 <?php echo $message->user_id === $_SESSION['user_id'] ? 'bg-primary text-white' : 'bg-light'; ?>" style="max-width: 75%;">
                                        <?php if ($message->media_type !== 'text'): ?>
                                            <?php if ($message->media_type === 'image'): ?>
                                                <div class="mb-2">
                                                    <img src="<?php echo URL_ROOT; ?>/assets/uploads/group/<?php echo $message->media_url; ?>" class="img-fluid rounded" alt="صورة">
                                                </div>
                                            <?php elseif ($message->media_type === 'video'): ?>
                                                <div class="mb-2">
                                                    <video controls class="img-fluid rounded">
                                                        <source src="<?php echo URL_ROOT; ?>/assets/uploads/group/<?php echo $message->media_url; ?>" type="video/mp4">
                                                        المتصفح الخاص بك لا يدعم عنصر الفيديو.
                                                    </video>
                                                </div>
                                            <?php elseif ($message->media_type === 'audio'): ?>
                                                <div class="mb-2">
                                                    <audio controls>
                                                        <source src="<?php echo URL_ROOT; ?>/assets/uploads/group/<?php echo $message->media_url; ?>" type="audio/mpeg">
                                                        المتصفح الخاص بك لا يدعم عنصر الصوت.
                                                    </audio>
                                                </div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($message->message)): ?>
                                            <p class="mb-0"><?php echo nl2br($message->message); ?></p>
                                        <?php endif; ?>
                                        
                                        <small class="<?php echo $message->user_id === $_SESSION['user_id'] ? 'text-white-50' : 'text-muted'; ?> d-block text-end">
                                            <?php echo formatTimeArabic($message->created_at); ?>
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
                        <input type="hidden" id="groupId" value="<?php echo $group_id; ?>">
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- بطاقة معلومات المجموعة -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">معلومات المجموعة</h5>
                </div>
                <div class="card-body">
                    <p><?php echo nl2br($group_data->description); ?></p>
                    <p class="mb-0">
                        <i class="fas fa-calendar-alt me-1"></i>
                        تم الإنشاء في <?php echo date('d/m/Y', strtotime($group_data->created_at)); ?>
                    </p>
                </div>
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" id="members-tab" data-bs-toggle="tab" href="#members" role="tab" aria-controls="members" aria-selected="true">الأعضاء</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="media-tab" data-bs-toggle="tab" href="#media" role="tab" aria-controls="media" aria-selected="false">الوسائط</a>
                    </li>
                </ul>
                <div class="card-body"  id="chatMessages">
                    <div class="tab-content" id="myTabContent">
                        <!-- تبويب الأعضاء -->
                        <div class="tab-pane fade show active" id="members" role="tabpanel" aria-labelledby="members-tab">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">الأعضاء (<?php echo count($members); ?>)</h5>
                                <?php if ($is_admin): ?>
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                                        <i class="fas fa-user-plus"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                            <div class="card-body p-0">
                                <ul class="list-group list-group-flush">
                                    <?php foreach ($members as $member): ?>
                                        <li class="list-group-item d-flex align-items-center">
                                            <img src="<?php echo URL_ROOT; ?>/assets/uploads/profile/<?php echo $member->profile_picture; ?>" class="rounded-circle me-2" width="32" height="32" alt="صورة المستخدم">
                                            <div>
                                                <div class="d-flex align-items-center">
                                                    <a href="<?php echo URL_ROOT; ?>/profile.php?username=<?php echo $member->username; ?>" class="text-decoration-none">
                                                        <?php echo ($member->id === $_SESSION['user_id']) ? 'أنت' : $member->full_name;  ?>
                                                    </a>
                                                    <?php if ($member->role === 'admin'): ?>
                                                        <span class="badge bg-primary ms-1">مشرف</span>
                                                    <?php endif; ?>
                                                    <?php if ($member->id === $group_data->created_by): ?>
                                                        <span class="badge bg-secondary ms-1">منشئ</span>
                                                    <?php endif; ?>
                                                </div>
                                                <small class="text-muted">@<?php echo $member->username; ?></small>
                                            </div>
                                            <?php if ($is_admin && $member->id !== $_SESSION['user_id']): ?>
                                                <div class="dropdown ms-auto">
                                                    <button class="btn btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <?php if ($member->role !== 'admin'): ?>
                                                            <li>
                                                                <a class="dropdown-item" href="<?php echo URL_ROOT; ?>/groups/manage.php?id=<?php echo $group_id; ?>&action=make_admin&user_id=<?php echo $member->id; ?>">
                                                                    <i class="fas fa-user-shield me-1"></i>جعله مشرفًا
                                                                </a>
                                                            </li>
                                                        <?php endif; ?>
                                                        <li>
                                                            <a class="dropdown-item text-danger" href="<?php echo URL_ROOT; ?>/groups/manage.php?id=<?php echo $group_id; ?>&action=remove_member&user_id=<?php echo $member->id; ?>">
                                                                <i class="fas fa-user-times me-1"></i>إزالة من المجموعة
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            </div>

    
                        <!-- تبويب الوسائط -->
                        <div class="tab-pane fade" id="media" role="tabpanel" aria-labelledby="media-tab">
                            <div class="row">
                                <?php foreach ($messages as $message): ?>
                                    <?php if ($message->media_type !== 'text'): ?>
                                        <div class="col-4 mb-3">
                                            <?php if ($message->media_type === 'image'): ?>
                                                <a href="<?php echo URL_ROOT; ?>/assets/uploads/group/<?php echo $message->media_url; ?>" data-lightbox="group-media" data-title="صورة من المجموعة">
                                                    <img src="<?php echo URL_ROOT; ?>/assets/uploads/group/<?php echo $message->media_url; ?>" class="img-fluid rounded" alt="صورة" style="max-width: 100%; height: auto;">
                                                </a>
                                            <?php elseif ($message->media_type === 'video'): ?>
                                                <video controls class="img-fluid rounded" style="max-width: 100%;">
                                                    <source src="<?php echo URL_ROOT; ?>/assets/uploads/group/<?php echo $message->media_url; ?>" type="video/mp4">
                                                    المتصفح الخاص بك لا يدعم عنصر الفيديو.
                                                </video>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- مودال مغادرة المجموعة -->
<div class="modal fade" id="leaveGroupModal" tabindex="-1" aria-labelledby="leaveGroupModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="leaveGroupModalLabel">تأكيد مغادرة المجموعة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>هل أنت متأكد من رغبتك في مغادرة هذه المجموعة؟</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <a href="<?php echo URL_ROOT; ?>/groups/index.php?action=leave&id=<?php echo $group_id; ?>" class="btn btn-danger">مغادرة</a>
            </div>
        </div>
    </div>
</div>

<!-- مودال إضافة عضو -->
<?php if ($is_admin): ?>
<div class="modal fade" id="addMemberModal" tabindex="-1" aria-labelledby="addMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addMemberModalLabel">إضافة أعضاء جدد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <input type="text" id="searchUser" class="form-control" placeholder="ابحث عن مستخدمين لإضافتهم...">
                </div>
                <div id="searchResults" class="list-group">
                    <!-- نتائج البحث ستظهر هنا -->
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // تمرير متغيرات PHP إلى JavaScript
    const currentUserId = <?php echo $_SESSION['user_id']; ?>;
    const groupId = <?php echo $group_id; ?>;
    const wsUrl = 'ws://<?php echo WS_HOST; ?>:<?php echo WS_PORT; ?>';
    
    // التمرير إلى سكريبت الدردشة المجموعة
    initializeGroupChat(wsUrl, currentUserId, groupId);
    
    // تمرير معلومات إضافية
    const chatMessagesDiv = document.getElementById('chatMessages');
    chatMessagesDiv.scrollTop = chatMessagesDiv.scrollHeight;
    
    <?php if ($is_admin): ?>
    // البحث عن المستخدمين لإضافتهم
    const searchUser = document.getElementById('searchUser');
    const searchResults = document.getElementById('searchResults');
    
    searchUser.addEventListener('input', function() {
        const query = this.value.trim();
        if (query.length < 2) {
            searchResults.innerHTML = '';
            return;
        }
        
        // الحصول على قائمة الأعضاء الحاليين
        const currentMembers = [
            <?php foreach ($members as $member): ?>
            <?php echo $member->id; ?>,
            <?php endforeach; ?>
        ];
        
        // بحث المستخدمين
        fetch(`${URL_ROOT}/search_users.php?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                searchResults.innerHTML = '';
                
                // تصفية المستخدمين الذين ليسوا أعضاء بالفعل
                const filteredUsers = data.filter(user => !currentMembers.includes(parseInt(user.id)));
                
                if (filteredUsers.length === 0) {
                    searchResults.innerHTML = '<div class="text-center py-3 text-muted">لا توجد نتائج أو جميع المستخدمين أعضاء بالفعل</div>';
                    return;
                }
                
                filteredUsers.forEach(user => {
                    const item = document.createElement('a');
                    item.href = `${URL_ROOT}/groups/manage.php?id=${groupId}&action=add_member&user_id=${user.id}`;
                    item.className = 'list-group-item list-group-item-action d-flex align-items-center';
                    
                    item.innerHTML = `
                        <img src="${URL_ROOT}/assets/uploads/profile/${user.profile_picture}" class="rounded-circle me-2" width="32" height="32" alt="صورة المستخدم">
                        <div>
                            <h6 class="mb-0">${user.full_name}</h6>
                            <small class="text-muted">@${user.username}</small>
                        </div>
                        <button class="btn btn-sm btn-outline-primary ms-auto add-member-btn">إضافة</button>
                    `;
                    
                    searchResults.appendChild(item);
                });
            })
            .catch(error => {
                console.error('Error searching users:', error);
                searchResults.innerHTML = '<div class="text-center py-3 text-danger">حدث خطأ أثناء البحث</div>';
            });
    });
    <?php endif; ?>
});

// دالة تهيئة دردشة المجموعة
function initializeGroupChat(wsUrl, userId, groupId) {
    // إنشاء اتصال WebSocket
    const websocket = new WebSocket(wsUrl);
    
    websocket.onopen = function(event) {
        console.log("WebSocket connection established");
        
        // تسجيل المستخدم في الخادم
        websocket.send(JSON.stringify({
            type: 'register',
            userId: userId
        }));
    };
    
    websocket.onmessage = function(event) {
        const data = JSON.parse(event.data);
        
        if (data.type === 'group' && data.groupId == groupId) {
            appendGroupMessage(data);
        }
    };
    
    // معالجة إرسال الرسائل
    const messageForm = document.getElementById('messageForm');
    const messageInput = document.getElementById('messageInput');
    const fileInput = document.getElementById('fileInput');
    
    // تعيين أزرار المرفقات
    const imageAttachment = document.getElementById('imageAttachment');
    const videoAttachment = document.getElementById('videoAttachment');
    const audioAttachment = document.getElementById('audioAttachment');
    const removeAttachment = document.getElementById('removeAttachment');
    const attachmentPreview = document.getElementById('attachmentPreview');
    const attachmentName = document.getElementById('attachmentName');
    const attachmentIcon = document.getElementById('attachmentIcon');
    
    let mediaFile = null;
    let mediaType = 'text';
    
    if (messageForm) {
        messageForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const message = messageInput.value.trim();
            
            if (message === '' && !mediaFile) {
                return;
            }
            
            // إذا كان هناك ملف مرفق
            if (mediaFile) {
                // تحميل الملف إلى الخادم أولاً
                const formData = new FormData();
                formData.append('file', mediaFile);
                formData.append('type', mediaType);
                formData.append('group_id', groupId);
                formData.append('user_id', userId);
                
                fetch(`${URL_ROOT}/upload_group_media.php`, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // إرسال الرسالة مع معلومات الوسائط عبر WebSocket
                        const messageObj = {
                            type: 'group',
                            groupId: groupId,
                            message: message,
                            mediaType: mediaType,
                            mediaUrl: data.filename
                        };
                        
                        websocket.send(JSON.stringify(messageObj));
                        
                        // مسح حقل الإدخال والمرفق
                        messageInput.value = '';
                        clearAttachment();
                    } else {
                        alert('حدث خطأ أثناء تحميل الملف: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error uploading file:', error);
                    alert('حدث خطأ أثناء تحميل الملف');
                });
            } else {
                // إرسال رسالة نصية فقط
                const messageObj = {
                    type: 'group',
                    groupId: groupId,
                    message: message
                };
                
                websocket.send(JSON.stringify(messageObj));
                
                // مسح حقل الإدخال
                messageInput.value = '';
            }
        });
    }
    
    // تعيين أحداث المرفقات
    if (imageAttachment) {
        imageAttachment.addEventListener('click', function(e) {
            e.preventDefault();
            mediaType = 'image';
            fileInput.accept = 'image/*';
            fileInput.click();
        });
    }
    
    if (videoAttachment) {
        videoAttachment.addEventListener('click', function(e) {
            e.preventDefault();
            mediaType = 'video';
            fileInput.accept = 'video/*';
            fileInput.click();
        });
    }
    
    if (audioAttachment) {
        audioAttachment.addEventListener('click', function(e) {
            e.preventDefault();
            mediaType = 'audio';
            fileInput.accept = 'audio/*';
            fileInput.click();
        });
    }
    
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) {
                clearAttachment();
                return;
            }
            
            // التحقق من حجم الملف (10MB كحد أقصى)
            const maxSize = 10 * 1024 * 1024;
            if (file.size > maxSize) {
                alert('حجم الملف كبير جدًا. الحد الأقصى هو 10 ميجابايت.');
                clearAttachment();
                return;
            }
            
            // حفظ مرجع الملف
            mediaFile = file;
            
            // عرض معاينة المرفق
            attachmentName.textContent = file.name;
            
            // تعيين أيقونة مناسبة
            if (mediaType === 'image') {
                attachmentIcon.className = 'fas fa-image me-2';
            } else if (mediaType === 'video') {
                attachmentIcon.className = 'fas fa-video me-2';
            } else if (mediaType === 'audio') {
                attachmentIcon.className = 'fas fa-microphone me-2';
            } else {
                attachmentIcon.className = 'fas fa-file me-2';
            }
            
            attachmentPreview.classList.remove('d-none');
        });
    }
    
    if (removeAttachment) {
        removeAttachment.addEventListener('click', function() {
            clearAttachment();
        });
    }
    
    // دالة مسح المرفق
    function clearAttachment() {
        mediaFile = null;
        mediaType = 'text';
        
        if (fileInput) {
            fileInput.value = '';
        }
        
        if (attachmentPreview) {
            attachmentPreview.classList.add('d-none');
        }
    }
}

// دالة إضافة رسالة مجموعة
function appendGroupMessage(data) {
    const messagesContainer = document.getElementById('messagesContainer');
    const currentUserId = <?php echo $_SESSION['user_id']; ?>;
    
    // إنشاء عنصر div للرسالة
    const messageDiv = document.createElement('div');
    messageDiv.className = `mb-3 ${data.senderId == currentUserId ? 'text-end' : ''}`;
    
    let innerContent = '';
    
    // إضافة معلومات المرسل إذا لم يكن المستخدم الحالي
    if (data.senderId != currentUserId) {
        innerContent += `
            <div class="d-flex align-items-center mb-1">
                <img src="${URL_ROOT}/assets/uploads/profile/${data.senderPicture}" class="rounded-circle me-1" width="24" height="24" alt="صورة المستخدم">
                <small>${data.senderName}</small>
            </div>
        `;
    }
    
    // إنشاء المحتوى الداخلي
    innerContent += `<div class="d-inline-block p-3 rounded-3 ${data.senderId == currentUserId ? 'bg-primary text-white' : 'bg-light'}" style="max-width: 75%;">`;
    
    // إضافة الوسائط إذا كانت موجودة
    if (data.mediaType && data.mediaType !== 'text' && data.mediaUrl) {
        if (data.mediaType === 'image') {
            innerContent += `<div class="mb-2">
                <img src="${URL_ROOT}/assets/uploads/group/${data.mediaUrl}" class="img-fluid rounded" alt="صورة">
            </div>`;
        } else if (data.mediaType === 'video') {
            innerContent += `<div class="mb-2">
                <video controls class="img-fluid rounded">
                    <source src="${URL_ROOT}/assets/uploads/group/${data.mediaUrl}" type="video/mp4">
                    المتصفح الخاص بك لا يدعم عنصر الفيديو.
                </video>
            </div>`;
        } else if (data.mediaType === 'audio') {
            innerContent += `<div class="mb-2">
                <audio controls>
                    <source src="${URL_ROOT}/assets/uploads/group/${data.mediaUrl}" type="audio/mpeg">
                    المتصفح الخاص بك لا يدعم عنصر الصوت.
                </audio>
            </div>`;
        }
    }
    
    // إضافة نص الرسالة إذا كان موجودًا
    if (data.message && data.message.trim() !== '') {
        innerContent += `<p class="mb-0">${data.message.replace(/\n/g, '<br>')}</p>`;
    }
    
    // إضافة الوقت
    // const timeStr = data.time ? new Date(data.time).toLocaleTimeString('ar-SA', { hour: '2-digit', minute: '2-digit' }) : new Date().toLocaleTimeString('ar-SA', { hour: '2-digit', minute: '2-digit' });
    
    innerContent += `<small class="${data.senderId == currentUserId ? 'text-white-50' : 'text-muted'} d-block text-end">
        ${data.time}
    </small>`;
    
    innerContent += '</div>';
    messageDiv.innerHTML = innerContent;
    
    // إضافة الرسالة إلى الحاوية
    messagesContainer.appendChild(messageDiv);
    
    // التمرير إلى أسفل المحادثة
    const chatMessages = document.getElementById('chatMessages');
    chatMessages.scrollTop = chatMessages.scrollHeight;
    
    // تشغيل صوت الإشعار إذا لم تكن الرسالة من المستخدم الحالي
    if (data.senderId != currentUserId) {
        const audio = new Audio(`${URL_ROOT}/assets/sounds/notification.wav`);
        audio.play();
    }
}
</script>
<?php require_once '../includes/footer.php'; ?>