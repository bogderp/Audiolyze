<?php
    session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Spotigraph Beta PHP</title>
    <style>
        body {
            text-align: center;
            background: green;
            background-size: cover;
            background-position: center;
            color: white;
            font-family: helvetica;
        }
        p {
            font-size: 22px;
            cursor: pointer; 
        }
        p#collection:hover {
            background: black;

        }
        img.loading2 {
            padding-top: 15px;
        }
    </style>
</head>
<body>
<div id="fb-root"></div>
    <script src="http://code.jquery.com/jquery-1.10.2.js"></script>
    <script>
            // This is called with the results from from FB.getLoginStatus().
            function statusChangeCallback(response) {
                console.log('statusChangeCallback');
                console.log(response);
                // The response object is returned with a status field that lets the
                // app know the current login status of the person.
                // Full docs on the response object can be found in the documentation
                // for FB.getLoginStatus().
                if (response.status === 'connected') {
                    // Logged into your app and Facebook.
                    testAPI();
                } else if (response.status === 'not_authorized') {
                    // The person is logged into Facebook, but not your app.
                    document.getElementById('status').innerHTML = 'Please log ' +
                        'into this app.';
                } else {
                    // The person is not logged into Facebook, so we're not sure if
                    // they are logged into this app or not.
                    document.getElementById('status').innerHTML = 'Please log ' +
                        'into Facebook.';
                }
            }
            // This function is called when someone finishes with the Login
            // Button.  See the onlogin handler attached to it in the sample
            // code below.
            function checkLoginState() {
                FB.getLoginStatus(function(response) {
                    statusChangeCallback(response);
                });
            }
            window.fbAsyncInit = function() {
                FB.init({
                    appId      : '171546376229575',
                    cookie     : true,
                    xfbml      : true,
                    version    : 'v2.1'
                });
                FB.getLoginStatus(function(response) {
                    statusChangeCallback(response);
                });
            };
            (function(d, s, id){
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) {return;}
                js = d.createElement(s); js.id = id;
                js.src = "//connect.facebook.net/en_US/sdk.js";
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));
            function testAPI() {
                console.log('Welcome!  Fetching your information.... ');
                FB.api('/me', function(response) {
                    console.log('Successful login for: ' + response.name);
                    document.getElementById('status').innerHTML =
                        'Thanks for logging in, ' + response.name + '!' + "<br></br>";
                });
            }

            function lastSong(userID) {
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (xmlhttp.readyState === 4 && xmlhttp.status ===200) {
                        var lastSongArray = JSON.parse(xmlhttp.responseText);
                        d = new Date(lastSongArray[1]);
                        document.getElementById('lastsong').innerHTML = 
                            ("You last listened to music on " + 
                            d.toDateString() + " at " + d.toLocaleTimeString() + 
                            "<br>" +" You listened to : " + "<b>" + lastSongArray[2] + "</b>"
                            + " by <b>" + lastSongArray[3] + "</b> <br>" +
                            '<img src="' + lastSongArray[0] + '" height="100" width="100">');
                    }
                }
                xmlhttp.open("GET","lastSong.php",true);
                xmlhttp.send();
            }

    </script>

    <img src="http://www.hypebot.com/.a/6a00d83451b36c69e2017c324894d7970b-pi" height="300" width="300">
    <h1>Spotigraph</h1>
    <p>Hello, this is Spotigraph beta. There's not much to see here besides <br> 
        a few statistics, most of what is going on is happening behind the <br> 
        scenes...for now.</p>
    
<fb:login-button scope="public_profile,email,user_actions.music" onlogin="checkLoginState();">
    </fb:login-button>

    <div id="status"></div>

    <p id="collection">Click to Start</p>

    <div id="lastsong" style="padding-top: 20px; display:none"></div>
    
    <img class="loading2" align=center src="loading.gif" style="display:none"></img>
    
    <div id="playstat" style="padding-top: 20px; display:none"></div>

    <div id="thegraph"></div>

    <div 
        class="fb-like" 
        data-href="https://www.facebook.com/Spotigraph" 
        data-width="1440" data-layout="standard" 
        data-action="like" data-show-faces="true" 
        data-share="true">
    </div>

    <script>
        $('#collection').click(function() {
            $('#collection').fadeOut('fast');
            $('#collection').replaceWith("<img class=\"loading\" src=\"loading.gif\" style=\"display:none\"><br></br></img>");
            $('.loading').fadeIn('slow');
            $('.loading2').fadeIn('slow');
            $.ajax({
                url: "buildDatabase.php",
                success: function() {
                    $('.loading').fadeTo('fast', 0);
                    lastSong();
                    $('#lastsong').fadeIn('slow');
                    $.ajax({
                        url: "formatGraphData.php",
                        success: function() {
                            console.log("done");
                            $.ajax({
                                url: "topArtists.php",
                                dataType: 'json',
                                success: function(data) {
                                    console.log("done");
                                    document.getElementById('playstat').innerHTML = 
                                        ("Top Five Artist's Number of Plays"+ '<br>' + 
                                        "1. " + data[0][0] + " was played " + data[0][1] + " times! " + '<br>' + 
                                        "2. " + data[1][0] + " was played " + data[1][1] + " times! " + '<br>' + 
                                        "3. " + data[2][0] + " was played " + data[2][1] + " times! " + '<br>' +
                                        "4. " + data[3][0] + " was played " + data[3][1] + " times! " + '<br>' +
                                        "5. " + data[4][0] + " was played " + data[4][1] + " times! ");
                                    $('.loading2').fadeTo('fast', 0);
                                    $('#playstat').fadeIn('slow');
                                }
                            }); 
                        }
                    });      
                }
            });      
        });        
    </script>
</body>
</html>