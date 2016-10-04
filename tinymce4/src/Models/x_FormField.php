<?php
namespace FormInModule\Models;

class FormField
{
    public $field_id;
    public $form_id;
    public $type = 'text';
    public $pos = 0;
    public $active = 1;
    public $required = 0;
    public $id = '';
    public $class = '';
    public $attributes = '';
    public $html_before = '';
    public $html_after  = '';
    public $label = '';
    public $description = '';
    public $defaultvalue = '';
    public $options = '';
    public $validation = '';

    public function setFormData($data, $container) {
        $fs = $container->get('FilterService');
        if (isset($data['type'])) {
            $this->type = $fs->filterString($data['type']);
        }
        if (isset($data['pos'])) {
            $this->pos = intval($data['pos']);
        }
        if (isset($data['active'])) {
            $this->active = 1;
        } else {
            $this->active = 0;
        }
        if (isset($data['required'])) {
            $this->required = 1;
        } else {
            $this->required = 0;
        }

        if (isset($data['id'])) {
            $this->id = $fs->filterString($data['id']);
        }
        if (isset($data['class'])) {
            $this->class = $fs->filterString($data['class']);
        }
        if (isset($data['attributes'])) {
            $this->attributes = $fs->filterString($data['attributes']);
        }
        if (isset($data['html_before'])) {
            $this->html_before = $fs->filterText($data['html_before']);
        }
        if (isset($data['html_after'])) {
            $this->html_after = $fs->filterText($data['html_after']);
        }

        if (isset($data['label'])) {
            $this->label = $fs->filterString($data['label']);
        }
        if (isset($data['description'])) {
            $this->description = $fs->filterText($data['description']);
        }
        if (isset($data['defaultvalue'])) {
            $this->defaultvalue = $fs->filterString($data['defaultvalue']);
        }
        if (isset($data['options'])) {
            $this->options = $fs->filterString($data['options']);
        }
        if (isset($data['validation'])) {
            $this->validation = $fs->filterString($data['validation']);
        }
    }

    public function validate($container) 
    {
        $errors = array();
        return $errors;
    }

    public function getValidation() {
        return $this->validation;
    }

    public function getHtmlBefore() {
         if (0 === strpos($this->html_before, 'callback:')) {
             return call_user_func_array(substr($this->html_before, 9), array($this,));
        } else {
            return $this->html_before;
        }
    }
    public function getHtmlAfter() {
         if (0 === strpos($this->html_after, 'callback:')) {
            return call_user_func_array(substr($this->html_after, 9), array($this));
        } else {
            return $this->html_after;
        }
    }
    public function getOptions() {
        $options = $this->options;
        if (0 === strpos($options, 'json:')) {
            return json_decode(substr($options, 5), true);
        } elseif (0 === strpos($options, 'callback:')) {
            return call_user_func_array(substr($options, 9), array($this));
        } else {
            return explode(',', $options);
        }
    }
    public function getDefaultvalue() {
        $defaultvalue = $this->defaultvalue;
        if (0 === strpos($defaultvalue, 'json:')) {
            $value = json_decode(substr($defaultvalue, 5), false);
        } elseif (0 === strpos($defaultvalue, 'callback:')) {
            $value = call_user_func_array(substr($defaultvalue, 9), array($this));
        } else if (in_array($this->type, ['selectMultiple', 'checkboxMultiple'])){
            $value = explode(',', $defaultvalue);
        } else {
            $value = $defaultvalue;
        }
        if (in_array($this->getType(), ['select', 'radio'])) {
            $options = $this->getOptions();
            $keys = array_keys($options);
            if (isset($options[$value])) {
                return $value;
            } else {
                $key = array_search($value, $options);
                if (false !== $key) {
                    return $key;
                } else {
                    return null;
                }
            }
        } elseif (in_array($this->getType(), ['selectMultiple', 'checkboxMultiple'])) {
            $options = $this->getOptions();
            $keys = array_keys($options);
            $aSelected = array();
            foreach ($value as $val) {
                if (isset($options[$val])) {
                    $aSelected[] = $val;
                } else {
                    $key = array_search($val, $options);
                    if (false !== $key) {
                        $aSelected[] = $key;
                    } 
                }
            }
            return $aSelected;
        } else {
            return $value;
        }
    }

    public function getLabel() {
        return $this->label;
    }
    public function getClass() {
        return $this->class;
    }
    public function getType() {
        return $this->type;
    }
    public function getId() {
        return '' == $this->id ? $this->getName() : $this->id;
    }
    public function getAttributes() {
        return $this->attributes;
    }

    public function getName() {
        return 'fid'.$this->field_id;
    }

}
