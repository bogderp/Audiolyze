<?php
    session_start();
    $conn = new mysqli("localhost", "publicuser", "srQ-kdq-5Jt-Mwp", "spotigraph");
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 

    require_once('facebook/autoload.php');

    use Facebook\FacebookSession; 
    use Facebook\FacebookJavaScriptLoginHelper;
    use Facebook\FacebookRequest;
    use Facebook\GraphUser;
    use Facebook\FacebookRequestException;

    FacebookSession::setDefaultApplication('171546376229575', '41345f543846339dba9079ddf0157df9');

    $helper = new FacebookJavaScriptLoginHelper();
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
            $userReq = new FacebookRequest(
                $session, 'GET', '/me?fields=id,name');
            $response = $userReq->execute();
            $reqObject = $response->getGraphObject();  

            $userID = $reqObject->getProperty('id') . "";
            $_SESSION['tableKey'] = $userID;
            $userName = $reqObject->getProperty('name') . "";

            $musicResult = $conn->query("SHOW TABLES LIKE '" . $userID . "_musicdata'");
            $userResult = $conn->query("SELECT currentLoginTime FROM userData WHERE userID='$userID'");
            $currentTime = date("c");
            $reportArray = array();

            if(mysqli_num_rows($userResult) == 1){
                $prevLogin = mysqli_fetch_assoc($userResult)['currentLoginTime'];
                $userInfo = "UPDATE userData SET prevLoginTime='$prevLogin', 
                    currentLoginTime='$currentTime' WHERE userID='$userID'";
                $conn->query($userInfo);
                if (mysqli_num_rows($musicResult) == 1) {
                	$graphResult = $conn->query("SHOW TABLES LIKE '" . $userID . "_graphdata'");
                	if (mysqli_num_rows($graphResult) == 1) {
                		//lastSong
                		//topArtists
                        $code = 1;
                        array_push($reportArray, $code);
                        array_push($reportArray, $prevLogin);
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
                $userInfo = "INSERT INTO userData (name, userID, currentLoginTime)
                    VALUES ('$userName', '$userID', '$currentTime')";
                $conn->query($userInfo);
                //buildDatabase
                //formatGraphData
                //lastSong
                //topArtists
                array_push($reportArray,4);
                echo json_encode($reportArray);
            }
       
        } catch(FacebookRequestException $e) {

            echo "Exception occured, code: " . $e->getCode();
            echo " with message: " . $e->getMessage();

        }
    }

    $conn->close();
?>