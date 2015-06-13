<?php
	error_reporting(E_ERROR);
	
	include('connect.php'); 
	
	//Get the first 50 messages ordered by time
	$result = mysqli_query($conn, "SELECT * FROM messages ORDER BY timestamp desc limit 0,150");
	$messages = array();

	$usecolor="";

	//Loop and get in an array all the rows until there are not more to get
	while($row = mysqli_fetch_array($result)){

		//Check if result returns a null value as the user might not be in the table
		$color = current($conn->query("SELECT IFNULL(google_color,'') FROM google_users WHERE google_name='".str_replace("_"," ",$row[id_user])."'")->fetch_assoc());

		if($color==''){
			$color='#555';
		}

	   $messages[] = "<div class='message'><div class='messagecontent' style='font-weight: bold; color:".$color."'>" . $row[id_user].": ".$row[message] . "</div></div>";
	   
	   //The last posts date
	   $old = $row[timestamp];
	}

	//Display the messages in an ascending order, so the newest message will be at the bottom
	for($i=149;$i>=0;$i--){
	   echo $messages[$i];
	}
	
	//Uncomment if you want to delete old messages
	//mysqli_query($conn, "DELETE FROM messages WHERE timestamp < " . $old);
?>