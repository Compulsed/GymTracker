<?php
  $conn = connect($config['db']);
  if( !$conn ) die("Could not connect to DB");  

  $PostController = new ControllerPosts($conn);
  $posts = $PostController->get_posts(10);

?>

<style>
	.comment_link a:hover{
		color: rgb(119, 193, 88);
	}
</style>

<div class="page">

	<?php foreach($posts as $post) : ?>
	<?php 
 		$comment_count = $PostController->get_num_comments($post['postId']);
	?>
	<div class="information news">
		<div class="title_nav">
			<h1><a href="article/<?php echo strtolower(str_replace(' ', '-', $post['title'])); ?>"><?php echo $post['title']; ?></a></h1>
		</div>
	
		<div class="news_preview">
			<p><?php echo $post['previewText']; ?></p>
		</div>

		<!-- Clicking click to view comments -->
		<p><a href="article/<?php echo strtolower(str_replace(' ', '-', $post['title'])); ?>">
			<?php if($comment_count > 0): ?>
				<?php echo "Read the " . $comment_count . " comments." ?>
			<?php else: ?>
				Be the first to comment!
			<?php endif ?>
		</a></p>
		
		<!-- When comment was posted -->
		<div class="time_stamp">
			<p><?php echo "Created by " . ucfirst(user_id_to_display_name($conn, $post['author'])) . " on " .  date("F j, Y, g:i a",strtotime($post['creationDate']));?></p>
		</div>

	</div>
	<?php endforeach ?>

 </div>