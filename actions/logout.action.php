<?
$current_uri = $_GET['curi'].".".$GLOBALS['PAGE_EXTENSION'];
if($current_uri==NULL || $current_uri=="")$current_uri = "login.".$GLOBALS['PAGE_EXTENSION'];
Authorisation::logout();
header("Location:/".$current_uri);
exit();
?>