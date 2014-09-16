<div id="IIRS_0_debug"><pre>
debug output:
<?php
require_once('framework_abstraction_layer.php');
require_once('utility.php');
require_once('environment.php');
require_once('inputs.php');

//-------------------------------------------------------------------- check input type: domain or town name
$is_domain   = false;
$towns_found = false;

if (!$townname) {
  print("no inputs\n");
  IIRS_0_set_message_translated("no inputs", $IIRS_widget_mode);
} else {
  if (strchr($townname, '.')) {
    //clean potential domain name and check it for TLD on end
    $domain = trim($townname);
    $domain = preg_replace('/^(https?:\/\/)?(www\.)?([^\/?]*).*/i', '$3', $domain);

    if ($domain && !strchr($domain, ' ')) {
      $effective_tld_names = file_get_contents(__DIR__ . '/effective_tld_names.dat.txt');
      $aAllFileEntries     = explode("\n", $effective_tld_names);
      print("check potential domain string [$domain] against [" . count($aAllFileEntries) . "] TLDs:\n");
      foreach ($aAllFileEntries as $sEntry) {
        if (strlen($sEntry) && substr($sEntry, 0, 2) != '//') {
          if (substr($domain, -(strlen($sEntry) + 1)) == ".$sEntry") {
            $is_domain = true;
            print("[$domain] ends with [$sEntry]\n");
            break;
          }
        }
      }
    }
  }

  //------------------------------------------------------------------------- process town name
  if($is_domain) {
    IIRS_0_set_message_translated('this looks like a domain (website address), you need to enter a town or area name instead', $IIRS_widget_mode);
  } else {
    print("not a domain, treating as a town name\n");
    //open map town lookup and options
    //there is potential here for querying several providers
    //for example: Google processes PostCodes, whilst OSM doesnt
    //we have the option of running our own OSM server to allow more lookups
    $mappingProvider = "Google";
    $format          = 'xml';
    $aPlaces         = array();
    print("looking up [$townname] on [$mappingProvider]\n");

    $allow_url_fopen = ini_get('allow_url_fopen');
    if (!$allow_url_fopen) ini_set('allow_url_fopen', 1);

    if ($mappingProvider == "OpenStreetMap") { //--------------
      //OSM has a usage limit, use of Nominatim is discouraged...
      //403 Forbidden codes can come through if you over use the OSM API
      //http://wiki.openstreetmap.org/wiki/Nominatim_usage_policy

      //not using Services_Openstreetmap module because it requires PEAR HTTP_Request2
      //http://wiki.openstreetmap.org/wiki/API
      //https://github.com/kenguest/Services_Openstreetmap/tree/master/examples
      //$osm = new Services_OpenStreetMap();
      //var_dump($osm->getPlace($townname));

      //http://wiki.openstreetmap.org/wiki/Nominatim#Parameters
      if ($is_example) $xml = file_get_contents("$IIRS_common_dir/registration/$mappingProvider.$format");
      else             $xml = IIRS_0_http_request("http://nominatim.openstreetmap.org/search?q=$townname&format=$format&polygon_kml=1&addressdetails=1");
      $doc = new DOMDocument();
      $doc->loadXML($xml);

      //translate the results in to a standardised array
      //TODO: use XShema
      if (!$doc->documentElement) {
        //TODO: what invalid XML returned?
        IIRS_0_set_message_translated("invalid XML returned: $xml\n", $IIRS_widget_mode);
      } elseif (!$doc->documentElement->childNodes) {
        //TODO: what invalid XML returned?
        IIRS_0_set_message_translated("invalid XML returned: $xml\n", $IIRS_widget_mode);
      } else {
        //handle no results
        if ($doc->documentElement->childNodes->length == 0) {
          print("no results for lookup\n");
        } else {
          //get places info into an associative array
          $placeNodes = $doc->documentElement->childNodes;
          foreach ($placeNodes as $placeNode) {
            if ($placeNode->nodeType == XML_ELEMENT_NODE && $placeNode->nodeName == 'place') {
              $aPlace = array();

              //assemble place description
              $description = '';
              foreach ($placeNode->childNodes as $placeValueNode) {
                $sPlaceKey = $placeValueNode->nodeName;
                if ( $sPlaceKey != 'geokml'
                  && $sPlaceKey != 'country_code'
                  && $sPlaceKey != 'station'
                ) {
                  $sPlaceValue = trim($placeValueNode->nodeValue);
                  if ($sPlaceValue) $description .= "$sPlaceValue, ";
                }
              }
              if ($description) $description = substr($description, 0, -2);

              //entry and add in to the array
              $aPlace['description'] = $description;
              //TODO: calculate the centre point from the geoxml
              //we can do this *if* we choose to use OSM again...
              $aPlace['centre_lat']  = '52';
              $aPlace['centre_lng']  = '0';
              $aPlaces[] = $aPlace;
            }
          }
        }
      }
    }

    elseif ($mappingProvider == "Google") { //--------------
      //http://maps.google.com/maps/api/geocode/xml?sensor=false&address=bedford
      $townname_encoded = urlencode($townname);
      $url_request      = "http://maps.google.com/maps/api/geocode/$format?sensor=false&address=$townname_encoded";
      print($url_request);
      if ($is_example) $xml = file_get_contents("$IIRS_common_dir/registration/$mappingProvider.$format");
      else             $xml = IIRS_0_http_request($url_request, 5.0, TRUE);

      //DOMDocument and DOMXpath (PHP5)
      //PHP >= 5 is checked for in the installation procedure (IIRS.install)
      $doc    = new DOMDocument();
      $doc->loadXML($xml);
      $oXPath = new DOMXpath($doc);

      //translate the results in to a standardised array
      if (!$doc->documentElement) {
        //TODO: what invalid XML returned?
        IIRS_0_set_message_translated("invalid XML returned: $xml\n", $IIRS_widget_mode);
      } elseif (!$doc->documentElement->childNodes) {
        //TODO: what invalid XML returned?
        IIRS_0_set_message_translated("invalid XML returned: $xml\n", $IIRS_widget_mode);
      } else {
        //handle no results
        var_dump($xml);
        if ($doc->documentElement->childNodes->length == 0) {
          print("no results for lookup\n");
        } else {
          //get places info into an associative array
          $placeNodes = $doc->documentElement->childNodes;
          foreach ($placeNodes as $placeNode) {
            if ($placeNode->nodeType == XML_ELEMENT_NODE && $placeNode->nodeName == 'result') {
              $aPlace = array();

              //entry and add in to the array
              //basic values
              $aPlace['description']  = getDOMValue($placeNode, 'formatted_address');
              $aPlace['centre_lat']   = getDOMValue($placeNode, 'geometry/location/lat');
              $aPlace['centre_lng']   = getDOMValue($placeNode, 'geometry/location/lng');
              $aPlace['granuality']   = getDOMValue($placeNode, 'type');

              //full address
              $fullAddress            = '';
              $nodeList               = $oXPath->query('address_component', $placeNode);
              foreach ($nodeList as $address_component) {
                $address_level = getDOMValue($address_component, "type[1]");
                $address_name  = getDOMValue($address_component, 'long_name');
                $fullAddress  .= $address_name . ', ';
                if ($address_level == 'country') $aPlace['country'] = $address_name;
              }
              if ($fullAddress) $fullAddress = substr($fullAddress, 0, -2);
              $aPlace['full_address'] = $fullAddress;

              //bounds
              $northeast              = getDOMValue($placeNode, 'geometry/bounds/northeast/lat') . ',' . getDOMValue($placeNode, 'geometry/bounds/northeast/lng');
              $southwest              = getDOMValue($placeNode, 'geometry/bounds/southwest/lat') . ',' . getDOMValue($placeNode, 'geometry/bounds/southwest/lng');
              $aPlace['bounds']       = "$northeast;$southwest";

              $aPlaces[] = $aPlace;
            }
          }
        }
      }

    } else { //oops, not a valid provider
      print("mapping provider [$mappingProvider] not valid, please set one");
      exit(1);
    }
    if (!$allow_url_fopen) ini_set('allow_url_fopen', 0);

    //-------------- output
    var_dump($aPlaces);
    $option        = 1;
    $place_uniques = array();
    $place_options = '';

    foreach ($aPlaces as $aPlace) {
      $place_description  = $aPlace['description'];
      $place_centre_lat   = $aPlace['centre_lat'];
      $place_centre_lng   = $aPlace['centre_lng'];
      $place_full_address = $aPlace['full_address'];
      $place_country      = $aPlace['country'];
      $place_granuality   = $aPlace['granuality'];
      $place_bounds       = $aPlace['bounds'];

      //unique identity output
      $unique_id = "$place_centre_lat,$place_centre_lng";
      if (!$unique_id || !isset($place_uniques[$unique_id])) {
        $sGoogleMapURL  = "https://www.google.com/maps/@$place_centre_lat,$place_centre_lng,16z";
        $sGoogleMapLink = '<a target="_blank" href="' . $sGoogleMapURL . '\">' . IIRS_0_translation('view on map') . '</a>';
        $selected       = ($option == 1 ? 'checked="1"' : '');
        $selected_class = ($option == 1 ? 'selected' : '');
        $place_value_serialised = urlencode(serialize($aPlace));

        //status, description, links
        if (IIRS_0_TI_isRegistered($townname, $place_centre_lat, $place_centre_lng, $place_description)) {
          $place_status  =  IIRS_0_translation('transition initiative already registered') . " $sGoogleMapLink<br/>";
          $place_status .= '<input class="IIRS_0_button" type="button" value="' . IIRS_0_translation('join!') . '"/>';
          $place_status .= '<input class="IIRS_0_button" type="button" value="' . IIRS_0_translation('message') . '"/>';
          $disabled      = 'disabled="1"';
        } else {
          $place_status  = IIRS_0_translation("transition initiative not registered yet!") . " $sGoogleMapLink<br/>";
          //$place_status .= IIRS_0_translation('closest initiative') . ': 5' . IIRS_0_translation('km');
          $disabled      = '';
        }

        //HTML assemble
        $place_output   = <<<"HTML"
          <li class="$selected_class">
            <input $disabled $selected name="place" class="IIRS_0_radio" value="$place_value_serialised" type="radio" id="IIRS_0_place_{$option}_input" />
            <label for="IIRS_0_place_{$option}_input">
              $place_description
              <div class="IIRS_0_full_address">$place_granuality: $place_full_address</div>
              <div class="IIRS_0_status">$place_status</div>
            </label>
          </li>
HTML;
        $place_options .= $place_output;

        //record for uniqueness
        if ($unique_id) $place_uniques[$unique_id] = 1;
        $option++;
      }
    }
    if ($option != 1) $towns_found = true;
  }
}
?>
</pre></div>

<style>
  .IIRS_0_full_address {
    color:#aaaaaa;
    font-size:11px;
    font-weight:normal;
  }

  /* ---------------------------------------------------------------- details entry layout */
  #IIRS_0_details, #IIRS_0_details tr, #IIRS_0_details td {
    border:none;
  }
  #IIRS_0_details {
    width:300px;
    float:left;
  }
  #IIRS_0_details_teaser {
    clear:both;
  }
  #IIRS_0_details_teaser_img {
    float:right;
    width:198px;
    padding:2px;
  }
  #IIRS_0_details td {
    white-space:nowrap;
  }
</style>


<?php //------------------------------------------------------------- HTML ?>
<div id="IIRS_0" class="IIRS_0_location_general">
  <div class="IIRS_0_h1" id="IIRS_0_popup_title"><?php print(IIRS_0_translation('connection of') . " $townname " . IIRS_0_translation('to the support and innovation network')); ?> </div>
  <form method="POST" id="IIRS_0_form_popup_location_general" action="domain_selection" class="IIRS_0_clear IIRS_0_formPopupNavigate"><div>
    <?php IIRS_0_printEncodedPostParameters(); ?>

    <h3><?php IIRS_0_print_translation('town matches'); ?></h3>
    <ul id="IIRS_0_list_selector">
      <?php if (!$towns_found) { ?>
        <li class="IIRS_0_place IIRS_0_message">
          <img src="<?php print("$IIRSURLImageStem/information"); ?>" />
          <?php print(IIRS_0_translation('no towns found matching') . " $townname " . '<br/>' . IIRS_0_translation('you will need to') . ' <a href="mailto:annesley_newholm@yahoo.it">' . IIRS_0_translation('register by email') . '</a> ' . IIRS_0_translation('because we cannot find your town on our maps system!')); ?>
        </li>
      <?php } ?>
      <?php print($place_options); ?>
      <li id="IIRS_0_other" class="IIRS_0_place">
        <?php IIRS_0_print_translation('other'); ?>:
        <input id="IIRS_0_research_townname_new" value="<?php if ($townname) print($townname); ?>" />
        <input id="IIRS_0_research" type="button" value="<?php IIRS_0_print_translation('search again'); ?>" />
      </li>
    </ul>

    <?php if ($offerBuyDomains && isset($nice_domains_html)) { ?>
    <h3 class="IIRS_0_horizontal_section"><?php IIRS_0_print_translation('domains to consider: are they yours?'); ?></h3>
    <ul id="IIRS_0_nice_domains">
      <?php print($nice_domains_html); ?>
    </ul>
    <ul id="IIRS_0_domain_setup_options">
      <li><input id="IIRS_0_domain_setup_worpress" name="domain_setup" type="radio" />         <label for="IIRS_0_domain_setup_worpress"><?php print(IIRS_0_translation('load') . ' <a href="http://wordpress.org" target="_blank">Wordpress</a> ' . IIRS_0_translation('on to this domain and give me the keys')); ?></label></li>
      <li><input id="IIRS_0_domain_setup_drupal" name="domain_setup" type="radio" />           <label for="IIRS_0_domain_setup_drupal"><?php print(IIRS_0_translation('load') . ' <a href="http://drupal.org" target="_blank">Drupal</a> ' . IIRS_0_translation('on to this domain and give me the keys')); ?></label></li>
      <li><input id="IIRS_0_domain_setup_none" checked="1" name="domain_setup" type="radio" /> <label for="IIRS_0_domain_setup_none"><?php IIRS_0_print_translation('stop being clever and just give me the domains'); ?></label></li>
    </ul>
    <input id="IIRS_0_buydomains" class="IIRS_0_bigbutton" disabled="1" type="button" value="<?php IIRS_0_print_translation('buy marked domains'); ?>" />
    <?php } ?>

    <h3 class="IIRS_0_horizontal_section"><?php IIRS_0_print_translation('some general details'); ?></h3>
    <img id="IIRS_0_details_teaser_img" src="<?php print("$IIRSURLImageStem/network_paper"); ?>" />
    <table id="IIRS_0_details">
      <tr><td><?php IIRS_0_print_translation('initiative name'); ?></td><td><input id="IIRS_0_initiative_name" class="IIRS_0_required" name="initiative_name" value="<?php print($townname); ?>" /> transition town<span class="required">*</span></td></tr>
      <tr><td><?php IIRS_0_print_translation('email'); ?></td><td><input id="IIRS_0_email" class="IIRS_0_required" name="email" /><span class="required">*</span></td></tr>
      <tr><td><?php IIRS_0_print_translation('your name'); ?></td><td><input id="IIRS_0_name" class="IIRS_0_required" name="name" /><span class="required">*</span></td></tr>
      <!-- NOTE: are we going to ring them? place this later on in the forms -->
      <!-- tr><td><?php IIRS_0_print_translation('phone number'); ?><br/>(<?php IIRS_0_print_translation('optional'); ?>)</td><td><input name="phone" /></td></tr -->
    </table>
    <div id="IIRS_0_details_teaser">
      <?php IIRS_0_print_translation('registering your email means that local people will contact you to offer support and for your opinion on projects like
      food growing, energy supply and other Transition ideals. we will let Transition Brixton (your nearest advanced Town)
      know you have registered so they can connect, support, encourage and share! :)'); ?>
    </div>

    <br class="IIRS_0_clear" />
    <input class="IIRS_0_bigbutton IIRS_0_back" type="button" value="&lt;&lt; <?php IIRS_0_print_translation('change search'); ?>" />
    <input class="IIRS_0_bigbutton" type="submit" value="<?php IIRS_0_print_translation('complete registration'); ?> &gt;&gt;" />
    <?php IIRS_0_print_translation('and then connect with local Transition Initiatives :)'); ?>
  </div></form>
</div>
