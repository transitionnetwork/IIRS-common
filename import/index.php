<?php
global $debug_environment;
require_once( 'framework_abstraction_layer.php' );
require_once( 'utility.php' );
require_once( 'environment.php' );
// print( $debug_environment );

$authenticated           = ( IIRS_0_input( 'password' ) == 'fryace4' );

if ( $authenticated ) {
  print( '<error>function not yet available</error>' );
} else print( '<error>password required</error>' );
?>

