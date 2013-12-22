<?php

class Template{
	protected $name, $location, $title, $description, $keys, $permission_use, $permission_edit, $permission_delete;
	
	public function __construct($name, $location, $title, $description,
				$permission_use, $permission_edit, $permission_delete, $keys=array()){
		$this->name = $name;
		$this->location = $location;
		$this->title = $title;
		$this->description = $description;
		$this->keys = $keys;
		$this->permission_use = $permission_use;
		$this->permission_edit = $permission_edit;
		$this->permission_delete = $permission_delete;
	}
	public function getName(){
		return $this->name;
	}
	public function getLocation(){
		return $this->location;
	}
	public function getTitle(){
		return $this->title;
	}
	public function getDescription(){
		return $this->description;
	}
	public function getKeys(){
		return $this->keys;
	}
	
	public function getPermissionUse(){
		return $this->permission_use;
	}
	public function getPermissionEdit(){
		return $this->permission_edit;
	}
	public function getPermissionDelete(){
		return $this->permission_delete;
	}
	
	public function getOrderedKeys(){
		$newArray = array();
		foreach($this->keys as $k){
			$newArray[$k->getOrder()] = $k; 
		}
		return $newArray;
	}
	public function suggestKeys(){
		$path = $GLOBALS['SITE_ROOT']."/templates/".$this->getLocation();
		$contents = FileUtils::getLocalFileContents($path);
		$regex ="/\\Q$\\Econtents\\['([^'][^']*)'\\]/";
		$keys = array();
		$keyNames = array();
		if(preg_match_all($regex, $contents, $matches, PREG_SET_ORDER)>1){
			$i = 0;
			foreach($matches as $match){
				$k = $match[1];
				$key = new TemplateKey($k, "line", $i);
				if(!in_array($k, $keyNames)){
					$keys[$i] = $key;
					$keyNames[] = $k;
				}
				$i++;
			}
		}
		return $keys;
	}
	public static function getTemplate($name){
		$db = $GLOBALS['DATABASE'];
		$connection = $db->getConnection();
		
		$sql = "SELECT location, title, description, `permission-use` AS puse, `permission-edit` AS pedit," 
		."`permission-delete` AS pdelete  FROM templates WHERE name = '$name'";
		$result = mysql_query($sql, $connection) or die(mysql_error());
		$location = "";
		$title = "";
		$description = "";
		$permission_use = "";
		$permission_edit = "";
		$permission_delete = "";
		if($t = mysql_fetch_object($result)){
			$location = $t->location;
			$title = $t->title;
			$description = $t->description;
			$permission_use = $t->puse;
			$permission_edit = $t->pedit;
			$permission_delete = $t->pdelete;
		}
		mysql_free_result($result);
		
		
		$sql = "SELECT `key`, `type`, `order`, `description` FROM `templates-contentkeys` WHERE template_name = '$name' ORDER BY `order` ASC";
		$result = mysql_query($sql, $connection) or die(mysql_error());
		$keys = array();
		while($k = mysql_fetch_object($result)){
			$keys[$k->key] = new TemplateContentKey($k->key, $k->type, $k->order, $k->description);
		}
		mysql_free_result($result);
		return new Template($name, $location, $title, $description, $permission_use, $permission_edit, $permission_delete, $keys);
	}
	
	public static function templateListing(){
		$listing = array();
		$userid = $_SESSION['userid'];
		if($userid == NULL || $userid == "")return $listing;
		$db = $GLOBALS['DATABASE'];
		$connection = $db->getConnection();
	
	
		$sql = 	"SELECT t.name, t.title, t.location, t.description, "
			."t.`permission-use` AS p_use, t.`permission-edit` AS p_edit, t.`permission-delete` AS p_delete "
			."FROM templates AS t "
			."WHERE (t.`permission-use` IN "
			."( "
			."SELECT agp.permission  "
			."FROM `access-groups-permissions`AS agp  "
			."LEFT JOIN users AS u on u.group = agp.group "
			."WHERE u.id='$userid' "
			.") "
			."AND t.`permission-use` NOT IN "
			."( "
			."SELECT up.permission  "
			."FROM `user-permissions`AS up  "
			."WHERE up.accept = 0 AND "
			."up.userid='$userid' "
			."))  "
			."OR t.`permission-use` IN "
			."( "
			."SELECT up.permission  "
			."FROM `user-permissions`AS up  "
			."WHERE up.accept = 1 AND "
			."up.userid='$userid' "
			.") OR t.`permission-use` = ''";
		$result = mysql_query($sql, $connection) or die($sql."<hr/>".mysql_error());
		while($t = mysql_fetch_object($result)){
			$listing[] = new Template($t->name, $t->location, $t->title, $t->description, $t->p_use,$t->p_edit, $t->p_delete);
		}
		return $listing;
	}

	private static function uniqueName($name){
		$db = $GLOBALS['DATABASE'];
		$connection = $db->getConnection();
		$orig_name = $name; 
		$unique = false;
		for($i=0,$max=100;!$unique&&$i<$max;$i++){
			$sql = "SELECT name FROM templates WHERE name=\"".$name."\"";
			//echo "$i: $name<br/>";
			$result = mysql_query($sql, $connection) or die(mysql_error());
			if($row = mysql_fetch_assoc($result)){
				$name = $orig_name."[".($i+1)."]";
			}else{
				$unique = true;
			}
			mysql_free_result($result);
		}
		//exit();
		if(!$unique) return NULL;
		return $name;
	}
	public static function add($path, $title, $description, $permission_use, $permission_edit, $permission_delete){
		$pi = pathinfo($path);
		$name = $pi['filename'];
		$name = Template::uniqueName($name);
		if($name != NULL){
			$db = $GLOBALS['DATABASE'];
			$connection = $db->getConnection();
			$sql = "INSERT templates (name, location, title, description, `permission-use`, `permission-edit`, `permission-delete`) "
					."VALUES ('$name', '$path', '$title', '$description', '$permission_use',  '$permission_edit', '$permission_delete')";
			mysql_query($sql, $connection) or die($sql."<hr/>".mysql_error());
		}else{
			$GLOBALS['FORM_RESULTS']->addErr("location", "Cannot get unique version of name");
		}
		return $name;
	
	}
	
	public function clearKeys(){
		if(!Authorisation::isAuthorised($this->getPermissionEdit())){
			$GLOBALS['FORM_RESULTS']->addErr("unauthorised", "You do not have permission to do this");
			return false;
		}
		$db = $GLOBALS['DATABASE'];
		$connection = $db->getConnection();
		
		$sql = "DELETE FROM `templates-contentkeys` WHERE `template_name`='".$this->getName()."'";
		mysql_query($sql, $connection) or die($sql."<hr/>".mysql_error());
	}
	public function setKey($key){
		if(!Authorisation::isAuthorised($this->getPermissionEdit())){
			$GLOBALS['FORM_RESULTS']->addErr("unauthorised", "You do not have permission to do this");
			return false;
		}
		$db = $GLOBALS['DATABASE'];
		$connection = $db->getConnection();
		
		$sql = "DELETE FROM `templates-contentkeys` WHERE `template_name`='".$this->getName()."' AND `key`='".$key->getName()."'";
		mysql_query($sql, $connection) or die($sql."<hr/>".mysql_error());
		
		
		$sql = "INSERT INTO `templates-contentkeys` (`template_name`, `key`, `type`, `order`) " 
				."VALUES ('".$this->getName()."','".$key->getName()."', '".$key->getType()."', ".$key->getOrder().")";
		mysql_query($sql, $connection) or die($sql."<hr/>".mysql_error());
		return true;
	}
}

?>