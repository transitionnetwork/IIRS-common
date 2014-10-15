<div id="IIRS_0_debug"><pre>
debug output:
<?php
require_once('framework_abstraction_layer.php');
require_once('utility.php');
require_once('environment.php');
require_once('inputs.php');

//------------------------------------- values
IIRS_0_TI_update_TI(array('summary' => $summary));

$url = IIRS_0_setting( 'thankyou_for_registering_url' );
if ( $url ) IIRS_0_redirect( $url );
?>
</pre></div>

<div id="IIRS_0">
  <div class="IIRS_0_h1"><?php IIRS_0_print_translation('thanks'); ?></div>
  <p><?php IIRS_0_print_translation('thankyou message'); ?></p>
</div>
