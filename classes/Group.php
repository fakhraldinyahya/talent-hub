<?php
class Group
{
    private $db;

    public function __construct($database)
    {
        $this->db = $database;
    }

    // إنشاء مجموعة جديدة
    public function createGroup($data)
    {
        $this->db->query('INSERT INTO groups (name, description, created_by, image) VALUES (:name, :description, :created_by , :group_image)');

        // ربط القيم
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':created_by', $data['created_by']);
        $this->db->bind(':group_image', $data['image']);

        // تنفيذ
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }

    // الحصول على جميع المجموعات
    public function getAllGroups()
    {
        $this->db->query('
            SELECT g.*, u.username, u.full_name, u.profile_picture, 
                   (SELECT COUNT(*) FROM group_members WHERE group_id = g.id) as members_count
            FROM groups g
            JOIN users u ON g.created_by = u.id
            ORDER BY g.created_at DESC
        ');

        return $this->db->resultSet();
    }

    // الحصول على المجموعات التي ينتمي إليها المستخدم
    public function getUserGroups($userId)
    {
        $this->db->query('
            SELECT g.*, u.username, u.full_name, u.profile_picture, 
                   (SELECT COUNT(*) FROM group_members WHERE group_id = g.id) as members_count,
                   gm.role as user_role,
                   (SELECT message FROM group_messages WHERE group_id = g.id ORDER BY created_at DESC LIMIT 1) as last_message,
                   (SELECT created_at FROM group_messages WHERE group_id = g.id ORDER BY created_at DESC LIMIT 1) as last_message_time,
                   (SELECT COUNT(*) FROM group_messages WHERE group_id = g.id AND created_at > IFNULL(
                        (SELECT last_read FROM group_members WHERE group_id = g.id AND user_id = :user_id), 
                        "1970-01-01 00:00:00"
                   )) as unread_count
            FROM groups g
            JOIN group_members gm ON g.id = gm.group_id
            JOIN users u ON g.created_by = u.id
            WHERE gm.user_id = :user_id
            ORDER BY last_message_time DESC, g.created_at DESC
        ');

        $this->db->bind(':user_id', $userId);

        return $this->db->resultSet();
    }

    // الحصول على مجموعة بواسطة المعرف
    public function getGroupById($groupId)
    {
        $this->db->query('
            SELECT g.*, u.username, u.full_name, u.profile_picture
            FROM groups g
            JOIN users u ON g.created_by = u.id
            WHERE g.id = :id
        ');

        $this->db->bind(':id', $groupId);

        return $this->db->single();
    }

    // تحديث مجموعة
    public function updateGroup($data)
    {
        $this->db->query('UPDATE groups SET name = :name, description = :description ,image = :image WHERE id = :id');

        // ربط القيم
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':image', $data['image']);

        // تنفيذ
        return $this->db->execute();
    }

    // حذف مجموعة
    public function deleteGroup($groupId)
    {
        // حذف جميع رسائل المجموعة
        $this->db->query('DELETE FROM group_messages WHERE group_id = :group_id');
        $this->db->bind(':group_id', $groupId);
        $this->db->execute();

        // حذف جميع أعضاء المجموعة
        $this->db->query('DELETE FROM group_members WHERE group_id = :group_id');
        $this->db->bind(':group_id', $groupId);
        $this->db->execute();

        // حذف المجموعة نفسها
        $this->db->query('DELETE FROM groups WHERE id = :id');
        $this->db->bind(':id', $groupId);

        return $this->db->execute();
    }

    // إضافة عضو إلى المجموعة
    public function addMember($groupId, $userId, $role = 'member')
    {
        // التحقق مما إذا كان المستخدم بالفعل عضوًا في المجموعة
        if ($this->isMember($groupId, $userId)) {
            return true;
        }

        $this->db->query('INSERT INTO group_members (group_id, user_id, role) VALUES (:group_id, :user_id, :role)');

        // ربط القيم
        $this->db->bind(':group_id', $groupId);
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':role', $role);

        // تنفيذ
        return $this->db->execute();
    }

    // إزالة عضو من المجموعة
    public function removeMember($groupId, $userId)
    {
        $this->db->query('DELETE FROM group_members WHERE group_id = :group_id AND user_id = :user_id');

        // ربط القيم
        $this->db->bind(':group_id', $groupId);
        $this->db->bind(':user_id', $userId);

        // تنفيذ
        return $this->db->execute();
    }

    // تغيير دور العضو في المجموعة
    public function changeMemberRole($groupId, $userId, $role)
    {
        $this->db->query('UPDATE group_members SET role = :role WHERE group_id = :group_id AND user_id = :user_id');

        // ربط القيم
        $this->db->bind(':group_id', $groupId);
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':role', $role);

        // تنفيذ
        return $this->db->execute();
    }

    // الحصول على أعضاء المجموعة
    public function getGroupMembers($groupId)
    {
        $this->db->query('
            SELECT u.*, gm.role, gm.joined_at
            FROM group_members gm
            JOIN users u ON gm.user_id = u.id
            WHERE gm.group_id = :group_id
            ORDER BY gm.role = "admin" DESC, gm.joined_at ASC
        ');

        $this->db->bind(':group_id', $groupId);

        return $this->db->resultSet();
    }

    // التحقق مما إذا كان المستخدم عضوًا في المجموعة
    public function isMember($groupId, $userId)
    {
        $this->db->query('SELECT * FROM group_members WHERE group_id = :group_id AND user_id = :user_id');

        // ربط القيم
        $this->db->bind(':group_id', $groupId);
        $this->db->bind(':user_id', $userId);

        $this->db->execute();

        return $this->db->rowCount() > 0;
    }

    // التحقق مما إذا كان المستخدم مشرفًا في المجموعة
    public function isGroupAdmin($groupId, $userId)
    {
        $this->db->query('
            SELECT * FROM group_members 
            WHERE group_id = :group_id AND user_id = :user_id AND role = "admin"
        ');

        // ربط القيم
        $this->db->bind(':group_id', $groupId);
        $this->db->bind(':user_id', $userId);

        $this->db->execute();

        return $this->db->rowCount() > 0;
    }

    // البحث عن المجموعات
    public function searchGroups($keyword)
    {
        $this->db->query('
            SELECT g.*, u.username, u.full_name, u.profile_picture, 
                   (SELECT COUNT(*) FROM group_members WHERE group_id = g.id) as members_count
            FROM groups g
            JOIN users u ON g.created_by = u.id
            WHERE g.name LIKE :keyword OR g.description LIKE :keyword
            ORDER BY g.created_at DESC
        ');

        $this->db->bind(':keyword', '%' . $keyword . '%');

        return $this->db->resultSet();
    }


    public function getOtherGroups($userId)
    {
        $this->db->query('
            SELECT g.*, u.username, u.full_name, u.profile_picture, 
                   (SELECT COUNT(*) FROM group_members WHERE group_id = g.id) as members_count
            FROM groups g
            JOIN users u ON g.created_by = u.id
            WHERE g.id NOT IN (
                SELECT group_id FROM group_members WHERE user_id = :user_id
            )
            ORDER BY g.created_at DESC
        ');

        $this->db->bind(':user_id', $userId);

        return $this->db->resultSet();
    }
    // الحصول على رسائل المجموعة
    public function getGroupMessages($groupId)
    {
        $this->db->query('
            SELECT gm.*, u.username, u.profile_picture
            FROM group_messages gm
            JOIN users u ON gm.user_id = u.id
            WHERE gm.group_id = :group_id
            ORDER BY gm.created_at ASC
        ');

        $this->db->bind(':group_id', $groupId);

        return $this->db->resultSet();
    }
    // تحديث حالة رسائل المجموعة إلى مقروءة
    public function markGroupMessagesAsRead($groupId, $userId)
    {
        $this->db->query('
            UPDATE group_members 
            SET last_read = NOW() 
            WHERE group_id = :group_id AND user_id = :user_id
        ');

        // ربط القيم
        $this->db->bind(':group_id', $groupId);
        $this->db->bind(':user_id', $userId);

        // تنفيذ
        return $this->db->execute();
    }

    public function get_groups($limit = 10, $offset = 0, $user_id = null, $search = null)
    {
        $sql = "SELECT g.*, u.username as creator_username";

        if ($user_id) {
            $sql .= ", (SELECT role FROM group_members WHERE group_id = g.id AND user_id = ?) as user_role, 
                      (SELECT status FROM group_members WHERE group_id = g.id AND user_id = ?) as user_status";
        }

        $sql .= " FROM groups g
                JOIN users u ON g.creator_id = u.id
                WHERE g.is_active = 1";

        $params = $user_id ? [$user_id, $user_id] : [];

        if ($search) {
            $sql .= " AND (g.name LIKE ? OR g.description LIKE ?)";
            $search = '%' . $search . '%';
            $params[] = $search;
            $params[] = $search;
        }

        $sql .= " ORDER BY g.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        return $this->db->resultSet($sql, $params);
    }
    public function get_groups_count($search = null)
    {
        $sql = "SELECT COUNT(*) as count FROM groups WHERE is_active = 1";
        $params = [];

        if ($search) {
            $sql .= " AND (name LIKE ? OR description LIKE ?)";
            $search = '%' . $search . '%';
            $params = [$search, $search];
        }

        $result = $this->db->single($sql, $params);

        return $result ? $result->count : 0;
    }
}
