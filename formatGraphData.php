<?php
	session_start();
	$userID = $_SESSION['tableKey'];
    $conn = new mysqli("localhost", "root", "21ooamlftw", "spotigraph");
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 

    $sql = "CREATE TABLE " . $userID . "_graphdata (
        artist VARCHAR(100) NULL
        )";

    $conn->query($sql);

    $result = $conn->query("SELECT publishTime, artistName FROM " 
        . $userID . "_musicdata ORDER BY publishTime DESC");

    if (mysqli_num_rows($result) > 0) {
        // output data of each row
        $dateArray = array();
        while ($row = mysqli_fetch_assoc($result)){
            $artist = mysql_real_escape_string($row["artistName"]);
            $playDate = date("d-m-y", strtotime($row["publishTime"]));
            $dateResult = $conn->query("SELECT `" . $playDate ."` FROM " 
                . $userID . "_graphdata");
            if(mysqli_num_rows($dateResult) < 1){
                $conn->query("ALTER TABLE " . $userID . "_graphdata ADD `" .
                    $playDate ."` MEDIUMINT NOT NULL DEFAULT '0'");
                array_push($dateArray, $playDate);
            }
            $artistResult = $conn->query("SELECT artist FROM " . $userID 
                . "_graphdata WHERE artist='$artist'");
            if(mysqli_num_rows($artistResult) < 1) {
                $conn->query("INSERT INTO " . $userID . "_graphdata 
                    (artist) VALUES ('$artist')");
                $conn->query("UPDATE " . $userID . "_graphdata SET 
                    `".$playDate."`=1 WHERE artist='$artist'");
            } else {
                $graphResult = $conn->query("SELECT `" . $playDate . "` FROM " 
                    . $userID . "_graphdata WHERE artist='$artist'");
                $graphRow = mysqli_fetch_assoc($graphResult);
                $count = $graphRow[$playDate];
                $count++;
                $updatePlays = "UPDATE " . $userID . "_graphdata SET `" . 
                    $playDate . "`='$count' WHERE artist='$artist'" ;
                $conn->query($updatePlays);
            }
        }
        $dateString = "";
        $dateSumString = "";
        for($i=0; $i < count($dateArray); $i++){
            if($i == count($dateArray)-1) {
                $dateString .= "`$dateArray[$i]`";
                $dateSumString .= "SUM(" . "$dateArray[$i]" . ")";
            } else {
                $dateString .= "`$dateArray[$i]`" . ", ";
                $dateSumString .= "SUM(" . "$dateArray[$i]" . "), ";
            }
        }

        $conn->query("ALTER TABLE " . $userID . "_graphdata ADD total 
            MEDIUMINT NOT NULL DEFAULT '0'");
        $totalResult = $conn->query("SELECT artist, " . $dateString . 
                " FROM " . $userID . "_graphdata");
        $total = 0;
        while($totalRow = mysqli_fetch_assoc($totalResult)){
            for($k=0; $k < count($dateArray); $k++){
                $total += $totalRow[$dateArray[$k]];
            }
            $theArtist = mysql_real_escape_string($totalRow["artist"]);
            $updateArtistTotal = "UPDATE " . $userID . "_graphdata SET total=$total 
                WHERE artist='$theArtist'";
            $conn->query($updateArtistTotal);
            $total = 0;
        }
    } 
?>