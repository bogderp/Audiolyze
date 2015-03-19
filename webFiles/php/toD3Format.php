<?php
	session_start();
	$userID = $_SESSION['tableKey'];
    include_once('config.php');

    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];

    $conn = new mysqli($host, $user, $pass, $base);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

	$sql = "SELECT `COLUMN_NAME` 
		FROM `INFORMATION_SCHEMA`.`COLUMNS` 
		WHERE `TABLE_SCHEMA`='audiolyze' 
		AND `TABLE_NAME`='" . $userID . "_graphdata'";

	$topArtists = $_SESSION['topArtists'];
	$topArtistsNamesArray = array();
	for($i=0; $i< count($topArtists)-4; $i++){
		array_push($topArtistsNamesArray, $topArtists[$i][0]);
	}

	$graphsql = "SELECT * FROM " . $userID . "_graphdata ORDER BY total DESC";
	$graphResult = $conn->query($graphsql);

	$d3DateArray = array();
	$dateArray = array();
	$d3formatted = array();
	
	$columnResult = $conn->query($sql);
	mysqli_fetch_assoc($columnResult); //first is artist
	while ($columnRow = mysqli_fetch_assoc($columnResult)) {
		$d3Date = date("U", strtotime($columnRow["COLUMN_NAME"])); //format for d3 graph
		if ($startDate) {

			if ($d3Date >= $startDate) {
				fwrite($myfile, "hi" . "\n");
				if ($endDate) {
					if ($d3Date <= $endDate) {
						array_push($dateArray,$columnRow["COLUMN_NAME"]);
						array_push($d3DateArray, $d3Date);
					}
				} else {
					array_push($dateArray,$columnRow["COLUMN_NAME"]);
					array_push($d3DateArray, $d3Date);
				}
			}
		} else if ($endDate) {
			if ($d3Date <= $endDate) {
				array_push($dateArray,$columnRow["COLUMN_NAME"]);
				array_push($d3DateArray, $d3Date);
			}
		} else {
			array_push($dateArray,$columnRow["COLUMN_NAME"]);
			array_push($d3DateArray, $d3Date);
		}
	}

	while($graphRow = mysqli_fetch_assoc($graphResult)) {
		$tempArray = array();
		if (in_array($graphRow['artist'], $topArtistsNamesArray)) {
			array_push($tempArray, $graphRow['artist']);
			for($i=0;$i < count($dateArray)-1; $i++) {
				$datePlayArray = array();
				array_push($datePlayArray, $d3DateArray[$i] * 1000);
				array_push($datePlayArray,(int)$graphRow[$dateArray[$i]]);
				array_push($tempArray, $datePlayArray);
			}
			array_push($d3formatted, $tempArray);
		}
	}
	echo json_encode($d3formatted);
	
?>