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
	
	
	//IF ABFRAGE HIER
	session_start();
	$_SESSION['currentUser'] = $_POST['username'];
	$_SESSION['currentUserID'] = $resObj->userID;
	
	
	return (md5($password)==$resObj->password);
}