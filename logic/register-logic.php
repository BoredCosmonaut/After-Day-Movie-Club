<?php
// Include database connection
require_once("db.php");

session_start();
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input data
    $username = mysqli_real_escape_string($conn, $_POST['reg-username']);
    $password = mysqli_real_escape_string($conn, $_POST['reg-password']);
    $name = mysqli_real_escape_string($conn, $_POST['reg-name']);
    $profileImage = "profile-picture-5.jpg";

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Check if the username already exists
    $checkQuery = "SELECT * FROM users WHERE username = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("s", $username);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    if ($result->num_rows > 0) {
        // Username already exists
        echo "Username already exists. Please choose a different username.";
    } else {
        // Username is unique, proceed with registration
        $sql = "INSERT INTO users (username, name,password, profileImage) VALUES (?, ?, ?,?)";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            // Bind parameters and execute the statement
            $stmt->bind_param("ssss", $username,$name,$hashedPassword,$profileImage);
            if ($stmt->execute()) {
                $_SESSION['username'] = $username;
                // Registration successful
                header("Location: ../home.php"); // Redirect to login page
                exit();
            } else {
                // Registration failed
                echo "Registration failed. Please try again.";
            }
            $stmt->close(); // Close the statement
        } else {
            // Error in preparing the statement
            echo "Error: " . $conn->error;
        }
    }
    // Close the check statement
    $checkStmt->close();

    // Close the connection
    $conn->close();
} else {
    // Redirect to registration page if accessed directly
    header("Location: ../login.php");
    exit();
}
?>
