<?php
  $conn = connect($config['db']);
  if( !$conn ) die("Could not connect to DB");

  if( isset($_GET['page']) && !empty($_GET['page']) ){
    $num_of_displayed_rows = 15;

    $user_id = $_SESSION['myuserid'];
    $lInputEdit = new ControllerExerciseEdit($conn, $user_id, $_GET['page'], $num_of_displayed_rows); 

    $delete_status = $lInputEdit->update_values_for_deletion();
    $recover_status = $lInputEdit->update_values_for_recovery();

    $num_total_pages = $lInputEdit->return_total_pages_required();    //Number of the total pages that can be displayed
    $page = $lInputEdit->return_page_num();                           //The page number currently active
    $total_stored_rows = $lInputEdit->return_total_of_input_rows();   //Total number of rows in database;
    $rows = $lInputEdit->return_page_rows();                          //Returns rows of the data to be displayed

    $user_row = user_information_row($conn, $user_id);

  } else {
   header("Location: input?e_edit&page=1");   //If not get values have been postest, the page wont be displayed correctly, so just redirect
   die();
 }

?>

<div class="page">
    <div class="information">
      <div class="title_nav">
          <h1>Edit Exercises</h1>
          <p><a href="input?exercises">view</a></p>
          <p><a href="input?e">add</a></p>
          <p><a href="input?e_edit">edit</a></p>
          <p><a href="input">| menu</a></p> 
          
          <p style="float:right; padding-top:13px;">
            <?php echo 
              "<a href='input.php?e_edit&page=" . ($page - 1) . "'> << </a>" .
              "Page: " . $page . " of " . $num_total_pages . 
              "<a href='input.php?e_edit&page=" . ($page + 1) . "'> >> </a>"; 
            ?>
          </p> 
      </div>

<?php if(count($rows)): ?>
    <?php if($user_row['help']) : ?>
    <div class="help">
      Removing an exercise will flag all that data asscioated with that exercise as deleted.<br>
      Recoving an exercise will only recover that exercise, but to recover the rest of the data you will have to manually edit it all back.
    </div>
    <?php endif ?>

<div class="exercise_input_table">
<form action="" method="post">
<table>
  <thread>
    <tr>
      <th class="col_name">Name</th> 
      <th class="col_sets">Sets</th> 
      <th class="col_sets">Reps</th> 
      <th class="col_des">Descriptions</th>
      <th class="col_sets">Ins</th>
      <th class="col_id">Alter</th>
    </tr>
  </thread>
  <tbody>
    <?php   
      foreach($rows as $row){
        $active = $row['active'];

        if(!$active)
          echo '<tr class="in_active">';
        else 
          echo '<tr>';

                     echo  '<td class="col_name">' . $row['name'] . '</td>' .
                           '<td class="col_sets">' . $row['sets'] . '</td>' .
                           '<td class="col_sets">' . $row['reps'] . '</td>' .
                           '<td class="col_des">' .  $row['description'] . '</td>' .
                           '<td class="col_sets">' . num_of_inputs_for_ex($conn, $user_id, $row['exerciseId']) .  '</td>';
        if(!$active) echo  '<td class="col_id in_active"><input type="checkbox" name="exer_recover_item' . $row['exerciseId'] . '"></td>';
        else         echo  '<td class="col_id"><input type="checkbox" name="exer_delete_item' . $row['exerciseId'] . '"></td>';

       echo '</tr>';
      } ?>
  </tbody>
</table>
<input type="submit" value="Alter" class="spec_input_buttom">

  <p style="float:right">
    <?php
      echo 'Showing ';
      if($total_stored_rows == $num_of_displayed_rows) 
        echo $num_of_displayed_rows; 
      else echo  count($rows);
      echo ' of '. $total_stored_rows . ' records.';
    ?>    

</form>
</div> <!-- exercise_input_table -->
<?php
  //Shows a status for how the values have changed in the database
  if( ($delete_status["message"] != "") || ($recover_status["message"] != "") ){   //If both haven't changed anything return nothing
    echo "<div id=\"notify\" class=\"success\">" .  "Database updated" . "</div>";
  }  

?>

<?php else: ?>
  <div class="help">
    There are no exercises to alter, click <a style="text-decoration: underline; font-weight: bold; color:white" href="input.php?e">here</a> to add an exercise
  </div>
<?php endif ?>
    

    </div>
</div>

