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
  print( '<div class="IIRS_0_data_license"><a target="_blank" href="http://google.com"><img src="/IIRS/images/powered-by-google-on-white2.png" /></a></div>' );
}

function IIRS_0_geocode( $town_name, $format = 'xml' ) {
  global $location_is_example, $IIRS_host_TLD, $IIRS_common_dir;

  $location_arrays = array();

  // http://maps.google.com/maps/api/geocode/xml?sensor=false&address=bedford
  // see also http://en.wikipedia.org/wiki/Country_code_top-level_domain
  //   for region bias codes
  $region_bias       = IIRS_0_setting( 'region_bias' );
  if ( ! $region_bias || $region_bias == 'region_bias' ) $region_bias = $IIRS_host_TLD;
  if ( $region_bias == 'dev' || $region_bias == 'org' ) $region_bias = 'uk';
  $town_name_encoded = urlencode( $town_name );
  $url_request       = "https://maps.google.com/maps/api/geocode/$format?sensor=false&region=$region_bias&address=$town_name_encoded&key=" . IIRS_GOOGLE_API_KEY;
  IIRS_0_debug_print( $url_request );

  if ( $location_is_example ) {
    IIRS_0_debug_print( "loading example file..." );
    $xml = file_get_contents( "$IIRS_common_dir/location_providers/example_data/google.$format" );
  } else {
    $xml = IIRS_0_http_request( $url_request, null, 5.0 );
  }
  IIRS_0_debug_print( $xml );

  if ( IIRS_is_error( $xml ) ) {
    $location_arrays = $xml;
  } else {
    // DOMDocument and DOMXpath ( PHP5 )
    // PHP >= 5 should be checked for in the installation procedure ( IIRS.install )
    $doc    = new DOMDocument( );
    $doc->loadXML( $xml );
    $oXPath = new DOMXpath( $doc );

    // translate the results in to a standardised array
    if ( ! $doc->documentElement || ! $doc->documentElement->childNodes ) {
      $location_arrays = new IIRS_Error( IIRS_LOCATION_XML_INVALID, 'Oops, it seems that the our servers are not responding! The manager has been informed and is trying to solve the problem. Please come back here tomorrow :)', 'Invalid XML returned', IIRS_MESSAGE_EXTERNAL_SYSTEM_ERROR, IIRS_MESSAGE_NO_USER_ACTION, array( '$url_request' => $url_request ) );
    } else {
      // IIRS_0_debug_var_dump( $xml );
      if ( $doc->documentElement->childNodes->length == 0 ) {
        // handle no results
        IIRS_0_debug_print( "no results for lookup" );
      } else {
        // get places info into an associative array
        $placeNodes = $doc->documentElement->childNodes;
        foreach ( $placeNodes as $placeNode ) {
          if ( $placeNode->nodeType == XML_ELEMENT_NODE && $placeNode->nodeName == 'result' ) {
            $location_array = array( );

            // entry and add in to the array
            // basic values
            $location_array['description']  = IIRS_0_get_DOM_value( $placeNode, 'formatted_address' );
            $location_array['latitude']     = IIRS_0_get_DOM_value( $placeNode, 'geometry/location/lat' );
            $location_array['longitude']    = IIRS_0_get_DOM_value( $placeNode, 'geometry/location/lng' );
            $location_array['granuality']   = IIRS_0_get_DOM_value( $placeNode, 'type' );

            // full address
            $fullAddress            = '';
            $nodeList               = $oXPath->query( 'address_component', $placeNode );
            foreach ( $nodeList as $address_component ) {
              $address_level = IIRS_0_get_DOM_value( $address_component, "type[1]" );
              $address_name  = IIRS_0_get_DOM_value( $address_component, 'long_name' );
              $fullAddress  .= $address_name . ', ';
              if ( $address_level == 'country' ) $location_array['country'] = $address_name;
            }
            if ( $fullAddress ) $fullAddress = substr( $fullAddress, 0, -2 );
            $location_array['full_address'] = $fullAddress;

            // bounds
            $northeast              = IIRS_0_get_DOM_value( $placeNode, 'geometry/bounds/northeast/lat' ) . ',' . IIRS_0_get_DOM_value( $placeNode, 'geometry/bounds/northeast/lng' );
            $southwest              = IIRS_0_get_DOM_value( $placeNode, 'geometry/bounds/southwest/lat' ) . ',' . IIRS_0_get_DOM_value( $placeNode, 'geometry/bounds/southwest/lng' );
            $location_array['bounds']       = "$northeast;$southwest";

            $location_arrays[] = $location_array;
          }
        }
      }
    }
  }

  return $location_arrays;
}
?>