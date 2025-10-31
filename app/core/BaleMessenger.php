<?php
class BaleMessenger {
    private $token;
    private $api_url = "https://tapi.bale.ai/bot";
    
    public function __construct($token) {
        $this->token = $token;
    }
    
    public function sendMessage($chat_id, $text) {
        $url = $this->api_url . $this->token . "/sendMessage";
        
        $data = [
            'chat_id' => $chat_id,
            'text' => $text,
            'parse_mode' => 'HTML'
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);
        
        if ($http_code == 200) {
            $result = json_decode($response, true);
            if ($result['ok']) {
                return [
                    'success' => true,
                    'message_id' => $result['result']['message_id'],
                    'response' => $result
                ];
            }
        }
        
        return [
            'success' => false,
            'error' => $curl_error ?: $response,
            'http_code' => $http_code
        ];
    }
    
    public function testConnection() {
        $url = $this->api_url . $this->token . "/getMe";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($response, true);
    }
	// در کلاس BaleMessenger اضافه کنید:
public function setWebhook($url) {
    $api_url = "https://tapi.bale.ai/bot" . $this->token . "/setWebhook";
    
    $data = ['url' => $url];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}
}
?>