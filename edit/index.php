<?php
/* Copyright 2015, 2016 Transition Network ltd
 * This program is distributed under the terms of the GNU General Public License
 * as detailed in the COPYING file included in the root of this plugin
 */

//<title>EDIT SCREEN</title>
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

// ------------------------------------------------------------- inputs
// $form = standard in inputs.php: the name of the form sent through
// in this case: login, update account details, update transition initiative
$IIRS_error = NULL;
$towns_searched_for = ! empty( $town_name );

// ------------------------------------------------------------- IIRS authentication
// from login form below
if ( $form == 'login' ) {
  IIRS_0_debug_print( "logging in [$name]..." );
  $ret = IIRS_0_login( $name, $pass );
  if ( IIRS_is_error( $ret ) ) $IIRS_error = $ret;
}

// ------------------------------------------------------------- updates
$TI = NULL;

if ( 'update account details' == $form ) {
  // current user logged in is required here
  IIRS_0_debug_print( "updating account details..." );
  $values = array(
    'name'     => $name,
    'email'    => $email,
    'password' => ( $pass == IIRS_0_CLEAR_PASSWORD ? NULL : $pass ),
  );
  $ret = IIRS_0_TI_update_user( $values );
  if ( IIRS_is_error( $ret ) ) $IIRS_error = $ret;
}

if ( 'update transition initiative' == $form ) {
  // current user logged in is required here
  // to get the correct TI
  IIRS_0_debug_print( "updating TI details..." );
  $values = array(
    'name'                  => $initiative_name,
    'summary'               => $summary,
    'domain'                => $domain,
    'town_name'             => $town_name,
    'location_latitude'     => $location_latitude,
    'location_longitude'    => $location_longitude,
    'location_description'  => $location_description,
    'location_country'      => $location_country,
    'location_full_address' => $location_full_address,
    'location_granuality'   => $location_granuality,
    'location_bounds'       => $location_bounds,
  );
  $ret = IIRS_0_TI_update_TI( $values );
  if ( IIRS_is_error( $ret ) ) $IIRS_error = $ret;
}

// ------------------------------------------------------------- load current values
// this page only works with a single user that has only 1 registered TI
// it does not handle users who have registered multiple TIs
$is_user_with_one_TI = false;
$location_uniques    = array();
if ( IIRS_0_logged_in() ) {
  IIRS_0_debug_print( "loading user details..." );
  if ( $user = IIRS_0_details_user() ) {
    $name  = $user['name'];
    $email = $user['email'];
    $phone = ( isset( $user['phone'] ) ? $user['phone'] : null );

    IIRS_0_debug_print( "loading TI [" . IIRS_0_CONTENT_TYPE . "] details..." );
    $TI = IIRS_0_details_TI_user();
    if ( is_array( $TI ) ) {
      $is_user_with_one_TI = true;

      $initiative_name      = $TI['name'];
      $summary              = $TI['summary'];
      $domain               = $TI['domain'];

      // values fo the desciption LI
      $location_array['description']  = $TI['location_description'];
      $location_array['latitude']     = $TI['location_latitude'];
      $location_array['longitude']    = $TI['location_longitude'];
      $location_array['country']      = $TI['location_country'];
      $location_array['full_address'] = $TI['location_full_address'];
      $location_array['granuality']   = $TI['location_granuality'];
      $location_array['bounds']       = $TI['location_bounds'];

      $location_options = IIRS_0_location_to_HTML( $location_array, $location_uniques, true ); // true = selected
    } else {
      $IIRS_error = new IIRS_Error( IIRS_USER_NO_ASSOCIATED_TI, 'There is no Initiative associated with this user', 'TI not linked to this user',  IIRS_MESSAGE_USER_ERROR, IIRS_MESSAGE_NO_USER_ACTION, $user );
      IIRS_0_debug_print( $IIRS_error );
    }
  } else {
    IIRS_0_debug_print( "could not load user: show login screen" );
  }
}

// ------------------------------------------------------------- location control
if ( $towns_searched_for ) {
  // append to the original option
  // still select the first option in this new list though
  // which will effectively anull the first selection above
  $location_options  .= IIRS_0_location_search_options( $town_name, $location_uniques );
  $towns_found        = ! empty( $location_options );
}

// ------------------------------------------------------------- editing area
?>
</pre></div>

<div id="IIRS_0">
  <?php
  if ( $IIRS_error ) {
    // IIRS_0_set_translated_error_message( ... ) uses IIRS_0_set_message( ... )
    IIRS_0_set_translated_error_message( $IIRS_error );
  } else {
  ?>
  <div class="IIRS_0_h1"><?php IIRS_0_print_translated_HTML_text( 'setup editor' ); ?>
    <?php IIRS_0_print_language_selector(); ?>
  </div>

  <?php if ( $is_user_with_one_TI ) { ?>
    <form action="index" class="IIRS_0_clear IIRS_0_formPopupNavigate" method="POST">
      <h2><?php IIRS_0_print_translated_HTML_text( 'your details' ); ?>:</h2>
      <div id="IIRS_0_name" class="IIRS_0_formfield">
        <label id="IIRS_0_name_label" class="IIRS_0_disabled"><?php IIRS_0_print_translated_HTML_text( 'your name' ); ?></label>
        <input id="IIRS_0_name_input" class="IIRS_0_disabled" disabled="1" type="text" name="name" value="<?php IIRS_0_print_HTML_form_value( $name ); ?>" />
      </div>
      <div id="IIRS_0_email" class="IIRS_0_formfield">
        <label id="IIRS_0_email_label"><?php IIRS_0_print_translated_HTML_text( 'email' ); ?></label>
        <input id="IIRS_0_email_input" type="text" name="email" value="<?php IIRS_0_print_HTML_form_value( $email ); ?>" />
      </div>
      <div id="IIRS_0_password" class="IIRS_0_formfield">
        <label id="IIRS_0_password_label"><?php IIRS_0_print_translated_HTML_text( 'password' ); ?></label>
        <input id="IIRS_0_password_input" type="password" name="pass" value="<?php IIRS_0_print_HTML_form_value( IIRS_0_CLEAR_PASSWORD ); ?>" />
      </div>
      <input type="hidden" name="form" value="update account details" />
      <input type="submit" name="submit" class="IIRS_0_bigbutton IIRS_0_clear" value="<?php IIRS_0_print_translated_HTML_text( 'update account details' ); ?>" />
    </form>

    <form action="index" class="IIRS_0_clear IIRS_0_formPopupNavigate" method="POST">
      <h2><?php IIRS_0_print_translated_HTML_text( 'transition initiative details' ); ?>:</h2>
      <div id="IIRS_0_initiative_name" class="IIRS_0_formfield">
        <label id="IIRS_0_initiative_name_label"><?php IIRS_0_print_translated_HTML_text( 'initiative name' ); ?></label>
        <input id="IIRS_0_initiative_name_input" type="text" name="initiative_name" value="<?php IIRS_0_print_HTML_form_value( $initiative_name ); ?>" />
      </div>
      <div id="IIRS_0_domain" class="IIRS_0_formfield">
        <label id="IIRS_0_domain_label"><?php IIRS_0_print_translated_HTML_text( 'website' ); ?></label>
        <input id="IIRS_0_domain_input" type="text" name="domain" value="<?php IIRS_0_print_HTML_form_value( $domain ); ?>" />
      </div>

      <ul id="IIRS_0_list_selector">
        <?php if ( $towns_searched_for && ! $towns_found ) { ?>
          <li class="IIRS_0_place IIRS_0_message IIRS_0_message_level_information">
            <img src="<?php IIRS_0_print_HTML_image_src( "$IIRS_URL_image_stem/information" ); ?>" />
            <?php IIRS_0_print_HTML_text( IIRS_0_translation( 'no towns found matching' ) . " $town_name " . '<br/>' . IIRS_0_translation( 'you will need to email' ) . ' ' . IIRS_EMAIL_TEAM_LINK . ' ' . IIRS_0_translation( 'to register by email because we cannot find your town on our maps system!' )); ?>
          </li>
        <?php } ?>
        <?php IIRS_0_print_HTML( $location_options ); ?>
        <li id="IIRS_0_other" class="IIRS_0_place">
          <?php IIRS_0_print_translated_HTML_text( 'change location' ); ?>:
          <input id="IIRS_0_research_town_name_new" value="<?php IIRS_0_print_HTML_form_value( $town_name ); ?>" />
          <input id="IIRS_0_research" type="button" value="<?php IIRS_0_print_translated_HTML_text( 'search' ); ?>" />
        </li>
      </ul>

      <div id="IIRS_0_summary" class="IIRS_0_formfield">
        <label id="IIRS_0_summary_label"><?php IIRS_0_print_translated_HTML_text( 'summary' ); ?></label>
        <?php IIRS_0_HTML_editor($summary, 'summary'); ?>
      </div>

      <input type="hidden" name="form" value="update transition initiative" />
      <input type="submit" class="IIRS_0_bigbutton IIRS_0_clear" name="submit" value="<?php IIRS_0_print_translated_HTML_text( 'update transition initiative' ); ?>" />
    </form>
  <?php } else {

  // ------------------------------------------------------------- login
  ?>

      <form action="index" class="IIRS_0_clear IIRS_0_formPopupNavigate" method="POST">
        <h2><?php IIRS_0_print_translated_HTML_text( 'login required to edit' ); ?>:</h2>
        <input type="text" name="name" value="<?php IIRS_0_print_HTML_form_value( $name ); ?>" />
        <input type="password" name="pass" />
        <input type="hidden" name="form" value="login" />
        <input name="submit" type="submit" class="IIRS_0_bigbutton IIRS_0_clear" value="login" />
      </form>
    <?php } ?>
  <?php } ?>
</div>
