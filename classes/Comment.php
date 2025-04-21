<?php
class Comment {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    // إضافة تعليق جديد
    public function addComment($data) {
        $this->db->query('INSERT INTO comments (user_id, post_id, content) VALUES (:user_id, :post_id, :content)');
        
        // ربط القيم
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':post_id', $data['post_id']);
        $this->db->bind(':content', $data['content']);
        
        // تنفيذ
        return $this->db->execute();
    }
    
    // الحصول على تعليقات منشور معين
    public function getCommentsByPostId($postId) {
        $this->db->query('
            SELECT c.*, u.username, u.profile_picture
            FROM comments c
            JOIN users u ON c.user_id = u.id
            WHERE c.post_id = :post_id
            ORDER BY c.created_at DESC
        ');
        
        $this->db->bind(':post_id', $postId);
        
        return $this->db->resultSet();
    }
    
    // الحصول على تعليق معين
    public function getCommentById($commentId) {
        $this->db->query('SELECT * FROM comments WHERE id = :id');
        $this->db->bind(':id', $commentId);
        
        return $this->db->single();
    }
    
    // التحقق مما إذا كان التعليق ينتمي للمستخدم
    public function isUserComment($commentId, $userId) {
        $this->db->query('SELECT * FROM comments WHERE id = :id AND user_id = :user_id');
        $this->db->bind(':id', $commentId);
        $this->db->bind(':user_id', $userId);
        
        $this->db->execute();
        
        return $this->db->rowCount() > 0;
    }
    
    // حذف تعليق
    public function deleteComment($commentId, $userId) {
        // التحقق من صلاحية المستخدم لحذف التعليق
        if (!$this->isUserComment($commentId, $userId) && !isAdmin()) {
            return false;
        }
        
        $this->db->query('DELETE FROM comments WHERE id = :id');
        $this->db->bind(':id', $commentId);
        
        return $this->db->execute();
    }
    
    // تحديث تعليق
    public function updateComment($data) {
        $this->db->query('UPDATE comments SET content = :content WHERE id = :id AND user_id = :user_id');
        
        // ربط القيم
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':content', $data['content']);
        
        // تنفيذ
        return $this->db->execute();
    }
    
    // الحصول على عدد التعليقات لمنشور معين
    public function getCommentsCount($postId) {
        $this->db->query('SELECT COUNT(*) as count FROM comments WHERE post_id = :post_id');
        $this->db->bind(':post_id', $postId);
        
        $row = $this->db->single();
        
        return $row->count;
    }
    
    // الحصول على تعليقات المستخدم
    public function getUserComments($userId) {
        $this->db->query('
            SELECT c.*, p.title as post_title, p.id as post_id
            FROM comments c
            JOIN posts p ON c.post_id = p.id
            WHERE c.user_id = :user_id
            ORDER BY c.created_at DESC
        ');
        
        $this->db->bind(':user_id', $userId);
        
        return $this->db->resultSet();
    }
}
?>