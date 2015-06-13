<?PHP
	include('connect.php');
	session_start();

	//Delete the user that left
	$result = mysqli_query($conn, "DELETE FROM google_users WHERE google_name='".$_SESSION['username']."' LIMIT 1");

	session_destroy();
	
	header('Location: http://www.leonelserra.net/chat/');
?>