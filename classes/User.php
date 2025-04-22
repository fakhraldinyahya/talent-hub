<?php
class User
{
    private $db;

    public function __construct($database)
    {
        $this->db = $database;
    }

    // تسجيل مستخدم جديد
    public function register($data)
    {
        $this->db->query('INSERT INTO users (username, email, password, full_name, profile_picture, role, category) 
                          VALUES (:username, :email, :password, :full_name, :profile_picture, :role, :category)');

        $this->db->bind(':username', $data['username']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', $data['password']);
        $this->db->bind(':full_name', $data['full_name']);
        $this->db->bind(':profile_picture', $data['profile_picture']);
        $this->db->bind(':role', $data['role']);
        $this->db->bind(':category', $data['category']); // ربط الحقل الجديد category

        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }

    // تسجيل الدخول
    public function login($email, $password)
    {
        $this->db->query('SELECT * FROM users WHERE email = :email');
        $this->db->bind(':email', $email);

        $row = $this->db->single();

        if (!$row) {
            return false;
        }

        $hashed_password = $row->password;

        if (password_verify($password, $hashed_password)) {
            return $row;
        } else {
            return false;
        }
    }

    // البحث عن مستخدم باستخدام البريد الإلكتروني
    public function findUserByEmail($email)
    {
        $this->db->query('SELECT * FROM users WHERE email = :email');
        $this->db->bind(':email', $email);

        $row = $this->db->single();

        // التحقق إذا تم العثور على المستخدم
        return $row ? true : false;
    }
    public function isAdmin($userId)
    {
        $this->db->query('SELECT role FROM users WHERE id = :id');
        $this->db->bind(':id', $userId);

        $row = $this->db->single();

        return $row && $row->role === 'admin';
    }
    // البحث عن مستخدم باستخدام اسم المستخدم
    public function findUserByUsername($username)
    {
        $this->db->query('SELECT * FROM users WHERE username = :username');
        $this->db->bind(':username', $username);

        $row = $this->db->single();

        // التحقق إذا تم العثور على المستخدم
        return $row ? true : false;
    }
    public function findUserByUsername1($username)
    {
        $this->db->query('SELECT * FROM users WHERE username = :username');
        $this->db->bind(':username', $username);

        $row = $this->db->single();

        // التحقق إذا تم العثور على المستخدم
        return $this->db->single();
    }

    // الحصول على معلومات المستخدم حسب المعرف
    public function getUserById($id)
    {
        $this->db->query('SELECT * FROM users WHERE id = :id');
        $this->db->bind(':id', $id);

        return $this->db->single();
    }

    // تحديث معلومات المستخدم
    public function updateUser($data)
    {
        // بناء استعلام التحديث ديناميكيًا
        $query = 'UPDATE users SET ';
        $updates = [];
        $binds = [];

        // تحديد الحقول المراد تحديثها
        if (isset($data['username'])) {
            $updates[] = 'username = :username';
            $binds[':username'] = $data['username'];
        }

        if (isset($data['email'])) {
            $updates[] = 'email = :email';
            $binds[':email'] = $data['email'];
        }

        if (isset($data['full_name'])) {
            $updates[] = 'full_name = :full_name';
            $binds[':full_name'] = $data['full_name'];
        }

        if (isset($data['bio'])) {
            $updates[] = 'bio = :bio';
            $binds[':bio'] = $data['bio'];
        }

        if (isset($data['profile_picture'])) {
            $updates[] = 'profile_picture = :profile_picture';
            $binds[':profile_picture'] = $data['profile_picture'];
        }

        if (isset($data['password'])) {
            $updates[] = 'password = :password';
            $binds[':password'] = $data['password'];
        }

        if (isset($data['role'])) {
            $updates[] = 'role = :role';
            $binds[':role'] = $data['role'];
        }

        // إذا لم تكن هناك تحديثات، ارجع true
        if (empty($updates)) {
            return true;
        }

        // إنهاء الاستعلام
        $query .= implode(', ', $updates) . ' WHERE id = :id';
        $binds[':id'] = $data['id'];

        $this->db->query($query);

        // ربط جميع القيم
        foreach ($binds as $key => $value) {
            $this->db->bind($key, $value);
        }

        // تنفيذ
        return $this->db->execute();
    }

    // الحصول على جميع المستخدمين
    public function getAllUsers()
    {
        $this->db->query('SELECT * FROM users ORDER BY created_at DESC');
        return $this->db->resultSet();
    }

    // حذف مستخدم
    public function deleteUser($id)
    {
        $this->db->query('DELETE FROM users WHERE id = :id');
        $this->db->bind(':id', $id);

        return $this->db->execute();
    }

    // البحث عن المستخدمين
    public function searchUsers($keyword)
    {
        $this->db->query('SELECT * FROM users WHERE username LIKE :keyword OR full_name LIKE :keyword OR email LIKE :keyword');
        $this->db->bind(':keyword', '%' . $keyword . '%');

        return $this->db->resultSet();
    }
    public function getTotalUsers()
    {
        $this->db->query('SELECT COUNT(*) as count FROM users');
        return $this->db->single()->count;
    }
    public function getAvailableUsers($current_user_id)
    {
        $this->db->query('SELECT id, username, email, full_name, profile_picture, bio 
                      FROM users 
                      WHERE id != :current_user_id 
                      ORDER BY full_name ASC');

        $this->db->bind(':current_user_id', $current_user_id);

        $availableUsers = $this->db->resultSet();


        return $availableUsers;
    }
    public function getTotalLikes($user_id)
    {
        $this->db->query("SELECT COUNT(*) as total_likes FROM likes WHERE post_id IN (SELECT id FROM posts WHERE user_id = :user_id)");
        $this->db->bind(':user_id', $user_id);
        $result = $this->db->single();
        return $result ? $result->total_likes : 0;
    }
    public function search_users($query, $limit = 10, $offset = 0)
    {
        $search = '%' . $query . '%';

        $sql = "SELECT u.id, u.username, u.full_name, u.profile_picture, u.bio, u.is_active
                FROM users u
                WHERE (u.username LIKE ? OR u.full_name LIKE ?) AND u.is_active = 1
                ORDER BY
                    CASE
                        WHEN u.username = ? THEN 1
                        WHEN u.username LIKE ? THEN 2
                        WHEN u.full_name = ? THEN 3
                        WHEN u.full_name LIKE ? THEN 4
                        ELSE 5
                    END
                LIMIT ? OFFSET ?";

        $this->db->query($sql);
        $this->db->bind(1, $search);
        $this->db->bind(2, $search);
        $this->db->bind(3, $query);
        $this->db->bind(4, $query . '%');
        $this->db->bind(5, $query);
        $this->db->bind(6, $query . '%');
        $this->db->bind(7, $limit);
        $this->db->bind(8, $offset);

        // استرجاع النتائج مباشرة
        return $this->db->resultSet();
    }
    public function get_users_count($search = null)
    {
        $sql = "SELECT COUNT(*) as count FROM users";
        $params = [];

        if ($search) {
            $sql .= " WHERE username LIKE ? OR full_name LIKE ? OR email LIKE ?";
            $search = '%' . $search . '%';
            $params = [$search, $search, $search];
        }

        $this->db->query($sql);

        foreach ($params as $index => $param) {
            $this->db->bind($index + 1, $param); // استخدام الأرقام للمكان النيباري
        }

        $result = $this->db->single();

        return $result && isset($result->count) ? $result->count : 0;
    }


    /**
     * إنشاء رمز استعادة كلمة المرور
     */
    public function createPasswordResetToken($email)
    {
        try {
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

            error_log("Creating token for email: " . $email);
            error_log("Token will expire at: " . $expiry);

            $query = "UPDATE users SET 
                     reset_token = :token,
                     token_expiry = :expiry
                     WHERE email = :email";

            $this->db->query($query);
            $this->db->bind(':token', $token);
            $this->db->bind(':expiry', $expiry);
            $this->db->bind(':email', $email);

            if ($this->db->execute()) {
                error_log("Token created successfully for email: " . $email);
                return $token;
            }

            error_log("Failed to create token for email: " . $email);
            return false;
        } catch (PDOException $e) {
            error_log("Error creating token: " . $e->getMessage());
            return false;
        }
    }

    public function verifyPasswordResetToken($token)
    {
        try {
            error_log("Verifying token in DB: " . $token);

            $query = "SELECT id, email FROM users 
                     WHERE reset_token = :token 
                     AND token_expiry >= NOW() 
                     LIMIT 1";

            $this->db->query($query);
            $this->db->bind(':token', $token);

            $result = $this->db->single();

            if (!$result) {
                error_log("Token not found or expired: " . $token);
                // تسجيل محتويات قاعدة البيانات للمساعدة في التشخيص
                $this->db->query("SELECT email, reset_token, token_expiry FROM users WHERE reset_token IS NOT NULL");
                $tokens = $this->db->resultSet();
                error_log("Current tokens in DB: " . print_r($tokens, true));
            }

            return $result;
        } catch (PDOException $e) {
            error_log("Database error in verifyPasswordResetToken: " . $e->getMessage());
            return false;
        }
    }
    public function updatePassword($user_id, $hashed_password)
    {
        try {
            $query = "UPDATE users SET 
                     password = :password,
                     reset_token = NULL,
                     token_expiry = NULL
                     WHERE id = :id";

            $this->db->query($query);
            $this->db->bind(':password', $hashed_password);
            $this->db->bind(':id', $user_id);

            return $this->db->execute();
        } catch (PDOException $e) {
            error_log("Database error in updatePassword: " . $e->getMessage());
            return false;
        }
    }
    public function clearResetToken($user_id)
    {
        $query = "UPDATE users SET reset_token = NULL, token_expiry = NULL WHERE id = :id";
        $this->db->query($query);
        $this->db->bind(':id', $user_id);

        return $this->db->execute();
    }
}
