<?php


require_once '../app/core/Router.php';
require_once '../app/core/Controller.php';
require_once '../app/core/Model.php';

require_once '../app/models/User.php';

require_once '../config/database.php';

$router = new Router();

// تعریف مسیرها
$router->add('', ['controller' => 'AuthController', 'action' => 'login']);
$router->add('auth/login', ['controller' => 'AuthController', 'action' => 'login']);
$router->add('auth/logout', ['controller' => 'AuthController', 'action' => 'logout']);
$router->add('admin/dashboard', ['controller' => 'AdminController', 'action' => 'dashboard']);
$router->add('admin/majors', ['controller' => 'AdminController', 'action' => 'majors']);

$url = $_GET['url'] ?? '';
$router->dispatch($url);
?>