<?php
// فایل: set_webhook.php
require_once 'config/database.php';
require_once 'app/core/BaleMessenger.php';

$bale = new BaleMessenger('360698616:jlQfKPAKUeOfzoD3foxlaYuIWXI_l-RT4mM');

// آدرس واقعی وب‌هوک شما
$webhook_url = 'https://nazmeno.ir/emamsadegh/webhook.php';

$result = $bale->setWebhook($webhook_url);

if ($result['ok']) {
    echo "✅ Webhook با موفقیت تنظیم شد\n";
    echo "URL: {$webhook_url}\n";
} else {
    echo "❌ خطا در تنظیم Webhook\n";
    echo "Error: {$result['description']}\n";
}
?>