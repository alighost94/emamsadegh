<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
//ini_set('log_errors', 1);
//ini_set('error_log', 'error_log.txt');
session_start();
// تعریف ثابت BASE_URL
define('BASE_URL', 'https://nazmeno.ir/emamsadegh/');
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