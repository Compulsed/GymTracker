<?php


 if(!isset($_SESSION['myuserid'])){
 	header("Location: /mobile.php");
 }

if(isset($_COOKIE['user_id']))
	setcookie("user_id", "", time()-3600);

if(isset($_COOKIE['keep_logged_in']))
	setcookie("keep_logged_in", "", time()-3600);


 //you can remove a single variable in the session 
 unset($_SESSION['myuserid']);
 
 // or this would remove all the variables in the session, but not the session itself 
 session_unset(); 
 
 // this would destroy the session variables 
 session_destroy(); 

?>

<p style="font-size: 50px;">
    <?php echo "Logging out"; ?> 
</p>


<script type="text/javascript">
	setTimeout("window.location='/mobile.php'",1000);
</script>