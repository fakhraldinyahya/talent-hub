<?php
class Like {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    // إضافة إعجاب
    public function likePost($userId, $postId) {
        // التحقق مما إذا كان المستخدم قد أعجب بالمنشور بالفعل
        if ($this->hasUserLikedPost($userId, $postId)) {
            return true;
        }
        
        $this->db->query('INSERT INTO likes (user_id, post_id) VALUES (:user_id, :post_id)');
        
        // ربط القيم
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':post_id', $postId);
        
        // تنفيذ
        return $this->db->execute();
    }
    
    // إلغاء إعجاب
    public function unlikePost($userId, $postId) {
        $this->db->query('DELETE FROM likes WHERE user_id = :user_id AND post_id = :post_id');
        
        // ربط القيم
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':post_id', $postId);
        
        // تنفيذ
        return $this->db->execute();
    }
    
    // التحقق مما إذا كان المستخدم قد أعجب بالمنشور
    public function hasUserLikedPost($userId, $postId) {
        $this->db->query('SELECT * FROM likes WHERE user_id = :user_id AND post_id = :post_id');
        
        // ربط القيم
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':post_id', $postId);
        
        $this->db->execute();
        
        return $this->db->rowCount() > 0;
    }
    
    // الحصول على عدد الإعجابات لمنشور معين
    public function getLikesCount($postId) {
        $this->db->query('SELECT COUNT(*) as count FROM likes WHERE post_id = :post_id');
        $this->db->bind(':post_id', $postId);
        
        $row = $this->db->single();
        
        return $row->count;
    }
    
    // الحصول على المستخدمين الذين أعجبوا بمنشور معين
    public function getUsersWhoLiked($postId) {
        $this->db->query('
            SELECT u.id, u.username, u.profile_picture
            FROM likes l
            JOIN users u ON l.user_id = u.id
            WHERE l.post_id = :post_id
        ');
        
        $this->db->bind(':post_id', $postId);
        
        return $this->db->resultSet();
    }
    
    // الحصول على المنشورات التي أعجب بها مستخدم معين
    public function getPostsLikedByUser($userId) {
        $this->db->query('
            SELECT p.*, u.username, u.profile_picture, 
                   (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as likes_count,
                   (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comments_count
            FROM likes l
            JOIN posts p ON l.post_id = p.id
            JOIN users u ON p.user_id = u.id
            WHERE l.user_id = :user_id
            ORDER BY l.created_at DESC
        ');
        
        $this->db->bind(':user_id', $userId);
        
        return $this->db->resultSet();
    }
}
?>