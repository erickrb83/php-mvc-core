<?php
namespace app\core;

use app\core\middlewares\BaseMiddleware;

class Controller{

    public $layout = 'main';
    public $action = '';
    /**
     * @var \app\core\middlewares\BaseMiddleware[]
     */
    protected $middlewares = [];

    public function setLayout($layout){
        $this->layout = $layout;
    }
    public function render($view, $params = []){
        return Application::$app->view->renderView($view, $params);
    }

    public function registerMiddleWare(BaseMiddleware $middleware){
        $this->middlewares[] = $middleware;
    }

    public function getMiddlewares(){
        return $this->middlewares;
    }
}