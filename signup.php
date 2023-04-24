<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
            header("Refresh: 3; url=login.php"); // Redirect the user to the login page after 3 seconds

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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
 </head>
 <body>

 <nav class="navbar bg-body-tertiary">
      <div class="container-fluid">
        <a href="index.php" class="navbar-brand">Sales Forecaster</a>
        <a href="login.php" class="d-flex">
          Log In
        </a>
      </div>
</nav>

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
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
 </body>
</html>
