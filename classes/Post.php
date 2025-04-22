<?php
class Post {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    // إنشاء منشور جديد
    public function createPost($data) {
        $this->db->query('INSERT INTO posts (user_id, title, content, media_type, media_url) VALUES (:user_id, :title, :content, :media_type, :media_url)');
        
        // ربط القيم
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':content', $data['content']);
        $this->db->bind(':media_type', $data['media_type']);
        $this->db->bind(':media_url', $data['media_url']);
        
        // تنفيذ
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }
    
    // الحصول على جميع المنشورات
    public function getAllPosts() {
        $this->db->query('
            SELECT p.*, u.username, u.profile_picture, 
                   (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as likes_count,
                   (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comments_count
            FROM posts p
            JOIN users u ON p.user_id = u.id
            ORDER BY p.created_at DESC
        ');
        
        return $this->db->resultSet();
    }
    
    // الحصول على منشورات مستخدم معين
    public function getPostsByUser($userId) {
        $this->db->query('
            SELECT p.*, u.username, u.profile_picture, 
                   (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as likes_count,
                   (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comments_count
            FROM posts p
            JOIN users u ON p.user_id = u.id
            WHERE p.user_id = :user_id
            ORDER BY p.created_at DESC
        ');
        
        $this->db->bind(':user_id', $userId);
        
        return $this->db->resultSet();
    }
    
    // الحصول على المنشورات بواسطة النوع
    public function getPostsByMediaType($postId) {
        $this->db->query('
            SELECT p.*, u.username, u.profile_picture, u.full_name,
                   (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as likes_count,
                   (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comments_count
            FROM posts p
            JOIN users u ON p.user_id = u.id
            WHERE media_type = :media_type
        ');
        
        $this->db->bind(':media_type', $postId);
        
        return $this->db->resultSet();
    }
    
    public function getPostById($postId) {
        $this->db->query('
            SELECT p.*, u.username, u.profile_picture, u.full_name,
                   (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as likes_count,
                   (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comments_count
            FROM posts p
            JOIN users u ON p.user_id = u.id
            WHERE p.id = :post_id
        ');
        
        $this->db->bind(':post_id', $postId);
        
        return $this->db->single();
    }
    // تحديث منشور
    public function updatePost($data) {
        $this->db->query('UPDATE posts SET title = :title, content = :content, media_type = :media_type, media_url = :media_url WHERE id = :id AND user_id = :user_id');
        
        // ربط القيم
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':content', $data['content']);
        $this->db->bind(':media_type', $data['media_type']);
        $this->db->bind(':media_url', $data['media_url']);
        
        // تنفيذ
        return $this->db->execute();
    }
    
    // حذف منشور
    public function deletePost($postId, $userId) {
        // التحقق من صلاحية المستخدم لحذف المنشور
        if (!$this->isUserPost($postId, $userId) && !isAdmin()) {
            return false;
        }
        
        $this->db->query('DELETE FROM posts WHERE id = :id');
        $this->db->bind(':id', $postId);
        
        return $this->db->execute();
    }
    // الحصول على إجمالي عدد المنشورات (للتصفح)
    public function getTotalPosts() {
        $this->db->query('SELECT COUNT(*) as count FROM posts');
        return $this->db->single()->count;
    }
    
    private $categoriesMap = [
        'audio' => ['singing', 'playing_instruments', 'recitation', 'voice_imitation', 'commentary'],
        'video' => ['acting', 'dancing', 'gaming', 'editing', 'filming'],
        'art'   => ['drawing', 'photography', 'calligraphy', 'design', 'sculpture', 'handcrafts'],
        'tech'  => ['programming', 'marketing', 'engineering', 'analysis', 'artificial_intelligence'],
    ];
    
    public function search_posts($query, $limit = 10, $offset = 0, $user_id = null) {
        $search = '%' . $query . '%';
        
        $sql = "SELECT p.*, u.username, u.full_name, u.avatar,
                (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as likes_count, 
                (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comments_count";
        
        if ($user_id) {
            $sql .= ", (SELECT COUNT(*) FROM likes WHERE post_id = p.id AND user_id = ?) as user_liked";
        }
        
        $sql .= " FROM posts p 
                JOIN users u ON p.user_id = u.id 
                WHERE p.is_active = 1 AND p.content LIKE ?";
        
        $params = $user_id ? [$user_id, $search] : [$search];
        
        
        $sql .= " ORDER BY p.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        $posts = $this->db->resultSet($sql, $params);
        
        
        return $posts;
    }
    
    public function get_posts_count($user_id = null, $profile_user_id = null, $filter = null) {
        $sql = "SELECT COUNT(*) as count FROM posts p WHERE p.is_active = 1";
        $params = [];
        
        // إذا كان هناك مستخدم محدد للملف الشخصي، عد منشوراته فقط
        if ($profile_user_id) {
            $sql .= " AND p.user_id = ?";
            $params[] = $profile_user_id;
        } 
        
        // تطبيق الفلتر
        if ($filter) {
            switch ($filter) {
                case 'images':
                    $sql .= " AND p.type = 'image'";
                    break;
                case 'videos':
                    $sql .= " AND p.type = 'video'";
                    break;
                case 'text':
                    $sql .= " AND p.type = 'text'";
                    break;
            }
        }
        
        $result = $this->db->single($sql, $params);
        
        return $result ? $result->count : 0;
    }

    public function getSimilarPosts($post_id, $user_id) {
        // استعلام لجلب المنشورات المشابهة
        $sql = "SELECT * FROM posts WHERE user_id = :user_id AND id != :post_id LIMIT 5";
        $this->db->query($sql);
        $this->db->bind(':user_id', $user_id, PDO::PARAM_INT);
        $this->db->bind(':post_id', $post_id, PDO::PARAM_INT);
        
        // تنفيذ الاستعلام وإرجاع النتائج
        return $this->db->resultSet();
    }
    
    
    // التحقق مما إذا كان المنشور ينتمي للمستخدم
    public function isUserPost($postId, $userId) {
        $this->db->query('SELECT * FROM posts WHERE id = :id AND user_id = :user_id');
        $this->db->bind(':id', $postId);
        $this->db->bind(':user_id', $userId);
        
        $this->db->execute();
        
        return $this->db->rowCount() > 0;
    }
    
    // البحث عن المنشورات
    public function searchPosts($keyword = null, $type = 'all') {
        $sql = '
            SELECT p.*, u.username, u.profile_picture, 
                   (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as likes_count,
                   (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comments_count
            FROM posts p
            JOIN users u ON p.user_id = u.id
            WHERE 1=1
        ';
    
        // بناء شروط البحث
        $params = [];
    
        if (!empty($keyword)) {
            $sql .= ' AND (p.title LIKE :keyword OR p.content LIKE :keyword)';
            $params[':keyword'] = '%' . $keyword . '%';
        }
        if (!empty($type) && $type !== 'all') {
            $sql .= ' AND p.media_type = :type';
            $params[':type'] = $type;
        }
    
        $sql .= ' ORDER BY p.created_at DESC';
    
        $this->db->query($sql);
    
        // ربط القيم
        foreach ($params as $key => $value) {
            $this->db->bind($key, $value);
        }
    
        return $this->db->resultSet();
    }
}
?>