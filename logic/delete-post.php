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

    header( 'Content-Type: application/json');
    //create a db connection
    require_once("db.php");
    //Error for failed conn
    if ($conn->connect_error) { 
        logMessage("Post Review Logic: Failed to do connection");
        exit;
    }

    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);

    //Checks if ıd is sent
    if(!isset($data["postId"])) {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
        exit;
    }

    $postId = $data["postId"];
    //Deletes post from like shared saved tables since its a foreing key there
    $updtaeQS = $conn -> prepare("DELETE FROM savedreviews WHERE postId = ?");
    $updtaeQS-> bind_param("i", $postId);
    $updtaeQS -> execute();

    $updtaeQP = $conn -> prepare("DELETE FROM sharedreviews WHERE postId = ?");
    $updtaeQP-> bind_param("i", $postId);
    $updtaeQP -> execute();

    $updtaeQL = $conn -> prepare("DELETE FROM likedreviews WHERE postId = ?");
    $updtaeQL-> bind_param("i", $postId);
    $updtaeQL -> execute();

    //Deletes the post 
    $updateQ = $conn -> prepare("DELETE FROM posts WHERE postId = ?");
    $updateQ -> bind_param("i", $postId);

    // Return checks
    if ($updateQ->execute()) {
        echo json_encode(['success' => true, 'message' => 'Post deleted successfully']);
    } else {
        logMessage("Post Review Logic: Failed to delete post with postId: $postId");
        echo json_encode(['success' => false, 'message' => 'Failed to delete post']);
    }

    mysqli_close($conn); 
?>