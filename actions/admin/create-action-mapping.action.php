<?php
$GLOBALS['FORM_RESULTS'] = new FormResults();
$location = "/admin/actions/create-action-instance.".$GLOBALS['PAGE_EXTENSION'];


$GLOBALS['FORM_RESULTS']->addValue("uri", $_POST['uri']);
$GLOBALS['FORM_RESULTS']->addValue("title", $_POST['title']);
$GLOBALS['FORM_RESULTS']->addValue("action", $_POST['action']);
$GLOBALS['FORM_RESULTS']->addValue("permission_view", $_POST['permission_view']);
$GLOBALS['FORM_RESULTS']->addValue("permission_edit", $_POST['permission_edit']);
$GLOBALS['FORM_RESULTS']->addValue("permission_edit_content", $_POST['permission_edit_content']);
$GLOBALS['FORM_RESULTS']->addValue("permission_delete", $_POST['permission_delete']);

$location = (ActionInstance::add($_POST['uri'],$_POST['title'],  $_POST['permission_view'], 
	$_POST['permission_edit'], $_POST['permission_edit_content'], 
	$_POST['permission_delete'], $_POST['action'])?
			"/admin/actions/set-parameters.".$GLOBALS['PAGE_EXTENSION']."?action=".$_POST['uri']:
			"/admin/actions/create-action-instance.".$GLOBALS['PAGE_EXTENSION']);
	

$GLOBALS['FORM_RESULTS']->register();
header("Location:".$location);
exit();



?>