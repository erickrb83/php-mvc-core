<?php 

namespace app\core\forms;
use app\core\Model;

class TextAreaField extends BaseField{

    public function renderInput(){
        return sprintf(
            '<textarea name="%s" class="form-control%s">%s</textarea>',
            $this->attribute,
            $this->model->hasError($this->attribute) ? 'is-invalid' : '',
            $this->model->{$this->attribute},
        );
    }
}