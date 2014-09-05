<?php 
  /* ------------------
  : EMAIL FUNCTIONS
   ------------------- */

  //  
  // -- forgotten_password
  // 

  //Works - Antipattern

  /*
    Sends an email to the registered email address with a link to recover the account.
  */
  function check_email_recovery($conn, $email, $minutes_before_another = 5){
    $return_array = array(
      "completed" => false,
      "message" => ""
      );

    //Checks to see if the email is valid
    $return_status = check_email($conn, $email, true);
    if(!$return_status['is_valid']){
      if(!$return_status['check_array']['valid_email_string']){
        $return_array['message'] = "Invalid Email";
        return $return_array;  
      } else {
        $return_array['message'] = "Email not found";
        return $return_array;
      }
    }

    $query = $conn->query("SELECT * FROM tbl_users WHERE emailAddress = '$email'");
    $query = $query->fetchALL(PDO::FETCH_ASSOC);
    $user_row = $query[0];

    extract($user_row);

    //See that the user had click the register button and posts an email
    if( !((strtotime($recovEmailSentTime) + 5*$minutes_before_another ) < time())){
      $return_array['message'] = "Please wait another " . ((strtotime($recovEmailSentTime) + 60*$minutes_before_another ) - time()) . " seconds before sending another email to this address!";
      return $return_array;
    }

    $to = $email;
    $subject = "New password";
    $message = "Your username is: " . $user_rows[$row_number]['username'];
    $message .="\nLink to reset your password: http://" . $_SERVER['SERVER_NAME'] . "/register.php?token=" . sha1( $userId . time() . $loginPassword . $salt ) . dechex(time()) . $userId;
    $message .="\nThis token will only last 15 mintes, so be quick!";

    $headers = "From: noreply@stories-of-a-hobbyist.com" . "\r\n" . "CC: '$to'";
    
    if(mail($to, $subject, $message, $headers)){
      $conn->query("UPDATE tbl_users SET recovEmailSentTime = NOW() WHERE userId = $userId"); //Flags as having sent an email
      $return_array['message'] = "Email sent to email";
      $return_array['completed'] = true;
    } else {
      $return_array['message'] = "Unknown error: unable to send email to " . $emailAddress;
    }

    return $return_array;
  }



  /* 
    Gives default values to a newly registered users, if selected. These values are the default ones in the database
  */

  function give_defaults_to($conn, $userId){
    //exercises

    $all_template_exercises = $conn->prepare("SELECT * FROM tbl_exercises WHERE defaultExercise = TRUE");
    $all_template_exercises->execute();
    $template_exercise_rows = $all_template_exercises->fetchALL(PDO::FETCH_ASSOC);

    // All of the transferable rows
    $num_of_inputs = 0;
    foreach($template_exercise_rows as $row){
      $name = $row['name'];
      $description = $row['description'];
      $reps = $row['reps'];
      $sets = $row['sets'];
      $muscleGroup = $row['muscleGroup'];

      $sql = "INSERT INTO tbl_exercises(userIdF, share, name, sets, reps, description, muscleGroup) VALUES('$userId', 0, '$name', '$sets', '$reps', '$description', '$muscleGroup');";
      $conn->query($sql);
      $num_of_inputs++;
    }

    //share

    $all_template_routines = $conn->prepare("SELECT * FROM tbl_routines WHERE defaultRoutine = TRUE ORDER BY routineName");
    $all_template_routines->execute();
    $all_template_routines = $all_template_routines->fetchALL(PDO::FETCH_ASSOC);

    //IN: NAME, EXERCISE ID

    foreach($all_template_routines as $routine){
        $temp_routine_name = $routine['routineName'];


        // N(exerciseIdF) -> T(ExerciseId -> ExerciseName) ->N()
        //Find the exerciseIdF in the template values.
        $exerciseIdF = $routine['exerciseIdF']; //templates

        //Find the name accociated with that record
        $exercise_rows = $conn->query("SELECT * FROM tbl_exercises WHERE exerciseId = $exerciseIdF");
        $exercise_rows = $exercise_rows->fetchALL(PDO::FETCH_ASSOC);
        $exercise_name = $exercise_rows[0]['name'];

        //Find that same exercise name in the newly added values.
        $same_exercise_name_rows = $conn->query("SELECT * FROM tbl_exercises WHERE name = '$exercise_name' AND defaultExercise = true");
        $same_exercise_name_rows = $same_exercise_name_rows->fetchAll(PDO::FETCH_ASSOC);
        $same_exercise_name = $same_exercise_name_rows[0]['name'];
        
        //Find the exerciseId in those values.
        $same_exercise_rows = $conn->query("SELECT * FROM tbl_exercises WHERE name = '$same_exercise_name' AND userIdF = $userId");
        $same_exercise_rows = $same_exercise_rows->fetchAll(PDO::FETCH_ASSOC);
        $same_exerciseIdF = $same_exercise_rows[0]['exerciseId'];
        //Attack it to the new routine.

        $conn->query("INSERT INTO tbl_routines(routineName, exerciseIdF, userIdF) VALUES('$temp_routine_name', $same_exerciseIdF, $userId)");
    }

  }

    //LIVE 
    function get_ip_address(){
      foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
          if (array_key_exists($key, $_SERVER) === true){
              foreach (explode(',', $_SERVER[$key]) as $ip){
                  $ip = trim($ip); // just to be safe

                  if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
                      return $ip;
                  } else {
                    return $_SERVER['REMOTE_ADDR']; //Remove this when LIVE
                  }
              }
          }
      }
  }

  /*
  Sends an email that allows the user to register their email address with the sever, also removes the message on 
  the user's profile
  */
  function email_registraion($conn, $email, $minutes_before_another = 5){
    $return_array = array(
      "completed" => false,
      "message" => ""
      );

    $return_status = check_email($conn, $email, true);
    if(!$return_status['is_valid']){
      if(!$return_status['check_array']['valid_email_string']){
        $return_array['message'] = "Invalid Email";
        return $return_array;  
      } else {
        $return_array['message'] = "Email not found";
        return $return_array;
      }
    }

    $query = $conn->query("SELECT * FROM tbl_users WHERE emailAddress = '$email'");
    $query = $query->fetchALL(PDO::FETCH_ASSOC);
    $user_row = $query[0];

    extract($user_row);

    if($registeredEmail){
      $return_array['completed'] = true;
      $return_array['message'] = "Is already registered";
      return $return_array;
    }

    //See that the user had click the register button and posts an email
    if( !((strtotime($regEmailSentTime) + 5*$minutes_before_another ) < time())){
      $return_array['message'] = "Please wait another " . ((strtotime($regEmailSentTime) + 60*$minutes_before_another ) - time()) . " seconds before sending another email!";
      return $return_array;
    }

    $to = $email;
    $subject = "Register your email for GymTracker";
    $message = "Your username is: " . ucfirst($username);
    $message .="\nClick this to register your email http://" . $_SERVER['SERVER_NAME'] . "/register.php?reg_token=" . sha1( $userId . $loginPassword . $salt ) .  $userId;

    $headers = "From: noreply@stories-of-a-hobbyist.com" . "\r\n" . "CC: '$to'";
    
    if(mail($to, $subject, $message, $headers)){
      $conn->query("UPDATE tbl_users SET regEmailSentTime = NOW() WHERE userId = $userId"); //Flags as having sent an email
      $return_array['message'] = "Email sent to registered Email";
      $return_array['completed'] = true;
    } else {
      $return_array['message'] = "Unknown error: unable to send email to " . $emailAddress;
    }

    return $return_array;
  }


  // TODO FIX THE CHECK BOX, and deal with kilos corrently
  // TODO clean up the function
  function check_register_information($conn){
    //Make sure the usernames are not the same.
      $return_information = array(
            "completed" => false,
            "message" => "",
            "username" => "",
            "firstName" => "",
            "email" => "",
      );

    if( !isset($_POST['user']) 
        && !isset($_POST['email']) 
        && !isset($_POST['password1']) 
        && !isset($_POST['first']) 
        && !isset($_POST['last']) 
        && !isset($_POST['password2']))         
          return $return_information;

      
      if($_POST['is_kilo'] == true){
        $is_kilo = true;
      }  else {
        $is_kilo = false;
      }


      $row_query = $conn->prepare("SELECT username, emailAddress FROM tbl_users"); // Returns _all_ of the stored IDs
      $row_query -> execute();
      $usernames = $row_query->fetchALL(PDO::FETCH_ASSOC);
      
      if( empty($_POST['user']) ){  
        $return_information['message'] = "Must have a username"; 
        return $return_information;
      }

      if( empty($_POST['email']) ){
          $return_information['message'] = "Must have an email"; 
          return $return_information;
      }

      if( empty($_POST['first']) ){
        $return_information['message'] = "Must have a first name"; 
        return $return_information;
      }

      if( empty($_POST['last']) ){
        $return_information['message'] = "Must have a last name"; 
        return $return_information;
      }
      
      $user_input = strtolower(strip_tags($_POST['user']));
      $user_email = strip_tags($_POST['email']);
      $first = strip_tags($_POST['first']);
      $last = strip_tags($_POST['last']);

      if( (strlen($user_input) < 5) || (strlen($user_input) > 15 )){ 
        $return_information['message'] = "Username must be greater than 4 characters and less than 15"; 
        return $return_information;
      }


      if (preg_match("/\\s/", $user_input)){
        $return_information['message'] = "Username must not have any spaces";
        return $return_information;
      } 
      
    //Checks if the username and email don't match any other in the database
    foreach($usernames as $username){
      if($user_input == $username['username']){
        $return_information['message'] = "Username has been taken"; 
        return $return_information;
      }
    }

    //Checks if the email is valid
    $return_array = check_email($conn, $user_email, false);
    if(!$return_array['is_valid']){
      if(!$return_array['check_array']['valid_email_string']){

        $return_information['message'] = "Invalid Email";
        return $return_information;  

      } else {

        $return_information['message'] = "Email found";
        return $return_information;

      }
    }


    $user_pass = $_POST['password1'];
    //Make sure the password is greater than 5 characters
    // if( (strlen($user_pass) < 8) && (strlen($user_pass) > 30 )){
    //  $return_information['message'] = "Password: greater than 8 characters less than 30"; 
    //  return $return_information;
    // }

    if(!check_password($user_pass)['is_valid']){
      $return_information['message'] = "Password: Must be greater than 7 characters less than 30";
    }
    
    if( $user_pass != $_POST['password2']){
      $return_information['message'] = "Passwords must match"; 
      return $return_information;
    }
    
    /* 
      : SALTING
    */

        CRYPT_BLOWFISH or die ('No Blowfish found.');

        //run for 5 seconds
        $Blowfish_Pre = '$2a$05$';
        $Blowfish_End = '$';
        //characters which will be accepted
        $Allowed_Chars ='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789./';
        $Chars_Len = 63;
        //len of salt
        $Salt_Length = 21;

        $mysql_date = date( 'Y-m-d' );
        $salt = "";

        for($i=0; $i<$Salt_Length; $i++){
            $salt .= $Allowed_Chars[mt_rand(0,$Chars_Len)];
        }
        $bcrypt_salt = $Blowfish_Pre . $salt . $Blowfish_End;
        $hashed_password = crypt($user_pass, $bcrypt_salt);

    /* 
      : end SALTING
    */



    $sql = "INSERT INTO tbl_users(username, loginPassword, joined, firstName, lastName, emailAddress, adminLevel, isKilo, lastTimeActive, salt, registeredIP)" .
             "VALUES(:user_input, :user_pass, now(), :first, :last, :email, 0, :is_kilo, now(), :salt, :registeredIP)";

    $query = $conn->prepare($sql);
    $query->bindParam(':user_input', $user_input, PDO::PARAM_STR);
    //$query->bindParam(':user_pass', $user_pass, PDO::PARAM_STR);
    $query->bindParam(':first', $first, PDO::PARAM_STR);
    $query->bindParam(':last', $last, PDO::PARAM_STR);
    $query->bindParam(':email', $user_email, PDO::PARAM_STR);
    $query->bindParam(':is_kilo', $is_kilo, PDO::PARAM_INT);

    $users_ip_address = get_ip_address();

    $query->bindParam(':registeredIP', $users_ip_address, PDO::PARAM_STR);

    $query->bindParam(':user_pass', $hashed_password, PDO::PARAM_STR);
    $query->bindParam(':salt', $salt, PDO::PARAM_STR);

    if(!$query->execute()){
      return $return_information['message'] = "Failed to write values to the database"; 
    }

    //Sends an email out to the users email
    email_registraion($conn, $user_email, 0);

    //If the give default values flag has been set, give the user the default values
    $last_user_id = $conn->prepare("SELECT userId, username FROM tbl_users ORDER BY userId DESC LIMIT 1");
    $last_user_id -> execute();
    $last_id_row = $last_user_id ->fetchALL(PDO::FETCH_ASSOC);
    $user_id = $last_id_row[0]['userId'];

    if($_POST['default_values'] == true){
        give_defaults_to($conn, $user_id);
    }
    
    //The user has been registered, return true
    $return_information['completed'] = true;
    $return_information['message'] = "Username is registered!";
    $return_information['username'] = $user_input;
    $return_information['firstName'] = $first;
    $return_information['email'] = $user_email;
    
    return $return_information;
  }

  /*
    See if the registraion token is valid 
  */
  function check_if_registration_hash($conn, $token_string){
    $return_array = array(
      "valid" => false,
      "message" => "",
      "user_id" => ""
    );

    if(empty($token_string)){
      $return_array['message'] = "Invalid registration token";
      return $return_array;
    }

    //Hash, time, userid
    //b35074ee5716389067de90ea8ca6d4d655d6f5ed-51f7883e-1 Token template 
    $hash_length = strlen("b35074ee5716389067de90ea8ca6d4d655d6f5ed");

    //Gets the hash part of the token
    $sent_hash = substr($token_string, 0, $hash_length);
    //Gets the user id part of the token
    $sent_user_id = substr($token_string, $hash_length);

    $stmt = $conn->prepare("SELECT * FROM tbl_users WHERE userId = :sent_user_id");
    $stmt->bindParam(':sent_user_id', $sent_user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user_rows = $stmt->fetchALL(PDO::FETCH_ASSOC);


    $user_row = $user_rows[0];
    extract($user_row);

    if($sent_hash == sha1( $userId . $loginPassword . $salt ) ){
      $return_array['message'] = "Hashes match";
      $return_array['valid'] = true;
      $return_array['user_id'] = $sent_user_id;

      $conn->query("UPDATE tbl_users SET registeredEmail = TRUE WHERE userId = $sent_user_id");
    } else {
      $return_array['message'] = "The registration token is invalid";
      $return_array['valid'] = false;
    }

    return $return_array;
  }


  function check_if_recovery_hash($conn, $token_string, $time = 15){
    $return_array = array(
      "valid" => false,
      "message" => "",
      "user_id" => ""
    );

    if(empty($token_string)){
      $return_array['message'] = "Invalid token";
      return $return_array;
    }

    //Hash, time, userid
    //b35074ee5716389067de90ea8ca6d4d655d6f5ed-51f7883e-1 Token template 
    $hash_length = strlen("b35074ee5716389067de90ea8ca6d4d655d6f5ed");
    $time_length = strlen("51f7883e");

    //Gets the time part of the token
    $sent_time = hexdec( substr($token_string, $hash_length, $time_length) );
    //Gets the hash part of the token
    $sent_hash = substr($token_string, 0, $hash_length);
    //Gets the user id part of the token
    $sent_user_id = substr($token_string, $hash_length + $time_length);

    // 5 Minutes
    if( ($sent_time + 60*$time) < time()){
      $return_array['message'] = "Token have expired, as it's over an " . $time . " minutes old!";
      return $return_array;
    }

    $stmt = $conn->query("SELECT * FROM tbl_users WHERE userId = '$sent_user_id'");
    $user_rows = $stmt->fetchALL(PDO::FETCH_ASSOC);

    $user_row = $user_rows[0];
    extract($user_row);


    if($sent_hash == sha1( $userId . $sent_time . $loginPassword . $salt ) ){
      $return_array['message'] = "Hashes match";
      $return_array['valid'] = true;
      $return_array['user_id'] = $sent_user_id;
    } else {
      $return_array['message'] = "The token is invalid";
      $return_array['valid'] = false;
    }

    return $return_array;
  }

  /*
    Checks if the the passwords are valid, and puts the new password hash in the database
  */
  function check_new_passwords_and_update($conn, $user_id, $password1, $password2){
    $return_array = array(
      "completed" => false,
      "message" => ""
    );

    $password1 = strip_tags($password1);
    $password2 = strip_tags($password2);

    if( empty($password1) || empty($password2) ){
      $return_array['message'] = "Make sure both passwords are filled in";
      return $return_array;
    }

    if( $password1 != $password2 ){
      $return_array['message'] = "Passwords don't match";
      return $return_array; 
    }

    if( (strlen($password1) < 8) && (strlen($password1) > 30 )){
      $return_array['message'] = "Passwords must be greater than 7 characters and less than 30";
      return $return_array; 
    }

    if( (preg_match("/\\s/", $password1) ) ){
      $return_array['message'] = "Passwords must not have spaces";
      return $return_array;
    }

    if (preg_match('/[^A-Z\d.]/i', $password1)){
      $return_array['message'] = "Must only have alphnumeric characters";
      return $return_array;
    }


      CRYPT_BLOWFISH or die ('No Blowfish found.');

      //run for 5 seconds
      $Blowfish_Pre = '$2a$05$';
      $Blowfish_End = '$';
      //characters which will be accepted
      $Allowed_Chars ='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789./';
      $Chars_Len = 63;
      //len of salt
      $Salt_Length = 21;

      $mysql_date = date( 'Y-m-d' );
      $salt = "";

      for($i=0; $i<$Salt_Length; $i++){
          $salt .= $Allowed_Chars[mt_rand(0,$Chars_Len)];
      }
      $bcrypt_salt = $Blowfish_Pre . $salt . $Blowfish_End;
      $hashed_password = crypt($password1, $bcrypt_salt);


  $conn->query("UPDATE tbl_users SET loginPassword = '$hashed_password', salt = '$salt' WHERE userId = '$user_id'");

  $return_array = array(
    "completed" => true,
    "message" => "Your password has now been updated!"
  );

  return $return_array;
}


?>