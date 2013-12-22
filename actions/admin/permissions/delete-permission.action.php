<?php
$GLOBALS['FORM_RESULTS'] = new FormResults();

$permission = Permission::getPermission($_POST['permission']);
if($permission!=null){
    $permission->delete();
}else{
    $GLOBALS['FORM_RESULTS']->addErr("permission", "Permission not found");
}


include($GLOBALS['ACTIONS_FOLDER']."/admin/permissions/permission-listing.action.php");

?>