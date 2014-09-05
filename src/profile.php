<?php
    require_once "inc/config.php";
	require_once "inc/functions.php";
    require_once "inc/parts/header.php";

    //If a userid isn't set, redirect back to home
    if(isset($_SESSION['myuserid'])){ 
 		
        if(isset($_GET['edit']))
            require "inc/user/edit.php";                                 //Allows the user to change their details.
        else if(isset($_GET['admin']) && ($_SESSION['myuserid'] == 1) )  //If the first user(admin account) is logged in, they will be presented with admin options
            require "inc/admin/admin_commands.php";
        else if(isset($_GET['edit_message']))
            require "inc/user/edit_message.php";
        else if(isset($_GET['user_data_back']))
            require "inc/user/user_data.php";
        else
            require "inc/user/profile_page.php"; // default page  	
   
    } else {
    	header("Location: /");
    }

    require_once "inc/parts/footer.php";
?>
