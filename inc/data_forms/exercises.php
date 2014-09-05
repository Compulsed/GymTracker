<?php
  //Wont work if the input has a zero
  
  $conn = connect($config['db']);
  if( !$conn ) die("Could not connect to DB");
  $user_id = $_SESSION['myuserid'];

  $rows = return_active_exercises_rows($conn, $user_id);
  
  //Gets user information
  $user_row = user_information_row($conn, $user_id);
?>

<div class="page">
    <div class="information">
    <div class="title_nav">
        <h1>View Exercises</h1>
        <p><a href="input?exercises">view</a></p>
        <p><a href="input?e">add</a></p>
        <p><a href="input?e_edit">edit</a></p>
        <p><a href="input">| menu</a></p> 
    </div>


<!-- Table that displays the current, active exercises -->
<?php if(count($rows)) : ?>

<!-- Information for the user who has help enabled-->
<?php if($user_row['help']) : ?>
    <div class="help">
      To view more information on an exercise, just click on it.<br>
      To add an exercise, click "Add", to remove, click "Edit".
    </div>
<?php endif ?>

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
    Please enter an exercise before it's displayed here!<br>
    You can add one from the database by clicking, "Database" and adding the ones you wish or<br>
    You can add your own by clicking "Add".
  </div>
<?php endif ?>


</div><!-- End information -->
</div><!-- End page -->
