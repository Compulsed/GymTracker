<html>
<head>
  <link rel="stylesheet" type="text/css" href="/css/reset.css">  
</head>

<body>

<style>

input{
	border-radius: 7px;
	border: 1px solid #e3e3e3;
}


.nav{
	position:fixed;

	border: none;	
	width:100%;
	padding: 1%;
	margin: 0px;

	background: rgb(119, 193, 88);

    border-bottom: 3px solid rgb(76, 174, 76);

	height: 90px;
}

.nav .splitter{
	border: none;
	width: 32%;
	height: 100%;
	padding: 0px;
	margin: 0px;
	display:inline-block;

    border-right: 3px solid rgb(76, 174, 76);

	text-align: center;
}

.nav a .splitter{
	color:white;
	font-size: 280%;
	text-decoration: none;
	font-weight: bold;


}

.pusher{
	height: 140px;
	width: 100%;
}

.title_text{
	padding-top: 3%;
	height: 100%;
}

li{
	list-style:none
}

</style>

	<div class="nav">


	<a href="/mobile.php">
		<div class="splitter">
			<li class="title_text">GymTracker</li>
		</div>
	</a>


	<?php if(isset($_SESSION['myuserid'])) : ?>
		<a href="/mobile.php">
			<div class="splitter">
				<li class="title_text">Routines</li>
			</div>
		</a>

		<a href="/mobile.php?logout">
			<div class="splitter">
				<li class="title_text">Logout</li>
			</div>
		</a>
	<?php endif ?>

	</div>

	<div class="pusher"></div>