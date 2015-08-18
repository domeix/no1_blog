<!DOCTYPE html>
<html>
<head>
<title>Login to read our blogs!</title>
<link rel="stylesheet" href="stylesheet.css">
</head>

<form method="post">

	<label for="username">Username: </label>
	<input type="text" name="username" autocomplete="off"><br>
	<label for="password">Password: </label>
	<input type="password" name="password"><br>
	<input type="submit" value="login">

</form>


<?php
if(isset($_POST["username"]) && isset($_POST["password"])) {
	require_once 'loginFunc.php';
	if(login($_POST["username"],$_POST["password"])) {	//logindata correct
		header("location:main.php");
	} else {											//logindata wrong
		echo "please check your login";
	}
	
	
	
	
	
}