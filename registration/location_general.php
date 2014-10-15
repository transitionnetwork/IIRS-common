<div id="IIRS_0_debug"><pre>
debug output:
<?php
global $debug_environment;
require_once( 'framework_abstraction_layer.php' );
require_once( 'utility.php' );
require_once( 'environment.php' );
require_once( 'inputs.php' );
require_once( 'location.php' );
print($debug_environment);

// -------------------------------------------------------------------- check input type: domain or town name
$is_domain   = false;
$towns_found = false;

if ( !$town_name ) {
  print( "no inputs\n" );
  IIRS_0_set_message_translated( "no inputs", $IIRS_widget_mode );
} else {
  if ( strchr( $town_name, '.' )) {
    // clean potential domain name and check it for TLD on end
    $domain = trim( $town_name );
    $domain = preg_replace( '/^( https?:\/\/ )?( www\. )?( [^\/?]* ).*/i', '$3', $domain );

    if ( $domain && !strchr( $domain, ' ' )) {
      $effective_tld_names = file_get_contents( __DIR__ . '/effective_tld_names.dat.txt' );
      $all_file_entries     = explode( "\n", $effective_tld_names );
      print( "check potential domain string [$domain] against [" . count( $all_file_entries ) . "] TLDs:\n" );
      foreach ( $all_file_entries as $entry ) {
        if ( strlen( $entry ) && substr( $entry, 0, 2 ) != '// ' ) {
          if ( substr( $domain, -( strlen( $entry ) + 1 )) == ".$entry" ) {
            $is_domain = true;
            print( "[$domain] ends with [$entry]\n" );
            break;
          }
        }
      }
    }
  }

  // ------------------------------------------------------------------------- process town name
  if( $is_domain ) {
    IIRS_0_set_message_translated( 'this looks like a domain ( website address ), you need to enter a town or area name instead', $IIRS_widget_mode );
  } else {
    print( "not a domain, treating as a town name\n" );
    $location_options = IIRS_0_location_search_options( $town_name );
    $towns_found   = ! empty( $location_options );
  }
}
?>
</pre></div>

<style>
  /* ---------------------------------------------------------------- details entry layout */
  #IIRS_0_details, #IIRS_0_details tr, #IIRS_0_details td {
    border:none;
  }
  #IIRS_0_details {
    width:300px;
    float:left;
  }
  #IIRS_0_details_teaser {
    clear:both;
  }
  #IIRS_0_details_teaser_img {
    float:right;
    width:198px;
    padding:2px;
  }
  #IIRS_0_details td {
    white-space:nowrap;
  }
</style>


<?php // ------------------------------------------------------------- HTML ?>
<div id="IIRS_0" class="IIRS_0_location_general">
  <div class="IIRS_0_h1" id="IIRS_0_popup_title"><?php print( IIRS_0_translation( 'connection of' ) . " $town_name " . IIRS_0_translation( 'to the support and innovation network' )); ?> </div>
  <form method="POST" id="IIRS_0_form_popup_location_general" action="domain_selection" class="IIRS_0_clear IIRS_0_formPopupNavigate"><div>
    <?php IIRS_0_printEncodedPostParameters(); ?>

    <h3><?php IIRS_0_print_translation( 'town matches' ); ?></h3>
    <ul id="IIRS_0_list_selector">
      <?php if ( !$towns_found ) { ?>
        <li class="IIRS_0_place IIRS_0_message">
          <img src="<?php print( "$IIRS_URL_image_stem/information" ); ?>" />
          <?php print( IIRS_0_translation( 'no towns found matching' ) . " $town_name " . '<br/>' . IIRS_0_translation( 'you will need to' ) . ' <a href="mailto:annesley_newholm@yahoo.it">' . IIRS_0_translation( 'register by email' ) . '</a> ' . IIRS_0_translation( 'because we cannot find your town on our maps system!' )); ?>
        </li>
      <?php } ?>
      <?php print( $location_options ); ?>
      <li id="IIRS_0_other" class="IIRS_0_place">
        <?php IIRS_0_print_translation( 'other' ); ?>:
        <input id="IIRS_0_research_townname_new" value="<?php if ( $town_name ) print( $town_name ); ?>" />
        <input id="IIRS_0_research" type="button" value="<?php IIRS_0_print_translation( 'search again' ); ?>" />
      </li>
    </ul>

    <?php if ( $offer_buy_domains && isset( $nice_domains_html )) { ?>
    <h3 class="IIRS_0_horizontal_section"><?php IIRS_0_print_translation( 'domains to consider: are they yours?' ); ?></h3>
    <ul id="IIRS_0_nice_domains">
      <?php print( $nice_domains_html ); ?>
    </ul>
    <ul id="IIRS_0_domain_setup_options">
      <li><input id="IIRS_0_domain_setup_worpress" name="domain_setup" type="radio" />         <label for="IIRS_0_domain_setup_worpress"><?php print( IIRS_0_translation( 'load' ) . ' <a href="http:// wordpress.org" target="_blank">Wordpress</a> ' . IIRS_0_translation( 'on to this domain and give me the keys' )); ?></label></li>
      <li><input id="IIRS_0_domain_setup_drupal" name="domain_setup" type="radio" />           <label for="IIRS_0_domain_setup_drupal"><?php print( IIRS_0_translation( 'load' ) . ' <a href="http:// drupal.org" target="_blank">Drupal</a> ' . IIRS_0_translation( 'on to this domain and give me the keys' )); ?></label></li>
      <li><input id="IIRS_0_domain_setup_none" checked="1" name="domain_setup" type="radio" /> <label for="IIRS_0_domain_setup_none"><?php IIRS_0_print_translation( 'stop being clever and just give me the domains' ); ?></label></li>
    </ul>
    <input id="IIRS_0_buydomains" class="IIRS_0_bigbutton" disabled="1" type="button" value="<?php IIRS_0_print_translation( 'buy marked domains' ); ?>" />
    <?php } ?>

    <h3 class="IIRS_0_horizontal_section"><?php IIRS_0_print_translation( 'some general details' ); ?></h3>
    <img id="IIRS_0_details_teaser_img" src="<?php print( "$IIRS_URL_image_stem/network_paper" ); ?>" />
    <table id="IIRS_0_details">
      <tr><td><?php IIRS_0_print_translation( 'initiative name' ); ?></td><td><input id="IIRS_0_initiative_name" class="IIRS_0_required" name="initiative_name" value="<?php print( $town_name ); ?>" /> transition town<span class="required">*</span></td></tr>
      <tr><td><?php IIRS_0_print_translation( 'email' ); ?></td><td><input id="IIRS_0_email" class="IIRS_0_required" name="email" /><span class="required">*</span></td></tr>
      <tr><td><?php IIRS_0_print_translation( 'your name' ); ?></td><td><input id="IIRS_0_name" class="IIRS_0_required" name="name" /><span class="required">*</span></td></tr>
      <!-- NOTE: are we going to ring them? place this later on in the forms -->
      <!-- tr><td><?php IIRS_0_print_translation( 'phone number' ); ?><br/>( <?php IIRS_0_print_translation( 'optional' ); ?> )</td><td><input name="phone" /></td></tr -->
    </table>
    <div id="IIRS_0_details_teaser">
      <?php IIRS_0_print_translation( 'registering your email means that local people will contact you to offer support and for your opinion on projects like food growing, energy supply and other Transition ideals. we will let Transition Brixton ( your nearest advanced Town ) know you have registered so they can connect, support, encourage and share! : )' ); ?>
    </div>

    <br class="IIRS_0_clear" />
    <input class="IIRS_0_bigbutton IIRS_0_back" type="button" value="&lt;&lt; <?php IIRS_0_print_translation( 'change search' ); ?>" />
    <input class="IIRS_0_bigbutton" type="submit" value="<?php IIRS_0_print_translation( 'complete registration' ); ?> &gt;&gt;" />
    <?php IIRS_0_print_translation( 'and then connect with local Transition Initiatives : )' ); ?>
  </div></form>
</div>
