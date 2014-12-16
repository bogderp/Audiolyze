<?php

    session_start();
    $conn = new mysqli("localhost", "publicuser", "srQ-kdq-5Jt-Mwp", "spotigraph");
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 

    $userID = $_SESSION['tableKey'];

    $result = $conn->query("SELECT publishTime FROM " . $userID . 
        "_musicdata ORDER BY publishTime");

    $count = mysqli_num_rows($result);
    $row = mysqli_fetch_assoc($result);
    $rowTime = substr($row["publishTime"], 0 ,22) . 
    	':' . substr($row["publishTime"], 22);
    $olderTime = date("U",strtotime($rowTime));
    $numerator = 0;
    $denominator = 0;

    while($denominator < $count-1) {
    	$nextRow = mysqli_fetch_assoc($result);
	    $nextRowTime = substr($nextRow["publishTime"], 0 ,22) . 
	    	':' . substr($nextRow["publishTime"], 22);
	    $newestTime = date("U",strtotime($nextRowTime));
	    $numerator += ($olderTime-$newestTime);
	    echo $numerator . " ";
	    $denominator++;
	    $olderTime = $newestTime;
    }

    $avgTime = abs($numerator/$denominator); //Average time between plays;
    $userInfo = "UPDATE userData SET avgTimeBtwnPlays='$avgTime' WHERE userID='$userID'";
	$conn->query($userInfo);
    $conn->close();
?>