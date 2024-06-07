<?php 
    //Php logic for saved posts
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

    if(!isset($data["action"]) || !isset($data["postId"])) {
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
    //Get info about user,post
    $action = $data['action'];
    $postId = $data['postId'];
    $userId = $info["user_id"];
    //Get total bookmark amount
    $stmt2 = $conn ->prepare("Select saveAmount FROM posts WHERE postId = ?");
    $stmt2->bind_param("s", $postId);
    //Execute query
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    $info2 = $result2->fetch_assoc();
    if($action == "add") {
        $saveAmount = $info2["saveAmount"] + 1;
        $stmt2 = $conn ->prepare("UPDATE posts SET saveAmount = ? WHERE postId = ?");
        $stmt2->bind_param("ss",$saveAmount, $postId);
        //Execute query
        $stmt2->execute();
        //Add bookmark
        $stmt = $conn -> prepare('INSERT INTO savedReviews (userId, postId) values(?,?)');
        $stmt->bind_param('ss', $userId, $postId);
        $result = $stmt->execute();
        $stmt->close();
    } elseif($action == "remove") {
        $saveAmount = $info2["saveAmount"] - 1;
        $stmt2 = $conn ->prepare("UPDATE posts SET saveAmount = ? WHERE postId = ?");
        $stmt2->bind_param("ss",$saveAmount, $postId);
        //Execute query
        $stmt2->execute();
        //Remove bookmark
        $stmt = $conn -> prepare("Delete From savedReviews where userId = ? and postId = ?");
        $stmt -> bind_param('ss',$userId,$postId);
        $result = $stmt->execute();
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        exit;
    }

    if ($result) {
    echo json_encode(['success' => true]);
    } else {
    echo json_encode(['success' => false, 'message' => 'Database operation failed']);
    }

    mysqli_close($conn);   
?>