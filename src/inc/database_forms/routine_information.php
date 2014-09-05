<?php
	  // $conn = connect($config['db']);
	  // if( !$conn ) die("Could not connect to DB");  	

	  // if(isset($_SESSION['myuserid'])){
	  // 	$user_id = $_SESSION['myuserid'];
	  // } else {
	  // 	$user_id = 0;
	  // }

	  // $routine_id = $_GET['routineId'];

	  // //Gets the routine name.
	  // $routine_name = $conn->query("SELECT routineName FROM tbl_routines WHERE routineId = $routine_id");
	  // $routine_name = $routine_name->fetchAll(PDO::FETCH_ASSOC);
	  // $routine_name = $routine_name[0]['routineName'];


	  // $exercise_ids = $conn->query("SELECT exerciseIdF from tbl_routines WHERE userIdF = $user_id AND routineName = '$routine_name'");
	  // $exercise_ids = $exercise_ids->fetchALL(PDO::FETCH_ASSOC);
?>



<!-- <div class="page">
	<div class="information">
		<h2><?php echo $routine_name; ?></h2>

		<?php foreach($exercise_ids as $an_exercise_id) : ?>
			<?php 
				$exercise_id = $an_exercise_id['exerciseIdF'];
				$exercise_name = $conn->query("SELECT name from tbl_exercises WHERE exerciseId = $exercise_id");
				$exercise_name = $exercise_name->fetchALL(PDO::FETCH_ASSOC);
				$exercise_name = $exercise_name[0]['name'];
			?>
			<p><?php echo $exercise_name; ?></p>
		<?php endforeach ?>


	</div>
</div> -->