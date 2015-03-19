<?php
	session_start();
	$userID = $_SESSION['tableKey'];
    include_once('config.php');

    $conn = new mysqli($host, $user, $pass, $base);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }


    $result = $conn->query("SELECT publishTime, songName, artistName, 
    	albumURL FROM " . $userID . "_musicdata ORDER BY publishTime DESC");
    $lastArray = array();
    if (mysqli_num_rows($result) > 0) {
        // output data of each row
            $row = mysqli_fetch_assoc($result);
            array_push($lastArray, $row["albumURL"]);
            $publishTime = substr($row["publishTime"], 0 ,22) . 
            	':' . substr($row["publishTime"], 22);
            array_push($lastArray, $publishTime);
            array_push($lastArray, $row["songName"]);
            array_push($lastArray, $row["artistName"]);
            echo json_encode($lastArray);
    } 
    $conn->close();
?>