<?php
$GLOBALS['FORM_RESULTS'] = new FormResults();


if(FormResults::isFormFieldEmpty("permissionString")){
    $GLOBALS['FORM_RESULTS']->addErr("permissionString","No permission string given");
}else if(FormResults::isFormFieldEmpty("description")){
    $GLOBALS['FORM_RESULTS']->addErr("description","No description given");
}else{
    $permission = new Permission();
    $permission->setPermissionString($_POST['permissionString']);
    $permission->setDescription($_POST['description']);
    $permission->setDeletable(true);
    $permission->add();
}
include($GLOBALS['ACTIONS_FOLDER']."/admin/permissions/permission-listing.action.php");

?>