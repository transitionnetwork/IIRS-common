<?php
/* Copyright 2015, 2016 Transition Network ltd
 * This program is distributed under the terms of the GNU General Public License
 * as detailed in the COPYING file included in the root of this plugin
 */

//<title>Registration SCREEN #2</title>

global $location_is_example, $mapping_provider, $location_array_not_specified;
$not_specified = IIRS_0_translation( "not-specified" );
$location_array_not_specified = array(
  'description'  => $not_specified,
  'latitude'     => "0",
  'longitude'    => "0",
  'full_address' => $not_specified,
  'country'      => $not_specified,
  'granuality'   => $not_specified,
  'bounds'       => $not_specified,
);
$mapping_provider    = 'mapquest';
$location_is_example = ( isset( $town_name ) && substr( $town_name, 0, 7 ) == 'example' );

function IIRS_0_tld_bounds( $tld = '' ) {
  global $IIRS_host_TLD;

  $host_TLD_touse = ( $tld = '' ? $IIRS_host_TLD : $tld );
  $host_ISO_3166  = strtoupper( $host_TLD_touse );
  $bounds         = IIRS_0_iso_3166_bounds( $host_ISO_3166 );

  return $bounds;
}

function IIRS_0_iso_3166_bounds( $ISO_3166_code ) {
  require_once( 'iso-3166-bounds.php' );
  $bounds = $iso_3166_bounds[ $ISO_3166_code ];
  return $bounds;
}

function IIRS_0_location_search_options( $town_name, &$location_uniques = array() ) {
  // return null on null $location_arrays (indicates error)
  $location_arrays = IIRS_0_location_lookup( $town_name );
  if ( IIRS_is_error( $location_arrays ) ) $location_HTML = $location_arrays;
  else $location_HTML = IIRS_0_standard_location_output( $location_arrays, $location_uniques );
  return $location_HTML;
}

function IIRS_0_location_lookup( $town_name ) {
  // town lookup and options
  // RETURNS an array of places
  global $location_is_example, $IIRS_host_TLD, $IIRS_common_dir, $mapping_provider;

  $location_provider = "$IIRS_common_dir/location_providers/$mapping_provider.php";
  $location_arrays   = array();

  IIRS_0_debug_print( "looking up [$town_name] on [$mapping_provider]" );
  if ( file_exists( $location_provider ) ) {
    require_once( $location_provider );
    $location_arrays = IIRS_0_geocode( $town_name );
  } else {
    $location_arrays = new IIRS_Error( IIRS_LOCATION_PROVIDER_INVALID, 'Oops, it seems that the our servers are not responding! The manager has been informed and is trying to solve the problem. Please come back here tomorrow :)', 'Mapping provider not valid, please set one', IIRS_MESSAGE_SYSTEM_ERROR, IIRS_MESSAGE_NO_USER_ACTION, array( '$mapping_provider' => $mapping_provider ) );
  }

  if ( IIRS_is_error( $location_arrays ) ) IIRS_0_debug_print( $location_arrays );

  return $location_arrays;
}

function IIRS_0_standard_location_output( $location_arrays, &$location_uniques = array() ) {
  // return null on null input (indicates error)
  // HTML output
  // IIRS_0_debug_var_dump( $location_arrays );
  $option           = 1;
  $location_output  = '';

  // return null on null input (indicates error)
  if ( is_null( $location_arrays ) || IIRS_is_error( $location_arrays ) ) {
    $location_output = $location_arrays;
  } else {
    foreach ( $location_arrays as $location_array ) {
      // HTML assemble
      $location_output .= IIRS_0_location_to_HTML( $location_array, $location_uniques, (1 == $option) );
      $option++;
    }
  }

  return $location_output;
}

function IIRS_0_location_to_HTML( $location_array, &$location_uniques = array(), $select = false, $town_name = '' ) {
  static $option = 1;
  $location_description  = $location_array['description'];
  $location_latitude     = $location_array['latitude'];
  $location_longitude    = $location_array['longitude'];
  $location_full_address = $location_array['full_address'];
  $location_country      = $location_array['country'];
  $location_granuality   = $location_array['granuality'];
  $location_bounds       = $location_array['bounds'];

  if ( IIRS_is_error( $location_array ) ) {
    $location_output = $location_array;
  } else {
    // unique identity output
    $unique_id = "$location_latitude,$location_longitude";
    if ( ! $location_uniques || ! $unique_id || ! isset( $location_uniques[$unique_id] )) {
      if ( is_numeric( $location_latitude ) && is_numeric( $location_longitude ) ) {
        $google_map_URL  = "https://www.google.com/maps/@$location_latitude,$location_longitude,16z";
        $google_map_link = '<a target="_blank" href="' . $google_map_URL . '\">' . IIRS_0_translation( 'view on map' ) . '</a>';
        $selected        = ( $select ? 'checked="1"' : '' );
        $selected_class  = ( $select ? 'selected' : '' );
        $location_value_serialised = urlencode( serialize( $location_array ));

        // status, description, links
        // SECURITY: $location_status is un-escaped output
        //   $location_latitude && $location_longitude in $google_map_link are numeric
        //   translations are the responsibility of the administrator
        if ( IIRS_0_TI_search_result_already_registered( $town_name, $location_latitude, $location_longitude, $location_description )) {
          $location_status  =  IIRS_0_translation( IGNORE_TRANSLATION, 'transition initiative already registered' ) . " $google_map_link<br/>";
          $location_status .= '<input class="IIRS_0_button" type="button" value="' . IIRS_0_translation( IGNORE_TRANSLATION, 'join!' ) . '"/>';
          $location_status .= '<input class="IIRS_0_button" type="button" value="' . IIRS_0_translation( IGNORE_TRANSLATION, 'message' ) . '"/>';
          $disabled      = 'disabled="1"';
        } else {
          $location_status  = IIRS_0_translation( 'transition initiative not registered yet!' ) . " $google_map_link<br/>";
          // $location_status .= IIRS_0_translation( IGNORE_TRANSLATION, 'closest initiative' ) . ': 5' . IIRS_0_translation( IGNORE_TRANSLATION, 'km' );
          $disabled      = '';
        }

        // HTML assemble
        $location_description_escaped  = IIRS_0_escape_for_HTML_text( $location_description );
        $location_full_address_escaped = IIRS_0_escape_for_HTML_text( "$location_granuality: $location_full_address" );
        $location_output   = <<<"HTML"
          <li class="$selected_class">
            <input $disabled $selected name="place" class="IIRS_0_radio IIRS_0_required" value="$location_value_serialised" type="radio" id="IIRS_0_location_{$option}_input" />
            <label for="IIRS_0_location_{$option}_input">
              $location_description_escaped
              <div class="IIRS_0_full_address">$location_full_address_escaped</div>
              <div class="IIRS_0_status">$location_status</div>
            </label>
          </li>
HTML;

        // record for uniqueness
        if ( $unique_id && $location_uniques ) $location_uniques[$unique_id] = 1;
      }
    }

    $option++;
  }

  return $location_output;
}
?>
