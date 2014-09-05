<?php
	  $conn = connect($config['db']);
	  if( !$conn ) die("Could not connect to DB");  	

	  if(isset($_SESSION['myuserid'])){
	  	$user_id = $_SESSION['myuserid'];
	  } else {
	  	$user_id = 0;
	  }

?>


<div class="page">
	<div class="information">
		<h2>Sort Exercises by muscle groups</h2>

	<?php foreach($ARRAY_OF_MUSCLE_GROUPS as $muscleGroup): ?>
		<p style="font-weight: bold;"><?php echo $muscleGroup ?></p>
		<?php 
			$all_exercises = $conn->query("SELECT * FROM tbl_exercises WHERE (share = true) AND active = true AND muscleGroup LIKE '%$muscleGroup%'");
			$all_exercises = $all_exercises->fetchALL(PDO::FETCH_ASSOC);
			echo '<p>';
			foreach($all_exercises as $exercise){
				if(!check_unique_name($conn, $user_id, $exercise['name']) ) 
					echo '<a style="color:red;" href="database.php?exId=' . $exercise['exerciseId'] . '">' . $exercise['name'] . '</a> ';
				else 
					echo '<a style="color:black;" href="database.php?exId=' . $exercise['exerciseId'] . '">' . $exercise['name'] . '</a> ';
			
			}
			echo '</p><br>';
		?>
	<?php endforeach ?>
	
	<div class="container">
		
		<div class="block inline_legend red"></div>
		<p class="inline_legend">You've added something with that name.</p>

		<div class="block inline_legend black"></div>
		<p class="inline_legend">You can copy this value.</p>
	
	</div>


	</div>
</div>