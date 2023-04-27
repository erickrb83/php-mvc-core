<?php
namespace app\core\forms;

use app\core\Model;
use app\core\forms\InputField;

class Form {

    public static function begin($action, $method){
        echo sprintf('<form action="%s" method="%s">', $action, $method); // or ="{$action}" ="{$method}"
        return new Form();
    }

    public function field(Model $model, $attribute){
        return new InputField($model, $attribute);
    }

    public static function end(){
        return '</form>';
    }
}
