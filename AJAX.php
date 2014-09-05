<?php
//Comment

require_once "inc/config.php";
require_once "inc/functions.php";

$conn = connect($config['db']);
if( !$conn ) die("Could not connect to DB");

//Deletes the comment
if( isset($_GET['comment_id']) ){

    ob_start();
    session_start();

    $comment_id = $_GET['comment_id'];
    $user_id = $_SESSION['myuserid'];

	$PostController = new ControllerPosts($conn);
	$status = $PostController->remove_comment($comment_id, $user_id);

	 $message = array();

	 if($status)
	 	$message['message'] = "Success";
	 else
	 	$message['message'] = "Failed";

	 echo json_encode($message);
}

//Adds the comment
if(isset($_GET['post_id']) && isset($_GET['title_text']) && isset($_GET['body_text']) ){
    session_start();
    
	$return_information = array(
		"new_comment" => NULL,
		"creation_string" => NULL
	);

    $user_id = $_SESSION['myuserid'];

	$PostController = new ControllerPosts($conn);

    $user_id = $_SESSION['myuserid'];
	$PostController->add_comment($_GET['post_id'], $user_id, $_GET['title_text'], $_GET['body_text']);

	$new_comment = $conn->query("SELECT * from tbl_comments WHERE userIdF = $user_id ORDER BY commentId DESC");
	$new_comment = $new_comment->fetchALL(PDO::FETCH_ASSOC);
	$new_comment = $new_comment[0];


	$return_information['creation_string'] = "Created by: " . ucfirst(user_id_to_display_name($conn, $new_comment['userIdF'])) . " on " .  date("F j, Y, g:i a",strtotime($new_comment['creationDate']));
	$return_information['new_comment'] = $new_comment['commentId'];

	echo json_encode($return_information);
}

if(isset($_GET['exercise_id'])){
	
		$return_values = array(
			"labels"=> "",
			"data"=> ""
		);

		$sql = "SELECT * FROM tbl_exercise_values WHERE exerciseIdF = :exercise_id";
		$exercise_values = $conn->prepare($sql);
		$exercise_values->bindParam(":exercise_id", $_GET['exercise_id'], PDO::PARAM_INT);
		$exercise_values->execute();
		$exercise_values = $exercise_values->fetchAll(PDO::FETCH_ASSOC);

		$num_exercise_values = count($exercise_values);

		$i = 1;
		foreach($exercise_values as $exercise_value){
			$return_values['labels'][$i - 1] = "Week " . $i;
			$return_values['data'][$i - 1] =  $exercise_value['weightDone'];
			$i++;
		}

		echo json_encode($return_values);	
}

?>

