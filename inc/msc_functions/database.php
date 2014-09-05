<?php




	//Requirements
	// - Must make sure the name is unique
	// Inputs to transfer, name, description, reps, sets, muscleGroup, mediaUrl
	function copy_exercise($conn, $exercise_id, $user_id){
		//Get the values
		$exercise_rows = $conn->query("SELECT * from tbl_exercises WHERE active = TRUE AND exerciseId = $exercise_id");
		$exercise_rows = $exercise_rows->fetchALL(PDO::FETCH_ASSOC);
		$exercise_row = $exercise_rows[0];

		//Makes sure the names aren't the same.
		$unique_check_name = $conn->query("SELECT * from tbl_exercises WHERE active = TRUE AND userIdF = $user_id");
		$unique_check_name = $unique_check_name->fetchALL(PDO::FETCH_ASSOC);
		$found = false;
		foreach($unique_check_name as $exercise){
			if($exercise['name'] == $exercise_row['name']){
				return "Error: when copying name must be unique per user";
			}
		}

		//Name must be unique per user;
		$name 			= $exercise_row['name'];
		$description	= $exercise_row['description'];
		$reps			= $exercise_row['reps'];
		$sets			= $exercise_row['sets'];
		$muscleGroup	= $exercise_row['muscleGroup'];
		$mediaUrl		= $exercise_row['mediaUrl'];
		$rating 		= $exercise_row['rating'];

		$sql = "INSERT INTO tbl_exercises(userIdF, name, description, reps, sets, muscleGroup, mediaUrl, rating) ";
		$sql .= "VALUES($user_id, '$name', '$description', $reps, $sets, '$muscleGroup', '$mediaUrl', '$rating')";

		if($conn->query($sql)){
			return "Copy successful!";
		} else {
			return "Copy failed!";
		}
	}

	//Implied names are the same
	function copy_exercise_information_to_existing_exercise($conn, $exercise_id_to, $exercise_id_from, $user_id){
		$exercise_rows = $conn->query("SELECT * from tbl_exercises WHERE active = TRUE AND exerciseId = $exercise_id_from");
		$exercise_rows = $exercise_rows->fetchALL(PDO::FETCH_ASSOC);
		$exercise_row = $exercise_rows[0];

		$name 			= $exercise_row['name'];
		$description	= $exercise_row['description'];
		$reps			= $exercise_row['reps'];
		$sets			= $exercise_row['sets'];
		$muscleGroup	= $exercise_row['muscleGroup'];
		$mediaUrl		= $exercise_row['mediaUrl'];
		$rating 	 	= $exercise_row['rating'];

		$sql = "UPDATE tbl_exercises SET name = :name, description = :description, reps = :reps, sets = :sets, muscleGroup = :muscleGroup, mediaUrl = :mediaUrl, rating = :rating WHERE exerciseId = $exercise_id_to";
		$update_query = $conn->prepare($sql);

		$update_query->bindParam(":name", $name, PDO::PARAM_STR);
		$update_query->bindParam(":description", $description, PDO::PARAM_STR);
		$update_query->bindParam(":reps", $reps, PDO::PARAM_INT);
		$update_query->bindParam(":sets", $sets, PDO::PARAM_INT);
		$update_query->bindParam(":muscleGroup", $muscleGroup, PDO::PARAM_STR);
		$update_query->bindParam(":mediaUrl", $mediaUrl, PDO::PARAM_STR);
		$update_query->bindParam(":rating", $rating, PDO::PARAM_STR);

		if($update_query->execute()){
			return "Copy successful!";
		} else {
			return "Copy failed!";
		}
	}


	//If not used, TRUE; if used false.
	function check_unique_name($conn, $user_id, $name){
		//Makes sure the names aren't the same.
		$unique_check_name = $conn->query("SELECT * from tbl_exercises WHERE active = TRUE AND userIdF = $user_id AND active = TRUE");
		$unique_check_name = $unique_check_name->fetchALL(PDO::FETCH_ASSOC);
		foreach($unique_check_name as $exercise){
			if($exercise['name'] == $name){
				return false;
			}
		}
		return true;
	}

	function num_of_inputs_for_ex($conn, $user_id, $exercise_id){
		$count = $conn->query("SELECT COUNT(*) FROM tbl_exercise_values WHERE active = true AND exerciseIdF = $exercise_id AND userIdF = $user_id");
		$count = $count->fetchALL(PDO::FETCH_ASSOC);
		return $count[0]['COUNT(*)'];
	}

	function id_of_user_ex_with_same_name($conn, $user_id, $name){
		$id_rows = $conn->query("SELECT exerciseId FROM tbl_exercises WHERE userIdF = $user_id AND name = '$name' AND active = true");
		$id_rows = $id_rows->fetchALL(PDO::FETCH_ASSOC);

		return $id_rows[0]['exerciseId'];
	}

	function arr_of_comments($conn, $exercise_id){
		$query = $conn->query("SELECT notes FROM tbl_exercise_values WHERE exerciseIdF = $exercise_id AND active = true AND notes != '' ORDER BY timeCompleted DESC");
		$query = $query->fetchALL(PDO::FETCH_ASSOC);
		
		$comments = array();

		foreach($query as $comment){
				array_push($comments, $comment['notes']);
		}
		return $comments;
	}

	//Must have comma seperated values
	function parse_for_graph($conn, $user_id, $exercise_id){
		$return_values = array(
			"labels"=> "",
			"data"=> ""
		);

		$sql = "SELECT * FROM tbl_exercise_values WHERE userIdF = $user_id AND exerciseIdF = $exercise_id AND active = TRUE";
		$exercise_values = $conn->query($sql);
		$exercise_values = $exercise_values->fetchAll(PDO::FETCH_ASSOC);

		$num_exercise_values = count($exercise_values);

		if($num_exercise_values < 52){
			$i = 1;
			foreach($exercise_values as $exercise_value){
				$return_values['labels'] .= "'Week " . $i . "', ";
				$return_values['data'] .=  $exercise_value['weightDone'] . ", ";
				$i++;
			}
		} else {
			// Months
			$i = 0;
			$temp = 0;

			foreach($exercise_values as $exercise_value){
				$temp = $temp + $exercise_value['weightDone'];
				$i++;

				if( ($i % 4) == 0 ){
					$return_values['labels'] .= "'Month " . ($i / 4) . "', ";
					$return_values['data'] .=  ($temp / 4) . ", ";
					$temp = 0;
				}

			}
		}


		$return_values['labels'] =  substr_replace($return_values['labels'] ,"",-2);
		$return_values['data'] =  substr_replace($return_values['data'] ,"",-2);

		return $return_values;	
	}

?>