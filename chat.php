<?PHP

	session_start();

	$now = time();
	if (isset($_SESSION['discard_after']) && $now > $_SESSION['discard_after']) {
	    // this session has worn out its welcome; kill it and start a brand new one
	    session_unset();
	    session_destroy();
	    session_start();
	}

	// either new or old, it should live at most for another hour
	$_SESSION['discard_after'] = $now + 3600;


	if (!(isset($_SESSION['login']) && $_SESSION['login'] != '')) {

		header ("Location: index.php");

	}

?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>PHP Chat **beta**</title>
    <script src="jquery/jquery-2.1.4.js" type="text/javascript"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.js"></script>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.css" type="text/css" media="screen">
    <link rel="stylesheet" href="bootstrap/css/bootstrap-theme.css" type="text/css" media="screen">
    <link rel="stylesheet" href="css/chatstyles.css" type="text/css" media="screen">
</head>

<body id="page-top" class="index">

<!-- Navigation -->
    <nav class="navbar navbar-default navbar navbar-inverse navbar-fixed-top">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header page-scroll">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <div id="userbadge">
                    <a class="navbar-brand" href="#page-top"><?PHP echo $_SESSION['username']?></a>
                    <?PHP echo "<img src=".$_SESSION['picture']." width='40' height='40' style='margin-top: 5px;'>" ?>
                </div>
                
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right">
                    <li class="hidden">
                        <a href="#page-top"></a>
                    </li>
                    <li class="page-scroll">
                        <a href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container-fluid -->
    </nav>
    
    <!-- Message section -->
    <header style="margin-right: auto;  margin-left: auto;">
    	<section id="thechat">
	        <div class="container-fluid marketing">
	                <div class="row row-centered" style="margin-top:50px; margin-left:0px; margin-right:0px;">
	            		
	            		<div class="col-md-12 text-center page-header">
	                        <h2>Message</h2>
	                        <hr class="star-primary">
	                    </div>

		            		<div class="col-md-6 col-sm-6">
		                    	<div id="chat" >
		                            <!-- style="width:500px;margin:0 auto;overflow:hidden;"This div will contain the messages -->
		                            <div id="message_list"></div>
		                            <!--This div will contain an eventual error messages-->
		                            <div id="error" style="width:500px; text-align:center; color:red;"></div>
		                            <!--This div contains the forms and the send button-->
		                            <div id="write">
		                                <textarea id="write_message" cols="50" rows="2"></textarea>
		                                <br/>
		                                <input type="button" value="Send" onClick="send();" class="btn btn-primary"/>
		                            </div>
		                        </div>
		                     </div>

		                    <div class="col-md-6 col-sm-6">
		                    	<div id="users">
		                            <!-- This div will contain the active users -->
		                            <div id="users_list"></div>
		                        </div>
		                    </div>
		            </div>
	        </div>
        </section>
    </header>

<script type="text/javascript">

	$("#write_message").keyup(function(e){
	    var code = (e.keyCode ? e.keyCode : e.which);
		if(code == 13) { //Enter keycode
			send();
	 	}
	});

	//This function will display the messages
	function showmessages(){
		
		$.ajax({
			url: "show-messages.php"
			, success: function(result){$("#message_list").html(result);}
			, error: function(xhr){alert("An error occured while fetching: "+xhr.status+" "+xhr.statusText);}
			});
		
	   //Repeat the function each 30 seconds
	   setTimeout('showmessages()',1000);
	};

	//Scroll messages so that you always see the most recent
	function scrollup(){
		$(function() {
		  var msgscr = $('#message_list');
		  var height = msgscr[0].scrollHeight;
		  msgscr.scrollTop(height);
		});
	}

	function showusers(){
		$.ajax({
			url: "getusers.php"
			, success: function(result){ $("#users_list").html(result);}
			, error: function(xhr){alert("An error occured while fetching users: "+xhr.status+" "+xhr.statusText);}
		});

		setTimeout('showusers()',10000);
	};
	
	

//This function will submit the message
function send(){
	   //Send an Ajax to the 'send.php' file with all the required informations
	   var sendto = 'send.php?message=' + document.getElementById('write_message').value + '&name=' + <?php echo
str_replace(" ", "_", json_encode($_SESSION['username'])); ?>;

	   var error = '';
	   
	   $('#write_message').val('');

	   $.ajax({
	   	url: sendto,
	   	success: function(result){error = result;},
	   	error: function(xhr){alert("An error occured while sending: "+xhr.status+" "+xhr.statusText);}
	   });
	   
	   //If an error occurs the 'send.php' file send`s the number of the error and based on that number a message is displayed
	   switch(error){
	   case 1:
		  error = 'The database is down!';
		  break;
	   case 2:
		  error = 'The database is down!';
		  break;
	   case 3:
		  error = 'Don`t forget the message!';
		  break;
	   case 4:
		  error = 'The message is too long!';
		  break;
	   case 5:
		  error = 'Don`t forget the name!';
		  break;
	   case 6:
		  error = 'The name is too long!';
		  break;
	   case 7:
		  error = 'This name is already used by somebody else!';
		  break;
	   case 8:
		  error = 'The database is down!';
	   }

	   if(error == ''){
		  document.getElementById('error').innerHTML = '';
		  showmessages();
	   }
	   else{
		  document.getElementById('error').innerHTML = error;
	   }

	   showmessages();

	   setTimeout('scrollup()',1000);
}

showusers();

//Start the showmessages() function
showmessages();

//small delay to give time to fetch messages and then scroll down at the beggining
setTimeout('scrollup()',1000);

</script>

</body>
</html>