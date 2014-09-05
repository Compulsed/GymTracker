<?php
  if(isset($_GET['name']) && isset($_SESSION['myuserid']) ){ // Redirection if there is no value set
    $conn = connect($config['db']);
    if( !$conn ) die("Could not connect to DB");
    $user_id = $_SESSION['myuserid'];

    $status = check_for_inputs($conn, $user_id, $_GET['name']);

    //Gets user information
    $user_row = user_information_row($conn, $user_id);
    extract($user_row);

  } else {
    header("Location: /");
    die();
  }


?>

<style>
  .input_body input[type=text] {
  border: 1px solid #d3d3d3;
  height: 20px;
  border-radius: 3px;
  outline-color: rgb(119, 193, 88);
  padding-left: 10px;
  font-size: 15px;
}

.input_body p{
    font-size: 15px;   
}

tbody p{
    margin-top: 5px;
}

</style>

<div class="page">
    <div class="information">
        
    <div class="title_nav">
        <h1><?php echo $_GET['name']?></h1>
        <p><a href="input?weights">view</a></p> 
        <p><a href="input?n">add</a></p>
        <p><a href="input?n_edit">edit</a></p>  
        <p><a href="input">| menu</a></p>
    </div>    

<?php if($user_row['help']): ?>
    <div class="help">
        Enter the values done for today, then click "Input" and the information is stored for next time!<br>
        A description isn't requried, but it's recommened to help you improve on technique
    </div>
<?php endif ?>

<form action="" method="post">	  
<table>
    <thread>
        <tr>
            <th>Exercise</th>
            <th>Weight</th>
            <th>Sets</th>
            <th>Reps</th>
            <th>Notes</th>
        </tr>
    </thread>
    <tbody class="input_body">
      <?php $exercise_names = get_exercises_info($conn, $user_id, $_GET['name']);        
          foreach($exercise_names as $exercise){
                $exerciseId = $exercise->return_ex_id();
                $suggested_reps = amount_to_do($conn, "sets", $exerciseId, $user_id);
                $suggested_sets = amount_to_do($conn, "reps", $exerciseId, $user_id);
                echo '<tr>';
                    echo '<td><p>' . $exercise->return_ex_name() .'</p></td>';
                    echo '<td><input type="text" value="' . (last_input_weight($conn, $exerciseId, $user_id) + (rand(0, 6))) .'"name="' . 'exId' . $exerciseId . '"></td>';
                    echo '<td><input type="text" value="' . $suggested_reps . '" name="' . 'exIdSets' . $exerciseId . '"></td>';
                    echo '<td><input type="text" value="' . $suggested_sets . '" name="' . 'exIdReps' . $exerciseId . '"></td>';
                    echo '<td><input type="text" name="' . 'exIdDescription' . $exerciseId . '"></td>';
                echo '<tr>'; 
          }
        ?>
    </tbody>
</table>

    <input class="spec_input_buttom" type="submit" value="Input">
</form>
<br>    


    </div>
</div>

<?php
  if( $status["message"] != ""){
    if( $status["completed"] === true){
      //Goes the the routine selector and prints the completion message
      header("Location: input.php?n&input_status=" . $status['message']);
    }
    else 
      echo "<div id=\"notify\" class=\"error\">" .  $status['message'] . "</div>";
  }  
?>