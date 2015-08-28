<!DOCTYPE html>
<html>
<head>
<title>Write your own blog!</title>
<base href="//<?php echo $_SERVER['HTTP_HOST'] ?>">
<link rel="stylesheet" href="stylesheet.css">
</head>

<?php
session_start();
if(!isset($_SESSION['currentUser'])) {
	header('location: .');
} 
require_once 'dbconnect.php';
$oDB = new DBconnect();

$edit = false;
$blogEntryID = NULL; 
if(isset($_GET['blogEntryID'])) {
	$blogEntryID = $_GET['blogEntryID'];
	$edit = true;

}


if(isset($_POST['text']) && isset($_POST['heading'])) {
	$success = false;
	
	$image = false;
	if($_FILES['image']['tmp_name'] != "") {
		$image = file_get_contents ($_FILES['image']['tmp_name']);
		$image = base64_encode($image);
	}
	
	if($_POST['text']!="" && $_POST['heading'] != "") {
		$success = $oDB->saveBlogEntry($_POST['heading'], $_POST['text'], $edit, $blogEntryID, $image);
	}
}	
if($edit) {
	$blogEntry = $oDB->getBlogEntry($blogEntryID);
}
?>
<div class='writingarea'>
	<form method="post" enctype="multipart/form-data">
		<input type="text" style="width: 600px; margin-bottom: 5px;"
			name="heading" placeholder="heading" autocomplete="off"
			<?php if($edit){ echo 'value="'.$blogEntry->heading.'"';}?>><br>
		<textarea name="text"
			style="width: 600px; height: 300px; margin-bottom: 5px;"
			placeholder="enter your text here"><?php if($edit){ echo $blogEntry->text;}?></textarea>
		<br>
		<label for="image">image: </label>
		<input type="file" name="image">	
			
		<br> <br> <input type="submit" value="publish">
	</form>
<?php

if(isset($_POST['text']) && isset($_POST['heading'])) {
	echo "<div class='successinfo'>";
	if($success){
		echo "  <div class='successinfo' id='successful'>
				publishing successful
				<br>
				<a href='.'>back</a>
				";
	} else {
		echo "<div class='successinfo' id='unsuccessful'>
		publishing unsuccessful";
	}
	echo "</div>";
	

}
?>
</div>
</html>