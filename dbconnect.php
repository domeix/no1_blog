<?php
class DBconnect {
	private $db = NULL;
	
	function __construct() {
		$this->db = mysqli_connect("localhost", "dominik", "1234", "blog") or die("Error " . mysqli_error($db));
	}
	
	function query($string) {
		return $this->db->query($string);
	}
	
	/**
	 * @return all users in an array starting with 1!
	 */
	function getAllUsersArray() {
		$result = $this->db->query("SELECT userID, username FROM user ORDER BY userID ASC;");
		
		$allusersArr = ["definitly no user!!"];
		while ($row = mysqli_fetch_assoc($result)) {
			array_push($allusersArr, $row['username']);
		}
		
		if(!isset($allusersArr[1])) {
			die ("No users in database.");
		}
		
		return $allusersArr;
	}
	
	function getUser($userID) {
		return $this->query("SELECT username FROM user WHERE userID LIKE '$userID';");
	}
	
	function getBlogEntryID($commentID) {
		$result = $this->query("SELECT blogEntryID FROM comments WHERE commentID LIKE '$commentID';");
		return mysqli_fetch_object($result)->blogEntryID;
	}
	
	function getCommentText($commentID) {
		$result = $this->query("SELECT commentText FROM comments WHERE commentID LIKE '$commentID';");
		return mysqli_fetch_object($result)->commentText;
	}
	
	function saveComment($blogEntryID, $commentText, $edit, $commentID) {
		$userID = $_SESSION['currentUserID'];

		if($edit) {
			$success = $this->query("UPDATE comments SET commentText = '$commentText' WHERE commentID LIKE '$commentID';");
			$success2 = true;
		} else {
			$success = $this->query("INSERT INTO comments (blogEntryID, commentText, userID)  VALUES ('$blogEntryID', '$commentText', '$userID');");
			$success2 = $this->query("UPDATE blogentries SET hasComment = TRUE WHERE blogEntryID LIKE '$blogEntryID';");
		}
		
		return ($success&&$success2);		
	}
	
	function saveBlogEntry($heading, $text, $edit, $blogEntryID) {
		$userID = $_SESSION['currentUserID'];

		if($edit) {
			$success = $this->query("UPDATE blogentries SET heading = '$heading', text = '$text' WHERE blogEntryID LIKE '$blogEntryID';");
				
		} else {
			$success = $this->query("INSERT INTO blogentries (heading, text, userID)  VALUES ('$heading', '$text', '$userID');");
		}
		
		return $success;
	}
	
	
	function getBlogEntry($blogEntryID) {
		$result = $this->query("SELECT * FROM blogentries WHERE blogEntryID LIKE '$blogEntryID';");
		return mysqli_fetch_object($result);
	}
	
}