<?php
	session_start();
	$userID = $_SESSION['tableKey'];
    include_once('config.php');

	$sql = "SELECT `COLUMN_NAME` 
		FROM `INFORMATION_SCHEMA`.`COLUMNS` 
		WHERE `TABLE_SCHEMA`='audiolyze' 
		AND `TABLE_NAME`='" . $userID . "_graphdata'";

	$columnResult = $conn->query($sql);
	mysqli_fetch_assoc($columnResult); //first is artist

	$dateArray = array();
	$columnRow = mysqli_fetch_assoc($columnResult); //last date.
	$lastDate = $columnRow["COLUMN_NAME"];
	$tempDate = '';
	while ($columnRow = mysqli_fetch_assoc($columnResult)) {
		$firstDate = $tempDate;
		$tempDate = $columnRow["COLUMN_NAME"]; //to avoid the last entry 'total'.
	}

    $userInfo = "UPDATE userData SET startDate='$firstDate', 
        endDate='$lastDate' WHERE userID='$userID'";
    $conn->query($userInfo);
	
    $startEndArray = array(date("U", strtotime($firstDate)), date("U", strtotime($lastDate)));
    echo json_encode($startEndArray);

?>