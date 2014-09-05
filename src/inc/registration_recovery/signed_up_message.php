<div class="page">
    <div class="information">
		<div class="title_nav">
	        <h1>Congratulations, <?php echo $_GET['firstName']; ?>!</h1>
	    </div>
		<div class="help">
			You've signed up, feel free to login with your email, <?php echo $_GET['signed_up']; ?>
		</div>
    </div>
</div>

<script>

	$("#login_button").addClass("selected");

	var position_of_button = $("#login_button").offset();
	console.log("left:" + position_of_button.left);
	console.log("top:" + position_of_button.top);

	jQuery(".popup").css({
	    "position":"absolute", 
	    "top": $("#login_button").offset().top + 23 + "px",
	    "left": $("#login_button").offset().left  + "px",
	});

	$(".popup").toggle(500, function(){
		if( is_tog === false ){ 
			is_tog = true;
			$("#login_button").addClass("selected");
		}
		else {
			is_tog = false;
			$("#login_button").removeClass("selected");
		}
	});
	
</script>