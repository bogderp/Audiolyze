<?php
    session_start();
    include_once('config.php');

    $conn = new mysqli($host, $user, $pass, $base);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $userID = $_SESSION['tableKey'];

    $conn->query("DROP TABLE `" . $userID . "_graphdata`, `" . $userID . "_musicdata`");
    $conn->query("DELETE FROM `userData` WHERE userID=$userID");
    $conn->close();
?>