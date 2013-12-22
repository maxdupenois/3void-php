<?php
if(!is_dir(clasz())){
	die("Class folder '".clasz()."' not found");
}
//Load Classes
require_once(clasz("ClassLoader.class.php"));
ClassLoader::setRootClassFolder(clasz());
import("utils.FormResults");
import("user.User");
import("authorisation.Authorisation");
import("mappings.Mapping");
import("mappings.actions.*");
import("mappings.pages.*");
import("mappings.pages.templates.*");

?>
