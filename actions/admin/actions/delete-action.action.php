<?php
$GLOBALS['FORM_RESULTS'] = new FormResults();

$action = Action::getAction($_POST['actionid'], "delete");
if($action!=null){
    $action->delete();
}else{
    $GLOBALS['FORM_RESULTS']->addErr("action", "Action does not exist");
}


include($GLOBALS['ACTIONS_FOLDER']."/admin/actions/action-listing.action.php");

?>