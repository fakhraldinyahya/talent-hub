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
        try {
            $data = json_decode($msg, true);
            
            switch ($data['type'] ?? '') {
                case 'register':
                    $this->handleRegistration($from, $data);
                    break;
                    
                case 'private':
                    $this->handlePrivateMessage($from, $data);
                    break;
                    
                    
                case 'typing':
                    $this->handleTypingIndicator($from, $data);
                    break;
                case 'makRead':
                    
                    $this->chat->markMessagesAsRead( $data['receiver'],$data['sender']);
                    break;
                case 'group':
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
                                        'time' => formatTimeArabic((new DateTime('now'))->format('Y-m-d H:i:s')),
                                    ]));
                                }
                            }
                        }
                    }
                    
                    break;
            }
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
    }


    protected function handleRegistration($conn, $data) {
        $userId = $data['userId'];
        $this->users[$conn->resourceId] = $userId;
        
        // تحديث حالة المستخدم إلى متصل
        $this->chat->updateUserStatus($userId, true);
        
        $conn->send(json_encode([
            'type' => 'status',
            'status' => 'connected'
        ]));
        
        $this->broadcastOnlineUsers();
    }

    protected function handlePrivateMessage($from, $data) {
        $senderId = $this->users[$from->resourceId];
        $receiverId = $data['receiver'];

        // إنشاء الرسالة في قاعدة البيانات
        $messageId = $this->chat->createMessage(
            $senderId,
            $receiverId,
            $data['message'],
            $data['mediaType'] ?? 'text',
            $data['mediaUrl'] ?? null
        );

        if (!$messageId) {
            throw new \Exception("Failed to save message");
        }
        
        // الحصول على معلومات المرسل
        $this->db->query('SELECT username, profile_picture FROM users WHERE id = :id');
        $this->db->bind(':id', $senderId);
        $senderInfo = $this->db->single();
        
        // إعداد بيانات الرسالة
        $messageData = [
            'type' => 'private',
            'messageId' => $messageId,
            'senderId' => $senderId,
            'senderName' => $senderInfo->username,
            'senderPicture' => $senderInfo->profile_picture,
            'message' => $data['message'],
            'mediaType' => $data['mediaType'] ?? 'text',
            'mediaUrl' => $data['mediaUrl'] ?? null,
            'time' => formatTimeArabic((new DateTime('now'))->format('Y-m-d H:i:s')),
        ];
        
        // إرسال الرسالة للمستلم إذا كان متصلاً
        $this->sendToReceiver($receiverId, $messageData);
        
        // تحديث قائمة المحادثات للطرفين
        $this->updateConversationLists($senderId, $receiverId, $data['message']);
        
        // إرسال تأكيد للمرسل
        $from->send(json_encode([
            'type' => 'message_sent',
            'messageId' => $messageId,
            'time' => $messageData['time']
        ]));
    }

    protected function sendToReceiver($receiverId, $messageData) {
        $receiverConnId = array_search($receiverId, $this->users);
        
        if ($receiverConnId !== false) {
            foreach ($this->clients as $client) {
                if ($client->resourceId == $receiverConnId) {
                    $client->send(json_encode($messageData));
                    // تحديث حالة الرسالة كمقروءة إذا وصلت

                    // $this->chat->markMessagesAsRead($messageData['senderId'], $receiverId);
                    break;
                }
            }
        }
    }

    protected function updateConversationLists($senderId, $receiverId, $lastMessage) {
        // تحديث قائمة المرسل
        $this->sendConversationUpdate($senderId, $receiverId, $lastMessage);
        
        // تحديث قائمة المستقبل
        $this->sendConversationUpdate($receiverId, $senderId, $lastMessage);
    }

    protected function sendConversationUpdate($userId, $chatId, $lastMessage) {
        $unreadCount = $this->chat->getUnreadMessagesCount($userId);
        
        foreach ($this->clients as $client) {
            if (isset($this->users[$client->resourceId]) && $this->users[$client->resourceId] == $userId) {
                $client->send(json_encode([
                    'type' => 'conversation_update',
                    'chatId' => $chatId,
                    'lastMessage' => $lastMessage,
                    'time' => formatTimeArabic((new DateTime('now'))->format('Y-m-d H:i:s')),
                    'unreadCount' => $unreadCount
                ]));
                break;
            }
        }
    }

    

    protected function handleTypingIndicator($from, $data) {
        $senderId = $this->users[$from->resourceId];
        $receiverId = $data['receiver'];
        
        $typingData = [
            'type' => 'typing',
            'senderId' => $senderId,
            'isTyping' => $data['isTyping']
        ];
        
        $this->sendToReceiver($receiverId, $typingData);
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