<?php
  $conn = connect($config['db']);
  if( !$conn ) die("Could not connect to DB");

  if( isset($_GET['page']) && !empty($_GET['page']) && isset($_SESSION['myuserid']) ){
    $num_of_displayed_rows = 15;

    $user_id = $_SESSION['myuserid']; 
    $lInputEdit = new ControllerInputEdit($conn, $user_id, $_GET['page'], $num_of_displayed_rows); 
    //Checks what has been posted, and will update the selected value
    $delete_status = $lInputEdit->update_values_for_deletion();
    $recover_status = $lInputEdit->update_values_for_recovery();
    
    //Number of the total pages that can be displayed
    $num_total_pages = $lInputEdit->return_total_pages_required();
    //The page number currently active
    $page = $lInputEdit->return_page_num();
    //Total number of rows in database;
    $total_stored_rows = $lInputEdit->return_total_of_input_rows();
    
    //Stores the exercises ids of the exercise inputs where their base exercise has been removed
    $deleted_ex_ids = $lInputEdit->return_not_recoverable_ex_ids();

    //All the rows to be displayed on current page
    $exercise_input_rows = $lInputEdit->return_page_rows();

   } else {
     //If not get values have been postest, the page wont be displayed correctly, so just redirect
     header("Location: input.php?n_edit&page=1");
     die();
   }

  $user_row = user_information_row($conn, $user_id); //This is used for help

?>

<div class="page">
    <div class="information">

    <div class="title_nav">
        <h1>Edit Inputs</h1>
        <p><a href="input?weights">view</a></p> 
        <p><a href="input?n">add</a></p>
        <p><a href="input?n_edit">edit</a></p>
        <p><a href="input">| menu</a></p>  

        <p style="float:right; padding-top:13px;">
          <?php echo 
            "<a href='input.php?n_edit&page=" . ($page - 1) . "'> << </a>" .
            "Page: " . $page . " of " . $num_total_pages . 
            "<a href='input.php?n_edit&page=" . ($page + 1) . "'> >> </a>"; 
          ?>
        </p> 
    </div>

<?php if(count($exercise_input_rows)): ?>

<?php if($user_row['help']): ?>
  <div class="help">
    You may add or remove inputs as you wish, if they're removed they aren't displayed on any statistics.
  </div>
<?php endif ?>

<div class="weight_edit_table">
<form action="" method="post">	
  <table>
  <thread>
    <tr>
      <th class="col_name">Ex Name</th>
      <th class="col_name">Weight</th> 
      <th class="col_sets">Sets</th> 
      <th class="col_sets">Reps</th> 
      <th class="col_date">Date</th> 
      <th class="col_des">Notes</th>
      <th class="col_id">A/D</th>
    </tr>
  </thread>
  <tbody>
  
  <?php 
  foreach($exercise_input_rows as $row){
        $active = $row['active'];


        if($active)
          echo '<tr>';
        else 
          echo '<tr class="in_active">';

        echo    '<td class="col_name">'    .  $row['exerciseName'] . 
                 '<td class="col_name">'  .   $row['weight'] . '</td>' .
                '<td class="col_sets">'  .   $row['setsCompleted'] . '</td>' .
                '<td class="col_sets">'  .   $row['repsCompleted'] . '</td>' .
                '<td class="col_date">'   .   date("D, F jS g:i A",strtotime($row['timeCompleted'])) . '</td>' .
                '<td class="col_des">'   .   $row['inputDescription'] . '</td>';
                
                if(in_array($row['liftId'], $deleted_ex_ids))
                  echo '<td class="col_id"><input type="checkbox" disabled="disabled"></td>';  
                else if($active) 
                  echo '<td class="col_id"><input type="checkbox" name="delete_item' . $row['liftId'] . '">' . '</td>';
                else 
                  echo '<td class="col_id"><input type="checkbox" name="recover_item' . $row['liftId'] . '">' . '</td>';

         echo '</tr>';    
  }
  ?>       
  </tbody>
</table>  
  <input class="spec_input_buttom" type="submit" value="Alter">
</div> <!-- weight_edit_table -->

  <p style="float:right">
    <?php
      echo 'Showing ';
      if($total_stored_rows == $num_of_displayed_rows) 
        echo $num_of_displayed_rows; 
      else echo  count($exercise_input_rows);
      echo ' of '. $total_stored_rows . ' records.';
    ?>

</form>
<?php else: ?>
  <div class="help">
    There is no inputs to edit or change, click <a style="text-decoration: underline; font-weight: bold; color:white" href="input.php?n">here</a> to add one
  </div>
<?php endif ?>

    </div>
</div>
<?php
  //Shows a status for how the values have changed in the database
  if( ($delete_status["message"] != "") || ($recover_status["message"] != "") ){   //If both haven't changed anything return nothing
    $message = "";
    $success = false;

    if( ($delete_status["completed"]) && $recover_status["completed"]){
      $success = true;
      $message = $delete_status["message"] . " and " . $recover_status["message"];
    } else if( $delete_status["completed"] && $recover_status["message"] == ""){
      $success = true;
      $message = $delete_status["message"];
    } else if( $recover_status["completed"] && ($delete_status["message"] == "") ){
      $success = true;
      $message = $recover_status["message"];
    } else {
      $message = $delete_status["message"] . " and " . $recover_status["message"];
      $success = false;
    }


    if($success)
      echo "<div id=\"notify\" class=\"success\">" .  $message . "</div>";
    else 
      echo "<div id=\"notify\" class=\"errorr\">" .  $message . "</div>";
  }  
?>