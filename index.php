<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
session_start();
// تعریف ثابت BASE_URL
define('BASE_URL', 'http://localhost/emamsadegh/');
require_once 'vendor/autoload.php';
if (ob_get_length()) {
    ob_end_clean();
}
ob_start();
// بارگذاری فایل‌های核心
require_once 'app/core/App.php';
require_once 'app/core/Controller.php';
require_once 'app/core/Model.php';

require_once 'app/models/TeacherProfile.php';
require_once 'app/models/AssistantProfile.php';
require_once 'app/models/StaffRecord.php';
require_once 'app/models/StaffScore.php';
require_once 'config/database.php';
require_once 'app/core/BaleMessenger.php';
require_once 'app/core/PDFHelper.php';

$app = new App();
?>