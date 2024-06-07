<?php 
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
    //Acces the search value from the session
    $search = $_SESSION['search'];
    $search2 = "$search%";
    $stmt2 = $conn->prepare("SELECT * from movies WHERE movieName LIKE ?");
    $stmt2->bind_param("s", $search2);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    $info2 = $result2->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="favicon.ico" type="image/ico">
</head>
<body>
    <section id="main">
        <section id="search-side-bar">
            <div id="side-bar-content">
                <div id="profile-preview">
                    <div id="profile-image-container">
                        <img src="Images/Profile Picture/<?=$info["profileImage"]?>" alt="profile-image" id="profile-image" ">
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
        <section id="search-result">
            <div id = "result-search-top">
                <p id = "result-search-name">Search result for: <?=$search?></p>
            </div>
            <?php 
                foreach($info2 as $movie) { ?> 
                    <div id= "search-result-movies">
                        <div id= "movie-container">
                            <div id="search-image-container">
                                <img src="Images/Movie Covers/<?=$movie["movieCover"]?>" alt="" id="search-movie-image" movie-name ="<?=$movie["movieName"]?>" movie-id="<?=$movie["movieId"]?>">
                            </div>
                            <p id = "search-movie-name"><?=$movie["movieName"]?></p>
                        </div>
                    </div>                
                <?php }
            ?>

        </section>
    </section>
    <script src="scripts/search.js"></script>
</body>
</html>