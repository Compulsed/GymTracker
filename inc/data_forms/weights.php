<?php
  $conn = connect($config['db']);
  if( !$conn ) die("Could not connect to DB");

  if( isset($_GET['page']) && !empty($_GET['page']) && isset($_SESSION['myuserid']) ){
    $num_of_displayed_rows = 15;

    $user_id = $_SESSION['myuserid']; 
    $lInputEdit = new ControllerInputView($conn, $user_id, $_GET['page'], $num_of_displayed_rows); 
    
    //Number of the total pages that can be displayed
    $num_total_pages = $lInputEdit->return_total_pages_required();
    //The page number currently active
    $page = $lInputEdit->return_page_num();
    //Total number of rows in database;
    $total_stored_rows = $lInputEdit->return_total_of_input_rows();

    //All the rows to be displayed on current page
    $exercise_input_rows = $lInputEdit->return_page_rows();

   } else {
     //If not get values have been postest, the page wont be displayed correctly, so just redirect
     header("Location: input.php?weights&page=1");
     die();
   }

    $user_row = user_information_row($conn, $user_id); //This is used for help
?>

<div class="page">
    <div class="information">

    <div class="title_nav">
        <h1>View Inputs</h1>
        <p><a href="input?weights">view</a></p> 
        <p><a href="input?n">add</a></p>
        <p><a href="input?n_edit">edit</a></p> 
        <p><a href="input">| menu</a></p>  

        <p style="float:right; padding-top:13px;">
          <?php echo 
            "<a href='input?weights&page=" . ($page - 1) . "'> << </a>" .
            "Page: " . $page . " of " . $num_total_pages . 
            "<a href='input?weights&page=" . ($page + 1) . "'> >> </a>"; 
          ?>
        </p> 
    </div>

<?php if(count($exercise_input_rows)): ?>

<?php if($user_row['help']): ?>
    <div class="help">
      To view more information on an the input, just click on it.<br>
      To add an input, click "Add", to remove, click "Edit".
    </div>
<?php endif ?>

<div class="weight_edit_table">
<form action="" method="post">	
  <table>
  <thread>
    <tr>
      <th class="col_name">Ex Name</th>
      <th class="col_sets">Weight</th> 
      <th class="col_sets">Sets</th> 
      <th class="col_sets">Reps</th> 
      <th class="col_date">Date</th> 
      <th class="col_des">Notes</th>
    </tr>
  </thread>
  <tbody class="hover_selector">
  
<!--   /database.php?exId=181 -->

  <?php 
  foreach($exercise_input_rows as $row){
        $active = $row['active'];


        echo '<tr onclick="document.location = \'/database?exId=' . $row['exerciseId'] . '\';">';
        echo    '<td class="col_name">'    .  $row['exerciseName'] . 
                 '<td class="col_sets">'  .   $row['weight'] . '</td>' .
                '<td class="col_sets">'  .   $row['setsCompleted'] . '</td>' .
                '<td class="col_sets">'  .   $row['repsCompleted'] . '</td>' .
                '<td class="col_date">'   .   date("D, F jS g:i A",strtotime($row['timeCompleted'])) . '</td>' .
                '<td class="col_des">'   .   $row['inputDescription'] . '</td>';
         echo '</tr>';    
  }
  ?>       
  </tbody>
</table>  
</div>

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