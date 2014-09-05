<?php
    $conn = connect($config['db']);
    if( !$conn ) die("Could not connect to DB");

    $user_id = $_SESSION['myuserid'];

    $user_row = user_information_row($conn, $user_id); //This is used for help

    $routine_names = get_active_routine_names($conn, $user_id);

    
?>

<style>
    .selection_block{
        background: rgb(119, 193, 88);
    }
</style>



<div class="page">
    <div class="information">
    <div class="title_nav">
        <h1 style="display:inline;">Add Inputs</h1>
        <p><a href="input?weights">view</a></p> 
        <p><a href="input?n">add</a></p>
        <p><a href="input?n_edit">edit</a></p> 
        <p><a href="input">| menu</a></p> 
    </div>

<form action="" method="post">	 

    <?php if(count($routine_names)): ?> 

    <?php if($user_row['help']): ?>
      <div class="help">
        Select the routine you wish to use!
      </div>
    <?php endif ?>

    <p>Select today's routine!</p>    
    <div> 
        <?php 
            foreach($routine_names as $name){
                //Get all of the exercise names in the routine 
                $exercise_ids_query = $conn->query("SELECT exerciseIdF FROM tbl_routines WHERE userIdF = $user_id AND routineName = '$name' AND active = TRUE");
                $exercise_ids = $exercise_ids_query->fetchALL(PDO::FETCH_ASSOC);
                
                $str_exercise_names = "";
                 foreach($exercise_ids as $exerciseId){
                     $id = $exerciseId['exerciseIdF'];

                     $exercise_name_query = $conn->query("SELECT name FROM tbl_exercises WHERE exerciseId = $id AND active = true");
                     $exercise_name = $exercise_name_query->fetchALL(PDO::FETCH_ASSOC);

                     $str_exercise_names .= $exercise_name[0]['name'] . ', ';
                 }

                $str_exercise_names = substr($str_exercise_names, 0, -2);
                $str_exercise_names .= '.';
                echo '<a href="input?n&name=' . $name  . '"><div class="routine_selection"><p>'. $name . ' - ' . $str_exercise_names  . '</p></div></a>';
            }
         ?> 
    </div>

    <?php else: ?>
        <div class="help">
            No active routines, click <a href="input.php?r"style="text-decoration: underline; font-weight:bold; color:white">here</a> to make one.
        </div> 
    <? endif?>
</form>
    
    </div>
</div>

<?php

if(isset($_GET['input_status'])){
    echo "<div id=\"notify\" class=\"success\">" .  $_GET['input_status'] . "</div>";
}

?>