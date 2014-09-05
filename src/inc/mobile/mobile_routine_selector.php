<?php
$user_id = $_SESSION['myuserid'];
?>

        <style>
            h1{
                font-size: 75px;
            }   
            
            a{
                text-decoration: none;
            }

            h1{
              text-align: center;
              padding: 50px;
            }          

            .selector_button{
              width: 95%;
              text-align: center;
              height: 7%;
              border: 1px solid rgb(76, 174, 76);
              background: rgb(119, 193, 88);

              margin-bottom: 8%;
              color: white;

              font-size: 400%;

              border-radius: 7px;
            }


            .routine_group{
              margin-left: 3%;
            }

        </style>            

<title>Input</title>

<?php        
  $conn = connect($config['db']);
  if( !$conn ) die("Could not connect to DB");
    
  $routine_names = get_active_routine_names($conn, $user_id);
?>

<h1>Select Todays Routine</h1>
    
    <div class="routine_group">
      <?php foreach($routine_names as $name) echo '<a href="mobile.php?routine_name=' . $name  . '"><div class="selector_button">'. $name . '</div></a>'; 
    ?>  
    </div>
    

    </body>    
</html>