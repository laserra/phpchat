<?php

########## Google Settings.. Client ID, Client Secret from https://cloud.google.com/console #############
$google_client_id 		= 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';
$google_client_secret 	= 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';
$google_redirect_url 	= 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';
$google_developer_key 	= 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';

include('connect.php')

//include google api files
require_once 'src/Google_Client.php';
require_once 'src/contrib/Google_Oauth2Service.php';

//start session
session_start();

$gClient = new Google_Client();
$gClient->setApplicationName('Login to LeonelSerra.net/chat');
$gClient->setClientId($google_client_id);
$gClient->setClientSecret($google_client_secret);
$gClient->setRedirectUri($google_redirect_url);
$gClient->setDeveloperKey($google_developer_key);

$google_oauthV2 = new Google_Oauth2Service($gClient);

//If user wish to log out, we just unset Session variable
if (isset($_REQUEST['reset'])) 
{
  unset($_SESSION['token']);
  $gClient->revokeToken();
  header('Location: ' . filter_var($google_redirect_url, FILTER_SANITIZE_URL)); //redirect user back to page
}

//If code is empty, redirect user to google authentication page for code.
//Code is required to aquire Access Token from google
//Once we have access token, assign token to session variable
//and we can redirect user back to page and login.
if (isset($_GET['code'])) 
{ 
	$gClient->authenticate($_GET['code']);
	$_SESSION['token'] = $gClient->getAccessToken();
	header('Location: ' . filter_var($google_redirect_url, FILTER_SANITIZE_URL));
	return;
}


if (isset($_SESSION['token'])) 
{ 
	$gClient->setAccessToken($_SESSION['token']);
}


if ($gClient->getAccessToken()) 
{
	  //For logged in user, get details from google using access token
	  $user 				= $google_oauthV2->userinfo->get();
	  $user_id 				= $user['id'];
	  $user_name 			= filter_var($user['name'], FILTER_SANITIZE_SPECIAL_CHARS);
	  $email 				= filter_var($user['email'], FILTER_SANITIZE_EMAIL);
	  $profile_url 			= filter_var($user['link'], FILTER_VALIDATE_URL);
	  $profile_image_url 	= filter_var($user['picture'], FILTER_VALIDATE_URL);
	  $profile_color        = '#' . strtoupper(dechex(rand(0,10000000)));
	  $personMarkup 		= "$email<div><img src='$profile_image_url?sz=50'></div>";
	  $_SESSION['token'] 	= $gClient->getAccessToken();
}
else 
{
	//For Guest user, get google login url
	$authUrl = $gClient->createAuthUrl();
}

//HTML page start


if(isset($authUrl)) //user is not logged in, show login button
{
	echo '<!DOCTYPE HTML><html>';
	echo '<head>';
	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
	echo '<title>PHP Chat **beta**</title>';
	echo '<script src="jquery/jquery-2.1.4.js" type="text/javascript"></script>';
    echo '<script type="text/javascript" src="bootstrap/js/bootstrap.js"></script>';
    echo '<link rel="stylesheet" href="bootstrap/css/bootstrap.css" type="text/css" media="screen">';
    echo '<link rel="stylesheet" href="bootstrap/css/bootstrap-theme.css" type="text/css" media="screen">';
    echo '<link rel="stylesheet" href="css/chatstyles.css" type="text/css" media="screen">';
	echo '</head>';
	echo '<body>';
	echo '<header>';
	echo '<div class="container-fluid">';
	echo '<div class="row">';
	echo '<div class="col-lg-12 text-center page-header" >';
	echo '<div class="row row-centered">';
	echo '<div class="col-lg-12 col-centered">';
	echo '<h1>Login with Google</h1>';
	echo '<a class="login" href="'.$authUrl.'" ><img src="images/Red-signin_Long_base_32dp.png" /></a>';
	echo '</div>';
	echo '</div>';
	echo '</div>';
	echo '</div>';
	echo '</div>';
	echo '</header>';
} 
else // user logged in 
{
   /* connect to database using mysqli */
	$mysqli = new mysqli($hostname, $db_username, $db_password, $db_name);
	
	if ($mysqli->connect_error) {
		die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
	}
	
	//compare user id in our database
	$user_exist = $mysqli->query("SELECT COUNT(google_id) as usercount FROM google_users WHERE google_id=$user_id")->fetch_object()->usercount; 
	if($user_exist)
	{
		session_start();
		$_SESSION['login'] = "1";
		$_SESSION['username'] = $user_name;
		$_SESSION['picture'] = $profile_image_url;
		
		//echo 'Welcome '.$user_name.'!';
		header('Location: http://www.leonelserra.net/chat/chat.php');
	}else{ 
		//user is new
		//echo 'Hi '.$user_name.', Thanks for Registering!';
		$mysqli->query("INSERT INTO google_users (google_id, google_name, google_email, google_link, google_picture_link, google_color) 
		VALUES ($user_id, '$user_name','$email','$profile_url','$profile_image_url','$profile_color')");
		
		session_start();
		$_SESSION['login'] = '';

		header('Location: http://www.leonelserra.net/chat/chat.php');
	}
}
echo '</body></html>';
?>