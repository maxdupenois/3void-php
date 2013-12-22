<?php
$GLOBALS['FORM_RESULTS'] = new FormResults();
//$location = "/admin/actions/create-action-instance.".$GLOBALS['PAGE_EXTENSION'];

/*$GLOBALS['FORM_RESULTS']->addValue("uri", $_POST['uri']);
$GLOBALS['FORM_RESULTS']->addValue("title", $_POST['title']);
$GLOBALS['FORM_RESULTS']->addValue("action", $_POST['action']);
$GLOBALS['FORM_RESULTS']->addValue("permission_view", $_POST['permission_view']);
$GLOBALS['FORM_RESULTS']->addValue("permission_edit", $_POST['permission_edit']);
$GLOBALS['FORM_RESULTS']->addValue("permission_edit_content", $_POST['permission_edit_content']);
$GLOBALS['FORM_RESULTS']->addValue("permission_delete", $_POST['permission_delete']);*/



//$location = (ActionInstance::add($_POST['uri'],$_POST['title'],  $_POST['permission_view'],
//	$_POST['permission_edit'], $_POST['permission_edit_content'],
//	$_POST['permission_delete'], $_POST['action'])?
//			"/admin/actions/set-parameters.".$GLOBALS['PAGE_EXTENSION']."?action=".$_POST['uri']:
//			"/admin/actions/create-action-instance.".$GLOBALS['PAGE_EXTENSION']);
	

//$GLOBALS['FORM_RESULTS']->register();
//header("Location:".$location);
//exit();

if(FormResults::isFormFieldEmpty("uri")){
    $GLOBALS['FORM_RESULTS']->addErr("uri","No uri given");
}else if(FormResults::isFormFieldEmpty("title")){
    $GLOBALS['FORM_RESULTS']->addErr("title","No title given");
}else if(FormResults::isFormFieldEmpty("action")){
    $GLOBALS['FORM_RESULTS']->addErr("action","No action given");
}else if(($action = Action::getAction($_POST['action']))==null){
    $GLOBALS['FORM_RESULTS']->addErr("action","No valid action given");
}else{
    $mapping = new ActionMapping($_POST['uri']);
    $mapping->setTitle($_POST['title']);
    $mapping->setRegex(($_POST['regex']=="1"));
    $mapping->setAction($action);
    $mapping->setPermissionView($_POST['permission_view']);
    $mapping->setPermissionEdit($_POST['permission_edit']);
    $mapping->setPermissionEditContent("locked");
    $mapping->setPermissionDelete($_POST['permission_delete']);
    $mapping->add();
}
include($GLOBALS['ACTIONS_FOLDER']."/admin/actions/action-mapping-listing.action.php");

?>