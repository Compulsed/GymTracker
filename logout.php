<?php

 // you have to open the session to be able to modify or remove it 
 require_once 'inc/parts/header.php';

 if(!isset($_SESSION['myuserid'])){
 	header("Location: /");
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

<div class="page">
	<div class="information">
            <div class="help" style="text-align:center;">
            	Logging out
            </div>
        </div>
</div>

<?php require_once 'inc/parts/footer.php'; ?>

<script type="text/javascript">
setTimeout("window.location='/articles'",1000);
</script>