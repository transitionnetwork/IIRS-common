<div id="IIRS_0_debug"><pre>
debug output:
<?php
require_once('framework_abstraction_layer.php');
require_once('utility.php');
require_once('environment.php');
require_once('inputs.php');

//------------------------------------- values
IIRS_0_TI_updateTI(array('summary' => $summary));

//------------------------------------- about us section
?>
</pre></div>

<div id="IIRS_0">
  <div class="IIRS_0_h1"><?php IIRS_0_print_translation('advanced settings'); ?></div>
  <form method="POST" id="IIRS_0_form_popup_advanced" action="finished" class="IIRS_0_clear"><div>
    <?php IIRS_0_printEncodedPostParameters(); ?>
    <?php IIRS_0_print_translation('yay'); ?>

    <br class="IIRS_0_clear" />
    <input class="IIRS_0_bigbutton IIRS_0_back" type="button" value="&lt;&lt; <?php IIRS_0_print_translation('back'); ?>" />
    <input class="IIRS_0_bigbutton" type="submit" value="<?php IIRS_0_print_translation('finish'); ?>" />
  </div></form>
</div>
