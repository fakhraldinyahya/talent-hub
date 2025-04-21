<?php
require_once 'config/config.php';
require_once 'config/db.php';
require_once 'classes/Chat.php';

// التحقق مما إذا كان المستخدم مسجل الدخول
if (!isLoggedIn()) {
    redirect('login.php');
}

if (isset($_SESSION['user_id'])) {
    $database = new Database();
    $chat = new Chat($database);
    $chat->updateUserStatus($_SESSION['user_id'], false);
}

session_unset();
session_destroy();

redirect('login.php');
?>