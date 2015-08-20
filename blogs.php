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
		<p id='username'>
		" . $_SESSION['currentUser'] .
		"</p>
		<p class='plogout' id='logouttop'><a href='./logout' class='alogout'>Logout</a></p>
		
		</header>";

require_once 'dbconnect.php';
$oDB = new DBconnect();

$allusernames = $oDB->getAllUsernamesArray();

#---------------------------
# navigation bar
#---------------------------
echo "<aside>";


foreach ($allusernames as $username) {
	$userID = $oDB->getUserID($username);
	
	echo "
		<p class='pBlog'><a href='./blogs/selectedUserID/$userID' class='aBlog'>$username</a></p>
		";
}

echo "	<p><a href='./writeBlog'>Blog<br>schreiben</a></p>
		<p><a href=''>anderer<br>Link</a></p>	
		<p>noch mehr<br>Links</p>
		<p><a href='user'>☺ user ☺<br>administration</a></p>
		<p class='plogout'><a href='./logout' class='alogout'>Logout</a></p>
			
	</aside>";
#----------------------------



if(isset($_GET['selectedUserID'])) {
	$selectedUserID = $_GET['selectedUserID'];
	
} else {
	//the currently logged in user is the standard selected user
	$selectedUserID = $_SESSION['currentUserID'];
}

$selectedUserName = $oDB->getUsername($selectedUserID);


echo "<main>
		<h3>$selectedUserName's blog</h3>";


//get all blog entries of the selected user
$result = $oDB->query("SELECT * FROM blogentries WHERE userID LIKE '$selectedUserID' AND active ORDER BY blogEntryID DESC;");

while($row = mysqli_fetch_object($result)) {
	$text = $row->text;
	$text = nl2br($text); //Zeilenumbruch
	$heading = $row->heading;
	$blogEntryID = $row->blogEntryID;
	$hasComment = $row->hasComment;
	$creationDate = $row->creationDate;
	$modificationDate = $row->modificationDate;
	
	//Blogentries
	echo" <article>
				<h4>#$blogEntryID - $heading</h4>
				<p class='datetime'>created: $creationDate";
	if($modificationDate!=$creationDate) {
		echo ", modificated: $modificationDate (<a href='entryhistory/blogEntryID/$blogEntryID'>show history</a>)";
	}
	echo"	</p>	<p class='blogentries'>$text</p>";
				
				if($selectedUserID==$_SESSION['currentUserID']) {		//own entries
					echo "<a href='writeBlog/blogEntryID/$blogEntryID' class='aBlogEdit'>edit</a>";
				}
				echo "
				<p class='pComBut'><a href='comment/blogEntryID/$blogEntryID/selectedUserID/$selectedUserID' class='aComBut'>comment</a>

				";
	
				if($hasComment) {
					$result2 = $oDB->query("SELECT * FROM comments WHERE blogEntryID LIKE '$blogEntryID' AND active ORDER BY commentID ASC;");
					echo "<details>";
					
					while($row2 = mysqli_fetch_object($result2)) {
						$commentText = $row2->commentText;
						$commentText = nl2br($commentText); //Zeilenumbruch
						$commentID = $row2->commentID;
						$commentUserID = $row2->userID;
						$commentUserName = $oDB->getUsername($commentUserID);
						$commentCreationDate = $row2->creationDate;
						$commentModificationDate = $row2->modificationDate;
						
						
						echo "<p class='comment'>
								<b>#$commentID by $commentUserName:</b> <br>
								";
						
						if($commentCreationDate != $commentModificationDate) {
							echo "<span class='datetime'>(<a href='commenthistory/commentID/$commentID/selectedUserID/$selectedUserID'>show history</a>)</span><br>";
						}
						
						echo "
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
		





		

