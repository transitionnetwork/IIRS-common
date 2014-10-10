<?php
//-------------------------------------------------------------------- TI
$tiID              = IIRS_0_input('tiID');
$town_name         = IIRS_0_input('townname');
$initiative_name   = IIRS_0_input('initiative_name');
$summary           = IIRS_0_input('summary');
$domain            = IIRS_0_input('domain');
$domain_other      = IIRS_0_input('domain_other');
if ($domain_other) $domain = $domain_other;

if ($location_value_serialised = IIRS_0_input('place')) {
  $location_array        = unserialize(urldecode($location_value_serialised));
  $location_description  = $location_array['description'];
  $location_latitude   = $location_array['latitude'];
  $location_longitude   = $location_array['longitude'];
  $location_country      = $location_array['country'];
  $location_full_address = $location_array['full_address'];
  $location_granuality   = $location_array['granuality'];
  $location_bounds       = $location_array['bounds'];
}
$is_example = (substr($town_name, 0, 7) == 'example' || $town_name == 't:town or area' || $town_name == '');

//-------------------------------------------------------------------- user
$userID            = IIRS_0_input('userID');
$name              = IIRS_0_input('name');
$email             = IIRS_0_input('email');
$pass              = IIRS_0_input('pass');
$phone             = IIRS_0_input('phone');

//-------------------------------------------------------------------- misc
$form              = IIRS_0_input('form');

IIRS_0_debug_print_inputs();
?>
