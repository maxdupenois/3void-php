<?php
$GLOBALS['FORM_RESULTS'] = new FormResults();
$location = "/admin/pages/create-page.".$GLOBALS['PAGE_EXTENSION'];


$GLOBALS['FORM_RESULTS']->addValue("uri", $_POST['uri']);
$GLOBALS['FORM_RESULTS']->addValue("title", $_POST['title']);
$GLOBALS['FORM_RESULTS']->addValue("template", $_POST['template']);
$GLOBALS['FORM_RESULTS']->addValue("permission_view", $_POST['permission_view']);
$GLOBALS['FORM_RESULTS']->addValue("permission_edit", $_POST['permission_edit']);
$GLOBALS['FORM_RESULTS']->addValue("permission_edit_content", $_POST['permission_edit_content']);
$GLOBALS['FORM_RESULTS']->addValue("permission_delete", $_POST['permission_delete']);

$page = new Page($_POST['uri']);
$page->setTitle($_POST['title']);
$page->setRegex(false);
$page->setTemplate(Template::getTemplate($_POST['template']));
$page->setPermissionView($_POST['permission_view']);
$page->setPermissionEdit($_POST['permission_edit']);
$page->setPermissionEditContent($_POST['permission_edit_content']);
$page->setPermissionDelete($_POST['permission_delete']);

$location = ($page->add()?
			"/admin/pages/set-page-content.".$GLOBALS['PAGE_EXTENSION']."?page=".$_POST['uri']:
			"/admin/pages/create-page.".$GLOBALS['PAGE_EXTENSION']);
	

$GLOBALS['FORM_RESULTS']->register();
header("Location:".$location);
exit();



?>