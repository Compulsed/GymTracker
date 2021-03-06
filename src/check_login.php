<?php
require_once "inc/config.php";
require_once "inc/functions.php";

$conn = connect($config['db']);
if( !$conn ) die("Could not connect to DB");

require_once "inc/parts/header.php";

$valid_form = true;
if( !isset($_POST['mypassword']) || !isset($_POST['myemail'])){
    $valid_form = false;   
} 

if($valid_form){
    // username and password sent from the form 
    $myemail=strtolower($_POST['myemail']); 
    $mypassword=$_POST['mypassword']; 

    if( empty($myemail) || empty($mypassword)){
        $valid_form = false;
    }
}

if($valid_form){
    //Collect information on user given a username and password
    $sql = "SELECT salt, loginPassword, username, emailAddress, userId, firstName FROM tbl_users WHERE emailAddress = '$myemail'";
    $query = $conn->prepare($sql);
    $query->execute();
    $rows = $query->fetchALL(PDO::FETCH_ASSOC);
}

$logged_in = false;
if($valid_form && count($rows)){
    $user_information = $rows[0];

    $Blowfish_Pre = '$2a$05$';
    $Blowfish_End = '$';
    $hashed_pass = crypt($mypassword, $Blowfish_Pre . $user_information['salt'] . $Blowfish_End);

    if ($hashed_pass == $user_information['loginPassword'] ){
        $status =  'Logging in!';
        $logged_in = true;

        $_SESSION['myuserid'] = $user_information['userId'];
        $user_row = user_information_row($conn, $_SESSION['myuserid']);

        $conn->query("UPDATE tbl_users SET lastTimeActive = now() WHERE userId = " . $_SESSION['myuserid']);

        /* 
            The cookie is done with a hashed userid + stored hashed password
        */

        //Set cookies for a month
        if(isset($_POST['stay_logged_in']) && $_POST['stay_logged_in'] == true){
            setcookie("user_id", $user_information['userId'], time() + 60*60*24*30);
            
            $cookie_hash = crypt($hashed_pass, $Blowfish_Pre . $user_information['userId'] . $Blowfish_End);
            setcookie("keep_logged_in", $cookie_hash, time() + 60*60*24*30);
        }
    } else {
        $logged_in = false;
        $status = 'There was a problem with your email or password.';
    }
} else {
        $logged_in = false;
        $status = 'There was a problem with your login';        
}



?>

<div class="page">
	<div class="information">
            <div class="help" style="text-align:center;">
                <?php echo $status ?> 
            </div>
        </div>
</div>

<?php require 'inc/parts/footer.php'; ?>

<?php if($logged_in) : ?>
    <script type="text/javascript">
        <?php if(!$user_row['help']) : ?>
            setTimeout("window.location='/input?n'",2000);
        <?php else : ?>
            setTimeout("window.location='/input?n'",2000);
        <?php endif ?>
    </script>
<?php else: ?>
    <script>setTimeout("window.location='/'",2000)</script>
<?php endif ?>
