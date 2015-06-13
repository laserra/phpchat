<?php
	include('connect.php'); 
	
	//Get all users ordered by time
	$result = mysqli_query($conn, "SELECT * FROM google_users ORDER BY google_name desc");
	$users = array();
	
	
	//Loop and get in an array all the rows until there are not more to get
	while($row = mysqli_fetch_array($result)){

		$color = current($conn->query("SELECT google_color FROM google_users WHERE google_name='".str_replace("_"," ",$row[google_name])."'")->fetch_assoc());

	   //Put the users in divs and then in an array
	   $users[] = "<div class='user' style='font-weight: bold; color:".$color."'>" . $row[google_name]."</div>";
	   
	   //The last posts date
	   $old = $row[timestamp];
	}
	//Display users in an ascending order
	for($i=9;$i>=0;$i--){
	   echo $users[$i];
	}
?>