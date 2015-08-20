<?php
require_once 'dbconnect.php';

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
	$oDB = new DBconnect();
	$result = $oDB->query("SELECT * FROM user WHERE username LIKE '$username' AND active;");
	$resObj = mysqli_fetch_object($result);
	
	if(!isset($resObj->password)) { //DB-connection error
		return false;
	}
	
	echo "<br>".$resObj->password."<br>".md5($password)."<br>";
	
	if(md5($password)==$resObj->password){
		session_start();
		$_SESSION['currentUser'] = $username;
		$_SESSION['currentUserID'] = $resObj->userID;
		return true;
	} else {
		return false;
	}

}