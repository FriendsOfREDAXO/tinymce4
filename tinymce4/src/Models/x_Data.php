<?php
namespace FormInModule\Models;

class Data
{
    public function setFormFile($data, $container, $form, $field_list) {
        $fs = $container->get('FilterService');
        foreach ($field_list as $field) {
            if ('file' != $field->type) continue;
            $fid = 'fid'.$field->field_id;
            if (isset($data['name'][$fid])) {
                $this->{$fid} = array(
                    'name' => $data['name'][$fid],
                    'type' => $data['type'][$fid],
                    'tmp_name' => $data['tmp_name'][$fid],
                    'error' => $data['error'][$fid],
                    'size' => $data['size'][$fid],
                );
            } else {
                $this->{$fid} = array(
                    'name' => '',
                    'type' => '',
                    'tmp_name' => '',
                    'error' => 0,
                    'size' => 0,
                );
            }
        }
    }
    public function setFormData($data, $container, $form, $field_list) {
        $fs = $container->get('FilterService');
        foreach ($field_list as $field) {
            $fid = 'fid'.$field->field_id;
            switch ($field->type):
            case 'submit':
            case 'html':
                continue;
                break;
            case 'checkbox':
                if (isset($data[$fid])){
                    $this->{$fid} = 1;
                } else {
                    $this->{$fid} = 0;
                }
                break;
            case 'text':
            case 'password':
            case 'email':
            case 'hidden':
            case 'select':
            case 'radio':
                $this->{$fid} = $fs->filterString($data[$fid]);
                break;
            case 'textarea':
                $this->{$fid} = $fs->filterText($data[$fid]);
                break;
            case 'selectMultiple':
            case 'checkboxMultiple':
                if (!isset($data[$fid]) || !is_array($data[$fid])){
                    $this->{$fid} = array();
                }else{
                    $this->{$fid} = $data[$fid];
                }
                break;
            default:
            // Nothing to do
            endswitch;
        }
    }
    public function validate($container, $form, $field_list) {
        $errors = array();
        foreach ($field_list as $field) {
            $fid = 'fid'.$field->field_id;
            $validation = $field->getValidation();
            $options = $field->getOptions();
            switch ($field->type):
            case 'submit':
            case 'html':
            case 'hidden':
                continue;
                break;
            case 'checkbox':
                if (1 == $field->required && 0 == $this->{$fid}) {
                    $errors[$fid] = 'Input required';
                }
                break;
            case 'text':
            case 'password':
            case 'textarea':
                if (1 == $field->required && '' == $this->{$fid}) {
                    $errors[$fid] = 'Input required';
                } else {
                    if (0 === strpos($validation, '/') && !preg_match($validation, $this->{$fid})) {
                        $errors[$fid] = 'Not valid';
                    } elseif (0 === strpos($validation, 'callback:')) {
                        $err = call_user_func(substr($validation, 9), array(
                            'value' => $this->{$fid},
                        ));
                        if ('' != $err) {
                            $errors[$fid] = $err;
                        }
                    }
                }
                break;
            case 'email':
                if (1 == $field->required && '' == $this->{$fid}) {
                    $errors[$fid] = 'Input required';
                } elseif ('' != $this->{$fid} && 0 === strpos($validation, '/') && !preg_match($validation, $this->{$fid})) {
                    $errors[$fid] = 'Not valid';
                } elseif ('' != $this->{$fid} && 0 === strpos($validation, 'callback:')) {
                    $err = call_user_func(substr($validation, 9), array(
                        'value' => $this->{$fid},
                    ));
                    if ('' != $err) {
                        $errors[$fid] = $err;
                    }
                } elseif ('' != $this->{$fid} && false === filter_var($this->{$fid}, FILTER_VALIDATE_EMAIL)) {
                    $errors[$fid] = 'Not valid';
                }
                break;
            case 'select':
            case 'radio':
                $keys = array_keys($options);
                if (1 == $field->required && $this->{$fid} != $keys[0]) {
                    $errors[$fid] = 'Input required';
                } elseif (0 === strpos($validation, 'callback:')) {
                    $err = call_user_func(substr($validation, 9), array(
                        'value' => $this->{$fid},
                    ));
                    if ('' != $err) {
                        $errors[$fid] = $err;
                    }
                } elseif (!in_array($this->{$fid}, $keys)) {
                    $errors[$fid] = 'Value out of range';
                }
                break;
            case 'selectMultiple':
            case 'checkboxMultiple':
                if (1 == $field->required && 0 == count($this->{$fid})) {
                    $errors[$fid] = 'Input required';
                } elseif (0 === strpos($validation, 'callback:')) {
                    $err = call_user_func(substr($validation, 9), array(
                        'value' => $this->{$fid},
                        ));
                    if ('' != $err) {
                        $errors[$fid] = $err;
                    }
                }
                break;
            case 'file':
                if (1 == $field->required && ( 
                    0 == count($this->{$fid}) || '' == $this->{$fid}['name']
                )) {
                    $errors[$fid] = 'Input required';
                } 
                break;

            default:
            // Nothing to do
            endswitch;
        }
        return $errors;
    }

    // Gibt die Daten mit dem Label zurück
    public function getFormData($container, $form, $field_list) {
        $Translator = $container->get('TranslatorService');
        $a =array();
        foreach ($field_list as $field) {
            $fid = 'fid'.$field->field_id;
            switch ($field->type):
            case 'submit':
            case 'html':
                continue;
                break;
            case 'checkbox':
                $a[] = array(
                    'field_id' => $field->field_id,
                    'label' => $field->getLabel(),
                    'value' => 1 == $this->{$fid} ? $Translator->trans('Yes') : $Translator->trans('No'),
                    'key' => '',
                );
                break;
            case 'hidden':
            case 'text':
            case 'password':
            case 'textarea':
            case 'email':
                $a[] = array(
                    'field_id' => $field->field_id,
                    'label' => $field->getLabel(),
                    'value' => $this->{$fid},
                    'key' => '',
                );
                break;
            case 'select':
            case 'radio':
                $options = $field->getOptions();
                $a[] = array(
                    'field_id' => $field->field_id,
                    'label' => $field->getLabel(),
                    'value' => $options[$this->{$fid}],
                    'key' => $this->{$fid},
                );
                break;
            case 'selectMultiple':
            case 'checkboxMultiple':
                $options = $field->getOptions();
                $value = '';
                $key = '';
                if (is_array($this->{$fid})) {
                    $sep = '';
                    foreach ($this->{$fid} as $k) {
                        $value.= $sep.$options[$k];
                        $key.= $sep.$k;
                        $sep = ', ';
                    }
                }
                $a[] = array(
                    'field_id' => $field->field_id,
                    'label' => $field->getLabel(),
                    'value' => $value,
                    'key' => $key,
                );
                break;
            case 'file':
                $a[] = array(
                    'field_id' => $field->field_id,
                    'label' => $field->getLabel(),
                    'value' => $this->{$fid}['name'],
                    'key' => '',
                );
            default:
            // Nothing to do
            endswitch;
        }

        return $a;
    }
    
}
