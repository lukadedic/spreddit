<?php
include('./classes/DB.php');
include('./classes/Login.php');
include('./classes/Post.php');
include('./classes/comment.php');
?>
<html>
	<head>
		    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
        <link rel="stylesheet" href="style.css">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/css/bootstrap.min.css" integrity="sha384-Zug+QiDoJOrZ5t4lssLdxGhVrurbmBWopoEl+M6BdEfwnCJZtKxi1KgxUyJq13dy" crossorigin="anonymous">
    <link href="jumbotron.css" rel="stylesheet">
	</head>

	<body>
		<header>
			 <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
      <a class="navbar-brand" href="index.php">Spreddit</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarsExampleDefault">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item active">
            <a class="nav-link" href="index.php">Home <span class="sr-only">(current)</span></a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="profile.php">profile</a>
          </li>
          <li class="nav-item">
            <a class="nav-link disabled" href="#">Disabled</a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="http://example.com" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Dropdown</a>
            <div class="dropdown-menu" aria-labelledby="dropdown01">
              <a class="dropdown-item" href="#">Action</a>
              <a class="dropdown-item" href="#">Another action</a>
              <a class="dropdown-item" href="#">Something else here</a>
            </div>
          </li>
        </ul>
       
        <form class="form-inline my-2 my-lg-0" action="index.php" method="post">
          <input class="form-control mr-sm-2" type="text" name="searchbox" value="" aria-label="Search">
          <button class="btn btn-outline-success my-2 my-sm-0" type="submit" name="search" value="search">Search</button>
        </form>
      </div>
    </nav>
		</header>
		   <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
		<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/js/bootstrap.min.js" integrity="sha384-a5N7Y/aK3qNeh15eJKGWxsqtnX/wWdSZSKp+81YjTmS15nvnvxKHuzaWwXHDli+4" crossorigin="anonymous"></script>
	</body>
</html>


<?php
$showTimeline = False;
if (Login::isLoggedIn()) {
        $userid = Login::isLoggedIn();
        $showTimeline = True;
} else {
        echo 'Not logged in';
}
if (isset($_GET['postid'])) {
        Post::likePost($_GET['postid'], $userid);
}

if (isset($_POST['comment'])) {
        Comment::createComment($_POST['commentbody'], $_GET['postid'], $userid);
}

if (isset($_POST['searchbox'])) {
        $tosearch = explode(" ", $_POST['searchbox']);
        if (count($tosearch) == 1) {
                $tosearch = str_split($tosearch[0], 2);
        }
        $whereclause = "";
        $paramsarray = array(':username'=>'%'.$_POST['searchbox'].'%');
        for ($i = 0; $i < count($tosearch); $i++) {
                $whereclause .= " OR username LIKE :u$i ";
                $paramsarray[":u$i"] = $tosearch[$i];
        }
        $users = DB::query('SELECT users.username FROM users WHERE users.username LIKE :username '.$whereclause.'', $paramsarray);
        print_r($users);
        header("Location: profile.php?username=".$users[0]['username']);
        $whereclause = "";
        $paramsarray = array(':body'=>'%'.$_POST['searchbox'].'%');
        for ($i = 0; $i < count($tosearch); $i++) {
                if ($i % 2) {
                $whereclause .= " OR body LIKE :p$i ";
                $paramsarray[":p$i"] = $tosearch[$i];
                }
        }
        $posts = DB::query('SELECT posts.body FROM posts WHERE posts.body LIKE :body '.$whereclause.'', $paramsarray);
        echo '<pre>';
        print_r($posts);
        echo '</pre>';  
}
?>

<?php
$followingposts = DB::query('SELECT posts.id, posts.body, posts.likes, posts.postimg, users.`username` FROM users, posts, followers
WHERE posts.user_id = followers.user_id
AND users.id = posts.user_id
AND follower_id = :userid
ORDER BY posts.likes DESC;', array(':userid'=>$userid));
foreach($followingposts as $post) {?>
		 <html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../../../favicon.ico">

    <title>Offcanvas template for Bootstrap</title>

    <!-- Bootstrap core CSS -->
    
    <link rel = "stylesheet" type = "text/css" href = "Bootstrap/css/bootstrap.min.css">

    <!-- Custom styles for this template -->
    <link href="canvas.css" rel="stylesheet">
  </head>

  <body class="bg-light">
  <main role="main" class="container">
    <?php  echo '
      <div class="my-3 p-3 bg-white rounded box-shadow">
        <h6 class="border-bottom border-gray pb-2 mb-0"></h6>
        <div class="media text-muted pt-3">
          <img data-src="holder.js/32x32?theme=thumb&bg=007bff&fg=007bff&size=1" alt="" class="mr-2 rounded">
          <p class="media-body pb-3 mb-0 small lh-125 border-bottom border-gray">
            <strong class="d-block text-gray-dark">';?>
             
            	<?php echo "<a href='profile.php?username=".$post['username']."'> <span style='font-size:15px;'>".$post['username']."</span>  </a>";
		         ?></strong>
            <?php echo"<span style='font-size:25px;'>".$post['body'];echo "<br>";
                  if($post['postimg'] != ""){ 
                    $pic= $post['postimg'];
                    echo '<img class="w-50 p-0 " src="'.$pic.'">';
                  }
                  

	            	
             ?>
          </p>
        </div>  
        <?php  

              echo "<form action='index.php?postid=".$post['id']."' method='post'>";
              if (!DB::query('SELECT post_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$post['id'], ':userid'=>$userid))) {
              echo "<input class='btn btn-primary pull-right' type='submit' name='like' value='Like'>";
              } else {
              echo "<input class='btn btn-primary pull-right type='submit' name='unlike' value='Unlike'>";
              }
              echo "<span>".$post['likes']." likes</span>
              </form>

              
              
              
              <!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8' />
    
    <meta name='viewport' content='width=device-width, initial-scale=1.0 />

    <link rel='stylesheet' type='text/css' href='bootstrap/css/bootstrap.min.css' />
    <link rel='stylesheet' type='text/css' href='font-awesome/css/font-awesome.min.css' />

    <script type='text/javascript' src='js/jquery-1.10.2.min.js'></script>
    <script type='text/javascript' src='bootstrap/js/bootstrap.min.js'></script>
</head>
<body>

<div class=' container'>



<!-- Comment Box - START -->
<div class='container pb-cmnt-container'>
    <div class=' row'>
        <div class='col-md-6 col-md-offset-3'>
            <div class='panel panel-info'>
                <div class='panel-body'>
                <form action='index.php?postid=".$post['id']."' method='post'>

                   
                        <input class='btn btn-primary pull-right' type='submit' name='comment' value='Comment' >
                      
                                     <textarea  class='pb-cmnt-textarea' name='commentbody'></textarea> 
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<style>
    .pb-cmnt-container {
        font-family: Lato;
        margin-left: -30px;
        margin-top: 10px;
    }

    .pb-cmnt-textarea {
        resize: none;
        padding: 10px;
        height: 40px;
        width: 50%;
        border: 1px solid #F2F2F2;
    }
    .form-inline
    {
        
        padding-top: 5px;
    }
		.btn{
			background-color:lightblue;
		}
		.pb-cmnt-textarea{
			float:right;
			border: 1px solid #007bff;
		
				
				}

			.box-shadow
			{
				box-shadow:0 0.30rem 0.75rem rgb(0, 0, 0);
			}
</style>

<!-- Comment Box - END -->

</div>

</body>
</html>
				
				

				
              ";
              Comment::displayComments($post['id']);
                ?>

      </div>
    </main>
      
   
   <script src = "./Jquery/jquery.min.js"></script>
<script src="./Bootstrap/js/bootstrap.min.js"></script>
    <script src="offcanvas.js"></script>
  </body>
</html> <?php 
        
}
?>
