<?php
class App {
    protected $controller = 'Auth';
    protected $method = 'index';
    protected $params = [];

    public function __construct() {
        $url = $this->parseUrl();
        
        // کنترلر
        if (isset($url[0]) && file_exists('app/controllers/' . ucfirst($url[0]) . 'Controller.php')) {
            $this->controller = ucfirst($url[0]);
            unset($url[0]);
        }
        
        $controllerFile = 'app/controllers/' . $this->controller . 'Controller.php';
        
        if (file_exists($controllerFile)) {
            require_once $controllerFile;
            $controllerClass = $this->controller . 'Controller';
            $this->controller = new $controllerClass;
        } else {
            // اگر فایل کنترلر وجود نداشت، به صفحه لاگین هدایت شود
            require_once 'app/controllers/AuthController.php';
            $this->controller = new AuthController();
        }
        
        // متد
        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }
        
        // پارامترها
        $this->params = $url ? array_values($url) : [];
        
        call_user_func_array([$this->controller, $this->method], $this->params);
    }
    
    private function parseUrl() {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url_parts = explode('/', $url);
            
            // 🔥 اضافه کردن این بخش برای رفع مشکل routing
            if (count($url_parts) == 2 && $url_parts[0] == 'assistant' && $url_parts[1] == 'addStudent') {
                return $url_parts;
            }
            
            return $url_parts;
        }
        return [];
    }
}
?>