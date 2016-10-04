<?php
namespace FormInModule\Services;

class Submission {
    public $container;
    public $form;
    public $model;
    public $field_list;
    public $data;

    public function __construct($container) {
        $this->container = $container;
    }
    public function getForm(){
        return $this->form;
    }
    public function getModel() {
        return $this->model;
    }
    public function getData() {
        if (null === $this->data) {
            $this->data = $this->model->getFormData($this->container, $this->getForm(), $this->getFieldList());
        }
        return $this->data;
    }

    public function getFieldList() {
        if (null === $this->field_list) {
            $this->field_list = $this->container->get('FormFieldRepository')
                ->findBy( array(
                    'form_id' => $this->form->form_id, 
                    'active' => 1,
                ), array('pos' => 'ASC'));
        }
        return $this->field_list;
    }

    public function saveData() {
        $dir = $this->container->getParameter('data_dir').'/submissions/f'.$this->form->form_id;
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $file = date('Ymd_His');
        $i = 1;
        while (file_exists($dir.'/'.$file.'_'.$i.'.txt')) {
            $i++;
        }
        $filename = $file.'_'.$i.'.txt';
        file_put_contents($dir.'/'.$filename, serialize($this->getData()));
    }
    
    public function sendDataEmail() {
        if ('' == trim($this->form->sendto)) {
            return '';
        }
        $body = '';
        foreach ($this->getData() as $field) {
            $body.= $field['label'].': '.$field['value']."\n";
        }
        $mail = new \rex_mailer();
        $mail->Body = '<p>'.nl2br($body).'</p>';
        $mail->AltBody = $body;
        $mail->addAddress($this->form->sendto);
        if ('' != $this->form->sendcc) {
            foreach(explode(',', $this->form->sendcc) as $cc) {
                $mail->addCC($cc);
            }
        }
        if ('' != $this->form->sendbcc) {
            foreach(explode(',', $this->form->sendbcc) as $bcc) {
                $mail->addBCC($bcc);
            }
        }
        // Attach files if there are
        foreach ($this->getFieldList() as $field) {
            if ('file' != $field->getType()) continue;
            $fid = 'fid'.$field->field_id;
            if ('' == $this->model->{$fid}['name']) continue;
            $mail->AddAttachment($this->model->{$fid}['tmp_name'],
                $this->model->{$fid}['name']);
        }
        $mail->Subject = $this->form->title;
        $res = $mail->Send();
    }

    public function sendConfirmEmail() {
        if (0 == $this->form->confirmto) {
            return;
        }
        $to = '';
        $subject = $this->form->confirmsubject;
        $body    = $this->form->confirmbody;
        foreach ($this->getData() as $result) {
            $subject = str_replace('[label_'.$result['field_id'].']', $result['label'], $subject);
            $subject = str_replace('[value_'.$result['field_id'].']', $result['value'], $subject);
            $body = str_replace('[label_'.$result['field_id'].']', $result['label'], $body);
            $body = str_replace('[value_'.$result['field_id'].']', $result['value'], $body);
            if ($result['field_id'] == $this->form->confirmto) {
                $to = $result['value'];
            }
        }
        if ('' == $to) {
            return;
        }
        $mail = new \rex_mailer();
        $mail->Body = $body;
        $mail->AltBody = strip_tags($body);
        $mail->addAddress($to);
        if ('' != $this->form->confirmbcc) {
            foreach(explode(',', $this->form->confirmbcc) as $bcc) {
                $mail->addBCC($bcc);
            }
        }
        $mail->Subject = $subject;
        $res = $mail->Send();
    }
}
