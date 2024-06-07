<?php 
    //Php logic for changing profile image 
    // Log file path
    $logFile = 'debug.log';
    //Start session
    session_start();
    // Function to log messages to the file
    function logMessage($message) {
        global $logFile;
        file_put_contents($logFile, date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL, FILE_APPEND);
    }

    header( 'Content-Type: application/json');

    //create a db connection
    $conn = mysqli_connect("localhost", "root", "", "movie-page");

    //conn check
    if ($conn->connect_error) { 
        logMessage("Saved Reviews Logic: Failed to do connection");
        exit;
    } 

    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);

    if(!isset($data["picName"])) {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
        exit;
    }

    //Gets userId
    $username = $_SESSION['username'];
    // Get user id with username
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    //Execute query
    $stmt->execute();
    $result = $stmt->get_result();
    $info = $result->fetch_assoc();
    //Get info about user and image
    $picName = $data['picName'];
    $userId = $info["user_id"];

    //Update users profile image
    $updtaeQ = $conn -> prepare("UPDATE users SET profileImage = ? where user_id = ?");
    $updtaeQ -> bind_param("si", $picName, $userId);
    $updtaeQ -> execute();

    mysqli_close($conn); 
?>