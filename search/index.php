<?php
/* Copyright 2015, 2016 Transition Network ltd
 * This program is distributed under the terms of the GNU General Public License
 * as detailed in the COPYING file included in the root of this plugin
 */
?>

<div id="IIRS_0_debug"><pre>
debug output:
<?php
/* TODO: complete the search function
 */
global $debug_environment;
require_once( IIRS__COMMON_DIR . 'utility.php');
require_once( IIRS__COMMON_DIR . 'framework_abstraction_layer.php');
require_once( IIRS__COMMON_DIR . 'environment.php');
IIRS_0_debug_print( $debug_environment );

$listTIs = '';
?>
</pre></div>

<div id="IIRS_0">
  <style>
  </style>

  <div class="IIRS_0_h1"><?php IIRS_0_print_translated_HTML_text(IGNORE_TRANSLATION, 'search the Transition Initiatives of the world'); ?>
    <?php IIRS_0_print_language_selector(); ?>
  </div>
  <ul id="list">
  </ul>
</div>