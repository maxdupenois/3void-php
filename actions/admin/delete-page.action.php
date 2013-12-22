<?php
$GLOBALS['FORM_RESULTS'] = new FormResults();

$page = Page::getPage($_GET['page'], "delete");
if($page!=null&&$page->delete()){
	$GLOBALS['FORM_RESULTS']->addMsg("success","Deleted '".$_GET['page']."'");
}
	

$GLOBALS['FORM_RESULTS']->register();
header("Location:/admin/pages/index.".$GLOBALS['PAGE_EXTENSION']);
exit();



?>