<?php
	$host = "localhost";
	$user = "publicuser";
	$pass = "srQ-kdq-5Jt-Mwp";
	$base = "audiolyze";

    $conn = new mysqli($host, $user, $pass, $base);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
