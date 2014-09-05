<?php

    ob_start();
    session_start();

    require_once "inc/config.php";
    require_once "inc/functions.php";

    $conn = connect($config['db']);
    if( !$conn ) die("Could not connect to DB");

      //Check if there is a login cookie, if there is just skip to the input
      if( isset($_COOKIE['user_id']) && isset($_COOKIE['keep_logged_in']) ){

        $cookie_user_id = $_COOKIE['user_id'];

        $user_rows = $conn->prepare("SELECT * FROM tbl_users WHERE userId = :userId");
        $user_rows->bindParam(":userId", $cookie_user_id, PDO::PARAM_INT);
        $user_rows->execute();
        $user_rows = $user_rows->fetchALL(PDO::FETCH_ASSOC);

        if(count($user_rows) > 0){
            $user_row = $user_rows[0];

            $Blowfish_Pre = '$2a$05$';
            $Blowfish_End = '$';
            $cookie_hash = crypt($user_row['loginPassword'], $Blowfish_Pre . $cookie_user_id . $Blowfish_End);
            
            if($_COOKIE['keep_logged_in'] == $cookie_hash){
                $_SESSION['myuserid'] = $_COOKIE['user_id'];
            }
        } 
      }


    require_once "inc/mobile/mobile_header.php";

    //Loads the required page corrosponding the the gobal variables

    //If a userid isn't set, redirect to the login page
    if(isset($_SESSION['myuserid'])){ 
        
        if(isset($_GET['logout']))
            require_once "inc/mobile/mobile_logout.php";
        else if(isset($_GET['routine_name']))
            require_once "inc/mobile/mobile_weight_input.php";
        else
            require_once "inc/mobile/mobile_routine_selector.php";

    } else {

        if(isset($_POST['myemail']) && isset($_POST['mypassword']) ){
            require_once "inc/mobile/mobile_check_login.php";
        } else {
            require_once "inc/mobile/mobile_login_page.php";
        }
    }

?>
