<?php
namespace Tinymce4\Services;

class ServiceContainer
{
    public $services = array();
    public $parameters = array();
    public $routes = array();
    protected static $_instance = null;
    public $namespace = '\Tinymce4';

    public function get($service){
        if (!isset($this->services[$service])) {
            $class = $this->namespace.'\Services\\'.$service;
            $this->services[$service] = new $class($this);
        }
        return $this->services[$service];
    }
    public static function getInstance(){
        if (null === self::$_instance) {
           self::$_instance = new self;
           $container = self::$_instance;
           include __DIR__. '/../../config/values.php';
           include __DIR__. '/../../config/routing.php';
       }
       return self::$_instance;
    }

    public function setParameter($key, $value){
        $this->parameters[$key] = $value;
    }

    public function getParameter($key, $default=null){
        return isset($this->parameters[$key]) ? $this->parameters[$key] : $default;
    }
    public function addRoute($route, $action){
        $this->routes[$route] = $action;
    }

    public function handleRoute($route){
        if(preg_match('/\/([a-z]{2})\//', $route, $matches)){
            $language = $matches[1];
            $route = substr($route, 3);
            $this->service('LanguageService')->setLanguage($language);
        }
        foreach($this->routes as $pattern => $handler){
            if(0 === strpos($route, $pattern)){
                list($class, $method) = explode(':', $handler);
                $arg = trim(str_replace($pattern, '', $route), '/');
                $controller = new $class($this);
                return $controller->{$method}($arg);
            }
        }
        return '';
    }

    protected function __clone() {}
    protected function __construct() {}
}

