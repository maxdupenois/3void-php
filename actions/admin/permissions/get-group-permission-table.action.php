<?php
$GLOBALS['FORM_RESULTS'] = new FormResults();
//
//                
$messages = array();
$success = false;
$allowed = false;
$groups = array();
$permissions = array();
if(FormResults::isFormFieldEmpty("offset")){
    $messages[] ="No offset given";
}else if(FormResults::isFormFieldEmpty("limit")){
    $messages[] ="No limit given";
}else{
    $asc = true;
    if(!FormResults::isFormFieldEmpty("asc")){
        $asc = ($_POST['asc']=="true");
    }
    //Nothing else to sort by
//    $sort = "group";
//    if(!FormResults::isFormFieldEmpty("sort")){
//        $sort = ($_POST['sort']);
//    }
    $permissions = Permission::getPermissions();
    $groups = Group::getGroups(intval($_POST['limit'],10), intval($_POST['offset'],10));
}
?>


{
    head : [{"class":"permissionHeaderRow", "cells":[
    {"cellInfo":"","class":"groupLabel"}
    <?php foreach($groups as $g){?>
    ,{"cellInfo":"<?=$g->getGroupName()?>","class":"groupLabel"}
    <?php } ?>
    ]}],
    body : [
        <?php
        $firstRow = true;
        foreach($permissions as $p){
            if($p->getPermissionString()=="") continue; ?>
            <?=($firstRow?"":",")?>
            {"class":"permissionRow", "cells":[
            {"cellInfo":"<?=$p->getPermissionString()?>","class":"permissionLabel"}
            <?php
            foreach($groups as $g){
                $hasPermission = $g->hasPermission($p->getPermissionString());
            ?>
            ,{"cellInfo":{"permissionName":"<?=$p->getPermissionString()?>",
                        "groupName":"<?=$g->getGroupName()?>",
                        "hasPermission":<?=($hasPermission?"true":"false")?>}
                        ,"class":"permissionCell"}
            <?php }?>
            ]}
        <?php
            $firstRow = false;
        } ?>
    ]
}