<div id="IIRS_0_debug"><pre>
debug output:
<?php
global $debug_environment;
require_once('framework_abstraction_layer.php');
require_once('utility.php');
require_once('environment.php');
print($debug_environment);

//need to get a specific TI in this view
$aTI = IIRS_0_detailsTI_page();

$website = (empty($aTI['domain']) ? 'currently no website' : '<a target="_blank" href="http://' . $aTI['domain'] . '">website</a>');
?>
</pre></div>

<script type="text/javascript">
  //TODO: json_encode() is PHP 5 >= 5.2.0
  //TODO: what does json_encode() do with null?
  //var oTI = <?php print(json_encode($aTI)); ?>;
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

    //only show maps if there is an object to show
    if (oTI) {
      jQuery(document).ready(function(e){
        var script   = document.createElement("script");
        var callback = "IIRS_initialize_map";
        script.type  = "text/javascript";
        script.src   = "https://maps.googleapis.com/maps/api/js?key=" + sGoogleAPIKey + "&callback=" + callback;
        document.body.appendChild(script);
      });
    }

    function IIRS_initialize_map() {
      mapOptions = {
        center: new google.maps.LatLng(oTI.location_latitude, oTI.location_longitude),
        zoom: 8
      };
      map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);

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
  </script>

  <div class="IIRS_0_h1"><?php print($aTI['name']); ?>
    <?php printLanguageSelector(); ?>
  </div>
  <div class="IIRS_0_website"><?php print($website); ?></div>
  <p class="IIRS_0_summary"><?php print($aTI['summary']); ?></p>
  <!-- div id="map-canvas">map loading...</div -->
</div>