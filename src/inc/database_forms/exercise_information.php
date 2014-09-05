<?php
	  $conn = connect($config['db']);
	  if( !$conn ) die("Could not connect to DB");  	

	  if(isset($_SESSION['myuserid'])){
	  	$user_id = $_SESSION['myuserid'];
	  } else {
	  	$user_id = 0;
	  }

	  if(isset($_GET['add'])){
	  	copy_exercise($conn, $_GET['add'], $user_id);
	  }

	  if(isset($_GET['copyId'])){
	  	copy_exercise_information_to_existing_exercise($conn, $_GET['exId'], $_GET['copyId'], $user_id);
	  }


	  $exercise_row;
	  if(!empty($_GET['exId']) && is_numeric($_GET['exId']) ){
	  	$exerciseId = $_GET['exId'];
	  	$exercise_rows = $conn->query("SELECT * FROM tbl_exercises WHERE (userIdF = $user_id OR share = TRUE) AND exerciseId = $exerciseId");
	  	$exercise_rows = $exercise_rows->fetchALL(PDO::FETCH_ASSOC);

	  	if(count($exercise_rows)){
	  		$exercise_row = $exercise_rows[0];
	  	} else {
	  		$exercise_row = NULL;
	  	}
	  }
?>

<!-- If there is a user row, if not null is set -->
<?php if(isset($exercise_row)): ?>
<?php
	$add_to_string = "'database.php?exId=" . $_GET['exId'] . "&add=" . $_GET['exId'] . "'";
?>

<div class="page">
	<div class="information">
	    <div class="title_nav">
	        <h2 style="display:inline"><?php echo $exercise_row['name']; ?></h2>
	        <p style="display:inline"><a href="database.php">Back</a></p>
			
			<!-- If the user dosn't own the recrod -->
			<?php if($exercise_row['userIdF'] != $user_id): ?>
				<?php if(!check_unique_name($conn, $user_id, $exercise_row['name']) ): ?>
				<div style="display:inline" class="red">Added</div>
				<div style="display:inline" class="red">
					<a href=<?php echo 'database.php?exId=' . id_of_user_ex_with_same_name($conn, $user_id, $exercise_row['name']) ?>>View MY Exercise</a>
				</div>
				<div style="display:inline" class="red">
					<a href=<?php echo 'database.php?exId=' , id_of_user_ex_with_same_name($conn, $user_id, $exercise_row['name']) . '&copyId=' . $exercise_row['exerciseId']; ?>>
						Copy Meta information to My record
					</a>
				</div>


				<?php elseif($exercise_row['share'] && isset($_SESSION['myuserid'])) : ?>
		       	<div style="display:inline" class="red"><a href=<?php echo $add_to_string ?>>Copy to MY Exercises</a></div>
		       	<?php endif ?>
			<?php endif ?>

	    </div>

		<table>

	<?php if(!$exercise_row['active']):?>
		<tr>
			<td colspan="2" style="background: rgba(255, 0, 0, .5); text-align: center">NOT ACTIVE!</td>
		</tr>
	<?php endif ?>

	<?php if($exercise_row['userIdF'] == $user_id): ?>
		<tr>
			<td>Completed</td>
			<td><?php echo num_of_inputs_for_ex($conn, $user_id, $exercise_row['exerciseId']) ?></td>
		</tr>
	<?php endif ?>

		<tr>
			<td>Rating</td>
			<td><?php if($exercise_row['rating'] != NULL) echo $exercise_row['rating']; else echo "Unrated";  ?></td>
		</tr>

		<tr>
			<td>Sets</td>
			<td><?php echo $exercise_row['sets']; ?></td>
		</tr>
		
		<tr>
			<td>Reps</td>
			<td><?php echo $exercise_row['reps']; ?></td>
		</tr>
		
		<tr>
			<td>Description</td>
			<td><?php echo $exercise_row['description']; ?></td>
		</tr>

		<tr>
			<td>Muscle Groups</td>
			<td><?php echo $exercise_row['muscleGroup']; ?></td>
		</tr>

		<?php if(($exercise_row['userIdF'] == $user_id) ): ?>
		<tr>
			<td>Progress</td>
			<td>
				<?php if( num_of_inputs_for_ex($conn, $user_id, $exercise_row['exerciseId']) > 1 ): ?>
					<canvas id="canvas" height="615" width="820"></canvas>
				    <script>
				    	  var canvas = document.getElementById("canvas");
						  var context = canvas.getContext("2d");
						  context.fillStyle = "Black";
						  context.font = "bold 32px Arial";
						  context.fillText("Fetching progress...", 820/2-150, 615/2);

				    	  set_progress_data(<?php echo $exercise_row['exerciseId']; ?>);
				    </script>
				<?php else: ?>
					Must have 2 or more values in this exercise before progress is displayed.
				<?php endif ?>
			</td>
		</tr>
		<?php endif ?>
		

		<?php if($exercise_row['mediaUrl']) : ?>
		<tr>
			<td>Media url</td>
			<td><?php 
				$url = $exercise_row['mediaUrl'];
				echo '<iframe width="820" height="615" src="//www.youtube.com/embed/' . $url . '" frameborder="0" allowfullscreen></iframe>'; 
			?></td>
		</tr>
		<?php endif ?>

		<!-- If the user owns the record -->
		<?php if($exercise_row['userIdF'] == $user_id): ?>
		<tr>
			<td>Comments</td>
			<td>
				<?php 
				$comments = arr_of_comments($conn, $exercise_row['exerciseId']);
				if(count($comments)){	
					echo  "<ul style='list-style: circle; margin-left: 30px;'>";
					foreach($comments as $comment){
						echo "<li>" . $comment . "</li>";
					}
					echo "</ul>";
				} else 
					echo "No comments on this record";
				?>
			</td>
		<tr>
		<?php endif ?>
		


		<tr>
			<td>Time created</td>
			<td><?php echo $exercise_row['timeCreated']; ?></td>
		</tr>

		</table>
	</div>
</div>

<!-- The exercise record is not found or resitricted -->
<?php else: ?>
<div class="page">
	<div class="information">
		<div class="help">
			Record not found or inaccessible. 
			<p style="display:inline; color:white;"><a href="database.php">Back?</a></p>
		</div>
	</div>
</div>
<?php endif ?>