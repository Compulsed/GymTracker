<?php
  //Wont work if the input has a zero
  
  $conn = connect($config['db']);
  if( !$conn ) die("Could not connect to DB");
  $user_id = $_SESSION['myuserid'];


  // Returns a status to indicate whether it posted to the DB or not.
  $status = check_user_exercise_input($conn, $user_id, $ARRAY_OF_MUSCLE_GROUPS, 3);
  // Returns all exercise in the database given a connectio and user id;
  $rows = return_active_exercises_rows($conn, $user_id);
  
  //Gets user information
  $user_row = user_information_row($conn, $user_id);
?>

<div class="page">
    <div class="information">
    <div class="title_nav">
        <h1>Add Exercises</h1>
        <p><a href="input?exercises">view</a></p>
        <p><a href="input?e">add</a></p>
        <p><a href="input?e_edit">edit</a></p>
        <p><a href="input">| menu</a></p> 
    </div>

<!-- Information for the user who has help enabled-->
<?php if($user_row['help']) : ?>
    <div class="help">
      To create exercises, simply type in an exercise name, the amount of sets you wish and do and the amount of reps you wish to do in those sets<br>
      A small description is required for helping you remember how to do the exercise.
      If you wish to delete an exercise you've made, simply click edit, find the exercise then delete it.<br>
      Also, to get you started, you may wish to check in the database for an exercise that suits you, and then add it! 
    </div>
<?php endif ?>

<!-- An input form for exercises -->
<form action="" method="post">	
  <table class="input_table" style="width: 500px;">
      <tr>
        <th colspan="2">ENTER AN EXERCISE BELOW</td>
      </tr>

      <tr>
        <td class="col_sets_in">Ex Name:</td>
        <td><input type="text" name="name" ></td>
      </tr>
      
      <tr>
        <td class="col_sets_in">Sets:</td>
        <td><input type="text" name="sets"></td>
      </tr>
        
      <tr>
        <td class="col_sets_in">Reps:</td>
        <td><input type="text" name="reps"></td>
      </tr>
      
      <tr>
        <td class="col_sets_in">Muscle Groups:</td>
        <!-- <td><input type="text" name="muscle_group"></td> -->
        <td>
          <?php make_muscle_group_selector(3, $ARRAY_OF_MUSCLE_GROUPS); ?>
        </td>
      </tr>  

      <tr>
        <td class="col_sets_in">Description:</td>
        <td><input type="text" name="description"></td>
      </tr>
     
  </table>
<input type="submit" value="Enter Information" class="spec_input_buttom">  
</form>     
</div>

<!-- Table that displays the current, active exercises -->
<div class="information">
<h2>Current exercises</h2>
<?php if(count($rows)) : ?>
<div class="exercise_input_table">
<table>
  <thread>
    <tr>
      <th class="col_name">Name</th> 
      <th class="col_sets">Sets</th> 
      <th class="col_sets">Reps</th> 
      <th class="col_muscle">Groups</th>
      <th class="col_des">Descriptions</th>
      <th class="col_sets">Ins</th>
    </tr>
  </thread>
  <tbody class="hover_selector">
    <?php   
      foreach($rows as $row){
        echo '<tr  onclick="document.location = \'/database.php?exId=' . $row['exerciseId'] . '\';">';
        echo    '<td class="col_name">'     . $row['name'] . '</td>' .
                '<td class="col_sets">'     . $row['sets'] . '</td>' .
                '<td class="col_sets">'     . $row['reps'] . '</td>' .
                '<td class="col_musclue">'  . $row['muscleGroup']       . '</td>' .
                '<td class="col_des">'      . $row['description'] . '</td>' .
                '<td class="col_sets">' . num_of_inputs_for_ex($conn, $user_id, $row['exerciseId']) .  '</td>'; 
       echo '</tr>';
      } ?>
  
  </tbody>
</table>  
</div> <!-- exercise_input_table -->


<?php else: ?>
  <!-- The message that's shown when the user has no exercises -->
  <div class="help">
    Please enter an exercise before it's displayed here!
  </div>
<?php endif ?>


</div><!-- End information -->
</div><!-- End page -->

<?php
  if( $status["message"] != ""){
    if( $status["completed"] === true)
      echo "<div id=\"notify\" class=\"success\">" .  $status['message'] . "</div>";
    else 
      echo "<div id=\"notify\" class=\"error\">" .  $status['message'] . "</div>";
  }  
?>