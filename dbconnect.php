<?php

function dbconnect() {
	$db = mysqli_connect("localhost", "dominik", "1234", "blog") or die("Error " . mysqli_error($db));
	return $db;
}
