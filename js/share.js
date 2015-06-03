(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&appId=171546376229575&version=v2.0";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));



function dynamicHeight() {
    var height = $(window).height();
    height = parseInt(height) + 'px';
    $("main").css('height',height);
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



function toD3Format(startDate, endDate) {
    $.ajax({
        url: "php/toD3Format.php",
        data: {'startDate':startDate,'endDate':endDate},
        type: "POST",
        dataType: 'JSON',
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
            defaultChartConfig("thegraph", d3formatted); 
            $('#thegraph').fadeTo('slow',1);
        }
    });    
};

function restOfArtists(data) {

}

function topArtists() {
    $.ajax({
        url: "php/topArtists.php",
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
            $("#dateContainer").fadeIn('slow');
            $("#thegraph").css('opacity',1);
            $('#lastsong').fadeIn('slow', function () {
                $('#playstat').fadeIn('slow');
                if (data.length > 5) {
                    $('#playstat2').fadeIn('slow');
                }
            });
            $('.fb-like-box').fadeIn('slow');
        }
    }); 
}

var minStartDate = "";
var validStartDate = "";
var maxEndDate = "";
var validEndDate = "";
function getDateRange() {
    $.ajax({
        url: "php/getDateRange.php",
        dataType: "JSON",
        success: function(data) {
            minStartDate = data[0];
            validStartDate = minStartDate;
            var sDate = new Date(minStartDate*1000);
            maxEndDate = data[1];
            validEndDate = maxEndDate;
            var eDate = new Date(maxEndDate*1000);
            $('#startLabel').text("Please enter a date after " + (sDate.getMonth()+1) + "/" + sDate.getDate() + "/" + sDate.getFullYear());
            $('#endLabel').text("Please enter a date before " + (eDate.getMonth()+1) + "/" + eDate.getDate() + "/" + eDate.getFullYear());
        }
    });  
}
 
function lastSong() {
    $.ajax({
            url: "php/lastSong.php",
            success: function(data) {
                var lastSongArray = JSON.parse(data);
                d = new Date(lastSongArray[1]);
                document.getElementById('lastsong').innerHTML = 
                    ("They last listened to music on " + 
                    d.toDateString() + " at " + d.toLocaleTimeString() + 
                    "<br>" +" You listened to : " + "<b>" + lastSongArray[2] + "</b>"
                    + " by <b>" + lastSongArray[3] + "</b> <br>" +
                    '<img src="' + lastSongArray[0] + '" height="100" width="100">');
                    topArtists();
            }
        });
}
         
$(document).ready(function(){
    $(window).on('beforeunload', function() {
        $(window).scrollTop(0);
    });
    dynamicHeight();
    $(window).bind('resize', dynamicHeight);

    $('#requestForm').on('submit',function(e) {
        e.preventDefault();
        requestData = $(this).serialize();
        console.log(requestData);
        $.ajax({
                url: "php/request.php",
                type: "POST",
                data: requestData,
                success: function(data) {
                    console.log(data);
                    $("#requests").modal('hide');
                }
        });  
    });
    getDateRange();
    lastSong();

    isVisible = 0;
    $('.mobile').click(function(){
        if(!isVisible) {
            $('.mobileMenu li').fadeIn('slow');
            isVisible = 1;
        } else {
            $('.mobileMenu li').fadeOut('slow');
            isVisible = 0;
        }
        
    })

    $('#mobileimg').click(function(){
        if(!isVisible) {
            $('.mobileMenu li').fadeIn('slow');
            isVisible = 1;
        } else {
            $('.mobileMenu li').fadeOut('slow');
            isVisible = 0;
        }
        
    })

    // if text input, startField value is not empty show the "X" button
    validStartDate = minStartDate;
    $("#startField").keyup(function() {
        $("#x").fadeIn();

        userStartDate = new Date($.trim($("#startField").val()));
        if((userStartDate.getMonth()+1) && userStartDate.getDate() && userStartDate.getFullYear() 
                && userStartDate.getFullYear() > 2013 && userStartDate.getFullYear() <= new Date().getFullYear()) {
            if (Date.parse(userStartDate) >= minStartDate*1000 && Date.parse(userStartDate) <= maxEndDate*1000 && Date.parse(userStartDate) < validEndDate*1000) {
                $('#thegraph').fadeTo('slow',0, function() {
                    validStartDate = Date.parse(userStartDate)/1000;
                    toD3Format(Date.parse(userStartDate)/1000, validEndDate);
                });
                
            }
        }
        if ($.trim($("#startField").val()) == "") {
            $("#x").fadeOut();
            $('#thegraph').fadeTo('slow',0, function() {
                toD3Format(minStartDate, validEndDate);
                validStartDate = minStartDate;
            });
        }
    });
    // on click of "X", delete input field value and hide "X"
    $("#x").click(function() {
        $("#startField").val("");
        $(this).hide();
        $('#thegraph').fadeTo('slow',0, function() {
            toD3Format(minStartDate, validEndDate);
            validStartDate = minStartDate;
        });
    });

    // if text input, startField value is not empty show the "X" button
    $("#endField").keyup(function() {
        $("#x2").fadeIn();

        userEndDate = new Date($.trim($("#endField").val()));
        if((userEndDate.getMonth()+1) && userEndDate.getDate() && userEndDate.getFullYear() 
                && userEndDate.getFullYear() > 2013 && userEndDate.getFullYear() <= new Date().getFullYear()) {
            if (Date.parse(userEndDate) > minStartDate*1000 && Date.parse(userEndDate) <= maxEndDate*1000 && Date.parse(userEndDate) > validStartDate*1000) {
                $('#thegraph').fadeTo('slow',0, function() {
                    validEndDate = Date.parse(userEndDate)/1000;
                    toD3Format(validStartDate, Date.parse(userEndDate)/1000);
                });
                
            }
        }
        if ($.trim($("#endField").val()) == "") {
            $("#x2").fadeOut();
            $('#thegraph').fadeTo('slow',0, function() {
                toD3Format(validStartDate, maxEndDate);
                validEndDate = maxEndDate;
            });
        }
    });
    // on click of "X", delete input field value and hide "X"
    $("#x2").click(function() {
        $("#endField").val("");
        $(this).hide();
        $('#thegraph').fadeTo('slow',0, function() {
            toD3Format(validStartDate, maxEndDate);
            validEndDate = maxEndDate;
        });
    });



    $('#startEndDate').submit(function() {
        return false;
    });
}); 