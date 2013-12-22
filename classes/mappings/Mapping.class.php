<?php

class Mapping {
    protected $uri, $title, $is_action,
    $permission_view, $permission_edit, $permission_edit_content,
    $permission_delete;
    protected $regex = false;


    public function __construct($uri) {
        $this->uri = $uri;
    }
    public function setURI($uri) {
        $this->uri = $uri;
    }
    public function setTitle($title) {
        $this->title = $title;
    }

    public function setIsAction($is_action) {
        $this->is_action = $is_action;
    }
    public function setPermissionView($permission_view) {
        $this->permission_view = $permission_view;
    }

    public function setPermissionEdit($permission_edit) {
        $this->permission_edit = $permission_edit;
    }

    public function setPermissionEditContent($permission_edit_content) {
        $this->permission_edit_content = $permission_edit_content;
    }

    public function setPermissionDelete($permission_delete) {
        $this->permission_delete = $permission_delete;
    }

    public function setRegex($regex) {
        $this->regex = $regex;
    }
    public function isAction() {
        return $this->is_action;
    }
    public function getRegex() {
        return $this->regex;
    }
    public function isRegex() {
        return ($this->regex!=NULL);
    }
    public function getURI() {
        return $this->uri;
    }
    public function getTitle() {
        return $this->title;
    }

    public function getPermissionView() {
        return $this->permission_view;
    }
    public function getPermissionEdit() {
        return $this->permission_edit;
    }
    public function getPermissionEditContent() {
        return $this->permission_edit_content;
    }
    public function getPermissionDelete() {
        return $this->permission_delete;
    }

    protected function fillMapping() {
        $mapping = Mapping::getMappingQuery($this->uri);
        //                echo "MAPPING FROM MAPPING QUERY FOR ".$this->uri." ".($mapping == NULL?"null":"exists")."<br/>";
        //                exit();
        if($mapping != NULL) {
            $this->setTitle($mapping->getTitle());
            $this->setIsAction($mapping->isAction());
            $this->setPermissionView($mapping->getPermissionView());

            $this->setPermissionEdit($mapping->getPermissionEdit());
            $this->setPermissionEditContent($mapping->getPermissionEditContent());
            $this->setPermissionDelete($mapping->getPermissionDelete());
            $this->setRegex($mapping->getRegex());
            return true;
        }
        return false;
    }

    public static function isURIAnAction($uri) {
    //Strip extension
        $uri = Mapping::stripURIExtension($uri);
        $mapping = Mapping::getMappingQuery($uri);
        if($mapping == NULL) return false;
        return $mapping->isAction();
    }

    public static function stripURIExtension($uri) {
        $i=strrpos($uri, ".");
        return ($i==0?$uri:substr($uri, 0, $i));
    }

    private static function isURIValid($uri) {
        return preg_match("/^[a-zA-Z0-9][a-zA-Z0-9_\-\/]*$/", $uri);
    }

    private static function getMappingQuery($uri) {
        $db = $GLOBALS['DATABASE'];
        $connection = $db->getConnection();

        //Get Mapping Details
        $sql = "SELECT `title`, `permission-view` AS `view`, `permission-edit` AS `edit`, `permission-edit-content` AS `edit_content`,
		`permission-delete` AS `delete`, `is_action` FROM `mappings` WHERE uri = '$uri' AND `regular-exp` = 0";
        $result = mysql_query($sql, $connection) or die(mysql_error());
        $mapping = NULL;
        $regex = false;
        $m = mysql_fetch_object($result);
        if(!$m) {
            mysql_free_result($result);
            $sql = "SELECT `uri`, `title`, `permission-view` AS `view`, `permission-edit` AS `edit`, `permission-edit-content` AS `edit_content`,
			`permission-delete` AS `delete`, `is_action` FROM `mappings` WHERE ('$uri' REGEXP uri) AND `regular-exp` = 1";
            $result = mysql_query($sql, $connection) or die(mysql_error());
            $m = mysql_fetch_object($result);
            $regex = true;
        }
        if(is_object($m)) {
            $is_action = false;
            if(is_string($m->is_action)) {
                $is_action = ($m->is_action=="1"||strtolower($m->is_action)=="true");
            }else if(is_numeric($m->is_action)) {
                    $is_action = ($m->is_action>0);
                }else {
                    $is_action = $m->is_action;
                }
            $mapping = new Mapping($uri);
            $mapping->setTitle($m->title);
            $mapping->setIsAction($is_action);
            $mapping->setPermissionView($m->view);

            $mapping->setPermissionEdit($m->edit);
            $mapping->setPermissionEditContent($m->edit_content);
            $mapping->setPermissionDelete($m->delete);
            $mapping->setRegex(NULL);
            if($regex) 	$mapping->setRegex($m->uri);
        }
        mysql_free_result($result);
        //                echo "MAPPING HERE FOR ".$uri." ".($mapping == NULL?"null":"exists")."<br/>";
        return $mapping;
    }
    protected function edit($uri, $title, $is_action, $permission_view,
        $permission_edit, $permission_edit_content, $permission_delete, $regex=false) {
        if(!Authorisation::isAuthorised($this->getPermissionEdit())) {
            $GLOBALS['FORM_RESULTS']->addErr("unauthorised", "You do not have permission to do this");
            return false;
        }

        if($uri == NULL || $uri == "") {
            $GLOBALS['FORM_RESULTS']->addErr("uri", "No page location given");
        }
        if(!$regex&&!Mapping::isURIValid($uri)) {
            $GLOBALS['FORM_RESULTS']->addErr("uri", "Invalid uri, they must start with an alphanumeric character and all subsequent must be either alphanumeric or - or _");
        }
        if($regex) {
            try {
                preg_match($uri, "dummy string");
            }catch(Exception $e ){
                $GLOBALS['FORM_RESULTS']->addErr("uri", "Regular expression is invalid: '".$e."'");
            }
        }
        if($title == NULL || $title == "") {
            $GLOBALS['FORM_RESULTS']->addErr("title", "No page title given");
        }


        if($GLOBALS['FORM_RESULTS']->hasErrors()) {
            return false;
        }

        $db = $GLOBALS['DATABASE'];
        $connection = $db->getConnection();
        if($uri != $this->getURI()) {
        //Check to see if uri already exists
            $sql = "SELECT uri FROM `mappings` WHERE uri = '".$uri."'";
            $result = mysql_query($sql, $connection) or die(mysql_error());
            if($p = mysql_fetch_object($result)) {
                $GLOBALS['FORM_RESULTS']->addErr("uri", "Location is not unique");
            }
            mysql_free_result($result);
        }
        if($title != $this->getTitle()) {
        //Check to see if title already exists
            $sql = "SELECT uri FROM `mappings` WHERE title = '".$title."'";
            $result = mysql_query($sql, $connection) or die(mysql_error());
            if($p = mysql_fetch_object($result)) {
                $GLOBALS['FORM_RESULTS']->addErr("title", "Title is not unique");
            }
            mysql_free_result($result);
        }
        if($GLOBALS['FORM_RESULTS']->hasErrors()) {
            return false;
        }
        //Edit mapping
        $sql = "UPDATE mappings SET uri='".$uri."', title='".$title."', is_action=".($is_action?1:0).", "
            ."`permission-view` = '".$permission_view."', `permission-edit`= '".$permission_edit."', "
            ."`permission-edit-content` = '".$permission_edit_content."',
                                `permission-delete` =  '".$permission_delete."', `regular-exp`=".($regex?1:0)."  WHERE uri = '".$this->getURI()."'";
        mysql_query($sql, $connection) or die($sql."<hr/>".mysql_error());
        $this->setURI($uri);
        $this->setTitle($title);
        $this->setIsAction($is_action);
        $this->setPermissionView($permission_view);
        $this->setPermissionEdit($permission_edit);
        $this->setPermissionEditContent($permission_edit_content);
        $this->setPermissionDelete($permission_delete);
        return true;

    }

    protected static function getListing($isaction=false) {
        $listing = array();
        $userid = Authorisation::currentUserId();

        $db = $GLOBALS['DATABASE'];


        $sql = "SELECT p.uri, p.title, p.`permission-view`, "
            ."p.`permission-edit`, p.`permission-edit-content`, "
            ."p.`permission-delete` "
            ."FROM mappings AS p "
            ."WHERE p.`is_action` IS ".($isaction?"TRUE":"FALSE")." "
            ."AND ( "
            ."p.`permission-view` = \"\" "
            ."OR ( "
            ."p.`permission-view` "
            ."IN ( "
            ."SELECT agp.permission "
            ."FROM `access-groups-permissions` AS agp "
            ."LEFT JOIN users AS u ON u.group = agp.group "
            ."WHERE u.id = \"%s\" "
            .") "
            ."AND p.`permission-view` NOT "
            ."IN ( "
            ."SELECT up.permission "
            ."FROM `user-permissions` AS up "
            ."WHERE up.accept =0 "
            ."AND up.userid = \"%s\" "
            .") "
            ."OR p.`permission-view` "
            ."IN ( "
            ."SELECT up.permission "
            ."FROM `user-permissions` AS up "
            ."WHERE up.accept =1 "
            ."AND up.userid = \"%s\" "
            .") "
            .") "
            .") ORDER BY p.uri";
        $sqlobjects = $db->query($sql, $userid, $userid, $userid);
        while($p = array_pop($sqlobjects)) {
            $m = new Mapping($p->uri);
            $m->setTitle($p->title);
            $m->setIsAction($isaction);
            $m->setPermissionView($p->{"permission-view"});
            $m->setPermissionEdit($p->{"permission-edit"});
            $m->setPermissionEditContent($p->{"permission-edit-content"});
            $m->setPermissionDelete($p->{"permission-delete"});
            $listing[] = $m;
        }
        return $listing;
    }

    protected function add() {
        $uri = $this->getUri();
        $title = $this->getTitle();
        $is_action = $this->isAction();
        $permission_view = $this->getPermissionView();
        $permission_edit = $this->getPermissionEdit();
        $permission_edit_content = $this->getPermissionEditContent();
        $permission_delete = $this->getPermissionDelete();
        $regex = $this->isRegex();
        if(!Authorisation::isAuthorised("admin.mapping.create")) {
            $GLOBALS['FORM_RESULTS']->addErr("unauthorised", "You do not have permission to do this");
            return false;
        }
        if($uri == NULL || $uri == "") {
            $GLOBALS['FORM_RESULTS']->addErr("uri", "No page location given");
        }
        if(!$regex&&!Mapping::isURIValid($uri)) {
            $GLOBALS['FORM_RESULTS']->addErr("uri", "Invalid uri, they must start with an alphanumeric character and all subsequent must be either alphanumeric or - or _");
        }
        if($regex) {
            try {
                preg_match($uri, "dummy string");
            }catch(Exception $e ){
                $GLOBALS['FORM_RESULTS']->addErr("uri", "Regular expression is invalid: '".$e."'");
            }
        }
        if($title == NULL || $title == "") {
            $GLOBALS['FORM_RESULTS']->addErr("title", "No page title given");
        }


        if($GLOBALS['FORM_RESULTS']->hasErrors()) {
            return false;
        }
        $db = $GLOBALS['DATABASE'];
        $connection = $db->getConnection();

        //Check to see if uri already exists
        $sql = "SELECT uri FROM `mappings` WHERE uri = '".$uri."'";
        $result = mysql_query($sql, $connection) or die(mysql_error());
        if($p = mysql_fetch_object($result)) {
            $GLOBALS['FORM_RESULTS']->addErr("uri", "Location is not unique");
        }
        mysql_free_result($result);

        //Check to see if title already exists
        $sql = "SELECT uri FROM `mappings` WHERE title = '".$title."'";
        $result = mysql_query($sql, $connection) or die(mysql_error());
        if($p = mysql_fetch_object($result)) {
            $GLOBALS['FORM_RESULTS']->addErr("title", "Title is not unique");
        }
        mysql_free_result($result);

        if($GLOBALS['FORM_RESULTS']->hasErrors()) {
            return false;
        }

        //Add mapping
        $sql = "INSERT INTO mappings (uri, title, is_action, `permission-view`, `permission-edit`, "
            ."`permission-edit-content`, `permission-delete`, `regular-exp`) "
            ."VALUES ('".$uri."', '".$title."', ".($is_action?1:0).", '".$permission_view."', '".$permission_edit."', "
            ."'".$permission_edit_content."', '".$permission_delete."', ".($regex?1:0).")";
        mysql_query($sql, $connection) or die($sql."<hr/>".mysql_error());
        return true;
    }

    protected function delete() {
        if(!Authorisation::isAuthorised($this->getPermissionDelete())) {
            $GLOBALS['FORM_RESULTS']->addErr("unauthorised", "You do not have permission to do this");
            return false;
        }
        $db = $GLOBALS['DATABASE'];
        $connection = $db->getConnection();
        $sql = "DELETE FROM `mappings` WHERE `uri`='".$this->getURI()."'";
        mysql_query($sql, $connection) or die($sql."<hr/>".mysql_error());
        return true;
    }
}

?>