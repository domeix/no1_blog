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

$allusersArr = ["keinName"];
while ($row = mysqli_fetch_assoc($result)) {
	array_push($allusersArr, $row['username']);
}


if(!isset($allusersArr[1])) {
	die ("No users in database.");
}


#---------------------------
# navigation bar
#---------------------------
echo "<aside>";

$i=1;
while(isset($allusersArr[$i])) {
	echo "
		<p class='pBlog'><a href='./blogs/selectedUserID/$i' class='aBlog'>".$allusersArr[$i]."</a></p>
		";
	$i++;
}

echo "	<p><a href='./writeBlog'>Blog schreiben</a></p>
		<p><a href=''>anderer Link</a></p>	
		<p>noch mehr Links</p>
		<p>weitere Links</p>
		<p class='logout'><a href='./logout' class='logout'>Logout</a></p>
			
	</aside>";
#----------------------------



if(isset($_GET['selectedUserID'])) {
	$selectedUserID = $_GET['selectedUserID'];
	
} else {
	//the currently logged in user is the standard selected user
	$selectedUserID = $_SESSION['currentUserID'];
}

$selectedUserName = $allusersArr[$selectedUserID];


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
				
				<p class='pComBut'><a href='comment/blogEntryID/$blogEntryID/selectedUserID/$selectedUserID' class='aComBut'>comment</a>

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
		





		

