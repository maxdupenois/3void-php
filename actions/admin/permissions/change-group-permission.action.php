<?php
$GLOBALS['FORM_RESULTS'] = new FormResults();

$messages = array();
$success = false;
$allowed = false;
if(FormResults::isFormFieldEmpty("permission")){
    $messages[] ="No permission string given";
}else if(FormResults::isFormFieldEmpty("group")){
    $messages[] ="No group given";
}else if(FormResults::isFormFieldEmpty("allow")){
    $messages[] ="No change to value given";
}else{
    $permission = Permission::getPermission($_POST['permission']);
    $group = Group::getGroup($_POST['group']);
    if($_POST['allow']=="true"){
        $success = $group->allow($permission);
        $allowed = true;
    }else{
        $success = $group->deny($permission);
        $allowed = false;
    }
}
?>
{   'success':<?=($success?"true":"false")?>,
    'messages':[
<?php
$first = true;
foreach($messages as $m){
    if(!$first) echo ",";
    echo "\"".$m."\"";
    $first = false;
}?>],
    'allowed':<?=($allowed?"true":"false")?>
}