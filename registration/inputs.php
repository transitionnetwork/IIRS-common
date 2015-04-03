<?php
/* Copyright 2015, 2016 Transition Network ltd
 * This program is distributed under the terms of the GNU General Public License
 * as detailed in the COPYING file included in the root of this plugin
 */
?>

<?php
global $location_array_not_specified;

// -------------------------------------------------------------------- configuration
$accept_website_address = IIRS_0_setting( 'accept_website_address' );
$offer_buy_domains      = IIRS_0_setting( 'offer_buy_domains' );
$add_projects           = IIRS_0_setting( 'add_projects' );
$advanced_settings      = IIRS_0_setting( 'advanced_settings' );

// -------------------------------------------------------------------- TI
// SECURITY: IIRS_RAW_USER_INPUT indicates that the dangerous USER INPUT will not have slashes added before single quotes
// pay attention to appropriately escape when outputting these values into the HTML stream
$town_name         = IIRS_0_input( 'town_name',       IIRS_RAW_USER_INPUT );
$initiative_name   = IIRS_0_input( 'initiative_name', IIRS_RAW_USER_INPUT );
$native_ti_ID      = IIRS_0_input( 'native_ti_ID' );
$town_name_base    = IIRS_0_input( 'town_name_base' );
$summary           = IIRS_0_input( 'summary' );
$domain            = IIRS_0_input( 'domain' );
$domain_other      = IIRS_0_input( 'domain_other' );
if ( ! isset( $domain ) || empty( $domain ) ) $domain = $domain_other;

if ( $location_value_serialised = IIRS_0_input( 'place' )) {
  $location_array        = unserialize( urldecode( $location_value_serialised ));
  if ( $location_array == $location_array_not_specified )  IIRS_0_debug_print( 'location not specified' );
  $location_description  = $location_array['description'];
  $location_latitude     = $location_array['latitude'];
  $location_longitude    = $location_array['longitude'];
  $location_country      = $location_array['country'];
  $location_full_address = $location_array['full_address'];
  $location_granuality   = $location_array['granuality'];
  $location_bounds       = $location_array['bounds'];
}

//-------------------------------------------------------------------- user
$native_user_ID    = IIRS_0_input( 'native_user_ID' );
$name              = IIRS_0_input( 'name' );
$email             = IIRS_0_input( 'email' );
$pass              = IIRS_0_input( 'pass' );
$phone             = IIRS_0_input( 'phone' );

//-------------------------------------------------------------------- misc
$form              = IIRS_0_input( 'form' );

// IIRS_0_debug_print_inputs();
?>
