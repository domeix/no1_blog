<!DOCTYPE html>
<html>
<head>
<title>User-Administration</title>
<base href="//<?php echo $_SERVER['HTTP_HOST'] ?>/No1_Blog/">
<link rel="stylesheet" href="stylesheet.css">
</head>
<body>
<aside>
<p id='refresh'>۞ <a href='user'>refresh page</a></p>
<p id='back'><a href='.'>home</a></p>
<p class='plogout'><a href='./logout' class='alogout'>Logout</a></p>
</aside>

<?php
echo "
<main>
<table>
<thead>
<tr>
</table>
";


session_start();
if(!isset($_SESSION['currentUser'])) {
	header('location: .');
} 
require_once 'dbconnect.php';
$oDB = new DBconnect();

#------------------------------
# Create new user, edit old ones

if(isset($_POST['username']) && isset($_POST['password']) && isset($_POST['email'])) {
	
	$success = false;
	
	if(isset($_POST['editUserID'])) {		//editing user
		$success = $oDB->updateUser($_POST['editUserID'], $_POST['username'], $_POST['password'], $_POST['email']);		
		
	} else {								//creating user
		if($_POST['username'] != "" && $_POST['password'] != "" && $_POST['email'] != "") {
			$success = $oDB->createNewUser(NULL, $_POST['username'], $_POST['password'], $_POST['email']);
		}
	}
}

#-------------------------------
$edit = false;
if(isset($_GET['editUserID'])) {
	$edit = true;
	$editUserID = $_GET['editUserID'];
	$editUser = $oDB->getUserdataByID($editUserID);
}
#--------------------------------
$delete = false;
if(isset($_GET['delUserID'])) {
	$delete = true;
	$delUserID = $_GET['delUserID'];
	
	$delSuccess = $oDB->deleteUser($delUserID);
}
#--------------------------------

$allUsernames = $oDB->getAllUsernamesArray();

echo "<table id = 'usertable'>
		<thead>
			<tr>
				<td>userID</td>
				<td>username</td>
				<td>email</td>
				<td>modificationDate</td>
				<td></td>
				<td></td>
			</tr>
		</thead>
		<tbody>
		";


foreach ($allUsernames as $username) {
	$thisUser = $oDB->getUserdataByName($username);
	$userID = $thisUser->userID;
	$email = $thisUser->email;
	$modificationDate = $thisUser->modificationDate;
	
	echo "<tr><td>$userID</td><td>$username</td><td>$email</td><td>$modificationDate</td>
		<td><a href='user/editUserID/$userID'>- edit -</a></td>
		<td><a href='user/delUserID/$userID'>delete</a></td>
	</tr>";
	$userID++;
}

?>

<form method='POST'>
<?php if($edit) {echo"<input type='hidden' name='editUserID' value='$editUserID'>"; }?>
<table id="usertableIn">
	<tbody>
		<tr>
			<th colspan="2" align="left">
			<?php
			if($edit) {
				echo "Edit user #$editUserID";
			} else {
				echo "Create a new user";
			}
			?>
			</th>
		<tr>
			<td>Username</td>
			<td><input	type="text" name="username" autocomplete="off"
			<?php if($edit){echo "value='" . $editUser->username . "'";} ?>
			></td>
		</tr>
		<tr>
			<td>Password</td>
			<td><input type="text" name="password" autocomplete="off"
			<?php if($edit){echo "placeholder='no change'";} ?>
			></td>
		</tr>
		<tr>
			<td>Email</td>
			<td><input type="email" name="email" autocomplete="off"
			<?php if($edit){echo "value='" . $editUser->email . "'" ;}?>
			></td>
		</tr>
		<tr>
			<td colspan="2" align="left">
			<input type="submit" 
			<?php if($edit){echo "value='Edit user #$editUserID'";} 
			else {echo "value='Create user'";} ?>			
			></td>
		</tr>
	</tbody>
</table>
</form>



<?php 
if(isset($success)) {
	if($success) {
		echo "<div class='successinfo' id='successful'>saving successful</div>";
	} else {
		echo "<div class='successinfo' id='unsuccessful'>saving unsuccessful</div>";
	}
	
}
echo "
</main>
</body>
</html>

";
