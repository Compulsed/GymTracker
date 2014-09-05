
  //Deletes the comment
  function delete_comment(comment_id_js){
      //Posts the data to the server
      console.log("Delete button clicked");

      $.ajax({
           type: "GET",
           url: "/AJAX.php",
           async: true,
           dataType: "json",
           data: {
            'comment_id' : comment_id_js
           },
           success: function(JSON){
              console.log("Ajax says sucess for: " + comment_id_js);
              console.log("Completed?" + JSON.message);

                $("#comment_id_" + comment_id_js).fadeOut("slow", function(){
                $("#comment_id_" + comment_id_js).remove();
              });
           }
      });
  }

  //Adds the comment to the database
  function add_comment(post_id){
      console.log("Submit button clicked");

      var title_text = $('#title_text').val();
      var body_text = $('#body_text').val();
      
      //Makes sure there is a value stored in the form
      if(title_text !== "" && body_text !== ""){

        //Posts the data to the server
        $.ajax({
             type: "GET",
             url: "/AJAX.php",
             async: true,
             dataType: "json",
             data: {
              'title_text' : title_text,
              'body_text' : body_text,
              'post_id' : post_id
             },
             success: function(JSON){
                body_text = body_text.replace(/\n/g, '<br />');
                
                var post_string = "";
                post_string +=  '<div class="page" style="display:none;" id="comment_id_' + JSON.new_comment + '"><div class="information news"><a href="#!" onclick="delete_comment(' + JSON.new_comment + ')"><p style="float:right; font-size: 12px;">Delete</p></a>';
                post_string += '<h2>' +  title_text + '</h2><p>' + body_text +  '</p>';
                post_string += '<div class="created_by"><p>' + JSON.creation_string + '</p></div></div></div>';

                $('#posted_comments').append(post_string);  
                $('#comment_id_' + JSON.new_comment).fadeIn('slow');
                $('#title_text').val("");
                $('#body_text').val("");
             }
        });

      } else {
        $('#status').text("fill in both fields");
      }
  }


  //Loads the graphical information for progress
  function set_progress_data(exercise_id){  
      $.ajax({
         type: "GET",
         url: "/AJAX.php",
         async: true,
         dataType: "json",
         data: {
          'exercise_id' : exercise_id
         },
         success: function(JSON){ 
            var string_data = JSON.data;
            var float_data = new Array();

            for(var i=0; i<string_data.length; i++){ 
              float_data[i] = parseFloat(string_data[i]); 
            } 


            setTimeout(function(){
              var lineChartData = {
                  labels : JSON.labels,
                  datasets : [
                      {
                          fillColor : "rgba(151,187,205,0.5)",
                          strokeColor : "rgba(151,187,205,1)",
                          pointColor : "rgba(151,187,205,1)",
                          pointStrokeColor : "#fff",
                          data : float_data
                      }
                  ]
              }       

              //Fixes a bug where if the values are the same it wont draw a graph
              var unique = true;
              for(var i = 1; i < float_data.length; i++){  
                if(float_data[i-1] != float_data[i]){
                  unique = false;
                  break;
                }
              }
              if(unique){
                var canvas = document.getElementById("canvas");
                var context = canvas.getContext("2d");
                context.fillStyle = "Black";
                context.font = "bold 32px Arial";
                context.clearRect(0, 0, canvas.width, canvas.height)
                context.fillText("There have been no improvements here.", 820/2-300, 615/2);
              }

              console.log(JSON.labels);
              console.log(float_data);

              if(!unique) var myLine = new Chart(document.getElementById("canvas").getContext("2d")).Line(lineChartData, {});
            }, 0);
        }
      });
  }



  //If there is a notify element, fade it in
  $(document).ready(function(){       
    $("#notify").fadeIn(500);
  });


  //Checks the login button and applies correct styling
  $(document).ready(function(){
    var is_tog = false;

    $("#login_button").click(function(){
      $("#login_button").addClass("selected");

      var position_of_button = $("#login_button").offset();
      console.log("left:" + position_of_button.left);
      console.log("top:" + position_of_button.top);

      jQuery(".popup").css({
          "position":"absolute", 
          "top": $("#login_button").offset().top + 23 + "px",
          "left": $("#login_button").offset().left - 270 + "px",
      });

      $(".popup").toggle(500 ,function(){
        if( is_tog === false ){ 
          is_tog = true;
          $("#login_button").addClass("selected");
        }
        else {
          is_tog = false;
          $("#login_button").removeClass("selected");
        }
      });
    });
  });
       

  // Finds which is used, then highlights it  
  $(document).ready(function(){            

   var file, n;

   file = window.location.pathname;
   n = file.lastIndexOf('/');
   if (n >= 0) {
      file = file.substring(n + 1);
   }
   

   if(file === "articles"){
        $('#articles').addClass('selected');
   } else if(file === "logout"){
        $('#logout').addClass('selected');
   } else if(file === "log"){
        $('#log').addClass('selected');
   } else if((file === "input") || (file === "values_input_form") ){
        $('#input').addClass('selected');
   } else if(file === "register"){
        $('#register').addClass('selected');
   } else if(file === "profile") {
        $('#user').addClass('selected');
   } else if(file === "learn"){ 
        $('#learn').addClass('selected');
   } else if(file === "database"){
        $('#database').addClass('selected');
   }else {
        $('#articles').addClass('selected');
   }

   $('#go').addClass('selected');
  });