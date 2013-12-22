<?php
import("menus.*");
import("files.File");
class Page extends Mapping {
    private $template, $contents, $menus;

    public function __construct($uri) {
        parent::__construct($uri);
        $this->setIsAction(false);
    }

    public function setTemplate($template) {
        $this->template = $template;
    }




    public function setContents($contents) {
        $this->contents = $contents;

    }
    public function setMenus($menus) {
        $this->menus = $menus;

    }
    public function getTemplate() {
        return $this->template;
    }
    public function getContents() {
        return $this->contents;
    }
    public function getMenus() {
        return $this->menus;
    }
    public static function getPage($uri, $purpose='view') {
        $db = $GLOBALS['DATABASE'];
        $connection = $db->getConnection();
        //Strip extension
        $uri = Mapping::stripURIExtension($uri);
        $page = new Page($uri);

        $pageFilled = $page->fillMapping();

        if(!$pageFilled && $purpose == 'view') {
            header("HTTP/1.0 404 Not Found");
            //Possible loop if 404 not found so:
            if($uri=="err/404") die("404 Page Not Found");
            return Page::getPage("err/404.".$GLOBALS['PAGE_EXTENSION']);
        }else if(!$pageFilled && $purpose != 'view') {
                return NULL;
            }


        if(!Authorisation::authoriseMapping($page, $purpose)) {
            if($purpose == "view") {
                if($uri=="err/401") die("Auth required for 401, this is wrong.");
                $GLOBALS['REQUESTED_URI'] = $uri;
                return Page::getPage("err/401.".$GLOBALS['PAGE_EXTENSION']);
            }else {
                return NULL;
            }
        }



        //Get Template

        $uri = ($page->isRegex()?$page->getRegex():$uri);

        $sql = "SELECT template_name FROM `mappings-templates` WHERE uri = '$uri'";
        $result = mysql_query($sql, $connection) or die(mysql_error());
        $template_name = "";
        if($t = mysql_fetch_object($result)) $template_name = $t->template_name;
        mysql_free_result($result);



        $template = Template::getTemplate($template_name);
        $keys = $template->getKeys();

        //Get Page Content
        $sql = "SELECT key_name, content FROM `pages-contentkeys` WHERE page_uri = '$uri'";
        $result = mysql_query($sql, $connection) or die(mysql_error());

        $contents = array();
        while($c = mysql_fetch_object($result)) {
            $content = $c->content;
			/*echo "\n------------------------------\n";
			echo "\n------------------------------\n";
			echo "\n------------------------------\n";
			echo $c->key_name;
			echo "\n------------------------------\n";
			echo $content;
			//$content = preg_replace("/\\\\{3}\\\"/", "", $content); // \\\" -> \"
			//$content = preg_replace("/[^\\\\]\\\\[^\\\"]/", "", $content); // \" -> "
			//$content = preg_replace("/[^\\\\]\\\\[^\\\\]/", "", $content); // \\ -> \
			//echo "\n==============================\n";
			//echo $content;
			echo "\n------------------------------\n\n\n\n\n";*/
            //Must be an older version but i may need stripslashes at some point
            //if($c->key_name=="javascript") echo $content;
            //$content = stripslashes($content);
			/*if($uri == "admin/test" && $c->key_name == "main"){
				echo "-------------------------------------\n";
				echo "Content: \n".$content;
				echo "\n-------------------------------------\n";
				echo "Stripped: \n".$content_stripped;
				echo "\n-------------------------------------\n";
				$content_stripped = $content;
			}*/

            if(isset($keys[$c->key_name]))
                $contents[$c->key_name] = new PageContentItem($keys[$c->key_name], $content );
        }
        mysql_free_result($result);
        //exit();

        //Get page Menus
        $sql = "SELECT `menu_name`, `key` FROM `pages-menus` WHERE page_uri = '$uri'";
        $result = mysql_query($sql, $connection) or die($sql."<hr/>".mysql_error());
        $keyToMenuName = array();
        while($m = mysql_fetch_object($result)) {
            $keyToMenuName[$m->key] = $m->menu_name;
        }
        mysql_free_result($result);

        $keyToMenu = array();
        foreach($keyToMenuName as $key => $name) {
            $menu = Menu::getMenu($name, $purpose);
            if($menu != NULL)$keyToMenu[$key] = $menu;
        }

        $page->setTemplate($template);
        $page->setContents($contents);
        $page->setMenus($keyToMenu);

        return $page;
    }

    public function edit($uri, $title, $permission_view,
        $permission_edit, $permission_edit_content, $permission_delete, $template) {
        if(!Authorisation::isAuthorised($this->getPermissionEdit())) {
            $GLOBALS['FORM_RESULTS']->addErr("unauthorised", "You do not have permission to do this");
            return false;
        }

        if($template == NULL || $template == "") {
            $GLOBALS['FORM_RESULTS']->addErr("template", "No page template given");
            return false;
        }

        if(!parent::edit($uri, $title, false, $permission_view,
        $permission_edit, $permission_edit_content, $permission_delete))return false;

        if($template != $this->getTemplate()->getName()) {
            $db = $GLOBALS['DATABASE'];
            $connection = $db->getConnection();
            //Clear the old data
            $sql = "DELETE FROM `pages-contentkeys` WHERE `page_uri`='".$this->getURI()."'";
            mysql_query($sql, $connection) or die($sql."<hr/>".mysql_error());

            $sql = "DELETE FROM `pages-menus` WHERE `page_uri`='".$this->getURI()."'";
            mysql_query($sql, $connection) or die($sql."<hr/>".mysql_error());

            $sql = "DELETE FROM `mappings-templates` WHERE `uri`='".$this->getURI()."'";
            mysql_query($sql, $connection) or die($sql."<hr/>".mysql_error());
            //Just in case the update has been unsuccessful we'll remove it from both uri's
            if($uri != $this->getURI()) {
                $sql = "DELETE FROM `pages-contentkeys` WHERE `page_uri`='".$uri."'";
                mysql_query($sql, $connection) or die($sql."<hr/>".mysql_error());

                $sql = "DELETE FROM `pages-menus` WHERE `page_uri`='".$uri."'";
                mysql_query($sql, $connection) or die($sql."<hr/>".mysql_error());

                $sql = "DELETE FROM `mappings-templates` WHERE `uri`='".$uri."'";
                mysql_query($sql, $connection) or die($sql."<hr/>".mysql_error());
            }
            $sql = "INSERT INTO `mappings-templates` (uri, template_name) "
                ."VALUES ('".$uri."', '$template')";
            mysql_query($sql, $connection) or die($sql."<hr/>".mysql_error());

        }
        $GLOBALS['FORM_RESULTS']->clear();
        $GLOBALS['FORM_RESULTS']->addMsg("success", "Page '".$uri."' has been edited");
        return true;
    }
    public function add() {
        if(!Authorisation::isAuthorised("admin.page.create")) {
            $GLOBALS['FORM_RESULTS']->addErr("unauthorised", "You do not have permission to do this");
            return false;
        }
        if($this->getTemplate() == NULL) {
            $GLOBALS['FORM_RESULTS']->addErr("template", "No page template given");
            return false;
        }

        if(!parent::add())return false;


        $db = $GLOBALS['DATABASE'];
        $connection = $db->getConnection();


        $sql = "INSERT INTO `mappings-templates` (uri, template_name) "
            ."VALUES ('".$this->getUri()."', '".$this->getTemplate()->getName()."')";
        mysql_query($sql, $connection) or die($sql."<hr/>".mysql_error());

        $GLOBALS['FORM_RESULTS']->clear();
        $GLOBALS['FORM_RESULTS']->addMsg("success", "Page '".$this->getUri()."' has been added");
        return true;
    }

    public function editContent($key, $value) {
        if(!Authorisation::isAuthorised($this->getPermissionEditContent())) {
            $GLOBALS['FORM_RESULTS']->addErr("unauthorised", "You do not have permission to do this");
            return false;
        }


        $db = $GLOBALS['DATABASE'];
        $connection = $db->getConnection();

        $sql = "DELETE FROM `pages-contentkeys` WHERE `page_uri`='".$this->getURI()."' AND `key_name`='".$key->getName()."'";
        mysql_query($sql, $connection) or die($sql."<hr/>".mysql_error());


        //Replace anything that's not the regex replace itself
        $value = preg_replace("/([^1])\\/\\%\\%textarea/", "$1/textarea", $value);
        $value = mysql_real_escape_string($value);
        //echo "INSERTING: '".$value."' for key ".$key->getName()."\n";
        //$escaped = mysql_real_escape_string($value);
        $sql = "INSERT INTO `pages-contentkeys` (page_uri, key_name, content) "
            ."VALUES ('".$this->getURI()."','".$key->getName()."', '".$value."')";
        mysql_query($sql, $connection) or die($sql."<hr/>".mysql_error());

		/*if($key->getName()=="main"){
			echo "\n------------------------------\n";
			echo "Standard: ".$value;
			echo "\n==============================\n";
			echo "Escaped: ".$escaped;
			echo "\n==============================\n";
			echo "Stripped: ".stripslashes($escaped);
			echo "\n------------------------------\n\n\n\n\n";
			exit();
		}*/

        return true;
    }


    public static function pageListingByFolder() {
        return File::listingByFolder(Page::pageListing());
    }


    public static function pageListing($userid=NULL) {
        $listing = array();
        if($userid == NULL) {
            $userid = $_SESSION['userid'];
            if($userid == NULL || $userid == "")return $listing;
        }
        $db = $GLOBALS['DATABASE'];
        $connection = $db->getConnection();


        $sql = "SELECT p.uri, p.title, p.`permission-view` AS p_view, "
            ."p.`permission-edit` AS p_edit, p.`permission-edit-content` AS p_edit_c, "
            ."p.`permission-delete` AS p_delete "
            ."FROM mappings AS p "
            ."WHERE p.`is_action` IS FALSE "
            ."AND ( "
            ."p.`permission-view` = \"\" "
            ."OR ( "
            ."p.`permission-view` "
            ."IN ( "
            ."SELECT agp.permission "
            ."FROM `access-groups-permissions` AS agp "
            ."LEFT JOIN users AS u ON u.group = agp.group "
            ."WHERE u.id = '$userid' "
            .") "
            ."AND p.`permission-view` NOT "
            ."IN ( "
            ."SELECT up.permission "
            ."FROM `user-permissions` AS up "
            ."WHERE up.accept =0 "
            ."AND up.userid = '$userid' "
            .") "
            ."OR p.`permission-view` "
            ."IN ( "
            ."SELECT up.permission "
            ."FROM `user-permissions` AS up "
            ."WHERE up.accept =1 "
            ."AND up.userid = '$userid' "
            .") "
            .") "
            .") ORDER BY p.uri";
        $result = mysql_query($sql, $connection) or die($sql."<hr/>".mysql_error());
        while($p = mysql_fetch_object($result)) {
            $m = new Mapping($p->uri);
            $m->setTitle($p->title);
            $m->setIsAction(false);
            $m->setPermissionView($p->p_view);
            $m->setPermissionEdit($p->p_edit);
            $m->setPermissionEditContent($p->p_edit_c);
            $m->setPermissionDelete($p->p_delete);
            $listing[] = $m;
        }
        return $listing;

    }

    public function delete() {
        $db = $GLOBALS['DATABASE'];
        $connection = $db->getConnection();

        if(!Authorisation::isAuthorised($this->getPermissionDelete())) {
            $GLOBALS['FORM_RESULTS']->addErr("unauthorised", "You do not have permission to do this");
            return false;
        }
        $sql = "DELETE FROM `pages-contentkeys` WHERE `page_uri`='".$this->getURI()."'";
        mysql_query($sql, $connection) or die($sql."<hr/>".mysql_error());

        $sql = "DELETE FROM `pages-menus` WHERE `page_uri`='".$this->getURI()."'";
        mysql_query($sql, $connection) or die($sql."<hr/>".mysql_error());

        $sql = "DELETE FROM `mappings-templates` WHERE `uri`='".$this->getURI()."'";
        mysql_query($sql, $connection) or die($sql."<hr/>".mysql_error());

        return parent::delete();

    }


    public static function pageNotFound() {
        header("HTTP/1.0 404 Not Found");
        //        header("Location:/err/404.".$GLOBALS['PAGE_EXTENSION']);
        $page = Page::getPage("err/404.".$GLOBALS['PAGE_EXTENSION']);
        $GLOBALS['CURRENT_PAGE'] = $page;
        $contents = $page->getContents();
        $template = $page->getTemplate();
        $menus = $page->getMenus();
        require_once($GLOBALS['TEMPLATES_FOLDER'].'/'.$template->getLocation());
        exit();
    }
    public static function unauthorised($uri) {
        if($uri=="err/401") die("Auth required for 401, this is wrong.");
        $current_user = NULL;
        if(isset($_SESSION['userid']) && $_SESSION['userid'] != NULL && $_SESSION['userid']!= "")
            $current_user = User::getUser($_SESSION['userid']);
        $GLOBALS['REQUESTED_URI'] = $uri;
        header("HTTP/1.0 401 Unauthorized");
        $page = Page::getPage("err/401.".$GLOBALS['PAGE_EXTENSION']);
        $GLOBALS['CURRENT_PAGE'] = $page;
        $contents = $page->getContents();
        $template = $page->getTemplate();
        $menus = $page->getMenus();
        require_once($GLOBALS['TEMPLATES_FOLDER'].'/'.$template->getLocation());
        exit();
    }
    public static function forbidden() {
        header("HTTP/1.0 403 Forbidden");
        //        header("Location:/err/403.".$GLOBALS['PAGE_EXTENSION']);
        $page = Page::getPage("err/403.".$GLOBALS['PAGE_EXTENSION']);
        $GLOBALS['CURRENT_PAGE'] = $page;
        $contents = $page->getContents();
        $template = $page->getTemplate();
        $menus = $page->getMenus();
        require_once($GLOBALS['TEMPLATES_FOLDER'].'/'.$template->getLocation());
        exit();
    }
    public static function internalServerError($err="") {
        header("HTTP/1.0 500 Internal Server Error");
        //        $queryStr = ($err!=""?"?err=".urlencode($err):"");
        //        header("Location:/err/500.".$GLOBALS['PAGE_EXTENSION'].$queryStr);
        $page = Page::getPage("err/500.".$GLOBALS['PAGE_EXTENSION']);
        $GLOBALS['CURRENT_PAGE'] = $page;
        $contents = $page->getContents();
        $template = $page->getTemplate();
        $menus = $page->getMenus();
        $GLOBALS['INTERNAL_SERVER_ERROR'] = $err;
        require_once($GLOBALS['TEMPLATES_FOLDER'].'/'.$template->getLocation());
        exit();
    }

}

?>