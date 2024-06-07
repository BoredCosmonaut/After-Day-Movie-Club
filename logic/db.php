<?php
    //Connection to the database
    $conn = mysqli_connect("localhost","root","","movie-page");
    if ($conn->connect_error) { 
        die("Connection failed: " . $conn->connect_error); 
    } 
?>