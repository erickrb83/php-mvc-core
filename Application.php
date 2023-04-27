<?php
namespace app\core;

use app\core\db\{Database, DbModel};
use app\models\User;

class Application{
    public static $ROOT_DIR;
    public static Application $app;
    
    public Router $router;
    public Request $request;
    public Response $response;
    public Session $session;
    public Database $db;
    public View $view;
    public ?Controller $controller = null;
    public ?UserModel $user; // ? tells server it might be "null"
    public $userClass;
    public $layout = 'main';

    public function __construct($rootPath, $config){
        $this->userClass = $config['userClass'];
        self::$ROOT_DIR = $rootPath;
        self::$app = $this;
        
        $this->request = new Request();
        $this->response = new Response();
        $this->session = new Session();
        $this->router = new Router($this->request, $this->response);
        $this->view = new View();
        $this->db = new Database($config['db']);
        
        $primaryValue = $this->session->get('user');
        if($primaryValue){
            $primaryKey = $this->userClass::primaryKey();
            $this->user = User::findOne([$primaryKey => $primaryValue]);
            return true;
        } else {
            $this->user = null;
        }
    }

    public static function isGuest(){
       return !self::$app->user;
    }

    public function run(){
        try{
            echo $this->router->resolve();
        } catch(\Exception $e) {
            $this->response->setStatusCode($e->getCode());
            echo $this->view->renderView('404', [
                'exception' => $e
            ]);
        }
    }

    public function setController($controller){
        $this->controller = $controller;
    }

    public function getController(){
        return $this->controller;
    }

    public function login(UserModel $user){
        $this->user = $user;
        $primaryKey = $user->primaryKey();
        $primaryValue = $user->{$primaryKey};
        $this->session->set('user', $primaryValue);
        return true;
    }

    public function logout(){
        $this->user = null;
        $this->session->remove('user');
    }
}  