<?php
  $conn = connect($config['db']);
  if( !$conn ) die("Could not connect to DB");

  if(isset($_GET['token'])){
  	$token_string = $_GET['token'];
  }

  $token_status = check_if_recovery_hash($conn, $token_string, 15);


  if($token_status['valid']){
  	if( isset($_POST['pass1']) && isset($_POST['pass2'])){
  		//Sending the wrong userid?
  		$password_status = check_new_passwords_and_update($conn, $token_status['user_id'], $_POST['pass1'], $_POST['pass2']);
  	
  	} else {	
	  $password_status = array(
	  	 "completed" => false,
	  	 "message" => ""
	  );
  	}

  	//Extracts the user ID from the token(last value)
	$hash_length = strlen("b35074ee5716389067de90ea8ca6d4d655d6f5ed");
	$time_length = strlen("51f7883e");
	$user_id = substr($token_string, $hash_length + $time_length);
	$stmt = $conn->query("SELECT username FROM tbl_users WHERE userId = '$user_id'");
	$user_rows = $stmt->fetchALL(PDO::FETCH_ASSOC);
	$username = $user_rows[0]['username'];
  } 


?>




<div class="page">
    <div class="information">

    	<!-- If token is not valid -->
		<?php if(!$token_status['valid']) : ?>
		<div class="help">
			<?php echo $token_status['message'] ?><br>
		</div>
		<?php endif ?>

		<!-- Do if the token is valid -->
		<?php if($token_status['valid']) : ?>
			<div class="title_nav">
	        	<h1>Set new password</h1>
	    	</div>
			<form method="post">
				<h2>Enter the matching passwords for: <?php echo $username; ?></h2>
				<p>First password</p>
				<input style="width:200px" type="password" name="pass1">
				<p>Second password</p>
				<input style="width:200px" type="password" name="pass2"><br>
				<input type="submit" value="sumbit">
			</form>	

			<?php if($password_status['completed'])
					header('Location: register.php?recovered');
				  else 
				  	echo $password_status['message'];
			?>
		<?php endif ?>

    </div>
</div>