<?php
//-------------------------------------------------------------------- configuration
$accept_website_address = IIRS_0_setting( 'accept_website_address' );
$offer_buy_domains      = IIRS_0_setting( 'offer_buy_domains' );
$add_projects           = IIRS_0_setting( 'add_projects' );
$advanced_settings      = IIRS_0_setting( 'advanced_settings' );

//-------------------------------------------------------------------- TI
$ti_ID             = IIRS_0_input( 'tiID' );
$town_name         = IIRS_0_input( 'townname' );
$initiative_name   = IIRS_0_input( 'initiative_name' );
$summary           = IIRS_0_input( 'summary' );
$domain            = IIRS_0_input( 'domain' );
$domain_other      = IIRS_0_input( 'domain_other' );
if ( $domain_other ) $domain = $domain_other;

if ( $location_value_serialised = IIRS_0_input( 'place' )) {
  $location_array        = unserialize( urldecode( $location_value_serialised ));
  $location_description  = $location_array['description'];
  $location_latitude     = $location_array['latitude'];
  $location_longitude    = $location_array['longitude'];
  $location_country      = $location_array['country'];
  $location_full_address = $location_array['full_address'];
  $location_granuality   = $location_array['granuality'];
  $location_bounds       = $location_array['bounds'];
}

//-------------------------------------------------------------------- user
$user_ID           = IIRS_0_input( 'userID' );
$name              = IIRS_0_input( 'name' );
$email             = IIRS_0_input( 'email' );
$pass              = IIRS_0_input( 'pass' );
$phone             = IIRS_0_input( 'phone' );

//-------------------------------------------------------------------- misc
$form              = IIRS_0_input( 'form' );

IIRS_0_debug_print_inputs();
?>
