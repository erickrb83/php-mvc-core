<?php
namespace app\core;
use app\core\h;

class Request {
    public function getPath(){
        $path = $_SERVER['REQUEST_URI'] ?? '/'; // "??" shorthand for isset(). if whatever is not set before ?? it will assign the following value. 
        $position = strpos($path, '?');
        if($position === false){
            return $path;
        }
        return substr($path, 0, $position);
    }

    public function method(){
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    public function isGet(){
        return $this->method() === 'get';
    }

    public function isPost(){
        return $this->method() === 'post';
    }

    public function getBody(){
        $body = [];
        if($this->method() === 'get'){
            foreach($_GET as $key => $value){
                $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        if($this->method() === 'post'){
            foreach($_POST as $key => $value){
                $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        return $body;
    }
}