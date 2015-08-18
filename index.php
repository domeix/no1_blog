<?php
session_start();
if(isset($_SESSION['currentUser'])) {
	header('location:main');
} else {
	header('location:login');
}

