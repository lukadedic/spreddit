<?php
include('./classes/DB.php');
include('./classes/Login.php');
include('./classes/Post.php');
include('./classes/Image.php');
$username = "";
?>
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
<?php  
$isFollowing = False;
if (isset($_GET['username'])) {
        if (DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$_GET['username']))) {
                $username = DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['username'];
                $userid = DB::query('SELECT id FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['id'];

                //echo "Username: $username ; UserID: $userid";
               
                $followerid = Login::isLoggedIn();
                if (isset($_POST['follow'])) {
                        if ($userid != $followerid) {
                                if (!DB::query('SELECT follower_id FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$userid, ':followerid'=>$followerid))) {
                                        
                                        DB::query('INSERT INTO followers VALUES (\'\', :userid, :followerid)', array(':userid'=>$userid, ':followerid'=>$followerid));
                                } else {
                                        echo 'Already following!';
                                }
                                $isFollowing = True;
                        }
                }
                if (isset($_POST['unfollow'])) {
                        if ($userid != $followerid) {
                                if (DB::query('SELECT follower_id FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$userid, ':followerid'=>$followerid))) {
                                        
                                        DB::query('DELETE FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$userid, ':followerid'=>$followerid));
                                }
                                $isFollowing = False;
                        }
                }
                if (DB::query('SELECT follower_id FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$userid, ':followerid'=>$followerid))) {
                        //echo 'Already following!';
                        $isFollowing = True;
                }


                if (isset($_POST['deletepost'])) {
                        if (DB::query('SELECT id FROM posts WHERE id=:postid AND user_id=:userid', array(':postid'=>$_GET['postid'], ':userid'=>$followerid))) {
                                DB::query('DELETE FROM posts WHERE id=:postid and user_id=:userid', array(':postid'=>$_GET['postid'], ':userid'=>$followerid));
                                DB::query('DELETE FROM post_likes WHERE post_id=:postid', array(':postid'=>$_GET['postid']));
                                echo 'Post deleted!';
                        }
                }

                if (isset($_POST['post'])) {
                        if ($_FILES['postimg']['size'] == 0) {
                                Post::createPost($_POST['postbody'], Login::isLoggedIn(), $userid);
                        } else {
                                $postid = Post::createImgPost($_POST['postbody'], Login::isLoggedIn(), $userid);
                                Image::uploadImage('postimg', "UPDATE posts SET postimg=:postimg WHERE id=:postid", array(':postid'=>$postid));
                        }
                }
                if (isset($_GET['postid'])&&!isset($_POST['deletepost'])) {
                        Post::likePost($_GET['postid'], $followerid);
                }
                $posts = Post::displayPosts($userid, $username, $followerid);
        } else {
                die('User not found!');
        		//$username = 'avsec';
        }
}else{
	$username = DB::query('SELECT username FROM users WHERE id=:userid', array(':userid'=>Login::isLoggedIn()))[0]['username'];
    $userid = DB::query('SELECT id FROM users WHERE id=:userid', array(':userid'=>Login::isLoggedIn()))[0]['id'];	
    $followerid = Login::isLoggedIn();    
    $posts = Post::displayPosts($userid, $username, $followerid);
	
}
?>
<h1 style="margin-left: 30px;"><?php echo $username; ?>'s Profile</h1>
<form action="profile.php?username=<?php echo $username; ?>" method="post">
        <?php
        if ($userid != $followerid) {
                if ($isFollowing) {
                        echo '<input type="submit" name="unfollow" value="Unfollow">';
                } else {
                        echo '<input type="submit" name="follow" value="Follow">';
                }
        }
        ?>
</form>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Comment Box Template | PrepBootstrap</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="font-awesome/css/font-awesome.min.css" />

    <script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container">



<!-- Comment Box - START -->
<div class="container pb-cmnt-container">
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="panel panel-info">
                <div class="panel-body">
                    
<form action="profile.php?username=<?php echo $username; ?>" method="post" enctype="multipart/form-data">
        <textarea placeholder="write your post here..." name="postbody" rows="12" cols="80"class="pb-cmnt-textarea"></textarea>
        <form class="form-inline">
                
        <br />Upload an image:
        <input type="file" name="postimg">
        <input class="btn btn-primary pull-right" type="submit" name="post" value="Post">
</form>

                        
                        

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .pb-cmnt-container {
        font-family: Lato;
        margin-top: 100px;
    }

    .pb-cmnt-textarea {
        resize: none;
        padding: 20px;
        height: 130px;
        width: 100%;
        border: 1px solid #FFF;
        border-radius: 30px;
        margin-bottom: 5px;
    }

    .panel{
        border:1px solid #007bff;
        border-radius: 30px;
        padding: 10px;
        margin-left: -110px;
        margin-bottom: 30px;
    }

    
</style>

<!-- Comment Box - END -->

</div>

</body>
</html>


<div class="posts" style="margin-left: 40px;">
        <?php echo $posts; ?>
</div>
