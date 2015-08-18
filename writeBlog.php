<!DOCTYPE html>
<html>
<head>
<title>Write your own blog!</title>
<base href="//localhost/No1_Blog/">
<link rel="stylesheet" href="stylesheet.css">
</head>

<?php
session_start();
if(!isset($_SESSION['currentUser'])) {
	header('location: .');
} 
require_once 'dbconnect.php';?>



<form method="post">
	<input type="text" style="width: 600px; margin-bottom:5px;" name="heading" placeholder="heading"><br>
	<textarea name="text" style="width: 600px; height: 300px; margin-bottom:5px;" placeholder="enter your text here"></textarea><br>
	<br>
	<input type="submit" value="publish">
</form>

<?php

if(isset($_POST['text']) && isset($_POST['heading'])) {
	$db = dbconnect();
	$userID = $_SESSION['currentUserID'];
	$heading = $_POST['heading'];
	$text = $_POST['text'];
	$success = $db->query("INSERT INTO blogentries (heading, text, userID)  VALUES ('$heading', '$text', '$userID');");
	
	if($success){
		echo "publishing successful
				<br>
				<a href='.'>back</a>
				";
	} else {
		echo "publishing unsuccessful";
	}
	
}