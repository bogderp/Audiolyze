<?php
    session_start();
    $conn = new mysqli("localhost", "publicuser", "srQ-kdq-5Jt-Mwp", "spotigraph");
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 
    $token = $_SESSION['token'];
    $userID = $_SESSION['tableKey'];
    require_once('facebook/autoload.php');

    use Facebook\FacebookSession; 
    use Facebook\FacebookJavaScriptLoginHelper;
    use Facebook\FacebookRequest;
    use Facebook\GraphUser;
    use Facebook\FacebookRequestException;

    FacebookSession::setDefaultApplication('171546376229575', '41345f543846339dba9079ddf0157df9');

    $session = new FacebookSession($token);

    $playResult = "";
    $plays = '/me/music.listens?limit=50';
    $offset = 50;
    $isTrue = true;

    if($session) {

        try {
            while ($isTrue) {
                $initialReq = new FacebookRequest(
                    $session, 'GET', $plays);
                $response = $initialReq->execute();
                $reqObject = $response->getGraphObject();               

                $nextRequest = "/?ids=";
                $total = 0;
                for ($i = 0; $i < count($reqObject->getProperty('data')->asArray()); $i++) {
                    $playID = $reqObject->getProperty('data')->getProperty($i)->getProperty('id');
                    $pubTime = $reqObject->getProperty('data')->getProperty($i)->getProperty('publish_time');
                    $songID = $reqObject->getProperty('data')->getProperty($i)->getProperty('data')->
                         getProperty('song')->getProperty('id');



                    $playResult = $conn->query("SELECT playID FROM " . $userID . "_musicdata WHERE playID='$playID'");

                    if(mysqli_num_rows($playResult) == 0){
                        $songData = "INSERT INTO " . $userID . "_musicdata (playID, publishTime, songID)
                            VALUES ('$playID', '$pubTime', '$songID')";
                        $conn->query($songData);

                        $nextRequest .= $songID;
                        $total ++;
                        if ($total != 50) {
                            $nextRequest .= ',';
                        } else {
                            try {  
                                $songReq = new FacebookRequest(
                                    $session, 'GET', $nextRequest);
                                $response = $songReq->execute();
                                $songObject = $response->getGraphObject();

                                $allSongIDs = $songObject->getPropertyNames();
                                foreach ($allSongIDs as $theID) {
                                    $songName =  mysql_escape_string($songObject->getProperty($theID)
                                            ->getProperty('title'));
                                    $artistName = mysql_escape_string($songObject->getProperty($theID)
                                            ->getProperty('data')->getProperty('musician')->getProperty(0)
                                            ->getProperty('name'));
                                    $albumName = mysql_escape_string($songObject->getProperty($theID)
                                            ->getProperty('data')->getProperty('album')->getProperty(0)
                                            ->getProperty('url')->getProperty('title'));
                                    $albumURL = mysql_escape_string($songObject->getProperty($theID)
                                            ->getProperty('image')->getProperty(0)->getProperty('url'));
                                    $songURL = mysql_escape_string($songObject->getProperty($theID)
                                            ->getProperty('url'));

                                    $musicHistory = "UPDATE " . $userID . "_musicdata SET songName='$songName', 
                                        artistName='$artistName', albumName='$albumName', 
                                        albumURL='$albumURL', songURL='$songURL' WHERE songID='$theID'" ;
                                    $conn->query($musicHistory);

                                }
                            } catch(FacebookRequestException $e) {

                                echo "Exception occured, code: " . $e->getCode();
                                echo " with message: " . $e->getMessage();

                            }
                            $nextRequest = "/?ids=";
                            $total = 0;
                        } 
                    } else if($i > 0 && mysqli_num_rows($playResult) == 1) {
                        $nextRequest = substr($nextRequest,0,strlen($nextRequest)-1);
                        echo $nextRequest;
                        try {  
                            $songReq = new FacebookRequest(
                                $session, 'GET', $nextRequest);
                            $response = $songReq->execute();
                            $songObject = $response->getGraphObject();

                            $allSongIDs = $songObject->getPropertyNames();
                            foreach ($allSongIDs as $theID) {
                                $songName =  mysql_escape_string($songObject->getProperty($theID)
                                        ->getProperty('title'));
                                $artistName = mysql_escape_string($songObject->getProperty($theID)
                                        ->getProperty('data')->getProperty('musician')->getProperty(0)
                                        ->getProperty('name'));
                                $albumName = mysql_escape_string($songObject->getProperty($theID)
                                        ->getProperty('data')->getProperty('album')->getProperty(0)
                                        ->getProperty('url')->getProperty('title'));
                                $albumURL = mysql_escape_string($songObject->getProperty($theID)
                                        ->getProperty('image')->getProperty(0)->getProperty('url'));
                                $songURL = mysql_escape_string($songObject->getProperty($theID)
                                        ->getProperty('url'));

                                $musicHistory = "UPDATE " . $userID . "_musicdata SET songName='$songName', 
                                    artistName='$artistName', albumName='$albumName', 
                                    albumURL='$albumURL', songURL='$songURL' WHERE songID='$theID'" ;
                                $conn->query($musicHistory);

                            }
                        } catch(FacebookRequestException $e) {

                            echo "Exception occured, code: " . $e->getCode();
                            echo " with message: " . $e->getMessage();

                        }
                        break;
                    }
                } 
                if(mysqli_num_rows($playResult) == 1){
                    $isTrue = false;
                }
                $plays = 'me/music.listens?limit=50&offset=' . '$offset';
                $offset += 50;
            }

        } catch(FacebookRequestException $e) {

            echo "Exception occured, code: " . $e->getCode();
            echo " with message: " . $e->getMessage();

        }
    }
    $conn->query("DROP TABLE `" . $userID . "_graphdata`");
    $conn->close();
?>