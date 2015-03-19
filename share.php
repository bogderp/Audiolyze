<?php
    session_start();
    include_once('webFiles/php/config.php');
    $conn = new mysqli($host, $user, $pass, $base);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $userID = base_convert($_GET['i'], 18, 13);

    $_SESSION['tableKey'] = $userID;

    $userResult = $conn->query("SELECT name FROM userData WHERE userID='$userID'");
    $name = mysqli_fetch_assoc($userResult)['name'];
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Bogdan Pozderca">
    <meta property="og:title" content='Audiolyzed Graph and Statistics' />
    <meta property="og:image" content="http://www.audiolyze.com/imgFiles/metaImage.png">
    <meta property="og:description" content="Check out my analyzed music history! You can even analyze your own for free!" />
    <meta property="og:url" content="<?php echo 'http://www.audiolyze.com/share.php?i=' . $_GET['i']; ?>"/>
    <link rel="shortcut icon" href="imgFiles/favicon.ico" type="image/x-icon">
    <link rel="icon" href="imgFiles/favicon.ico" type="image/x-icon">


	<?php echo "<title>" . $name . "'s Audiolyze Graph" . "</title>"; ?>

    <!-- Bootstrap core CSS -->
    <link href="webFiles/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="webFiles/css/share.css">
    <link href="webFiles/css/nv.d3.css" rel="stylesheet" type="text/css">
    <link href="webFiles/css/graph.css" rel="stylesheet" type='text/css'>
  </head>

  <body>    
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.html">Audiolyze</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="index.html">Home</a></li>
            <!--<li><a href="#about">About</a></li>
            <li><a href="#contact">Contact</a></li>-->
            <li><a href="FAQ.html">FAQ</a></li>
            <li><a href="#requests" data-toggle="modal" data-target="#requests">Submit A Request</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

    <main class="bs-docs-masthead" id="content" role="main">
         <div class="row">
            <div class="col-lg-4 col-md-2 col-sm-1">
            </div>       
            <div id="welcome "class="col-lg-4 col-md-8 col-sm-10 col-xs-12">
                <h1>Welcome to Audiolyze</h1>
                <?php echo "<p class=\"lead\">" . $name . " decided to share their Audiolyzed statistics and graph with you! Enjoy!</p>";
                      echo "<p class=\"lead2\">" . $name . " decided to share their Audiolyzed statistics and graph with you! To view the graph please visit this site on a computer. Enjoy!</p>"; 
                ?>
            </div>
            <div class="col-lg-4 col-md-2 col-sm-1">
            </div>
        </div>  

        <div id="dateContainer">
            <form id="startEndDate">
                <label id="startLabel"></label>
                <label id="endLabel"></label>
                <input id="startField" name="startField" type="text" placeholder="mm/dd/yyyy"/>
                <div id="delete"><span id="x">x</span></div>
                <input id="endField" name="endField" type="text" placeholder="mm/dd/yyyy"/>
                <div id="delete2"><span id="x2">x</span></div>
            </form>
        </div>    
	    <div class='chart full' id='thegraph'>Top Artists<svg></svg></div>    
	    
	     <div class="bodyContainer">
	        <div id="lastsong" style="display:none"></div>
	        
	        <div id="playstat" style="padding-top: 20px; display:none"></div>
            <div id="playstat2" style="padding-top: 20px; display:none"></div>
	    </div><!-- /.bodyContainer -->    
        
        <div class="fb-like-box" 
            data-href="https://www.facebook.com/Audiolyze" 
            data-width="100" data-colorscheme="dark" 
            data-show-faces="true" data-header="false" 
            data-stream="false" data-show-border="false"
            style="display:none">
        </div>      
    </main>


    <div class="modal fade" id="requests" tabindex="-1" role="dialog" aria-labelledby="requestLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="requestLabel">Request Form</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" id="requestForm">
                    <fieldset>

                        <!-- name input-->
                        <div class="form-group">
                          <label class="col-md-2 control-label" for="submitName">Name</label>  
                          <div class="col-md-10">
                          <input id="submitName" name="submitName" type="text" placeholder="John Doe" class="form-control input-md">
                            
                          </div>
                        </div>

                        <!-- email input-->
                        <div class="form-group">
                          <label class="col-md-2 control-label" for="submitEMail">E-mail</label>  
                          <div class="col-md-10">
                          <input id="submitEMail" name="submitEMail" type="text" placeholder="jdoe@domain.com" class="form-control input-md">
                            
                          </div>
                        </div>

                        <!-- Textarea -->
                        <div class="form-group">
                          <label class="col-md-2 control-label" for="submitRequest">Request</label>
                          <div class="col-md-10">                     
                            <textarea class="form-control" id="submitRequest" name="submitRequest" 
                                style="height:250px" placeholder="Maximum of 250 Words"></textarea>
                          </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="send" name="send">Submit Request</button>
                        </div>

                    </fieldset>
                </form>
            </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="webFiles/dist/js/bootstrap.min.js"></script>
    <script src="webFiles/js/d3.v3.js"></script>
    <script src="webFiles/js/nv.d3.js"></script>
    <script src="webFiles/js/share.js" type="text/javascript"></script>
  </body>
</html>

