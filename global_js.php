/*
<?php
require_once( IIRS__COMMON_DIR . 'utility.php');
require_once( IIRS__COMMON_DIR . 'framework_abstraction_layer.php');
require_once( IIRS__COMMON_DIR . 'environment.php');
?>
*/

<?php
IIRS_0_print_javascript_variable('g_sLangCode',          $lang_code); //for sending through to subsequent AJAX loads


IIRS_0_print_javascript_variable('g_sDomainStem',        $IIRS_domain_stem);
IIRS_0_print_javascript_variable('g_sIIRSURLStem',       $IIRS_URL_stem);
IIRS_0_print_javascript_variable('g_sIIRSURLCommonStem', $IIRS_URL_common_stem);
IIRS_0_print_javascript_variable('g_sIIRSURLProcessStem',$IIRS_URL_process_stem);
IIRS_0_print_javascript_variable('g_sIIRSURLImageStem',  $IIRS_URL_image_stem);
IIRS_0_print_javascript_variable('g_sGoogleAPIKey',      IIRS_GOOGLE_API_KEY);

IIRS_0_print_javascript_variable('g_sThrobber', '<img id="IIRS_0_throbber" src="' . "$IIRS_URL_image_stem/throbber-active" . '" />');
?>