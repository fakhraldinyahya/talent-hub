<?php
require_once 'config/config.php';
require_once 'config/db.php';
require_once 'classes/User.php';

// التحقق مما إذا كان المستخدم مسجل الدخول
if (!isLoggedIn()) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// التحقق من وجود استعلام البحث
if (!isset($_GET['q']) || empty($_GET['q'])) {
    header('Content-Type: application/json');
    echo json_encode([]);
    exit;
}

$query = sanitize($_GET['q']);

// إنشاء كائنات الفئات
$database = new Database();
$user = new User($database);

// البحث عن المستخدمين
$results = $user->searchUsers($query);

// تحويل النتائج إلى مصفوفة بسيطة
$users = [];
foreach ($results as $result) {
    $users[] = [
        'id' => $result->id,
        'username' => $result->username,
        'full_name' => $result->full_name,
        'profile_picture' => $result->profile_picture
    ];
}

// إرسال النتائج كـ JSON
header('Content-Type: application/json');
echo json_encode($users);
