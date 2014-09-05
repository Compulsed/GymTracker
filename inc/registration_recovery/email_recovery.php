<?php
  $conn = connect($config['db']);
  if( !$conn ) die("Could not connect to DB");
?>

<div class="page">
	<div class="information">
		<h1>Password recovery</h1>
		<div class="help">
			Enter your email address associated with the account you wish to recover.<br>
			Please also check your spam box for the email.
		</div>
		<p style="display:inline;">Email address:</p>
		<form method="post" style="display:inline;">
			<input type="text" name="email_address" value="">
			<input type="submit" value="Submit">
		</form>
		<p>
			<?php 
				if ( isset($_POST['email_address']) ) {
					$status = check_email_recovery($conn, $_POST['email_address'], 5);
					if($status['completed'] == true)
						header('Location: register.php?email_sent=' . $_POST['email_address']);
					else
						echo "<div id=\"notify\" class=\"error\">" . $status['message'] .  "</div>";
				}  
			?>
		</p>
	</div>
</div>

