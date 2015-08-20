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
	 * creates Database as designed in mysqlWorkspace
	 * 19.08.15 - 11.00
	 */		
 	function createDatabase() {
		
		$this->query("
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema blog
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema blog
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `blog` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `blog` ;

-- -----------------------------------------------------
-- Table `blog`.`user`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `blog`.`user` (
  `rowID` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `userID` INT NOT NULL COMMENT '',
  `active` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '',
  `username` VARCHAR(50) NOT NULL COMMENT '',
  `password` VARCHAR(32) NOT NULL COMMENT '',
  `email` VARCHAR(200) NULL COMMENT '',
  `modificationDate` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '',
  PRIMARY KEY (`rowID`)  COMMENT '');


-- -----------------------------------------------------
-- Table `blog`.`blogentries`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `blog`.`blogentries` (
  `rowID` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `blogEntryID` INT NOT NULL COMMENT '',
  `active` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '',
  `heading` VARCHAR(100) NOT NULL COMMENT '',
  `text` TEXT(10000) NOT NULL COMMENT '',
  `userID` INT NOT NULL COMMENT '',
  `creationDate` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '',
  `modificationDate` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '',
  `hasComment` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '',
  PRIMARY KEY (`rowID`)  COMMENT '',
  INDEX `username_idx` (`userID` ASC)  COMMENT '')
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `blog`.`comments`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `blog`.`comments` (
  `rowID` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `commentID` INT NOT NULL COMMENT '',
  `blogEntryID` INT NOT NULL COMMENT '',
  `commentText` VARCHAR(1000) NOT NULL COMMENT '',
  `userID` INT NOT NULL COMMENT '',
  `active` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '',
  `modificationDate` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '',
  INDEX `userid_idx` (`userID` ASC)  COMMENT '',
  INDEX `blogentryID_idx` (`blogEntryID` ASC)  COMMENT '',
  PRIMARY KEY (`rowID`)  COMMENT '')
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `blog`.`indices`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `blog`.`indices` (
  `id` INT NOT NULL COMMENT '',
  `nextUserID` INT NULL COMMENT '',
  `nextBlogEntryID` INT NULL COMMENT '',
  `nextCommentID` INT NULL COMMENT '',
  PRIMARY KEY (`id`)  COMMENT '')
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
	function getAllUsersArray() {
		$result = $this->db->query("SELECT userID, username FROM user WHERE active ORDER BY userID ASC;");
		
		$allusersArr = ["definitly no user!!"];
		while ($row = mysqli_fetch_assoc($result)) {
			array_push($allusersArr, $row['username']);
		}
		
		if(!isset($allusersArr[1])) {
			die ("No users in database.");
		}
		
		return $allusersArr;
	}
	/**
	 * @param int $userID
	 * @return string username
	 */
	function getUsername($userID) {
		return $this->getUserdata($userID)->username;
	}
	
	function getUserdata($userID) {
		$result = $this->query("SELECT * FROM user WHERE userID LIKE '$userID' AND active;");
		return mysqli_fetch_object($result);
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
	
	function saveBlogEntry($heading, $text, $edit, $blogEntryID) {
		$userID = $_SESSION['currentUserID'];

		if($edit) {
			$oldBlogEntry = $this->getBlogEntry($blogEntryID);
			if($heading==$oldBlogEntry->heading && $text==$oldBlogEntry->text) {
				$success = false;				
			}else{
				$success = $this->copyBlogEntry($blogEntryID);
				
				if($success) {
					$success = $this->query("UPDATE blogentries SET heading = '$heading', text = '$text' WHERE blogEntryID LIKE '$blogEntryID' AND active;");
				}
			}	
		} else {
			
			$blogEntryID = $this->getNextIndex("blogentries");
			
			$success = $this->query("INSERT INTO blogentries (blogEntryID, heading, text, userID)  VALUES ('$blogEntryID', '$heading', '$text', '$userID');");
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
	
	
	function createNewUser($username, $password, $email){
		
		$userID = $this->getNextIndex("user");
		$pw5 = md5($password);
		
		return $this->query("INSERT INTO user (userID, username, password, email) VALUES ('$userID', '$username', '$pw5', '$email');");
	
	}
	
	
	
	
	
}























