<!DOCTYPE html>
<html>
<head>
<title>Comment this blog</title>
<link rel="stylesheet" href="stylesheet.css">
</head>


<?php
session_start();
if(!isset($_SESSION['currentUser'])) {
	header('location: .');
}
require 'dbconnect.php';

//FEHLER ABFANGEN!
$blogEntryID = $_POST['blogEntryID'];

echo "Here you're able to comment blogentry number $blogEntryID.";
?>

<form method="post">
<textarea name="comment" style="width: 600px; height: 300px; margin-bottom:5px;" placeholder="enter your comment here"></textarea><br>
<br><br>

<?php
echo "<input type='hidden' name='blogEntryID' value='$blogEntryID'>";
?>

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
				<a href='.'>back</a>
				";
	} else {
		echo "commenting unsuccessful";
	}

}


?>