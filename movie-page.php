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
    // Get user info with username
    $stmt = $conn->prepare("SELECT user_id,username, name, profileImage FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    //Execute query
    $stmt->execute();
    $result = $stmt->get_result();
    $info = $result->fetch_assoc();
    // Get the movieId and movieName from the query string
    if (!isset($_GET['movieId']) || !isset($_GET['movieName'])) {
        header("Location: movies.php");
        exit();
    }

    //Get movie name and Id
    $movieId = $_GET['movieId'];
    $movieName = $_GET['movieName'];
    $_SESSION["movie-id"] = $movieId;
    //Write the query
    $stmt2 = $conn ->prepare("Select * From movies where movieId = ?");
    $stmt2 ->bind_param("s",$movieId);
    $stmt2 ->execute();
    $result2 = $stmt2 ->get_result();
    $info2 = $result2 ->fetch_assoc();
    if(!$info2) {
        echo("movie not found");
        exit();
    }

$stmt3 = $conn->prepare("SELECT posts.*, users.*,
                                savedreviews.userId AS saved_user_id, 
                                likedreviews.userId AS liked_user_id, 
                                sharedreviews.userId AS shared_user_id 
                                FROM movies 
                                LEFT JOIN posts ON movies.movieId = posts.movieId
                                LEFT JOIN users ON posts.postUserId = users.user_id  
                                LEFT JOIN savedreviews ON posts.postId = savedreviews.postId AND savedreviews.userId = ?
                                LEFT JOIN likedreviews ON posts.postId = likedreviews.postId AND likedreviews.userId = ?
                                LEFT JOIN sharedreviews ON posts.postId = sharedreviews.postId AND sharedreviews.userId = ?
                                WHERE movies.movieId = ?
                                ORDER BY posts.likeAmount 
                                LIMIT 2");
$stmt3->bind_param("ssss", $info["user_id"], $info["user_id"], $info["user_id"], $movieId);
$stmt3->execute();
$result3 = $stmt3->get_result();
$info3 = [];
while ($row = $result3->fetch_assoc()) {
    // Determine if the post is liked by the current user
    $row['is_liked'] = ($row['liked_user_id'] == $info["user_id"]) ? true : false;
    $row['is_saved'] = ($row['saved_user_id'] == $info["user_id"]) ? true : false;
    $row['is_shared'] = ($row['shared_user_id'] == $info["user_id"]) ? true : false;
    $info3[] = $row;
}
$stmt4 = $conn -> prepare("SELECT review,postRating FROM posts where postUserId = ? AND movieId =?");
$stmt4 ->bind_param("ss", $info["user_id"], $movieId);
$stmt4 -> execute();
$result4 = $stmt4->get_result();
$info4 = [];
while($row = $result4 ->fetch_assoc()) {
    $info4 = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title><?=$info2["movieName"]?></title>
    <link rel="icon" href="favicon.ico" type="image/ico">

</head>
<body>
    <section id="main">
        <section id="side-bar">
            <div id="side-bar-content">
                <div id="profile-preview">
                    <div id="profile-image-container">
                        <img src="Images/Profile Picture/<?=$info["profileImage"]?>" alt="profile-image" id="profile-image" ">
                    </div>
                    <div id="profile-text-container">
                        <p id="name"><?=$info["name"]?></p>
                        <p id="username"><?=$info["username"]?></p>
                    </div>
                </div>
                <div id="list-items">
                    <ol id="sidebar-items">
                        <a href="home.php" style = "text-decoration:none;"><li id="home-page" class="nav-item"><img src="Images/Icons/i-home.svg" alt="" id="home-icon"><p id="home-item" class="nav-item-php">Home</p></li></a>
                        <a href="profile.php" style = "text-decoration:none;"><li id="profile" class="nav-item"><img src="Images/Icons/i-user.svg" alt="" id="profile-icon"><p id="profile-item">Profile</p></li></a>
                        <a href="saved-reviews.php" style = "text-decoration:none;"><li id="collections" class="nav-item"><img src="Images/Icons/i-bookmark.svg" alt="" id="coll-icon"><p id="coll-item">Bookmarks</p></li></a>
                        <a href="movies.php"  style = "text-decoration:none;"><li id="movies" class="nav-item"><img src="Images/Icons/i-movies.svg" alt="" id="movie-icon"><p id="movie-item">Movies</p></li></a>                    
                        <a href="logic/logout.php" onclick= "session_destroy()" style = "text-decoration:none;"><li id="logout" class ="nav-item"><img src="Images/Icons/i-logout.svg" alt="" id = "logout-icon"><p id ="logout-item">Logout</p></li></a>
                    </ol>
                </div>
            </div>
        </section>
        <section id="movie-info-section">
            <section id="top-info">
                <div id="image-container">
                    <img src="Images/Movie Covers/<?=$info2["movieCover"]?>" alt="" id="movie-info-image">
                </div>
                <div id="movie-info-text">
                    <p id="movie-title" class="info-text">Movie Name:<span id="title-name" class="info-text-inside"><?=$info2["movieName"]?></span></p>
                    <p id="release-date" class="info-text">Release Date: <span class="info-text-inside"><?=$info2["releaseDate"]?></span></p>
                    <p id="budget" class="info-text">Budget: <span class="info-text-inside"><?=$info2["budget"]?></span></p>
                    <p id="length" class="info-text">Length: <span class="info-text-inside"><?=$info2["length"]?></span></p>
                    <p id="genre" class="info-text">Genre: <span class="info-text-inside"><?=$info2["genre"]?></span></p>
                    <p id="studio" class="info-text">Studio: <span class="info-text-inside"><?=$info2["studio"]?></span></p>
                    <?php
                        if($result4 -> num_rows == 0) { ?>
                            <div id="add-review-button-container">
                                <button id = "add-review-button">Post Review</button>
                            </div>                           
                        <?php } else { ?>
                            <div id="add-review-button-container">
                                <button id = "add-review-button">Update Review</button>
                            </div>
                        <?php }
                    ?>
                </div>
                <div id="movie-trailer-container">
                    <iframe id="movie-trailer" width="560" height="315" src="<?=$info2["trailerLink"]?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                </div>
            </section>
    <?php
        if($result4 -> num_rows == 0) { ?>
            <section id="review-form">
                <div id="exit-container">
                    <img src="Images/Icons/i-exit.svg" alt="" id = "exit-review">
                </div>
                <form action="logic/post-review-logic.php" method = "post">
                    <div id= "review-inputs">
                        <div id= "score-input">
                            <select name="score" id="score-select">
                                <option value="1">1-Bad</option>
                                <option value="2">2-Meh</option>
                                <option value="3">3-Decent</option>
                                <option value="4">4-Good</option>
                                <option value="5">5-Perfect</option>
                            </select>
                            <button id= "add-review-button-box">Add Review</button>
                        </div>
                        <textarea name="review-input" id="review-input" placeholder = "Write your review" maxlength = "300" minlength = "1"></textarea>
                    </div>
                </form>
            </section>                          
        <?php } else if ($result4 -> num_rows > 0){ ?>
            <section id="review-form">
                <div id="exit-container">
                    <img src="Images/Icons/i-exit.svg" alt="" id = "exit-review">
                </div>
                <form action="logic/update-post-logic.php" method = "post">
                    <div id= "review-inputs">
                        <div id= "score-input">
                            <select name="score-u" id="score-select">
                                <option value="1">1-Bad</option>
                                <option value="2">2-Meh</option>
                                <option value="3">3-Decent</option>
                                <option value="4">4-Good</option>
                                <option value="5">5-Perfect</option>
                            </select>
                            <button id= "add-review-button-box">Update Review</button>
                        </div>
                        <textarea name="review-input-u" id="review-input" placeholder = "Write your review" maxlength = "300" minlength = "1"><?=$info4["review"]?></textarea>
                    </div>
                </form>
            </section>
        <?php }
    ?>
            <section id="info-middle">
                <div id="synapsis-container">
                    <p id="synapsis-title">Synapsis</p>
                </div>
                <div id="synapsis-text-container">
                    <p id="synapsis-text"><?=$info2["movieDetail"]?></p>
                </div>
            </section>
            <section id="info-bottom">
                <p id="popular-reviews-title">Popular Reviews</p>
                <div id="popular-reviews-container">
                <?php 
                foreach($info3 as $post) {
                    if($post["name"] == "") { ?>
                        <p id= "no-review-text">No review yet</p>
                    <?php } else { ?>
                        <div  class="saved-profile-post">
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
                            </div>
                            <div class="post-rating">
                                <p id="movie-name">Movie Name: <span id="name"><?=$post["movieName"]?></span></p>
                                <p id="movie-rating">Rating:<span id="rating"><?=$post["postRating"]?>/5</span></p>
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
                }
                ?>
                </div>
            </section>
        </section>
    </section>
    <script src="scripts/movie-page.js"></script>
</body>
</html>