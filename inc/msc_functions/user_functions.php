<?php

function check_username_status($conn, $userId, $username1Id, $username2Id, $current_passwordId){
	$return_array = array(
      "completed" => false,
      "message" => ""
     );

	if(
		!isset($_POST[$username1Id]) &&
		!isset($_POST[$username2Id]) &&
		!isset($_POST[$current_passwordId])
	){
		return $return_array;
	}

	if(isset($_POST[$username1Id])){
		$username1 = $_POST[$username1Id];
	} else {
		$username1 = "";
	}

	if(isset($_POST[$username2Id])){
		$username2 = $_POST[$username2Id];
	} else {
		$username2 = "";
	}

	if(isset($_POST[$current_passwordId])){
		$current_password = $_POST[$current_passwordId];
	} else { 
		$current_password = "";
	}

	//////// CONTAINMENT CHECK ///////////
	$username1 = strip_tags($username1);
	$current_password = strip_tags($current_password);

	if(empty($username1)){
		$return_array['message'] = "The username field must not be empty";
		return $return_array;
	}

	if(empty($current_password)){
		$return_array['message'] = "The password field must not be empty";
		return $return_array;
	}	

	///////// VAILD USERNAME CHECK ///////////
	if($username1 != $username2){
		$return_array['message'] = "Usernames are not the same";
		return $return_array;
	}

	//Make sure the username is greater than 5 characters less than 15
    if( (strlen($username1) < 4) || (strlen($username2) > 15 )){
    	$return_array['message'] = "Username: greater than 4 characters less than 15"; 
    	return $return_array;
    }

	 //////// UNIQUE EMAIL CHECK ///////////
    $query = $conn->prepare('SELECT username FROM tbl_users');
    $query->execute();
    $user_rows = $query->fetchALL(PDO::FETCH_ASSOC);

    $found = false;
    foreach($user_rows as $row){
      if($row['username'] == $username1){
        $found = true;
        break;
      }   
    }
    //Returns as no email was found
    if( $found == true ){
      $return_array['message'] = "Username has already been taken.";
      return $return_array;
    }

    ///////// PASSWORD CHECK ///////////
    $query = $conn->prepare('SELECT loginPassword, salt FROM tbl_users WHERE userId = :userId');
    $query->bindParam(":userId", $userId, PDO::PARAM_INT);
    $query->execute();
    $user_rows = $query->fetchALL(PDO::FETCH_ASSOC);
    $user_row = $user_rows[0];

    $Blowfish_Pre = '$2a$05$';
    $Blowfish_End = '$';
    $hashed_pass = crypt($current_password, $Blowfish_Pre . $user_row['salt'] . $Blowfish_End);

	if($user_row['loginPassword'] != $hashed_pass){
		$return_array['message'] = "The current password does not match your existing one.";
    	return $return_array;
	}

	///////// INSERT INTO DATABASE //////////

    $sql = "UPDATE tbl_users SET username = :username WHERE userId = :userId";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":username", $username1, PDO::PARAM_STR);
   	$stmt->bindParam(":userId", $userId, PDO::PARAM_INT);

    if(!$stmt->execute()){
     	$return_array['message'] = "Failed to update to the database";
     	return $return_array;
    } 

   	$return_array['completed'] = true;
    $return_array['message'] = "Username updated";

    return $return_array;
}


function check_password_values($conn, $userId, $password1Id, $password2Id, $current_passwordId){
		$return_array = array(
	      "completed" => false,
	      "message" => ""
	     );


		if(
			!isset($_POST[$current_passwordId]) &&
			!isset($_POST[$password1Id]) &&
			!isset($_POST[$password2Id])
		){
			return $return_array;
		}

		if(isset($_POST[$current_passwordId])){
			$current_password = $_POST[$current_passwordId];
		} else {
			$current_password = "";
		}

		if(isset($_POST[$password1Id])){
			$password1 = $_POST[$password1Id];
		} else {
			$password1 = "";
		}

		if(isset($_POST[$password2Id])){
			$password2 = $_POST[$password2Id];
		} else { 
			$password2 = "";
		}


		//////// CONTAINMENT CHECK ///////////
		$password1 = strip_tags($password1);
		$password2 = strip_tags($password2);

		if(empty($password1)){
			$return_array['message'] = "The password field must not be empty";
			return $return_array;
		}

		if(empty($current_password)){
			$return_array['message'] = "The current password field must not be empty";
			return $return_array;
		}		

		///////// VAILD EMAIL CHECK ///////////
		if($password1 != $password2){
			$return_array['message'] = "The passwords must be the same";
			return $return_array;	
		}

	    	//Make sure the password is greater than 5 characters
	    if( (strlen($password1) < 8) && (strlen($password1) > 30 )){
	    	$return_information['message'] = "Password: greater than 8 characters less than 30"; 
	    	return $return_information;
	    }


	    ///////// PASSWORD CHECK ///////////
	    $query = $conn->prepare('SELECT loginPassword, salt FROM tbl_users WHERE userId = :userId');
	    $query->bindParam(":userId", $userId, PDO::PARAM_INT);
	    $query->execute();
	    $user_rows = $query->fetchALL(PDO::FETCH_ASSOC);
	    $user_row = $user_rows[0];

	    $Blowfish_Pre = '$2a$05$';
	    $Blowfish_End = '$';
	    $hashed_pass = crypt($current_password, $Blowfish_Pre . $user_row['salt'] . $Blowfish_End);

		if($user_row['loginPassword'] != $hashed_pass){
			$return_array['message'] = "The current password does not match your existing one.";
	    	return $return_array;
		}

		///////// UPDATE INFORMATION /////////
    //////  : SALTING

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
        $new_hashed_password = crypt($password1, $bcrypt_salt);

   
    //////  : end SALTING

	    $sql = "UPDATE tbl_users SET loginPassword = :new_hashed_password, salt = :salt WHERE userId = :userId";
	    $stmt = $conn->prepare($sql);
	    $stmt->bindParam(":new_hashed_password", $new_hashed_password, PDO::PARAM_STR);
	    $stmt->bindParam(":salt", $salt, PDO::PARAM_STR);
	   	$stmt->bindParam(":userId", $userId, PDO::PARAM_INT);

	    if(!$stmt->execute()){
	     	$return_array['message'] = "Failed to update to the database";
	     	return $return_array;
	    } 

	   	$return_array['completed'] = true;
	    $return_array['message'] = "Password updated";

	    return $return_array;
}

function check_email_values($conn, $userId, $email1Id, $email2Id, $sent_passwordId){
		$return_array = array(
	      "completed" => false,
	      "message" => ""
	     );


		if(!isset($_POST[$email1Id]) &&
			!isset($_POST[$email2Id]) &&
			!isset($_POST[$sent_passwordId])){
			return $return_array;
		}

		if(isset($_POST[$email1Id])){
			$email1 = $_POST[$email1Id];
		} else {
			$email1 = "";
		}

		if(isset($_POST[$email2Id])){
			$email2 = $_POST[$email2Id];
		} else {
			$email2 = "";
		}

		if(isset($_POST[$sent_passwordId])){
			$sent_password = $_POST[$sent_passwordId];
		} else { 
			$sent_password = "";
		}

		//////// CONTAINMENT CHECK ///////////
		$email1 = strip_tags($email1);
		$email2 = strip_tags($email2);

		if(empty($email1)){
			$return_array['message'] = "The email field must not be empty";
			return $return_array;
		}

		if(empty($sent_password)){
			$return_array['message'] = "The password field must not be empty";
			return $return_array;
		}

		///////// VAILD EMAIL CHECK ///////////
		if($email1 != $email2){
			$return_array['message'] = "Emails are not the same";
			return $return_array;
		}

	    if( !filter_var($email1, FILTER_VALIDATE_EMAIL) ){
	      $return_array['message'] = "Invalid Email";
	      return $return_array;
	    }

	    ///////// UNIQUE EMAIL CHECK ///////////
	    $query = $conn->prepare('SELECT emailAddress FROM tbl_users');
	    $query->execute();
	    $user_rows = $query->fetchALL(PDO::FETCH_ASSOC);

	    $found = false;
	    $row_number = 0;
	    foreach($user_rows as $row){
	      if($row['emailAddress'] == $email1){
	        $found = true;
	        break;
	      }   
	      $row_number++;
	    }
	    //Returns as no email was found
	    if( $found == true ){
	      $return_array['message'] = "Email has already been taken.";
	      return $return_array;
	    }


	    ///////// PASSWORD CHECK ///////////
	    $query = $conn->prepare('SELECT loginPassword, salt FROM tbl_users WHERE userId = :userId');
	    $query->bindParam(":userId", $userId, PDO::PARAM_INT);
	    $query->execute();
	    $user_rows = $query->fetchALL(PDO::FETCH_ASSOC);
	    $user_row = $user_rows[0];

	    $Blowfish_Pre = '$2a$05$';
	    $Blowfish_End = '$';
	    $hashed_pass = crypt($sent_password, $Blowfish_Pre . $user_row['salt'] . $Blowfish_End);

		if($user_row['loginPassword'] != $hashed_pass){
			$return_array['message'] = "The current password does not match your existing one.";
	    	return $return_array;
		}

		///////// UPDATE INFORMATION /////////
	    $sql = "UPDATE tbl_users SET emailAddress = :email, registeredEmail = FALSE WHERE userId = :userId";
	    $stmt = $conn->prepare($sql);
	    $stmt->bindParam(":email", $email1, PDO::PARAM_STR);
	    $stmt->bindParam(":userId", $userId, PDO::PARAM_INT);

	    if(!$stmt->execute()){
	     	$return_array['message'] = "Failed to update to the database";
	     	return $return_array;
	    } 

		//Sends an email to the user to register their new username
	    $return_array['completed'] = true;
	    $status = email_registraion($conn, $email1, 0);
	    $return_array['message'] = "Email Address updated, " . $status['message'];

	    return $return_array;
	}

?>