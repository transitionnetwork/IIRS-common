/*
<?php
require_once('framework_abstraction_layer.php');
require_once('utility.php');
require_once('environment.php');
?>
*/

<?php
IIRS_0_print_javascript_variable('g_sLangCode',          $langCode); //for sending through to subsequent AJAX loads


IIRS_0_print_javascript_variable('g_sDomainStem',        $IIRSDomainStem);
IIRS_0_print_javascript_variable('g_sIIRSURLStem',       $IIRSURLStem);
IIRS_0_print_javascript_variable('g_sIIRSURLCommonStem', $IIRSURLCommonStem);
IIRS_0_print_javascript_variable('g_sIIRSURLProcessStem',$IIRSURLProcessStem);
IIRS_0_print_javascript_variable('g_sIIRSURLImageStem',  $IIRSURLImageStem);

IIRS_0_print_javascript_variable('g_sThrobber', '<img id="IIRS_0_throbber" src="' . "$IIRSURLImageStem/throbber-active" . '" />');
?>