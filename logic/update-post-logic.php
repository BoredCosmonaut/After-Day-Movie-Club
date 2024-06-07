<?php 
    //Start the session
    session_start();
    $logFile = "debug.log";
    function logMessage($message) {
        global $logFile;
        file_put_contents($logFile, date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL, FILE_APPEND);
    }
    
    require_once("db.php");
    logMessage("Inside update post logic");

    //if db connection fails
    if($conn -> connect_error) {
        logMessage("Update Post Logic: Failed connections");
        exit();
    }


    if($_SERVER["REQUEST_METHOD"] == "POST") {
        logMessage("Update-Post-Logic:Form submitted");
        //Get user id with the username
        $username = $_SESSION["username"];
        logMessage("Username from session:" . $username);
        $stmt = $conn ->prepare("SELECT user_id FROM users WHERE username = ?");
        $stmt -> bind_param("s", $username);
        //Execute query
        $stmt -> execute();
        $result = $stmt -> get_result();
        $info = $result ->fetch_assoc();

        //Get the required info
        $review = mysqli_real_escape_string($conn, $_POST["review-input-u"]);
        $score = mysqli_real_escape_string($conn, $_POST["score-u"]);
        $movieId = $_SESSION["movie-id"];
        $userId = $info["user_id"];
        logMessage("User ID:" .$userId);

        //Get the id of the edited post
        $pStatement = $conn -> prepare("Select postId FROM posts Where postUserId = ? and movieId = ?");
        $pStatement -> bind_param("ii",$userId,$movieId);
        $pStatement -> execute();
        $pResult = $pStatement -> get_result();
        $pInfo = $pResult -> fetch_assoc();
        $postId = $pInfo["postId"];
        logMessage("".$movieId);
        logMessage("".$userId);
        logMessage("".$postId);

        //Update the post
        $updateStatement =$conn -> prepare("Update posts SET review = ?, postRating = ? WHERE postId = ?");
        $updateStatement ->bind_param("sss", $review,$score,$postId);
        $updateStatement -> execute();
        logMessage("Post with the $postId has been updated");
  
        header("Location: ../movie-page.php");
        exit();
    }
?>