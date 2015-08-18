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
require_once 'dbconnect.php';
$oDB = new DBconnect();

$edit = false;
$blogEntryID = NULL; 
if(isset($_GET['blogEntryID'])) {
	$blogEntryID = $_GET['blogEntryID'];
	$edit = true;

}

if(isset($_POST['text']) && isset($_POST['heading'])) {
	$success = $oDB->saveBlogEntry($_POST['heading'], $_POST['text'], $edit, $blogEntryID);
	
}	
if($edit) {
	$blogEntry = $oDB->getBlogEntry($blogEntryID);
}
?>


<form method="post">
	<input type="text" style="width: 600px; margin-bottom:5px;" name="heading" placeholder="heading" autocomplete="off"
		<?php if($edit){ echo 'value="'.$blogEntry->heading.'"';}?>
	><br>
	<textarea name="text" style="width: 600px; height: 300px; margin-bottom:5px;" placeholder="enter your text here"
		><?php if($edit){ echo $blogEntry->text;}?></textarea><br>
	<br>
	<input type="submit" value="publish">
</form>

<?php

if(isset($_POST['text']) && isset($_POST['heading'])) {
	if($success){
		echo "publishing successful
				<br>
				<a href='.'>back</a>
				";
	} else {
		echo "publishing unsuccessful";
	}
	
}