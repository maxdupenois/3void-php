<?php
$GLOBALS['FORM_RESULTS'] = new FormResults();

$file = File::getFile($_POST['fileid'], "delete");
if($file!=null){
    $file->delete();
    unlink($file->getLocation());

    $dir = dirname($file->getUri());
    removeEmptyDirs($dir);

}else{
    $GLOBALS['FORM_RESULTS']->addErr("file", "File does not exist");
}
function removeEmptyDirs($dir){
    if($dir != "" && $dir != "."){
        if($dh  = opendir($GLOBALS['UPLOADS_FOLDER']."/".$dir)){
            $empty = true;
            while ((false !== ($filename = readdir($dh)))&&$empty) {
                $empty = ($filename=="."||$filename=="..");
            }
            closedir($dh);
            if($empty){
                rmdir($GLOBALS['UPLOADS_FOLDER']."/".$dir);
                //go up folder
                $lastSlash = strrpos($dir, "/");
                if($lastSlash!==false){
                    removeEmptyDirs(substr($dir, 0, $lastSlash));
                }
            }
        }
    }
}

//Don't need to register
//$GLOBALS['FORM_RESULTS']->register();

include($GLOBALS['ACTIONS_FOLDER']."/admin/file-listing.action.php");

?>