<?php
// فایل: app/core/BaleWebhookHandler.php
class BaleWebhookHandler {
    private $bale;
    private $db;
    
    public function __construct($db) {
        $this->bale = new BaleMessenger('360698616:jlQfKPAKUeOfzoD3foxlaYuIWXI_l-RT4mM');
        $this->db = $db;
    }
    
    public function handle() {
        try {
            $input = file_get_contents('php://input');
            file_put_contents('webhook_log.txt', $input . "\n\n", FILE_APPEND);
            
            $update = json_decode($input, true);
            
            if (isset($update['message'])) {
                $this->processMessage($update['message']);
            }
            
            http_response_code(200);
            echo 'OK';
            
        } catch (Exception $e) {
            error_log("Webhook Error: " . $e->getMessage());
            http_response_code(500);
        }
    }
    
    private function processMessage($message) {
        $chat_id = $message['chat']['id'];
        $first_name = $message['from']['first_name'];
        $last_name = $message['from']['last_name'] ?? '';
        $text = trim($message['text']);
        
        // لاگ برای دیباگ
        file_put_contents('message_log.txt', 
            "Chat ID: {$chat_id}, Text: {$text}\n", FILE_APPEND);
        
        // بررسی آیا کاربر قبلاً ثبت شده
        if ($this->isUserRegistered($chat_id)) {
            $this->sendWelcomeMessage($chat_id, $first_name);
            return;
        }
        
        // بررسی state فعلی کاربر
        $user_state = $this->getUserState($chat_id);
        
        if ($user_state) {
            // کاربر state دارد
            switch ($user_state['state']) {
                case 'waiting_mobile':
                    $this->handleMobileInput($chat_id, $first_name, $text);
                    break;
                    
                case 'waiting_verification':
                    $this->handleVerification($chat_id, $user_state['data']['request_id'], $text);
                    break;
            }
        } else {
            // کاربر جدید
            $this->handleNewUser($chat_id, $first_name);
        }
    }
    
    private function isUserRegistered($chat_id) {
        $query = "SELECT id FROM users WHERE bale_chat_id = ? AND role_id = 4";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$chat_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }
    
    private function getUserState($chat_id) {
        $query = "SELECT state, data FROM user_sessions 
                  WHERE chat_id = ? AND expires_at > NOW()";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$chat_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            $result['data'] = json_decode($result['data'], true);
            return $result;
        }
        
        return null;
    }
    
    private function setUserState($chat_id, $state, $data = null) {
        $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $data_json = $data ? json_encode($data) : null;
        
        $query = "INSERT INTO user_sessions (chat_id, state, data, expires_at) 
                  VALUES (?, ?, ?, ?) 
                  ON DUPLICATE KEY UPDATE 
                  state = VALUES(state), 
                  data = VALUES(data), 
                  expires_at = VALUES(expires_at),
                  last_activity = NOW()";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$chat_id, $state, $data_json, $expires_at]);
    }
    
    private function clearUserState($chat_id) {
        $query = "DELETE FROM user_sessions WHERE chat_id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$chat_id]);
    }
    
    private function handleNewUser($chat_id, $first_name) {
        $welcome_message = "👋 سلام {$first_name} عزیز!\n\n"
                         . "به سامانه پیام‌رسان هنرستان امام صادق (ع) خوش آمدید.\n\n"
                         . "📱 لطفاً شماره موبایل خود را که در سامانه هنرستان ثبت کرده‌اید ارسال کنید:\n"
                         . "مثال: 09123456789";
        
        $this->bale->sendMessage($chat_id, $welcome_message);
        
        // ذخیره state
        $this->setUserState($chat_id, 'waiting_mobile');
    }
    
    private function handleMobileInput($chat_id, $first_name, $mobile) {
        // اعتبارسنجی شماره موبایل
        $mobile = $this->normalizeMobile($mobile);
        
        if (!$this->isValidMobile($mobile)) {
            $this->bale->sendMessage($chat_id, 
                "❌ شماره موبایل معتبر نیست.\n"
                . "لطفاً شماره را به فرمت 09123456789 وارد کنید.\n"
                . "مثال: 09123456789"
            );
            return;
        }
        
        // پیدا کردن کاربر بر اساس موبایل
        $user = $this->findParentByMobile($mobile);
        
        if (!$user) {
            $this->bale->sendMessage($chat_id, 
                "❌ شماره موبایل در سامانه یافت نشد.\n\n"
                . "لطفاً از درستی شماره اطمینان حاصل کنید.\n"
                . "اگر شماره را تغییر داده‌اید، با مدیریت هنرستان تماس بگیرید.\n\n"
                . "📞 تلفن هنرستان: ۰۲۱-۱۲۳۴۵۶۷۸"
            );
            return;
        }
        
        // بررسی آیا این کاربر قبلاً ثبت‌نام کرده
        if ($user['bale_chat_id']) {
            $this->bale->sendMessage($chat_id, 
                "⚠️ این شماره موبایل قبلاً ثبت‌نام کرده است.\n\n"
                . "اگر پیام‌ها را دریافت نمی‌کنید، با مدیریت تماس بگیرید."
            );
            $this->clearUserState($chat_id);
            return;
        }
        
        // ایجاد کد تأیید
        $verification_code = sprintf("%06d", mt_rand(1, 999999));
        $expires_at = date('Y-m-d H:i:s', strtotime('+10 minutes'));
        
        // ذخیره درخواست
        $query = "INSERT INTO chat_id_requests (user_id, mobile, chat_id, verification_code, expires_at) 
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$user['id'], $mobile, $chat_id, $verification_code, $expires_at]);
        
        $request_id = $this->db->lastInsertId();
        
        // آپدیت state
        $this->setUserState($chat_id, 'waiting_verification', [
            'request_id' => $request_id,
            'user_id' => $user['id']
        ]);
        
        // ارسال کد تأیید
        $message = "📋 کد تأیید\n\n"
                 . "شماره موبایل: {$mobile}\n"
                 . "🔢 کد تأیید: <b>{$verification_code}</b>\n"
                 . "⏰ این کد تا ۱۰ دقیقه معتبر است.\n\n"
                 . "لطفاً این کد را برای ربات ارسال کنید.";
        
        $this->bale->sendMessage($chat_id, $message);
    }
    
    private function handleVerification($chat_id, $request_id, $code) {
        $query = "SELECT cr.*, u.first_name, u.last_name, 
                         s2.first_name as student_first_name, s2.last_name as student_last_name
                  FROM chat_id_requests cr
                  JOIN users u ON cr.user_id = u.id
                  JOIN parents p ON u.id = p.user_id
                  JOIN students s ON p.student_id = s.id
                  JOIN users s2 ON s.user_id = s2.id
                  WHERE cr.id = ? AND cr.status = 'pending' 
                  AND cr.expires_at > NOW()";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$request_id]);
        $request = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$request) {
            $this->bale->sendMessage($chat_id, "❌ درخواست نامعتبر یا منقضی شده است.");
            $this->clearUserState($chat_id);
            return;
        }
        
        if ($request['verification_code'] !== $code) {
            $this->bale->sendMessage($chat_id, "❌ کد تأیید نادرست است.");
            return;
        }
        
        // تأیید موفق
        // آپدیت chat_id کاربر
        $update_query = "UPDATE users SET bale_chat_id = ? WHERE id = ?";
        $update_stmt = $this->db->prepare($update_query);
        $update_stmt->execute([$chat_id, $request['user_id']]);
        
        // آپدیت وضعیت درخواست
        $update_request = "UPDATE chat_id_requests SET status = 'verified' WHERE id = ?";
        $update_req_stmt = $this->db->prepare($update_request);
        $update_req_stmt->execute([$request_id]);
        
        // پاک کردن state
        $this->clearUserState($chat_id);
        
        // ارسال پیام خوش‌آمدگویی
        $student_name = $request['student_first_name'] . ' ' . $request['student_last_name'];
        $welcome_message = "✅ ثبت‌نام با موفقیت تکمیل شد!\n\n"
                         . "👤 نام: {$request['first_name']} {$request['last_name']}\n"
                         . "👦 دانش‌آموز: {$student_name}\n\n"
                         . "📢 از این پس:\n"
                         . "• اطلاعیه‌های انضباطی فرزندتان را دریافت خواهید کرد\n"
                         . "• از آخرین وضعیت درسی مطلع خواهید شد\n"
                         . "• پیام‌های مهم هنرستان را دریافت می‌کنید\n\n"
                         . "🏫 هنرستان فنی و حرفه‌ای امام صادق (ع)";
        
        $this->bale->sendMessage($chat_id, $welcome_message);
        
        // لاگ فعالیت
        $this->logActivity($request['user_id'], $chat_id);
    }
    
    private function sendWelcomeMessage($chat_id, $first_name) {
        $message = "👋 سلام {$first_name} عزیز!\n\n"
                 . "شما قبلاً در سامانه پیام‌رسان هنرستان ثبت‌نام کرده‌اید.\n\n"
                 . "📢 از این پس:\n"
                 . "• اطلاعیه‌های انضباطی فرزندتان را دریافت خواهید کرد\n"
                 . "• از آخرین وضعیت درسی مطلع خواهید شد\n"
                 . "• پیام‌های مهم هنرستان را دریافت می‌کنید\n\n"
                 . "🏫 هنرستان فنی و حرفه‌ای امام صادق (ع)";
        
        $this->bale->sendMessage($chat_id, $message);
    }
    
    private function normalizeMobile($mobile) {
        // حذف فاصله و کاراکترهای غیرعددی
        $mobile = preg_replace('/[^0-9]/', '', $mobile);
        
        // اگر با 9 شروع شده، 0 اضافه کن
        if (preg_match('/^9[0-9]{9}$/', $mobile)) {
            $mobile = '0' . $mobile;
        }
        
        return $mobile;
    }
    
    private function isValidMobile($mobile) {
        return preg_match('/^09[0-9]{9}$/', $mobile);
    }
    
    private function findParentByMobile($mobile) {
        $query = "SELECT u.id, u.first_name, u.last_name, u.bale_chat_id 
                  FROM users u 
                  WHERE u.mobile = ? AND u.role_id = 4";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$mobile]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    private function logActivity($user_id, $chat_id) {
        // اگر جدول activity_logs دارید، از این استفاده کنید
        error_log("Parent registered: User ID {$user_id}, Chat ID {$chat_id}");
    }
}
?>