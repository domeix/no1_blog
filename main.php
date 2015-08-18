<?php
session_start();
if(!isset($_SESSION['currentUser'])) {
	header('location: .');
}
header("location:blogs");