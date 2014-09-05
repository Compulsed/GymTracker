<?php
  $conn = connect($config['db']);
  if( !$conn ) die("Could not connect to DB");
  $user_id = $_SESSION['myuserid'];
  
  $status = check_if_valid_form($conn, $user_id, 6);
  $routines = parse_routine_information($conn, $user_id);

    //Gets user information
    $user_row = user_information_row($conn, $user_id);
    extract($user_row);
?>

<style>
    dl{
        margin-top: 10px;
    }
    dt{
        cursor: pointer;
        font-weight: bold;
        font-size: 15px;
        line-height: 2em;
        background: #e3e3e3;
        border-bottom: 1px solid #c5c5c5;
        border-top: 1px solid white;
    }
    dd{
        font-size: 15px;
        margin: 0;
        padding: 1em 0;
    }
    .hide{
        display: none;
    }
</style>



<div class="page">
    <div class="information">          
    <div class="title_nav">
        <h1>Add Routine</h1>
        <p><a href="input?routines">view</a></p>
        <p><a href="input?r">add</a></p>
        <p><a href="input?r_edit">edit</a></p>
        <p><a href="input">| menu</a></p> 
    </div>



<div>
    <form action="" method="post">	
        
        <h2 style="display:inline">Routine Name:</h2>
        <input style="display:inline" type="text" name="routineName"><br>

        <h2 style="display:inline">Exercises:</h2>   
        <?php print_exercise_collection($conn, $user_id, 6); ?>

        <br>
    <input class="spec_input_buttom" type="submit" value="Input">
    </form>
</div>

<?php if(count($routines)): ?>
    
<?php if($user_row['help']): ?>
    <div class="help">
        Create your routines by making a routine name then selecting the exercises you wish to have in your routine.<br>
        To edit add exercises to your routine just type in exactly the same routine name and then press the submit button.<br>
        To delete information, click edit next to the "Add routine title"<br>
    </div>
<?php endif ?>

<?php 
    echo '<dl>';
    foreach($routines as $routine){
        echo '<dt>' . $routine->return_routine_name() . '</dt><dd>';
        for($i = ($routine->return_exercise_size()); $i > 0; $i-- ){
            if($i != 1)
                echo  $routine->popExercise() . ', ';
            else 
                echo $routine->popExercise() . '.';
        }
        echo '</dd>';
    }
    echo '</dl>';
?>    

<script>
    (function(){
        $('dd').filter(':nth-child(n+4)').addClass('hide');

        $('dl').on('mouseenter', 'dt', function(){
            $(this)
                .next()
                    .slideDown(200)  
                    .siblings('dd')
                        .slideUp(200);
        });
    })();
</script>

<?php else: ?>
    <div class="help">
        There are no active routines, to add one select a name and at least one exercise.<br>
        If there are no exercises, to add one click <a style="text-decoration: underline; font-weight: bold; color:white" href="input.php?e">here</a>
    </div>
<?php endif ?>

<?php
  if( $status["message"] != ""){
    if( $status["completed"] === true)
      echo "<div id=\"notify\" class=\"success\">" .  $status['message'] . "</div>";
    else 
      echo "<div id=\"notify\" class=\"error\">" .  $status['message'] . "</div>";
  }  
?>

</div>
</div>