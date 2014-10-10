<?php
function IIRS_0_location_search_options( $town_name, &$location_uniques = array() ) {
  $location_arrays = IIRS_0_location_lookup( $town_name );
  $location_HTML   = IIRS_0_standard_location_output( $location_arrays, $location_uniques );
  return $location_HTML;
}

function IIRS_0_location_lookup( $town_name ) {
  // town lookup and options
  // RETURNS an array of places
  $mapping_provider = "Google";
  $format           = 'xml';
  $location_arrays     = array( );
  print( "looking up [$town_name] on [$mapping_provider]\n" );

  if ( 'OpenStreetMap' == $mapping_provider ) { // --------------
    // OSM has a usage limit, use of Nominatim is discouraged...
    // 403 Forbidden codes can come through if you over use the OSM API
    // http://wiki.openstreetmap.org/wiki/Nominatim_usage_policy

    // not using Services_Openstreetmap module because it requires PEAR HTTP_Request2
    // http://wiki.openstreetmap.org/wiki/API
    // https://github.com/kenguest/Services_Openstreetmap/tree/master/examples
    // $osm = new Services_OpenStreetMap( );
    // var_dump( $osm->getPlace( $town_name ));

    // http://wiki.openstreetmap.org/wiki/Nominatim#Parameters
    if ( $is_example ) $xml = file_get_contents( "$IIRS_common_dir/registration/$mapping_provider.$format" );
    else               $xml = IIRS_0_http_request( "http://nominatim.openstreetmap.org/search?q=$town_name&format=$format&polygon_kml=1&addressdetails=1" );
    $doc = new DOMDocument( );
    $doc->loadXML( $xml );

    // translate the results in to a standardised array
    // TODO: use XShema
    if ( !$doc->documentElement ) {
      // TODO: what invalid XML returned?
      IIRS_0_set_message_translated( "invalid XML returned: $xml\n", $IIRS_widget_mode );
    } elseif ( !$doc->documentElement->childNodes ) {
      // TODO: what invalid XML returned?
      IIRS_0_set_message_translated( "invalid XML returned: $xml\n", $IIRS_widget_mode );
    } else {
      // handle no results
      if ( $doc->documentElement->childNodes->length == 0 ) {
        print( "no results for lookup\n" );
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
            $location_array['latitude']  = '52';
            $location_array['longitude']  = '0';
            $location_arrays[] = $location_array;
          }
        }
      }
    }
  }

  elseif ( 'Google' == $mapping_provider ) { // --------------
    // http://maps.google.com/maps/api/geocode/xml?sensor=false&address=bedford
    $town_name_encoded = urlencode( $town_name );
    $url_request      = "http://maps.google.com/maps/api/geocode/$format?sensor=false&address=$town_name_encoded";
    print( $url_request );
    if ( $is_example ) $xml = file_get_contents( "$IIRS_common_dir/registration/$mapping_provider.$format" );
    else               $xml = IIRS_0_http_request( $url_request, 5.0, TRUE );

    // DOMDocument and DOMXpath ( PHP5 )
    // PHP >= 5 should be checked for in the installation procedure ( IIRS.install )
    $doc    = new DOMDocument( );
    $doc->loadXML( $xml );
    $oXPath = new DOMXpath( $doc );

    // translate the results in to a standardised array
    if ( !$doc->documentElement ) {
      // TODO: what invalid XML returned?
      IIRS_0_set_message_translated( "invalid XML returned: $xml\n", $IIRS_widget_mode );
    } elseif ( !$doc->documentElement->childNodes ) {
      // TODO: what invalid XML returned?
      IIRS_0_set_message_translated( "invalid XML returned: $xml\n", $IIRS_widget_mode );
    } else {
      // handle no results
      var_dump( $xml );
      if ( $doc->documentElement->childNodes->length == 0 ) {
        print( "no results for lookup\n" );
      } else {
        // get places info into an associative array
        $placeNodes = $doc->documentElement->childNodes;
        foreach ( $placeNodes as $placeNode ) {
          if ( $placeNode->nodeType == XML_ELEMENT_NODE && $placeNode->nodeName == 'result' ) {
            $location_array = array( );

            // entry and add in to the array
            // basic values
            $location_array['description']  = IIRS_0_get_DOM_value( $placeNode, 'formatted_address' );
            $location_array['latitude']   = IIRS_0_get_DOM_value( $placeNode, 'geometry/location/lat' );
            $location_array['longitude']   = IIRS_0_get_DOM_value( $placeNode, 'geometry/location/lng' );
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

  } else { // oops, not a valid provider
    print( "mapping provider [$mapping_provider] not valid, please set one" );
    exit( 1 );
  }

  return $location_arrays;
}

function IIRS_0_standard_location_output( $location_arrays, &$location_uniques = array() ) {
  // HTML output
  var_dump( $location_arrays );
  $option        = 1;
  $location_output  = '';

  foreach ( $location_arrays as $location_array ) {
    // HTML assemble
    $location_output .= IIRS_0_location_to_HTML( $location_array, $location_uniques, (1 == $option) );
    $option++;
  }

  return $location_output;
}

function IIRS_0_location_to_HTML( $location_array, &$location_uniques = array(), $select = false ) {
  static $option = 1;
  $location_description  = $location_array['description'];
  $location_latitude     = $location_array['latitude'];
  $location_longitude    = $location_array['longitude'];
  $location_full_address = $location_array['full_address'];
  $location_country      = $location_array['country'];
  $location_granuality   = $location_array['granuality'];
  $location_bounds       = $location_array['bounds'];

  // unique identity output
  $unique_id = "$location_latitude,$location_longitude";
  if ( ! $location_uniques || ! $unique_id || ! isset( $location_uniques[$unique_id] )) {
    $google_map_URL  = "https://www.google.com/maps/@$location_latitude,$location_longitude,16z";
    $google_map_link = '<a target="_blank" href="' . $google_map_URL . '\">' . IIRS_0_translation( 'view on map' ) . '</a>';
    $selected        = ( $select ? 'checked="1"' : '' );
    $selected_class  = ( $select ? 'selected' : '' );
    $location_value_serialised = urlencode( serialize( $location_array ));

    // status, description, links
    if ( IIRS_0_TI_is_registered( $town_name, $location_latitude, $location_longitude, $location_description )) {
      $location_status  =  IIRS_0_translation( 'transition initiative already registered' ) . " $google_map_link<br/>";
      $location_status .= '<input class="IIRS_0_button" type="button" value="' . IIRS_0_translation( 'join!' ) . '"/>';
      $location_status .= '<input class="IIRS_0_button" type="button" value="' . IIRS_0_translation( 'message' ) . '"/>';
      $disabled      = 'disabled="1"';
    } else {
      $location_status  = IIRS_0_translation( "transition initiative not registered yet!" ) . " $google_map_link<br/>";
      // $location_status .= IIRS_0_translation( 'closest initiative' ) . ': 5' . IIRS_0_translation( 'km' );
      $disabled      = '';
    }

    // HTML assemble
    $location_output   = <<<"HTML"
      <li class="$selected_class">
        <input $disabled $selected name="place" class="IIRS_0_radio" value="$location_value_serialised" type="radio" id="IIRS_0_location_{$option}_input" />
        <label for="IIRS_0_location_{$option}_input">
          $location_description
          <div class="IIRS_0_full_address">$location_granuality: $location_full_address</div>
          <div class="IIRS_0_status">$location_status</div>
        </label>
      </li>
HTML;

    // record for uniqueness
    if ( $unique_id && $location_uniques ) $location_uniques[$unique_id] = 1;
  }

  $option++;

  return $location_output;
}
?>
