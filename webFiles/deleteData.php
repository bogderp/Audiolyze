<?php
    session_start();
    $conn = new mysqli("localhost", "publicuser", "srQ-kdq-5Jt-Mwp", "spotigraph");
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 

    $userID = $_SESSION['tableKey'];

    $conn->query("DROP TABLE `" . $userID . "_graphdata`, `" . $userID . "_musicdata`");
    $conn->query("DELETE FROM `userData` WHERE userID=$userID");
    $conn->close();
?>