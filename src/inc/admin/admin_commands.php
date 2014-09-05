<?php
  $conn = connect($config['db']);
  if( !$conn ) die("Could not connect to DB");
  
    //Makes sure there is a user logged in.
    if(isset($_SESSION['myuserid'])){
        $user_id = $_SESSION['myuserid'];
    } else {
        header("Location: /");
    }

    //For completion status, default display nothing
    $status = array(
        "completed" => false,
        "message" => ""
    );

    //Controls 
    $PostController = new ControllerPosts($conn);

    if(isset($_POST['title']) && isset($_POST['body']) && isset($_POST['preview']) ){
        $title = $_POST['title'];
        $body = $_POST['body'];
        $preview = $_POST['preview'];
        if( !empty($title) && !empty($body) && !empty($preview) ){

            if($PostController->is_unique_title($title)){
                if(!preg_match('#^[A-Z0-9 ]+$#i', $title)){
                    $status['message'] = "Title must contain only alphanumeric and spaces";   
                } else {
                    $PostController->input_data($title, $preview, $body, $user_id);
                    $status['message'] = "Posted user content";
                    $status['completed'] = true;
                } 
            } else {
                $status['message'] = "Title must be unique";
            }
        }
    }
?>

<style>
.red{
    color:red;
}

.orange{
    color:orange;
}

.live{
    color:rgb(119, 193, 88);
}

.done{
    text-decoration:line-through;
}
</style>

<div class="page">
    <div class="information">
        <div class="title_nav">
            <h1>New feed input</h1>
        </div>
        <p>Can only use alphanumeric values and spaces in the title</p>
        <form method="post">
            Title:<input type="text" name="title" style="display:block; width:500px;">            
            Preview:<textarea type="text" name="preview" cols="100" rows="10" style="display:block;"></textarea>
            Body:<textarea type="text" name="body" cols="100" rows="20" style="display:block;"></textarea>
            <input type="submit" value="Sumbit" style="display:block;">
        </form>
    </div>
</div>


<div class="page">
    <div class="information">
        <div class="title_nav">
            <h1>TO DO</h1>
        </div>
        <ol>
            <li class="">Consider a "learn tab".</li>
            <li class="">Implement graphs</li>
            <li class="live">Implement more statistics</li>
            <li class="live">Create an admin panel</li>
            <li class="live done">Allow for users not to need to put in all the values for input they might not complete their routine</li>
            <li class="live">Allow users to dynamically update an exercise given that it might not be in their routine</li>
            <li class="">Allow users to select exercises based on a predefined list</li>
            <li class="">Have a page for selecting exercises where there is a picture of the body and the user can select them off of that</li>
            <li class="live">Find a way to stop people who aren't logging in from accessing certain pages</li>
            <li class="live">The ability for users to change passwords/email?</li>
            <li class="live">Help options for the new users</li>
            <li class="live">Predefined Exercises and routines for new users</li>
            <li class="live">Check sql injection on everything</li>
            <li class="">Project clean up</li>
            <li class="live">Improve learn options</li>
            <li class="live">Check password length limit</li>
            <li class="">Implement a goals system</li>
            <li class="live">Hide config files</li>
            <li class="live">Learn about htaccess and include it</li>
            <li class="live">Fix and style the mobile verison</li>
            <li class="live">Have a site map and figure out how to optimize for google</li>
            <li class="live">Create a site name</li>
            <li class="live">Remove all default values</li>
            <li class="live">Have the only h1 title lag being in the header and hidden</li>
            <li class="live">Set up a .com.au</li>
            <li class="live">Do JS validation on all forms</li>
        </ol>
    </div>
</div>


<?php
  if( $status["message"] != ""){
    if( $status["completed"] === true){
      echo "<div id=\"notify\" class=\"success\">" .  $status['message'] . "</div>";
    }
    else 
      echo "<div id=\"notify\" class=\"error\">" .  $status['message'] . "</div>";
  }  
?>