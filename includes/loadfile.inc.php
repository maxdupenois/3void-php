<?php
//Check to see if file actually exists if so give preference to it
//This is here instead of in generate-page to avoid any unnecessary work
//Shouldn't get here if we setup .htaccess correctly so commented out
//if(isset($_GET['uri'])){
//    require_once(clasz('utils/FileUtils.class.php'));
//    $fileLocation = path($_GET['uri']);
//
//    $pathInfo = pathinfo($fileLocation);
//    if(isset($pathInfo['extension'])){
//        $ext = $pathInfo['extension'];
//        if(is_file($fileLocation)&&$ext!="php"){
////            echo "Here";
//            header("Content-type: ".FileUtils::getAppropriateContentType($fileLocation));
//            echo FileUtils::readBinary($fileLocation);
//            exit();
//        }
//    }
//}
?>
