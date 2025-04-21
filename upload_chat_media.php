<?php
require_once '../config/config.php';
require_once '../config/db.php';

// التحقق مما إذا كان المستخدم مسجل الدخول
if (!isLoggedIn()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// التحقق من طريقة الطلب
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// التحقق من وجود الملف
if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
    exit;
}

// التحقق من وجود المعلومات المطلوبة
if (!isset($_POST['type']) || !isset($_POST['sender_id']) || !isset($_POST['receiver_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

$type = sanitize($_POST['type']);
$sender_id = sanitize($_POST['sender_id']);
$receiver_id = sanitize($_POST['receiver_id']);

// التحقق من نوع الملف
$allowed_types = [];
$max_size = 10 * 1024 * 1024; // 10MB

switch ($type) {
    case 'image':
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        break;
    case 'video':
        $allowed_types = ['video/mp4', 'video/webm', 'video/ogg'];
        break;
    case 'audio':
        $allowed_types = ['audio/mpeg', 'audio/ogg', 'audio/wav'];
        break;
    default:
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid media type']);
        exit;
}

// التحقق من نوع الملف وحجمه
if (!in_array($_FILES['file']['type'], $allowed_types)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unsupported file type']);
    exit;
}

if ($_FILES['file']['size'] > $max_size) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'File size exceeds the maximum limit (10MB)']);
    exit;
}

// إنشاء مجلد التحميل إذا لم يكن موجودًا
$upload_dir = APP_ROOT . '/assets/uploads/chat/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// إنشاء اسم فريد للملف
$filename = uniqid() . '_' . $sender_id . '_' . $receiver_id . '_' . time() . '.' . pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
$upload_path = $upload_dir . $filename;

// نقل الملف المؤقت إلى المجلد المستهدف
if (move_uploaded_file($_FILES['file']['tmp_name'], $upload_path)) {
    // إرسال استجابة ناجحة
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'filename' => $filename]);
} else {
    // إرسال استجابة فشل
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file']);
}