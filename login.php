<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("database_connection.php");

if(isset($_COOKIE["type"]))
{
 header("location:index.php");
}

$message = '';

if(isset($_POST["login"]))
{
 if(empty($_POST["user_email"]) || empty($_POST["user_password"]))
 {
  $message = "<div class='alert alert-danger'>Both Fields are required</div>";
 }
 else
 {
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
  if($count > 0)
  {
   $result = $statement->fetchAll();
   foreach($result as $row)
   {
    if(password_verify($_POST["user_password"], $row["user_password"]))
    {
     setcookie("type", $row["user_type"], time()+3600);
     header("location:index.php");
    }
    else
    {
     $message = '<div class="alert alert-danger">Wrong Password</div>';
    }
   }
  }
  else
  {
   $message = "<div class='alert alert-danger'>Wrong Email Address</div>";
  }
 }
}


?>

<!DOCTYPE html>
<html>
 <head>
  <title>Yavuz Login</title>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
 </head>
 <body>
  <br />
  <div class="container">
   <h2 align="center">Log In to Yavuz's 347 Project</h2>
   <br />
   <div class="panel panel-default">

    <div class="panel-heading">Login</div>
    <div class="panel-body">
     <span><?php echo $message; ?></span>
     <form method="post">
      <div class="form-group">
       <label>User Email</label>
       <input type="text" name="user_email" id="user_email" class="form-control" />
      </div>
      <div class="form-group">
       <label>Password</label>
       <input type="password" name="user_password" id="user_password" class="form-control"/>
      </div>
      <a href="signup.php">Sign Up</a>
      <div class="form-group">
       <input type="submit" name="login" id="login" class="btn btn-info" value="Login" />
      </div>
     </form>
    </div>
   </div>
   <br />
   <p>Admin email - admin@email.com</p>
   <p>Admin Password - password</p>
   <p>All user password is 'password'</p>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
 </body>
</html>