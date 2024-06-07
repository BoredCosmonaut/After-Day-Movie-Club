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
    $stmt = $conn->prepare("SELECT username, name, profileImage FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $info = $result->fetch_assoc();
    //Get Categories
    $stmt2 = $conn->prepare("SELECT DISTINCT genre FROM movies");
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    $info2 = $result2->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Movies</title>
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
                        <p id="username">@<?=$info['username']?></p>
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
        <section id="movies-segment">
            <div id="top-movies">
                <div id= "top-text">
                    <h1 id="movies-header">Movies</h1>
                    <p id="movies-desc">İzleyebileceğiniz farklı filmlere göz atmaya ne dersiniz.</p>
                </div>
                <div id = "top-search">
                    <form action="logic/search-logic.php" method = "get">
                        <input type="text" name = "search-value" id = "search-input" placeholder = "search">
                    </form>
                </div>
            </div>
            <ol id="categories">
                <?php
                    foreach($info2 as $category) { ?>
                        <div class="category-container">
                            <p class="category-name"><?=$category["genre"]?></p>
                                <?php
                                    $stmt3 = $conn->prepare("SELECT movieCover,movieName,movieId FROM movies where genre = ? ORDER BY movieId DESC");
                                    $stmt3->bind_param("s", $category["genre"]);
                                    $stmt3->execute();
                                    $result3 = $stmt3->get_result();
                                    $info3 = $result3->fetch_all(MYSQLI_ASSOC);
                                ?>
                            <ol id="popular" class="category">
                                <?php 
                                    foreach($info3 as $item) { ?>
                                        <li class="list-movie"><div class="list-movie-image-container"><img src="Images/Movie Covers/<?=$item["movieCover"]?>" alt="" class="list-movie-image" movie-name ="<?=$item["movieName"]?>" movie-id="<?=$item["movieId"]?>"><p class="movie-name"><?=$item["movieName"]?></p></div></li>
                                    <?php }
                                ?>
                            </ol>
                        </div>
                    <?php }
                ?>
            </ol>
        </section>
    </section>
    <script src="scripts/movies.js"></script>
</body>
</html>