<?php

/*

<--- Users --->
email -         
password -      
display name -  
first name -    
last name -   
*/


/*
	//Tested
	Checks to see if it the email is in the database, and whether it's a valid email string.
	Automatically assume you're looking to see if it's in the database
*/

function check_password($password){
	$check_array = array(
		"is_alphnumeric" => true,
		"is_too_short" => false,
		"is_too_long" => false
	);


	if( strlen($password) < 8)
		$check_array['is_too_short'] = true;

	if( strlen($password) > 30)
		$check_array['is_too_long'] = true;

	if( !ctype_alnum($password) )
		$check_array['is_alphnumeric'] = false;
  	
  	$completed_array = array();

	if( $check_array['is_alphnumeric'] && !$check_array['is_too_long'] && !$check_array['is_too_short'])
		$completed_array['is_valid'] = true;
	else 
		$completed_array['is_valid'] = false;

	$completed_array['check_array'] = $check_array;

  	return $completed_array;
}

function check_email($conn, $email, $check_for_email_in_db = true){
	$check_array = array(
		"valid_email_string" => true,
		"email_in_db" => false,
	);

	//Valid email string check
    if( !filter_var($email, FILTER_VALIDATE_EMAIL) ){
   	  $check_array['valid_email_string'] = false;
    }


    //Checks to see if the email is in the database
    $query = $conn->prepare('SELECT COUNT(*) FROM tbl_users WHERE emailAddress = :email');
    $query->bindParam(":email", $email, PDO::PARAM_STR);
    $query->execute();
    $user_rows = $query->fetchALL(PDO::FETCH_ASSOC);

  	$in_db = $user_rows[0]['COUNT(*)'];

  	if($in_db > 0){
  		$check_array['email_in_db'] = true;
  	}

  	// parse information for returning.
  	$completed_array = array();

  	$completed_array['check_array'] = $check_array;

  	if($check_array['valid_email_string'] && ($check_for_email_in_db == $check_array['email_in_db']))
		$completed_array['is_valid'] = true;
	else 
		$completed_array['is_valid'] = false;

  	return $completed_array;
}

?>