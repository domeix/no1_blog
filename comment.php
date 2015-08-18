<!DOCTYPE html>
<html>
<head>
<title>Comment this blog</title>
<base href="//localhost/No1_Blog/">
<link rel="stylesheet" href="stylesheet.css">
</head>


<?php
session_start();
if(!isset($_SESSION['currentUser'])) {
	header('location: .');
}
require_once 'dbconnect.php';

//FEHLER ABFANGEN!
$blogEntryID = $_GET['blogEntryID'];
$selectedUserID = $_GET['selectedUserID'];

echo "Here you're able to comment blogentry #$blogEntryID.";
?>

<form method="post">
<textarea name="comment" style="width: 600px; height: 300px; margin-bottom:5px;" placeholder="enter your comment here"></textarea><br>
<br>

	<input type="submit" value="comment">

</form>

<?php 
if(isset($_POST['comment'])) {

	$db = dbconnect();

	$userID = $_SESSION['currentUserID'];
	$comment = $_POST['comment'];
	$success = $db->query("INSERT INTO comments (blogEntryID, commentText, userID)  VALUES ('$blogEntryID', '$comment', '$userID');");

	$success2 = $db->query("UPDATE blogentries SET hasComment = TRUE WHERE blogEntryID LIKE '$blogEntryID';");
	
	if($success && $success2){
		echo "commenting successful
				<br>
				<a href='./blogs/selectedUserID/$selectedUserID'>back</a>
				";
	} else {
		echo "commenting unsuccessful";
	}

}


?>