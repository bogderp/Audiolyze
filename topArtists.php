<?php
	session_start();
	$userID = $_SESSION['tableKey'];
    $conn = new mysqli("www.db4free.net", "spotiuser", "se4rft6yh", "spotigraph", 3306);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 


    $result = $conn->query("SELECT artist, total 
        FROM " . $userID . "_graphdata ORDER BY total DESC");

    $topArray = array();
    $rowCount = mysqli_num_rows($result);
    if ($rowCount <= 10){
        $totalTopArtists = $rowCount; 
    } else if ($rowCount > 10 && $rowCount <= 15){
        $totalTopArtists = 10;
    } else if ($rowCount > 15 && $rowCount <= 25){
        $totalTopArtists = 15;
    } else if ($rowCount > 25 && $rowCount <= 35){
        $totalTopArtists = 20;
    } else if ($rowCount > 35 && $rowCount <= 40){
        $totalTopArtists = 30;
    } else{
        $totalTopArtists = 40;
    };

    if (mysqli_num_rows($result) > 0) {
        for($i=0; $i < $totalTopArtists; $i++) {
            $artistPlayArray = array();
            $row = mysqli_fetch_assoc($result);
            array_push($artistPlayArray, $row["artist"]);
            array_push($artistPlayArray, $row["total"]);
            array_push($topArray, $artistPlayArray);
        }
        echo json_encode($topArray);
    } 
    $conn->close();
?>