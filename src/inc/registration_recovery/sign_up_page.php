<?php
    $conn = connect($config['db']);
    if( !$conn ) die("Could not connect to DB");

    $status = check_register_information($conn);

    //If the username is registered use it then go to homepage, auto sign in 
    if($status['completed'] == true){
        header('Location: register.php?signed_up=' . $status['email'] . '&firstName=' . $status['firstName']);
    }

    if(isset($_POST['reg_email']) && isset($_POST['reg_password']) && isset($_POST['reg_display_name'])){
        $sign_up_email = $_POST['reg_email'];
        $sign_up_password = $_POST['reg_password'];
        $sign_up_name = $_POST['reg_display_name'];

        if(!empty($sign_up_email) && !empty($sign_up_password) && !empty($_POST['reg_display_name'])){
            $sent_sign_up_values = true;
        }
    }  

    require_once "inc/static_text/sign_up_reasons.php";
?>

<style>
h3{
    padding: 0px;
    margin-top: 30px;
    font-size: 20px;
}

.left_float p{
    padding: 0;
    margin: 0;
}
</style>



<div class="float_container clearfix" style="width:960px">

<div class="left_float">
    <h2>WHY SIGN UP?</h2>

    <?php foreach($reasons as $reason): ?>
        <h3>
            <?php echo $reason['title']; ?>
        </h3>
        <p>
            <?php echo $reason['body']; ?>
        </p>
    <?php endforeach?>

</div>

<div class="right_float">
    <div class="register">
    <h2 style="text-align: right">Register</h2>

    <form method="post">

    <table>
        <tr>
            <td class="left_col">*Email</td>
            <!-- If values sent from main page sign up -->
            <?php if(isset($sent_sign_up_values)): ?>
                <td class="right_col"><input type="text" name="email" placeholder="something@email.com"value=<?php echo '"' . $sign_up_email . '"' ?>></td>
            <?php else: ?>
                <td class="right_col"><input type="text" name="email" placeholder="something@email.com"value=<?php echo "'e" . randString(5) . "@gmail.com'"; ?>/></td>
            <?php endif ?>
        </tr>

        <tr>
            <td class="left_col">*Password</td>

            <!-- If values sent from main page sign up -->
            <?php if(isset($sent_sign_up_values)) :?>
                <td class="right_col"><input type="password" name="password1" placeholder="Password" value=<?php echo '"' . $sign_up_password .  '"'; ?>/></td>
            <?php else :?>
                <td class="right_col"><input type="password" name="password1" placeholder="Password" value=""/></td>
            <?php endif ?>
        </tr>

        <tr>
            <td class="left_col">*Repeat Password</td>
            <td class="right_col"><input type="password" name="password2" placeholder="Password" value=""/></td>
        </tr>

        <tr>
            <td class="left_col">*Display Name</td>
            <!-- If values sent from main page sign up -->
            <?php if(isset($sent_sign_up_values)) :?>
                <td class="right_col"><input type="text" name="user" placeholder="Username" value=<?php echo '"' . $sign_up_name .  '"'; ?>/></td>
            <?php else: ?>
                <td class="right_col"><input type="text" name="user" placeholder="Username" value=<?php echo "'u" . randString(5) . "'"; ?> /></td>
            <?php endif ?>
        </tr>

        <tr>
                <td class="left_col">*First</td>
                <td class="right_col"><input type="text" name="first" placeholder="John" value="TESTUSER"></td>
        </tr>

        <tr>
            <td class="left_col">*Last</td>
            <td class="right_col"><input type="text" name="last" placeholder="Doe" value="Doe"/></td>
        </tr>

        <tr>
            <td class="left_col">*Presets</td>
            <td class="right_col"><input style="width=20px;" type='checkbox' name='default_values' checked/></td>
        </tr>


        <tr>
            <td class="left_col">*Weight in kg</td>
            <td class="right_col"><input style="width=20px;" type='checkbox' name='is_kilo' checked/></td>
        </tr>

        <tr>
            <td colspan="2"><input type="submit" value="GO!"/></td>
        </tr>
        

    </table>
    </form>

    <?php 
     if(!$status['completed']) //Failed to register the use
        echo '<p style="color:#CD2626;"><strong>' . $status['message'] . '</strong></p>'; 
    ?>
        
    </div>
</div>

<div style="clear: both;"></div>

</div><!-- End container -->

<script>

$(function(){ // document ready
  var left_of_float = $(".right_float").position().left;
  var stickyTop = $('.right_float').offset().top; // returns number 
  var originalMargin =  $('.right_float').css("margin-top");

  $(window).scroll(function(){ // scroll event  
 
    var windowTop = $(window).scrollTop(); // returns number
 
    if (stickyTop < windowTop) {
      $('.right_float').css({ position: 'fixed', top: 0, left: left_of_float, 'margin-top': 5});
    }
    else {
      $('.right_float').css({
        'position' :'static', 
        'margin-top' : originalMargin});
    }
 
  });
 
});

</script>


    
    