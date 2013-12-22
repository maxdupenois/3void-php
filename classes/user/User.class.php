<?php

class User{
	protected $userid, $username;
	
	
	public function __construct($userid, $username){
		$this->userid = $userid;
		$this->username = $username;
	}
	
	
	public function getUserId(){
		return $this->userid;
	}
	public function getUsername(){
		return $this->username;
	}
	public static function checkUser($userid, $password){
		$db = $GLOBALS['DATABASE'];
		$connection = $db->getConnection();

		//Get Page Details
		$sql = "SELECT `username` FROM `users` WHERE id = '$userid'";
		$sql .= " AND password = MD5('$password')";
		$result = mysql_query($sql, $connection) or die(mysql_error());

		$user = NULL;
		if($u = mysql_fetch_object($result)){
			$user =  new User($userid, $u->username);
		}
		mysql_free_result($result);
		return $user;
	}

	public static function getUser($userid){
		$db = $GLOBALS['DATABASE'];
		$connection = $db->getConnection();
		
		//Get Page Details
		$sql = "SELECT `username` FROM `users` WHERE id = '$userid'";
		$result = mysql_query($sql, $connection) or die(mysql_error());

		$user = NULL;
		if($u = mysql_fetch_object($result)){
			$user =  new User($userid, $u->username);
		}
		mysql_free_result($result);
		return $user;
	}
}

?>