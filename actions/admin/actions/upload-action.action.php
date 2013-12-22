<?php
$GLOBALS['FORM_RESULTS'] = new FormResults();
$permissionUse = $_POST["permission_use"];
$permissionEdit = $_POST["permission_edit"];
$permissionDelete = $_POST["permission_delete"];
$result = 0;
if(isset($_POST["location"])&&$_POST["location"]!=""){
    if(substr($_POST["location"], -1)=="/"){
        $location = $_POST["location"].$_FILES['currentfile']['name'];
    }else{
        $location = $_POST["location"];
    }
}else{
    $location = $_FILES['currentfile']['name'];
}
if(substr($location, 0, 1)=="/"){
    $location = substr($location, 1);
}

$id = $_POST['fileid'];
$key = $_POST['uploaderkey'];
$err = array();
if(FormResults::isFormFieldEmpty("title")){
    $err["message"] = "No title given";
    $result = 0;
}else if(FormResults::isFormFieldEmpty("description")){
    $err["message"] = "No description given";
    $result = 0;
}else{
    $action = new Action();
    $action->setDescription($_POST['description']);
    $action->setLocation($location);
    $action->setTitle($_POST['title']);
    $action->setPermissionUse($permissionUse);
    $action->setPermissionEdit($permissionEdit);
    $action->setPermissionDelete($permissionDelete);
    $location = $action->getLocation();
    $result = ($action->upload($_FILES['currentfile']['tmp_name'])?1:0);

    $err = error_get_last();
}
//$form_err = $GLOBALS['FORM_RESULTS']->errorsToString();

//if($GLOBALS['DEBUG'])
?>
<html>
<head>
<script language="javascript" type="text/javascript">

	window.top.window.IFrameUploaderManager.finishedUpload(
       "<?php echo $key; ?>",
        <?php echo $id; ?>,
        <?php echo $result; ?>,
       "<?php echo $location; ?>",
       "<?php echo $err["message"]; ?>"
    );

</script>
</head>
<body></body>
</html>