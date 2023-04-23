<?php
require_once("database_connection.php");

$message = '';

if (isset($_POST["signup"])) {
    if (empty($_POST["user_email"]) || empty($_POST["user_password"])) {
        $message = "<div class='alert alert-danger'>Both Fields are required</div>";
    } else {
        $query = "
        SELECT * FROM user_details 
        WHERE user_email = :user_email
        ";
        $statement = $connect->prepare($query);
        $statement->execute(
            array(
                'user_email' => $_POST["user_email"]
            )
        );
        $count = $statement->rowCount();
        if ($count == 0) {
            $hashed_password = password_hash($_POST["user_password"], PASSWORD_DEFAULT);
            $query = "
            INSERT INTO user_details (user_email, user_password, user_type) 
            VALUES (:user_email, :user_password, :user_type)
            ";
            $statement = $connect->prepare($query);
            $statement->execute(
                array(
                    'user_email' => $_POST["user_email"],
                    'user_password' => $hashed_password,
                    'user_type' => 'user'
                )
            );
            $message = "<div class='alert alert-success'>Registration Completed</div>";
        } else {
            $message = "<div class='alert alert-danger'>Email Address already exists</div>";
        }
    }
}

?>

<!DOCTYPE html>
<html>
 <head>
  <title>Yavuz Sign Up</title>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
 </head>
 <body>
  <br />
  <div class="container">
   <h2 align="center">Sign Up for Yavuz's 347 Project</h2>
   <br />
   <div class="panel panel-default">
    <div class="panel-heading">Sign Up</div>
    <div class="panel-body">
     <span><?php echo $message; ?></span>
     <form method="post">
      <div class="form-group">
       <label>User Email</label>
       <input type="text" name="user_email" id="user_email" class="form-control" />
      </div>
      <div class="form-group">
       <label>Password</label>
       <input type="password" name="user_password" id="user_password" class="form-control" />
      </div>
      <div class="form-group">
       <input type="submit" name="signup" id="signup" class="btn btn-info" value="Sign Up" />
      </div>
     </form>
    </div>
   </div>
   <br />
  </div>
 </body>
</html>
