<?php 
  if(!isset($_GET['routine_name'])) die(); // Change to a could not be loaded page

  $conn = connect($config['db']);
  if( !$conn ) die("Could not connect to DB");
  
  $user_id = $_SESSION['myuserid'];        
        
  $status = check_for_inputs($conn, $user_id, $_GET['routine_name']);
?>

<style>
 
    h1{
        font-size: 75px;
    }   

    .form_title{
        font-size: 50px;
    } 
    
    input{
         width: 100%;
         height: 75px;
         font-size: 40px;
    }
    
    .ex_title{
        font-weight: bold;
        font-size: 75px;
    }

    .box_wrapper{
      border-bottom: 1px solid black;
      padding-bottom: 50px;
      padding-top: 50px;
    }


    #submit_button{
        height: 8%;
        border:none;
        margin-top:20px;
        background: rgb(119, 193, 88);
        color:white;
        border: 1px solid rgb(76, 174, 76);
        border-radius: 7px;
    }

    .center_it{
      width: 90%;
      margin-left: 5%;
      margin-right: 5%;
    }

</style>

<div class="center_it">
  <h1><?php echo $_GET['routine_name']?></h1> 
  <p><?php echo $status['message'] ?></p>

  <form action="" method="post">    
          <?php
             $exercise_names = get_exercises_info($conn, $user_id, $_GET['routine_name']);   
             
              foreach($exercise_names as $exercise){
                  $exerciseId = $exercise->return_ex_id();
                  $suggested_reps = amount_to_do($conn, "sets", $exerciseId);
                  $suggested_sets = amount_to_do($conn, "reps", $exerciseId);
                      
                      echo '<div class="box_wrapper">';
                      echo '<p class="ex_title">' . $exercise->return_ex_name() . '</p>';
                      echo '<p class="form_title">Weight</p>';
                      echo '<input type="text" value="' . last_input_weight($conn, $exerciseId, $user_id) .'"name="' . 'exId' . $exerciseId . '">';
                      echo '<p class="form_title">Sets</p>';
                      echo '<input type="text" value="' . $suggested_reps . '" name="' . 'exIdSets' . $exerciseId . '">';
                      echo '<p class="form_title">Reps</p>';
                      echo '<input type="text" value="' . $suggested_sets . '" name="' . 'exIdReps' . $exerciseId . '">';
                      echo '<p class="form_title">Description</p>';
                      echo '<input type="text" name="' . 'exIdDescription' . $exerciseId . '">'  . '<br>';
                      echo '</div>';
              }
          ?>
      <input id="submit_button" type="submit" value="Input"><br>
  </form>
</div>

</body>
</html>