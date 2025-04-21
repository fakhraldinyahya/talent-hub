let websocket;
let connectedUserId;
let chatWithUserId;
let mediaFile = null;
let mediaType = 'text';

function initializeChat(wsUrl, userId, receiverId) {
    connectedUserId = userId;
    chatWithUserId = receiverId;
    
    // إنشاء اتصال WebSocket
    websocket = new WebSocket(wsUrl);
    
    websocket.onopen = function(event) {
        console.log("WebSocket connection established");
        
        // تسجيل المستخدم في الخادم
        websocket.send(JSON.stringify({
            type: 'register',
            userId: connectedUserId
        }));
    };
    
    websocket.onmessage = function(event) {
        const data = JSON.parse(event.data);
        console.log("Received messagedatadatadata:", data);
        switch (data.type) {
            case 'status':
                console.log("Connection status:", data.status);
                break;
                
            case 'onlineUsers':
                updateOnlineStatus(data.users);
                break;
            case 'unreadUpdate':
                updateCountLastmessage(data);
                break;
                
            case 'private':
                if (data.senderId == chatWithUserId) {
                    appendMessage(data, false);
                } else {
                    // إشعار بوصول رسالة من مستخدم آخر
                    console.log("sddddddddddddddddddddd")
                    showNotification(data);
                }
                break;
                
            case 'confirm':
                // تأكيد إرسال الرسالة
                console.log("Message sent successfully:", data.messageId);
                break;
        }
    };
    
    websocket.onclose = function(event) {
        console.log("WebSocket connection closed");
        // محاولة إعادة الاتصال بعد 5 ثوانٍ
        setTimeout(function() {
            initializeChat(wsUrl, connectedUserId, chatWithUserId);
        }, 5000);
    };
    
    websocket.onerror = function(error) {
        console.error("WebSocket error:", error);
    };
    
    // تهيئة النموذج
    const messageForm = document.getElementById('messageForm');
    if (messageForm) {
        messageForm.addEventListener('submit', sendMessage);
    }
    
    // تهيئة أزرار المرفقات
    const imageAttachment = document.getElementById('imageAttachment');
    const videoAttachment = document.getElementById('videoAttachment');
    const audioAttachment = document.getElementById('audioAttachment');
    const fileInput = document.getElementById('fileInput');
    const removeAttachment = document.getElementById('removeAttachment');
    
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
        fileInput.addEventListener('change', handleFileSelect);
    }
    
    if (removeAttachment) {
        removeAttachment.addEventListener('click', function() {
            clearAttachment();
        });
    }
}

// إرسال رسالة
function sendMessage(e) {
    e.preventDefault();
    
    const messageInput = document.getElementById('messageInput');
    const receiverId = document.getElementById('receiverId').value;
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
        formData.append('sender_id', connectedUserId);
        formData.append('receiver_id', receiverId);
        
        fetch(`${URL_ROOT}/upload_chat_media.php`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // إرسال الرسالة مع معلومات الوسائط عبر WebSocket
                const messageObj = {
                    type: 'private',
                    receiver: receiverId,
                    message: message,
                    mediaType: mediaType,
                    mediaUrl: data.filename
                };
                
                websocket.send(JSON.stringify(messageObj));
                
                // إضافة الرسالة إلى المحادثة محليًا
                const localMessage = {
                    messageId: Date.now(),
                    senderId: connectedUserId,
                    message: message,
                    mediaType: mediaType,
                    mediaUrl: data.filename,
                    time: new Date().toISOString()
                };
                
                appendMessage(localMessage, true);
                
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
            type: 'private',
            receiver: receiverId,
            message: message
        };
        
        websocket.send(JSON.stringify(messageObj));
        
        // إضافة الرسالة إلى المحادثة محليًا
        const localMessage = {
            messageId: Date.now(),
            senderId: connectedUserId,
            message: message,
            mediaType: 'text',
            time: new Date().toISOString()
        };
        
        appendMessage(localMessage, true);
        
        // مسح حقل الإدخال
        messageInput.value = '';
    }
    
    // التمرير إلى أسفل المحادثة
    const chatMessages = document.getElementById('chatMessages');
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

// إضافة رسالة إلى المحادثة
function appendMessage(data, isOwn) {
    const messagesContainer = document.getElementById('messagesContainer');
    
    // إنشاء عنصر div للرسالة
    const messageDiv = document.createElement('div');
    messageDiv.className = `mb-3 ${isOwn ? 'text-end' : ''}`;
    
    // إنشاء المحتوى الداخلي
    let innerContent = `<div class="d-inline-block p-3 rounded-3 ${isOwn ? 'bg-primary text-white' : 'bg-light'}" style="max-width: 75%;">`;
    
    // إضافة الوسائط إذا كانت موجودة
    if (data.mediaType && data.mediaType !== 'text' && data.mediaUrl) {
        if (data.mediaType === 'image') {
            innerContent += `<div class="mb-2">
                <img src="${URL_ROOT}/assets/uploads/chat/${data.mediaUrl}" class="img-fluid rounded" alt="صورة">
            </div>`;
        } else if (data.mediaType === 'video') {
            innerContent += `<div class="mb-2">
                <video controls class="img-fluid rounded">
                    <source src="${URL_ROOT}/assets/uploads/chat/${data.mediaUrl}" type="video/mp4">
                    المتصفح الخاص بك لا يدعم عنصر الفيديو.
                </video>
            </div>`;
        } else if (data.mediaType === 'audio') {
            innerContent += `<div class="mb-2">
                <audio controls>
                    <source src="${URL_ROOT}/assets/uploads/chat/${data.mediaUrl}" type="audio/mpeg">
                    المتصفح الخاص بك لا يدعم عنصر الصوت.
                </audio>
            </div>`;
        }
    }
    
    // إضافة نص الرسالة إذا كان موجودًا
    if (data.message && data.message.trim() !== '') {
        innerContent += `<p class="mb-0">${data.message.replace(/\n/g, '<br>')}</p>`;
    }
    
    // إضافة الوقت وعلامات القراءة
    const timeStr = data.time ? new Date(data.time).toLocaleTimeString('ar-SA', { hour: '2-digit', minute: '2-digit' }) : new Date().toLocaleTimeString('ar-SA', { hour: '2-digit', minute: '2-digit' });
    
    innerContent += `<small class="${isOwn ? 'text-white-50' : 'text-muted'} d-block text-end">
        ${timeStr}
        ${isOwn ? '<i class="fas fa-check ms-1"></i>' : ''}
    </small>`;
    
    innerContent += '</div>';
    messageDiv.innerHTML = innerContent;
    
    // إضافة الرسالة إلى الحاوية
    messagesContainer.appendChild(messageDiv);
    
    // التمرير إلى أسفل المحادثة
    const chatMessages = document.getElementById('chatMessages');
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

// تحديث حالة الاتصال للمستخدمين
function updateOnlineStatus(users) {
    const userStatus = document.getElementById('userStatus');
    const statusText = document.getElementById('statusText');
    
    // البحث عن المستخدم الذي نتحدث معه
    if(userStatus){
        const chatUser = users.find(user => user.id == chatWithUserId);
        if (chatUser) {
            // تحديث الحالة إلى متصل
            userStatus.className = 'position-absolute bottom-0 end-0 bg-success rounded-circle';
            userStatus.style.width = '10px';
            userStatus.style.height = '10px';
            
            if (statusText) {
                statusText.textContent = 'متصل الآن';
            }
        } else {
            // تحديث الحالة إلى غير متصل
            userStatus.className = 'position-absolute bottom-0 end-0 bg-secondary rounded-circle';
            userStatus.style.width = '10px';
            userStatus.style.height = '10px';
            
            if (statusText) {
                statusText.textContent = 'غير متصل';
            }
        }
    }
}
function updateCountLastmessage(data) {
    const chatItem = document.querySelector(`.list-group-item[data-chat-id="${data.chatId}"]`);
    if (chatItem) {
        const lastMessageElement = chatItem.querySelector('.last-message');
        if (lastMessageElement) {
            lastMessageElement.textContent = data.lastMessage;
        }

        const unreadBadge = chatItem.querySelector('.badge');
        if (unreadBadge) {
            unreadBadge.textContent = data.unreadCount;
        } else if (data.unreadCount > 0) {
            const newBadge = document.createElement('span');
            newBadge.className = 'badge bg-primary rounded-pill';
            newBadge.textContent = data.unreadCount;
            chatItem.appendChild(newBadge);
        }
        const timeElement = chatItem.querySelector('.last-message-time');
        
        if (timeElement) {
            timeElement.textContent = data.time;
        }
    }
}

// عرض إشعار بوصول رسالة جديدة
function showNotification(data) {
    // تحديث عدد الرسائل غير المقروءة في القائمة الجانبية
    const chatItems = document.querySelectorAll('.chat-list .list-group-item');
    
    chatItems.forEach(item => {
        const username = item.getAttribute('href').split('=')[1];
        
        // التحقق مما إذا كان الرابط يتوافق مع المرسل
        if (data.senderName && username === data.senderName) {
            // تحديث نص آخر رسالة
            const lastMessageEl = item.querySelector('.flex-grow-1 small');
            if (lastMessageEl) {
                const messagePreview = data.message && data.message.length > 20 
                    ? data.message.substring(0, 20) + '...' 
                    : data.message || 'صورة';
                lastMessageEl.textContent = messagePreview;
            }
            
            // تحديث عدد الرسائل غير المقروءة
            let badgeEl = item.querySelector('.badge');
            if (!badgeEl) {
                badgeEl = document.createElement('span');
                badgeEl.className = 'badge bg-primary rounded-pill';
                item.appendChild(badgeEl);
            }
            
            const count = parseInt(badgeEl.textContent || '0') + 1;
            badgeEl.textContent = count;
            
            // تحديث وقت آخر رسالة
            const timeEl = item.querySelector('small.text-muted.ms-2');
            if (timeEl) {
                const time = new Date(data.time).toLocaleTimeString('ar-SA', { hour: '2-digit', minute: '2-digit' });
                timeEl.textContent = time;
            }
            
            return;
        }
    });
    
    // يمكن إضافة إشعار صوتي أو رسالة منبثقة هنا
    if ('Notification' in window && Notification.permission !== 'granted') {
        Notification.requestPermission();
    }
    if ('Notification' in window && Notification.permission === 'granted') {
        const notification = new Notification('رسالة جديدة', {
            body: `رسالة جديدة من ${data.senderName}`,
            icon: `${URL_ROOT}/assets/uploads/profile/${data.senderPicture}`
        });
        
        notification.onclick = function() {
            window.focus();
            window.location.href = `${URL_ROOT}/chat/private.php?user=${data.senderName}`;
        };
    }
    
    // تشغيل صوت الإشعار
    const audio = new Audio(`${URL_ROOT}/assets/sounds/notification.mp3`);
    audio.play();
}

// معالجة اختيار ملف
function handleFileSelect(e) {
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
    const attachmentPreview = document.getElementById('attachmentPreview');
    const attachmentName = document.getElementById('attachmentName');
    const attachmentIcon = document.getElementById('attachmentIcon');
    
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
}

// إزالة المرفق
function clearAttachment() {
    mediaFile = null;
    mediaType = 'text';
    
    const fileInput = document.getElementById('fileInput');
    const attachmentPreview = document.getElementById('attachmentPreview');
    
    if (fileInput) {
        fileInput.value = '';
    }
    
    if (attachmentPreview) {
        attachmentPreview.classList.add('d-none');
    }
}