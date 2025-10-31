<?php
class Router {
    protected $routes = [];

    public function add($route, $params = []) {
        $this->routes[$route] = $params;
    }

    public function dispatch($url) {
        $url = $this->removeQueryStringVariables($url);
        
        foreach ($this->routes as $route => $params) {
            // اگر route شامل پارامتر باشه
            if (preg_match('#^' . $route . '$#', $url, $matches)) {
                $controller = $this->getNamespace() . $params['controller'];
                if (class_exists($controller)) {
                    $controller_object = new $controller();
                    $action = $params['action'];
                    
                    if (is_callable([$controller_object, $action])) {
                        // پارامترهای اضافی (مثل id) رو از matches استخراج می‌کنیم
                        array_shift($matches); // اولین المان که کل URL هست رو حذف می‌کنیم
                        call_user_func_array([$controller_object, $action], $matches);
                    } else {
                        throw new \Exception("Method $action in controller $controller not found");
                    }
                } else {
                    throw new \Exception("Controller class $controller not found");
                }
                return;
            }
        }
        
        throw new \Exception('No route matched.', 404);
    }

    protected function removeQueryStringVariables($url) {
        if ($url != '') {
            $parts = explode('&', $url, 2);
            if (strpos($parts[0], '=') === false) {
                $url = $parts[0];
            } else {
                $url = '';
            }
        }
        return $url;
    }

    protected function getNamespace() {
        $namespace = 'App\Controllers\\';
        return $namespace;
    }
}
?>