<div id="IIRS_0_debug"><pre>
debug output:
<?php
global $debug_environment;
require_once( 'framework_abstraction_layer.php' );
require_once( 'utility.php' );
require_once( 'environment.php' );
require_once( 'registration/inputs.php' );
require_once( 'location.php' );
print( $debug_environment );

// ------------------------------------------------------------- inputs
// $form = standard in inputs.php: the name of the form sent through
// in this case: login, update account details, update transition initiative
$towns_searched_for = ! empty( $town_name );

// ------------------------------------------------------------- IIRS authentication
// from login form below
if ( $form == 'login' ) {
  print( "logging in [$name]..." );
  IIRS_0_login( $name, $pass );
}

// ------------------------------------------------------------- updates
$TI = NULL;

if ( 'update account details' == $form ) {
  // current user logged in is required here
  print( "updating account details...\n" );
  $values = array(
    'name'     => $name,
    'email'    => $email,
    'password' => ( $pass == IIRS_0_CLEAR_PASSWORD ? NULL : $pass ),
  );
  IIRS_0_TI_update_user( $values );
}

if ( 'update transition initiative' == $form ) {
  // current user logged in is required here
  // to get the correct TI
  print( "updating TI details...\n" );
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
  IIRS_0_TI_update_TI( $values );
}

// ------------------------------------------------------------- load current values
// this page only works with a single user that has only 1 registered TI
// it does not handle users who have registered multiple TIs
$is_user_with_one_TI = false;
$location_uniques    = array();
if ( IIRS_0_logged_in() ) {
  print( "loading user details...\n" );
  if ( $user = IIRS_0_details_user() ) {
    $name  = $user['name'];
    $email = $user['email'];
    $phone = $user['phone'];

    print( "loading TI [" . IIRS_0_CONTENT_TYPE . "] details...\n" );
    if ( $TI = IIRS_0_details_TI_user() ) {
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
      print( "could not load associated TI. invalid user for this screen. show login screen.\n" );
    }
  } else {
    print( "could not load user: show login screen\n" );
  }
}

// ------------------------------------------------------------- location control
if ( $towns_searched_for ) {
  // append to the original option
  // still select the first option in this new list though
  // which will effectively anull the first selection above
  $location_options     .= IIRS_0_location_search_options( $town_name, $location_uniques );
  $towns_found        = ! empty( $location_options );
}

// ------------------------------------------------------------- editing area
?>
</pre></div>

<div id="IIRS_0">
  <style>
  </style>

  <div class="IIRS_0_h1"><?php IIRS_0_print_translation( 'setup editor' ); ?>
    <?php IIRS_0_print_language_selector(); ?>
  </div>

  <?php if ( $is_user_with_one_TI ) { ?>
    <form action="index" class="IIRS_0_clear IIRS_0_formPopupNavigate" method="POST">
      <h2><?php IIRS_0_print_translation( 'your details' ); ?>:</h2>
      <div id="IIRS_0_name" class="IIRS_0_formfield">
        <label id="IIRS_0_name_label">name:</label>
        <input id="IIRS_0_name_input" type="text" name="name" value="<?php print( $name ); ?>" />
      </div>
      <div id="IIRS_0_email" class="IIRS_0_formfield">
        <label id="IIRS_0_email_label">email:</label>
        <input id="IIRS_0_email_input" type="text" name="email" value="<?php print( $email ); ?>" />
      </div>
      <div id="IIRS_0_password" class="IIRS_0_formfield">
        <label id="IIRS_0_password_label">password:</label>
        <input id="IIRS_0_password_input" type="password" name="pass" value="<?php print( IIRS_0_CLEAR_PASSWORD ); ?>" />
      </div>
      <input type="hidden" name="form" value="update account details" />
      <input type="submit" name="submit" class="IIRS_0_bigbutton IIRS_0_clear" value="<?php IIRS_0_print_translation( 'update account details' ); ?>" />
    </form>

    <form action="index" class="IIRS_0_clear IIRS_0_formPopupNavigate" method="POST">
      <h2><?php IIRS_0_print_translation( 'transition initiative details' ); ?>:</h2>
      <div id="IIRS_0_initiative_name" class="IIRS_0_formfield">
        <label id="IIRS_0_initiative_name_label">initiative name:</label>
        <input id="IIRS_0_initiative_name_input" type="text" name="initiative_name" value="<?php print( $initiative_name ); ?>" />
      </div>
      <div id="IIRS_0_domain" class="IIRS_0_formfield">
        <label id="IIRS_0_domain_label">domain:</label>
        <input id="IIRS_0_domain_input" type="text" name="domain" value="<?php print( $domain ); ?>" />
      </div>

      <ul id="IIRS_0_list_selector">
        <?php if ( $towns_searched_for && ! $towns_found ) { ?>
          <li class="IIRS_0_place IIRS_0_message">
            <img src="<?php print( "$IIRS_URL_image_stem/information" ); ?>" />
            <?php print( IIRS_0_translation( 'no towns found matching' ) . " $town_name " . '<br/>' . IIRS_0_translation( 'you will need to' ) . ' <a href="mailto:annesley_newholm@yahoo.it">' . IIRS_0_translation( 'register by email' ) . '</a> ' . IIRS_0_translation( 'because we cannot find your town on our maps system!' )); ?>
          </li>
        <?php } ?>
        <?php print( $location_options ); ?>
        <li id="IIRS_0_other" class="IIRS_0_place">
          <?php IIRS_0_print_translation( 'change location' ); ?>:
          <input id="IIRS_0_research_townname_new" value="<?php if ( $town_name ) print( $town_name ); ?>" />
          <input id="IIRS_0_research" type="button" value="<?php IIRS_0_print_translation( 'search' ); ?>" />
        </li>
      </ul>

      <div id="IIRS_0_summary" class="IIRS_0_formfield">
        <label id="IIRS_0_summary_label">summary:</label>
        <textarea id="IIRS_0_summary_input" class="IIRS_0_textarea" name="summary"><?php print( $summary ); ?></textarea>
      </div>

      <input type="hidden" name="form" value="update transition initiative" />
      <input type="submit" class="IIRS_0_bigbutton IIRS_0_clear" name="submit" value="<?php IIRS_0_print_translation( 'update transition initiative' ); ?>" />
    </form>
<?php } else {

// ------------------------------------------------------------- login
?>

    <form action="index" class="IIRS_0_clear IIRS_0_formPopupNavigate" method="POST">
      <h2><?php IIRS_0_print_translation( 'login required to edit' ); ?>:</h2>
      <input type="text" name="name" value="<?php print( $name ); ?>" />
      <input type="password" name="pass" />
      <input type="hidden" name="form" value="login" />
      <input name="submit" type="submit" class="IIRS_0_bigbutton IIRS_0_clear" value="login" />
    </form>
  <?php } ?>
</div>
