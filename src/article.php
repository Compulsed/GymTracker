<?php
   require_once "inc/config.php";
	 require_once "inc/functions.php";
   require_once "inc/parts/header.php";

   $conn = connect($config['db']);
   if( !$conn ) die("Could not connect to DB");  
      $PostController = new ControllerPosts($conn);
      $found = false; //Checks to see if the article is in the database

      if(isset($_GET['title'])){
        $article = $PostController->get_article($_GET['title']);
    
        if($article['post']){
          $post = $article['post'];
          $comments = $article['comments'];
          $found = true;
        }

      }
?>

<?php if($found): ?>
<!-- If the page is found in the database -->
<div class="page">
    <div class="information news">
        <div class="title_nav">
            <h1><?php echo $post['title']; ?></h1>
        </div>
        
        <div class="news_preview">
          <p><?php echo $post['bodyText']; ?><p>
        </div>
        
        <div class="created_by">
          <p>
            <?php echo "Created by: " . ucfirst(user_id_to_display_name($conn, $post['author'])) . " on " .  date("F j, Y, g:i a",strtotime($post['creationDate']));?>
          </p>
        </div>
    </div>
</div>

<div class="page">
    <div class="information news">
        <div class="title_nav">
            <h1>Comments</h1>
        </div>
    </div>
</div>


<?php foreach($comments as $comment) : ?>
<!-- Displays all the comments asscioated with the record -->
<div class="page" id="comment_id_<?php echo $comment['commentId']; ?>">
  <div class="information">

     <?php if( isset($_SESSION['myuserid']) ) //Checks to see if the user is logged in, then if they're the user or admin
            if ( ($_SESSION['myuserid'] == $comment['userIdF']) || ( $_SESSION['myuserid'] == 1) ) : ?> 

      <a href="#!" class="delete_link" onclick="delete_comment(<?php echo $comment['commentId']; ?>);">
        <p style="float:right; font-size: 12px;">Delete</p>
      </a>
     <?php endif ?>

    <h2><?php echo $comment['title']; ?></h2>
    <p><?php echo $comment['bodyText']; ?><p>

    <div class="created_by">
    <p>
        <?php echo "Created by: " . ucfirst(user_id_to_display_name($conn, $comment['userIdF'])) . " on " .  date("F j, Y, g:i a",strtotime($comment['creationDate']));?>
    <p>
    </div>

  </div>
</div>
<?php endforeach ?>

<div id="posted_comments">
<!-- The newly posted comments get appended here -->
</div>

<?php if(isset($_SESSION['myuserid'])) : ?>
<!-- Comment box if the user is logged in -->
<div class="page">
  <div class="information">
  <h2>Add a comment</h2>

  <form class="comment">
    <p>Title</p> <input type="text" id="title_text" placeholder="Title of comment">
    <p>Body:</p><textarea cols="100" rows="20" id="body_text"></textarea><br>
    <?php 
    // post id
    $post_id = $PostController->title_string_to_id(strtolower(str_replace('-', ' ', $_GET['title'])));
    ?>
    <button type="button" onclick="add_comment(<?php echo $post_id;?>)">Post</button>
    <p id="status"></p>
  </form>

  </div>
</div>
<?php else: ?>
<!-- Alters the user that they must be logged in to comment -->
<div class="page">
  <div class="information">
    <h2>You must register to comment.</h2>
  </div>
</div>
<?php endif ?>

<?php else: ?>
<!-- Is displayed if the URL is bad or cannot be found in the database -->
  <div class="page">
    <div class="information">
        <div class="title_nav">
            <h1>Article not found</h1>
        </div>
        <p>Cannot find the article, check the url.</p>
    </div>
 </div>
<?php endif ?>


<?php    
   require_once "inc/parts/footer.php";
?>

