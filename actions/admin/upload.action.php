<?php
$permissionView = $_POST["permission_view"];
$permissionEdit = $_POST["permission_edit"];
$permissionDelete = $_POST["permission_delete"];
$result = 0;
if(isset($_POST["uri"])&&$_POST["uri"]!=""){
    if(substr($_POST["uri"], -1)=="/"){
        $uri = $_POST["uri"].$_FILES['currentfile']['name'];
    }else{
        $uri = $_POST["uri"];
    }
}else{
    $uri = $_FILES['currentfile']['name'];
}
if(substr($uri, 0, 1)=="/"){
    $uri = substr($uri, 1);
}
$file = File::uploadFile($_FILES['currentfile']['tmp_name'],  $uri,
    $permissionView, $permissionEdit, $permissionDelete);

if($file!=null) $result = 1;

$id = $_POST['fileid'];
$key = $_POST['uploaderkey'];
$err = error_get_last();
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
       "<?php echo $uri; ?>",
       "<?php echo $err["message"]; ?>"
    );

</script>
</head>
<body></body>
</html>