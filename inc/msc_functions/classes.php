<?php
  
  class Logging{
    var $file_name;
    var $append;

    function Logging($file, $append = false){
      $this->file_name = $file;
      $this->append = $append;
    }

    function file_write($text){
      file_put_contents($this->file_name, $text . '\n', FILE_APPEND | LOCK_EX);
    }
  }



  /*
      : Exercise edit controller
  */


  class ControllerExerciseEdit{
    //Valid on constructor
    var $user_id;           
    var $conn;
    var $input_rows_per_page;   
    var $total_input_rows;      
    var $total_pages_required;  
    var $active_page;           

    function ControllerExerciseEdit($conn, $user_id, $page, $input_rows_per_page){
      $this->conn = $conn;
      $this->user_id = $user_id;

      $this->total_input_rows = count(return_exercises_rows($this->conn, $this->user_id, 100000));

      $this->total_pages_required = ceil($this->total_input_rows/$input_rows_per_page);

      if($page < 1) $page = 1;
      if($page >= $this->total_pages_required) $page = $this->total_pages_required;
      $this->active_page = $page;

      $this->input_rows_per_page = $input_rows_per_page;
    }



  //Returns an array, message: status of what happened, completed:if any commits to the database
    function update_values_for_deletion(){
      $rows_on_page = $this->input_rows_per_page;
      //The row entry needs to be 0 for page 1 as that's where it points to on all the rows
      //BUGS TOO DRUNK
      if((($rows_on_page * $this->active_page) - $rows_on_page) >= 0){
        $row_entry = ($rows_on_page * $this->active_page) - $rows_on_page; 
      } else {
        $row_entry = 0;
      }

      return check_deleted_exercise_values($this->conn, $this->user_id, $this->input_rows_per_page, $row_entry);
    }

      //Returns an array, message: status of what happened, completed:if any commits to the database
    function update_values_for_recovery(){
      $rows_on_page = $this->input_rows_per_page;
      //BUGS TOO DRUNK
      if((($rows_on_page * $this->active_page) - $rows_on_page) >= 0){
        $row_entry = ($rows_on_page * $this->active_page) - $rows_on_page; 
      } else {
        $row_entry = 0;
      }

      return check_recovered_exercise_values($this->conn, $this->user_id, $this->input_rows_per_page, $row_entry);
    }

    // getter
    function return_total_of_input_rows(){
      return $this->total_input_rows;
    }

    // getter
    function return_total_pages_required(){
      return $this->total_pages_required;
    }

    // getter
    function return_page_num(){
      return $this->active_page;
    }

    function return_page_rows(){
      $rows_on_page = $this->input_rows_per_page;
      //The row entry needs to be 0 for page 1 as that's where it points to on all the rows
      if((($rows_on_page * $this->active_page) - $rows_on_page) >= 0){
        $row_entry = ($rows_on_page * $this->active_page) - $rows_on_page; 
      } else {
        $row_entry = 0;
      }
      return return_exercises_rows($this->conn, $this->user_id, $rows_on_page, $row_entry);
    }

    function return_input_rows_per_page(){
      return $this->input_rows_per_page;
    }
  }

  
  //Input view controller, deals with the pages
  class ControllerInputView{
    //Valid on constructor
    var $user_id;           
    var $conn;
    var $input_rows_per_page;   
    var $total_input_rows;      
    var $total_pages_required;  
    var $active_page;           

    function ControllerInputView($conn, $user_id, $page, $input_rows_per_page){
      $this->conn = $conn;
      $this->user_id = $user_id;

      $this->total_input_rows = count(return_active_exercise_inputs($this->conn, $this->user_id, 100000));


      $this->total_pages_required = ceil($this->total_input_rows/$input_rows_per_page);

      if($page < 1) $page = 1;
      if($page > $this->total_pages_required) $page = $this->total_pages_required;
      $this->active_page = $page;

      $this->input_rows_per_page = $input_rows_per_page;
    }


    // getter
    function return_total_of_input_rows(){
      return $this->total_input_rows;
    }

    // getter
    function return_total_pages_required(){
      return $this->total_pages_required;
    }


    // getter
    function return_page_num(){
      return $this->active_page;
    }

    function return_page_rows(){
      $rows_on_page = $this->input_rows_per_page;
      //The row entry needs to be 0 for page 1 as that's where it points to on all the rows
      // LIVE THIS COULD CAUSE BUGS, TOO DRUNK TOO KNOW WHY
      if((($rows_on_page * $this->active_page) - $rows_on_page) >= 0){
        $row_entry = ($rows_on_page * $this->active_page) - $rows_on_page; 
      } else {
        $row_entry = 0;
      }
      return return_active_exercise_inputs($this->conn, $this->user_id, $rows_on_page, $row_entry);
    }

    function return_input_rows_per_page(){
      return $this->input_rows_per_page;
    }
  }



  //Input edit controller for edit exercise, all the logic is in the functions.php file
  class ControllerInputEdit{
    //Valid on constructor
    var $user_id;           
    var $conn;
    var $input_rows_per_page;   
    var $total_input_rows;      
    var $total_pages_required;  
    var $active_page;           

    var $not_recoverable_ids = NULL; //Ids which don't have parents

    function ControllerInputEdit($conn, $user_id, $page, $input_rows_per_page){
      $this->conn = $conn;
      $this->user_id = $user_id;

      $this->total_input_rows = count(return_exercise_inputs($this->conn, $this->user_id, 100000));


      $this->total_pages_required = ceil($this->total_input_rows/$input_rows_per_page);

      if($page < 1) $page = 1;
      if($page > $this->total_pages_required) $page = $this->total_pages_required;
      $this->active_page = $page;

      $this->input_rows_per_page = $input_rows_per_page;
    }

    //Returns an array, message: status of what happened, completed:if any commits to the database
    function update_values_for_deletion(){
      $rows_on_page = $this->input_rows_per_page;
      //The row entry needs to be 0 for page 1 as that's where it points to on all the rows
      $row_entry = ($rows_on_page * $this->active_page) - $rows_on_page; 
      if($row_entry < 0){
          $row_entry = 0;
      }

      //check_for_deleted_values($conn, $user_id, $amount_to_show = 200, $location = 0)
      return check_for_deleted_values($this->conn, $this->user_id, $this->input_rows_per_page, $row_entry);
    }

    //Returns an array, message: status of what happened, completed:if any commits to the database
    function update_values_for_recovery(){
      $rows_on_page = $this->input_rows_per_page;
      //The row entry needs to be 0 for page 1 as that's where it points to on all the rows
      $row_entry = ($rows_on_page * $this->active_page) - $rows_on_page; 
      if($row_entry < 0){
          $row_entry = 0;
      }

      $result = check_for_recovered_values($this->conn, $this->user_id, $this->input_rows_per_page, $row_entry);
      $this->not_recoverable_ids = $result['deleted_ex_ids'];

      return $result;
    }

    // getter
    function return_total_of_input_rows(){
      return $this->total_input_rows;
    }

    // getter
    function return_total_pages_required(){
      return $this->total_pages_required;
    }

    function return_not_recoverable_ex_ids(){
      // The check for recovered values function must be ran first, if it hasn't been run, it will.
      if( empty($this->not_recoverable_ids) ){
        $rows_on_page = $this->input_rows_per_page;
        //The row entry needs to be 0 for page 1 as that's where it points to on all the rows
        $row_entry = ($rows_on_page * $this->active_page) - $rows_on_page; 
        if($row_entry < 0){
          $row_entry = 0;
        }

        $this->not_recoverable_ids = check_for_recovered_values($this->conn, $this->user_id, $this->input_rows_per_page, $row_entry)['deleted_ex_ids'];

      }
      return  $this->not_recoverable_ids;
    }

    // getter
    function return_page_num(){
      return $this->active_page;
    }

    function return_page_rows(){
      $rows_on_page = $this->input_rows_per_page;
      //The row entry needs to be 0 for page 1 as that's where it points to on all the rows
      // LIVE THIS COULD CAUSE BUGS, TOO DRUNK TOO KNOW WHY
      if((($rows_on_page * $this->active_page) - $rows_on_page) >= 0){
        $row_entry = ($rows_on_page * $this->active_page) - $rows_on_page; 
      } else {
        $row_entry = 0;
      }
      return return_exercise_inputs($this->conn, $this->user_id, $rows_on_page, $row_entry);
    }

    function return_input_rows_per_page(){
      return $this->input_rows_per_page;
    }
  }

  class ControllerPosts{
    var $conn;

      function ControllerPosts($conn){
        $this->conn = $conn;
      }

      function get_article($title){
        //Check url does a preg match to make sure there is only alphanumeric and '-'s
        $article = array(
          "comments" => false,
          "post" => false
          );

        if( url_check($title) ){

          // Parses the url, changes the dashes to spaces.
          $title_string = strtolower(str_replace('-', ' ', $title));

          $is_in_db = $this->conn->prepare("SELECT COUNT(*) FROM tbl_posts WHERE title = :title_string");
          $is_in_db->bindParam(":title_string", $title_string, PDO::PARAM_STR);
          $is_in_db->execute();
          $is_in_db = $is_in_db->fetchALL(PDO::FETCH_ASSOC);

          //The post is in the database
          if($is_in_db[0]['COUNT(*)'] > 0){
            $article['post'] = $this->get_post_by_title($title_string);
            $article['comments'] = $this->get_comments_by_title($title_string);
          } 
        }
        return $article;
      }

      function input_data($title, $preview, $body, $author){
        $sql = "INSERT INTO tbl_posts(title, previewText, bodyText, author) VALUES(:title, :preview, :body, :author)";
        $stmt = $this->conn->prepare($sql);
        
        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':preview', $preview, PDO::PARAM_STR);
        $stmt->bindParam(':body', $body, PDO::PARAM_STR);
        $stmt->bindParam(':author', $author, PDO::PARAM_INT);

        if(!$stmt->execute())
            return "FAILED TO COMMIT INSERT!";
        else
            return "Completed";
      }

      function get_num_comments($id){
        $number = $this->conn->query("SELECT COUNT(*) FROM tbl_comments WHERE postIdF = $id");
        $number = $number->fetchALL(PDO::FETCH_ASSOC);
        return $number[0]['COUNT(*)'];
      }

      function is_unique_title($title){
        $title_check = $this->conn->prepare("SELECT COUNT(*) FROM tbl_posts WHERE title = :title");
        $title_check->bindParam(":title", $title, PDO::PARAM_STR);
        $title_check->execute();
        $title_check = $title_check->fetchALL(PDO::FETCH_ASSOC);
        if($title_check[0]['COUNT(*)'] > 0){
          return false;
        } else {
          return true;
        }
      }


      // GET BY
      function get_post_by_id($id){
        $posts = $this->conn->query("SELECT * FROM tbl_posts WHERE postId = $id");
        return $posts->fetchALL(PDO::FETCH_ASSOC)[0];
      }

      function get_post_by_title($title){
        $posts = $this->conn->query("SELECT * from tbl_posts WHERE title = '$title'");
        return $posts->fetchAll(PDO::FETCH_ASSOC)['0'];
      }



      function get_posts($limit = 10){
        $posts = $this->conn->query("SELECT * FROM tbl_posts ORDER BY creationDate DESC LIMIT $limit");
        return $posts->fetchALL(PDO::FETCH_ASSOC);
      }

      function remove_all_posts(){
         $this->conn->query("DELETE FROM tbl_posts");
      }

      function arr_valid_post_titles(){
        $titles = array();

        $posts = $this->conn->query("SELECT * from tbl_posts ORDER BY title DESC");
        $posts = $posts->fetchALL(PDO::FETCH_ASSOC);

        foreach($posts as $post){
          array_push($titles, $post['title']);
        }

        return $titles;
      }

      function arr_valid_post_ids(){
        $ids = array();

        $posts = $this->conn->query("SELECT * FROM tbl_posts ORDER BY postId DESC");
        $posts = $posts->fetchALL(PDO::FETCH_ASSOC);

        foreach($posts as $post){
          array_push($ids, $post['postId']);
        }
        return $ids;
      }

      function title_string_to_id($title){
        $title_to_id = $this->conn->query("SELECT postId FROM tbl_posts WHERE title = '$title'");
        $title_to_id = $title_to_id->fetchAll(PDO::FETCH_ASSOC);
        return $title_to_id[0]['postId'];
      }

      function get_comments_by_title($title){
        $title_to_id = $this->conn->query("SELECT postId FROM tbl_posts WHERE title = '$title'");
        $title_to_id = $title_to_id->fetchAll(PDO::FETCH_ASSOC);
        $id = $title_to_id[0]['postId'];

        $comment_query = $this->conn->query("SELECT * from tbl_comments WHERE postIdF = $id");
        $comment_rows = $comment_query->fetchAll(PDO::FETCH_ASSOC);

        return $comment_rows;
      }

      function get_comments($post_id){
        $comment_query = $this->conn->query("SELECT * from tbl_comments WHERE postIdF = $post_id");
        $comment_rows = $comment_query->fetchAll(PDO::FETCH_ASSOC);
        return $comment_rows;
      }

      //Checks to make sure the users owns the comment, or if the admin is logged in.
      function remove_comment($comment_id, $user_id){
        $sql = "";

        if($user_id == 1){
          $sql = "DELETE FROM tbl_comments WHERE commentId = :commentId";
        } else {
          $sql = "DELETE FROM tbl_comments WHERE commentId = :commentId AND userIdF = :user_id";
        }

        $stmt = $this->conn->prepare($sql);

        if($user_id != 1) $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':commentId', $comment_id, PDO::PARAM_INT);

        if(!$stmt->execute()){
          return false;
        } else {
          return true;
        }
      }

      function add_comment($post_id, $user_id, $title, $body){
        $sql = "INSERT INTO tbl_comments(title, bodyText, userIdF, postIdF) VALUES(:title, :body, :userId, :postId)";
        $stmt = $this->conn->prepare($sql);

        $title = strip_tags($title);
        $body = strip_tags($body);

        $body = nl2br($body);

        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':body', $body, PDO::PARAM_STR);
        $stmt->bindParam(':userId', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':postId', $post_id, PDO::PARAM_INT);
          


        if(!$stmt->execute())
            return "FAILED TO COMMIT INSERT!";
        else
            return "Completed";
      }
  }


//
// -- routine_input
//

  class class_routine{
    var $routineName;
    var $exerciseNames = array();
    var $exerciseSize = 0;
    var $iter = 0;

    function class_routine($routineName){
        $this->routineName = $routineName;
    }

    function pushExercise($exerciseName){
        array_push($this->exerciseNames, $exerciseName);
        $this->exerciseSize++;
    }

    function popExercise(){
         if($this->exerciseSize > 0){
            $this->exerciseSize--;
            return $this->exerciseNames[$this->iter++];
         } else {
            return "QUE EMPTY";
         }
    }

    function return_exercise_size(){
        return $this->exerciseSize;
    }

    function return_routine_name(){
        return $this->routineName;
    }
  } 


//
// -- values_input_form
//

  //Core functionality
  class ex_id_name{
        var $ex_id;
        var $ex_name;
        var $ex_sets;
        var $ex_reps;
        var $ex_description;
        
        function ex_id_name($name, $id, $sets, $reps, $description){
            $this->ex_id = $id;
            $this->ex_name = $name;
            $this->ex_sets = $sets;
            $this->ex_reps = $reps;
            $this->ex_description = $description;
        }
        
        function return_ex_id(){
            return $this->ex_id;
        }
        
        function return_ex_name(){
            return $this->ex_name;
        }
        
        function return_ex_sets(){
            return $this->ex_sets;
        }
        
        function return_ex_reps(){
            return $this->ex_reps;
        }
        
        function return_ex_description(){
            return $this->ex_descripton;
        }
        
  }; 

?>