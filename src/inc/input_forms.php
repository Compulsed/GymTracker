<?php
   $conn = connect($config['db']);
   if( !$conn ) die("Could not connect to DB");

    //Gets user information
    $user_row = user_information_row($conn, $user_id);
    extract($user_row);
?>

<div class="page">
    <div class="information">
    <div class="title_nav">
        <h1>Input into</h1>
    </div>

<?php if($user_row['help']) : ?>
    <div class="help">
        Select the item you wish to add to or change, if you want to delete from the field, just click "edit"
    </div>
<?php endif ?>

    <h3>Exercises</h3>
    <div style="margin-bottom: 30px;">
        <a href="input?e"><div class="selection_button selection_green">New Exercise</div></a>
        <a href="input?exercises"><div class="selection_button selection_green">View Exercises</div></a>
        <a href="input?e_edit"><div class="selection_button selection_green">Edit Exercises</div></a>
    </div>

    <h3>Routines</h3>
    <div style="margin-bottom: 30px;">
        <a href="input?r"><div class="selection_button selection_blue">New Routine</div></a>
        <a href="input?routines"><div class="selection_button selection_blue">View Routines</div></a>
        <a href="input?r_edit"><div class="selection_button selection_blue">Edit Routines</div></a>
    </div>

    <h3>Inputs</h3>
    <div style="margin-bottom: 30px;">
        <a href="input?n"><div class="selection_button selection_red">New Input</div></a>
        <a href="input?weights"><div class="selection_button selection_red">View Inputs</div></a>
        <a href="input?n_edit"><div class="selection_button selection_red">Edit Inputs</div></a>
    </div>

    </div>
</div>