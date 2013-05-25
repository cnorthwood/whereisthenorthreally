$(WhereIsTheNorthReally);

function WhereIsTheNorthReally() {

    var last_results = [];
    
    var map = new L.Map('map', {
        minZoom: 6,
        maxZoom: 8,
        zoomControl: false,
        doubleClickZoom: false,
        scrollWheelZoom: false,
        touchZoom: false,
        dragging: false
    });
    var mapTiles = new L.TileLayer('http://otile1.mqcdn.com/tiles/1.0.0/osm/{z}/{x}/{y}.png', {
        attribution: 'Data, imagery and map information provided by MapQuest, <a href="http://openstreetmap.org/">Open Street Map</a> and contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>',
        maxZoom: 18
    });
    map.addLayer(mapTiles);
    map.setView(new L.LatLng(52, -1), 6);
    
    var placeId = null;
    var marker = null;
    $('#javascript-warning').hide();
    
    function updateLocation(data) {
        $('#loading-form').hide();
        $('#submission-form').show();
        var newLocation = new L.LatLng(data.lat, data.lon);
        
        if (marker !== null) {
            map.removeLayer(marker);
        }
        marker = new L.Marker(newLocation);
        map.addLayer(marker);
        map.panTo(newLocation);
        map.setZoom(8);
        placeId = data.id;
        
        $("#placename").text(data.name);
        
        if (data.lastLocation) {
            switch (data.lastSubmission) {
                case "south":
                    var message = "So you think " + data.lastLocation + " is down south? Interesting...";
                    var message_class = "success";
                    break;
                case "midlands":
                    var message = data.lastLocation + " is in the Midlands? Okay";
                    var message_class = "success";
                    break;
                case "north":
                    var message = data.lastLocation + " lives up North? I've made a note of that";
                    var message_class = "success";
                    break;
                case "dunno":
                    var message = "Don't know where " + data.lastLocation + " is? Let's try another one";
                    var message_class = "info";
                    break;
            }
            showResults(message_class, message, data.agreement);
            last_results.push({place: data.lastLocation, choice: data.lastSubmission, agreement: data.agreement});
        }
    }
    
    function submit(choice) {
        $('#submission-form').hide();
        $('#loading-form').show();
        $.ajax({
            type: "POST",
            url: "process.php",
            data: {
                placeId: placeId,
                postcode: $('#postcode').val(),
                choice: choice,
                csrftoken: csrftoken
            },
            success: updateLocation,
            error: function() {
                $('#loading-form').hide();
                $('#submission-form').show();
                showResults("error", "Oh dear, looks like something went wrong and your last submission didn't get saved");
            },
            dataType: "json"
        });
    }
    
    function showResults(message_class, message) {
        $("#results-message").removeClass("alert-success alert-info alert-error")
        $("#results-message").addClass("alert-" + message_class);
        $("#results-message").text(message);
        $("#results-message").show();
    }
    
    $('#north').click(function(e){
        e.preventDefault();
        submit("north");
    });
    $('#midlands').click(function(e){
        e.preventDefault();
        submit("midlands");
    });
    $('#south').click(function(e){
        e.preventDefault();
        submit("south");
    });
    $('#dunno').click(function(e){
        e.preventDefault();
        submit("dunno");
    });
    
    $.ajax({
        type: "GET",
        url: "process.php",
        success: updateLocation,
        dataType: "json"
    });
}
