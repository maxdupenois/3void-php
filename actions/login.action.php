<?
    $GLOBALS['FORM_RESULTS'] = new FormResults();
    $GLOBALS['FORM_RESULTS']->addValue("email", $_POST['email']);
	$userid = $_POST['email'];
	$password = $_POST['password'];
	$requested_uri = $_POST['requested_uri'];
	$query_string = $_POST['query_string'];
	if($requested_uri==NULL || $requested_uri==".".$GLOBALS['PAGE_EXTENSION']) $requested_uri = "login.".$GLOBALS['PAGE_EXTENSION'];
	if($query_string!=NULL && $query_string!="") $requested_uri .= "?".$query_string;
	if(!Authorisation::login($userid, $password)){
//		$requested_uri .= ($query_string!=NULL && $query_string!=""?"&":"");
//		$requested_uri .= "lerr=Invalid%20username%20or%20password";
        $GLOBALS['FORM_RESULTS']->addErr("invalid", "Invalid Username or Password");
	}
	$GLOBALS['FORM_RESULTS']->register();
//    if(isset($_SESSION['FORM_RESULTS'])){
//        echo "SET FORM RESULTS $requested_uri";
//        exit();
//    }
	header("Location:/".$requested_uri);
	exit();
?>