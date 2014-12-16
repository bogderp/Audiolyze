<?php
    session_start();
    $conn = new mysqli("localhost", "publicuser", "srQ-kdq-5Jt-Mwp", "spotigraph");
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 
    $token = $_SESSION['token'];
    require_once('facebook/autoload.php');

    use Facebook\FacebookSession; 
    use Facebook\FacebookJavaScriptLoginHelper;
    use Facebook\FacebookRequest;
    use Facebook\GraphUser;
    use Facebook\FacebookRequestException;

    FacebookSession::setDefaultApplication('171546376229575', '41345f543846339dba9079ddf0157df9');

    $session = new FacebookSession($token);

    if($session) {
        try {
            $userPlays = $_GET['data'];
            $initialReq = new FacebookRequest(
                $session, 'GET', '/me/music.listens?limit=' . $userPlays . '');
            $response = $initialReq->execute();
            $reqObject = $response->getGraphObject();               

            $nextRequest = "/?ids=";
            $total = 0;
            $userID = $reqObject->getProperty('data')->getProperty(0)->getProperty('from')
                ->getProperty('id') . "";

            $sql = "CREATE TABLE " . $userID . "_musicdata (
                playID VARCHAR(100) NULL,
                publishTime TEXT NULL,
                songID BIGINT(50) NULL,
                songName TEXT NULL,
                artistName TEXT NULL,
                albumName TEXT NULL,
                albumURL TEXT NULL,
                songURL TEXT NULL
                )";
            $conn->query($sql);
            
            for ($i = 0; $i < count($reqObject->getProperty('data')->asArray()); $i++) {
                $playID = $reqObject->getProperty('data')->getProperty($i)->getProperty('id');
                $pubTime = $reqObject->getProperty('data')->getProperty($i)->getProperty('publish_time');
                $songID = $reqObject->getProperty('data')->getProperty($i)->getProperty('data')->
                     getProperty('song')->getProperty('id');

                $songData = "INSERT INTO " . $userID . "_musicdata (playID, publishTime, songID)
                    VALUES ('$playID', '$pubTime', '$songID')";
                $conn->query($songData);

                $nextRequest .= $songID;
                $total ++;
                if ($total != 50 && $i != count($reqObject->getProperty('data')->asArray())-1) {
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
            }  

        } catch(FacebookRequestException $e) {

            echo "Exception occured, code: " . $e->getCode();
            echo " with message: " . $e->getMessage();

        }
    }

    $conn->close();
?>