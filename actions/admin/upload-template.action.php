<?php
$GLOBALS['FORM_RESULTS'] = new FormResults();
$fail_location = "/admin/pages/add-template.".$GLOBALS['PAGE_EXTENSION'];
$success_location = "/admin/pages/set-template-keys.".$GLOBALS['PAGE_EXTENSION']."?tmplt=";


$GLOBALS['FORM_RESULTS']->addValue("location", $_POST['location']);
$GLOBALS['FORM_RESULTS']->addValue("title", $_POST['title']);
$GLOBALS['FORM_RESULTS']->addValue("description", $_POST['description']);
$GLOBALS['FORM_RESULTS']->addValue("permission_use", $_POST['permission_use']);
$GLOBALS['FORM_RESULTS']->addValue("permission_edit", $_POST['permission_edit']);
$GLOBALS['FORM_RESULTS']->addValue("permission_delete", $_POST['permission_delete']);

if(!isset($_POST['title']) || $_POST['title'] == NULL || $_POST['title'] == ""){
	$GLOBALS['FORM_RESULTS']->addErr("title", "No title given.");
}


$location = $_POST['location'];

//Remove backslashes
$location = preg_replace("/\\\\/", "/", $location);
//Remove whitespace
$location = preg_replace("/\\s\\s*/", "_", $location);
//Remove leading slash
$location = (substr($location, 0, 1)=="/"?substr($location, 1):$location);

//If location includes a name, which we'll 
//specify as needing an extension,
//then we use the pathinfo of the $location
//otherwise we take the location as the dir
//and the File name as the name 

//Reg exp = any number of anything including a . and no / at the end
if(preg_match("/([^\\/]*\\.[^\\/]*)([^\\/]*\\.[^\\/]*)*$/", $location)>0){
	//Here we assume location includes a filenam
	$pathLocInfo  = pathinfo($location);
	$dir = (isset($pathLocInfo['dirname'])?$pathLocInfo['dirname']:"");
	$filename = $pathLocInfo['basename'];
	$ext = $pathLocInfo['extension'];
	$filenameNoExt = $pathLocInfo['filename'];
}else{
	$pathInfo = pathinfo($_FILES['template']['name']);
	$filename = $pathInfo['basename'];
	$ext = $pathInfo['extension'];
	$filenameNoExt = $pathInfo['filename'];
	$dir = $location;
}

if($dir==".")$dir = "";
if($dir!=""&&substr($dir, -1)=="/") $dir = substr($dir, 0, -1);


$fullDirectoryPath = $GLOBALS['SITE_ROOT']."/templates/".$dir;
$fullFilePath = $fullDirectoryPath."/".$filenameNoExt.".".$ext;

if(!FileUtils::checkDir($fullDirectoryPath)) {
	$GLOBALS['FORM_RESULTS']->addErr("location", "Cannot find or create destination path");
}else if(is_file($fullFilePath)){
	$GLOBALS['FORM_RESULTS']->addErr("location", "Filename is not unique in this location, please specify a new one.");
}


if($GLOBALS['FORM_RESULTS']->hasErrors()){
	$GLOBALS['FORM_RESULTS']->register();
	header("Location:".$fail_location);
	exit();
}

if(!move_uploaded_file($_FILES['template']['tmp_name'], $fullFilePath)){
	$GLOBALS['FORM_RESULTS']->addErr("location", "Could not upload file to this location, I may not have permission.");
	$GLOBALS['FORM_RESULTS']->register();
	header("Location:".$fail_location);
	exit();
}

$path = ($dir!=""?$dir."/":"").$filenameNoExt.".".$ext;

$name = Template::add($path, $_POST['title'], $_POST['description'], 
	$_POST['permission_use'],$_POST['permission_edit'], $_POST['permission_delete']);

$loc = $fail_location;
if($name != NULL) $loc = $success_location.$name;
	
	
$GLOBALS['FORM_RESULTS']->register();
header("Location:".$loc);
exit();

?>