<div id="IIRS_0_debug"><pre>
debug output:
<?php
global $debug_environment;
require_once('framework_abstraction_layer.php');
require_once('utility.php');
require_once('environment.php');
require_once('registration/inputs.php');
print($debug_environment);

$aAllTIs = IIRS_0_TIs_all();
usort($aAllTIs, 'sort_date_desc');

$authenticated = (IIRS_0_input('password') == 'fryace4');

if ($authenticated) {
  $doc              = new DOMDocument();
  //TODO: set default namespace to TN
  $node_initiatives = $doc->createElementNS('http://transitionnetwork.org/namespaces/2014/transition', 'initiatives');
  //$node_initiatives->add_namespace('http://transitionnetwork.org/namespaces/2014/transition');
  //$node_initiatives->add_namespace('http://transitionnetwork.org/namespaces/2014/transition', 'tn');
  $doc->appendChild($node_initiatives);
  foreach ($aAllTIs as $TI) {
    $node_initiative = $doc->createElement('initiative');
    $node_initiatives->appendChild($node_initiative);
    $node_name = $doc->createElement('name', $TI['name']);
    $node_initiative->appendChild($node_name);
  }
} else print("password required\n");
?>
</pre></div>

<?php
  if ($authenticated) print(htmlentities($doc->saveXML()));
  else                print('password required');
?>

