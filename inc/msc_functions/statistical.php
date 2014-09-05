<?php
	
	//Returns an integer of the inputs done
	function inputs_done($conn, $user_id){
		// Statistics
		$statistics_query = $conn->prepare("SELECT * FROM tbl_exercise_values WHERE active = TRUE AND userIdF=:userId");
		$statistics_query->execute(array( "userId" => $user_id ));
		$exercise_rows = $statistics_query->fetchALL(PDO::FETCH_ASSOC);

		$entry_count = 0;
		foreach($exercise_rows as $exercise_row){
			$entry_count++;
		}

		return $entry_count;
	} 

	// Returns the most done exercise, given a user_id
	// Returns an asscioative array with, 'exId', 'exerciseName', 'frequency'
	function most_done($conn, $user_id){
		$statistics_query = $conn->prepare("SELECT * FROM tbl_exercise_values WHERE active = TRUE and userIdF=:userId ORDER BY exerciseIdF");
		$statistics_query->execute(array( "userId" => $user_id ));
		$exercise_rows = $statistics_query->fetchALL(PDO::FETCH_ASSOC);

		$exercise_id_and_frequencys_OBJ = array();

		class exercise_id_and_frequencys{
			var $exercise_id;
			var $frequency;

			function exercise_id_and_frequencys($exercise_id, $initial_frequency){
				$this->exercise_id = $exercise_id;
				$this->frequency = $initial_frequency;
			}

			function add_one(){
				$this->frequency++;
			}

			function return_frequency(){
				return $this->frequency;
			}

			function return_exercise_id(){
				return $this->exercise_id;
			}
		}

		$t_exercise_id = "";
		foreach($exercise_rows as $exercise_row){
			if( $t_exercise_id != $exercise_row['exerciseIdF'] ) {
				array_push($exercise_id_and_frequencys_OBJ, new exercise_id_and_frequencys($exercise_row['exerciseIdF'], 1));
				$t_exercise_id = $exercise_row['exerciseIdF'];
			} else {
				$exercise_id_and_frequencys_OBJ[count($exercise_id_and_frequencys_OBJ)-1]->add_one();
			}
		}

		$highest_frequency_OBJ = new exercise_id_and_frequencys("default", 0);
		foreach($exercise_id_and_frequencys_OBJ as $ex_id_OBJ){

			if($ex_id_OBJ->return_frequency() > $highest_frequency_OBJ->return_frequency() ){
				$highest_frequency_OBJ = $ex_id_OBJ;
			}

		}

		$query = $conn->prepare("SELECT exerciseId, name FROM tbl_exercises WHERE active = TRUE AND exerciseId=:exercise_id");
		$query->execute(array( "exercise_id" =>  $highest_frequency_OBJ->return_exercise_id() ));
		$exerciseName = $query->fetchALL(PDO::FETCH_ASSOC);

		//Using a count ruins the first fetch
		$query->execute(array( "exercise_id" =>  $highest_frequency_OBJ->return_exercise_id() ));
		$row_count = count( $query->fetchAll( PDO::FETCH_BOTH ) );
		
		//Some users may have no inputs yet
		if($row_count > 0)
			return array(
				"exId" => $highest_frequency_OBJ->return_exercise_id(),
				"exerciseName" => $exerciseName[0]['name'],
				"frequency" => $highest_frequency_OBJ->return_frequency()
			);
		else // if user has no inputs
			return array(
				"exId" => 0,
				"exerciseName" => "Nothing",
				"frequency" => 0
			);
	} 


?>