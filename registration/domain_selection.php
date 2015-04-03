<?php
/* Copyright 2015, 2016 Transition Network ltd
 * This program is distributed under the terms of the GNU General Public License
 * as detailed in the COPYING file included in the root of this plugin
 */

//<title>Registration SCREEN #3</title>
?>

<div id="IIRS_0_debug"><pre>
debug output:
<?php
global $debug_environment, $location_is_example, $location_array_not_specified;
require_once( IIRS__COMMON_DIR . 'utility.php' );
require_once( IIRS__COMMON_DIR . 'framework_abstraction_layer.php' );
require_once( IIRS__COMMON_DIR . 'environment.php' );
require_once( IIRS__COMMON_DIR . 'location.php' );
require_once( IIRS__COMMON_DIR . 'registration/inputs.php' );
require_once( IIRS__COMMON_DIR . 'akismet.php' );
IIRS_0_debug_print( $debug_environment );

/* This is the start of the multi-threaded version of the domain checking code
 * it currently requires PECL > 2.0.0
 * http://php.net/manual/en/book.pthreads.php
 *
 * require_once( IIRS__COMMON_DIR . 'IIRS_DomainChecker_Thread.php' );
 * new IIRS_DomainChecker_Thread( 'abc.com' );
 * $valid_domains = IIRS_DomainChecker_Thread::waitAllFinished();
 * var_dump($valid_domains);
 * exit(0);
 */

IIRS_0_debug_print("-------------- User and TI registration");
$TI_save_error  = NULL;

// ------------------------------------- register the User!
// make the permanent additions!
// if the $pass is not being captured a random one will be generated
// $resultant_pass will contain the password sent, or generated
// using a step by step with rollback here because of potential concurrency issues
//   could have used a verification -> add system instead
//   but the host framework may have its own reasons for rejecting additions later on
// IIRS_0_TI_add_user( ... ) also does a login as the new user
// IIRS_0_delete_current_user() will logout and delete the user during rollback
if ( ! isset( $pass ) ) $pass = IIRS_0_generate_password( $name );
$native_user_ID = IIRS_0_TI_add_user( $name, $email, $pass, $phone );

// ------------------------------------- register the TI!
if ( IIRS_is_error( $native_user_ID ) ) {
  IIRS_0_debug_print( "user addition failed [$native_user_ID]" );
  $TI_save_error = $native_user_ID;
} else {
  IIRS_0_debug_print( "added user [$native_user_ID]" );
  // IIRS_0_TI_verify_add_TI() will also:
  //   check Akismet for possible SPAM
  //   check to see if the logged in user already has a TI registration
  //   check to see if the TI is already registered
  //   actual addition may also be rejected by the host framework (error will be wrapped in an IIRS_Error)
  // TODO: use IIRS_0_remove_transition_words( ... ) to register the base initiative name
  //   currently disabled because this would restrict the users ability to register 2 conflicting names, e.g.:
  //     bedfordInTransition
  //     transitionBedford
  //   could push the user to add a meaningful disambiguation like "West"
  //     bedfordWestInTransition
  //     transitionBedford
  $initiative_name_base = IIRS_0_remove_transition_words( $initiative_name );
  $native_ti_ID         = IIRS_0_TI_verify_add_TI( $native_user_ID, $IIRS_host_domain, $initiative_name, $town_name_base, $location_latitude, $location_longitude, $location_description, $location_country, $location_full_address, $location_granuality, $location_bounds, $domain );
  if ( IIRS_is_error( $native_ti_ID ) ) {
    // rollback user registration
    IIRS_0_debug_print( "TI data verification failed [$native_ti_ID]" );
    IIRS_0_debug_print( "rolling back: deleting and logging out current user" );
    $rollback_user = IIRS_0_delete_current_user(); // also does a logout, can return system errors
    if ( IIRS_is_error( $rollback_user ) ) IIRS_0_debug_print( "rollback error [$rollback_user]" );
    $TI_save_error = $native_ti_ID;
  } else {
    IIRS_0_debug_print( "TI addition completed [$native_ti_ID]" );
    if ( $location_array == $location_array_not_specified ) {
      IIRS_0_debug_print( 'location not specified: alerting central command' );
      $location_unspecified_email_message = "Location un-specified during registration of [$town_name]";
      new IIRS_Error( IIRS_GEOCODE_REGISTRATION_WITHOUT_LOCATION, $location_unspecified_email_message, "Location un-specified during registration of [$town_name] [$native_ti_ID]", IIRS_MESSAGE_EXTERNAL_SYSTEM_ERROR, NULL );
    }

    // ------------------------------------------- registration email with generated password to the user
    if ( is_null( $pass ) ) {
      IIRS_0_debug_print( "resultant password unknown! no email will be sent" );
    } else {
      $subject  = IIRS_0_registration_email_subject();
      $body     = IIRS_0_registration_email_html( $name, $pass );

      //user email
      IIRS_0_debug_print( "Email user registration [$body]" );
      $email_ok = IIRS_0_send_email( $email, $subject, $body );
      if ( IIRS_is_error( $email_ok ) ) {
        IIRS_0_debug_print( "email failed to send [$email_ok]" );
      }

      //admin registration alert email
      if ($admin_email_address = IIRS_0_setting('registration_notification_email')) {
        $admin_subject = IIRS_0_translation( '[IIRS admin notice] new Transition account registered' );
        if (IIRS_0_debug()) $admin_email_address = 'annesley_newholm@yahoo.it';

        IIRS_0_debug_print( "Email administration of user registration" );
        $email_ok = IIRS_0_send_email( $admin_email_address, $admin_subject, $body );
        if ( IIRS_is_error( $email_ok ) ) {
          IIRS_0_debug_print( "administration registration heads up email failed to send [$email_ok]" );
        }
      }
    }
  }
}

if ( ! $TI_save_error ) {
  //------------------------------------- debug
  IIRS_0_debug_print( "details:" );
  IIRS_0_debug_print( "  native_user_ID:$native_user_ID (don't forget that all emails on dev point to annesley newholms yahoo email address)" );
  IIRS_0_debug_print( "  native_ti_ID:$native_ti_ID" );

  //------------------------------------- get some nice domain names for this town
  $domains_found     = false;
  $domain_part       = ($location_is_example ? 'bedford' : $town_name);
  $nice_domains_html = '';

  // internationalisation of nice_domain array and TLD array
  $nice_domains      = IIRS_0_get_nice_domains( $domain_part );
  IIRS_0_debug_print( 'nice_domain stems:' );
  IIRS_0_debug_var_dump( $nice_domains );

  //using our tld list is too time consuming
  //could use threads but beyond scope for now
  /*
  $effective_tld_names = file_get_contents(__DIR__ . '/effective_tld_names.dat.txt');
  $aAllFileEntries     = explode("\n", $effective_tld_names);
  $all_TLDs               = array();
  foreach ($aAllFileEntries as $entry) {
    if (strlen($entry) && substr($entry, 0, 2) != '//') {
      $all_TLDs[] = $entry;
    }
  }
  IIRS_0_debug_print("check potential domain string [$domain_part] combinations against [" . count($all_TLDs) . "] TLDs:");
  */
  $all_TLDs = IIRS_0_get_nice_TLDs();
  $option = 1;

  // NOTE: that in some server environments DNS checks like this will hang the process
  // need to continue these all on separate threads... v2
  if ( true ) {
    IIRS_0_debug_print( 'testing nice_domains...' );
    foreach ($nice_domains as $nice_domain) {
      foreach ($all_TLDs as $tld) {
        $full_domain = strtolower("$nice_domain.$tld");
        IIRS_0_debug_print( "testing [$full_domain]" );
        $ip_address  = gethostbyname( $full_domain ); // gethostbyname PHP 4,5
        $valid_dns   = ( $ip_address != $full_domain );

        if ($valid_dns) {
          IIRS_0_debug_print( "  valid [$ip_address]" );
          $domains_found  = true;
          $selected       = ($option == 1 ? 'checked="1"' : '');
          $selected_class = ($option == 1 ? 'selected' : '');
          // SECURITY: $full_domain is constructed from user input
          $full_domain_escaped = IIRS_0_escape_for_HTML_href( $full_domain );
          $view_in_new_window  = IIRS_0_translation( 'view in new window' );
          $nice_domains_html .= <<<"HTML"
            <li class="$selected_class">
              <input $selected name="domain" class="IIRS_0_radio" value="$full_domain" type="radio" id="IIRS_0_domain_{$option}_input" />
              <label for="IIRS_0_domain_{$option}_input">
                $full_domain
                <div class="IIRS_0_status"><a target="_blank" href="http://$full_domain">$view_in_new_window</a></div>
              </label>
            </li>
HTML;
          $option++;
        } else {
          IIRS_0_debug_print( "  invalid" );
        }
      }
    }
  }
}

?>
</pre></div>

<div id="IIRS_0">
  <?php
  if ( $TI_save_error ) {
    // IIRS_0_set_translated_error_message( ... ) uses IIRS_0_set_message( ... )
    IIRS_0_set_translated_error_message( $TI_save_error );
  } else {
    // IIRS_0_set_message() will escape the
    $message = IIRS_0_translation('you are now registered.') . " $initiative_name " . IIRS_0_translation('is go!');
    if ( $IIRS_widget_mode ) $message .= "\n" . IIRS_0_translation('you will need to log in to') . ' TransitionNetwork.org ' . IIRS_0_translation('to manage your registration, NOT this website') . '.';
    IIRS_0_set_message( IIRS_MESSAGE_SUCCESS_REGISTRATION, $message );
    ?>

    <form method="POST" id="IIRS_0_form_popup_domain_selection" action="summary_projects" class="IIRS_0_clear IIRS_0_formPopupNavigate"><div>
      <?php IIRS_0_printEncodedPostParameters(); ?>
      <input name="initiative_name_base" type="hidden" value="<?php IIRS_0_print_HTML_form_value( $initiative_name_base ); ?>" />
      <input name="native_user_ID" type="hidden" value="<?php IIRS_0_print_HTML_form_value( $native_user_ID ); ?>" />
      <input name="native_ti_ID" type="hidden" value="<?php IIRS_0_print_HTML_form_value( $native_ti_ID ); ?>" />

      <div class="IIRS_0_h1"><?php IIRS_0_print_translated_HTML_text('Here are the websites we have found that might correspond to your initiative. We invite you to select one; complete the "other" field or choose the option "no wesbite".'); ?></div>
      <ul id="IIRS_0_list_selector">
        <?php if ( ! $domains_found ) { ?>
          <!--li class="IIRS_0_domain IIRS_0_message">
            <img src="< ?php IIRS_0_print_HTML_image_src("$IIRS_URL_image_stem/information"); ?>" />
            < ?php
              IIRS_0_print_translated_HTML_text(IGNORE_TRANSLATION, 'no registered websites found for this town');
              IIRS_0_print_HTML_text( " $town_name " );
              IIRS_0_print_translated_HTML_text(IGNORE_TRANSLATION, 'you will need to email');
              IIRS_0_print_HTML( ' ' . IIRS_EMAIL_TEAM_LINK . ' ' );
              IIRS_0_print_translated_HTML_text(IGNORE_TRANSLATION, 'to register by email. please type your website name in below if you have one');
            ?>
          </li -->
        <?php } ?>
        <?php IIRS_0_print_HTML( $nice_domains_html ); ?>
        <li>
          <input name="domain" class="IIRS_0_radio" value="none" type="radio" id="IIRS_0_domain_none_input" />
          <label for="IIRS_0_domain_none_input">
            <?php IIRS_0_print_translated_HTML_text('no website'); ?>
            <div class="IIRS_0_status"><?php IIRS_0_print_translated_HTML_text('we do not currently have a website'); ?></div>
          </label>
        </li>
        <li id="IIRS_0_other">
          <?php
            if ( $domains_found ) IIRS_0_print_translated_HTML_text( 'other' );
            else IIRS_0_print_translated_HTML_text( 'your website' );
          ?>:
          <input id="IIRS_0_research_domain_other" name="domain_other" />
        </li>
      </ul>

      <?php if ( $offer_buy_domains ) { ?>
        <ul id="IIRS_0_domain_setup_options">
          <li><input id="IIRS_0_domain_setup_worpress" name="domain_setup" type="radio" />         <label for="IIRS_0_domain_setup_worpress"><?php IIRS_0_print_translated_HTML_text( IGNORE_TRANSLATION, 'load' ); ?><a href="http:// wordpress.org" target="_blank">Wordpress</a><?php IIRS_0_print_translated_HTML_text( IGNORE_TRANSLATION, 'on to this domain and give me the keys' ); ?></label></li>
          <li><input id="IIRS_0_domain_setup_drupal" name="domain_setup" type="radio" />           <label for="IIRS_0_domain_setup_drupal"><?php IIRS_0_print_translated_HTML_text( IGNORE_TRANSLATION, 'load' ); ?><a href="http:// drupal.org" target="_blank">Drupal</a><?php IIRS_0_print_translated_HTML_text( IGNORE_TRANSLATION, 'on to this domain and give me the keys' ); ?></label></li>
          <li><input id="IIRS_0_domain_setup_none" checked="1" name="domain_setup" type="radio" /> <label for="IIRS_0_domain_setup_none"><?php IIRS_0_print_translated_HTML_text( IGNORE_TRANSLATION, 'stop being clever and just give me the domains' ); ?></label></li>
        </ul>
        <input id="IIRS_0_buydomains" class="IIRS_0_bigbutton" disabled="1" type="button" value="<?php IIRS_0_print_translated_HTML_text( IGNORE_TRANSLATION, 'buy marked domains' ); ?>" />
      <?php } ?>

      <div class="IIRS_0_horizontal_section">
        <input class="IIRS_0_bigbutton IIRS_0_back" type="button" value="&lt;&lt; <?php IIRS_0_print_translated_HTML_text('back'); ?>" />
        <input class="IIRS_0_bigbutton" type="submit" value="<?php IIRS_0_print_translated_HTML_text('save and continue'); ?> &gt;&gt;" />
      </div>
    </form>
  <?php } ?>
</div> <!-- /IIRS_0 -->
