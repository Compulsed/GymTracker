<?php

$conn = connect($config['db']);
if( !$conn ) die("Could not connect to DB");

if($_GET['reg_token']){
	$status = check_if_registration_hash($conn, $_GET['reg_token']);
} else {
	header("Location: /");
}

?>

<div class="page">
    <div class="information">
		<div class="title_nav">
	        <h1>Email registration</h1>
	    </div>
		<div class="help">
			<?php if($status['valid']): ?>
				Your email has been registered!
			<?php else: ?>
				<?php echo $status['message']; ?>
		<?php endif?>
		</div>
    </div>
</div>