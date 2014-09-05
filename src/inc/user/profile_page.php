<?php
	$conn = connect($config['db']);
	if( !$conn ) die("Could not connect to DB");
	$user_id = $_SESSION['myuserid'];

    if(isset($_GET['help'])){
        $help_status = $_GET['help'];
        $conn->query("UPDATE tbl_users SET help = $help_status WHERE userId = $user_id");
        header("Location: profile.php");
    }

	$user_row = user_information_row($conn, $user_id);
    extract($user_row);

    //Deals with time and date
    $date = date("F jS Y",strtotime($user_row['joined']));
?>

<div class="page">
    <div class="information">
    	<div class="title_nav">
	    	<h1 style="display:inline;">Profile of: <?php echo ucfirst($user_row['firstName']) . ' ' . ucfirst($user_row['lastName']); ?></h1>
	    	<p style="display:inline;"><a href="profile.php?edit">(edit)</a></p>
	    </div>


    	<p>First name: <strong><?php echo ucfirst($user_row['firstName']); ?></strong></p>
    	<p>Last name: <strong><?php echo ucfirst($user_row['lastName']); ?></strong></p>
        <p>Display name: <strong><?php echo ucfirst($user_row['username']); ?></strong></p>
    	<p>Joined since: <strong><?php echo $date ?></strong></p>
    	
        <?php if($user_row['registeredEmail']) : ?>
            <p>Email: <strong><?php echo $user_row['emailAddress']; ?></strong></p>
        <?php else: ?>
            <p>Email: <strong><?php echo $user_row['emailAddress']; ?><a href="profile.php?register">(not registered)</a></strong></p>    
        <?php endif ?>

        <?php if($user_row['isKilo']) : ?>
            <p>View weight in: <strong>Kilos</strong></p>
        <?php else : ?>
            <p>View weight in: <strong>Pounds</strong></p>
        <?php endif ?>

        <?php if($user_row['help']) : ?>
            <p>Help enabled <strong><a href="profile.php?help=false">(disable)?</a></strong></p>
        <?php else : ?>
            <p>Help disabled <strong><a href="profile.php?help=true">(enable)?</a></strong></p>
        <?php endif ?>
        <!--  <p><a href="profile.php?user_data_back">Request input data</a></p> -->
    	<div class="help">
    		<h1>Statistics</h1>
    		Values entered into system: <?php echo inputs_done($conn, $user_id); ?><br>
            <?php 
                $a_most_done = most_done($conn, $user_id); 
                echo "Most done exercise is: " . $a_most_done['exerciseName'] . " at " . $a_most_done['frequency'] . " times.<br>" 
            ?>

    	</div>   
    </div>
    



<?php
    //See that the user had click the register button and posts an email
    if(isset($_GET['register']) ){ 
        $email_status = email_registraion($conn, $emailAddress, 5);
        if($email_status['completed'])
            echo "<div id=\"notify\" class=\"success\">" . $email_status['message'] .  "</div>";
        else 
            echo "<div id=\"notify\" class=\"error\">" . $email_status['message'] .  "</div>";
    } else if(!$user_row['registeredEmail']){ //Warns the user that they need to regist their email
        echo "<div id=\"notify\" class=\"error\">Please register your email! Click next to your email to do so!</div>";
    }
?>