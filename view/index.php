<div id="IIRS_0_debug"><pre>
debug output:
<?php
/* Manual Initiative Profile view page
 * NOTE: this should NOT normally be used
 * The framework system should show the posts / nodes / whatevers natively
 * thus using the local templating system and all fitting in rather nicely
 * use that page instead and override the edit function
 *
 * Redirect all TI editing to /IIRS/edit to prevent users from going in to the host framework editing suite
 */

global $debug_environment;
require_once('framework_abstraction_layer.php');
require_once('utility.php');
require_once('environment.php');
print($debug_environment);

//need to get a specific TI in this view
$TI = IIRS_0_details_TI_user();

$website = (empty($TI['domain']) ? 'currently no website' : '<a target="_blank" href="http://' . $TI['domain'] . '">website</a>');
?>
</pre></div>

<script type="text/javascript">
  //TODO: json_encode() is PHP 5 >= 5.2.0
  //TODO: what does json_encode() do with null?
  //var oTI = <?php print(json_encode($TI)); ?>;
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
    <?php IIRS_0_print_javascript_variable('sGoogleAPIKey', $google_API_key); ?>

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

  <div class="IIRS_0_h1"><?php print($TI['name']); ?>
    <?php IIRS_0_print_language_selector(); ?>
  </div>
  <div class="IIRS_0_website"><?php print($website); ?></div>
  <p class="IIRS_0_summary"><?php print($TI['summary']); ?></p>
  <!-- div id="map-canvas">map loading...</div -->
</div>