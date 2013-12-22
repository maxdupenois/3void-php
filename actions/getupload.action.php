<?php 
if(isset($_GET['file'])){
    File::streamFile($_GET['file']);
}else{
//    echo preg_replace("/^uploads/", "", $_GET['uri']);
    File::streamFile(preg_replace("/^uploads\//", "", $_GET['uri']));
}
?>