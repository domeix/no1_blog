<!DOCTYPE html>
<html>
<head>
<title>Login to read our blogs!</title>
<base href="//<?php echo $_SERVER['HTTP_HOST'] ?>/No1_Blog/">
<link rel="stylesheet" href="stylesheet.css">
</head>

<form method="post">
	<table>
	<tbody>
		<tr>
			<td>
				<label for="username">Username: </label>
			</td>
			<td>
				<input type="text" name="username" autocomplete="off" class="loginput">
			</td>
		</tr>
		<tr>
			<td>
				<label for="password">Password: </label>
			</td>
			<td>
				<input type="password" name="password" class="loginput">
			</td>
		</tr>
		<tr>
			<td>
				<input type="submit" value="login">
			</td>
		</tr>
	</tbody>
	</table>
</form>


<?php


require_once 'dbconnect.php';
$oDB = new DBconnect();
$usernames = $oDB->getAllUsernamesArray();


echo "The users are: " . implode(", ", $usernames);


if(isset($_POST["username"]) && isset($_POST["password"])) {
	require_once 'loginFunc.php';
	if(login($_POST["username"],$_POST["password"])) {	//logindata correct
		header("location:main");
	} else {											//logindata wrong
		echo "please check your login";
	}
	
	
	
	
	
}