<?php
/* Copyright 2015, 2016 Transition Network ltd
 * This program is distributed under the terms of the GNU General Public License
 * as detailed in the COPYING file included in the root of this plugin
 */
?>

<?php
/* normalised array return format:
    $location_array['description']
    $location_array['latitude']
    $location_array['longitude']
    $location_array['granuality']
    $location_array['country']
    $location_array['full_address']
    $location_array['bounds']
*/

function IIRS_0_geocode_notice() {
  print( '<div class="IIRS_0_data_license">Geocoding Courtesy of <a href="http://www.mapquest.com/" target="_blank">MapQuest</a> <img src="http://developer.mapquest.com/content/osm/mq_logo.png"></div>' );
}

function IIRS_0_geocode( $town_name, $format = 'xml' ) {
  global $location_is_example, $IIRS_host_TLD, $IIRS_common_dir;

  $location_arrays = array();

  // http://open.mapquestapi.com/geocoding/
  // see also http://en.wikipedia.org/wiki/Country_code_top-level_domain
  //   for region bias codes
  // NOTE: the country bias DOES NOT work for MapQuest. we are using a calculated bounding box instead
  //   boundingBox=61.06,-14.02,49.67,2.09
  $bounds_param      = '';
  $region_bias       = IIRS_0_setting( 'region_bias' );
  if ( ! $region_bias || $region_bias == 'region_bias' ) $region_bias = $IIRS_host_TLD;
  if ( $region_bias == 'dev' || $region_bias == 'org' ) $region_bias = 'uk';
  if ( $bounds_details = IIRS_0_tld_bounds( $region_bias ) ) {
    $bounds_min     = $bounds_details['min'];
    $bounds_max     = $bounds_details['max'];

    $bounds_min_lng = $bounds_min['lng'];
    $bounds_min_lat = $bounds_min['lat'];
    $bounds_max_lng = $bounds_max['lng'];
    $bounds_max_lat = $bounds_max['lat'];

    $bounds_param   = 'boundingBox=' . $bounds_max_lat . ',' . $bounds_min_lng . ',' . $bounds_min_lat . ',' . $bounds_max_lng;
    IIRS_0_debug_print( "located the country bounds for [$IIRS_host_TLD / $region_bias] => [$bounds_param]" );
  } else {
    IIRS_0_debug_print( "failed to locate the country bounds for [$IIRS_host_TLD / $region_bias]" );
  }
  $town_name_encoded = urlencode( $town_name );
  $url_request       = "http://open.mapquestapi.com/geocoding/v1/address?key=Fmjtd%7Cluu829uan1%2C8n%3Do5-9w1a9r&location=$town_name_encoded&callback=renderGeocode&outFormat=$format&$bounds_param";
  IIRS_0_debug_print( "region_bias:[" . IIRS_0_setting( 'region_bias' ) . "] => $region_bias" );
  IIRS_0_debug_print( $url_request );

  // http://wiki.openstreetmap.org/wiki/Nominatim#Parameters
  if ( $location_is_example ) {
    IIRS_0_debug_print( "loading example file..." );
    $xml = file_get_contents( "$IIRS_common_dir/location_providers/example_data/mapquest.$format" );
  } else {
    $xml = IIRS_0_http_request( $url_request );
  }
  IIRS_0_debug_print( $xml );

  if ( IIRS_is_error( $xml ) ) {
    $location_arrays = $xml;
  } else {
    $doc = new DOMDocument( );
    $doc->loadXML( $xml );
    $oXPath = new DOMXpath( $doc );

    // translate the results in to a standardised array
    if ( ! $doc->documentElement || ! $doc->documentElement->childNodes ) {
      $location_arrays = new IIRS_Error( IIRS_LOCATION_XML_INVALID, 'Oops, it seems that the our servers are not responding! The manager has been informed and is trying to solve the problem. Please come back here tomorrow :)', 'Invalid XML returned', IIRS_MESSAGE_EXTERNAL_SYSTEM_ERROR, IIRS_MESSAGE_NO_USER_ACTION );
    } else {
      // handle no results (not an error as such!)
      if ( $doc->documentElement->childNodes->length == 0 ) {
        IIRS_0_debug_print( "no results from lookup" );
      } else {
        // get places info into an associative array
        $placeNodes = $oXPath->query( '/response/results/result/locations/location' );
        IIRS_0_debug_print( "$placeNodes->length results from lookup" );
        foreach ( $placeNodes as $placeNode ) {
          if ( $placeNode->nodeType == XML_ELEMENT_NODE ) {
            /*
              <street></street>
              <adminArea5 type="City"></adminArea5>
              <adminArea3 type="State">England</adminArea3>
              <adminArea4 type="County">Bedford</adminArea4>
              <postalCode></postalCode>
              <adminArea1 type="Country">GB</adminArea1>
              <geocodeQuality>COUNTY</geocodeQuality>
              <geocodeQualityCode>A4XAX</geocodeQualityCode>
              <dragPoint>false</dragPoint>
              <sideOfStreet>N</sideOfStreet>
              <displayLatLng><latLng><lat>52.136381</lat><lng>-0.467504</lng></latLng></displayLatLng>
              <linkId>0</linkId>
              <type>s</type>
              <latLng><lat>52.136381</lat><lng>-0.467504</lng></latLng>
              <mapUrl>
            */
            $location_array = array( );

            // assemble place description
            $description = '';
            foreach ( $placeNode->childNodes as $placeValueNode ) {
              $sPlaceKey = $placeValueNode->nodeName;
              if ( $sPlaceKey != 'geocodeQuality'
                && $sPlaceKey != 'geocodeQualityCode'
                && $sPlaceKey != 'dragPoint'
                && $sPlaceKey != 'sideOfStreet'
                && $sPlaceKey != 'displayLatLng'
                && $sPlaceKey != 'linkId'
                && $sPlaceKey != 'type'
                && $sPlaceKey != 'latLng'
                && $sPlaceKey != 'mapUrl'
              ) {
                $sPlaceValue = trim( $placeValueNode->nodeValue );
                if ( $sPlaceValue ) $description .= "$sPlaceValue, ";
              }
            }
            if ( $description ) $description = substr( $description, 0, -2 );

            // entry and add in to the array
            $location_array['description']  = $description;
            $location_array['latitude']     = IIRS_0_get_DOM_value( $placeNode, 'latLng/lat' );;
            $location_array['longitude']    = IIRS_0_get_DOM_value( $placeNode, 'latLng/lng' );;
            $location_array['granuality']   = IIRS_0_get_DOM_value( $placeNode, 'geocodeQuality' );;
            $location_array['country']      = IIRS_0_get_DOM_value( $placeNode, "adminArea1[@type='Country']" );;
            $location_array['full_address'] = $description;
            $location_array['bounds']       = '';

            $location_arrays[] = $location_array;
          }
        }
      }
    }
  }

  return $location_arrays;
}
?>
