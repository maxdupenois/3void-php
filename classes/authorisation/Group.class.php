<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Groupclass
 *
 * @author Max
 */
class Group {
    private $groupName;
    private $description;
    private $userCount=-1;

    public function __construct(){}
    public function __destruct(){}


    public function add(){
        if(!Authorisation::isAuthorised("admin.group.add")){
            $GLOBALS['FORM_RESULTS']->addErr("unauthorised", "You do not have permission to add a group");
            return false;
        }
        if(Group::getGroup($this->getGroupName())!=null){
            $GLOBALS['FORM_RESULTS']->addErr("group", "This group already exists");
            return false;
        }
        $db = $GLOBALS['DATABASE'];
        $sql = "INSERT INTO `access-groups` (`group`, `description`) VALUES (\"%s\", \"%s\")";
        if(!$db->query($sql, $this->getGroupName(), $this->getDescription())){
            $GLOBALS['FORM_RESULTS']->addErr("database", "Failed to insert group");
            return false;
        }

        $GLOBALS['FORM_RESULTS']->clear();
        $GLOBALS['FORM_RESULTS']->addMsg("success", "Group '".$this->getGroupName()."' has been added");
        return true;
    }
    public function allow($permission){
        if(!Authorisation::isAuthorised("admin.group.permissionchange ")){
            $GLOBALS['FORM_RESULTS']->addErr("unauthorised", "You do not have permission to change group permission");
            return false;
        }
        if($this->hasPermission($permission->getPermissionString())){
            return true;
        }
        $db = $GLOBALS['DATABASE'];
        $sql = "INSERT INTO `access-groups-permissions` (`group`, `permission`) VALUES (\"%s\", \"%s\")";
        if(!$db->query($sql, $this->getGroupName(), $permission->getPermissionString())){
            $GLOBALS['FORM_RESULTS']->addErr("database", "Failed to set permission");
            return false;
        }
        return true;
    }
    public function deny($permission){
        if(!Authorisation::isAuthorised("admin.group.permissionchange ")){
            $GLOBALS['FORM_RESULTS']->addErr("unauthorised", "You do not have permission to change group permission");
            return false;
        }
        if(!$this->hasPermission($permission->getPermissionString())){
            return true;
        }
        $db = $GLOBALS['DATABASE'];
        $sql = "DELETE FROM `access-groups-permissions` WHERE `group`= \"%s\"AND `permission` = \"%s\"";
        if(!$db->query($sql, $this->getGroupName(), $permission->getPermissionString())){
            $GLOBALS['FORM_RESULTS']->addErr("database", "Failed to set permission");
            return false;
        }
        return true;
    }

    public function getUserCount(){
        if(!Authorisation::isAuthorised("admin.group.usercount")){
            $GLOBALS['FORM_RESULTS']->addErr("unauthorised", "You do not have permission to count group members");
            return -1;
        }
        if($userCount>-1) return $userCount;
        $db = $GLOBALS['DATABASE'];
        $sql = "SELECT COUNT(id) AS count FROM `users` WHERE `group`=\"%s\"";
        $sqlObjects = $db->query($sql, $this->getGroupName());
        if(($o = array_pop($sqlObjects))!=null){
            $userCount = intval($o->count, 10);
        }
        return $userCount;
    }

    public function hasPermission($permission){
        if(!Authorisation::isAuthorised("admin.group.permissioncheck")){
            $GLOBALS['FORM_RESULTS']->addErr("unauthorised", "You do not have permission to permission check a group");
            return false;
        }
        $hasPermission = false;
        $db = $GLOBALS['DATABASE'];
        $sql = "SELECT * FROM `access-groups-permissions` WHERE ".
                "`permission`=\"%s\" AND `group`=\"%s\"";
        $sqlObjects = $db->query($sql, $permission, $this->getGroupName());
        if(($o = array_pop($sqlObjects))!=null){
            $hasPermission = true;
        }
        return $hasPermission;
    }


    public function delete(){
        if(!Authorisation::isAuthorised("admin.group.delete")){
            $GLOBALS['FORM_RESULTS']->addErr("unauthorised", "You do not have permission to delete a group");
            return false;
        }
        if($this->getUserCount()<0){
            $GLOBALS['FORM_RESULTS']->addErr("unauthorised", "Cannot check user count, can't delete a group");
            return false;
        }
        if($this->getUserCount()>0){
            $GLOBALS['FORM_RESULTS']->addErr("users", "Cannot delete group as it has members");
            return false;
        }

        $db = $GLOBALS['DATABASE'];
        $sql = "DELETE FROM `access-groups-permissions` WHERE `group`=\"%s\"";
        if(!$db->query($sql, $this->getGroupName())){
            $GLOBALS['FORM_RESULTS']->addErr("database", "Failed to delete permission group mapping");
            return false;
        }
        $sql = "DELETE FROM `access-groups` WHERE `group`=\"%s\"";
        if(!$db->query($sql, $this->getGroupName())){
            $GLOBALS['FORM_RESULTS']->addErr("database", "Failed to delete group");
            return false;
        }
        $GLOBALS['FORM_RESULTS']->clear();
        $GLOBALS['FORM_RESULTS']->addMsg("success", "Group '".$this->getGroupName()."' has been deleted");
        return true;
    }
    public static function getGroups($limit=-1, $offset=-1){
        if(!Authorisation::isAuthorised("admin.group.list")){
            $GLOBALS['FORM_RESULTS']->addErr("unauthorised", "You do not have permission to list groups");
            return false;
        }
        $groups = array();
        $db = $GLOBALS['DATABASE'];
        $sql = "SELECT * FROM `access-groups` ORDER BY `group` ASC";
        if($limit>-1){
            $sql .= " LIMIT %d";
            if($offset>-1){
                $sql .= " OFFSET %d";
                $sqlObjects = $db->query($sql, $limit, $offset);
            }else{
                $sqlObjects = $db->query($sql, $limit);
            }
        }else{
            $sqlObjects = $db->query($sql);
        }
        while(($o = array_pop($sqlObjects))!=null){
            $group = new Group();
            $group->setGroupName($o->group);
            $group->setDescription($o->description);
            $groups[] = $group;
        }
        return $groups;
    }
    public static function getGroup($groupName){
        if(!Authorisation::isAuthorised("admin.group.get")){
            $GLOBALS['FORM_RESULTS']->addErr("unauthorised", "You do not have permission to get a group");
            return false;
        }
        $group = null;
        $db = $GLOBALS['DATABASE'];
        $sql = "SELECT * FROM `access-groups` WHERE `group`=\"%s\"";
        $sqlObjects = $db->query($sql, $groupName);
        if(($o = array_pop($sqlObjects))!=null){
            $group = new Group();
            $group->setGroupName($o->group);
            $group->setDescription($o->description);
        }
        return $group;
    }

    public function setGroupName($groupName){
        $this->groupName = $groupName;
    }
    public function setDescription($description){
        $this->description = $description;
    }
    public function getGroupName(){
        return $this->groupName;
    }
    public function getDescription(){
        return $this->description;
    }
    }
?>
