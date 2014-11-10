<div id="IIRS_0_debug"><pre>
debug output:
<?php
global $debug_environment;
require_once( IIRS__COMMON_DIR . 'utility.php' );
require_once( IIRS__COMMON_DIR . 'framework_abstraction_layer.php' );
require_once( IIRS__COMMON_DIR . 'environment.php' );
require_once( IIRS__COMMON_DIR . 'registration/inputs.php' );
require_once( IIRS__COMMON_DIR . 'location.php' );
IIRS_0_debug_print( $debug_environment );

// -------------------------------------------------------------------- check input type: domain or town name
$is_domain      = false;
$towns_found    = false;
$set_message_id = null;
$set_message    = null;
$IIRS_error     = null;
$no_towns_found_disabled       = '';
$no_towns_found_disabled_class = '';

if ( ! $town_name ) {
  $IIRS_error = new IIRS_Error( IIRS_NO_INPUTS, "Oops, we didn't recieve your data. Please try again", "No inputs", IIRS_MESSAGE_USER_ERROR, IIRS_JAVASCRIPT_BACK );
} else {
  if ( strchr( $town_name, '.' )) {
    // clean potential domain name and check it for TLD on end
    $domain = trim( $town_name );
    $domain = preg_replace( '/^( https?:\/\/ )?( www\. )?( [^\/?]* ).*/i', '$3', $domain );

    if ( $domain && !strchr( $domain, ' ' )) {
      $effective_tld_names = file_get_contents( __DIR__ . '/effective_tld_names.dat.txt' );
      $all_file_entries     = explode( "\n", $effective_tld_names );
      IIRS_0_debug_print( "check potential domain string [$domain] against [" . count( $all_file_entries ) . "] TLDs:" );
      foreach ( $all_file_entries as $entry ) {
        if ( strlen( $entry ) && substr( $entry, 0, 2 ) != '// ' ) {
          if ( substr( $domain, -( strlen( $entry ) + 1 )) == ".$entry" ) {
            $is_domain = true;
            IIRS_0_debug_print( "[$domain] ends with [$entry]" );
            break;
          }
        }
      }
    }
  }

  if( $is_domain ) {
    // domain entry instead of town entry is a user error at the moment
    $set_message_id = IIRS_MESSAGE_LOOKS_LIKE_A_DOMAIN;
    $set_message    = IIRS_0_translation( 'this looks like a domain ( website address ), you need to enter a town or area name instead' );

    /*
    // ----------------------------------------------------------------- WHOIS on domain (disabled)
    // lookup the whois records to get the locations and emails and user them for the town_name and details
    $aEntries = IIRS_0_whois( $domain );
    if ( IIRS_is_error( $aEntries ) ) {
      // whois lookup failed: offer to buy the domain?
      $IIRS_error = $aEntries; // we do not care about whois failure at the moment
      IIRS_0_debug_print("whois lookup returned empty text.");
    } elseif ( $aEntries ) {
      // success :)
      IIRS_0_debug_var_dump( $aEntries );

      // email domain checkup only
      $is_email_valid = checkdnsrr($sRegistrantEmail, "MX");

      // location lookup
      //if ($sRegistrantPostalCode && $sRegistrantCountry) $town_name = "$sRegistrantPostalCode, $sRegistrantCountry";
      if ($sRegistrantCity)       $town_name = "$sRegistrantCity";
      else {
        //get the town from the domain name
        IIRS_0_debug_print("no registrant city found, lets look in the domain name for a city name");
        $city = preg_replace('/\..*|transition|town|transicao/i', '', $domain);
        if ($city) $town_name = $city;
        else {
          IIRS_0_debug_print("no registrant city found in the domain name either, try to get the city name from the IP");
          //TODO: try to get the town from the IP address
          //...
        }
      }
    }
    */
  } else {
    // ------------------------------------------------------------------------- process town name
    // user input appears to not be a domain, so treat it as a town name
    IIRS_0_debug_print( "not a domain, treating as a town name" );
    $location_options = IIRS_0_location_search_options( $town_name );
    if ( IIRS_is_error( $location_options ) ) $IIRS_error = $location_options;
    else $towns_found = ! empty( $location_options );
  }
}

if ( ! $towns_found ) {
  $no_towns_found_disabled       = 'disabled="1"';
  $no_towns_found_disabled_class = 'IIRS_0_disabled';
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
  <?php
  if ( $set_message ) {
    IIRS_0_set_message( $set_message_id, $set_message );
  } elseif ( $IIRS_error ) {
    IIRS_0_set_translated_error_message( $IIRS_error );
  } else {
  ?>
    <div class="IIRS_0_h1" id="IIRS_0_popup_title"><?php IIRS_0_print_translated_HTML_text( 'connection of' ); IIRS_0_print_HTML_text( " $town_name " ); IIRS_0_print_translated_HTML_text( 'to the support and innovation network' ); ?> </div>
    <form method="POST" id="IIRS_0_form_popup_location_general" action="domain_selection" class="IIRS_0_clear IIRS_0_formPopupNavigate"><div>
      <?php IIRS_0_printEncodedPostParameters(); ?>

      <h3><?php IIRS_0_print_translated_HTML_text( 'town matches' ); ?></h3>
      <ul id="IIRS_0_list_selector">
        <?php if ( ! $towns_found ) { ?>
          <li class="IIRS_0_place IIRS_0_message">
            <img src="<?php IIRS_0_print_HTML_image_src( "$IIRS_URL_image_stem/information" ); ?>" />
            <?php
              IIRS_0_print_translated_HTML_text( 'no towns found matching' );
              IIRS_0_print_html_text( " $town_name. " );
              IIRS_0_print_translated_HTML_text( 'you will need to email' );
              IIRS_0_print_HTML( ' ' . IIRS_EMAIL_TEAM_LINK . ' ' );
              IIRS_0_print_translated_HTML_text( 'to register by email because we cannot find your town on Google Maps!' );
            ?>
          </li>
        <?php } ?>
        <?php IIRS_0_print_HTML( $location_options ); ?>
        <li id="IIRS_0_other" class="IIRS_0_place">
          <?php IIRS_0_print_translated_HTML_text( 'other' ); ?>:
          <input id="IIRS_0_research_town_name_new" value="<?php IIRS_0_print_HTML_form_value( $town_name ); ?>" />
          <input id="IIRS_0_research" type="button" value="<?php IIRS_0_print_translated_HTML_text( 'search again' ); ?>" />
        </li>
      </ul>

      <h3 class="IIRS_0_horizontal_section"><?php IIRS_0_print_translated_HTML_text( 'some general details' ); ?></h3>
      <img id="IIRS_0_details_teaser_img" src="<?php IIRS_0_print_HTML_image_src( "$IIRS_URL_image_stem/network_paper" ); ?>" />
      <table id="IIRS_0_details">
        <tr><td><?php IIRS_0_print_translated_HTML_text( 'initiative name' ); ?></td><td><input id="IIRS_0_initiative_name" <?php IIRS_0_print( $no_towns_found_disabled ); ?> class="IIRS_0_required" name="initiative_name" value="<?php IIRS_0_print_HTML_form_value( $town_name ); ?>" /> transition town<span class="required">*</span></td></tr>
        <tr><td><?php IIRS_0_print_translated_HTML_text( 'email' ); ?></td><td><input id="IIRS_0_email" <?php IIRS_0_print( $no_towns_found_disabled ); ?> class="IIRS_0_required" name="email" /><span class="required">*</span></td></tr>
        <tr><td><?php IIRS_0_print_translated_HTML_text( 'your name' ); ?></td><td><input id="IIRS_0_name" <?php IIRS_0_print( $no_towns_found_disabled ); ?> class="IIRS_0_required" name="name" /><span class="required">*</span></td></tr>
        <!-- NOTE: are we going to ring them? place this later on in the forms -->
        <!-- tr><td><?php IIRS_0_print_translated_HTML_text( 'phone number' ); ?><br/>( <?php IIRS_0_print_translated_HTML_text( 'optional' ); ?> )</td><td><input name="phone" /></td></tr -->
      </table>
      <div id="IIRS_0_details_teaser">
        <?php IIRS_0_print_translated_HTML_text( 'registering your email means that local people will contact you to offer support and for your opinion on projects like food growing, energy supply and other Transition ideals. we will let your nearest advanced Transition Town know you have registered so they can connect, support, encourage and share! : )' ); ?>
      </div>

      <br class="IIRS_0_clear" />
      <input class="IIRS_0_bigbutton IIRS_0_back" type="button" value="&lt;&lt; <?php IIRS_0_print_translated_HTML_text( 'change search' ); ?>" />
      <input <?php IIRS_0_print( $no_towns_found_disabled ); ?> class="IIRS_0_bigbutton <?php IIRS_0_print_HTML_class( $no_towns_found_disabled_class ); ?>" type="submit" value="<?php IIRS_0_print_translated_HTML_text( 'complete registration' ); ?> &gt;&gt;" />
      <?php IIRS_0_print_translated_HTML_text( 'and then connect with local Transition Initiatives : )' ); ?>
    </div></form>
  <?php } ?>
</div>
