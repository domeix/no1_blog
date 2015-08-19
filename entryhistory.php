<!DOCTYPE html>
<html>
<head>
<title>Bloghistory</title>
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

if(!isset($_GET['blogEntryID'])){
	die("No blogentry selected.");
}

$blogEntryID = $_GET['blogEntryID'];

$result = $oDB->query("SELECT userID FROM blogentries WHERE blogEntryID = $blogEntryID LIMIT 1;");
$userID = mysqli_fetch_object($result)->userID;
$username = $oDB->getUsername($userID);

echo " <main><h3>History of Entry #$blogEntryID by $username</h3>";

$result = $oDB->query("SELECT * FROM blogentries WHERE blogEntryID = $blogEntryID ORDER BY rowID DESC;");

while($row = mysqli_fetch_object($result)) {
	$text = $row->text;
	$text = nl2br($text); //Zeilenumbruch
	$heading = $row->heading;
	$creationDate = $row->creationDate;
	$modificationDate = $row->modificationDate;
	
	//Blogentries
	echo" <article>
	<h4>#$blogEntryID - $heading</h4>
	<p class='datetime'>created: $creationDate";
	if($modificationDate!=$creationDate) {
		echo ", modificated: $modificationDate";
	}
	echo"	</p>	<p class='blogentries'>$text</p>
	</article>
	";
	
}

echo "<a href='blogs/userID/$userID'>back</a>

</main>";


























