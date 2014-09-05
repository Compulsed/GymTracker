<?php
	$conn = connect($config['db']);
	if( !$conn ) die("Could not connect to DB");

	$user_id = $_SESSION['myuserid'];

	$user_row = user_information_row($conn, $user_id);

    extract($user_row);

	// function check_user_name_values(){

	// }

	// function check_name_values(){

	// }

    //Names
    /*

	email, repeat email, email_password
	password_password, password, 
	username, repeat_username, username_password
	first_name, last_name, name_password

    */


	$email_status = check_email_values($conn, $userId, 'email', 'repeat_email', 'email_password');
	$username_status = check_username_status($conn, $userId, 'username', 'repeat_username', 'username_password');
	$password_status = check_password_values($conn, $userId, 'password', 'repeat_password', 'password_password');

	if($email_status['completed'] || $password_status['completed'] || $username_status['completed']){
		$get_status = "";
		$a_post = false;

		if(!empty($email_status['message'])){
			if($a_post == true){ 
				$get_status .=  "&";
			}
			$get_status .= "email=" . $email_status['message'];
			$a_post = true;
		}

		if(!empty($password_status['message'])){
			if($a_post == true){ 
				$get_status .=  "&";
			}
			$get_status .= "password=" . $password_status['message'];
			$a_post = true;
		}

		if(!empty($username_status['message'])){
			if($a_post == true){ 
				$get_status .=  "&";
			}
			$get_status .= "username=" . $username_status['message'];
			$a_post = true;
		}

		header("Location: profile.php?edit_message&" . $get_status);
	}



?>



<div class="page">
    <div class="information">
    	<div class="title_nav">
	    	<h1 style="display:inline;">Profile of: <?php echo ucfirst($user_row['firstName']) . ' ' . ucfirst($user_row['lastName']); ?></h1>
	    	<p style="display:inline;"><a href="profile.php">(back)</a></p>
	    </div>


<form method="post">
	
	<!-- EMAIL -->
    <div id="email_button" class="selection_row">
     <p>Change Email</div>
	<div id="email_form" style="display:none;">
		<h1>Change Email addresses</h1>
	    <div class="register" style="width: 40%">
		    <table>
		        <tr>
		            <td class="left_col">*New Email</td>
		            <td class="right_col"><input type="text" name="email" value=""/></td>
		        </tr>

		        <tr>
		            <td class="left_col">*Repeat Email</td>
		            <td class="right_col"><input type="text" name="repeat_email" value=""/></td>
		        </tr>

		        <tr>
		            <td class="left_col">*Current Password</td>
		            <td class="right_col"><input type="password" name="email_password" value=""/></td>
		        </tr>

		    </table>
	    </div>
	</div>
	
	<!-- Password -->
     <div id="password_button" class="selection_row">
     	<p>Change Password</p></div>
	<div id="password_form" style="display:none;">
		<h1>Change Password</h1>
	    <div class="register" style="width: 40%">
		    <table>
		 		<tr>
		            <td class="left_col">*Current password</td>
		            <td class="right_col"><input type="password" name="password_password" value=""/></td>
		        </tr>

		        <tr>
		            <td class="left_col">*New password</td>
		            <td class="right_col"><input type="password" name="password" value=""/></td>
		        </tr>

		        <tr>
		            <td class="left_col">*Repeat Password</td>
		            <td class="right_col"><input type="password" name="repeat_password" value=""/></td>
		        </tr>
		    </table>
	    </div>
	</div>

	<!-- Username -->
    <div id="username_button" class="selection_row">
     <p>Change Display name</p></div>
	<div id="username_form" style="display:none;">
		<h1>Change Display name</h1>
	    <div class="register" style="width: 40%">
		    <table>
		        <tr>
		            <td class="left_col">*Username</td>
		            <td class="right_col"><input type="text" name="username" value=""/></td>
		        </tr>

		        <tr>
		            <td class="left_col">*Repeat Username</td>
		            <td class="right_col"><input type="text" name="repeat_username" value=""/></td>
		        </tr>

		 		<tr>
		            <td class="left_col">*Current password</td>
		            <td class="right_col"><input type="password" name="username_password" value=""/></td>
		        </tr>
		    </table>
	    </div>
	</div>	

	<!-- Name -->
	<div id="name_button" class="selection_row"><p>Change Name</p></div>
	<div id="name_form" style="display:none;">
		<h1>Change name</h1>
	    <div class="register" style="width: 40%">
		    <table>
		        <tr>
		            <td class="left_col">*First Name</td>
		            <td class="right_col"><input type="text" name="first_name" value=""/></td>
		        </tr>

		        <tr>
		            <td class="left_col">*Last Name</td>
		            <td class="right_col"><input type="text" name="last_name" value=""/></td>
		        </tr>

		 		<tr>
		            <td class="left_col">*Current password</td>
		            <td class="right_col"><input type="password" name="name_password" value=""/></td>
		        </tr>
		    </table>
	    </div>
	</div>

	<input type="submit" value="Sumbit changes" id="change_button" />

</form>


    </div>
</div>

<script>

$("#email_button").on("click", function(){
	$("#email_form").toggle("slow");
});

$("#password_button").on("click", function(){
	$("#password_form").toggle("slow");
});

$("#username_button").on("click", function(){
	$("#username_form").toggle("slow");
});

$("#name_button").on("click", function(){
	$("#name_form").toggle("slow");
});



</script>