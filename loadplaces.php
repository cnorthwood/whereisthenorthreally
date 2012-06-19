<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<?php die(); ?>
<html>
<head>
    <title>Map</title>
    <link rel="stylesheet" href="leaflet/leaflet.css" type="text/css">
</head>

<body>
    <div style="height: 400px; width: 600px;" id="map"></div>
    <script src="leaflet/leaflet.js" type="text/javascript"></script>
    <script type="text/javascript">
        var map = new L.Map('map');
        var mapTiles = new L.TileLayer('http://otile1.mqcdn.com/tiles/1.0.0/osm/{z}/{x}/{y}.png', {
            attribution: 'Data, imagery and map information provided by MapQuest, <a href="http://openstreetmap.org/">Open Street Map</a> and contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>',
            maxZoom: 18
        });
        map.addLayer(mapTiles);
        map.setView(new L.LatLng(52, -1), 6);
    <?php
        error_reporting(E_ALL);
        ini_set('display_errors', 'true');
        require('config.inc.php');
        $db = new mysqli(null, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
        $handle = fopen("place_locations.csv", "r");
        while (($data = fgetcsv($handle)) !== FALSE) {
            $query = $db->prepare("INSERT INTO places(name,lat,lon) VALUES(?,?,?)");
            $query->bind_param('sdd', $data[0], $data[1], $data[2]);
            $query->execute();
    ?>
            map.addLayer(new L.Marker(new L.LatLng(<?php echo $data[1]; ?>,<?php echo $data[2]; ?>)));
    <?php
        }
    ?>
    </script>
</body>
</html>
