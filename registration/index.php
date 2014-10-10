<div id="IIRS_0_debug"><pre>
debug output:
<?php
global $debug_environment;
require_once('framework_abstraction_layer.php');
require_once('utility.php');
require_once('environment.php');
require_once('inputs.php');
print($debug_environment);
?>
</pre></div>

<div id="IIRS_0">
  <div id="IIRS_0_no_javascript" class="IIRS_0_warning">
    <?php IIRS_0_print_translation('oops, Javascript failed to run, services unavailable, please go to'); ?>
    &nbsp;<a href="http://transitionnetwork.org/">Transition Network</a>&nbsp;
    <?php IIRS_0_print_translation('to register instead'); ?>
  </div>

  <!-- intial form -->
  <div class="IIRS_0_h1"><?php IIRS_0_print_translation('register your transition town'); ?>
    <?php IIRS_0_print_language_selector(); ?>
  </div>
  <!-- using an absolute @action here because this HTML is also presented out-of-IIRS-context, e.g. a wordpress shortcode -->
  <form method="POST" id="IIRS_0_form_sidebar" class="IIRS_0_formPopupNavigate IIRS_0_form" action="/IIRS/registration/location_general"><div>
    <input id="IIRS_0_townname" class="IIRS_0_hint IIRS_0_required" name="townname" value="<?php IIRS_0_print_translation('town or area'); ?>" />
    <input id="IIRS_0_submit" name="submit" type="submit" disabled="1" value="<?php IIRS_0_print_translation('check'); ?> &gt;&gt;" />
  </div></form>
  <div class="IIRS_0_reason"><?php IIRS_0_print_translation('connect with other Transition Towns, get visitors, have parties, blow things up.'); ?></div>
</div>