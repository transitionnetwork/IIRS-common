<?php
//-------------------------------------------------------------------- TI
$tiID              = IIRS_0_input('tiID');
$townname          = IIRS_0_input('townname');
$initiative_name   = IIRS_0_input('initiative_name');
$domain            = IIRS_0_input('domain');
$domain_other      = IIRS_0_input('domain_other');
if ($domain_other) $domain = $domain_other;
$summary           = IIRS_0_input('summary');
if ($place_value_serialised = IIRS_0_input('place')) {
  $aPlace             = unserialize(urldecode($place_value_serialised));
  $place_bounds       = $aPlace['bounds'];
  $place_description  = $aPlace['description'];
  $place_centre_lat   = $aPlace['centre_lat'];
  $place_centre_lng   = $aPlace['centre_lng'];
  $place_full_address = $aPlace['full_address'];
  $place_country      = $aPlace['country'];
  $place_granuality   = $aPlace['granuality'];
}
$is_example        = (substr($townname, 0, 7) == 'example' || $townname == 't:town or area' || $townname == '');

//-------------------------------------------------------------------- user
$userID            = IIRS_0_input('userID');
$name              = IIRS_0_input('name');
$email             = IIRS_0_input('email');
$pass              = IIRS_0_input('pass');
$phone             = IIRS_0_input('phone');

//-------------------------------------------------------------------- misc
$form            = IIRS_0_input('form');

IIRS_0_debug_print_inputs();
?>
