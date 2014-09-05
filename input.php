<?php
    require_once "inc/config.php";
	require_once "inc/functions.php";
	require_once "inc/parts/header.php";

    if(!isset($_SESSION['myuserid'])){
        header("Location: /");
    }

    //looks at the url and finds the get values to direct to certain pages

    //Input and edit new routines
    if(isset($_GET['r'])) 
        require "inc/data_forms/routine_input.php";

    else if(isset($_GET['r_edit']))
        require "inc/data_forms/routine_edit.php";

    else if(isset($_GET['routines']))
        require "inc/data_forms/routines.php";


    //Editing exercises and editting them
    else if(isset($_GET['e']))
        require "inc/data_forms/exercise_input.php"; 

    else if(isset($_GET['e_edit']))
        require "inc/data_forms/exercise_edit.php";

    else if(isset($_GET['exercises']))
        require "inc/data_forms/exercises.php";



    // New input into the database 
    else if(isset($_GET['n']) && isset($_GET['name']))
        require "inc/data_forms/weight_input.php";

    else if(isset($_GET['n']))
        require "inc/data_forms/weight_routine_selector.php";

    else if(isset($_GET['n_edit']))
        require "inc/data_forms/weight_input_edit.php";

    else if(isset($_GET['weights']))
        require "inc/data_forms/weights.php";

    //The selector for all the inputs
    else 
        require "inc/input_forms.php";

	require_once "inc/parts/footer.php";

?>