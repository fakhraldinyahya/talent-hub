<?php
session_start();

// معلومات التكوين الأساسية
define('SITE_NAME', 'Talent Hub');
define('APP_ROOT', dirname(dirname(__FILE__)));
define('URL_ROOT', 'http://localhost/talent-hub');
define('UPLOAD_DIR', APP_ROOT . '/assets/uploads/');
define('PROFILE_PIC_DIR', UPLOAD_DIR . 'profile/');
define('POSTS_MEDIA_DIR', UPLOAD_DIR . 'posts/');

// تكوين قاعدة البيانات
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'talent_hub');

// معلومات WebSocket
define('WS_HOST', '127.0.0.1');
define('WS_PORT', 8080);

// دالة مساعدة لتوجيه المستخدم
function redirect($page) {
    header('Location: ' . URL_ROOT . '/' . $page);
    exit;
}

// دالة للتحقق من تسجيل دخول المستخدم
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// دالة للتحقق من صلاحيات المسؤول
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

// دالة لعرض رسائل الخطأ
function flash($message, $type = 'danger') {
    if (!isset($_SESSION['flash'])) {
        $_SESSION['flash'] = array();
    }
    $_SESSION['flash'][] = ['message' => $message, 'type' => $type];
}

// دالة لعرض رسائل الخطأ المخزنة
function display_flash() {
    if (isset($_SESSION['flash'])) {
        foreach ($_SESSION['flash'] as $flash) {
            echo '<div class="alert alert-' . $flash['type'] . ' alert-dismissible fade show" role="alert">
                    ' . $flash['message'] . '
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
        }
        unset($_SESSION['flash']);
    }
}

// دالة لتنظيف المدخلات
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}