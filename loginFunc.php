<?php
require 'dbconnect.php';

/**checks logindata,
 * adds user-information into session-variable, if login correct
 * 
 * @param string $username
 * @param string $password
 * @return boolean
 * true, if logindata is correct
 * false, if logindata is wrong
 */
function login($username, $password) {
	$db = dbconnect();
	$result = $db->query("SELECT * FROM user WHERE username LIKE '$username' LIMIT 1;");
	$resObj = mysqli_fetch_object($result);
	
	if(!isset($resObj->password)) {
		return false;
	}
	
	if(md5($password)==$resObj->password){
		session_start();
		$_SESSION['currentUser'] = $username;
		$_SESSION['currentUserID'] = $resObj->userID;
		return true;
	} else {
		return false;
	}

}