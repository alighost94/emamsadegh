<?php
// فایل: webhook.php در روت
require_once 'config/database.php';
require_once 'app/core/BaleMessenger.php';
require_once 'app/core/BaleWebhookHandler.php';

$database = new Database();
$db = $database->getConnection();

$handler = new BaleWebhookHandler($db);
$handler->handle();
?>