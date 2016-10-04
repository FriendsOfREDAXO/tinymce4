<?php
	
	$code = '';
	$code .= '<fieldset class="form-horizontal">'.PHP_EOL;
	$code .= '  <div class="form-group">'.PHP_EOL;
	$code .= '    <label class="col-sm-2 control-label" for="value-1">VALUE 1</label>'.PHP_EOL;
	$code .= '    <div class="col-sm-10">'.PHP_EOL;
	$code .= '      <textarea class="form-control tinyMCEEditor" id="value-1" name="REX_INPUT_VALUE[1]">REX_VALUE[1]</textarea>'.PHP_EOL;
	$code .= '    </div>'.PHP_EOL;
	$code .= '  </div>'.PHP_EOL;
	$code .= '</fieldset>';
	
	$fragment = new rex_fragment();
	$fragment->setVar('class', 'info', false);
	$fragment->setVar('title', 'Beispiel: Module Input', false); //todo
	$fragment->setVar('body', rex_string::highlight($code), false);
	echo $fragment->parse('core/page/section.php');
	
	///
	
	$code = '';
	$code .= 'REX_VALUE[id="1" output="html"]';
	
	$fragment = new rex_fragment();
	$fragment->setVar('class', 'info', false);
	$fragment->setVar('title', 'Beispiel: Module Output', false); //todo
	$fragment->setVar('body', rex_string::highlight($code), false);
	echo $fragment->parse('core/page/section.php');

        $fragment = new rex_fragment();
	$fragment->setVar('class', 'info', false);
	$fragment->setVar('title', 'Weitere Infos', false); //todo
	$fragment->setVar('body', '<a target="_blank" href="https://github.com/akuehnis/redaxo5_addon_tinymce4">https://github.com/akuehnis/redaxo5_addon_tinymce4</a>', false);
	echo $fragment->parse('core/page/section.php');
	
	
?>
