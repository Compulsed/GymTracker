<?php

require_once "msc_functions/input_checks.php";
require_once "msc_functions/classes.php";
require_once "msc_functions/statistical.php";
require_once "msc_functions/registeration_recovery.php";
require_once "msc_functions/user_functions.php";
require_once "msc_functions/database.php";

/*

<--- Tags --->
BUG : This could be a bug or could cause a bug.
LIVE : Something to take into consideration before the website would go live.
CONSIDER : Something that might need some more consideration. 

Vaildation rules. 

 <--- Users --->
email -         
password -      
display name -  
first name -    
last name -     


*/

function return_value($value){
	return $value;
}


/* ------------------
	: MY SQL FUNCTIONS
   ------------------- */

function connect($config){
	extract($config);
	try {
		 $conn = new PDO("mysql:host={$host};dbname={$dbname}", $username, $password); 
		 $conn -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //T his sets the amount of information given on an error
     $conn-> setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

		 return $conn;
	} catch(Exception $e){
        echo "Exception thrown" . $e->getMessage();
		return false;
	}
} // Returns a connection if successful else returns false. 

/* ------------------
  : MSC FUNCTIONS
   ------------------- */

function randString($length, $charset='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789')
{
    $str = '';
    $count = strlen($charset);
    while ($length--) {
        $str .= $charset[mt_rand(0, $count-1)];
    }
    return $str;
}

function user_id_to_display_name($conn, $userId){
  $user_rows = $conn->query("SELECT username from tbl_users WHERE userId = $userId");
  $user_rows = $user_rows->fetchALL(PDO::FETCH_ASSOC);
  if(count($user_rows))
    return $user_rows[0]['username'];
  else 
    return "Username Not found";
}

function url_check($url){
  if (preg_match('/[^a-z_\-0-9]/i', $url)){
     return false;
  } else {
    return true;
  }
}

/* ------------------
	: data_forms
   ------------------- */
//  
// -- Daily Input
// 

  /* Action: Returns all the unique routine names.
   Improvements
      -
   */
  function get_active_routine_names($conn, $user_id){
    $routine_query = $conn->prepare("SELECT routineName FROM tbl_routines WHERE userIdF = $user_id AND active = TRUE ORDER BY routineName");
    $routine_query -> execute();
    $routine_rows = $routine_query->fetchALL(PDO::FETCH_ASSOC);
    
    $all_active_routine_names = array();
    $temp_name = "";
    
    foreach($routine_rows as $routine)
      if( $routine['routineName'] != $temp_name ){                         // If the values aren't the same
          array_push($all_active_routine_names, $routine['routineName']);    // Saves the unique value   
          $temp_name = $routine['routineName'];                            // Stores the value into a tmep for checking again                                                         // Increment the array pointer
      } 

    return $all_active_routine_names;
  } 

  function get_active_share_routine_names($conn, $user_id){
    $routine_query = $conn->prepare("SELECT routineName, routineId FROM tbl_routines WHERE share = TRUE AND active = TRUE ORDER BY routineName");
    $routine_query -> execute();
    $routine_rows = $routine_query->fetchALL(PDO::FETCH_ASSOC);
    
    $all_active_routine_names_ids = array();
    $temp_name = "";

    //send ID over with unique name
    
    foreach($routine_rows as $routine)
      if( $routine['routineName'] != $temp_name ){
          $routine_name_id = array();

          $routine_name_id['name'] = $routine['routineName'];
          $routine_name_id['id'] = $routine['routineId'];

          // If the values aren't the same
          array_push($all_active_routine_names_ids, $routine_name_id);    // Saves the unique value   
          $temp_name = $routine['routineName'];                            // Stores the value into a tmep for checking again                                                         // Increment the array pointer
      } 

    return $all_active_routine_names_ids;
  } 




  
//
// -- exercise_edit
//


  /* Action: Returns all the exercise tables.
   Improvements
      -
   */
  function return_exercises_rows($conn, $user_id, $amount_to_show = 100, $location = 0){ 
    $query = $conn->prepare("SELECT * FROM tbl_exercises WHERE userIdF = $user_id ORDER BY exerciseId DESC LIMIT $location, $amount_to_show");
    $query -> execute();
    return $query->fetchALL(PDO::FETCH_ASSOC);
  }
  
  /* Action: checks the post information for all the items to be deleted 
   Improvements
      - (done)Can delete other peoples records
      - allow another parameter for what the exercise is called, not just assuming, good for resusibility
   */
  function check_deleted_exercise_values($conn, $user_id, $amount_to_show = 200, $location = 0){
      $return_array = array(
          "message" => "",
          "completed" => false
        );

      $row_query = $conn->prepare("SELECT exerciseId from tbl_exercises WHERE userIdF = $user_id ORDER BY exerciseId DESC LIMIT $location,  $amount_to_show");
      $row_query -> execute();
      $all_exercise_ids = $row_query->fetchALL(PDO::FETCH_ASSOC);
      
      $any_inputs_changed = false; //If any values have been updated in the database

      //Will only check the ids of which the users owns and by what page they're on
      foreach($all_exercise_ids as $an_exercise_id){
          $id_to_check = 'exer_delete_item' . $an_exercise_id['exerciseId'];
          if( isset($_POST[$id_to_check]) ){
              // Deletes all of the routine entries where the exercise id is inclined
              $sql = "UPDATE tbl_routines SET active = false, deactivationTime = NOW() WHERE exerciseIdF = " . $an_exercise_id['exerciseId'];
              $conn -> query($sql);

              // Deletes all of the exercise values entries where the exercise id is inclined
              $sql = "UPDATE tbl_exercise_values SET active = false, deactivationTime = NOW() WHERE exerciseIdF = " . $an_exercise_id['exerciseId'];
              $conn -> query($sql);

              $sql = "UPDATE tbl_exercises SET active = false, deactivationTime = NOW() WHERE exerciseId = " . $an_exercise_id['exerciseId'];
              $conn -> query($sql);

              $any_inputs_changed = true;
          }
      }
      //If the database has been updated, return success
      if($any_inputs_changed){
          $return_array['message'] = "Values removed";
          $return_array['completed'] = true;
      } 

      return $return_array;  //returns defaults if failed
  }

  /* Action: checks the post information for all the items to be recovered 
   ImprovementsF
      - (done)Can delete other peoples records
      - allow another parameter for what the exercise is called, not just assuming, good for resusibility
   */
  function check_recovered_exercise_values($conn, $user_id, $amount_to_show = 200, $location = 0){
      $return_array = array(
        "message" => "",
        "completed" => false
      );

      $row_query = $conn->prepare("SELECT * from tbl_exercises WHERE userIdF = $user_id ORDER BY exerciseId DESC LIMIT $location,  $amount_to_show");
      $row_query -> execute();
      $all_exercise_ids = $row_query->fetchALL(PDO::FETCH_ASSOC);

      $any_inputs_changed = false;
      $duplicates = false;
      //Will only check the ids of which the users owns
      foreach($all_exercise_ids as $a_exercide_id){
          $id_to_check = 'exer_recover_item' . $a_exercide_id['exerciseId'];
          if( isset($_POST[$id_to_check]) ){
            $exercise_id = $a_exercide_id['exerciseId'];
            
            // Checking if value is unique
            $unique_name_query = $conn->query("SELECT name FROM tbl_exercises WHERE exerciseId = $exercise_id");
            $unique_name_query = $unique_name_query->fetchALL(PDO::FETCH_ASSOC);
            $unique_name = $unique_name_query[0]['name'];

            $check_name = $conn->query("SELECT COUNT(*) from tbl_exercises WHERE name = '$unique_name' AND userIdF = $user_id AND ACTIVE = TRUE");
            $check_name = $check_name->fetchALL(PDO::FETCH_ASSOC);

            if($check_name[0]['COUNT(*)'] > 0){
              $duplicates = true;
            } else {
              // Deletes all of the routine entries where the exercise id is inclined
              $sql = "UPDATE tbl_exercises SET active = TRUE, deactivationTime = '' WHERE exerciseId = $exercise_id";
              $conn -> query($sql);

              $any_inputs_changed = true;
            }
          }
      }

      //If the database has been updated, return success
      if($any_inputs_changed){
          if(!$duplicates){
            $return_array['message'] = "Values recovered";
          } else { 
            $return_array['message'] = "Values recovered, and you cannot recover a record if there is a active record with that same name";
            $return_array['completed'] = true;
          }
      } else if($duplicates){
         $return_array['message'] = "You cannot recover a record if there is an active record with that same name";
      }

      return $return_array;  //returns defaults if failed
  }


  /////////////// FIXING POINTER LINE ///////////// ------------------------------------------------------------------------------------>

//
// -- exercise_input (pretected, prepared, tag strip, form chaning protected, resubmit protected) 
//

  function make_muscle_group_selector($amount, $ARRAY_OF_MUSCLE_GROUPS){
    for($i = 0; $i < $amount; $i++){
      echo '<select name="muscle_group_id' . $i . '">';
        echo '<option value="null">Empty</option>';
      foreach($ARRAY_OF_MUSCLE_GROUPS as $group){
        echo '<option value="' . $group . '">' . $group . '</option>';
      }
      echo '</select>';
    }
  }

  function return_active_exercises_rows($conn, $user_id){ 
    $query = $conn->prepare("SELECT * FROM tbl_exercises WHERE userIdF = $user_id AND active = true ORDER BY timeCreated DESC");
    $query -> execute();
    return $query->fetchALL(PDO::FETCH_ASSOC);
  }

  /* Action: Checks the post values on load, checks if they're all there, then adds to the database.
     Return: Array with, 'message' displaying what has happened, and compeleted, whether it wrote to the database or not. 
   Improvements
      - Should limit inputs down to alphanumeric, and spaces
   */
  function check_user_exercise_input($conn, $user_id, $ARRAY_OF_MUSCLE_GROUPS, $selectors){
    if( !isset($_POST['name']) && !isset($_POST['description']) &&  !isset($_POST['sets']) && !isset($_POST['reps']))
      return array(
        "message" => "",
        "completed" => false
      );  

    if( isset($_POST['name']) && isset($_POST['description']) &&  
        isset($_POST['sets']) && isset($_POST['reps'])) {
      if( $_SERVER['REQUEST_METHOD'] === 'POST'){
          $name = $_POST['name'];
          $description = $_POST['description'];
          $sets = $_POST['sets'];
          $reps = $_POST['reps'];

          //Strip all of the tags
          $name = strip_tags($name);
          $description = strip_tags($description); 
          $sets = strip_tags($sets); 
          $reps = strip_tags($reps);

          //Makes sure the input values for reps and sets are numbers
          if( !is_numeric($sets) || !is_numeric($reps) ){
              return array(
                "message" => "Sets and reps must be a number",
                "completed" => false
            );   
          }

          //Checks if the strips returned an empty value
          if( empty($name) || empty($description) || empty($sets) || empty($reps))
            return array(
              "message" => "There are empty values",
              "completed" => false
            );



          //Checks to make sure exercise names arent the same
          $check_unique = $conn->prepare("SELECT COUNT(*) FROM tbl_exercises WHERE userIdF = $user_id AND active = true AND name = :name");
          $check_unique->bindParam(':name', $name, PDO::PARAM_STR);
          $check_unique->execute();
          $count = $check_unique->fetchALL(PDO::FETCH_ASSOC);
          if($count[0]['COUNT(*)'] >= 1)
            return array(
              "message" => "Exercise name must be unique",
              "completed" => false
            );

          //Retreves the safe(can't be hacked) values from the muscle group selector 
          $array_muscles_groups = array();
          for($i = 0; $i < $selectors; $i++){
            //Checks to make sure the values are set and that they're not empty(null).
            if(isset($_POST['muscle_group_id' . $i]) && $_POST['muscle_group_id' . $i] != "null"){
              
              $check_for = $_POST['muscle_group_id' . $i];
              //Checks to see if the value posted is in the list of possible groups, then checks to see if it's not already been submitted. 
              if( in_array( $check_for, $ARRAY_OF_MUSCLE_GROUPS) && !in_array($check_for, $array_muscles_groups) )
                array_push($array_muscles_groups, $check_for);
            }
          }
          $str_muscle_groups = implode(", ", $array_muscles_groups);

          // The input query 
          $stmt = $conn->prepare("INSERT INTO tbl_exercises(userIdF, name, sets, reps, description, share, muscleGroup)
                      VALUES('$user_id', :name, :sets, :reps, :description, 0, :muscleGroup)");
          $stmt->bindParam(':name', $name, PDO::PARAM_STR);
          $stmt->bindParam(':sets', $sets, PDO::PARAM_INT);
          $stmt->bindParam(':reps', $reps, PDO::PARAM_INT);
          $stmt->bindParam(':description', $description, PDO::PARAM_STR);
          $stmt->bindParam(':muscleGroup', $str_muscle_groups, PDO::PARAM_STR);

          if($stmt->execute()){
            return array(
              "message" => "Information sent",
              "completed" => true
            ); 
          } else { // on failure to write to the DB
            return array(
              "message" => "Failed to write to the database",
              "completed" => false
            ); 
          }

      }
    } else {
      return array(
        "message" => "Please enter all Information",
        "completed" => false
      );
    }
  } 




//
// -- dayly_input_edit (protected)
//
  function check_for_deleted_values($conn, $user_id, $amount_to_show = 200, $location = 0){
      //Selects even the active ones, this is because it must return exactly the "amount to show". 
      $row_query = $conn->prepare("SELECT liftId FROM tbl_exercise_values WHERE userIdF = $user_id ORDER BY liftId DESC LIMIT $location, $amount_to_show"); // Returns _all_ of the stored IDs
      $row_query -> execute();
      $all_lifts_ids = $row_query->fetchALL(PDO::FETCH_ASSOC);
      
      $updated = false;
      foreach($all_lifts_ids as $a_liftId){ // Checks all of the ids against the server header (TODO check what headers there are)
          $id_to_check = 'delete_item' . $a_liftId['liftId'];
          if( isset($_POST[$id_to_check]) ){       //Check if they're set or not, if set delete the record.   
                  $conn -> query("UPDATE tbl_exercise_values SET active = FALSE, deactivationTime = NOW() WHERE liftId = " . $a_liftId['liftId']);
                  $updated = true;
          }
      }
      if($updated)
        return array(
          "message" => "Values deleted",
          "completed" => true
        );  
      else 
        return array(
            "message" => "",
            "completed" => false
        ); 
  }

  // check_for_recovered_values(Object(PDO), '7', 15, -15) : if zero inputs
  function check_for_recovered_values($conn, $user_id,  $amount_to_show = 200, $location = 0){
      $row_query = $conn->prepare("SELECT liftId, exerciseIdF FROM tbl_exercise_values WHERE userIdF = $user_id ORDER BY liftId DESC LIMIT $location, $amount_to_show"); // Returns the users stored IDs
      $row_query -> execute();
      $all_lifts_ids = $row_query->fetchALL(PDO::FETCH_ASSOC);
      
      $active_routine_query = $conn->prepare("SELECT exerciseId FROM tbl_exercises WHERE userIdF = $user_id AND active = TRUE ORDER BY exerciseId"); 
      $active_routine_query -> execute();
      $all_user_active_exercise_ids = $active_routine_query->fetchALL(PDO::FETCH_ASSOC);

      $ids_of_deleted_exercise = array();

      $updated = false;
      foreach($all_lifts_ids as $a_liftId){ // Checks all of the ids against the server header (TODO check what headers there are)
          $id_to_check = 'recover_item' . $a_liftId['liftId'];
          $parent_is_active = false;

          //checks to see if the parent id is active, if it isn't then there would be an orphan record orphans can't be recovered unless their parents are
          foreach($all_user_active_exercise_ids as $active_exercise_id){
              if($active_exercise_id['exerciseId'] == $a_liftId['exerciseIdF']){
                $parent_is_active = true;
              }
          }

          if($parent_is_active && isset($_POST[$id_to_check]) ){       //Check if they're set or not, if set delete the record.   
                  $conn -> query("UPDATE tbl_exercise_values SET active = TRUE, deactivationTime = '' WHERE liftId = " . $a_liftId['liftId']);
                  $updated = true; //There is at least one record that's been changed
          }

          if(!$parent_is_active){ //We must deactive the record so there isn't any orphans, this information can be used
            array_push($ids_of_deleted_exercise, $a_liftId['liftId']);
          }
      }
      if($updated)
        return array(
          "message" => "Values recovered",
          "completed" => true,
          "deleted_ex_ids" => $ids_of_deleted_exercise
        );  
      else 
        return array(
            "message" => "",
            "completed" => false,
            "deleted_ex_ids" => $ids_of_deleted_exercise
        ); 
  }

  function return_exercise_inputs($conn, $user_id, $amount_to_show = 100, $location = 0){
    $query = $conn->prepare("SELECT * FROM vw_ExerciseValues WHERE userIdF = $user_id ORDER BY liftId DESC LIMIT $location, $amount_to_show");
    $query -> execute();
    return $query->fetchALL(PDO::FETCH_ASSOC);
  }


  function return_active_exercise_inputs($conn, $user_id, $amount_to_show = 100, $location = 0){
    $query = $conn->query("SELECT * FROM vw_ExerciseValues WHERE userIdF = $user_id AND ACTIVE = TRUE ORDER BY liftId DESC LIMIT $location, $amount_to_show");
    return $query->fetchALL(PDO::FETCH_ASSOC);
  }


//
// -- routine_edit (protected)
//

/*  
    Notes: 
      - There will be an error with reenabling routines, if they already exist
*/  
  function get_routine_tables($conn, $user_id, $amount_to_show = 200, $location = 0){
    $routine_query = $conn->prepare("SELECT * FROM db_gym.vw_Routines WHERE `userIdF` = '$user_id' AND `routineActive` = 1 ORDER BY routineName LIMIT $location, $amount_to_show");
    $routine_query -> execute();
    $routine_rows = $routine_query->fetchALL(PDO::FETCH_ASSOC);
    return $routine_rows;
  }
  

  function check_deleted_routine_values($conn, $user_id){
      $row_query = $conn->prepare("SELECT routineId FROM tbl_routines WHERE userIdF = $user_id ORDER BY routineId"); // Returns user specific ids
      $row_query -> execute();
      $all_user_routine_ids = $row_query->fetchALL(PDO::FETCH_ASSOC);
      
      $updated = false;
      foreach($all_user_routine_ids as $a_routineId){ // Checks all of the ids against the server header (TODO check what headers there are)
          $id_to_check = 'delete_item' . $a_routineId['routineId'];
          if( isset($_POST[$id_to_check])){       //Check if they're set or not, if set delete the record.   
                  $conn -> query("UPDATE  tbl_routines SET active = false WHERE routineID = " . $a_routineId['routineId']);
                  $updated = true;
          }
      }

      if($updated)
        return array(
          "message" => "Values deleted",
          "completed" => true
        );  
      else 
        return array(
            "message" => "",
            "completed" => false
        );   
  }

//
// -- routine_input
//

   function check_if_valid_form($conn, $user_id, $amount_to_check){
    $user_routine_name = "";

    //Returns nothing if nothing has been set(If the page is first loaded)
    if( !isset($_POST['routineName']) ) 
      return array(
        "message" => "",
        "completed" => false
      );


    //Checks if there has been a routine name entered 
    if( isset($_POST['routineName']) && !empty($_POST['routineName']))
       $user_routine_name = $_POST['routineName'];
    else 
      return array(
        "message" => "Please enter a routine name",
        "completed" => false
      );
    
    //Checks to make sure the routine isn't empty after all the tags have been stripped
    $user_routine_name = strip_tags($user_routine_name);
    if( $user_routine_name == "" || empty($user_routine_name) ){
      return array(
        "message" => "Please enter a valid routine name",
        "completed" => false
      );
    }    
    
   //Check how many selections have been used that aren't null
   $ids_of_selectors_used = array();
   for( $selectionId  = 0; $selectionId < $amount_to_check; $selectionId++){
      if( isset( $_POST['exerciseSelectId' . $selectionId] ) ){
         if($_POST['exerciseSelectId' . $selectionId]  != 'null'){ //Null being the default for the selection 
            array_push($ids_of_selectors_used, $selectionId); // Pushes all the values that aren't null to an array 
         }
      }
   }

   //If there aren't any selectors used, then there is nothing to be done
   if(count($ids_of_selectors_used) == 0){
      return array(
        "message" => "Please enter in at least one exercise",
        "completed" => false
      );
   }
   
   
   // Checks to make sure there are no duplicates 
   $sql = "SELECT exerciseIdF FROM tbl_routines WHERE routineName = :user_routine_input AND userIdF = $user_id AND active = TRUE";
   //Will return all exercise values that have been in a given routine
   $query = $conn->prepare($sql);
   $query -> bindParam(":user_routine_input", $user_routine_name, PDO::PARAM_STR);
   $query -> execute();
   $all_possible_ex_id_rows = $query->fetchALL(PDO::FETCH_ASSOC);

   //Stores all of the exercises in a routine given a routine name
   $exercises_in_routine = array();
   foreach($all_possible_ex_id_rows as $possible_exercise_ids){
      array_push($exercises_in_routine, $possible_exercise_ids['exerciseIdF']);
   }

  // -- 

   //Check to make sure the user can access the routine(protection against XSS aatacks)
   $sql = "SELECT exerciseId FROM tbl_exercises WHERE userIdF = $user_id AND active = TRUE";
   $query = $conn->prepare($sql);
   $query -> execute();
   $all_possible_ex_id_rows = $query->fetchALL(PDO::FETCH_ASSOC);

   //Stores all of the exercises in a routine given a routine name
   $allowable_ex_ids = array();
   foreach($all_possible_ex_id_rows as $row){
      array_push($allowable_ex_ids, $row['exerciseId']);
   }

   $values_entered_into_db = 0;
   //Iterates over all the sectors used.
   for($i = 0; $i < count($ids_of_selectors_used); $i++){
      $exerciseId = $_POST['exerciseSelectId' . $ids_of_selectors_used[$i]];
  
      $routine_check = true;
   // Check if the "rotuine name" has that exercise id
      foreach($exercises_in_routine as $exercise_id){
         if($exerciseId == $exercise_id)
            $routine_check = false;
      }

      $exercise_check = false;
   //Checks that the user has access to the exerciseId   
      foreach($allowable_ex_ids as $exercise_id){
        if($exerciseId == $exercise_id){
          $exercise_check = true;
        }
      }

      $stmt = "INSERT INTO tbl_routines(userIdF, exerciseIdF, routineName, share) 
          VALUES('$user_id', '$exerciseId', '$user_routine_name', 0)";

      if( ($routine_check == true)  && ($exercise_check == true) ){
         array_push($exercises_in_routine, $exerciseId); //As the value is inputed into the database, checks again for it. 
         ++$values_entered_into_db;
         $conn->query($stmt);
      }
   }

    return array(
      "message" => "Values entered into database:" . $values_entered_into_db,
      "completed" => true
    );
  }


  function print_exercise_collection($conn, $user_id, $amount_to_print){   
    $query = $conn->prepare("SELECT * FROM tbl_exercises WHERE userIdF = $user_id AND active = TRUE ORDER BY name ");    
    $query -> execute();
    $rows = $query->fetchALL(PDO::FETCH_ASSOC);

    for($j = 0; $j < $amount_to_print; $j++){
        echo '<select name="exerciseSelectId' . $j . '">'; 
        echo '<option value="null">None</option>';
        foreach($rows as $row){
            echo "<option value='" . $row['exerciseId'] . "'>" . $row['name'] . "</option>\n\t";
        }
        echo '</select>';   
    }
  
  }


  function get_active_routine_tables($conn, $user_id){
    $routine_query = $conn->prepare("SELECT * FROM db_gym.vw_Routines WHERE userIdF = $user_id  AND routineActive = TRUE ORDER BY routineName");
    $routine_query -> execute();
    $routine_rows = $routine_query->fetchALL(PDO::FETCH_ASSOC);
    return $routine_rows;
  }


function parse_routine_information($conn, $user_id){
    $routine_rows = get_active_routine_tables($conn, $user_id);
    //Load the array
    $routines = array();
    $t_routine = "";
    foreach($routine_rows as $routine){
        if($t_routine != $routine['routineName']){
            //Load a new routine
            array_push($routines, new class_routine($routine['routineName']));

            $routines[count($routines)-1]->pushExercise($routine['exerciseName']);
            $t_routine = $routine['routineName'];
        } else 
            $routines[count($routines)-1]->pushExercise($routine['exerciseName']); 
    }
    return $routines; // An array of class_routine;
}






//
// -- weight_input
//

  function get_exercises_info($conn, $user_id, $routineName){
      $sql = 'SELECT * FROM vw_ExerciseNames
         WHERE routineName =' . "'$routineName'" . " AND exerciseActive = TRUE AND routineActive = TRUE AND userIdF = " . $user_id;

      $exercise_name_query = $conn->prepare($sql);
      $exercise_name_query -> execute();
      $assoc_exercise_names = $exercise_name_query->fetchALL(PDO::FETCH_ASSOC);
      
      $exercise_names = array();
      foreach($assoc_exercise_names as $exercise_row){
          array_push($exercise_names, new ex_id_name($exercise_row['exerciseName'], $exercise_row['exerciseId'], 0, 0, ''));
      }
      
      return $exercise_names;
  }

  // Takes the user Id and the routine it's looking for.
  function check_for_inputs($conn, $user_id, $routineName){
    //check all exerciseIds that could be set(be loading in the exercises)
    $sql = 'SELECT exerciseId FROM vw_ExerciseNames 
      WHERE routineName =' . "'$routineName'" . " AND exerciseActive = TRUE AND routineActive = TRUE AND userIdF = " . $user_id;

    $exercise_name_query = $conn->prepare($sql);
    $exercise_name_query -> execute();
    $assoc_exercise_names = $exercise_name_query->fetchALL(PDO::FETCH_ASSOC);
    
    // Stores all the exercise IDs in the array 
    $exercise_ids = array();
    foreach($assoc_exercise_names as $row){
        array_push($exercise_ids, 
          array(
            "exerciseId" => $row['exerciseId'],
            "entered_correctly" => false
          )
        );
    }
    

    //Check to see if any complete rows are set
    $any_values_set = false;
    foreach($exercise_ids as $exercise_id){
      if( isset($_POST['exId' . $exercise_id['exerciseId']]) &&
          isset($_POST['exIdSets' . $exercise_id['exerciseId']]) && 
          isset($_POST['exIdReps' . $exercise_id['exerciseId']]) ){
        $any_values_set = true;
        break;
      }
    }

    //If there are no values set, don't write any status message
    if(!$any_values_set){
      return array(
        "completed" => false,
        "message" => ""
      );
    }

    //Check which values are set and which are not
    //Set values are ones which have weight, sets, and reps completed. Js will protect against any numbers
    $i = 0;
    foreach($exercise_ids as $exercise_id){
      if( isset($_POST['exId' . $exercise_id['exerciseId']]) &&
          isset($_POST['exIdSets' . $exercise_id['exerciseId']]) && 
          isset($_POST['exIdReps' . $exercise_id['exerciseId']]) ){

          // echo "Exercise id: " . $exercise_id['exerciseId'] . "Has been posted";

          $weight = $_POST['exId' . $exercise_id['exerciseId']];
          $sets = $_POST['exIdSets' . $exercise_id['exerciseId']];
          $reps = $_POST['exIdReps' . $exercise_id['exerciseId']];

          $valid_weight = false;
          $valid_sets = false;
          $valid_reps = false;

          if(!empty($weight) && is_numeric($weight)) $valid_weight = true;
          if(!empty($sets) && is_numeric($sets)) $valid_sets = true;
          if(!empty($reps) && is_numeric($reps)) $valid_reps = true;

          if( ($valid_weight == true) && ($valid_sets == true) &&  ($valid_reps == true)){
            $exercise_ids[$i]['entered_correctly'] = true;
          } 
        }
      $i++;
    }    

    $i = 0;
    foreach($exercise_ids as $exercise_id){
      if($exercise_id['entered_correctly']){
        $temp_exercise_weight = $_POST['exId' . $exercise_id['exerciseId']];               
        $temp_exercise_sets = $_POST['exIdSets' . $exercise_id['exerciseId']];               
        $temp_exercise_reps = $_POST['exIdReps' . $exercise_id['exerciseId']];               
        $temp_exercise_description = strip_tags($_POST['exIdDescription' . $exercise_id['exerciseId']]);
        
        $sql = 'INSERT INTO tbl_exercise_values(userIdF, weightDone, repsCompleted, setsCompleted, timeCompleted, exerciseIdF, notes)
            VALUES( :user_id, :temp_exercise_weight, :temp_exercise_reps,  :temp_exercise_sets, now(), :exercise_id, :temp_exercise_description)';
        
        $query = $conn->prepare($sql);  
        $query->execute( array(
            "user_id"=> $user_id, 
            "temp_exercise_weight"=>$temp_exercise_weight,
            "temp_exercise_reps"=>$temp_exercise_reps,
            "temp_exercise_sets"=>$temp_exercise_sets,
            "exercise_id"=> $exercise_id['exerciseId'],
            "temp_exercise_description"=>$temp_exercise_description
          )); 
          $i++;   
      }             
    }

    return array(
      "completed" => true,
      "message" => $i . " Inputs entered into database"
    );
  }


  //Inputs and analysis
  function last_input_weight($conn, $exerciseId, $user_id){
      $query = $conn->prepare("SELECT weightDone FROM tbl_exercise_values where exerciseIdf =" . $exerciseId . " order by liftId  DESC LIMIT 1");
      $query -> execute();
      $row = $query->fetchALL(PDO::FETCH_ASSOC);        
      if( empty($row[0]['weightDone'])) return "";
      else return $row[0]['weightDone'];
  }     

//bug
  function amount_to_do($conn, $for, $exerciseId){
      $query = $conn->prepare("SELECT " . $for . " FROM tbl_exercises WHERE exerciseId = " . $exerciseId);
      $query -> execute();
      $row = $query->fetchALL(PDO::FETCH_ASSOC);        
      return $row[0][$for];
  }


//
// -- user_home
//

  function user_information_row($conn, $userId){
    $query = $conn->prepare("SELECT * FROM tbl_users WHERE userId=:userId");
    $query->bindParam(":userId", $userId, PDO::PARAM_INT);
    $query->execute();
    $user_rows = $query->fetchALL(PDO::FETCH_ASSOC);
    if(count($user_rows))
      return $user_rows[0]; //Will only return one result;
    else 
      return "UserId not found!";
  }

?>