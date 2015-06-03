<?php
    session_start();
    require_once('config.php');
    require_once('fbConfig.php');

    $helper = new Facebook\FacebookJavaScriptLoginHelper();
    try {
        $session = $helper->getSession();
        $token = $session->getAccessToken() . "";
        $_SESSION['token'] = $token;
    } catch(FacebookRequestException $ex) {
        // When Facebook returns an error
    } catch(\Exception $ex) {
        // When validation fails or other local issues
    } 

    if($session) {
        try {
            $userReq = new Facebook\FacebookRequest(
                $session, 'GET', '/me?fields=id,name');
            $response = $userReq->execute();
            $reqObject = $response->getGraphObject();  

            $userID = $reqObject->getProperty('id') . "";
            $_SESSION['tableKey'] = $userID;
            $userName = $reqObject->getProperty('name') . "";

            $musicResult = $conn->query("SHOW TABLES LIKE '" . $userID . "_musicdata'");
            $userResult = $conn->query("SELECT currentLoginTime FROM userData WHERE userID='$userID'");
            $shareResult = $conn->query("SELECT shareCode FROM userData WHERE userID='$userID'");
            $currentTime = date("c");
            $reportArray = array();

            if(mysqli_num_rows($userResult) == 1){
                $prevLogin = mysqli_fetch_assoc($userResult)['currentLoginTime'];
                $shareCode = mysqli_fetch_assoc($shareResult)['shareCode'];
                $userInfo = "UPDATE userData SET prevLoginTime='$prevLogin', 
                    currentLoginTime='$currentTime' WHERE userID='$userID'";
                $conn->query($userInfo);

                if (!$shareCode) {
                    $shareCode = mysql_escape_string("?i=" . base_convert($userID, 13, 18));
                    
                    $shareInfo = "UPDATE userData SET shareCode='$shareCode' WHERE userID='$userID'";
                    $conn->query($shareInfo);
                }

                if (mysqli_num_rows($musicResult) == 1) {
                	$graphResult = $conn->query("SHOW TABLES LIKE '" . $userID . "_graphdata'");
                	if (mysqli_num_rows($graphResult) == 1) {
                        $lastSongResult = $conn->query("SELECT playID FROM " 
                            . $userID . "_musicdata ORDER BY publishTime DESC");
                        $lastSongArray = mysqli_fetch_assoc($lastSongResult);
                        $lastSong = $lastSongArray['playID'];
                        $lastSongInfo = "UPDATE userData SET lastSong=$lastSong WHERE userID='$userID'";
                        $conn->query($lastSongInfo);

                		//lastSong
                		//topArtists
                        array_push($reportArray, 1);
                        array_push($reportArray, $prevLogin);
                        array_push($reportArray, $shareCode);
                        echo json_encode($reportArray);
                	} else {
                		//formatGraphData
                		//lastSong
                		//topArtists
                        // or startOver
                        array_push($reportArray,2);
                        echo json_encode($reportArray);
                	}
                } else {
                	//buildDatabase
            		//formatGraphData
            		//lastSong
            		//topArtists
                    array_push($reportArray,3);
                    echo json_encode($reportArray);
                }
            } else {

				$secReq = new Facebook\FacebookRequest(
				    $session, 'GET', '/'.$userID.'/music.listens?limit=15');
				$response2 = $secReq->execute();
				$reqObject2 = $response2->getGraphObject();               

				$songArray = $reqObject2->getProperty('data');

				if (count($songArray)) {
	                $shareCode = mysql_escape_string("?i=" . base_convert($userID, 13, 18));
	                
	                $userInfo = "INSERT INTO userData (name, userID, currentLoginTime, shareCode)
	                    VALUES ('$userName', '$userID', '$currentTime', '$shareCode')";
	                $conn->query($userInfo);
	               
	                //buildDatabase
	                //formatGraphData
	                //lastSong
	                //topArtists
	                array_push($reportArray,4);
	                echo json_encode($reportArray);
				} else {
	                array_push($reportArray,5);
	                echo json_encode($reportArray);
				}


            }
       
        } catch(Facebook\FacebookRequestException $e) {

            echo "Exception occured, code: " . $e->getCode();
            echo " with message: " . $e->getMessage();

        }
    }

    $conn->close();
?>