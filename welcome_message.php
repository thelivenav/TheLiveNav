<!DOCTYPE html>
<html lang="en">
<head>

<meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <!--<link href="/maps/documentation/javascript/examples/default.css" rel="stylesheet">
    <!--
    Include the maps javascript with sensor=true because this code is using a
    sensor (a GPS locator) to determine the user's location.
    See: https://developers.google.com/apis/maps/documentation/javascript/basics#SpecifyingSensor
    -->
    <script src="http://maps.googleapis.com/maps/api/js?sensor=false&amp;libraries=places"></script>
    <script src="http://seetheirwork.com/js/Fluster2.packed.js" type="text/javascript"></script>

    <script>
	var directionsDisplay;
var directionsService = new google.maps.DirectionsService();
var map;

function initialize() {
directionsDisplay = new google.maps.DirectionsRenderer();
  var mapOptions = {
    zoom:18,
	disableDefaultUI: true,
    mapTypeId: google.maps.MapTypeId.ROADMAP,
	zoomControl: true,
    zoomControlOptions: {
      style: google.maps.ZoomControlStyle.SMALL
    }
  };
  map = new google.maps.Map(document.getElementById('map-canvas'),
      mapOptions);
  var geocoder;
geocoder = new google.maps.Geocoder(); 

        var infowindow = new google.maps.InfoWindow();

        var input = document.getElementById('navAddr');

        var autocomplete = new google.maps.places.Autocomplete(input);

        google.maps.event.addListener(autocomplete, 'place_changed', function() {
          infowindow.close();
          var place = autocomplete.getPlace();


          

          var address = '';
          if (place.address_components) {
            address = [(place.address_components[0] &&
                        place.address_components[0].short_name || ''),
                       (place.address_components[1] &&
                        place.address_components[1].short_name || ''),
                       (place.address_components[2] &&
                        place.address_components[2].short_name || '')
                      ].join(' ');
          }

          infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
        });


  // Try HTML5 geolocation
  if(navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {

      var pos = new google.maps.LatLng(position.coords.latitude,
                                       position.coords.longitude);
	  var image = 'images/car-pin.png';
      var infowindow = new google.maps.Marker({
        map: map,
        position: pos,
        icon: image,
        draggable: true
      });

      map.setCenter(pos);
      map.setZoom(18);
      	// this initializes the dialog (and uses some common options that I do)
  $("#navigateTo").dialog({autoOpen : false, modal : true, show : "blind", hide : "blind",
  buttons: {
        "Navigate": function() {
            //run navigate function
			  if(navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {

      var pos = new google.maps.LatLng(position.coords.latitude,
                                       position.coords.longitude);

			directionsDisplay.setMap(map);
			var request = {

			origin:pos,
			destination: $("#navAddr").val(),
			travelMode: google.maps.TravelMode.DRIVING
		  };
		
		  directionsService.route(request, function(result, status) {
			if (status == google.maps.DirectionsStatus.OK) {
			  directionsDisplay.setDirections(result);
			  calculateDistances();
			}
		  });
            $("#navigateTo").dialog("close" );
            });
}
        },
        Cancel: function() {
          $("#navigateTo").dialog("close" );
        }
      },
      close: function() {
        //allFields.val( "" ).removeClass( "ui-state-error" );
      }
    });
	// initialize copyright info popup
	$("#copyrightInfo").dialog({autoOpen : false, modal : true, show : "blind", hide : "blind",
  buttons: {Close: function() {
          $("#copyrightInfo").dialog("close" );
        }},
  close: function() {
        //allFields.val( "" ).removeClass( "ui-state-error" );
      }
  });
  
  //update location func
       setInterval(function(){

  // Try HTML5 geolocation
  if(navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
      var pos = new google.maps.LatLng(position.coords.latitude,
                                       position.coords.longitude);

   		infowindow.setPosition(pos);

      map.setCenter(pos);
      if(position.coords.speed != null)
      {
      	var speed = position.coords.speed * 2.23693629;
      	speed = parseInt(speed);
      	if(speed >= 5)
      	{
	      $("#speed").html(speed + " Mph");
	      $("#speedo").show();
	  }
  }
  else
  {
  	$("#speedo").hide();
  }

		console.log("update");
		geocoder.geocode({
        'latLng': pos
    }, function(results, status) {
        document.getElementById("test").innerHTML = 'Approximate Address:' + (results[0].formatted_address);
    });
  });
    
}

  },2000);
    }, function() {
      handleNoGeolocation(true);
    });
  } else {
    // Browser doesn't support Geolocation
    handleNoGeolocation(false);
  }

 
}

function handleNoGeolocation(errorFlag) {
  if (errorFlag) {
    var content = 'Error: The Geolocation service failed.';
  } else {
    var content = 'Error: Your browser doesn\'t support geolocation.';
  }

  var options = {
    map: map,
    position: new google.maps.LatLng(60, 105),
    content: content
  };

  var infowindow = new google.maps.InfoWindow(options);
  map.setCenter(options.position);
}

google.maps.event.addDomListener(window, 'load', initialize);

// script works up to here

function calculateDistances() {
  var service = new google.maps.DistanceMatrixService();
  if(navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {

      	var pos = new google.maps.LatLng(position.coords.latitude,
                                       position.coords.longitude);
		
	service.getDistanceMatrix(
    {
      origins: [pos],
      destinations: [$("#navAddr").val()],
      travelMode: google.maps.TravelMode.DRIVING,
      unitSystem: google.maps.UnitSystem.IMPERIAL,
      avoidHighways: false,
      avoidTolls: false
    }, callback);
	
	 }); //end get position
	} //end of geolocation

}

function callback(response, status) {
  if (status != google.maps.DistanceMatrixStatus.OK) {
    alert('Error was: ' + status);
  } else {
    var origins = response.originAddresses;
    var destinations = response.destinationAddresses;
    var outputDiv = document.getElementById('distanceDiv');
    distanceDiv.innerHTML = '';
   // deleteOverlays();

    for (var i = 0; i < origins.length; i++) {
      var results = response.rows[i].elements;
      // addMarker(origins[i], false);
      for (var j = 0; j < results.length; j++) {
       // addMarker(destinations[j], true);
        // distanceDiv.innerHTML += origins[i] + ' to ' + destinations[j]
        distanceDiv.innerHTML += ' Distance to ' + destinations[j]
            + ': <br />' + results[j].distance.text + ' in '
            + results[j].duration.text;
      }
    }
  }
}	
</script>
<?php echo $library_src; ?>
<?php echo $script_head; ?>
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
	<meta charset="utf-8">
	<title>The Live Nav - Internet Based Navigation System</title>
	<meta name="description" content="An free Internet based navigation system.">
	<meta name="keywords" content="sat nav, satnav, navigation, web nav, internet navigation, mobile navigation, directions, gps nav">
	<meta name="author" content="Michael Gane">
	<link rel="icon" type="image/png" href="http://thelivenav.co.uk/favicon.png" />
	<style type="text/css">

	::selection{ background-color: #E13300; color: white; }
	::moz-selection{ background-color: #E13300; color: white; }
	::webkit-selection{ background-color: #E13300; color: white; }

	body {
		background-color: grey;
		font: 13px/20px normal Helvetica, Arial, sans-serif;
		color: #4F5155;
		margin: 0px;
		padding: 0px;
	}

	a {
		color: #003399;
		background-color: transparent;
		font-weight: normal;
	}

	h1 {
		color: white;
		background-color: transparent;
		font-size: 19px;
		font-weight: normal;

	}
	h2
	{
		color:white;
	}

	code {
		font-family: Consolas, Monaco, Courier New, Courier, monospace;
		font-size: 12px;
		background-color: #f9f9f9;
		color: #002166;
		display: block;
		margin: 14px 0 14px 0;
		padding: 12px 10px 12px 10px;
	}

	
	p.footer{
		text-align: right;
		font-size: 11px;
		line-height: 32px;
		padding: 0 10px 0 10px;
		margin: 20px 0 0 0;
		display:none;
	}

#footer {
   position:absolute;
   text-align: left;
position: fixed;
bottom: 0;
right:left;
   width:100%;
   height:50px;   /* Height of the footer */
   background:grey;
   	
}
	#map-page{

   min-height:100%;
   position:absolute;
}

#test
{
	width:60%;
	float:left;
	color:white;
	-webkit-box-sizing: border-box; /* Safari/Chrome, other WebKit */
	-moz-box-sizing: border-box;    /* Firefox, other Gecko */
	box-sizing: border-box;         /* Opera/IE 8+ */
	padding:30px;
}
#speedo
{
	width:10%;
	float:left;
}
#distance
{
	position:absolute;
	bottom:8px;
}
#map-page, #map-canvas {
	width: 100%;
	height: 100%;
	padding: 0; 
}
#menus div {
	display:inline;
	margin-left:2%;
	margin-right:2%;
}
#menus img {
	width:48px;
	height:48px;
}
#settings_info { border:0; }
#ui-tab-dialog-close { position:absolute; right:0; top:23px; }
#ui-tab-dialog-close a { float:none; padding:0; }
#settings_content div {
	text-align:center;
}
	</style>
</head>
<body>

<div id="map-page">
	
		<div id="map-canvas"></div>


<div id="footer">
	<div id="test">Loading Approximate Address</div>
	<div id="speedo">
		<h2>Speed:<br />
			<span id="speed"></span>
		</h2>
	</div>
	<div id="distance">
		<h2><br />
			<span id="distanceDiv"></span>
		</h2>
	</div>
		<div id="menus">
			<div id="trafficOff">
				<img src="images/traffic_lights_close.png" />
			</div>
			<div id="trafficOn">
				<img src="images/traffic_lights_checkmark.png" />
			</div>
			<div id="navigate">
				<img src="images/navigate.png" />
			</div>
			<div id="copyright">
				<img src="images/copyrightlogo.png" />
			</div>
			<div id="settingsicon">
				<img src="images/settings.png" />
			</div>
		</div>
	<p class="footer">Page rendered in <strong>{elapsed_time}</strong> seconds</p></div>

</div>
<div id="navigateTo" title="Destination Address" class="ui-dialog-content ui-widget-content">
    <p>
		<input type="text" id="navAddr" name="navAddr" />
	</p>
</div>
<div id="copyrightInfo" title="Copyright Information" class="ui-dialog-content ui-widget-content">
    <p>
		<a rel="license" href="http://creativecommons.org/licenses/by-sa/3.0/deed.en_US"><img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/by-sa/3.0/88x31.png" /></a><br /><span xmlns:dct="http://purl.org/dc/terms/" property="dct:title">TheLiveNav</span> by <a xmlns:cc="http://creativecommons.org/ns#" href="http://thelivenav.co.uk" property="cc:attributionName" rel="cc:attributionURL">Michael Gane</a> is licensed under a <a rel="license" href="http://creativecommons.org/licenses/by-sa/3.0/deed.en_US">Creative Commons Attribution-ShareAlike 3.0 Unported License</a>.<br />Based on a work at <a xmlns:dct="http://purl.org/dc/terms/" href="https://github.com/thelivenav" rel="dct:source">https://github.com/thelivenav</a>.
	</p>
</div>
<div id="settings" title="Options">
	<div id="settings_info">
		<ul id="settings_tabs">
			<li><a href="#settings_account">Account</a></li>
			<li><a href="#settings_donate">Donate</a></li>
			<li><a href="#settings_about">About</a></li>
			<li id="ui-tab-dialog-close"></li>
		</ul>
		<div id="settings_content">
			<div id="settings_account">
				<div id="SSI">Loading Sign In...</div>
			</div>
			<div id="settings_donate">
				<p>
				Help cover the costs of Hosting / Domains and help to cover the cost of high numbers of requests to the &copy; Google API by donating.
				</p>
				<p><form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
				<input type="hidden" name="cmd" value="_s-xclick">
				<input type="hidden" name="hosted_button_id" value="UWZ5TRZ5ATFKJ">
				<input type="image" src="https://www.paypalobjects.com/en_US/GB/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal – The safer, easier way to pay online.">
				<img alt="" border="0" src="https://www.paypalobjects.com/en_GB/i/scr/pixel.gif" width="1" height="1">
				</form></p>
			</div>
			<div id="settings_about">
				This is currently under development!
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function() {
	
	trafficLayer = new google.maps.TrafficLayer();
	$('#trafficOn').hide();
	//loadMap();

    $("#trafficOff").click(function() {
		$('#trafficOn').fadeIn('slow');
		$('#trafficOff').hide();
		trafficLayer.setMap(map);
	});
	$("#trafficOn").click(function() {
			$('#trafficOn').hide();
			$('#trafficOff').fadeIn('slow');
			trafficLayer.setMap(null);
	});
	
	//add social sign in
	$('#SSI').load('index.php/user/dashboard');

  // next add the onclick handler
  $("#navigate").click(function() {
    $("#navigateTo").dialog("open");
    return false;
  });
  $("#copyright").click(function() {
    $("#copyrightInfo").dialog("open");
    return false;
  });
  $("#settingsicon").click(function() {
    $("#settings").dialog("open");
    return false;
  });
  
  //tabbed settings js
  $('#settings_info').tabs();
	$('#settings').dialog({ autoOpen : false,
							modal : true,
							show : "blind",
							hide : "blind",
						   'width':600, 'height':400, 
						   'minWidth':600, 'minHeight':300, 
						   'draggable':true
	});
	//steal the close button
	$('#ui-tab-dialog-close').append($('a.ui-dialog-titlebar-close'));
			
	//move the tabs out of the content and make them draggable
	$('.ui-dialog').addClass('ui-tabs')
					.prepend($('#settings_tabs'))
					.draggable('option','handle','#settings_tabs'); 
			
	//switch the titlebar class
	$('.ui-dialog-titlebar').remove();
	$('#settings_tabs').addClass('ui-dialog-titlebar');

});
</script>
</body>
</html>