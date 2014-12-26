function dynamicHeight() {
    var height = $(window).height();
    //heightGraph = parseInt(height * 0.90) + 'px';
    height = parseInt(height) + 'px';
    $("main").css('height',height);
    //$(".chart.full").css('height',heightGraph);
}

var colors = d3.scale.category20();
var keyColor = function(d, i) {return colors(d.key)};

function defaultChartConfig(container, data, useGuideline) {
  if (useGuideline === undefined) useGuideline = true;
  nv.addGraph(function() {
    var chart;
    chart = nv.models.stackedAreaChart()
                  .useInteractiveGuideline(false)
                  .x(function(d) { return d[0] })
                  .y(function(d) { return d[1] })
                  .color(keyColor);

    chart.xAxis
        .tickFormat(function(d) { return d3.time.format('%x')(new Date(d)) });

    chart.yAxis
        .tickFormat(d3.format(',.2f'));

    d3.select('#' + container + ' svg')
          .datum(data)
        .transition().duration(500).call(chart);

    nv.utils.windowResize(chart.update);

    return chart;
  });
}

function toD3Format() {
    $.ajax({
        url: "webFiles/php/toD3Format.php",
        dataType: 'json',
        success: function(data) {
            var d3formatted = [ ];
            for(i=0; i<data.length; i++){
                obj = {
                    "key": data[i][0],
                    "values": [],
                };
                for(m=1; m<data[i].length; m++){
                    obj.values.push(data[i][m]);
                };
                d3formatted.push(obj);
            };
            console.log(d3formatted);
            defaultChartConfig("thegraph", d3formatted); 
        }
    });    
};

function topArtists() {
    $.ajax({
        url: "webFiles/php/topArtists.php",
        dataType: 'json',
        success: function(data) {
            toD3Format();
            otherArtists = data[data.length-4][1];
            totalPlays = data[data.length-3];
            newestDate = new Date(data[data.length-2][0]);
            oldestDate = new Date(data[data.length-2][1]);

            if (newestDate.toDateString() === oldestDate.toDateString()) {
                dateSpan = "today " + oldestDate.toDateString();
            } else {
                dateSpan = "from " + oldestDate.toDateString() + " to " + newestDate.toDateString();
            }

            avgTimePlays = data[data.length-1];

            if (data.length > 8) {
                document.getElementById('playstat').innerHTML = 
                    ("Top 5 Artists' Number of Plays " +  
                    dateSpan + "<br>" +
                    "1. " + data[0][0] + " was played " + data[0][1] + " times! " + 
                        "That\'s " + (data[0][1]/totalPlays*100).toFixed(2) + "% of Plays <br>" + 
                    "2. " + data[1][0] + " was played " + data[1][1] + " times! " + 
                        "That\'s " + (data[1][1]/totalPlays*100).toFixed(2) + "% of Plays <br>" + 
                    "3. " + data[2][0] + " was played " + data[2][1] + " times! " + 
                        "That\'s " + (data[2][1]/totalPlays*100).toFixed(2) + "% of Plays <br>" + 
                    "4. " + data[3][0] + " was played " + data[3][1] + " times! " + 
                        "That\'s " + (data[3][1]/totalPlays*100).toFixed(2) + "% of Plays <br>" + 
                    "5. " + data[4][0] + " was played " + data[4][1] + " times! " +
                        "That\'s " + (data[4][1]/totalPlays*100).toFixed(2) + "% of Plays");
            } else if (data.length == 5) {
                document.getElementById('playstat').innerHTML = ("No Top Artist data to Display!");
            } else {
                playStat = "Top " + (data.length-4) + " Artists' Number of Plays " + dateSpan + "<br>";
                
                for (i=0; i< data.length-4; i++) {
                    playStat += (i+1) + ". " + data[i][0] + " was played " + data[i][1] + " times! " + 
                        "That\'s " + (data[i][1]/totalPlays*100).toFixed(2) + "% of Plays <br>";
                }
                document.getElementById('playstat').innerHTML = (playStat);
            }

            document.getElementById('playstat2').innerHTML = 
                ("On average, in the past " + totalPlays + " listens they listened to a song every " +
                (avgTimePlays/60).toFixed(2) + " minutes!");
            $('.loading').fadeOut('slow');
            $('#lastsong').fadeIn('slow', function () {
                $('#playstat').fadeIn('slow');
                $("#thegraph").css('opacity',1);
                $('html, body').animate({
                    scrollTop: $(".bodyContainer").offset().top
                }, 2000);
            });
            $('#removeData').fadeIn('slow');
            $('.fb-like-box').fadeIn('slow');
        }
    }); 
}

function avgTimeBtwnPlays() {
    $.ajax({
        url: "webFiles/php/avgTimeBtwnPlays.php",
        success: function() {
            topArtists();
        }
    });  
}

function buildGraphTable() {
    $.ajax({
        url: "webFiles/php/buildGraphTable.php",
        success: function() {
            console.log("done");
            avgTimeBtwnPlays();
        }
    }); 
}  

function addToGraphTable() {
    $.ajax({
        url: "webFiles/php/addToGraphTable.php",
        success: function(data) {
            console.log(data);
            avgTimeBtwnPlays();
        }
    }); 
} 

function lastSong() {
    $.ajax({
            url: "webFiles/php/lastSong.php",
            success: function(data) {
                var lastSongArray = JSON.parse(data);
                d = new Date(lastSongArray[1]);
                document.getElementById('lastsong').innerHTML = 
                    ("You last listened to music on " + 
                    d.toDateString() + " at " + d.toLocaleTimeString() + 
                    "<br>" +" You listened to : " + "<b>" + lastSongArray[2] + "</b>"
                    + " by <b>" + lastSongArray[3] + "</b> <br>" +
                    '<img src="' + lastSongArray[0] + '" height="100" width="100">');
            }
        });
}


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
    $('#fblogin').fadeOut('slow');
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
        $('#status').fadeIn("slow");
        checkPermissions();
    });
}

function fbShare (link) {
    FB.ui({
      method: 'share',
      href: link,
    }, function(response){});
}

function checkPermissions(){
    FB.api(
        "/me/permissions",
        function (response) {
            if (response.data.length == 4) {
                $('#fblogin').fadeOut('slow');
                $('#grantPerm').fadeOut('slow');
                $.ajax({
                    url: "webFiles/php/router.php",
                    dataType: 'json',
                    success: function(data) {
                        console.log(data[0]);
                        if (data[0] == 1) {
                            lastLogin = new Date(data[1]);
                            $('#directions').replaceWith("<p id=\"directions\" " +
                                "style=\"display:none\">You were last here on " + 
                                lastLogin.toDateString() + " at " + lastLogin.toLocaleTimeString() + 
                                ". " + "Click the button below" +
                                " to recalculate your statistics and graph since your last login.</p>");
                            $('#directions').fadeIn('slow', function() {
                            });
                            $('#addToTable').fadeIn('slow');
                            $('#fbShare').attr("onclick","fbShare('http://www.audiolyze.com/share.php"  + data[2] + "')");
                            $('#viewStats').attr("href","http://www.audiolyze.com/share.php" + data[2]);
                            $('#statsDropdown').fadeIn('slow');
                        } else if (data[0] == 2){
                            lastSong();
                            buildGraphTable();
                        } else {
                            $('#directions').fadeOut('slow',function(){
                                $('#directions').replaceWith("<p id=\"directions\" style=\"display:none\">" +
                                    "Please choose the number of listens to query. <br> Wait time is " +
                                    "greater with a greater chosen value <br></p>");
                                $('#directions').fadeIn('slow');
                                $('#playForm').fadeIn('slow');
                            });
                        }
                    }
                }); 
            } else {
                $('#grantPerm').fadeIn('slow');
            }
        }
    );
}            

$(document).ready(function(){
    $(window).on('beforeunload', function() {
        $(window).scrollTop(0);
    });
    dynamicHeight();
    $(window).bind('resize', dynamicHeight);
    $('#fblogin').fadeIn('slow');
    $('#form').on('submit',function(e) {
        e.preventDefault();
        var userDefined = ($(this).serializeArray())[0].value;
        $("#playForm").fadeOut('slow');
        $('#directions').fadeOut('slow', function(){
            $('.loading').fadeIn('slow');
        });

        $.ajax({
                url: "webFiles/php/buildMusicTable.php",
                data: {'data':userDefined},
                success: function(data) {
                    console.log(data);
                    $('.loading').fadeOut('fast');
                    lastSong();
                    $('#lastsong').fadeIn('slow');
                    buildGraphTable();  
                }
        });  
    });

    $('#requestForm').on('submit',function(e) {
        e.preventDefault();
        requestData = $(this).serialize();
        console.log(requestData);
        $.ajax({
                url: "webFiles/php/request.php",
                type: "POST",
                data: requestData,
                success: function(data) {
                    console.log(data);
                    $("#requests").modal('hide');
                }
        });  
    });



    $('#addToTable').click(function() {
        $('#directions').fadeOut('slow');
        $('#lastsong').fadeOut('slow');
        $('#playstat').fadeOut('slow');
        $('#removeData').fadeOut('slow');
        $('.fb-like-box').fadeOut('slow');  
        $('#addToTable').fadeOut('slow', function() {
            $('.loading').fadeIn('slow');
            $.ajax({
                url: "webFiles/php/addToMusicTable.php",
                success: function() {
                    lastSong();
                    addToGraphTable();                    
                }
            }); 
        });
    })


    $('#removeData').click(function() {
        $('#status').fadeOut("slow");
        $('#directions').fadeOut('slow');
        $('#addToTable').fadeOut('slow');
        $('#lastsong').fadeOut('slow');
        $('#playstat').fadeOut('slow');
        $('.fb-like-box').fadeOut('slow');  
        $('#removeData').fadeOut('slow', function() {
            $.ajax({
                url: "webFiles/php/deleteData.php",
                success: function() { 
                    console.log("Everything Deleted");
                    FB.logout();
                    $('#fblogin').fadeIn('slow');
                    $('#directions').replaceWith("<p id=\"directions\" " +
                        "style=\"display:none\">All data has been deleted. <br> " +
                        "To start over please log in.</p>"); 
                    $('#directions').fadeIn('slow');
                }
            }); 
        });
    })
}); 