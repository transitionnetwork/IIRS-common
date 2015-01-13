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
require_once( IIRS__COMMON_DIR . 'registration/inputs.php' );
IIRS_0_debug_print( $debug_environment );
?>
</pre></div>

<div id="IIRS_0">
  <div class="IIRS_0_no_javascript IIRS_0_message IIRS_0_message_level_warning">
    <?php IIRS_0_print_translated_HTML_text('oops, Javascript failed to run, services unavailable, please go to'); ?>
    &nbsp;<a href="http://transitionnetwork.org/">Transition Network</a>&nbsp;
    <?php IIRS_0_print_translated_HTML_text('to register instead'); ?>
  </div>

  <!-- intial form -->
  <div class="IIRS_0_h1"><?php IIRS_0_print_translated_HTML_text('register your transition town'); ?>
    <?php IIRS_0_print_language_selector(); ?>
  </div>
  <!-- using an absolute @action here because this HTML is also presented out-of-IIRS-context, e.g. a wordpress shortcode -->
  <form method="POST" id="IIRS_0_form_sidebar" class="IIRS_0_formPopupNavigate IIRS_0_form" action="/IIRS/registration/location_general"><div>
    <input id="IIRS_0_town_name" class="IIRS_0_hint IIRS_0_required" name="town_name" value="<?php IIRS_0_print_translated_HTML_text('town or area'); ?>" />
    <input id="IIRS_0_submit" name="submit" type="submit" disabled="1" value="<?php IIRS_0_print_translated_HTML_text('check'); ?> &gt;&gt;" />
  </div></form>
  <div class="IIRS_0_rules"><a target="_blank" href="<?php IIRS_0_print_translated_HTML_text('http://www.transitionnetwork.org/support/becoming-official#criteria'); ?>"><?php IIRS_0_print_translated_HTML_text('what is a Transition Town?'); ?></a></div>
  <div class="IIRS_0_reason"><?php IIRS_0_print_translated_HTML_text('connect with other Transition Towns, get visitors, have parties.'); ?></div>
</div>