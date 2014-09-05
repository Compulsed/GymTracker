<?php
    require_once "inc/config.php";
	require_once "inc/functions.php";
	require_once "inc/parts/header.php";
    



    //Allows the user to type his or her email in to recover their accounts
    if( isset($_GET['forgotten']) )
        require "inc/registration_recovery/email_recovery.php";
    
    //Checks to see if there is a registration token in the url
    else if(isset($_GET['reg_token']))
        require "inc/registration_recovery/register_user_from_token.php";

    //Checks to see if there is a signed_up message set, meaning the account has been made
    else if( isset($_GET['signed_up']) )
        require "inc/registration_recovery/signed_up_message.php";

    //The message that there has been an email sent to the user so they can recover their passwords
    else if( isset($_GET['email_sent']) )
        require "inc/registration_recovery/email_sent.php";

    //Checks to see if there is a recovery password token in the header, if there is offer recovery options
    else if( isset($_GET['token']) )
        require "inc/registration_recovery/email_select_password.php";

    //The message to say the account has been recovered
    else if( isset($_GET['recovered']))
        require "inc/registration_recovery/email_recovered.php";

    //The page where the user can sign up to the website, if logged in redirect to index.php
    else if(!isset($_SESSION['myuserid']) ) 
        require "inc/registration_recovery/sign_up_page.php";
    else 
        header("Location: /");

    
	 require_once "inc/parts/footer.php";
?>
