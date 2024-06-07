<?php 
    // Start the session
    session_start();
    // Check if user is logged in
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit();
    }
    // Access the username from the session
    $username = $_SESSION['username'];
    require_once("logic/db.php");
    // Get user info
    $stmt = $conn->prepare("SELECT user_id,username, name, profileImage FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $info = $result->fetch_assoc();
    //Php for getting self posts
    $user_id = $info["user_id"];
    $info2 = [];
    $query2 = $conn->prepare(
        "SELECT users.username, users.name, users.profileImage, posts.*,
        savedreviews.userId AS saved_user_id, 
        likedreviews.userId AS liked_user_id, 
        sharedreviews.userId AS shared_user_id
        FROM posts 
        LEFT JOIN users ON posts.postUserId = users.user_id
        LEFT JOIN savedreviews ON posts.postId = savedreviews.postId AND savedreviews.userId = ?
        LEFT JOIN likedreviews ON posts.postId = likedreviews.postId AND likedreviews.userId = ?
        LEFT JOIN sharedreviews ON posts.postId = sharedreviews.postId AND sharedreviews.userId = ?
        where users.user_id = ? ORDER BY posts.postId DESC");
    if($query2) {
        $query2->bind_param("ssss", $user_id,$user_id,$user_id,$user_id);
        $query2 -> execute();
        $result2 = $query2 ->get_result();

        if (!$result2) {
            die("Error fetching random posts.");
        }
        while ($row = $result2->fetch_assoc()) {
            // Determine if the post is liked by the current user
            $row['is_liked'] = ($row['liked_user_id'] == $info["user_id"]) ? true : false;
            $row['is_saved'] = ($row['saved_user_id'] == $info["user_id"]) ? true : false;
            $row['is_shared'] = ($row['shared_user_id'] == $info["user_id"]) ? true : false;
            $info2[] = $row;
        }
    }
    //Php for getting shared posts
    $info3 = [];
    $query3 = $conn->prepare("SELECT users.username, users.name, users.profileImage, posts.*, sharedreviews.*, 
                            savedreviews.userId AS saved_user_id, 
                            likedreviews.userId AS liked_user_id, 
                            sharedreviews.userId AS shared_user_id
                            FROM sharedreviews 
                            LEFT JOIN posts ON sharedreviews.postId = posts.postId 
                            LEFT JOIN users ON posts.postUserId = users.user_id 
                            LEFT JOIN savedreviews ON posts.postId = savedreviews.postId AND savedreviews.userId = ?
                            LEFT JOIN likedreviews ON posts.postId = likedreviews.postId AND likedreviews.userId = ?
                            WHERE sharedreviews.userId = ?");
    if($query3) {
        $query3->bind_param("sss", $user_id,$user_id,$user_id);
        $query3 -> execute();
        $result3 = $query3 ->get_result();

        if (!$result3) {
            die("Error fetching random posts.");
        }
        while ($row = $result3->fetch_assoc()) {
            // Determine if the post is liked by the current user
            $row['is_liked'] = ($row['liked_user_id'] == $info["user_id"]) ? true : false;
            $row['is_saved'] = ($row['saved_user_id'] == $info["user_id"]) ? true : false;
            $row['is_shared'] = ($row['shared_user_id'] == $info["user_id"]) ? true : false;
            $info3[] = $row;
        }
    }

    $info4 = [];
    $iQuery = $conn ->prepare("Select imageName from profileImages");
    if($iQuery) {
        $iQuery -> execute();
        $result4 = $iQuery ->get_result();
        while ($row = $result4->fetch_assoc()) {
            $info4[] = $row;
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="favicon.ico" type="image/ico">
</head>
<body>
    <section id="main">
            <section id="side-bar">
                <div id="side-bar-content">
                    <div id="profile-preview">
                        <div id="profile-image-container">
                            <img src="Images/Profile Picture/<?=$info["profileImage"]?>" alt="profile-image" id="profile-image" ">
                            <div id = "edit-button-container">
                                <img src="Images/Icons/i-pencil.png" alt="" id = "edit-button">
                            </div>
                        </div>
                        <div id="profile-text-container">
                            <p id="name"><?=$info["name"]?></p>
                            <p id="username">@<?=$info["username"]?></p>
                        </div>
                    </div>
                    <div id="list-items">
                        <ol id="sidebar-items">
                        <a href="home.php" style = "text-decoration:none;"><li id="home-page" class="nav-item"><img src="Images/Icons/i-home.svg" alt="" id="home-icon"><p id="home-item" class="nav-item-php">Home</p></li></a>
                        <a href="profile.php" style = "text-decoration:none;"><li id="profile" class="nav-item"><img src="Images/Icons/i-user.svg" alt="" id="profile-icon"><p id="profile-item">Profile</p></li></a>
                        <a href="saved-reviews.php" style = "text-decoration:none;"><li id="collections" class="nav-item"><img src="Images/Icons/i-bookmark.svg" alt="" id="coll-icon"><p id="coll-item">Bookmarks</p></li></a>
                        <a href="movies.php"  style = "text-decoration:none;"><li id="movies" class="nav-item"><img src="Images/Icons/i-movies.svg" alt="" id="movie-icon"><p id="movie-item">Movies</p></li></a>                                
                        <a href="logic/logout.php"  style = "text-decoration:none;"><li id="logout" class ="nav-item"><img src="Images/Icons/i-logout.svg" alt="" id = "logout-icon"><p id ="logout-item">Logout</p></li></a>
                        </ol>
                    </div>
                </div>
        </section>
        <div id="profile-image-edit-container">
            <h2 id = "header-image-form">Choose Your New Profile Picture</h2>
            <form action="" method = "post" id = "profile-image-edit-form">
                <?php 
                $i = 1;
                foreach($info4 as $image) { 
                    $picture = $image["imageName"];
                    $pictureId = "profile-picture-$i";
                ?> 
                    <img value="<?=$picture?>" src="Images/Profile Picture/<?=$picture?>" alt="" class="image-option" pic-name="<?=$picture?>" id="<?=$pictureId?>">
                <?php 
                    $i++;
                }
                ?>
            </form>
                <div id = "button-container">
                    <button  id= "change-button" class = "image-form-button">Change</button>
                    <button id= "cancel-button" class = "image-form-button">Cancel</button>
                </div>
        </div>
        <section id="profile-timeline">
            <div id="profile-timeline-top">
                <div id="my-post-container">
                    <p id="mine-posts">My Reviews</p>
                </div>
                <div id="shared-posts-container">
                    <p id="shared-posts">Shared Reviews</p>
               </div>
            </div>
            <section id="my-post-timeline-container">
                <?php 
                    foreach($info2 as $post) {?>
                        <div class="profile-post">
                            <div id="user-info" class="post-user-info">
                                <div class="post-user-info-top">
                                    <div id="post-number-1-pp-container" class="post-profile-picture-container">
                                        <img src="Images/Profile Picture/<?=$post["profileImage"]?>" alt="" class="post-profile-picture"">
                                    </div>
                                    <div id="post-number-1-user-text" class="post-user-text">
                                        <p id="post-number-1-name" class="post-name"><?=$post["name"]?></p>
                                        <p id="post-number-1-username" class="post-username">@<?=$post["username"]?></p>
                                    </div>
                                </div>
                                <div id="post-number-1-rating" class="post-rating">
                                    <p id = "movie-name">Movie Name: <span id="name"><?=$post["movieName"]?></span></p>
                                    <p id = "movie-rating">Rating:<span id="rating"><?=$post["postRating"]?>/5</span></p>
                                </div>
                            </div>
                            <div id="post-number-1-review-container" class="post-review-container">
                                <div id="post-number-1-movie-cover-container" class="post-movie-cover-container">
                                    <img src="Images/Movie Covers/<?=$post["moviePoster"]?>" alt="" class="movie-cover" id="post-number-1-movie-cover">
                                </div>
                                <div id="post-number-1-movie-review-container" class="post-review-container-text">
                                    <p id="post-number-1-review" class="post-review"><?=$post["review"]?></p>
                                </div>
                            </div>
                            <div class="delete-sure" id = "<?=$post["postId"]?>dc">
                                <h2 id = "delete-question">Delete post ?</h2>
                                <h3 id = "delete-text">Are you sure you want to delete this review this can't be undone and this post will be removed from your profile.</h3>
                                <div id="delete-buttons">
                                    <button class = "delete-des" id = "<?=$post["postId"]?>cb">Cancel</button>
                                    <button class = "delete-des" id = "<?=$post["postId"]?>db">Delete</button>
                                </div>
                            </div>
                            <div id="misc-container" class="post-misc-container">
                                <img src="<?php echo $post['is_liked'] ? 'Images/Icons/i-heart-red.svg' : 'Images/Icons/i-heart.svg';?>" alt="" id="heart" class="post-heart" data-post-id="<?=$post["postId"]?>">
                                <p class = "misc-count-text" id="<?=$post["postId"]?>h"><?=$post["likeAmount"]?></p>
                                <img src="<?php echo $post['is_saved'] ? 'Images/Icons/i-bookmark-filled.png' : 'Images/Icons/i-bookmark.svg';?>" alt="" id="bookmark" class="post-bookmark" data-post-id="<?=$post["postId"]?>">
                                <p class = "misc-count-text" id="<?=$post["postId"]?>"><?=$post["saveAmount"]?></p>
                                <img src="<?php echo $post['is_shared'] ? 'Images/Icons/i-share-filled.png' : 'Images/Icons/i-share.svg';?>" alt="" id="share" class="post-share" data-post-id="<?=$post["postId"]?>">
                                <p class = "misc-count-text" id="<?=$post["postId"]?>s"><?=$post["shareAmount"]?></p>
                                <button id = "<?=$post["postId"]?>" class= "delete-post-button">Delete Post</button>
                            </div>
                        </div>
                    <?php }
                ?> 
            </section>
            <section id="shared-posts-timeline-container">
                <?php 
                    foreach($info3 as $post) {?>
                        <div id="post-number-1" class="profile-post">
                            <div id="user-info" class="post-user-info">
                                <div class="post-user-info-top">
                                    <div id="post-number-1-pp-container" class="post-profile-picture-container">
                                        <img src="Images/Profile Picture/<?=$post["profileImage"]?>" alt="" class="post-profile-picture"">
                                    </div>
                                    <div id="post-number-1-user-text" class="post-user-text">
                                        <p id="post-number-1-name" class="post-name"><?=$post["name"]?></p>
                                        <p id="post-number-1-username" class="post-username">@<?=$post["username"]?></p>
                                    </div>
                                </div>
                                <div id="post-number-1-rating" class="post-rating">
                                    <p id = "movie-name">Movie Name: <span id="name"><?=$post["movieName"]?></span></p>
                                    <p id = "movie-rating">Rating:<span id="rating"><?=$post["postRating"]?>/5</span></p>
                                </div>
                            </div>
                            <div id="post-number-1-review-container" class="post-review-container">
                                <div id="post-number-1-movie-cover-container" class="post-movie-cover-container">
                                    <img src="Images/Movie Covers/<?=$post["moviePoster"]?>" alt="" class="movie-cover" id="post-number-1-movie-cover">
                                </div>
                                <div id="post-number-1-movie-review-container" class="post-review-container-text">
                                    <p id="post-number-1-review" class="post-review"><?=$post["review"]?></p>
                                </div>
                            </div>
                            <div id="misc-container" class="post-misc-container">
                                <img src="<?php echo $post['is_liked'] ? 'Images/Icons/i-heart-red.svg' : 'Images/Icons/i-heart.svg';?>" alt="" id="heart" class="post-heart" data-post-id="<?=$post["postId"]?>">
                                <p class = "misc-count-text" id="<?=$post["postId"]?>h"><?=$post["likeAmount"]?></p>
                                <img src="<?php echo $post['is_saved'] ? 'Images/Icons/i-bookmark-filled.png' : 'Images/Icons/i-bookmark.svg';?>" alt="" id="bookmark" class="post-bookmark" data-post-id="<?=$post["postId"]?>">
                                <p class = "misc-count-text" id="<?=$post["postId"]?>"><?=$post["saveAmount"]?></p>
                                <img src="<?php echo $post['is_shared'] ? 'Images/Icons/i-share-filled.png' : 'Images/Icons/i-share.svg';?>" alt="" id="share" class="post-share" data-post-id="<?=$post["postId"]?>">
                                <p class = "misc-count-text" id="<?=$post["postId"]?>s"><?=$post["shareAmount"]?></p>
                            </div>
                        </div>
                    <?php }
                ?> 
            </section>
        </section>
        <section id="followed"></section>
    </section>
    <script src="scripts/profile.js"></script>
</body>
</html>