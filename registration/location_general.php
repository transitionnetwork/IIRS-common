<?php
/* Copyright 2015, 2016 Transition Network ltd
 * This program is distributed under the terms of the GNU General Public License
 * as detailed in the COPYING file included in the root of this plugin
 */
?>

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
global $mapping_provider;
$is_domain      = false;
$towns_found    = false;
$set_message_id = null;
$set_message    = null;
$IIRS_error     = null;
$no_towns_found_disabled       = '';
$no_towns_found_disabled_class = '';
$duplicate_validation_issue    = '';

if ( $town_name ) {
  // ----------------------------------------- check to see if it is a domain and give extra advice
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
  }

  // ------------------------------------------------------------------------- process as town name
  // user input appears to not be a domain, so treat it as a town name
  IIRS_0_debug_print( "treating as a town name" );
  $location_options = IIRS_0_location_search_options( $town_name );
  if ( IIRS_is_error( $location_options ) ) $IIRS_error = $location_options;
  else $towns_found = ! empty( $location_options );

  if ( ! $towns_found ) {
    $no_towns_found_disabled       = 'disabled="1"';
    $no_towns_found_disabled_class = 'IIRS_0_disabled';
    IIRS_0_debug_print( "sending extra geocode fail message" );
    new IIRS_Error( IIRS_GEOCODE_RESULTS_EMPTY, "No towns found for [$town_name]", "Geocode [$mapping_provider] returned zero results for [$town_name]", IIRS_MESSAGE_EXTERNAL_SYSTEM_ERROR, NULL );
  }

  // ------------------------------------------------------------------------- check for already registered initiative_name
  $TI_same_name  = IIRS_0_TI_same_name( $town_name ); // returns a TI or FALSE or [system] IIRS_Error
  if ( is_array( $TI_same_name ) ) {
    // ok, so we have a registration with an identical initiative name
    // same name entries are not necessarily close by. America and UK will have many name conflicts
    // reject this currently. ask for an alternative name
    // this might cause the user to experience surprise, anger, competition.
    // needs to be handled with emotional intelligence, i.e.:
    //   hey! we've found someone you can chat and work with in your space!
    //   OR
    //   someone OWNS this name and you are not permitted to create an Initiative here.
    IIRS_0_debug_var_dump( $TI_same_name );
    $set_message_id = IIRS_TI_EXISTS_SAME_NAME;
    $set_message    = IIRS_0_translation( 'The location is fine. However, the Initiative name already exists' );
    $set_message   .= " [$town_name]. ";
    $set_message   .= IIRS_0_translation( 'Please add something to the initiative name below to make it unique.' );
    $set_message   .= IIRS_0_translation( 'For Example:' );
    $set_message   .= " [west$town_name] or [energy_subgroup_$town_name].";
    $duplicate_validation_issue = 'IIRS_0_validation_fail';
  }
} else {
  $IIRS_error = new IIRS_Error( IIRS_NO_INPUTS, "Oops, we didn't recieve your data. Please try again", "No inputs", IIRS_MESSAGE_USER_ERROR, IIRS_JAVASCRIPT_BACK );
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
  if ( $IIRS_error ) {
    // only show the error
    IIRS_0_set_translated_error_message( $IIRS_error );
  } else {
  ?>
    <?php if (trim(IIRS_0_translation( 'connection of' ) . IIRS_0_translation( 'to the support and innovation network' )) != '') { ?>
      <div class="IIRS_0_h1" id="IIRS_0_popup_title"><?php IIRS_0_print_translated_HTML_text( 'connection of' ); IIRS_0_print_HTML_text( " $town_name " ); IIRS_0_print_translated_HTML_text( 'to the support and innovation network' ); ?> </div>
    <?php } ?>
    <form method="POST" id="IIRS_0_form_popup_location_general" action="domain_selection" class="IIRS_0_clear IIRS_0_formPopupNavigate"><div>
      <?php IIRS_0_printEncodedPostParameters(); ?>

      <h3><?php IIRS_0_print_translated_HTML_text( 'town matches' ); ?></h3>
      <?php if ( $towns_found ) print( IIRS_0_geocode_notice() ); ?>
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
      <?php
        // show the message and continue
        if ( $set_message ) IIRS_0_set_message( $set_message_id, $set_message, NULL, IIRS_MESSAGE_USER_WARNING );
      ?>
      <img id="IIRS_0_details_teaser_img" src="<?php IIRS_0_print_HTML_image_src( "$IIRS_URL_image_stem/network_paper" ); ?>" />
      <table id="IIRS_0_details">
        <tr><td><?php IIRS_0_print_translated_HTML_text( 'initiative name' ); ?></td><td><input id="IIRS_0_initiative_name" <?php IIRS_0_print( $no_towns_found_disabled ); ?> class="IIRS_0_required <?php print( $duplicate_validation_issue ); ?>" name="initiative_name" value="<?php IIRS_0_print_HTML_form_value( $town_name ); ?>" /> <?php IIRS_0_print_translated_HTML_text( 'transition town' ); ?><span class="required">*</span></td></tr>
        <tr><td><?php IIRS_0_print_translated_HTML_text( 'email' ); ?></td><td><input id="IIRS_0_email" <?php IIRS_0_print( $no_towns_found_disabled ); ?> class="IIRS_0_required" name="email" /><span class="required">*</span></td></tr>
        <tr><td><?php IIRS_0_print_translated_HTML_text( 'your name' ); ?></td><td><input id="IIRS_0_name" <?php IIRS_0_print( $no_towns_found_disabled ); ?> class="IIRS_0_required" name="name" /><span class="required">*</span></td></tr>
        <!-- NOTE: are we going to ring them? place this later on in the forms -->
        <!-- tr><td><?php IIRS_0_print_translated_HTML_text( IGNORE_TRANSLATION, 'phone number' ); ?><br/>( <?php IIRS_0_print_translated_HTML_text( IGNORE_TRANSLATION, 'optional' ); ?> )</td><td><input name="phone" /></td></tr -->
      </table>
      <div id="IIRS_0_details_teaser">
        <?php IIRS_0_print_translated_HTML_text( 'registering your email means that local people will contact you to offer support and for your opinion on projects like food growing, energy supply and other Transition ideals. we will let your nearest advanced Transition Town know you have registered so they can connect, support, encourage and share! : )' ); ?>
      </div>

      <br class="IIRS_0_clear" />
      <input class="IIRS_0_bigbutton IIRS_0_back" type="button" value="&lt;&lt; <?php IIRS_0_print_translated_HTML_text( 'change search' ); ?>" />

      <?php
        /* this is the submit button for the initial POST data in to the IIRS database
         * we are creating several buttons and hiding one of them to prevent robots from clicking anything
         * the previous screen of course requires a valid location which will also thwart robots
         */
        $false_submit_HTML = '<input name="false_submit" type="submit" class="IIRS_0_bigbutton IIRS_0_false_submit" value="register" />';
        print( $false_submit_HTML );
        print( $false_submit_HTML );
        print( $false_submit_HTML );
        print( $false_submit_HTML );
        print( "<input name=\"submit\" type=\"submit\" $no_towns_found_disabled class=\"IIRS_0_bigbutton $no_towns_found_disabled_class \" value=\"" . IIRS_0_translation( 'complete registration' ) . " &gt;&gt;\" />" );
        print( $false_submit_HTML );
        print( $false_submit_HTML );
      ?>

      <?php IIRS_0_print_translated_HTML_text( 'and then connect with local Transition Initiatives : )' ); ?>
    </div></form>
  <?php } ?>
</div>
