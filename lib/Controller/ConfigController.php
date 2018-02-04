<?php
namespace Tinymce4\Controller;

class ConfigController 
{
    public $container;

    public function __construct($container) {
        $this->container = $container; 
    }

    public function editAction() {
        $model = new \Tinymce4\Models\Config();
        $model->hydrate();
        $errors = array();
        if (isset($_POST['model'])) {
            $model->setFormData($_POST['model'], $this->container);
            $errors = $model->validate($this->container);
            if (0 == count($errors)) {
                $model->save();
                $url = $this->container->get('UrlService')->getUrl('/profile/index'); 
                header("Location: $url");
                die();
            }
        }

        return $this->container->get('RenderService')->render(
            'backend/config_edit.php', array(
                'model' => $model,
                'form' => $this->container->get('FormService'),
                'UrlService' => $this->container->get('UrlService'),
                'Translator' => $this->container->get('TranslatorService'),
                'errors' => $errors,
            ));
    }

    public function helpAction() {
        ob_start();
        include __DIR__.'/../../help.php';
        return ob_get_clean();
    }

    public function createModuleAction() {
        $db = \rex_sql::factory();
        $db->setTable(\rex::getTable('module'));
        $db->setValue('name', 'Tinymce4 Demo Modul');
        $code = '';
	$code .= '<fieldset class="form-horizontal">'.PHP_EOL;
	$code .= '  <div class="form-group">'.PHP_EOL;
	$code .= '    <label class="col-sm-2 control-label" for="value-1">VALUE 1</label>'.PHP_EOL;
	$code .= '    <div class="col-sm-10">'.PHP_EOL;
	$code .= '      <textarea class="form-control tinyMCEEditor" id="value-1" name="REX_INPUT_VALUE[1]">REX_VALUE[1]</textarea>'.PHP_EOL;
	$code .= '    </div>'.PHP_EOL;
	$code .= '  </div>'.PHP_EOL;
	$code .= '</fieldset>';
        $db->setValue('input', $code);
        $db->setValue('output', 'REX_VALUE[id="1" output="html"]');
        $db->insert();
        return $this->helpAction();
    }
     
}


