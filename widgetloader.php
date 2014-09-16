/* error reporting:
<?php
//scenario: JavaScript widget loader
//this is dynamic PHP created javascript
//it is the JavaScript necessary to load the HTML/CSS/JS pages required in to a page or popup
//the popup.js is included below which causes the form events and others to be overridden in widget mode
header('Content-type: application/javascript', true);

require_once('framework_abstraction_layer.php');
require_once('utility.php');
require_once('environment.php');
?>
*/

<?php
//--------------------------------------------------- directly include the javascript
//because we are doing this from just one script
require_once('translations_js.php'); //custom JS translations for alerts etc.
require_once('global_js.php');       //JS global variables, e.g. URL stems

//general all-scenario interaction, e.g. form checking
require_once("$IIRS_common_dir/general_interaction.js");
//switch forms to popup mode: often overrides and unbind() the general events
require_once("$IIRS_common_dir/popup_interaction.js");

//flow area specific general interaction, e.g. form checking
$customJavaScriptInteractionsPath = "$IIRS_common_dir/$processGroup/general_interaction.js";
if (file_exists($customJavaScriptInteractionsPath)) require_once($customJavaScriptInteractionsPath);
//flow area popup specific general interaction, e.g. extra form navigation and checking
$customJavaScriptInteractionsPath = "$IIRS_common_dir/$processGroup/popup_interaction.js";
if (file_exists($customJavaScriptInteractionsPath)) require_once($customJavaScriptInteractionsPath);
?>

//--------------------------------------------------- initial content load
//in line here
//TODO: replace document.write won't work on XML/XSL systems
document.write('<div id="IIRS_0_placeholder_popup">'   + g_sThrobber + ' transition initiative registration popup loading...</div>');
document.write('<div id="IIRS_0_placeholder_initial">' + g_sThrobber + ' transition initiative registration loading...</div>');

jQuery(document).ready(function(e){
  //set initial content to index
  //with the popup HTML
  //this will trigger a IIRS_0_newContent jQuery event
  //IIRS_0_setContent('#IIRS_0_placeholder_popup',   g_sIIRSURLCommonStem  + '/general.css'); //temp: included in popup.php
  IIRS_0_setContent('#IIRS_0_placeholder_popup',   g_sIIRSURLCommonStem  + '/popup');
  IIRS_0_setContent('#IIRS_0_placeholder_initial', g_sIIRSURLProcessStem + '/index');
});
