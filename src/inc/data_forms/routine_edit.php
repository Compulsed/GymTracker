<?php
  $conn = connect($config['db']);
  if( !$conn ) die("Could not connect to DB");
  $user_id = $_SESSION['myuserid'];

  $status = check_deleted_routine_values($conn, $user_id);

  //Returns all user routine data

  $total_rows = count(get_routine_tables($conn, $user_id, 100000));
  $total_pages_possible = (int)($total_rows/15 +1);
  
  if(isset($_GET['page'])){
     $page = $_GET['page'];
     if($page < 1) $page = 1;
     if($page > $total_pages_possible) $page = $total_pages_possible;
  }  else $page = 1;
  
  //Loads in all of the user inputed data for exercise values
  $rows = get_routine_tables($conn, $user_id, 15, ( - 15 + (15*$page)) );

    $user_row = user_information_row($conn, $user_id);

?>

<div class="page">
    <div class="information">

    <div class="title_nav">
        <h1>Edit Routine</h1>
        <p><a href="input?routines">view</a></p>
        <p><a href="input?r">add</a></p>
        <p><a href="input?r_edit">edit</a></p>
        <p><a href="input">| menu</a></p> 

        <p style="float:right; padding-top:13px;">
          <?php echo 
            "<a href='input.php?r_edit&page=" . ($page - 1) . "'> << </a>" .
            "Page: " . $page . " of " . $total_pages_possible . 
            "<a href='input.php?r_edit&page=" . ($page + 1) . "'> >> </a>"; 
          ?>
        </p> 
    </div>

<?php if(count($rows)): ?>
  <?php if($user_row['help']): ?>
      <div class="help">
        Removing an exercise from routine just deletes it, if you wish to recover that exercise, simply add it back!<br>
        Also note, routines will also extend across pages.
      </div>
  <?php endif ?>

<form action="" method="post">	
<table>
  <thread>
    <tr>
      <th class="col_des">Routine Name</th> 
      <th class="col_des">Exercise Name</th>
      <th class="col_del">Del</th>
    </tr>
  </thread>
  <tbody>
    <?php   

      $temp_name = "";

      foreach($rows as $row){
            echo '<tr>';  
            
            if( $row['routineName'] != $temp_name){
              echo    '<td style="font-weight: bold">' . $row['routineName'] . '</td>'; 
              $temp_name = $row['routineName'];
            } 
            else
                echo    '<td></td>'; //echo empty row
            
             echo '<td>' . $row['exerciseName'] . '</td>';
             echo '<td><input type="checkbox" name="delete_item' . $row['routineId'] . '"></td>';

            echo '</tr>';
      } 
      ?>
  </tbody>
</table>     
<input class="spec_input_buttom" type="submit" value="Delete">
  <p style="float:right">
    <?php
      echo 'Showing ';
      if($total_rows < 15) 
        echo $total_rows; 
      else echo '15'; 
      echo ' of '. $total_rows . ' records.';
    ?>
  </p>
</form>
<?php else: ?>
  <div class="help">
    There are no routines, click <a style="text-decoration: underline; font-weight: bold; color:white" href="input.php?r">here</a> to add one.
  </div>
<?php endif ?> 

    </div>
</div>

<?php
  if( $status["message"] != ""){
    if( $status["completed"] === true)
      echo "<div id=\"notify\" class=\"success\">" .  $status['message'] . "</div>";
    else 
      echo "<div id=\"notify\" class=\"error\">" .  $status['message'] . "</div>";
  }  
?>