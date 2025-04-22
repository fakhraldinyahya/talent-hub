class ChatApp {
    constructor(userId, receiverId, wsUrl) {
        this.userId = userId;
        this.receiverId = receiverId;
        this.wsUrl = wsUrl;
        this.websocket = null;
        this.typingTimeout = null;
        this.isTyping = false;
        this.URL_ROOT = window.URL_ROOT || '';

        this.initializeWebSocket();
        this.setupEventListeners();
    }

    initializeWebSocket() {
        this.websocket = new WebSocket(this.wsUrl);
        
        this.websocket.onopen = () => {
            console.log("اتصال WebSocket مفتوح");
            this.registerUser();
        };
        
        this.websocket.onmessage = (event) => {
            const data = JSON.parse(event.data);
            console.log("تم استقبال البيانات:", data);
            
            switch(data.type) {
                case 'status':
                    this.handleStatusUpdate(data);
                    break;
                    
                case 'private':
                    this.handleIncomingMessage(data);
                    break;
                    
                case 'typing':
                    this.handleTypingIndicator(data);
                    break;
                    
                case 'online_users':
                    this.updateOnlineStatus(data.users);
                    break;
                    
                case 'conversation_update':
                    this.updateChatListItem(data);
                    break;
                    
                case 'confirm':
                    this.handleMessageConfirmation(data);
                    break;
            }
        };
        
        this.websocket.onclose = () => {
            console.log("اتصال WebSocket مغلق - إعادة المحاولة...");
            setTimeout(() => this.initializeWebSocket(), 5000);
        };
        
        this.websocket.onerror = (error) => {
            console.error("خطأ في WebSocket:", error);
        };
    }

    registerUser() {
        this.websocket.send(JSON.stringify({
            type: 'register',
            userId: this.userId
        }));
    }

    setupEventListeners() {
        // إرسال الرسالة
        const messageForm = document.getElementById('messageForm');
        if (messageForm) {
            messageForm.addEventListener('submit', (e) => this.sendMessage(e));
        }
        
        // حقل إدخال الرسالة
        const messageInput = document.getElementById('messageInput');
        if (messageInput) {
            // مؤشر الكتابة
            messageInput.addEventListener('input', () => {
                this.sendTypingStatus(true);
                
                // إلغاء المؤشر السابق وإعادة تعيينه
                clearTimeout(this.typingTimeout);
                this.typingTimeout = setTimeout(() => {
                    this.sendTypingStatus(false);
                }, 2000);
            });
        }
        
        // مرفقات الصور/الملفات
        const fileInput = document.getElementById('fileInput');
        if (fileInput) {
            fileInput.addEventListener('change', (e) => this.handleFileSelect(e));
        }
    }

    sendMessage(e) {
        e.preventDefault();
        
        const messageInput = document.getElementById('messageInput');
        const message = messageInput.value.trim();
        
        if (message === '' && !this.mediaFile) return;
        
        // إرسال حالة التوقف عن الكتابة
        this.sendTypingStatus(false);
        clearTimeout(this.typingTimeout);
        
        if (this.mediaFile) {
            this.sendMediaMessage(message);
        } else {
            this.sendTextMessage(message);
        }
    }

    async sendMediaMessage(message) {
        const formData = new FormData();
        formData.append('file', this.mediaFile);
        formData.append('type', this.mediaType);
        formData.append('sender_id', this.userId);
        formData.append('receiver_id', this.receiverId);
        
        try {
            const response = await fetch(`${this.URL_ROOT}/upload_chat_media.php`, {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.websocket.send(JSON.stringify({
                    type: 'private',
                    receiver: this.receiverId,
                    message: message,
                    mediaType: this.mediaType,
                    mediaUrl: data.filename
                }));
                
                this.appendLocalMessage(message, data.filename);
                this.clearMessageInput();
            } else {
                alert('خطأ في رفع الملف: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('حدث خطأ أثناء إرسال الملف');
        }
    }

    sendTextMessage(message) {
        this.websocket.send(JSON.stringify({
            type: 'private',
            receiver: this.receiverId,
            message: message
        }));
        
        this.appendLocalMessage(message);
        this.clearMessageInput();
    }

    sendTypingStatus(isTyping) {
        if (this.isTyping !== isTyping) {
            this.isTyping = isTyping;
            this.websocket.send(JSON.stringify({
                type: 'typing',
                receiver: this.receiverId,
                isTyping: isTyping
            }));
        }
    }

    handleIncomingMessage(data) {
        if (data.senderId === this.receiverId) {
            // رسالة في الدردشة الحالية
            this.appendMessage(data, false);
            this.scrollToBottom();
            
            // إرسال تأكيد القراءة
            this.websocket.send(JSON.stringify({
                type: 'read_receipt',
                messageId: data.messageId
            }));
        } else {
            // رسالة من محادثة أخرى
            this.showNotification(data);
            this.updateChatListItem(data);
        }
    }

    handleTypingIndicator(data) {
        console.log(data,"datadatadatadatadatadatadatadatadata")
        const typingIndicator = document.getElementById('typingIndicator');
        if (!typingIndicator) return;
        
        if (data.isTyping) {
            // typingIndicator.textContent = `${data.senderName} يكتب...`;
            typingIndicator.style.display = 'block';
        } else {
            typingIndicator.style.display = 'none';
        }
    }

    appendMessage(data, isOwn) {
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
    
    
    innerContent += `<small class="${isOwn ? 'text-white-50' : 'text-muted'} d-block text-end">
        ${data.time}
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

    appendLocalMessage(message, mediaUrl = null) {
        const localMessage = {
            messageId: 'temp-' + Date.now(),
            senderId: this.userId,
            message: message,
            mediaType: this.mediaType || 'text',
            mediaUrl: mediaUrl,
            time: this.formatTime(new Date()),
        };
        
        this.appendMessage(localMessage, true);
        this.scrollToBottom();
    }

    updateChatListItem(data) {
        const chatItem = document.querySelector(`.chat-item[data-chat-id="${data.chatId}"]`);
        console.log("ddddddddddd",this.receiverId,data.chatId)
        if (chatItem) {
            // تحديث آخر رسالة
            const lastMessageEl = chatItem.querySelector('.last-message');
            if (lastMessageEl) {
                lastMessageEl.textContent = data.lastMessage;
            }
            
            // تحديث الوقت
            const timeEl = chatItem.querySelector('.message-time');
            if (timeEl) {
                timeEl.textContent = data.time;
            }
            
            // تحديث العداد
            const unreadEl = chatItem.querySelector('.unread-count');
            if (data.unreadCount > 0) {
                if (!unreadEl) {
                    const badge = document.createElement('span');
                    badge.className = 'unread-count';
                    chatItem.appendChild(badge);
                }
                if(this.receiverId == data.chatId){
                    this.websocket.send(JSON.stringify({
                        type: 'makRead',
                        receiver: this.receiverId,
                        sender: this.userId,
                    }));
                    console.log("dddddddddddddddddddddd")
                }else{
                    unreadEl.textContent = data.unreadCount;

                }
            } else if (unreadEl) {
                // unreadEl.remove();
            }
        }
    }
    handleStatusUpdate(data) {
        console.log("Connection status:", data.status);
    }

    updateOnlineStatus(users) {
        const statusElement = document.getElementById('userStatus');
        if (!statusElement) return;
        
        const isOnline = users.includes(this.receiverId);
        statusElement.textContent = isOnline ? 'متصل الآن' : 'غير متصل';
        statusElement.className = isOnline ? 'online' : 'offline';
    }

    showNotification(data) {
        // تحديث قائمة المحادثات
        this.updateChatListItem({
            chatId: data.senderId,
            lastMessage: data.message || 'مرفق',
            time: data.time,
            unreadCount: 1
        });
        
        // إشعار سطح المكتب
        if (Notification.permission === 'granted') {
            new Notification(`رسالة جديدة من ${data.senderName}`, {
                body: data.message || 'أرسل مرفق',
                icon: `${this.URL_ROOT}/assets/uploads/profile/${data.senderPicture}`
            });
        }
        
        // صوت الإشعار
        const audio = new Audio(`${this.URL_ROOT}/assets/sounds/notification.wav`);
        audio.play();
    }

    handleFileSelect(e) {
        const file = e.target.files[0];
        if (!file) return;
        
        // تحديد نوع الوسائط
        if (file.type.startsWith('image/')) {
            this.mediaType = 'image';
        } else if (file.type.startsWith('video/')) {
            this.mediaType = 'video';
        } else {
            this.mediaType = 'file';
        }
        
        this.mediaFile = file;
        this.showAttachmentPreview(file);
    }

    showAttachmentPreview(file) {
        const previewDiv = document.getElementById('attachmentPreview');
        const fileNameSpan = document.getElementById('attachmentName');
        
        fileNameSpan.textContent = file.name;
        previewDiv.style.display = 'flex';
    }

    clearMessageInput() {
        document.getElementById('messageInput').value = '';
        this.clearAttachment();
    }

    clearAttachment() {
        document.getElementById('fileInput').value = '';
        document.getElementById('attachmentPreview').style.display = 'none';
        this.mediaFile = null;
        this.mediaType = null;
    }

    scrollToBottom() {
        const container = document.getElementById('messagesContainer');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    }

    formatTime(date) {
        console.log(date,"datedatedatedatedatedatedatedate")
        return date.toLocaleTimeString('ar-SA', { hour: '2-digit', minute: '2-digit' });

    }
}

// تهيئة التطبيق عند تحميل الصفحة
// document.addEventListener('DOMContentLoaded', () => {
//     // const currentUserId = document.getElementById('currentUserId').value;
//     // const receiverId = document.getElementById('receiverId').value;
//     // const wsUrl = `ws://${window.location.hostname}:8080`;
    
//     // window.chatApp = new ChatApp(currentUserId, receiverId, wsUrl);
// });