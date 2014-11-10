<?php
global $IIRS_0_force_complete_framework;
$IIRS_0_force_complete_framework = true;

//--------------------------------------------------- inputs and environment
if ( ! function_exists( 'IIRS_0_input' ) ) {
  function IIRS_0_input( $key, $raw = FALSE ) {
    // use IIRS_RAW_USER_INPUT constant to indicate TRUE for $raw
    // this function needs to return the escaped value, unless asked otherwise
    // PHP may have already escaped it with get_magic_quotes_gpc
    // the framework may have re-written the super-globals $_POST
    //   e.g. Wordpress load.php does this
    // IIRS_0_input(town_name) = o\'brien
    // IIRS_0_input(town_name, true) = o'brien
    $already_added_slashes = get_magic_quotes_gpc();
    $value                 = ( isset( $_POST[$key] ) ? $_POST[$key] : ( isset($_GET[$key] ) ? $_GET[$key] : NULL ) );
    $final_value           = NULL;
    if     ( $already_added_slashes && $raw )     $final_value = stripslashes( $value );
    elseif ( ! $already_added_slashes && ! $raw ) $final_value = addslashes( $value );
    else                                          $final_value = $value;
    return $final_value;
  }
}

if (!function_exists('IIRS_0_setting')) {
  function IIRS_0_setting($setting) {
    switch ($setting) {
      case 'offer_buy_domains': return false;
      case 'add_projects': return false;
      case 'advanced_settings': return false;
      case 'image_entry': return false;
      case 'lang_code': return 'en';
      case 'server_country': return NULL;
      case 'override_TI_display': return false;
      case 'override_TI_editing': return true;
      case 'override_TI_content_template': return true;
      case 'language_selector': return false;
      case 'thankyou_for_registering_url': return null;
      case 'region_bias': return null;
      default: return false;
    }
  }
}

if (!function_exists('IIRS_0_current_path')) {
  function IIRS_0_current_path() {
    return $_SERVER['PHP_SELF'];
  }
}

if (!function_exists('IIRS_0_available_languages')) {
  function IIRS_0_available_languages() {
    return array('en');
  }
}

if (!function_exists('IIRS_0_locale')) {
  function IIRS_0_locale() {
    return 'en_EN';
  }
}


// ------------------------------------------------------- messaging
if (!function_exists('IIRS_0_set_message')) {
  function IIRS_0_set_message($mess_no, $message, $message_detail = null, $level = IIRS_MESSAGE_USER_INFORMATION, $_user_action = null, $_args = null) {
    // global $IIRS_widget_mode requires that the message is included in the HTML output
    // because the user is viewing the message through HTML transported in the widget on a *different* website
    // normal message display, that is through a plugin / module on *this* website can use the host framework function
    // e.g. Drupal uses drupal_set_message() which *indirectly* queues the message for display (once)
    $class = IIRS_0_message_class( $level );
    IIRS_0_print_HTML( "<div class=\"IIRS_0_message IIRS_0_message_$mess_no IIRS_0_message_level_$class\">" . IIRS_0_escape_for_HTML_text( $message ) . '</div>' );
  }
}

if (!function_exists('IIRS_0_redirect')) {
  function IIRS_0_redirect( $url ) {
    $protocol = ( isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' );
    header( $protocol . ' 302 Moved Temporarily' );
    $GLOBALS['http_response_code'] = 302;
  }
}

if (!function_exists('IIRS_0_registration_email_html')) {
  function IIRS_0_registration_email_html( $name, $password = null ) {
    $body     = '<h1>' . IIRS_0_translation( 'welcome to Transition' ) . '</h1>';
    $body    .= '<p>'  . IIRS_0_translation( 'here are your registration details' ) . '<br/>';
    $body    .= '<b>'  . IIRS_0_translation( 'username' ) . '</b>: ' . $name;
    if ( $password ) $body .=  ', <b>' . IIRS_0_translation( 'password' ) . '</b>: ' . $password;
    $body    .= '</p>';
    $body    .= '<p>'  . IIRS_0_translation( 'reply to this email with any thoughts / excitement / ideas / congratulations / bugs / other things :)' ) . '</p>';
    return $body;
  }
}


//--------------------------------------------------- common framework utilities
if ( $IIRS_0_force_complete_framework ) {
  //misc
  IIRS_0_function_required( 'IIRS_0_send_email' );
  IIRS_0_function_required( 'IIRS_0_http_request' );
  IIRS_0_function_required( 'IIRS_0_HTML_editor' );
  IIRS_0_function_required( 'IIRS_0_translation' );
  IIRS_0_function_required( 'IIRS_0_framework_name' );
}

if (!function_exists('IIRS_0_error_log')) {
  function IIRS_0_error_log( $error_string ) {
    error_log( $error_string );
  }
}

if (!function_exists('IIRS_0_debug')) {
  function IIRS_0_debug() {return TRUE;}
}

//--------------------------------------------------- TIs and Users
if (!function_exists('IIRS_0_details_TI_page')) {
  function IIRS_0_details_TI_page() {
    IIRS_0_set_not_supported_message( 'IIRS_0_details_TI_page' );
    return NULL;
  }
}

if (!function_exists('IIRS_0_TIs_all')) {
  function IIRS_0_TIs_all($page_size = 0, $page_offset = 0) {
    //TODO: use the IIRS_0_TIs_nearby() with unlimited results
    //setting sensible limit of 5000 for performance purposes
    //TODO: admin warning when limit is at 4000
    $TIs = IIRS_0_TIs_nearby(0, 0, '', 5000); // required function
    return $TIs;
  }
}

if (!function_exists('IIRS_0_URL_view_TI')) {
  function IIRS_0_URL_view_TI() {
    IIRS_0_set_not_supported_message( 'IIRS_0_URL_view_TI' );
    return NULL;
  }
}

if (!function_exists('IIRS_0_URL_edit_TI')) {
  function IIRS_0_URL_edit_TI() {
    IIRS_0_set_not_supported_message( 'IIRS_0_URL_edit_TI' );
    return NULL;
  }
}

if (!function_exists('IIRS_0_generate_password')) {
  function IIRS_0_generate_password( $name = NULL ) {
    return substr( str_shuffle( 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789' ), 0, 8 );
  }
}

if ( $IIRS_0_force_complete_framework ) {
  //querying
  IIRS_0_function_required('IIRS_0_TIs_nearby');
  IIRS_0_function_required('IIRS_0_TI_same_name');
  // IIRS_0_function_required('IIRS_0_TIs_viewport');
  IIRS_0_function_required('IIRS_0_details_user');
  IIRS_0_function_required('IIRS_0_details_TI_user');

  //registering
  IIRS_0_function_required('IIRS_0_TI_add_user');
  IIRS_0_function_required('IIRS_0_delete_current_user');
  IIRS_0_function_required('IIRS_0_TI_add_TI');
  IIRS_0_function_required('IIRS_0_TI_update_TI');
  IIRS_0_function_required('IIRS_0_TI_update_user');
  IIRS_0_function_required('IIRS_0_next_initnumber');

  //authentication
  IIRS_0_function_required('IIRS_0_logged_in');
  IIRS_0_function_required('IIRS_0_login');
}
?>
