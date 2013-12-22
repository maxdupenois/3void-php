<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
date_default_timezone_set('Europe/London');

/*Setup Globals*/

$GLOBALS['RENDER_START'] = microtime(true);

$GLOBALS['DOC_ROOT'] = str_replace("/includes", "", dirname(__FILE__));

$GLOBALS['SITE_ROOT'] = $GLOBALS['DOC_ROOT']; //Applicable for sites under folders otherwise just the doc root

$GLOBALS['DOMAIN'] = "http://3void.com";


//Folder Names

$GLOBALS['FOLDER_JS'] = "js";

$GLOBALS['FOLDER_CSS'] = "css";

$GLOBALS['FOLDER_IMAGES'] = "images";

$GLOBALS['FOLDER_ADMIN'] = "admin";

$GLOBALS['FOLDER_CLASS'] = "classes";

$GLOBALS['FOLDER_INCLUDES'] = "includes";

$GLOBALS['FOLDER_TEMPLATES'] = "templates";

$GLOBALS['FOLDER_TEMP'] = "templates/temporary-contents";

$GLOBALS['FOLDER_ACTIONS'] = "actions";

$GLOBALS['FOLDER_UPLOADS'] = "uploads";



//Get Shortcuts

require_once($GLOBALS['SITE_ROOT']."/".$GLOBALS['FOLDER_INCLUDES']."/shortcuts.inc.php");



//Phase These Out

$GLOBALS['CLASS_FOLDER']= clasz();

$GLOBALS['TEMPLATES_FOLDER']= tmplt();

$GLOBALS['TEMPORARY_CONTENTS']= temp();

$GLOBALS['ACTIONS_FOLDER'] = act();

$GLOBALS['UPLOADS_BASE']= $GLOBALS['FOLDER_UPLOADS'];

$GLOBALS['UPLOADS_FOLDER']= p_uploads();



//Extensions

$GLOBALS['PAGE_EXTENSION'] = "html";

$GLOBALS['ACTION_EXTENSION'] = "html";



//CSS Locations

$GLOBALS['TINY_MCE_LOCATION'] = "/js/tiny_mce/tiny_mce_gzip.js";

$GLOBALS['TINY_MCE_TEXT_CSS'] = "/css/text.css";



//Debug

$GLOBALS['DEBUG'] = false;



//Check to see if file actually exists if so give preference to it
//This is here instead of in generate-page to avoid any unnecessary work
require_once(inc("loadfile.inc.php"));





//Strip slashes from magic quotes if it's on.

require_once(inc("magicquotes.inc.php"));


//Load misc functions
require_once(inc("miscfunctions.inc.php"));

//Start Session

session_start();





//Setup Classes, importing defaults

require_once(inc("class.inc.php"));



//Connect to database

require_once(inc("db.inc.php"));





?>