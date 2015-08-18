<!DOCTYPE html>
<html>
<head>
<title>Read our blogs!</title>
<base href="//localhost/No1_Blog/">
<link rel="stylesheet" href="stylesheet.css">
</head>

<?php
session_start();
if(!isset($_SESSION['currentUser'])) {
	header('location: .');
}
echo "current user: " . $_SESSION['currentUser'];

require_once 'dbconnect.php';
$db = dbconnect();

$result = $db->query("SELECT userID, username FROM user ORDER BY userID ASC;");

$allusers = [];
while ($row = mysqli_fetch_assoc($result)) {
	array_push($allusers, $row['userID'].'-'.$row['username']);
}


if(!isset($allusers[0])) {
	die ("No users in database.");
}


#---------------------------
# navigation bar
#---------------------------
echo "<aside>";

$i=0;
while(isset($allusers[$i])) {
	echo "
		<p><a href='./blogs/selectedUser/".$allusers[$i]."/'>".$allusers[$i]."</a></p>
		";
	echo "<form method='post'>
			<input type='submit' name='selectedUser' value='".$allusers[$i]."'>
		</form>";
	$i++;
}

echo "	<p><a href='./writeBlog.php'>Blog schreiben</a></p>
		<p><a href=''>anderer Link</a></p>	
		<p>noch mehr Links</p>
		<p>weitere Links</p>
		<p class='logout'><a href='./logout.php' class='logout'>Logout</a></p>
			
	</aside>";
#----------------------------



if(isset($_REQUEST['selectedUser'])) {
	$selectedUser = $_REQUEST['selectedUser'];
	
} else {
	//the currently logged in user is the standard selected user
	$selectedUser = $_SESSION['currentUserID']."-".$_SESSION['currentUser'];
}



$selectedUserID = explode("-", $selectedUser)[0];
$selectedUserName = str_replace($selectedUserID."-", "", $selectedUser);


echo "<main>
		<h3>$selectedUserName's blog</h3>";

$result = $db->query("SELECT * FROM blogentries WHERE userID LIKE '$selectedUserID' ORDER BY blogEntryID DESC;");

while($row = mysqli_fetch_object($result)) {
	$text = $row->text;
	$text = nl2br($text); //Zeilenumbruch
	$heading = $row->heading;
	$blogEntryID = $row->blogEntryID;
	$hasComment = $row->hasComment;
	
	echo" <article>
				<h4>#$blogEntryID - $heading</h4>
				<p>$text</p>
				
				<form method='post' action='comment.php'>
					<input type='hidden' name='blogEntryID' value='$blogEntryID'>
					<input type='submit' value='comment' class='comBut'>
				</form>
				";
	
				if($hasComment) {
					$result2 = $db->query("SELECT * FROM comments WHERE blogEntryID LIKE '$blogEntryID';");
					echo "<details>";
					
					while($row = mysqli_fetch_object($result2)) {
						$commentText = $row->commentText;
						$commentText = nl2br($commentText); //Zeilenumbruch
						$commentID = $row->commentID;
						$commentUserID = $row->userID;
						
						$result3 = $db->query("SELECT username FROM user WHERE userID LIKE '$commentUserID';");
						$commentUserName = mysqli_fetch_object($result3)->username;
						
						echo "<p class='comment'>
								<b>#$commentID by $commentUserName:</b> <br>
								$commentText
								</p>";
						
					}
					
					
					echo "</details>";
				}
				
				echo "
			</article>";
}
echo "</main>";
		





		

