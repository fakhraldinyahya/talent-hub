<?php
class Chat
{
    private $db;

    public function __construct($database)
    {
        $this->db = $database;
    }

    // الحصول على قائمة الدردشات الخاصة للمستخدم
    public function getPrivateChats($userId)
    {
        $this->db->query('
            SELECT 
                u.id, u.username, u.full_name, u.is_online, u.profile_picture,
                (SELECT message FROM private_messages 
                 WHERE (sender_id = :user_id AND receiver_id = u.id) OR (sender_id = u.id AND receiver_id = :user_id) 
                 ORDER BY created_at DESC LIMIT 1) as last_message,
                (SELECT created_at FROM private_messages 
                 WHERE (sender_id = :user_id AND receiver_id = u.id) OR (sender_id = u.id AND receiver_id = :user_id) 
                 ORDER BY created_at DESC LIMIT 1) as last_message_time,
                (SELECT COUNT(*) FROM private_messages 
                 WHERE sender_id = u.id AND receiver_id = :user_id AND is_read = 0) as unread_count
            FROM users u
            WHERE u.id <> :user_id AND EXISTS(
                SELECT 1 FROM private_messages 
                WHERE (sender_id = :user_id AND receiver_id = u.id) OR (sender_id = u.id AND receiver_id = :user_id)
            )
            ORDER BY last_message_time DESC
        ');

        $this->db->bind(':user_id', $userId);

        return $this->db->resultSet();
    }

    // الحصول على رسائل المحادثة الخاصة بين مستخدمين
    public function getPrivateMessages($senderId, $receiverId)
    {
        $this->db->query('
            SELECT pm.*, u.username, u.profile_picture
            FROM private_messages pm
            JOIN users u ON pm.sender_id = u.id
            WHERE (pm.sender_id = :sender_id AND pm.receiver_id = :receiver_id) OR 
                  (pm.sender_id = :receiver_id AND pm.receiver_id = :sender_id)
            ORDER BY pm.created_at ASC
        ');

        $this->db->bind(':sender_id', $senderId);
        $this->db->bind(':receiver_id', $receiverId);

        return $this->db->resultSet();
    }

    // تحديث حالة الرسائل إلى مقروءة
    public function markMessagesAsRead($senderId, $receiverId)
    {
        $this->db->query('
            UPDATE private_messages 
            SET is_read = 1 
            WHERE sender_id = :sender_id AND receiver_id = :receiver_id AND is_read = 0
        ');

        // ربط القيم
        $this->db->bind(':sender_id', $senderId);
        $this->db->bind(':receiver_id', $receiverId);

        // تنفيذ
        return $this->db->execute();
    }
    public function updateUserStatus($userId, $isOnline)
    {
        try {
            $sql = "UPDATE users SET is_online = :is_online WHERE id = :user_id";

            $this->db->query($sql);
            $this->db->bind(':is_online', $isOnline ? 1 : 0); // تحويل الحالة إلى 1 أو 0
            $this->db->bind(':user_id', $userId);
            $this->db->execute();

            return true; // نجاح التحديث
        } catch (PDOException $e) {
            error_log("Error updating user status: " . $e->getMessage());
            return false; // خطأ أثناء التحديث
        }
    }
    // الحصول على عدد الرسائل غير المقروءة
    public function getUnreadMessagesCount($userId)
    {
        $this->db->query('
            SELECT COUNT(*) as count 
            FROM private_messages 
            WHERE receiver_id = :user_id AND is_read = 0
        ');

        // ربط القيم
        $this->db->bind(':user_id', $userId);

        $row = $this->db->single();

        return $row->count;
    }

    // الحصول على المجموعات التي ينتمي إليها المستخدم
    public function getUserGroups($userId)
    {
        $this->db->query('
            SELECT g.*, gm.role as user_role,
                   (SELECT message FROM group_messages WHERE group_id = g.id ORDER BY created_at DESC LIMIT 1) as last_message,
                   (SELECT created_at FROM group_messages WHERE group_id = g.id ORDER BY created_at DESC LIMIT 1) as last_message_time,
                   (SELECT COUNT(*) FROM group_messages WHERE group_id = g.id AND created_at > IFNULL(
                        (SELECT last_read FROM group_members WHERE group_id = g.id AND user_id = :user_id), 
                        "1970-01-01 00:00:00"
                   )) as unread_count
            FROM groups g
            JOIN group_members gm ON g.id = gm.group_id
            WHERE gm.user_id = :user_id
            ORDER BY last_message_time DESC, g.created_at DESC
        ');

        $this->db->bind(':user_id', $userId);

        return $this->db->resultSet();
    }
}
