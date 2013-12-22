<?php
//Connect to database
import("utils.Database");
$db_user
  =  "voidcom1_3void";
$db_pass
  =  "(x/2<:qK#V)X";
$db_server
  =  "localhost" ;
$db_name
  =  "voidcom1_3void";

$GLOBALS['DATABASE'] = new Database($db_server, $db_name, $db_user,$db_pass);
$GLOBALS['DATABASE']->connect();

function db(){
    return $GLOBALS['DATABASE'];
}

//ftp: @WEQHDBK
?>
