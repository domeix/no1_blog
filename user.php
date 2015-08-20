<!DOCTYPE html>
<html>
<head>
<title>User-Administration</title>
<base href="//localhost/No1_Blog/">
<link rel="stylesheet" href="stylesheet.css">
</head>

<table>
<thead>
<tr>
</table>


<?php
session_start();
if(!isset($_SESSION['currentUser'])) {
	header('location: .');
} 
require_once 'dbconnect.php';
$oDB = new DBconnect();

$edit = false;
if(isset($_POST['editUserID'])) {
	$edit = true;
	$editUserID = $_POST['editUserID'];
	$editUser = $oDB->getUserdata($editUserID);
}


#------------------------------
# Create new user

if(isset($_POST['username']) && isset($_POST['password']) && isset($_POST['email'])) {
	
	$success = false;
	
	if($_POST['username'] != "" && $_POST['password'] != "" && $_POST['email'] != "") {
		$success = $oDB->createNewUser($_POST['username'], $_POST['password'], $_POST['email']);
	}
	
}

#-------------------------------


$allUsersArr = $oDB->getAllUsersArray();

echo "<table id = 'usertable'>
		<thead>
			<tr>
				<td>userID</td>
				<td>username</td>
				<td>email</td>
				<td>modificationDate</td>
				<td></td>
			</tr>
		</thead>
		<tbody>
		";

$userID=1;
while(isset($allUsersArr[$userID])) {
	$thisUser = $oDB->getUserdata($userID);
	$username = $allUsersArr[$userID];
	$email = $thisUser->email;
	$modificationDate = $thisUser->modificationDate;
	
	echo "<tr><td>$userID</td><td>$username</td><td>$email</td><td>$modificationDate</td>
		<td><a href='user/editUserID/$userID'>edit</a></td>
	</tr>";
	$userID++;
}

?>

<form method='POST'>
<table>
	<tbody>
		<tr>
			<th>
			<?php
			if($edit) {
				echo "Edit user $editUserID
				<input type='hidden' name='userID' value='$editUserID'>";
			} else {
				echo "Create a new user";
			}
			?>
			</th>
		<tr>
			<td>Username</td>
			<td><input	type="text" name="username" autocomplete="off"
			<?php if($edit){echo "value='" . $editUser->username . "'" ?>
			></td>
		</tr>
		<tr>
			<td>Password</td>
			<td><input type="text" name="password" autocomplete="off"
			<?php if($edit){echo "placeholder='no change'" ?>
			></td>
		</tr>
		<tr>
			<td>Email</td>
			<td><input type="email" name="email" autocomplete="off"
			<?php if($edit){echo "value='" . $editUser->email . "'" ?>
			></td>
		</tr>
		<tr>
			<td>
			<input type="submit" 
			<?php if($edit){echo "value='Edit user $editUserID'";} 
			else {echo "value='Create user'";} ?>			
			></td>
		</tr>
	</tbody>
</table>
</form>


<?php 
if(isset($success)) {
	if($success) {
		echo "saving successful";
	} else {
		echo "saving unsuccessful";
	}
	
}
?>
</html>


