<?php
$GLOBALS['FORM_RESULTS'] = new FormResults();


$GLOBALS['FORM_RESULTS']->addValue("uri", $_POST['uri']);
$GLOBALS['FORM_RESULTS']->addValue("title", $_POST['title']);
$GLOBALS['FORM_RESULTS']->addValue("template", $_POST['template']);
$GLOBALS['FORM_RESULTS']->addValue("permission_view", $_POST['permission_view']);
$GLOBALS['FORM_RESULTS']->addValue("permission_edit", $_POST['permission_edit']);
$GLOBALS['FORM_RESULTS']->addValue("permission_edit_content", $_POST['permission_edit_content']);
$GLOBALS['FORM_RESULTS']->addValue("permission_delete", $_POST['permission_delete']);

$page = Page::getPage($_POST['page'], "edit");

$templateChanged = ($page->getTemplate()->getName() != $_POST['template']);

$success = $page->edit($_POST['uri'],$_POST['title'],  $_POST['permission_view'], 
	$_POST['permission_edit'], $_POST['permission_edit_content'], 
	$_POST['permission_delete'], $_POST['template']);
	
if($success && $templateChanged){
	$location= "/admin/pages/set-page-content.".$GLOBALS['PAGE_EXTENSION']."?page=".$page->getURI();
}else{
	$location= "/admin/pages/edit-page.".$GLOBALS['PAGE_EXTENSION']."?page=".($success?$page->getURI():$_POST['page']);
}

$GLOBALS['FORM_RESULTS']->register();
header("Location:".$location);
exit();

?>