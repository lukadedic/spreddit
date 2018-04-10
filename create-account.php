<?php
include('classes/db.php');
if (isset($_POST['createaccount'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $email = $_POST['email'];
        if (!DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$username))) {
                if (strlen($username) >= 3 && strlen($username) <= 32) {
                        if (preg_match('/[a-zA-Z0-9_]+/', $username)) {
                                if (strlen($password) >= 6 && strlen($password) <= 60) {
                                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                if (!DB::query('SELECT email FROM users WHERE email=:email', array(':email'=>$email))) {
                                        DB::query('INSERT INTO users VALUES (\'\', :username, :password, :email,\'\')', array(':username'=>$username, ':password'=>password_hash($password, PASSWORD_BCRYPT), ':email'=>$email));
                                        echo "Success!";
                                } else {
                                        echo 'Email in use!';
                                }
                        } else {
                                        echo 'Invalid email!';
                                }
                        } else {
                                echo 'Invalid password!';
                        }
                        } else {
                                echo 'Invalid username';
                        }
                } else {
                        echo 'Invalid username';
                }
        } else {
                echo 'User already exists!';
        }
}
?>



<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
  
  
    <title>login </title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

    <!-- Custom styles for this template -->
    <link href="signin.css" rel="stylesheet">
    <link rel="stylesheet" href="login-style.css">
  </head>

  <body class="text-center">

    <form class="form-signin" action="create-account.php" method="post">
      <img class="mb-4" src="logo.png" alt="" width="300" height="150">
      <h1 class="h3 mb-3 font-weight-normal">Register</h1>
      <label for="inputEmail" class="sr-only">username</label>
      <input type="text" name="username" value="" class="form-control" placeholder="username..." required autofocus>
      <label for="inputPassword" class="sr-only">Password</label>
      <input type="password" name="password" value="" class="form-control" placeholder="Password" required>
        <input type="email" name="email" value="" class="form-control" placeholder="email" required>
     
      <button class="btn btn-lg btn-primary btn-block" type="submit" name="createaccount" value="Create Account">Submit</button>
     
    </form>
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  </body>
</html>
