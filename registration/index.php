<?php
/* Copyright 2015, 2016 Transition Network ltd
 * This program is distributed under the terms of the GNU General Public License
 * as detailed in the COPYING file included in the root of this plugin
 */

//<title>Registration SCREEN #1 (usually a sidebar WIDGET)</title>
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

// Why register can be a short text or a link
// Links must start with http://
$why_register_html         = '';
$why_register_link_details = IIRS_0_translation('(why register link. format: http://[web address] [link text])');
if ($why_register_link_details != '(why register link. format: http://[web address] [link text])') {
  $why_register_link = substr($why_register_link_details, 0, strpos($why_register_link_details, " "));
  $why_register_text = substr($why_register_link_details, strpos($why_register_link_details, " ") + 1);
  $why_register_html = "<a href=\"$why_register_link\">$why_register_text</a>";
}
?>
</pre></div>

<div id="IIRS_0">
  <div class="IIRS_0_no_javascript IIRS_0_message IIRS_0_message_level_warning">
    <?php IIRS_0_print_translated_HTML_text('Oops, Javascript failed to run, services unavailable, please go to'); ?>
    &nbsp;<a href="http://transitionnetwork.org/">Transition Network</a>&nbsp;
    <?php IIRS_0_print_translated_HTML_text('to register instead'); ?>
  </div>

  <!-- intial form -->
  <div class="IIRS_0_h1"><?php IIRS_0_print_translated_HTML_text('register your Transition Initiative'); ?>
    <?php IIRS_0_print_language_selector(); ?>
  </div>
  <!-- using an absolute @action here because this HTML is also presented out-of-IIRS-context, e.g. a wordpress shortcode -->
  <form method="POST" id="IIRS_0_form_sidebar" class="IIRS_0_formPopupNavigate IIRS_0_form" action="/IIRS/registration/location_general"><div>
    <input id="IIRS_0_town_name" class="IIRS_0_hint IIRS_0_required" name="town_name" value="<?php IIRS_0_print_translated_HTML_text('town or area'); ?>" />
    <input id="IIRS_0_submit" name="submit" type="submit" disabled="1" value="<?php IIRS_0_print_translated_HTML_text('register'); ?> &gt;&gt;" />
  </div></form>
  <div class="IIRS_0_rules"><a target="_blank" href="<?php IIRS_0_print_translated_HTML_text('http://www.transitionnetwork.org/support/becoming-official#criteria'); ?>"><?php IIRS_0_print_translated_HTML_text('what is a Transition Initiative?'); ?></a></div>
  <div class="IIRS_0_reason"><?php IIRS_0_print_HTML($why_register_html); ?></div>
  <div class="IIRS_0_reason"><?php IIRS_0_print_translated_HTML_text('connect to the Transition Network and advertise yourself on our website.'); ?></div>
</div>