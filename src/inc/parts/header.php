<?php
        ob_start();
        session_start();

		date_default_timezone_set('Australia/Sydney');

        if(isset($_SESSION["myuserid"])){
			require_once "inc/config.php";
			require_once "inc/functions.php";
        	$conn = connect($config['db']);
			if( !$conn ) die("Could not connect to DB");

        	$user_id = $_SESSION["myuserid"];
        	$user_rows = $conn->query("SELECT * from tbl_users WHERE userId = $user_id");
        	$user_rows = $user_rows->fetchAll(PDO::FETCH_ASSOC);
        	$user_row = $user_rows[0];
        	extract($user_row);
        }
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />

	<title>GymTracker</title>

	<link rel="stylesheet" href="/css/reset.css">
	<link rel="stylesheet" href="/css/header_nav.css">
	<link rel="stylesheet" href="/css/news_page.css">
	<link rel="stylesheet" href="/css/table.css">
	<link rel="stylesheet" href="/css/popups.css">

	<!--<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>-->
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js" ></script>
	<script type="text/javascript" src="/js/Chart.min.js"></script>

	<script type="text/javascript" src="/js/website.js"></script>


</head>
<body>

<a href="mailto:djsalter@hotmail.com?Subject=Feedback">
<div class="css-vertical-text">
		Feedback
</div>
</a>

<div class="topbar">
	<div class="nav">

	<a href="/" id="logo" alt="The NetGym website" title="NetGym logo" rel="home">
		<img src="/img/logo3.png" alt="NetGym logo"/>
	</a>
		<ul><?php
			if(isset($_SESSION["myuserid"])){
				echo '<li id="logout">		<a href="/logout">Logout</a></li>' . PHP_EOL;
				echo '<li id="user">  		<a href="/profile">' . $firstName . '</a></li>' . PHP_EOL;
				//echo '<li id="#"><a href="#">Routines</a></li>' . PHP_EOL;
				// echo '<li id="database">	<a href="/database">Database</a></li>' . PHP_EOL;
				echo '<li id="articles">    <a href="/articles">Articles</a></li>' . PHP_EOL;
				echo '<li id="input"> 		<a href="/input">MY Workout</a></li>' . PHP_EOL;
				echo '<li id="go">    		<a href="/input?n">GO</a></li>' . PHP_EOL;
				// if($user_row['help'])
				// 	echo '<li id="learn"> 	<a href="/learn">INTRODUCTION</a></li>' . PHP_EOL;
			} else { //Is the output when the user is not logged in
				echo '<li id="login_button"><a href="#">Login</a></li>' . PHP_EOL;
				echo '<li id="register">    <a href="/register">Register</a></li>' . PHP_EOL;
				// echo '<li id="#">			<a href="/about">About</a></li>' . PHP_EOL;
				//echo '<li id="#"><a href="/">Routines</a></li>' . PHP_EOL;
				// echo '<li id="database">	<a href="/database">Database</a></li>' . PHP_EOL;
				echo '<li id="articles">	<a href="/articles">Articles</a></li>' . PHP_EOL;
			}?>

 			<?php if(!isset($_SESSION['myuserid'])): ?>
			<!-- Is only displayed if logged out -->

			<div class="popup">
				<form method="post" action="/check_login">

				<table>

					<tr>
						<td colspan="2"><p style="font-size: 18px; font-weight: bold; text-align: right;">Sign in</p></td>
					</tr>

					<tr>
						<td><p class="title">Email:</p></td>
						<td><input name="myemail" type="text" value=""/></td>
					</tr>

					<tr>
						<td><p class="title">Password:</p></td>
						<td><input name="mypassword" type="password" value=""/></td>
					</tr>

					<tr>
						<td colspan="2"><input type="submit" value="Login"></td>
					</tr>
				</table>

				<table>
					<tr class="logged_in_password">

						<td>
							<a href="register.php?forgotten">Forgotten password?</a>
						</td>

						<td>
							<p style="">Stay logged in?</p>
						</td>

						<td>
							<input style="width:20px;" type="checkbox" name="stay_logged_in" checked/>
						</td>

					</tr>
				</table>

				</form>
			</div>

 			<?php endif ?>
		</ul>

	</div><!-- nav -->
</div><!-- top bar -->
