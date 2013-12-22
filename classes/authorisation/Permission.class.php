<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Permissionclass
 *
 * @author mpd209
 */
class Permission {
    private $permissionString;
    private $deletable;
    private $description;

    public function __construct(){}
    public function __destruct(){}


    public function add(){
        if(!Authorisation::isAuthorised("admin.permissions.add")){
            $GLOBALS['FORM_RESULTS']->addErr("unauthorised", "You do not have permission to add a permission");
            return false;
        }
        if(Permission::getPermission($this->getPermissionString())!=null){
            $GLOBALS['FORM_RESULTS']->addErr("permission", "This permission already exists");
            return false;
        }
        $db = $GLOBALS['DATABASE'];
        $sql = "INSERT INTO `access-permissions` (`permission`, `deletable`,"
                ." `description`) VALUES (\"%s\", %d, \"%s\")";
        if(!$db->query($sql, $this->getPermissionString(), ($this->isDeletable()?1:0),
            $this->getDescription())){
            $GLOBALS['FORM_RESULTS']->addErr("database", "Failed to insert permission");
            return false;
        }
        //Make sure we can use it
        $sql = "INSERT INTO `user-permissions` (`userid`, `permission`, `accept`"
                .") VALUES (\"%s\", \"%s\", 1)";
        if(!$db->query($sql, Authorisation::currentUserId(), $this->getPermissionString())){
            $GLOBALS['FORM_RESULTS']->addErr("database", "Failed to add permission to user");
            return false;
        }
        $GLOBALS['FORM_RESULTS']->clear();
        $GLOBALS['FORM_RESULTS']->addMsg("success", "Permission '".$this->getPermissionString()."' has been added");
        return true;
    }

    public function delete(){
        if(!Authorisation::isAuthorised("admin.permissions.delete")){
            $GLOBALS['FORM_RESULTS']->addErr("unauthorised", "You do not have permission to delete a permission");
            return false;
        }
        if(!Permission::getPermission($this->getPermissionString())->isDeletable()){
            $GLOBALS['FORM_RESULTS']->addErr("permission", "This permission is not deletable");
            return false;
        }
        $db = $GLOBALS['DATABASE'];
        $sql = "DELETE FROM `access-groups-permissions` WHERE `permission`=\"%s\"";
        if(!$db->query($sql, $this->getPermissionString())){
            $GLOBALS['FORM_RESULTS']->addErr("database", "Failed to delete permission group mapping");
            return false;
        }
        $sql = "DELETE FROM `user-permissions` WHERE `permission`=\"%s\"";
        if(!$db->query($sql, $this->getPermissionString())){
            $GLOBALS['FORM_RESULTS']->addErr("database", "Failed to delete permission user mapping");
            return false;
        }
        $sql = "DELETE FROM `access-permissions` WHERE `permission`=\"%s\"";
        if(!$db->query($sql, $this->getPermissionString())){
            $GLOBALS['FORM_RESULTS']->addErr("database", "Failed to delete permission");
            return false;
        }
        $GLOBALS['FORM_RESULTS']->clear();
        $GLOBALS['FORM_RESULTS']->addMsg("success", "Permission '".$this->getPermissionString()."' has been deleted");
        return true;
    }

    public static function getPermissions(){
        $listing = array();
        if(($userid = Authorisation::currentUserId())==null){
            return $listing;
        }
        $db = $GLOBALS['DATABASE'];

        $sql = 	"("
                ."SELECT agp.permission, p.description, p.deletable "
                ."FROM `access-groups-permissions` AS agp "
                ."LEFT JOIN users AS u ON u.group = agp.group "
                ."LEFT JOIN `access-permissions` AS p ON p.permission = agp.permission "
                ."WHERE u.id = \"%s\" "
                ."AND agp.permission NOT "
                ."IN ( "
                ."SELECT up.permission "
                ."FROM `user-permissions` AS up "
                ."WHERE up.userid = \"%s\" "
                ."AND up.accept =0 "
                .") "
                .") "
                ."UNION ( "
                ."SELECT up.permission, p.description, p.deletable "
                ."FROM `user-permissions` AS up "
                ."LEFT JOIN `access-permissions` AS p ON p.permission = up.permission "
                ."WHERE up.userid = \"%s\" "
                ."AND up.accept =1 "
                .")"
                ."UNION ( "
                ."SELECT p.permission, p.description, p.deletable "
                ."FROM `access-permissions` AS p "
                ."WHERE p.permission = '' "
                .") ORDER BY permission ASC";
        $sqlObjects = $db->query($sql, $userid, $userid, $userid);

        $permission = null;
        while($p = array_pop($sqlObjects)){
            $permission = new Permission();
            $permission->setPermissionString($p->permission);
            $permission->setDescription($p->description);
            $permission->setDeletable(($p->deletable=="1"));
            $listing[] = $permission;
        }
        return $listing;
    }


    public static function getPermission($permissionStr){
        if(!Authorisation::isAuthorised($permissionStr)){
            return null;
        }

        $db = $GLOBALS['DATABASE'];

        $sql = 	"SELECT p.permission, p.description, p.deletable "
                ."FROM `access-permissions` AS p "
                ."WHERE p.permission =  \"%s\" "
                ."ORDER BY permission ASC ";
        $sqlObjects = $db->query($sql, $permissionStr);
        $permission = null;
        if($p = array_pop($sqlObjects)){
            $permission = new Permission();
            $permission->setPermissionString($p->permission);
            $permission->setDescription($p->description);
            $permission->setDeletable(($p->deletable=="1"));
        }
        return $permission;
    }
    public static function getPermissionListingByFolder(){
        return Listing::listingByFolder(Permission::getPermissions(), "getPermissionString()", ".");
    }

    public function setPermissionString($permissionString){
        $this->permissionString = $permissionString;
    }
    public function setDeletable($deletable){
        $this->deletable = $deletable;
    }
    public function setDescription($description){
        $this->description = $description;
    }

    public function getPermissionString(){
        return $this->permissionString;
    }
    public function isDeletable(){
        return $this->deletable;
    }
    public function getDescription(){
        return $this->description;
    }

}
?>
