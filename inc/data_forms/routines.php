<?php
  $conn = connect($config['db']);
  if( !$conn ) die("Could not connect to DB");
  $user_id = $_SESSION['myuserid'];
  
  $routines = parse_routine_information($conn, $user_id);

  $user_row = user_information_row($conn, $user_id); //This is used for help
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
        <h1>View Routine</h1>
        <p><a href="input?routines">view</a></p>
        <p><a href="input?r">add</a></p>
        <p><a href="input?r_edit">edit</a></p>
        <p><a href="input">| menu</a></p> 
    </div>


<!-- If there are exercises -->
<?php if(count($routines)): ?>

<?php if($user_row['help']): ?>
    <div class="help">
      To view more information on an the routine, just click on it.<br>
      To add an routine, click "Add", to remove, click "Edit".
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
        If there are no routines, to add one click <a style="text-decoration: underline; font-weight: bold; color:white" href="input.php?r">here</a><br>
        You can also add a routine from the database.
    </div>
<?php endif ?>

</div>
</div>