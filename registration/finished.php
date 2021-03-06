<?php
/* Copyright 2015, 2016 Transition Network ltd
 * This program is distributed under the terms of the GNU General Public License
 * as detailed in the COPYING file included in the root of this plugin
 */
?>

<div id="IIRS_0_debug"><pre>
debug output:
<?php
require_once( IIRS__COMMON_DIR . 'utility.php' );
require_once( IIRS__COMMON_DIR . 'framework_abstraction_layer.php' );
require_once( IIRS__COMMON_DIR . 'environment.php' );
require_once( IIRS__COMMON_DIR . 'registration/inputs.php' );

//------------------------------------- values
IIRS_0_TI_update_TI( array( 'summary' => $summary ) );

$url = IIRS_0_setting( 'thankyou_for_registering_url' );
if ( empty( $url ) ) $url = IIRS_0_URL_view_TI();
if ( $url ) IIRS_0_redirect( $url );
IIRS_0_debug_print( "thankyou_for_registering_url: [$url]" );
?>
</pre></div>
