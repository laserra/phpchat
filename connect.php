<?php

	########## MySql details #############
	$db_username = "username"; //Database Username
	$db_password = "password"; //Database Password
	$hostname = "localhost"; //Mysql Hostname
	$db_name = "chat_database"; //Database Name

	// Create connection
	$conn = new mysqli($hostname, $db_username, $db_password, $db_name);

	// Check connection
	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	} 

?>