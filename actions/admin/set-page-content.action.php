<?php
$GLOBALS['FORM_RESULTS'] = new FormResults();

$pageToEdit = Page::getPage($_POST['page']);
$pageTemplate = $pageToEdit->getTemplate();
$templateKeys = $pageTemplate->getOrderedKeys();
$success = true;
foreach($templateKeys as $key) {
	$GLOBALS['FORM_RESULTS']->addValue($key->getName(), $_POST[$key->getName()]);
	
	
	
	/*echo "\n------------------------------\n";
	echo "\n------------------------------\n";
	echo "\n------------------------------\n";
	echo $key->getName();
	echo "\n------------------------------\n";
	echo "Standard: ".$cleaned;
	echo "\n==============================\n";
	echo "Escaped: ".mysql_real_escape_string($cleaned);
	echo "\n==============================\n";
	echo "Stripped: ".stripslashes(mysql_real_escape_string($cleaned));
	echo "\n------------------------------\n\n\n\n\n";*/
	
	$success = $pageToEdit->editContent($key, $_POST[$key->getName()]);
}
if($success)$GLOBALS['FORM_RESULTS']->addMsg("success", "Content of '".$_POST['page']."' has been set");

$GLOBALS['FORM_RESULTS']->register();
header("Location:/admin/pages/set-page-content.".$GLOBALS['PAGE_EXTENSION']."?page=".$_POST['page']);
exit();
?>