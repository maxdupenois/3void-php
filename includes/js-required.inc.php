<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

date_default_timezone_set('EUROPE/LONDON');

$GLOBALS['DOC_ROOT'] = str_replace("/includes", "", dirname(__FILE__));
$GLOBALS['SITE_ROOT'] = $DOC_ROOT; //Applicable for sites under folders otherwise just the doc root
$GLOBALS['CLASS_FOLDER']= $SITE_ROOT."/classes";
$GLOBALS['TEMPLATES_FOLDER']= $SITE_ROOT."/templates";
$GLOBALS['TEMPORARY_CONTENTS']= $TEMPLATES_FOLDER."/temporary-contents";
$GLOBALS['ACTIONS_FOLDER'] = $SITE_ROOT."/actions";
$GLOBALS['UPLOADS_BASE']= "uploads";
$GLOBALS['UPLOADS_FOLDER']= $GLOBALS['SITE_ROOT']."/".$GLOBALS['UPLOADS_BASE'];
$GLOBALS['DOMAIN'] = "http://3void.com";

$GLOBALS['PAGE_EXTENSION'] = "html";
$GLOBALS['ACTION_EXTENSION'] = "html";

$GLOBALS['TINY_MCE_LOCATION'] = "/js/tiny_mce/tiny_mce_gzip.js";
$GLOBALS['TINY_MCE_TEXT_CSS'] = "/css/text.css";

$GLOBALS['DEBUG'] = false;


//Strip slashes from magic quotes if it's on.
if (get_magic_quotes_gpc()) {
    $process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
    while (list($key, $val) = each($process)) {
        foreach ($val as $k => $v) {
            unset($process[$key][$k]);
            if (is_array($v)) {
                $process[$key][stripslashes($k)] = $v;
                $process[] = &$process[$key][stripslashes($k)];
            } else {
                $process[$key][stripslashes($k)] = stripslashes($v);
            }
        }
    }
    unset($process);
}

if(!is_dir($CLASS_FOLDER)){
	die("Class folder '".$CLASS_FOLDER."' not found");
}
//Load Classes
require_once($GLOBALS['CLASS_FOLDER']."/ClassLoader.class.php");
ClassLoader::setRootClassFolder($GLOBALS['CLASS_FOLDER']);
import("utils.Database");
import("utils.FormResults");
import("user.User");
import("authorisation.Authorisation");
import("mappings.Mapping");
import("mappings.actions.*");
import("mappings.pages.*");
import("mappings.pages.templates.*");
//Start Session
session_start();

//Connect to database
$GLOBALS['DATABASE'] = new Database('localhost', 'voidcom1_3void', 'voidcom1_3void','(x/2<:qK#V)X');
$DATABASE->connect();
//ftp: @WEQHDBK
?>