<?php
class ClassLoader{
    private static $rootClassFolder = "";

    public static function setRootClassFolder($rootClassFolder){
        ClassLoader::$rootClassFolder = $rootClassFolder;
    }
    public static function getRootClassFolder(){
        return ClassLoader::$rootClassFolder;
    }
    
    public static function autoload($name){
        if($name == "")return;
	$classname = $name.".class.php";
        if(file_exists($classname)){
            require_once($classname);
            return;
        }
        //If not found need to search sub directories
        $classpath = ClassLoader::findClass($classname, ClassLoader::$rootClassFolder);
        if($classpath==null){
            echo("CLASS LOAD ERROR: '".$classname."' not found");
            exit();
        }
	require_once($classpath);
    }
    public static function findClass($classToFind, $current_folder){
        $foundClass = false;
        if(file_exists($current_folder."/".$classToFind)){
            return $current_folder."/".$classToFind;
        }
        $classpath = null;
        if ($dh = opendir($current_folder)){
            //Explicity checked in case $file evaluates as false (i.e. is a directory named 0)
            while ((($file = readdir($dh)) !== false )&& $classpath==null){
                if($file=="."||$file=="..") continue;
                if(is_dir($current_folder."/".$file)) {
                   $classpath = ClassLoader::findClass($classToFind, $current_folder."/".$file);
                }
            }
            closedir($dh);
        }else{
            return null;
        }
        return $classpath;
    }
    public static function includeallfrom($current_folder){
        if ($dh = opendir($current_folder)) {
            if(substr($current_folder, -1)=="/"){
                $current_folder = substr($current_folder, 0, strlen($current_folder)-1);
            }
            //Explicity checked in case $file evaluates as false (i.e. is a directory named 0)
            while (($file = readdir($dh)) !== false) {
                if($file=="."||$file=="..") continue;
                if(is_dir($current_folder."/".$file)) {
                    ClassLoader::includeallfrom($current_folder."/".$file);
                }else if(preg_match('/.*\.class\.php/i', $file)){
                    require_once($current_folder."/".$file);
                }
            }
            closedir($dh);
        }
    }
    public static function import($class_string){
        $parts = explode(".", $class_string);
        $length = count($parts);
        if($length < 1) return;
        $classnamepart = $parts[$length-1];
        if($classnamepart!="*" && class_exists($classnamepart, false)) return;

        $location = ClassLoader::$rootClassFolder."/";
        
        for($i = 0; $i < $length-1; $i++){
            $location .= $parts[$i]."/";
        }
        if($classnamepart=="*"){
            ClassLoader::includeallfrom($location);
        }else{
            $location .=  $classnamepart.".class.php";

            require_once($location);
        }
    }

}
//function __autoload($name){
//    ClassLoader::autoload($name);
//}

//Used like import("utils.FileUtils") or import("utils.*")
function import($class_string){
    ClassLoader::import($class_string);
}



?>