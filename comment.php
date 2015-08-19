<!DOCTYPE html>
<html>
<head>
<title>Comment this blog</title>
<base href="//localhost/No1_Blog/">
<link rel="stylesheet" href="stylesheet.css">
</head>

<div class="writingarea">
<?php
session_start();
if(!isset($_SESSION['currentUser'])) {
	header('location: .');
}
require_once 'dbconnect.php';
$oDB = new DBconnect();



//check, wether the comment is created or edited
//Error, if there is sth not defined
if(isset($_GET['blogEntryID'])){
	$blogEntryID = $_GET['blogEntryID'];
	$edit = false;
	$commentID = NULL;
}
if(isset($_GET['commentID'])) {
	$commentID = $_GET['commentID'];
	$edit = true;
	$blogEntryID = $oDB->getBlogEntryID($commentID);
} 
if(!isset($edit) || !isset($_GET['selectedUserID'])) {
	die("Internal communication error.");
}
$selectedUserID = $_GET['selectedUserID'];

echo "Here you're able to ";
if($edit) {
	echo "edit your comment to ";
} else {
	echo "comment ";
}
echo "blogEntry #$blogEntryID.";


if(isset($_POST['comment'])) {
	$success = $oDB->saveComment($blogEntryID, $_POST['comment'], $edit, $commentID);
}
?><br><br>
	<form method="post">
		<textarea name="comment"
			style="width: 600px; height: 300px; margin-bottom: 5px;"
			placeholder="enter your comment here">
<?php
if ($edit) {
	echo $oDB->getCommentText ( $commentID );
}
?>
</textarea>
		<br> <br> <input type="submit" value="save">
	</form>
</div>
<?php 
if(isset($_POST['comment'])) {
	if($success){
		echo "saving successful
				<br>
				<a href='./blogs/selectedUserID/$selectedUserID'>back</a>
				";
	} else {
		echo "saving unsuccessful";
	}

}


?>