<?php 

namespace app\core\forms;
use app\core\Model;

abstract class BaseField{

    public Model $model; 
    public $attribute;

    /**
     * @param \app\core\Model $model
     * @param string $attribute
     */
    public function __construct(Model $model, $attribute){
        $this->model = $model;
        $this->attribute = $attribute;
    }

    abstract public function renderInput();

    public function __toString(){
        return sprintf(
            '<div class="mb-3">
                <label>%s</label>
                %s
                <div class="invalid-feedback">
                    %s
                </div>
            </div>', 
            $this->model->getLabels($this->attribute), 
            $this->renderInput(),
            $this->model->getFirstError($this->attribute)
        );
    }
}