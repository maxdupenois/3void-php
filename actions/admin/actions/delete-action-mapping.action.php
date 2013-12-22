<?php
$GLOBALS['FORM_RESULTS'] = new FormResults();

$actionMapping = ActionMapping::getActionMapping($_POST['actionmapping'], "delete");
if($action!=null){
    $actionMapping->delete();
}else{
    $GLOBALS['FORM_RESULTS']->addErr("actionmapping", "Action mapping not found");
}


include($GLOBALS['ACTIONS_FOLDER']."/admin/actions/action-mapping-listing.action.php");

?>