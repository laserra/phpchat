<?php
	include("connect.php"); 
	
	$user_message=$_GET['message'];
	$user_name=$_GET['name'];
	
	//Check if message is empty and send the error code
	if(strlen($user_message) < 1){
	   echo 3;
	}
	//Check if message is too long
	else if(strlen($user_message) > 255){
	   echo 4;
	}
	//Check if name is empty
	else if(strlen($user_name) < 1){
	   echo 5;
	}
	//Check if name is too long
	else if(strlen($user_name) > 29){
	   echo 6;
	}
	
	//If everything is fine
	else{
	   //This array contains the characters what will be removed from the message and name, because else somebody could send redirection script or links
	   $search = array("<",">",">","<");
	   //Insert a new row in the chat table
	   mysqli_query($conn, "INSERT INTO messages (timestamp, id_user, ip, message) VALUES ('" . time() . "', '" . str_replace("_"," ",$user_name) . "', '" . @$REMOTE_ADDR . "', '" . str_replace($search,"",$user_message) . "')") or die(8);
	}
?>