<?php
namespace FormInModule\Services;

class FormService
{
    public $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function label($forFieldId, $content, $attributes = array())
    {
        return '<label for="' . $forFieldId . '"' . $this->parseAttributes($attributes) . '>' . 
            $content . '</label>';
    }

    
    public function textarea($key, $value = '', $attributes = array())
    {
        return '<textarea name="' .$key . '"' . 
            $this->parseAttributes($attributes) . '>' . 
            htmlspecialchars($value) . '</textarea>';
     }

    
    
    public function text($key, $value = '', $attributes = array()) {
        return $this->input('text', $key, $value, $attributes);
    }
    public function number($key, $value = '', $attributes = array()) {
        return $this->input('number', $key, $value, $attributes);
    }
    public function email($key, $value = '', $attributes = array()) {
        return $this->input('email', $key, $value, $attributes);
    }
    public function file($key, $attributes = array()) {
        return $this->input('file', $key, '' , $attributes);
    }
    public function hidden($key, $value = '', $attributes = array()) {
        return $this->input('hidden', $key, $value, $attributes);
    }
    public function password($key, $value = '', $attributes = array()) {
        return $this->input('password', $key, $value, $attributes);
    }

    public function input($type, $key, $value='', $attributes = array()){
        return '<input type="'.$type.'" name="'. $key .'"'.
            ' value="'.htmlspecialchars($value).'"'.
            $this->parseAttributes($attributes). '/>';
    }

    public function checkbox($key, $value, $attributes = array()) 
    {
        $checked = $value ? ' checked="checked"' : '';
        return '<input type="checkbox" value="1"' . $checked.
            ' name="' . $key.'"'. 
            $this->parseAttributes($attributes) . ' />';
    }

    public function select($key, $optionValues, $value = '', $attributes = array())
    {
        $str = '<select name="' . $key. '" ' . 
            $this->parseAttributes($attributes) . '>';
        foreach ($optionValues as $k => $text) {
            $str .= '<option';
            $str.= ' value="' . $k . '"';
            if( (string) $k === (string) $value ){
                 $str .= ' selected="selected"';
            }

            $str .= '>' . $text . '</option>';
        }
        $str .= '</select>';

        return $str;
    }
    
    public function radio($key, $optionValues, $value='', $attributes = array())
    {
        $str = '<div'. $this->parseAttributes($attributes). '>';
        foreach ($optionValues as $k => $text) {
            $str .= '<div class="radio">';
            $str .= '<label><input type="radio" name="'.
                $key.'" value="' . $k . '"';
            if( (string) $k === (string) $value ){
                 $str .= ' checked="checked"';
            }
            $str .= '/> ' . $text . '</label>';
            $str.= '</div>';
        }
        $str .= '</div>';

        return $str;
    }


    
    public function selectMultiple($key, $optionValues, $selectedValues=array(), $attributes = array())
    {
        $str = '<select name="'. $key.'[]" multiple="multiple" ' . 
             $this->parseAttributes($attributes) . '>';
        foreach ($optionValues as $k => $text) {
            $str .= '<option value="' . $k . '"';
            if (in_array($k, $selectedValues)) {
                $str .= ' selected="selected"';
            }
            $str .= '>' . $text . '</option>';
        }
        $str .= "</select>";

        return $str;
    }

    public function checkboxMultiple($key, $optionValues, $selectedValues=array(), $attributes = array())
    {
        $str = '<div'. $this->parseAttributes($attributes) . '>';
        foreach ($optionValues as $k => $text) {
            $str.= '<div class="checkbox">';
            $str .= '<label><input type="checkbox" name="'.
                $key.'[]" multiple="multiple" value="' . $k . '"';
            if (in_array($k, $selectedValues)) {
                $str .= ' checked="checked"';
            }
            $str .= '/> ' . $text . '</label>';
            $str.= '</div>';
        }
        $str .= "</div>";

        return $str;
    }



    
    /**
     * parse html attributes
     *
     */
    public function parseAttributes($attributes)
    {
        if (!is_array($attributes)) {
            return ' '.$attributes;
        }
        $attr = ' ';
        foreach ($attributes as $k => $v) {
            $attr .= " $k=\"$v\"";
        }

        return $attr. ' ';
    }
}

