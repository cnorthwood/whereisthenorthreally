<?php
    $token = md5(uniqid(rand(), true));
    setcookie('whereisthenorthcsrftoken', $token);
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Where is the North, really?</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" type="text/css">
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <link rel="stylesheet" href="leaflet/leaflet.css" type="text/css">
    <!--[if lte IE 8]>
        <link rel="stylesheet" href="leaflet/leaflet.ie.css" />
    <![endif]-->
    <link rel="stylesheet" href="whereisthenorthreally.css" />
    <script type="text/javascript">
        csrftoken = <?php echo json_encode($token); ?>;
    </script>
</head>

<body>
    <div class="container">
        <div class="page-header">
            <h1>So where's the North, really? <small>And what exactly is the Midlands?</small></h1>
            <p>A crowd-sourcing experiment to see where people think the geographic boundaries of England lay</p>
        </div>

        <div class="row">
            <div class="alert">
                <h2>Privacy Policy</h2>
                <button class="close" data-dismiss="alert">I'm okay with this</button>
                This site uses cookies as a security measure to prevent false submissions and records only your IP address to prevent against spam, and your postcode (if given) and choices for the purposes of analysing the data to determine where people perceive boundaries in England to lay.
           </div>
            <div class="span6 hidden-phone" id="map"></div>
            <div class="span6">
                <div class="alert" style="display: none;" id="results-message"></div>
                <div class="hero-unit">
                    <div id="submission-form" style="display: none;">
                        <form class="form-horizontal">
                            <legend>So where is...</legend>
                            <p id="placename"></p>
                            <fieldset style="text-align:center">
                                <div class="control-group">
                                    <button class="btn-primary btn-large" type="submit" id="north">The North</button>
                                    <button class="btn-primary btn-large" type="submit" id="midlands">The Midlands</button>
                                    <button class="btn-primary btn-large" type="submit" id="south">The South</button>
                                    <button class="btn" type="submit" id="dunno">I don't know where that is</button>
                                </div>
                                <div class="control-group">
                                    <label class="control-label" for="input01">What's the first part of your postcode?</label>
                                    <div class="controls">
                                        <input type="text" class="input-xlarge" id="postcode" placeholder="e.g., M5 or NW1">
                                        <span class="help-block">This helps to see if where you are in the country skews your perception of boundaries</span>
                                    </div>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                    <div id="loading-form">
                        <p>Getting your next location to place...</p>
                        <p id="javascript-warning">This site requires JavaScript to operate correctly, if this warning does not disappear than you may not have JavaScript enabled.</p>
                    </div>
                </div>
            </div>
        </div>
        <footer class="footer">
            <p>The software powering the site was written by <a href="http://www.pling.org.uk/">Chris Northwood</a> and is released <a href="https://github.com/cnorthwood/whereisthenorthreally">on Github</a> under <a href="https://github.com/cnorthwood/whereisthenorthreally/blob/master/LICENSE">the BSD license</a>.</p>
            <p>The locations are powered by <a href="http://www.geonames.org/">Geonames</a> under a <a href="http://creativecommons.org/licenses/by/3.0/">Creative Commons Attribution 3.0 License</a>.</p>
        </footer>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
    <script src="leaflet/leaflet.js" type="text/javascript"></script>
    <script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="whereisthenorthreally.js" type="text/javascript"></script>
</body>
</html>
