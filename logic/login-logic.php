<?php
    // Log file path
    $logFile = 'debug.log';

    // Function to log messages to the file
    function logMessage($message) {
        global $logFile;
        file_put_contents($logFile, date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL, FILE_APPEND);
    }
    // Start a session
    session_start();
    logMessage("Login Logic submit has accepted");
    // Check if form is submitted
    if($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get username and password from the form
        $username = $_POST['username'];
        $password = $_POST['password'];
        logMessage("Login Logic: form is being proccesed");
        // Database connection
        $conn = mysqli_connect("localhost", "root", "", "movie-page");
        if ($conn->connect_error) { 
            die("Connection failed: " . $conn->connect_error); 
        } 

        // Prepare SQL statement to retrieve user
        $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
        $stmt->bind_param("s", $username);

        // Execute the statement
        $stmt->execute();

        // Get the result
        $result = $stmt->get_result();
            
        // Check if the user exists
        if ($result->num_rows == 1) {
            // Fetch user data
            $row = $result->fetch_assoc();
            if(password_verify($password,$row['password'])) {
                // User exists, set session variables
                $_SESSION['username'] = $username;
                // Close the statement and connection
                $stmt->close();
                $conn->close();
                logMessage('Login Logic: User logged in successfully');
                // Redirect to home page or dashboard
                header("Location: ../home.php");
                exit();
            }
        } else {
            // Invalid password, redirect back to login page with error message
            header("Location: ../login.php");
            exit();
        }
    } else {
        // Redirect to login page if login form is not submitted
        header("Location: ../login.php");
        exit();
    }
?>
