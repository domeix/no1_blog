<?php
class DBconnect {
	private $db = NULL;
	
	function __construct() {
		$this->db = mysqli_connect("localhost", "dominik", "1234", "blog") or die("Error " . mysqli_error($db));
		mysqli_set_charset($this->db, "UTF-8");
	}
	
	function query($string) {
		return $this->db->query($string);
	}
		
	/**
	 * creates Database as designed in mysqlWorkspace
	 * 19.08.15 - 11.00
	 */		
 	function createDatabase($dbname) {
		
		$this->query("
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema $dbname
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema $dbname
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `$dbname` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `$dbname` ;

-- -----------------------------------------------------
-- Table `$dbname`.`user`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `$dbname`.`user` (
  `rowID` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `userID` INT NOT NULL COMMENT '',
  `active` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '',
  `username` VARCHAR(50) NOT NULL COMMENT '',
  `password` VARCHAR(32) NOT NULL COMMENT '',
  `email` VARCHAR(200) NULL COMMENT '',
  `modificationDate` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '',
  PRIMARY KEY (`rowID`)  COMMENT '');


-- -----------------------------------------------------
-- Table `$dbname`.`blogentries`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `$dbname`.`blogentries` (
  `rowID` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `blogEntryID` INT NOT NULL COMMENT '',
  `active` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '',
  `heading` VARCHAR(100) NOT NULL COMMENT '',
  `text` TEXT(10000) NOT NULL COMMENT '',
  `userID` INT NOT NULL COMMENT '',
  `creationDate` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '',
  `modificationDate` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '',
  `hasComment` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '',
  `imageID` INT NOT NULL DEFAULT NULL COMMENT '',
  PRIMARY KEY (`rowID`)  COMMENT '',
  INDEX `username_idx` (`userID` ASC)  COMMENT '')
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `$dbname`.`comments`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `$dbname`.`comments` (
  `rowID` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `commentID` INT NOT NULL COMMENT '',
  `blogEntryID` INT NOT NULL COMMENT '',
  `commentText` VARCHAR(1000) NOT NULL COMMENT '',
  `userID` INT NOT NULL COMMENT '',
  `active` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '',
  `modificationDate` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '',
  `creationDate` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '',
  PRIMARY KEY (`rowID`)  COMMENT '',
  INDEX `userid_idx` (`userID` ASC)  COMMENT '',
  INDEX `blogEntryID_idx` (`blogEntryID` ASC)  COMMENT '')
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `$dbname`.`indices`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `$dbname`.`indices` (
  `id` INT NOT NULL COMMENT '',
  `nextuserID` INT(11) NULL DEFAULT NULL COMMENT '',
  `nextblogentriesID` INT NULL COMMENT '',
  `nextcommentsID` INT NULL COMMENT '',
  `nextimageID` INT(11) NULL DEFAULT NULL COMMENT '',
  PRIMARY KEY (`id`)  COMMENT '')
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `$dbname`.`images`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `$dbname`.`images` (
  `imageID` INT(11) NOT NULL AUTO_INCREMENT COMMENT '',
  `image` MEDIUMTEXT NULL COMMENT '',
  PRIMARY KEY (`imageID`)  COMMENT '')
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
				
				");
		
		
	}
	#-------------------------------------
			
	/**
	 * @return all users in an array starting with 1!
	 */
	function getAllUsernamesArray() {
		$result = $this->db->query("SELECT username FROM user WHERE active ORDER BY userID ASC;");
		
		if(!$result) {
			die ("No users in database.");
		}
		
		$allusersArr = array();
		while ($row = mysqli_fetch_object($result)) {
			array_push($allusersArr, $row->username);
		}		
	
		return $allusersArr;
	}
	/**
	 * @param int $userID
	 * @return string username
	 */
	function getUsername($userID) {
		$result = $this->query("SELECT username FROM user WHERE userID LIKE '$userID';");
		return mysqli_fetch_object($result)->username;
	}
	
	function getUserID($username) {
		return $this->getUserdataByName($username)->userID;
	}
	
	function getUserdataByName($username) {
		$result = $this->query("SELECT * FROM user WHERE username LIKE '$username' AND active;");
		return mysqli_fetch_object($result);
	}
	
	
	function getUserdataByID($userID) {
		return $this->getUserdataByName($this->getUsername($userID));
	}
	
	/**
	 * @param int $commentID
	 * @return int blogEntryID
	 */
	function getBlogEntryID($commentID) {
		$result = $this->query("SELECT blogEntryID FROM comments WHERE commentID LIKE '$commentID' AND active;");
		return mysqli_fetch_object($result)->blogEntryID;
	}
	
	/**
	 * @param int $commentID
	 * @return string commentText
	 */
	function getCommentText($commentID) {
		$result = $this->query("SELECT commentText FROM comments WHERE commentID LIKE '$commentID' AND active;");
		return mysqli_fetch_object($result)->commentText;
	}
	
	/**
	 * creates new comment, with different rowID, modificationDate
	 * deactivates old comment
	 * @param int $commentID
	 * @return true, if copying successful
	 */
	function copyComment($commentID) {
		$result = $this->query("SELECT rowID FROM comments WHERE commentID LIKE '$commentID' AND active;");
		$oldRowID = mysqli_fetch_object($result)->rowID;
		$success = $this->query("
			INSERT INTO comments (commentID, blogEntryID, commentText, userID, creationDate)
				(SELECT commentID, blogEntryID, commentText, userID, creationDate FROM comments WHERE rowID = '$oldRowID');
				");
		if($success) {
			$success = $this->query("UPDATE comments SET active = 0 WHERE rowID = $oldRowID;");
		}
		return $success;
	}
	
	/**
	 * 
	 * @param int $blogEntryID
	 * @param string $commentText
	 * @param boolean $edit
	 * @param int $commentID
	 * @return true, if saving successful 
	 * OR false, if not (e.g. no change, missing db-connection etc.)
	 */
	function saveComment($blogEntryID, $commentText, $edit, $commentID) {
		$userID = $_SESSION['currentUserID'];

		if($edit) {
			
			$oldCommentText = $this->getCommentText($commentID);
			if($commentText==$oldCommentText) {
				$success = false;
				
			} else {
				
				$success = $this->copyComment($commentID);
				
				if($success) {
					$success = $this->query("UPDATE comments SET commentText = '$commentText' WHERE commentID LIKE '$commentID' and active;");
				}
			}
		} else {
			
			$commentID = $this->getNextIndex("comments");
			
			$success = $this->query("INSERT INTO comments (blogEntryID, commentText, userID, commentID)  VALUES ('$blogEntryID', '$commentText', '$userID', '$commentID');");
			if($success) {
				$success = $this->query("UPDATE blogentries SET hasComment = TRUE WHERE blogEntryID LIKE '$blogEntryID' and active;");
			}
		}
		
		return $success;		
	}
	
	/**
	 * creates new blogEntry, with different rowID, modificationDate
	 * deactivates old blogEntry
	 * @param int $blogEntryID
	 * @return true, if copying successful
	 */
	function copyBlogEntry($blogEntryID) {
		$result = $this->query("SELECT rowID FROM blogentries WHERE blogEntryID LIKE '$blogEntryID' AND active;");
		$oldRowID = mysqli_fetch_object($result)->rowID;
		$success = $this->query("
			INSERT INTO blogentries (blogEntryID, heading, text, userID, creationDate, hasComment)
			 	(SELECT blogEntryID, heading, text, userID, creationDate, hasComment 
				FROM blogentries
				WHERE rowID = '$oldRowID');
		");
		if($success){
			$success = $this->query("UPDATE blogentries SET active = 0 WHERE rowID LIKE '$oldRowID';");
		}
		return $success;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function saveBlogEntry($heading, $text, $edit, $blogEntryID, $image) {
		$userID = $_SESSION['currentUserID'];
		
		$imageID = 0;
		if($image != false){
			$imageID = $this->getNextIndex("image");
			$this->saveImage($imageID, $image);
		}

		if($edit) {
			$oldBlogEntry = $this->getBlogEntry($blogEntryID);
			if($heading==$oldBlogEntry->heading && $text==$oldBlogEntry->text && $imageID==$oldBlogEntry->imageID) {
				$success = false;				
			}else{
				$success = $this->copyBlogEntry($blogEntryID);
				
				if($success) {
					$success = $this->query("UPDATE blogentries SET heading = '$heading', text = '$text', imageID = '$imageID' WHERE blogEntryID LIKE '$blogEntryID' AND active;");
				}
			}	
		} else {
			
			$blogEntryID = $this->getNextIndex("blogentries");
			
			$success = $this->query("INSERT INTO blogentries (blogEntryID, heading, text, userID, imageID)  VALUES ('$blogEntryID', '$heading', '$text', '$userID', '$imageID');");
		}
		
		return $success;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	/**
	 * @param int $blogEntryID
	 * @return mysqli_fetch_object
	 */
	function getBlogEntry($blogEntryID) {
		$result = $this->query("SELECT * FROM blogentries WHERE blogEntryID LIKE '$blogEntryID' AND active;");
		return mysqli_fetch_object($result);
	}
	
	
	function getImage($imageID) {
		$result = $this->query("SELECT image FROM images WHERE imageID LIKE '$imageID';");
		return mysqli_fetch_object($result)->image;
	}
	
	function saveImage($imageID, $image) {
		$this->query("INSERT INTO images (imageID, image) VALUES ('$imageID', '$image')");
		
		
	}
	
	/**
	 * returns next index of the table $table
	 * increments the next index
	 * @param string $table
	 * @return int nextIndex
	 */
	function getNextIndex($table) {
		$result = $this->query("SELECT next"."$table"."ID FROM indices LIMIT 1;");
		$index = mysqli_fetch_array($result)[0];
		$this->query("UPDATE indices SET next"."$table"."ID = ($index+1) WHERE 1;");
		return $index;
	}
	
	
	function createNewUser($userID, $username, $password, $email){

		#-----------
		#if $userID is already set -> creation of updated copy
		# !isset() --> new user
		if(!isset($userID)) {
			
			#-------------
			# if name already exists -> error
			$allusernames = $this->getAllUsernamesArray();
			foreach($allusernames as $testuser) {
				if($testuser == $username) {
					return false;
				}
			}
				
			#----------
			# if name is new
			$userID = $this->getNextIndex("user");
		}

		$pw5 = md5($password);
		
		return $this->query("INSERT INTO user (userID, username, password, email) VALUES ('$userID', '$username', '$pw5', '$email');");
	}
	
	function updateUser($userID, $username, $password, $email) {
		if($password == "") {
			$password = $this->getUserdataByID($userID)->password;
		}
		$success = $this->deactivateUser($userID);
		if($success) {
			$success = $this->createNewUser($userID, $username, $password, $email);
		}
		
		return $success;		
	}
	
	function deleteUser($userID) {
		return $this->deactivateUser($userID);
	}
	
	private function deactivateUser($userID) {
		return $this->query("UPDATE user SET active=0 WHERE userID LIKE '$userID' AND active;");		
	}
	
	
	
	
}























