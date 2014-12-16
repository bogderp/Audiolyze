<?php
	session_start();
    $conn = new mysqli("localhost", "publicuser", "srQ-kdq-5Jt-Mwp", "spotigraph");
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 

    $userID = $_SESSION['tableKey'];

    $playsResult = $conn->query("SELECT publishTime FROM " . $userID . 
        "_musicdata ORDER BY publishTime DESC");

    $totalPlays = mysqli_num_rows($playsResult);
    $dateArray= array();
    $dateRow = mysqli_fetch_assoc($playsResult);
    $time = substr($dateRow["publishTime"], 0 ,22) . 
        ':' . substr($dateRow["publishTime"], 22);
    array_push($dateArray, $time);

    for($k=1; $k<$totalPlays-1;$k++) {
        $dateRow = mysqli_fetch_assoc($playsResult);
    }

    $dateRow = mysqli_fetch_assoc($playsResult);
    $time = substr($dateRow["publishTime"], 0 ,22) . 
        ':' . substr($dateRow["publishTime"], 22);
    array_push($dateArray, $time);

    $avgResult = $conn->query("SELECT avgTimeBtwnPlays FROM userData 
    WHERE userID='$userID'");
    $avgRow = mysqli_fetch_assoc($avgResult);
    $avgTimeBtwnPlays = $avgRow['avgTimeBtwnPlays'];

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

    $otherTotal = 0;
    if (mysqli_num_rows($result) > 0) {
        for ($i=0; $i < mysqli_num_rows($result); $i++) {
            $artistPlayArray = array();
            if ($i < $totalTopArtists) {
                $row = mysqli_fetch_assoc($result);
                array_push($artistPlayArray, $row["artist"]);
                array_push($artistPlayArray, $row["total"]);
                array_push($topArray, $artistPlayArray);
            } else {
                $row = mysqli_fetch_assoc($result);
                $otherTotal += $row["total"];
            }
        }
        array_push($artistPlayArray, "Other Artists");
        array_push($artistPlayArray, $otherTotal);
        array_push($topArray, $artistPlayArray);
        array_push($topArray, $totalPlays);
        array_push($topArray, $dateArray);
        array_push($topArray, $avgTimeBtwnPlays);
        $_SESSION['topArtists'] = $topArray;
        echo json_encode($topArray);
    } 
    $conn->close();
?>