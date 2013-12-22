<?php
$GLOBALS['FORM_RESULTS'] = new FormResults();

$templateToEdit = Template::getTemplate($_POST['template']);

$success = true;
$templateToEdit->clearKeys();
foreach($_POST as $post=>$value){
	if(preg_match("/^key_([0-9][0-9]*)$/", $post, $matches)){
		$index = $matches[1];
		$key_name = $value;
		$type = $_POST['type_'.$index];
		$position = $_POST['pos_'.$index];
		$GLOBALS['FORM_RESULTS']->addValue('key_'.$index, $key_name);
		$GLOBALS['FORM_RESULTS']->addValue('type_'.$index, $type);
		$GLOBALS['FORM_RESULTS']->addValue('pos_'.$index, $position);
		
		$key = new TemplateKey($key_name, $type, $position-1);
		$success = $templateToEdit->setKey($key);
	}
	
}
//exit();
if($success)$GLOBALS['FORM_RESULTS']->addMsg("success", "Keys of '".$_POST['template']."' have been set");

$GLOBALS['FORM_RESULTS']->register();
header("Location:/admin/pages/set-template-keys.".$GLOBALS['PAGE_EXTENSION']."?tmplt=".$_POST['template']);
exit();
?>