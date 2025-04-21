<?php
class Admin {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    // الحصول على إحصائيات النظام
    public function getDashboardStats() {
        $stats = [];
        
        // عدد المستخدمين
        $this->db->query('SELECT COUNT(*) as count FROM users');
        $stats['users_count'] = $this->db->single()->count;
        
        $this->db->query("SELECT COUNT(*) as count FROM users WHERE DATE(created_at) = CURDATE()");
        $stats['new_users_today'] = $this->db->single()->count;

        // عدد المنشورات
        $this->db->query('SELECT COUNT(*) as count FROM posts');
        $stats['posts_count'] = $this->db->single()->count;
        
        $this->db->query("SELECT COUNT(*) as count FROM posts WHERE DATE(created_at) = CURDATE()");
        $stats['new_posts_today'] = $this->db->single()->count;

        // عدد التعليقات
        $this->db->query('SELECT COUNT(*) as count FROM comments');
        $stats['comments_count'] = $this->db->single()->count;
        
        // عدد الإعجابات
        $this->db->query('SELECT COUNT(*) as count FROM likes');
        $stats['likes_count'] = $this->db->single()->count;
        
        // عدد المجموعات
        $this->db->query('SELECT COUNT(*) as count FROM groups');
        $stats['groups_count'] = $this->db->single()->count;
        
        // عدد المجموعات الجديدة اليوم
        $this->db->query("SELECT COUNT(*) as count FROM groups WHERE DATE(created_at) = CURDATE()");
        $stats['new_groups_today'] = $this->db->single()->count;

        // توزيع أنواع المنشورات
        $this->db->query('SELECT media_type, COUNT(*) as count FROM posts GROUP BY media_type');
        $stats['post_types'] = $this->db->resultSet();
        
        // المستخدمين الذين سجلوا مؤخرًا
        $this->db->query('SELECT * FROM users ORDER BY created_at DESC LIMIT 5');
        $stats['recent_users'] = $this->db->resultSet();
        
        // المنشورات الأخيرة
        $this->db->query('
            SELECT p.*, u.username, u.profile_picture 
            FROM posts p 
            JOIN users u ON p.user_id = u.id 
            ORDER BY p.created_at DESC LIMIT 5
        ');
        $stats['recent_posts'] = $this->db->resultSet();
        
        $this->db->query('
            SELECT p.*, u.username, u.profile_picture, 
                COUNT(l.id) AS likes_count, COUNT(c.id) AS comments_count
            FROM posts p
            LEFT JOIN users u ON p.user_id = u.id
            LEFT JOIN likes l ON p.id = l.post_id
            LEFT JOIN comments c ON p.id = c.post_id
            GROUP BY p.id
            ORDER BY likes_count DESC, comments_count DESC
            LIMIT 5
        ');
        $stats['popular_posts'] = $this->db->resultSet();
        return $stats;
    }
    
    
    // تغيير دور المستخدم
    public function changeUserRole($userId, $role) {
        $this->db->query('UPDATE users SET role = :role WHERE id = :id');
        
        // ربط القيم
        $this->db->bind(':id', $userId);
        $this->db->bind(':role', $role);
        
        // تنفيذ
        return $this->db->execute();
    }
    
    // حظر مستخدم (تنفيذ افتراضي بسيط)
    public function banUser($userId) {
        $this->db->query('UPDATE users SET is_banned = 1 WHERE id = :id');
        
        // ربط القيم
        $this->db->bind(':id', $userId);
        
        // تنفيذ
        return $this->db->execute();
    }
    
    // إلغاء حظر مستخدم
    public function unbanUser($userId) {
        $this->db->query('UPDATE users SET is_banned = 0 WHERE id = :id');
        
        // ربط القيم
        $this->db->bind(':id', $userId);
        
        // تنفيذ
        return $this->db->execute();
    }
    
    // حذف مستخدم
    public function deleteUser($userId) {
        // حذف المنشورات والتعليقات والإعجابات الخاصة بالمستخدم
        $this->deleteUserContent($userId);
        
        // حذف المستخدم
        $this->db->query('DELETE FROM users WHERE id = :id');
        $this->db->bind(':id', $userId);
        
        return $this->db->execute();
    }
    
    // حذف محتوى المستخدم (المنشورات، التعليقات، الإعجابات، المجموعات، الرسائل)
    private function deleteUserContent($userId) {
        // حذف التعليقات والإعجابات على منشورات المستخدم
        $this->db->query('DELETE FROM comments WHERE post_id IN (SELECT id FROM posts WHERE user_id = :user_id)');
        $this->db->bind(':user_id', $userId);
        $this->db->execute();
        
        $this->db->query('DELETE FROM likes WHERE post_id IN (SELECT id FROM posts WHERE user_id = :user_id)');
        $this->db->bind(':user_id', $userId);
        $this->db->execute();
        
        // حذف منشورات المستخدم
        $this->db->query('DELETE FROM posts WHERE user_id = :user_id');
        $this->db->bind(':user_id', $userId);
        $this->db->execute();
        
        // حذف تعليقات المستخدم
        $this->db->query('DELETE FROM comments WHERE user_id = :user_id');
        $this->db->bind(':user_id', $userId);
        $this->db->execute();
        
        // حذف إعجابات المستخدم
        $this->db->query('DELETE FROM likes WHERE user_id = :user_id');
        $this->db->bind(':user_id', $userId);
        $this->db->execute();
        
        // حذف رسائل المستخدم الخاصة
        $this->db->query('DELETE FROM private_messages WHERE sender_id = :user_id OR receiver_id = :user_id');
        $this->db->bind(':user_id', $userId);
        $this->db->execute();
        
        // حذف رسائل المستخدم في المجموعات
        $this->db->query('DELETE FROM group_messages WHERE user_id = :user_id');
        $this->db->bind(':user_id', $userId);
        $this->db->execute();
        
        // حذف عضوية المستخدم في المجموعات
        $this->db->query('DELETE FROM group_members WHERE user_id = :user_id');
        $this->db->bind(':user_id', $userId);
        $this->db->execute();
        
        // حذف المجموعات التي أنشأها المستخدم
        $this->db->query('
            DELETE FROM groups WHERE id IN (
                SELECT id FROM (
                    SELECT id FROM groups WHERE created_by = :user_id
                ) as tmp
            )
        ');
        $this->db->bind(':user_id', $userId);
        $this->db->execute();
    }
    
    // حذف منشور
    public function deletePost($postId) {
        // حذف التعليقات والإعجابات المرتبطة بالمنشور
        $this->db->query('DELETE FROM comments WHERE post_id = :post_id');
        $this->db->bind(':post_id', $postId);
        $this->db->execute();
        
        $this->db->query('DELETE FROM likes WHERE post_id = :post_id');
        $this->db->bind(':post_id', $postId);
        $this->db->execute();
        
        // حذف المنشور
        $this->db->query('DELETE FROM posts WHERE id = :id');
        $this->db->bind(':id', $postId);
        
        return $this->db->execute();
    }
    
}
?>