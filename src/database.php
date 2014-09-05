<?php
    require_once "inc/parts/header.php";
	require_once "inc/config.php";
	require_once "inc/functions.php";


	if(isset($_GET['exId'])) //The full information on the exercise
		require_once "inc/database_forms/exercise_information.php";
	else if(isset($_GET['listByMuscleGroup']))
		require_once "inc/database_forms/list_by_muscle_groups.php";
	//else if(isset($_GET['listRoutines']))
	//	require_once "inc/database_forms/list_routines.php";
	//else if(isset($_GET['routineId']))
	//	require_once "inc/database_forms/routine_information.php";
	else 
		require_once "inc/database_forms/home_database_page.php";


    require_once "inc/parts/footer.php";
?>
