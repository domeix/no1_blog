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
echo "<header id='user'> 
		<img src='Images/User1.png' class='icon'>
		
		" . $_SESSION['currentUser'] .
		"
		<div class='logout'><a href='./logout' class='logout'>Logout</a></div>
		
		</header>";

require_once 'dbconnect.php';
$oDB = new DBconnect();

$allusersArr = $oDB->getAllUsersArray();

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


//get all blog entries of the selected user
$result = $oDB->query("SELECT * FROM blogentries WHERE userID LIKE '$selectedUserID' ORDER BY blogEntryID DESC;");

while($row = mysqli_fetch_object($result)) {
	$text = $row->text;
	$text = nl2br($text); //Zeilenumbruch
	$heading = $row->heading;
	$blogEntryID = $row->blogEntryID;
	$hasComment = $row->hasComment;
	$datetime = $row->datetime;
	
	echo" <article>
				<h4>#$blogEntryID - $heading</h4>
				<p class='datetime'>created: $datetime</p>
				<p>$text</p>";
				
				if($selectedUserID==$_SESSION['currentUserID']) {		//own entries
					echo "<a href='writeBlog/blogEntryID/$blogEntryID' class='aBlogEdit'>edit</a>";
				}
				echo "
				<p class='pComBut'><a href='comment/blogEntryID/$blogEntryID/selectedUserID/$selectedUserID' class='aComBut'>comment</a>

				";
	
				if($hasComment) {
					$result2 = $oDB->query("SELECT * FROM comments WHERE blogEntryID LIKE '$blogEntryID';");
					echo "<details>";
					
					while($row = mysqli_fetch_object($result2)) {
						$commentText = $row->commentText;
						$commentText = nl2br($commentText); //Zeilenumbruch
						$commentID = $row->commentID;
						$commentUserID = $row->userID;
						
						$result3 = $oDB->getUser($commentUserID);
						$commentUserName = mysqli_fetch_object($result3)->username;
						
						echo "<p class='comment'>
								<b>#$commentID by $commentUserName:</b> <br>
								$commentText";
						
						if($_SESSION['currentUserID']==$commentUserID) {	//own comments
							echo "<a href='comment/commentID/$commentID/selectedUserID/$selectedUserID' class='aComEdit'>edit</a>";
						}
								
						echo "		</p>";
						
					}
					
					
					echo "</details>";
				}
	
				echo "
																	
			</article>";
}
echo "</main>";
		





		

