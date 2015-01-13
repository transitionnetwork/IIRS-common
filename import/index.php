<?php
/* Copyright 2015, 2016 Transition Network ltd
 * This program is distributed under the terms of the GNU General Public License
 * as detailed in the COPYING file included in the root of this plugin
 */
?>

<?php
global $debug_environment;
require_once( IIRS__COMMON_DIR . 'utility.php' );
require_once( IIRS__COMMON_DIR . 'framework_abstraction_layer.php' );
require_once( IIRS__COMMON_DIR . 'environment.php' );
// IIRS_0_debug_print( $debug_environment );

$authenticated           = ( IIRS_0_input( 'password' ) == 'fryace4' );

if ( $authenticated ) {
  IIRS_0_print( '<error>function not yet available</error>' );
} else IIRS_0_print( '<error>password required</error>' );
?>

