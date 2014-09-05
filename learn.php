<?php
    require_once "inc/config.php";
	require_once "inc/functions.php";
	require_once "inc/parts/header.php";
?>


<script>
function scrollToTop(){
	$('body').animate({
	   scrollTop: 0
	}, 'slow');
}
</script>


<style>
	.page .information h2{
		font-size: 35px;
	}

	.page .information h3{
		font-size: 25px;
	}

	img{
		margin-bottom: 25px;
	}

	.small{
		font-size: 10px;
	}
</style>

<div class="page">
	<div class="information">
		<h2>A quick introduction to GymTracker</h2>
		<div class="small">To remove this display, disable help on the user profile tab.</div>
		<ul style="padding:20px; list-style: circle;">
			<li>Adding a new exercise</li>
			<li>Creating new routine</li>
			<li>Using a routine</li>
		</ul>

		<h3 style="border-bottom: black 1px solid; margin-bottom: 25px;">Adding a new exercise</h3>
		<p>
			Gym tracker, if you haven't already been told is a method for recording and keeping track of your performace.
		</p><br>
		<p>
			If you've selected 'default values' on registeration you'll be presented with some prefined routines and exercises,
			if not, you'll be able to add and remove your own at any time by clicking, "MY Workout", in the top right hand corner.
		</p>
		<img src="img/my_work_loc.png">

		<p>
			To add a new exercise, select "New exercise".
		</p><br>
		<img src="img/new_exercise.png"><br>

		<p>
			Input all the information for the exercise, name of the exercise, the amount of sets reps you wish to complete with the given exercise and a small description to help remind yourself on how you do it
		</p>
		<img src="img/input_new_exercise.png"><br>


		<h3 style="border-bottom: black 1px solid; margin-bottom: 25px;">Creating new routine</h3>
		<p>Select "MY workout" on the top right hand corner.</p>
		<img src="img/my_work_loc.png">

		<p>Select "New routine"</p>
		<img src="img/new_routine.png">

		<p>For creating a routine, to add values to a routine use the name of the routine you wish to add to.</p>
		<p>To create an entirely new routine use a unique name</p>
		<p>Then just select some of the predefined exercises(defaults and user added) to the routine).<p>
		<p>Then just click "Enter information" to submit the values.</p>
		<img src="img/input_new_routine.png">

		<p>Check the values have been added correctly, if not, click edit to change them</p>
		<img src="img/check_values_added_to_routine.png">

		<h3 style="border-bottom: black 1px solid; margin-bottom: 25px;">Using a routine</h3>
		<p>To select a routine to add values to</p>
		<p>Click GO on the top navigation bar</p>
		<p>Then click the name of the routine you wish to use</p>
		<img src="img/select_routine.png">

		<p>To input in a routine, put all the weight values in, and if required add a short message for next time you do that particular exercise</p>
		<img src="img/input_into_routine.png">


		<a class="small" onClick="scrollToTop()" style="cursor: pointer">Back to top</a>
	</div>
</div>

<?php
	require_once "inc/parts/footer.php";
?>