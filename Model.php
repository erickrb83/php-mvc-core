<?php 
namespace app\core;

abstract class Model {

    public const RULE_REQUIRED = 'required';
    public const RULE_EMAIL = 'email';
    public const RULE_MIN = 'min';
    public const RULE_MAX = 'max';
    public const RULE_MATCH = 'match';
    public const RULE_UNIQUE = 'unique';

    public $errors = [];

    public function loadData($data){
        foreach ($data as $key => $value){
            if(property_exists($this, $key)){
                $this->{$key} = $value;
            }
        }
    }

    abstract public function rules();

    public function labels(){
        return [];
    }

    public function getLabels($attribute){
        return $this->labels()[$attribute] ?? $attribute;
    }

    public function validate(){
        foreach($this->rules() as $attribute => $rules){
            $value = $this->{$attribute};
            foreach($rules as $rule){
                $ruleName = $rule;
                if(!is_string($ruleName)){
                    $ruleName = $rule[0];
                }
                if($ruleName === self::RULE_REQUIRED && !$value){
                    $this->addErrorForRule($attribute, self::RULE_REQUIRED);
                }
                if($ruleName === self::RULE_EMAIL && !filter_var($value, FILTER_VALIDATE_EMAIL)){
                    $this->addErrorForRule($attribute, self::RULE_EMAIL);
                }
                if($ruleName === self::RULE_MIN && strlen($value) < $rule['min']){
                    $this->addErrorForRule($attribute, self::RULE_MIN, $rule);
                }
                if($ruleName === self::RULE_MAX && strlen($value) > $rule['max']){
                    $this->addErrorForRule($attribute, self::RULE_MAX, $rule);
                }
                if($ruleName === self::RULE_MATCH && $value !== $this->{$rule['match']}){
                    $rule['match'] = $this->getLabels($rule['match']);
                    $this->addErrorForRule($attribute, self::RULE_MATCH, $rule);
                }
                if($ruleName === self::RULE_UNIQUE){
                    $className = $rule['class'];
                    $uniqueAttr = $rule['attribute'] ?? $attribute;
                    $tableName = $className::tableName();
                    $statement = Application::$app->db->prepare("SELECT * FROM $tableName WHERE $uniqueAttr = :attr");
                    $statement->bindValue(":attr", $value);
                    $statement->execute();
                    $record = $statement->fetchObject();
                    if($record){
                        $this->addErrorForRule($attribute, self::RULE_UNIQUE, ['field' => $this->getLabels($attribute)]);
                    }
                }
            }
        }
        return empty($this->errors);
    }

    private function addErrorForRule($attribute, $rule, $params = []){
        $message = $this->errorMessages()[$rule] ?? '';
        foreach($params as $key => $value) {
            $message = str_replace("{{$key}}", $value, $message); // Two "{{ }}" replaces 
        }
        $this->errors[$attribute][] = $message;
    }

    public function addError($attribute, $message){
        $this->errors[$attribute][] = $message;
    }

    public function errorMessages(){
        return [
            SELF::RULE_REQUIRED => 'This field is required',
            SELF::RULE_EMAIL => 'This field must be valid email',
            SELF::RULE_MIN => 'Min length is {min}',
            SELF::RULE_MAX => 'Max length is {max}',
            SELF::RULE_MATCH => 'This field must be the same as {match}',
            SELF::RULE_UNIQUE => 'Record with this {field} already exists',
        ];
    }

    public function hasError($attribute){
        return $this->errors[$attribute]  ?? false;
    }

    public function getFirstError($attribute){
        return $this->errors[$attribute][0] ?? false;
    }
}