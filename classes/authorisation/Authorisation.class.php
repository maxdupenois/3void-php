<?php

class Authorisation{

    /* User Permission Listing



    */

    private static $currentUser;

    private static function setUnauthHeaders(){

        /*Using sessions so realm not required
        //header('WWW-Authenticate: Basic realm="Standard Security Realm"');
        */

        header("HTTP/1.0 401 Unauthorized");

    }



    public static function authoriseMapping($mapping, $purpose){

        //If no purpose fail

        if($purpose != 'view' && $purpose != 'edit' &&

            $purpose != 'edit_content' && $purpose != 'delete'){

            Authorisation::setUnauthHeaders();

            return false;

        }



        //If no auth accept

        if($purpose == 'view' && $mapping->getPermissionView() == "") return true;

        if($purpose == 'edit' && $mapping->getPermissionEdit() == "") return true;

        if($purpose == 'edit_content' && $mapping->getPermissionEditContent() == "") return true;

        if($purpose == 'delete' && $mapping->getPermissionDelete() == "") return true;





        //If locked fail

        if(($purpose == 'view' && $mapping->getPermissionView() == "locked") ||

            ($purpose == 'edit' && $mapping->getPermissionEdit() == "locked") ||

            ($purpose == 'edit_content' && $mapping->getPermissionEditContent() == "locked")||

            ($purpose == 'delete' && $mapping->getPermissionDelete() == "locked")){

            if($purpose == 'view') Authorisation::setUnauthHeaders();

            return false;

        }



        $authorised = false;

        if($purpose == 'view') $authorised = Authorisation::isAuthorised($mapping->getPermissionView());

        if($purpose == 'edit') $authorised = Authorisation::isAuthorised($mapping->getPermissionEdit());

        if($purpose == 'edit_content') $authorised = Authorisation::isAuthorised($mapping->getPermissionEditContent());

        if($purpose == 'delete') $authorised = Authorisation::isAuthorised($mapping->getPermissionDelete());

        if(!$authorised && $purpose == 'view') Authorisation::setUnauthHeaders();

        return $authorised;

    }



    public static function authoriseMenu($menu, $purpose){

        //If no purpose fail



        if($purpose != 'view' && $purpose != 'edit' &&

            $purpose != 'edit_content' && $purpose != 'delete'){

            return false;

        }



        //If no auth accept

        if($purpose == 'view' && $menu->getPermissionView() == "") return true;

        if($purpose == 'edit' && $menu->getPermissionEdit() == "") return true;

        if($purpose == 'delete' && $menu->getPermissionDelete() == "") return true;





        //If locked fail

        if(($purpose == 'view' && $menu->getPermissionView() == "locked") ||

            ($purpose == 'edit' && $menu->getPermissionEdit() == "locked") ||

            ($purpose == 'delete' && $menu->getPermissionDelete() == "locked")){

            return false;

        }





        $authorised = false;

        if($purpose == 'view') $authorised = Authorisation::isAuthorised($menu->getPermissionView());

        if($purpose == 'edit') $authorised = Authorisation::isAuthorised($menu->getPermissionEdit());

        if($purpose == 'delete') $authorised = Authorisation::isAuthorised($menu->getPermissionDelete());

        return $authorised;

    }



    public static function authoriseAction($action, $purpose){

        //If no purpose fail



        if($purpose != 'use' && $purpose != 'edit' && $purpose != 'delete'){

            return false;

        }



        //If no auth accept

        if($purpose == 'use' && $action->getPermissionUse() == "") return true;

        if($purpose == 'edit' && $action->getPermissionEdit() == "") return true;

        if($purpose == 'delete' && $action->getPermissionDelete() == "") return true;





        //If locked fail

        if(($purpose == 'use' && $action->getPermissionUse() == "locked") ||

            ($purpose == 'edit' && $action->getPermissionEdit() == "locked") ||

            ($purpose == 'delete' && $action->getPermissionDelete() == "locked")){

            return false;

        }





        $authorised = false;

        if($purpose == 'use') $authorised = Authorisation::isAuthorised($action->getPermissionUse());

        if($purpose == 'edit') $authorised = Authorisation::isAuthorised($action->getPermissionEdit());

        if($purpose == 'delete') $authorised = Authorisation::isAuthorised($action->getPermissionDelete());

        return $authorised;

    }



    public static function authoriseFile($file, $purpose){

        //If no purpose fail



        if($purpose != 'view' && $purpose != 'edit' && $purpose != 'delete'){

            return false;

        }



        //If no auth accept

        if($purpose == 'view' && $file->getPermissionView() == "") return true;

        if($purpose == 'edit' && $file->getPermissionEdit() == "") return true;

        if($purpose == 'delete' && $file->getPermissionDelete() == "") return true;





        //If locked fail

        if(($purpose == 'view' && $file->getPermissionView() == "locked") ||

            ($purpose == 'edit' && $file->getPermissionEdit() == "locked") ||

            ($purpose == 'delete' && $file->getPermissionDelete() == "locked")){

            return false;

        }





        $authorised = false;

        if($purpose == 'view') $authorised = Authorisation::isAuthorised($file->getPermissionView());

        if($purpose == 'edit') $authorised = Authorisation::isAuthorised($file->getPermissionEdit());

        if($purpose == 'delete') $authorised = Authorisation::isAuthorised($file->getPermissionDelete());

        return $authorised;

    }

    public static function currentUser(){

        if(!isset($_SESSION['userid'])|| $_SESSION['userid'] == NULL ||
            $_SESSION['userid'] == ""){
            Authorisation::$currentUser = null;
            return null;
         }
        if(Authorisation::$currentUser == null){
            Authorisation::$currentUser = User::getUser($_SESSION['userid']);
        }
        return Authorisation::$currentUser;
    }

    public static function currentUserId(){

        if(!isset($_SESSION['userid']))return null;

        $userid = $_SESSION['userid'];

        if($userid == NULL || $userid == "")return null;

        return $userid;

    }



    public static function isAuthorised($permission, $userid=NULL){



        if($userid == NULL){

            //if(!session_is_registered($_SESSION['userid'])) return false;

            if(!isset($_SESSION['userid']))return false;

            $userid = $_SESSION['userid'];

            if($userid == NULL || $userid == "")return false;

        }

        $db = $GLOBALS['DATABASE'];

        $connection = $db->getConnection();



        if($permission=="") return true;

        if($permission=="locked") return false;

        $accept = false;



        //See if we have an override

        $override = false;

        $sql = 	"SELECT up.accept FROM `user-permissions` AS up WHERE up.permission = '$permission' AND userid= '$userid'";

        $result = mysql_query($sql, $connection) or die(mysql_error());



        if($ov = mysql_fetch_object($result)){

            $override = true;

            $accept = $ov->accept;

        }

        mysql_free_result($result);

        if($override) return $accept;



        //If no override see if it is in the group permissions

        $sql = 	"SELECT agp.permission "

        ."FROM `access-groups-permissions` AS agp "

        ."LEFT JOIN users AS u ON u.group = agp.group "

        ."WHERE u.id = '$userid' "

        ."AND agp.permission = '$permission'";

        $result = mysql_query($sql, $connection) or die(mysql_error());



        if($ac = mysql_fetch_object($result)){

            $accept = true;

        }

        mysql_free_result($result);



        return $accept;

    }



    public static function login($userid, $password){

        if(User::checkUser($userid, $password)==NULL) return false;

        session_unregister('userid');

        session_register('userid');

        $_SESSION['userid'] = $userid;

        return true;

    }

    public static function logout(){

        session_unregister('userid');

        session_destroy();

    }



    public static function permissionListing(){

        $listing = array();

        $userid = $_SESSION['userid'];

        if($userid == NULL || $userid == "")return $listing;

        $db = $GLOBALS['DATABASE'];

        $connection = $db->getConnection();





        $sql = 	"("

        ."SELECT agp.permission, p.description "

        ."FROM `access-groups-permissions` AS agp "

        ."LEFT JOIN users AS u ON u.group = agp.group "

        ."LEFT JOIN `access-permissions` AS p ON p.permission = agp.permission "

        ."WHERE u.id = '$userid' "

        ."AND agp.permission NOT "

        ."IN ( "

        ."SELECT up.permission "

        ."FROM `user-permissions` AS up "

        ."WHERE up.userid = '$userid' "

        ."AND up.accept =0 "

        .") "

        .") "

        ."UNION ( "

        ."SELECT up.permission, p.description "

        ."FROM `user-permissions` AS up "

        ."LEFT JOIN `access-permissions` AS p ON p.permission = up.permission "

        ."WHERE up.userid = '$userid' "

        ."AND up.accept =1 "

        .")"

        ."UNION ( "

        ."SELECT p.permission, p.description "

        ."FROM `access-permissions` AS p "

        ."WHERE p.permission = '' "

        .") ORDER BY permission ASC";

        $result = mysql_query($sql, $connection) or die($sql."<hr/>".mysql_error());

        while($p = mysql_fetch_object($result)){

            $listing[] = $p;

        }

        return $listing;

    }



}

?>
