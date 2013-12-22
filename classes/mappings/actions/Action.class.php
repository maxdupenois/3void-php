<?php
import("utils.Listing");
class Action {
    protected $id, $location, $title, $description, $keys, $permissionUse, $permissionEdit, $permissionDelete;
    
    public function __construct() {
    }
   

    public static function getAction($id, $purpose="use") {
        $db = $GLOBALS['DATABASE'];
        $connection = $db->getConnection();


        $sql = "SELECT location, title, description, ".
                    "`permission-use`, `permission-edit`, `permission-delete` ".
                    "FROM actions WHERE id = %d";
        $sqlObjects = $db->query($sql, $id);
        $action = null;
        if(($a=array_pop($sqlObjects))!=null) {
            $action = new Action();
            $action->setId($id);
            $action->setLocation($a->location);
            $action->setTitle($a->title);
            $action->setDescription($a->description);
            $action->setPermissionUse($a->{'permission-use'});
            $action->setPermissionEdit($a->{'permission-edit'});
            $action->setPermissionDelete($a->{'permission-delete'});
        }
        if(!Authorisation::authoriseAction($action, $purpose)) {
             $GLOBALS['FORM_RESULTS']->addErr("unauthorised", "You do not have permission to use this action for that purpose");
            return null;
        }
        return $action;
    }

    public static function actionListing() {
        $listing = array();
        $userid = $_SESSION['userid'];
        if($userid == NULL || $userid == "")return $listing;
        $db = $GLOBALS['DATABASE'];


        $sql = 	"SELECT a.id, a.title, a.location, a.description, "
            ."a.`permission-use`, a.`permission-edit`, a.`permission-delete` "
            ."FROM actions AS a "
            ."WHERE (a.`permission-use` IN "
            ."( "
            ."SELECT agp.permission  "
            ."FROM `access-groups-permissions`AS agp  "
            ."LEFT JOIN users AS u on u.group = agp.group "
            ."WHERE u.id=%d "
            .") "
            ."AND a.`permission-use` NOT IN "
            ."( "
            ."SELECT up.permission  "
            ."FROM `user-permissions`AS up  "
            ."WHERE up.accept = 0 AND "
            ."up.userid=%d "
            ."))  "
            ."OR a.`permission-use` IN "
            ."( "
            ."SELECT up.permission  "
            ."FROM `user-permissions`AS up  "
            ."WHERE up.accept = 1 AND "
            ."up.userid=%d "
            .") OR a.`permission-use` = ''";
        $sqlObjects = $db->query($sql, $userid, $userid, $userid);
        $action = null;
        while(($a=array_pop($sqlObjects))!=null) {
            $action = new Action();
            $action->setId($a->id);
            $action->setLocation($a->location);
            $action->setTitle($a->title);
            $action->setDescription($a->description);
            $action->setPermissionUse($a->{'permission-use'});
            $action->setPermissionEdit($a->{'permission-edit'});
            $action->setPermissionDelete($a->{'permission-delete'});
            $listing[] = $action;
        }
        return $listing;
    }

    public function upload($templocation){
         if(!Authorisation::isAuthorised("admin.action.create")){
			$GLOBALS['FORM_RESULTS']->addErr("unauthorised", "You do not have permission to upload an action");
			return false;
		}
        $location = $this->getLocation();
        $locationFolderPath = dirname($location);
        $destination = $GLOBALS['ACTIONS_FOLDER']."/".$location;
        $destination = FileUtils::getUniqueFileName($destination);
        $directory = dirname($destination);
        //Need to get possible altered filename,
        $filename = basename($destination);
        $location = ($locationFolderPath!="."?$locationFolderPath."/".$filename:$filename);
        $this->setLocation($location);
        
        if(!FileUtils::checkDir($directory)){
			$GLOBALS['FORM_RESULTS']->addErr("directory", "Cannot create directory '".$directory."'");
			return false;
        }
        if(!move_uploaded_file($templocation, $destination)){
			$GLOBALS['FORM_RESULTS']->addErr("move", "Cannot move file to '".$location."'");
			return false;
        }
        $db = $GLOBALS['DATABASE'];
        $sql = "INSERT INTO `actions` (`location`,`title`,`description`, ".
                "`permission-use`,`permission-edit`,`permission-delete`) VALUES ".
                "(\"%s\", \"%s\", \"%s\", \"%s\", \"%s\", \"%s\")";
        $db->query($sql, $this->getLocation(), $this->getTitle(),
                    $this->getDescription(), $this->getPermissionUse(),
                    $this->getPermissionEdit(), $this->getPermissionDelete());
        $this->setId($db->lastId());
        return true;
    }

    public function delete(){
        if(is_file($GLOBALS['ACTIONS_FOLDER']."/".$this->getLocation())){
            unlink($GLOBALS['ACTIONS_FOLDER']."/".$this->getLocation());
            $dir = dirname($GLOBALS['ACTIONS_FOLDER']."/".$this->getLocation());
            FileUtils::removeEmptyDirs($dir, $GLOBALS['ACTIONS_FOLDER']);
        }
        $db = $GLOBALS['DATABASE'];
        $success = true;
        $sql = "DELETE FROM `mappings` WHERE `uri` IN (".
                "SELECT uri FROM `mappings-actions` WHERE `action-id`=%d)";
        $success = $db->query($sql, $this->getId());
        if($success){
            $sql = "DELETE FROM `mappings-actions` WHERE `action-id`=%d";
            $success = $db->query($sql, $this->getId());
        }
        if($success){
            $sql = "DELETE FROM `actions` WHERE `id`=%d";
            $success = $db->query($sql, $this->getId());
        }
        if(!$success){
            $GLOBALS['FORM_RESULTS']->addErr("delete", "Failed to delete action");
			return false;
        }
        return $success;
    }

    public static function getActionFolderListing(){
        return Listing::listingByFolder(Action::actionListing(), "getLocation()");
    }
    public function getId() {
        return $this->id;
    }
    public function getLocation() {
        return $this->location;
    }
    public function getTitle() {
        return $this->title;
    }
    public function getDescription() {
        return $this->description;
    }
    public function getPermissionUse() {
        return $this->permissionUse;
    }
    public function getPermissionEdit() {
        return $this->permissionEdit;
    }
    public function getPermissionDelete() {
        return $this->permissionDelete;
    }



    public function setId($id) {
        $this->id = $id;
    }
    public function setLocation($location) {
        $this->location = $location;
    }
    public function setTitle($title) {
        $this->title = $title;
    }
    public function setDescription($description) {
        $this->description = $description;
    }
    public function setPermissionUse($permission_use) {
        $this->permissionUse = $permission_use;
    }
    public function setPermissionEdit($permissionEdit) {
        $this->permissionEdit = $permissionEdit;
    }
    public function setPermissionDelete($permission_delete) {
        $this->permissionDelete = $permission_delete;
    }

}

?>