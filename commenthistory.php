<!DOCTYPE html>
<html>
<head>
<title>Bloghistory</title>
<base href="//<?php echo $_SERVER['HTTP_HOST'] ?>">
<link rel="stylesheet" href="stylesheet.css">
</head>

<?php
session_start();
if(!isset($_SESSION['currentUser'])) {
	header('location: .');
} 
require_once 'dbconnect.php';
$oDB = new DBconnect();

if(!isset($_GET['commentID']) || !isset($_GET['selectedUserID'])){
	die("No comment selected.");
}
$commentID = $_GET['commentID'];
$selectedUserID = $_GET['selectedUserID'];

$result = $oDB->query("SELECT userID FROM comments WHERE commentID LIKE '$commentID' LIMIT 1;");
$userID = mysqli_fetch_object($result)->userID;
$commentUsername = $oDB->getUsername($userID);		

echo " <main><h3>History of Comment #$commentID by $commentUsername</h3>";

$result = $oDB->query("SELECT * FROM comments WHERE commentID LIKE '$commentID' ORDER BY rowID DESC;");

while($row = mysqli_fetch_object($result)) {
	$commentText = $row->commentText;
	$commentText = nl2br($commentText); //Zeilenumbruch
	
	echo "<p class='comment'>
			<b>#$commentID by $commentUsername:</b> <br>
			$commentText </p>";
}
echo "<a href='blogs/selectedUserID/$selectedUserID'>back</a>

</main>";