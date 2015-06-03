<?php
    session_start();
    include_once('config.php');

    $userID = $_SESSION['tableKey'];

    $conn->query("DROP TABLE `" . $userID . "_graphdata`, `" . $userID . "_musicdata`");
    $conn->query("DELETE FROM `userData` WHERE userID=$userID");
    $conn->close();
?>