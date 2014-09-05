<?php
    /* THIS PAGE CANNOT BE VIEWED IF THE USER IS LOGGED IN */
  ob_start();
  session_start();
  

  require_once "inc/config.php";
  require_once "inc/functions.php";
  require_once "inc/static_text/sign_up_reasons.php";

  $conn = connect($config['db']);
  if( !$conn ) die("Could not connect to DB");

  //Check if there is a login cookie, if there is just skip to the input
  if( isset($_COOKIE['user_id']) && isset($_COOKIE['keep_logged_in']) ){

    $cookie_user_id = $_COOKIE['user_id'];

    $user_rows = $conn->prepare("SELECT * FROM tbl_users WHERE userId = :userId");
    $user_rows->bindParam(":userId", $cookie_user_id, PDO::PARAM_INT);
    $user_rows->execute();
    $user_rows = $user_rows->fetchALL(PDO::FETCH_ASSOC);

    if(count($user_rows) > 0){
        $user_row = $user_rows[0];

        $Blowfish_Pre = '$2a$05$';
        $Blowfish_End = '$';
        $cookie_hash = crypt($user_row['loginPassword'], $Blowfish_Pre . $cookie_user_id . $Blowfish_End);
        
        if($_COOKIE['keep_logged_in'] == $cookie_hash){
            $_SESSION['myuserid'] = $_COOKIE['user_id'];
            header("Location: input.php?n");
        }
    } 
  }

    if(isset($_SESSION['myuserid'])){
        header("Location: input?n");
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>GymTracker</title>
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0" />
    
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/home_page_registration.css"> 

    <script type="text/javascript" src="js/jquery.js"></script>

    <link href="http://netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet" /> 
    <link href="http://fonts.googleapis.com/css?family=Abel|Open+Sans:400,600" rel="stylesheet" />

</head>
<body class="homepage">


<div class="topbar">
    <div class="nav">      
    
    <a href="/" id="logo" alt="The NetGym website" title="NetGym logo" rel="home">
        <img src="/img/logo3.png" alt="NetGym logo"/>
    </a>          
        
    </div><!-- nav -->
</div><!-- top bar -->


<style>
h3{
    margin-top:18px;
    font-size: 20px;
}

.left_float p{
    padding: 0;
    margin: 0;
    margin-top: 3px;
}

.hide{
    display: none;
}
</style>


<div class="float_container clearfix" style="margin-bottom: 50px;">

<div class="left_float">
    <h2 style="margin-bottom: 20px;">WHY SIGN UP?</h2>

        <?php $i = 0; ?>
        <?php foreach($reasons as $reason): ?>
            <h3>
                <?php echo $reason['title']; ?>
            </h3>
            <p>
                <?php echo $reason['body']; ?>
            </p>
            <?php if($i === 2): ?>
                <a style="text-decoration: none; cursor: pointer;" id="clickme">
                    <p style="font-weight:bold; margin-top: 5px; color: black; float:right;">
                        Need more of a reason? Read more.
                    </p>
                </a>

                <div class="hide">
            <?php endif ?>
        <?php $i++; ?>
        <?php endforeach?>



    <a style="text-decoration: none; cursor: pointer;" id="hideme">
        <p style="font-weight:bold; margin-top: 5px; color: black; float:right;">
            Hide Reasons.
        </p>
    </a>

    </div>
</div>

<script>

$('#hideme').click(function() {
    $('#clickme').css("display", "block");
  $('.hide').hide('slow', function() {
    //When animation is complete

  });
});


$('#clickme').click(function() {
    $('#clickme').css("display", "none");
  $('.hide').show('slow', function() {
    //When animation is complete
  });
});

</script>

<!-- The login form -->
<div class="right_float sign_in_float">
    <div class="home_register_login">


    <form method="post" action="check_login">
        <table>
                <tr>
                    <td class="left_col">Email</td>
                    <td class="right_col"><input type="text" name="myemail" placeholder="email@email.com"  value=""/></td>
                </tr>

                <tr>
                    <td class="left_col">Password</td>
                    <td class="right_col"><input type="password" name="mypassword" placeholder="Password" value=""/></td>
                </tr>

                <tr>
                    <td colspan="2"><input type="submit" value="Login"/></td>
                </tr>

                <tr>
                    <td colspan="2">
                            <p style="float: left; font-size:12px;">Stay logged in?</p>
                            <input type="checkbox" style="position: absolute;" name="stay_logged_in"/>
        
                        <a href="register?forgotten" style="text-decoration: none; float:right; color:black;">
                            <p style="text-align: right; font-size:12px;">
                                Forgotten password?
                            </p>
                        </a>
                    </td>
                </tr>
        </table>
    </form>

    </div>
</div>

<!-- The register form-->
<div class="right_float reg_float">
    <div class="home_register_login">

    <form method="post" action="register">
    <table>
        <tr>
            <td class="left_col">Displayed as</td>
            <td class="right_col"><input type="text" name="reg_display_name" placeholder="View yourself as"/></td>
        </tr>

        <tr>
            <td class="left_col">Email</td>
            <td class="right_col"><input type="text" name="reg_email" placeholder="myemail@email.com"/></td>
        </tr>

        <tr>
            <td class="left_col">Password</td>
            <td class="right_col"><input type="password" name="reg_password" placeholder="Password"/></td>
        </tr>



        <tr>
            <td colspan="2"><input type="submit" value="Register"/></td>
        </tr>
      
    </table>
    </form>

    </div>
</div>


<script>
    
$(function(){ // document ready
  var left_of_float = $(".reg_float").position().left;
  var stickyTop = $('.reg_float').offset().top - 50; // returns number 
  var originalMargin =  $('.reg_float').css("margin-top");

  $(window).scroll(function(){ // scroll event  
 
    var windowTop = $(window).scrollTop(); // returns number
 
    if (stickyTop < windowTop) {
      left_of_float = $(".reg_float").position().left;
      $('.reg_float').css({ position: 'fixed', top: 0, left: left_of_float, 'margin-top': 50});
    }
    else {
      $('.reg_float').css({
        'position' :'static', 
        'margin-top' : originalMargin});
    }
 
  });
 
});

</script>

<div style="clear: both;"></div>

</div><!-- End container -->

        
<?php
	require_once "inc/parts/footer.php";
?>
