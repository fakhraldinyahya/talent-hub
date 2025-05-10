// تهيئة المكونات عند تحميل المستند
document.addEventListener('DOMContentLoaded', function() {
    // تهيئة مكونات Bootstrap Tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // تهيئة مكونات Bootstrap Popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
    
    // التمرير السلس للروابط الداخلية
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });
    
    // إخفاء رسائل التنبيه تلقائيًا بعد 5 ثوانٍ
    setTimeout(function() {
        var alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
        alerts.forEach(function(alert) {
            var bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
    
    // تفعيل خاصية الوضع المظلم
    setupDarkMode();
    
    // تحقق مما إذا كان الجهاز يستخدم اللمس
    window.addEventListener('touchstart', function detectTouch() {
        document.body.classList.add('touch-device');
        window.removeEventListener('touchstart', detectTouch);
    });
    
    // أحداث النماذج
    setupFormEvents();
    
    // مراقبة تحميل الصور البطيء (Lazy Loading)
    setupLazyLoading();
});

// تفعيل خاصية الوضع المظلم
function setupDarkMode() {
    // التحقق من وضع النظام
    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        document.documentElement.classList.add('dark-mode');
    }
    
    // مراقبة تغييرات وضع النظام
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', event => {
        if (event.matches) {
            document.documentElement.classList.add('dark-mode');
        } else {
            document.documentElement.classList.remove('dark-mode');
        }
    });
    
    // زر تبديل الوضع (إذا كان موجودًا)
    const darkModeToggle = document.getElementById('darkModeToggle');
    if (darkModeToggle) {
        darkModeToggle.addEventListener('click', function() {
            document.documentElement.classList.toggle('dark-mode');
            
            // حفظ الإعداد في التخزين المحلي (إذا كان مدعومًا)
            if (typeof(Storage) !== 'undefined') {
                if (document.documentElement.classList.contains('dark-mode')) {
                    localStorage.setItem('darkMode', 'enabled');
                } else {
                    localStorage.setItem('darkMode', 'disabled');
                }
            }
        });
        
        // تطبيق الإعداد المحفوظ من التخزين المحلي
        if (typeof(Storage) !== 'undefined' && localStorage.getItem('darkMode') === 'enabled') {
            document.documentElement.classList.add('dark-mode');
        }
    }
}

// إعداد أحداث النماذج
function setupFormEvents() {
    // مراقبة تأكيد حذف العناصر
    document.querySelectorAll('.confirm-delete').forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('هل أنت متأكد من رغبتك في الحذف؟ هذا الإجراء لا يمكن التراجع عنه.')) {
                e.preventDefault();
            }
        });
    });
    
    // تحقق بسيط من كلمة المرور
    const passwordFields = document.querySelectorAll('input[type="password"]');
    
    // passwordFields.forEach(field => {
    //     if (field.id.includes('password')) {
    //         const form = field.closest('form');
    //         if (form) {
    //             form.addEventListener('submit', function(e) {
    //                 if (field.value.length < 6) {
    //                     alert('يجب أن تكون كلمة المرور 6 أحرف على الأقل');
    //                     e.preventDefault();
    //                 }
    //             });
    //         }
    //     }
    // });
    
    // معاينة الصور قبل التحميل
    const imageInputs = document.querySelectorAll('input[type="file"][accept*="image"]');
    
    imageInputs.forEach(input => {
        const previewId = input.dataset.preview;
        const preview = document.getElementById(previewId);
        
        if (preview) {
            input.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    };
                    
                    reader.readAsDataURL(this.files[0]);
                }
            });
        }
    });
}

// إعداد تحميل الصور البطيء
function setupLazyLoading() {
    // التحقق من دعم Intersection Observer API
    if ('IntersectionObserver' in window) {
        const lazyImages = document.querySelectorAll('.lazy-load');
        
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    
                    if (img.dataset.srcset) {
                        img.srcset = img.dataset.srcset;
                    }
                    
                    img.classList.remove('lazy-load');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        lazyImages.forEach(img => {
            imageObserver.observe(img);
        });
    } else {
        // Fallback لمتصفحات لا تدعم Intersection Observer
        const lazyImages = document.querySelectorAll('.lazy-load');
        
        function lazyLoad() {
            lazyImages.forEach(img => {
                if (img.getBoundingClientRect().top <= window.innerHeight && img.getBoundingClientRect().bottom >= 0) {
                    img.src = img.dataset.src;
                    
                    if (img.dataset.srcset) {
                        img.srcset = img.dataset.srcset;
                    }
                    
                    img.classList.remove('lazy-load');
                }
            });
        }
        
        // تحميل الصور عند التمرير
        document.addEventListener('scroll', lazyLoad);
        window.addEventListener('resize', lazyLoad);
        window.addEventListener('orientationChange', lazyLoad);
        lazyLoad(); // تحميل أولي
    }
}

// دالة للتحقق من وجود تحديثات للرسائل في الدردشة
function checkForNewMessages(userId, lastMessageId) {
    fetch(`${URL_ROOT}/api/check_messages.php?user_id=${userId}&last_message_id=${lastMessageId}`)
        .then(response => response.json())
        .then(data => {
            if (data.new_messages) {
                // تحديث واجهة المستخدم لإظهار الرسائل الجديدة
                const chatCount = document.getElementById('chatCount');
                if (chatCount) {
                    chatCount.textContent = data.unread_count;
                    chatCount.classList.remove('d-none');
                }
                
                // تشغيل صوت الإشعار
                const audio = new Audio(`${URL_ROOT}/assets/sounds/notification.mp3`);
                audio.play();
            }
        })
        .catch(error => console.error('Error checking for new messages:', error));
}

// دالة للإبلاغ عن محتوى
function reportContent(contentType, contentId) {
    // إظهار نموذج الإبلاغ
    const modal = new bootstrap.Modal(document.getElementById('reportModal'));
    
    // تعيين معلومات المحتوى المبلغ عنه
    document.getElementById('reportContentType').value = contentType;
    document.getElementById('reportContentId').value = contentId;
    
    modal.show();
}