<?php 
    $logFile = "debug.log";
    function logMessage($message) {
        global $logFile;
        file_put_contents($logFile, date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL, FILE_APPEND);
    }
    session_start();
    if($_SERVER["REQUEST_METHOD"] == "GET") {
        logMessage("Search Logic:form is being proccesed");
        // Get username and password from the form
        $search = $_GET['search-value'];
        // Database connection
        $conn = mysqli_connect("localhost", "root", "", "movie-page");
        if ($conn->connect_error) { 
            die("Connection failed: " . $conn->connect_error); 
        }

        if($search !=" ") {
            $_SESSION["search"] = $search;
            header("Location: ../search.php");
            exit();
        }
        else {
            header("Location: ../movies.php");
            exit();
        } 

    }
    else {
        logMessage("form couldnt be proccesed");
        header("Location: ../movies.php");
        exit();
    }
?>