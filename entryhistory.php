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

//name rausfindne, üschr, dann alle blogeinträge nach moddate sortiert, zurückbutton.

