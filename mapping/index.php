<?php
/* Copyright 2015, 2016 Transition Network ltd
 * This program is distributed under the terms of the GNU General Public License
 * as detailed in the COPYING file included in the root of this plugin
 */
?>

<div id="IIRS_0_debug"><pre>
debug output:
<?php
global $debug_environment;
require_once( IIRS__COMMON_DIR . 'utility.php' );
require_once( IIRS__COMMON_DIR . 'framework_abstraction_layer.php' );
require_once( IIRS__COMMON_DIR . 'environment.php' );
IIRS_0_debug_print( $debug_environment );

$all_TIs = IIRS_0_TIs_all(); ?>;
?>
</pre></div>

<div id="IIRS_0">
  <div class="IIRS_0_no_javascript IIRS_0_message IIRS_0_message_level_warning">
    <?php IIRS_0_print_translated_HTML_text( 'oops, Javascript failed to run, services unavailable, please go to' ); ?>
    &nbsp;<a href="http://transitionnetwork.org/">Transition Network</a>&nbsp;
    <?php IIRS_0_print_translated_HTML_text( 'to register instead' ); ?>
  </div>

  <!-- intial form -->
  <div class="IIRS_0_h1"><?php IIRS_0_print_translated_HTML_text( 'mappings of transition towns around the world' ); ?>
    <?php IIRS_0_print_language_selector(); ?>
  </div>
  <div class="IIRS_0_map IIRS_0_fit_bounds">
    map loading...
    <div class="IIRS_0_HTML_data location_latitude">0</div>
    <div class="IIRS_0_HTML_data location_longitude">0</div>
    <div class="IIRS_0_HTML_data zoom">1</div>
    <div class="IIRS_0_HTML_data markers">
      <?php foreach ( $all_TIs as $TI ) {?>
        <div class="TI">
          <?php IIRS_0_print_HTML_encode_array( $TI ); ?>
        </div>
      <?php } ?>
    </div>
  </div>
</div>