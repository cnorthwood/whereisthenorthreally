$(WhereIsTheNorthReally);

function WhereIsTheNorthReally() {

    var submissions = 0;
    
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

    var resultsMap = new L.Map('results-map', {
        minZoom: 6
    });
    var resultsMapTiles = new L.TileLayer('http://otile1.mqcdn.com/tiles/1.0.0/osm/{z}/{x}/{y}.png', {
        attribution: 'Data, imagery and map information provided by MapQuest, <a href="http://openstreetmap.org/">Open Street Map</a> and contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>',
        maxZoom: 18
    });
    resultsMap.addLayer(resultsMapTiles);
    resultsMap.setView(new L.LatLng(52.7, -1), 6);
    var resultsMarkers = [];
    
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
            submissions++;
            showResults(message_class, message, data);
        }
    }
    
    function submit(choice) {
        var colour;
        switch (choice) {
            case "south":
                colour = '#0000cc';
                break;
            case "midlands":
                colour = '#00cc00';
                break;
            case "north":
                colour = '#cc0000';
                break;
            case "dunno":
                colour = '#999999';
                break;
        }

        var resultsMarker = new L.CircleMarker(marker.getLatLng(), {fillColor: colour});
        resultsMap.addLayer(resultsMarker);
        resultsMarkers.push(resultsMarker);

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
    
    function showResults(message_class, message, data) {
        $("#results-message").removeClass("alert-success alert-info alert-error");
        $("#results-message").addClass("alert-" + message_class);
        $("#results-message").text(message);
        $("#results-message").show();
        if (data.lastSubmission != 'dunno') {
            $('.quizresults tbody').append('<tr><td>' + data.lastLocation + '</td><td>' + data.lastSubmission + '</td><td>' + Math.round(parseFloat(data.agreement) * 100) + '% of people agree with you</td></tr>');
        }

        if (submissions % 10 == 0) {
            $('.quizbody').hide();
            $('.quizresults').fadeIn(function() {
                resultsMap.invalidateSize();
            });
        }
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
    $('#continuequiz').click(function(e){
        e.preventDefault();
        $('.quizresults').fadeOut(function() {
            $('.quizbody').show();
            $('.quizresults tbody').empty();
        });
        for (var i = 0; i < resultsMarkers.length; ++i) {
            resultsMap.removeLayer(resultsMarkers[i]);
        }
    });

    $('.map-switch').on('switch-change', function() {
        $('.quizbody').toggleClass('span12');
        $('.quizbody').toggleClass('span6');
        $('#map').toggle();
        map.invalidateSize();
    });
    
    $.ajax({
        type: "GET",
        url: "process.php",
        success: updateLocation,
        dataType: "json"
    });
}
