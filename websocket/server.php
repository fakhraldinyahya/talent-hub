<?php
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/db.php';
require_once dirname(__DIR__) . '/classes/Chat.php';
require_once dirname(__DIR__) . '/vendor/autoload.php';

class ChatServer implements MessageComponentInterface {
    protected $clients;
    protected $users = [];
    protected $db;
    protected $chat;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->db = new Database();
        $this->chat = new Chat($this->db);
        echo "Chat Server Started!\n";
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg, true);
        $type = isset($data['type']) ? $data['type'] : null;
        
        // تسجيل مستخدم جديد في الخادم
        if ($type === 'register') {
            $userId = $data['userId'];
            $this->users[$from->resourceId] = $userId;
            
            // إرسال رسالة حالة الاتصال للمستخدم
            $from->send(json_encode([
                'type' => 'status',
                'status' => 'connected',
                'userId' => $userId
            ]));
            
            // إعلام الجميع بالمستخدمين المتصلين
            $this->broadcastOnlineUsers();
        }
        // معالجة رسالة خاصة
        elseif ($type === 'private') {
            $senderId = $this->users[$from->resourceId];
            $receiverId = $data['receiver'];
            $message = $data['message'];
            $mediaType = isset($data['mediaType']) ? $data['mediaType'] : 'text';
            $mediaUrl = isset($data['mediaUrl']) ? $data['mediaUrl'] : '';
            
            // حفظ الرسالة في قاعدة البيانات
            $this->db->query('INSERT INTO private_messages (sender_id, receiver_id, message, media_type, media_url) VALUES (:sender_id, :receiver_id, :message, :media_type, :media_url)');
            $this->db->bind(':sender_id', $senderId);
            $this->db->bind(':receiver_id', $receiverId);
            $this->db->bind(':message', $message);
            $this->db->bind(':media_type', $mediaType);
            $this->db->bind(':media_url', $mediaUrl);
            $this->db->execute();
            $messageId = $this->db->lastInsertId();
            
            // استرجاع معلومات المرسل
            $this->db->query('SELECT username, profile_picture FROM users WHERE id = :id');
            $this->db->bind(':id', $senderId);
            $sender = $this->db->single();
            
            // إرسال الرسالة إلى المستلم إذا كان متصلاً
            if ($receiverConnId !== false) {
                foreach ($this->clients as $client) {
                    if ($client->resourceId == $receiverConnId) {
                        $client->send(json_encode([
                            'type' => 'private',
                            'messageId' => $messageId,
                            'senderId' => $senderId,
                            'senderName' => $sender->username,
                            'senderPicture' => $sender->profile_picture,
                            'message' => $message,
                            'mediaType' => $mediaType,
                            'mediaUrl' => $mediaUrl,
                            'time' => date('Y-m-d H:i:s')
                        ]));
                        
                        // تحديث حالة الرسالة إلى مقروءة
                        $this->db->query('UPDATE private_messages SET is_read = 1 WHERE id = :id');
                        $this->db->bind(':id', $messageId);
                        $this->db->execute();
                    }
                }
            }
            foreach ($this->clients as $client) {
                $client->send(json_encode([
                    'type' => 'unreadUpdate',
                    'chatId' => $receiverId,
                    'lastMessage' => $message,
                    'time' => date('Y-m-d H:i:s'),
                    'unreadCount' => $this->chat->getUnreadMessagesCount($receiverId)
                ]));
            }
            
            // إرسال تأكيد للمرسل
            $from->send(json_encode([
                'type' => 'confirm',
                'messageId' => $messageId,
                'receiver' => $receiverId,
                'time' => date('Y-m-d H:i:s')
            ]));
        }
        // معالجة رسالة مجموعة
        elseif ($type === 'group') {
            $senderId = $this->users[$from->resourceId];
            $groupId = $data['groupId'];
            $message = $data['message'];
            $mediaType = isset($data['mediaType']) ? $data['mediaType'] : 'text';
            $mediaUrl = isset($data['mediaUrl']) ? $data['mediaUrl'] : '';
            
            // التحقق من عضوية المستخدم في المجموعة
            $this->db->query('SELECT * FROM group_members WHERE group_id = :group_id AND user_id = :user_id');
            $this->db->bind(':group_id', $groupId);
            $this->db->bind(':user_id', $senderId);
            if (!$this->db->single()) {
                $from->send(json_encode([
                    'type' => 'error',
                    'message' => 'أنت لست عضوًا في هذه المجموعة'
                ]));
                return;
            }
            
            // حفظ الرسالة في قاعدة البيانات
            $this->db->query('INSERT INTO group_messages (group_id, user_id, message, media_type, media_url) VALUES (:group_id, :user_id, :message, :media_type, :media_url)');
            $this->db->bind(':group_id', $groupId);
            $this->db->bind(':user_id', $senderId);
            $this->db->bind(':message', $message);
            $this->db->bind(':media_type', $mediaType);
            $this->db->bind(':media_url', $mediaUrl);
            $this->db->execute();
            $messageId = $this->db->lastInsertId();
            
            // استرجاع معلومات المرسل
            $this->db->query('SELECT username, profile_picture FROM users WHERE id = :id');
            $this->db->bind(':id', $senderId);
            $sender = $this->db->single();
            
            // الحصول على أعضاء المجموعة
            $this->db->query('SELECT user_id FROM group_members WHERE group_id = :group_id');
            $this->db->bind(':group_id', $groupId);
            $members = $this->db->resultSet();
            
            // إرسال الرسالة إلى جميع أعضاء المجموعة المتصلين
            foreach ($members as $member) {
                $memberConnId = array_search($member->user_id, $this->users);
                if ($memberConnId !== false) {
                    foreach ($this->clients as $client) {
                        if ($client->resourceId == $memberConnId) {
                            $client->send(json_encode([
                                'type' => 'group',
                                'messageId' => $messageId,
                                'groupId' => $groupId,
                                'senderId' => $senderId,
                                'senderName' => $sender->username,
                                'senderPicture' => $sender->profile_picture,
                                'message' => $message,
                                'mediaType' => $mediaType,
                                'mediaUrl' => $mediaUrl,
                                'time' => date('Y-m-d H:i:s')
                            ]));
                        }
                    }
                }
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        
        // إزالة المستخدم من قائمة المتصلين
        if (isset($this->users[$conn->resourceId])) {
            unset($this->users[$conn->resourceId]);
            
            // إعلام الجميع بالمستخدمين المتصلين
            $this->broadcastOnlineUsers();
        }
        
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }
    
    // إرسال قائمة المستخدمين المتصلين
    protected function broadcastOnlineUsers() {
        // استرجاع معلومات المستخدمين المتصلين
        $onlineUsers = [];
        foreach ($this->users as $userId) {
            $this->db->query('SELECT id, username, profile_picture FROM users WHERE id = :id');
            $this->db->bind(':id', $userId);
            $user = $this->db->single();
            if ($user) {
                $onlineUsers[] = [
                    'id' => $user->id,
                    'username' => $user->username,
                    'profile_picture' => $user->profile_picture
                ];
            }
        }
        
        // إرسال القائمة إلى جميع العملاء
        foreach ($this->clients as $client) {
            $client->send(json_encode([
                'type' => 'onlineUsers',
                'users' => $onlineUsers
            ]));
        }
    }
}

// إنشاء وتشغيل الخادم
$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new ChatServer()
        )
    ),
    WS_PORT,
    WS_HOST
);

echo "WebSocket server started on " . WS_HOST . ":" . WS_PORT . "\n";
$server->run();