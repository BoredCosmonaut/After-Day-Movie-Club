<?php 
    //Php logic for adding reviews
    // Log file path
    $logFile = 'debug.log';
    //Start session
    session_start();
    // Function to log messages to the file
    function logMessage($message) {
        global $logFile;
        file_put_contents($logFile, date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL, FILE_APPEND);
    }
    
    //create a db connection
    require_once("db.php");
    logMessage("Inside Logic");
    //Error for failed conn
    if ($conn->connect_error) { 
        logMessage("Post Review Logic: Failed to do connection");
        exit;
    }


    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        logMessage("Form submitted");

        // Get user id with username
        $username = $_SESSION["username"];
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        //Execute query
        $stmt->execute();
        $result = $stmt->get_result();
        $info = $result->fetch_assoc();

        $review = mysqli_escape_string($conn, $_POST["review-input"]);
        $score = mysqli_escape_string($conn, $_POST["score"]);
        $movieId = $_SESSION["movie-id"];
        $userId = $info["user_id"];

        logMessage("Post review info;");
        logMessage($review);
        logMessage($score);
        logMessage($movieId);
        logMessage($userId);

        //Get the movie name and cover
        $mQuery = "SELECT movieName,movieCover FROM movies WHERE movieId = ?";
        $mStatement = $conn -> prepare($mQuery);
        $mStatement ->bind_param("s",$movieId);
        $mStatement -> execute();
        $mResult = $mStatement -> get_result();
        $mInfo = $mResult->fetch_assoc();

        //Insert the post into the database
        //Query for insertinh
        $insertQuery = "INSERT INTO posts (postUserId,postRating,movieName,review,moviePoster,likeAmount,saveAmount,shareAmount,movieId) VALUES(?,?,?,?,?,0,0,0,?)";
        //Preparing the query
        $insertStatement = $conn -> prepare($insertQuery);
        //Binding the variables to the query
        $insertStatement -> bind_param("ssssss",$userId,$score,$mInfo["movieName"],$review,$mInfo["movieCover"],$movieId);
        //Execute the query
        $insertStatement -> execute();
        //Debug message delete later
        logMessage("Post added to the database");
        //Returns back to the movies page
        header("Location: ../profile.php");
        //Exit code
        exit();
    }
?>