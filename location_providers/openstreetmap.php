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
  print( '<div class="IIRS_0_data_license">Geocoding Courtesy of <a href="http://www.openstreetmap.org/" target="_blank">OpenStreetMap</a></div>' );
}

function IIRS_0_geocode( $town_name, $format = 'xml' ) {
  global $location_is_example, $IIRS_host_TLD, $IIRS_common_dir;

  $location_arrays = array();

  // OSM has a usage limit, use of Nominatim is discouraged...
  // 403 Forbidden codes can come through if you over use the OSM API
  // http://wiki.openstreetmap.org/wiki/Nominatim_usage_policy
  // Open Street Map does not support post codes!

  // not using Services_Openstreetmap module because it requires PEAR HTTP_Request2
  // http://wiki.openstreetmap.org/wiki/API
  // https://github.com/kenguest/Services_Openstreetmap/tree/master/examples
  // $osm = new Services_OpenStreetMap( );
  // IIRS_0_debug_var_dump( $osm->getPlace( $town_name ));
  $town_name_encoded = urlencode( $town_name );
  $url_request       = "http://nominatim.openstreetmap.org/search?q=$town_name_encoded&format=$format&polygon_kml=1&addressdetails=1";
  IIRS_0_debug_print( $url_request );

  // http://wiki.openstreetmap.org/wiki/Nominatim#Parameters
  if ( $location_is_example ) {
    IIRS_0_debug_print( "loading example file..." );
    $xml = file_get_contents( "$IIRS_common_dir/registration/example_data/openstreetmap.$format" );
  } else {
    $xml = IIRS_0_http_request( $url_request );
  }

  if ( IIRS_is_error( $xml ) ) {
    $location_arrays = $xml;
  } else {
    $doc = new DOMDocument( );
    $doc->loadXML( $xml );

    // translate the results in to a standardised array
    if ( ! $doc->documentElement || ! $doc->documentElement->childNodes ) {
      $location_arrays = new IIRS_Error( IIRS_LOCATION_XML_INVALID, 'Oops, it seems that the our servers are not responding! The manager has been informed and is trying to solve the problem. Please come back here tomorrow :)', 'Invalid XML returned', IIRS_MESSAGE_EXTERNAL_SYSTEM_ERROR, IIRS_MESSAGE_NO_USER_ACTION );
    } else {
      // handle no results (not an error as such!)
      if ( $doc->documentElement->childNodes->length == 0 ) {
        IIRS_0_debug_print( "no results for lookup" );
      } else {
        // get places info into an associative array
        $placeNodes = $doc->documentElement->childNodes;
        foreach ( $placeNodes as $placeNode ) {
          if ( $placeNode->nodeType == XML_ELEMENT_NODE && $placeNode->nodeName == 'place' ) {
            $location_array = array( );

            // assemble place description
            $description = '';
            foreach ( $placeNode->childNodes as $placeValueNode ) {
              $sPlaceKey = $placeValueNode->nodeName;
              if ( $sPlaceKey != 'geokml'
                && $sPlaceKey != 'country_code'
                && $sPlaceKey != 'station'
              ) {
                $sPlaceValue = trim( $placeValueNode->nodeValue );
                if ( $sPlaceValue ) $description .= "$sPlaceValue, ";
              }
            }
            if ( $description ) $description = substr( $description, 0, -2 );

            // entry and add in to the array
            $location_array['description'] = $description;
            // TODO: calculate the centre point from the geoxml
            // we can do this *if* we choose to use OSM again...
            $location_array['latitude']     = '52';
            $location_array['longitude']    = '0';
            $location_array['granuality']   = '';
            $location_array['country']      = '';
            $location_array['full_address'] = '';
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
