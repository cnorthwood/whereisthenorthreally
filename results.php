<!DOCTYPE html>
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
    <link rel="stylesheet" href="tablesorter/style.css" />
    <link rel="stylesheet" href="whereisthenorthreally.css" />
</head>

<body>
    <div class="container">
        <div class="page-header">
            <h1>So where's the North, really? <small>And what exactly is the Midlands?</small></h1>
            <p>A crowd-sourcing experiment to see where people think the geographic boundaries of England lay</p>
        </div>

        <div class="row">
            <div class="span6 hidden-phone" id="map"></div>
            <div class="span6">
                <table class="table table-bordered sortable">
                    <thead>
                        <th>Place</th>
                        <th>North</th>
                        <th>Midlands</th>
                        <th>South</th>
                        <th>Didn't know</th>
                    </thead>
                    <tbody>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 'true');
$db = new mysqli($_SERVER['DB_HOST'], $_SERVER['DB_USERNAME'], $_SERVER['DB_PASSWORD'], $_SERVER['DB_DATABASE']);
$query = $db->prepare("SELECT places.name, places.lat, places.lon, results.choice, count(*) FROM places,results WHERE places.placeId = results.placeId GROUP BY places.name,results.choice");
$query->execute();
$query->bind_result($name, $lat, $lon, $choice, $count);

$results = array();
while ($query->fetch()) {
    if (!array_key_exists($name, $results)) {
        $results[$name] = array(
            'lat' => $lat,
            'lon' => $lon,
            'north' => 0,
            'midlands' => 0,
            'south' => 0,
            'dunno' => 0
        );
    }
    $results[$name][$choice] = $count;
}
foreach ($results as $name => $choices) {
    $choices['total'] = $choices['north'] + $choices['midlands'] + $choices['south'] + $choices['dunno'];
    $results[$name]['northpercent'] = $choices['north'] / $choices['total'];
    $results[$name]['midlandspercent'] = $choices['midlands'] / $choices['total'];
    $results[$name]['southpercent'] = $choices['south'] / $choices['total'];
    $choices['dunnopercent'] = $choices['dunno'] / $choices['total'];
    $results[$name]['colour'] = sprintf("#%02x%02x%02x", $results[$name]['northpercent'] * 255, $results[$name]['midlandspercent'] * 255, $results[$name]['southpercent'] * 255);
?>
                        <tr style="background-color: <?php echo $results[$name]['colour']; ?>">
                            <td><?php echo $name; ?></td>
                            <td><?php echo round($results[$name]['northpercent'] * 100); ?>%</td>
                            <td><?php echo round($results[$name]['midlandspercent'] * 100); ?>%</td>
                            <td><?php echo round($results[$name]['southpercent'] * 100) ?>%</td>
                            <td><?php echo round($choices['dunnopercent'] * 100); ?>%</td>
                        </tr>
<?php
}
?>
                    </tbody>
                </table>
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
    <script src="tablesorter/jquery.tablesorter.min.js" type="text/javascript"></script>
    <script type="text/javascript">
        $(function(){
            var map = new L.Map('map', {
                minZoom: 6
            });
            var mapTiles = new L.TileLayer('http://otile1.mqcdn.com/tiles/1.0.0/osm/{z}/{x}/{y}.png', {
                attribution: 'Data, imagery and map information provided by MapQuest, <a href="http://openstreetmap.org/">Open Street Map</a> and contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>',
                maxZoom: 18
            });
            map.addLayer(mapTiles);
            map.setView(new L.LatLng(52.7, -1), 6);
            <?php
            foreach ($results as $name => $choices) {
            ?>
                map.addLayer(new L.CircleMarker(new L.LatLng(<?php echo json_encode($choices['lat']); ?>, <?php echo json_encode($choices['lon']); ?>), {
                    fillColor: <?php echo json_encode($choices['colour']); ?>
                }).bindPopup('<h2><?php echo addslashes(htmlspecialchars($name)); ?></h2><dl class="dl-horizontal"><dt>North</dt><dd><?php echo round($choices['northpercent'] * 100); ?>%</dd><dt>Midlands</dt><dd><?php echo round($choices['midlandspercent'] * 100); ?>%</dd><dt>South</dt><dd><?php echo round($choices['southpercent'] * 100); ?>%</dd></dl>'));
            <?php } ?>
            $('.sortable').tablesorter();
        });
    </script>
</body>
</html>
