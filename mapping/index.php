<div id="IIRS_0_debug"><pre>
debug output:
<?php
global $debug_environment;
require_once('framework_abstraction_layer.php');
require_once('utility.php');
require_once('environment.php');
print($debug_environment);
?>
</pre></div>

<script type="text/javascript">
  //TODO: json_encode() is PHP 5 >= 5.2.0
  //TODO: what does json_encode() do with null?
  var aAllTIs = <?php print(json_encode(IIRS_0_TIs_all())); ?>;
</script>

<div id="IIRS_0">
  <style>
  #map-canvas {
    width:100%;
    height:300px;
  }
  </style>

  <script type="text/javascript">
    var map, mapOptions;
    <?php IIRS_0_print_javascript_variable('sGoogleAPIKey', $GoogleAPIKey); ?>

    jQuery(document).ready(function(e){
      var script   = document.createElement("script");
      var callback = "IIRS_initialize_map";
      script.type  = "text/javascript";
      script.src   = "https://maps.googleapis.com/maps/api/js?key=" + sGoogleAPIKey + "&callback=" + callback;
      document.body.appendChild(script);
    });

    function IIRS_initialize_map() {
      var oTI;

      mapOptions = {
        center: new google.maps.LatLng(<?php print("$defaultLat, $defaultLng"); ?>),
        zoom: 8
      };
      map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);

      if (aAllTIs) {
        for (var i = 0; i < aAllTIs.length; i++) {
          oTI    = aAllTIs[i];
          sImage = g_sIIRSURLImageStem + '/' + (oTI.status == 'official' ? 'official' : 'muller') + '.png';
          var marker = new google.maps.Marker({
            position: new google.maps.LatLng(oTI.location_latitude, oTI.location_longitude),
            map: map,
            title: oTI.name,
            icon: sImage
          });
          var infowindow = new google.maps.InfoWindow({
            content: '<div id="content"><b>' + oTI.name + '</b><br/><p>' + oTI.summary + '</p></div>'
          });
          google.maps.event.addListener(marker, 'click', function() {
            infowindow.open(map,marker);
          });
        }
      }
    }
  </script>

  <div id="IIRS_0_no_javascript" class="IIRS_0_warning">
    <?php IIRS_0_print_translation('oops, Javascript failed to run, services unavailable, please go to'); ?>
    &nbsp;<a href="http://transitionnetwork.org/">Transition Network</a>&nbsp;
    <?php IIRS_0_print_translation('to register instead'); ?>
  </div>

  <!-- intial form -->
  <div class="IIRS_0_h1"><?php IIRS_0_print_translation('mappings of transition towns around the world'); ?>
    <?php printLanguageSelector(); ?>
  </div>
  <div id="map-canvas">map loading...</div>
</div>