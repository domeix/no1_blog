<?php
session_start();
if(isset($_SESSION['currentUser'])) {
	header('location:main.php');
} else {
	header('location:login.php');
}

