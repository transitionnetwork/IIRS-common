<?php
global $debug_environment;
require_once( 'framework_abstraction_layer.php' );
require_once( 'utility.php' );
require_once( 'environment.php' );
// print( $debug_environment );

$authenticated           = ( IIRS_0_input( 'password' ) == 'fryace4' );
$all_registering_servers = ( IIRS_0_input( 'all_registering_servers' ) == 'yes' );

if ( $authenticated ) {
  $all_TIs = IIRS_0_TIs_all();
  usort( $all_TIs, 'sort_date_desc' );

  $doc              = new DOMDocument();
  // TODO: set default namespace to TN
  // $node_initiatives->add_namespace( 'http://transitionnetwork.org/namespaces/2014/transition' );
  // $node_initiatives->add_namespace( 'http://transitionnetwork.org/namespaces/2014/transition', 'tn' );
  $node_initiatives = $doc->createElementNS( $transition_namespace, 'initiatives' );
  $doc->appendChild( $node_initiatives );
  foreach ( $all_TIs as $TI ) {
    if ( $all_registering_servers || $TI['registering_server'] == $IIRS_host_domain ) {
      $node_initiative = $doc->createElement( 'initiative' );
      $node_initiatives->appendChild( $node_initiative );
      $node_initiative->setAttribute( 'guid', $TI['guid'] );
      $node_initiative->setAttribute( 'native-ID', $TI['native_ID'] );
      $node_initiative->setAttribute( 'registration-date', $TI['registration_date'] );
      $node_initiative->appendChild( $doc->createElement( 'name', $TI['name'] ));
      $node_initiative->appendChild( $doc->createElement( 'registering_server', $TI['registering_server'] ));
    }
  }
  print( $doc->saveXML() );
} else print( '<error>password required</error>' );
?>

