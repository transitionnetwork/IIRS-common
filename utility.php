<?php
// --------------------------------------------- SECURITY: escaping
// TODO: move all these in to a separate SECURITY file
function IIRS_0_htmlentities( $string ) {
  $value = NULL;
  if     ( is_string($string) )  $value = htmlentities( $string );
  elseif ( is_numeric($string) ) $value = $string;

  return $value;
}
function IIRS_0_escape_for_HTML_form_value( $string ) { return IIRS_0_htmlentities( $string ); }
function IIRS_0_escape_for_HTML_image_src( $string )  { return IIRS_0_htmlentities( $string ); }
function IIRS_0_escape_for_HTML_href( $string )       { return IIRS_0_htmlentities( $string ); }
function IIRS_0_escape_for_HTML_text( $string )       { return IIRS_0_htmlentities( $string ); }
function IIRS_0_escape_for_HTML_class( $string )      { return IIRS_0_htmlentities( $string ); }

function IIRS_0_escape_for_javascript_string_value( $string ) {
  // SECURITY: htmlentities( $string ); however would escape valid content
  // SECURITY: we are writing dynamic strings to the webpage here
  // SECURITY: some of them are related to the URL typed in by the User
  // $string could contain newline and single quotes allowing abritary XSS execution of JavaScript
  $string_escaped = NULL;

  // json_encode PHP >= 5.2
  // utf8_encode PHP >= 4.0
  // json_encode templorarily disabled because it places quotes around everything
  // if ( function_exists( 'json_encode' ) ) $string_escaped = json_encode( utf8_encode( $string ) );
  // else {
    $string_escaped = utf8_encode( $string );
    $string_escaped = str_replace( "'", "\\'", $string_escaped );
    $string_escaped = str_replace( '"', '\"',  $string_escaped );
    $string_escaped = str_replace( "\n", '\n', $string_escaped );
  // }
  return $string_escaped;
}

// ------------------------------------------------------- direct print functions
function IIRS_0_print( $string ) {
  // it is a PRIVATE function, use the context functions below
  // this is the *only* function where print or echo statements are permitted
  print( $string );
}

function IIRS_0_print_HTML( $string )            {
  // SECURITY: this is a dangerous function: the only direct HTML non-escaped output
  // SECURITY: remove XSS style script tags
  // someone can register a TI with <script> tags in that could appear anywhere
  // because we have a HTML editor summary $_POST entry
  $string = preg_replace( '/<script.+\/>/smi', '', $string );
  $string = preg_replace( '/<script/i', '', $string );
  IIRS_0_print( $string );
}
function IIRS_0_print_HTML_form_value( $string ) { IIRS_0_print( IIRS_0_escape_for_HTML_form_value( $string ) ); }
function IIRS_0_print_HTML_image_src( $string )  { IIRS_0_print( IIRS_0_escape_for_HTML_image_src( $string ) ); }
function IIRS_0_print_HTML_href( $string )       { IIRS_0_print( IIRS_0_escape_for_HTML_href( $string ) ); }
function IIRS_0_print_HTML_text( $string )       { IIRS_0_print( IIRS_0_escape_for_HTML_text( $string ) ); }
function IIRS_0_print_HTML_class( $string )      { IIRS_0_print( IIRS_0_escape_for_HTML_class( $string ) ); }
function IIRS_0_print_XML_doc( $doc )            { IIRS_0_print( $doc->saveXML() ); }

function IIRS_0_print_HTML_encode_array( $values ) {
  // used in mapping to transport the array settings to the HTML layer
  // JavaScript then picks up the HTML data and places it on the map
  foreach ($values as $key => $value) {
    IIRS_0_print('<div class="IIRS_0_HTML_data ' . IIRS_0_escape_for_HTML_class( $key ) . '">' . IIRS_0_escape_for_HTML_text( $value ) . '</div>');
  }
}

function IIRS_0_print_javascript_variable($name, $string) {
  // SECURITY: we are writing dynamic strings to the webpage here
  // SECURITY: some of them are related to the URL typed in by the User
  // $string could contain newline and single quotes allowing abritary XSS execution of JavaScript
  // $name is hardcoded in to the code and should not be user data so it is not escaped
  IIRS_0_print( 'var ' . $name . "='" . IIRS_0_escape_for_javascript_string_value( $string ) . "';\n" );
}

function IIRS_0_printEncodedPostParameters() {
  // copy all POST and GET values in to a new form
  // using hidden inputs
  // note that WordPress changes some inputs cleverly, like user id to the WP_User object
  //   this is checked for in the IIRS_0_escape_for_HTML_form_value call
  IIRS_0_print('<!-- auto passed form -->');
  foreach ( array_merge( $_POST, $_GET ) as $key => $value ) {
    if ( is_string( $value ) ) {
      $value_escaped = IIRS_0_escape_for_HTML_form_value( IIRS_0_input( $key, IIRS_RAW_USER_INPUT ) );
      IIRS_0_print('<input name="' . IIRS_0_escape_for_HTML_form_value( $key ) . '" value="' . $value_escaped . '" type="hidden" />');
    }
  }
  IIRS_0_print('<!-- /auto passed form -->');
  IIRS_0_print("\n\n");
}

function IIRS_0_debug_print( $string ) {
  // usually within <pre>
  // but standard html text output
  // thus all html entities should be escaped
  if ( IIRS_0_debug() ) IIRS_0_print_HTML_text( "$string\n" );
}

function IIRS_0_debug_var_dump( $object ) {
  // var_dump is NOT safe to output with
  // array_walk_recursive PHP >= 5.3
  if ( IIRS_0_debug() && function_exists( 'array_walk_recursive' ) ) {
    $array_object = array( $object );
    array_walk_recursive( $array_object, function(&$v) {
      if ( is_string( $v) )   $v = htmlspecialchars($v);
    });
    var_dump( $array_object );
  }
}

function IIRS_0_debug_print_inputs() {
  if ( IIRS_0_debug() ) {
    IIRS_0_debug_print( "------------- POST" );
    foreach ( $_POST as $key => $value ) {
      IIRS_0_debug_print( "  [$key] = $value" );
    }
  }
}

//--------------------------------------------- print with translation
function IIRS_0_print_translated_HTML_text( $string ) {
  IIRS_0_print_HTML_text( IIRS_0_translation( $string ) );
}

function IIRS_0_print_translated_javascript_variable( $name, $text ) {
  IIRS_0_print_javascript_variable( $name, IIRS_0_translation( $text ) );
}

function IIRS_0_set_message_translated( $mess_no, $message, $message_detail = NULL, $level = IIRS_MESSAGE_USER_INFORMATION, $user_action = null, $args = null ) {
  IIRS_0_set_message( $mess_no, IIRS_0_translation( $message ), IIRS_0_translation( $message_detail ), $level, $user_action, $args );
  // message type should be checked by caller
  // if ( is_string( $object ) ) IIRS_0_set_message( $mess_no, IIRS_0_translation( $object ), $level );
  // elseif ( IIRS_is_error( $object ) ) IIRS_0_set_translated_error_message( $object );
}

function IIRS_0_set_translated_error_message( $IIRS_error ) {
  return IIRS_0_set_message( $IIRS_error->err_no, IIRS_0_translation( $IIRS_error->friendly_err_message ), $IIRS_error->technical_err_message, $IIRS_error->level, $IIRS_error->user_action, $IIRS_error->args );
}

//--------------------------------------------- misc
function IIRS_0_function_required( $function_name ) {
  if ( ! function_exists( $function_name ) ) {
    IIRS_0_debug_print( "$function_name() required" );
    exit( 1 );
  }
}

function IIRS_0_set_not_supported_message( $function_name ) {
  // recoverable lack of function
  // but notify programmer that this could be implemented to improve functionality
  // use IIRS_0_function_required() for fatal requirement
  IIRS_0_debug_print( "[$function_name] not supported currently (non-fatal)" );
  return FALSE;
}

function IIRS_0_remove_transition_words( $town_name ) {
  // TODO: IIRS_0_remove_transition_words: currently these are only English words to replace, make it configurable
  $town_name_stub = str_ireplace( 'InTransition', '', $town_name );
  $town_name_stub = str_ireplace( 'Transition',   '', $town_name_stub );
  $town_name_stub = str_ireplace( 'Towns',        '', $town_name_stub );
  return $town_name_stub;
}

function IIRS_0_get_DOM_value( $startNode, $xpathString ) {
  $ret      = '';
  $oXPath   = new DOMXpath( $startNode->ownerDocument );
  $nodelist = $oXPath->query( $xpathString, $startNode );
  if ( $nodelist->length ) {
    $ret = $nodelist->item(0)->textContent;
  }
  return $ret;
}

function IIRS_0_sort_date_desc( $a, $b ) { return $a['date'] < $b['date']; }

function IIRS_0_message_class( $level ) {
  $class = 'unknown';
  switch ( $level ) {
    case IIRS_MESSAGE_USER_INFORMATION:      { $class = 'information';  break; }
    case IIRS_MESSAGE_USER_WARNING:          { $class = 'warning';      break; }
    case IIRS_MESSAGE_USER_ERROR:            { $class = 'error';        break; }
    case IIRS_MESSAGE_SYSTEM_ERROR:          { $class = 'system-error'; break; }
    case IIRS_MESSAGE_EXTERNAL_SYSTEM_ERROR: { $class = 'external-system-error'; break; }
    default: { $class = 'unregistered-message-level'; }
  }
  return $class;
}

function IIRS_0_message_html( $mess_no, $message, $message_detail = null, $level = IIRS_MESSAGE_USER_INFORMATION, $user_action = null, $args = null ) {
  // SECURITY: $message is text, NOT HTML. it will be pushed through IIRS_0_escape_for_HTML_text()
  // the caller should NOT escape the input
  // IIRS_0_message_html() output should be pushed through IIRS_0_print_HTML()
  // $message is also NOT translated. the caller must use the translation functions
  // this is because the message may comprise of several separately translated parts
  $class = IIRS_0_message_class( $level );
  $html  = "<div class=\"IIRS_0_message IIRS_Error IIRS_Error_$mess_no IIRS_0_message_level_$class\">";
  if ( $user_action ) $html .= '<a class="IIRS_0_user_action" href="' . $user_action . '">' . IIRS_0_translation( 'continue' ) . '</a>';
  if ( $message )     $html .= '<div class="IIRS_friendly_err_message">' . IIRS_0_escape_for_HTML_text( $message ) . '</div>';
  $html .= '<div class="IIRS_message_detail">';
  $html .= IIRS_0_translation( $message_detail );
  if ( $args ) {
    $html .= '<ul>';
    foreach ( $args as $key => $value ) $html .= "<li>$key = $value</li>";
    $html .= '</ul>';
  }
  $html .= '</div>';
  $html .= '</div>';

  return $html;
}

//--------------------------------------------- IIRS helpers
function IIRS_0_TI_verify_add_TI($user_ID, $IIRS_host_domain, $initiative_name, $town_name, $location_latitude, $location_longitude, $location_description, $location_country, $location_full_address, $location_granuality, $location_bounds, $domain) {
  // on success returns the native TI ID
  // on failure returns NULL
  $ret = null;

  // --------------------------------------- 1) check data input validity
  // the user is already registered, so we can use IIRS_0_details_user()
  // IIRS_0_akismet_check_ti_registration_name() returns true for is SPAM
  // TODO: concurrency issues with registration
  if ( ! is_numeric( $location_latitude ) || ! is_numeric( $location_longitude ) ) {
    $ret = new IIRS_Error( IIRS_INVALID_TI_INPUTS, 'We think you are a SPAM robot. please email us to resolve this issue.', 'location data is not numeric', IIRS_MESSAGE_USER_ERROR );
  }

  // --------------------------------------- 2) check user 1-1 TI relation
  if ( ! $ret ) {
    $TI = IIRS_0_details_TI_user(); // returns NULL if no TI associated
    if ( is_array( $TI ) ) {
      $ret = new IIRS_Error( IIRS_USER_ALREADY_HAS_TI, 'You have already registered a Transition Initiative under this username. Please logout and re-register', "User already associated with TI [$TI[name]]", IIRS_MESSAGE_USER_ERROR );
    } elseif ( IIRS_is_error( $TI ) ) {
      $ret = $TI;
    }
  }

  // --------------------------------------- 3) check duplicate registration by vicinity
  if ( ! $ret ) {
    $vicinity_match = IIRS_0_TI_vicinity_match( $location_latitude, $location_longitude, $location_description );
    if ( $vicinity_match === TRUE || IIRS_is_error( $vicinity_match ) ) $ret = $vicinity_match;
  }

  // --------------------------------------- 4) check duplicate TI name
  if ( ! $ret ) {
    $TI_same_name  = IIRS_0_TI_same_name( $initiative_name ); // returns a TI or FALSE or [system] IIRS_Error
    if ( is_array( $TI_same_name ) ) {
      // ok, so we have a registration with an identical name
      // same name entries are not necessarily close by. America and UK will have many name conflicts
      // reject this currently. ask for an alternative name
      // this might cause the user to experience surprise, anger, competition.
      // needs to be handled with emotional intelligence, i.e.:
      //   hey! we've found someone you can chat and work with in your space!
      //   OR
      //   someone OWNS this name and you are not permitted to create an Initiative here.
      IIRS_0_debug_var_dump( $TI_same_name );
      $ret = new IIRS_Error( IIRS_TI_EXISTS_SAME_NAME, 'A Transition Initiative already exists with this name. Please add something to the name or change it and try again', 'TI Name matched exactly in data verification stage', IIRS_MESSAGE_USER_WARNING );
    } elseif ( IIRS_is_error( $TI_same_name ) ) {
      $ret = $TI_same_name;
    }
  }

  // --------------------------------------- 5) check for SPAM using AKISMET
  if ( ! $ret ) {
    $is_SPAM = IIRS_0_akismet_check_ti_registration_name( IIRS_0_details_user(), $initiative_name );
    if ( $is_SPAM === TRUE || IIRS_is_error( $is_SPAM ) ) $ret = $is_SPAM;
  }

  // --------------------------------------- F) FINAL: if no vertification issues registrered then everything ok
  if ( ! $ret ) {
    // ask host framework to add the TI
    $ret = IIRS_0_TI_add_TI( $user_ID, $IIRS_host_domain, $initiative_name, $town_name, $location_latitude, $location_longitude, $location_description, $location_country, $location_full_address, $location_granuality, $location_bounds, $domain );
  }

  return $ret;
}

function IIRS_0_TI_search_result_already_registered( $town_name, $location_latitude, $location_longitude, $location_description ) {
  // this function is for checking search results matching against th database
  // not for full user data input: use IIRS_0_TI_verify_add_TI( ... ) for that
  $vicinity_match = IIRS_0_TI_vicinity_match( $location_latitude, $location_longitude, $location_description );
  return $vicinity_match;
}

function IIRS_0_TI_vicinity_match( $location_latitude, $location_longitude, $location_description ) {
  // returns TRUE or FALSE
  // $geokml, $location_description = potential matches to test for against the database
  // TIs may have identical location but different areas of interest.
  //   e.g. town vs. county areas but with same lat / lng
  $registered    = FALSE;

  $TIs_nearby    = IIRS_0_TIs_nearby( $location_latitude, $location_longitude, $location_description );

  foreach ( $TIs_nearby as $town_name => $aDetails ) {
    /*
    // compare the new TI with the nearby ones to decide if this registration is acceptable
    // policy level decisions are needed to decide what duplicates are and are not acceptable
    // currently this is disabled awaiting those decisions
    // i think that all registrations should be accepted, even in identical locations but names should be unique
    // nearby or identical locations should be encouraged to cooperate, not compete for ownership of space
    // ownership of space is an abstract human concept. now, back to work :D
    if ( isset($aDetails['location_description'] ) ) {
      if ( strcasecmp($aDetails['location_description'], $location_description ) ) $registered = true;
    }

    // try to asses how close / similar the areas are
    if ( isset($aDetails['geokml'] ) ) {
      $geokml = $aDetails['geokml'];
      //TODO: area comparison
      if (false) $registered = TRUE;
    }
    */
  }

  return $registered;
}


//--------------------------------------------- language detection functions
function IIRS_0_language_is_supported() {
  return in_array( IIRS_0_locale(), IIRS_0_available_languages() );
}

function IIRS_0_print_language_selector() {
  global $lang_list, $lang_code, $available_languages;

  if ( IIRS_0_setting( 'language_selector' ) ) {
    $html = '<select id="IIRS_0_language_control">';
    foreach ($available_languages as $code) {
      if (isset($lang_list[$code])) {
        $details  = $lang_list[$code];
        $selected = ($code == $lang_code ? 'selected' : '');
        $html    .= "<option $selected value=\"$code\">$details[1]</option>";
      }
    }
    $html .= '</select>';
    IIRS_0_print( $html );
  }
}

function IIRS_0_lookup_country_code_of_IPaddress($domain) {
  //now get the public ip address of this domain (either this server, or the referer)
  $hostIP             = gethostbyname($domain); //gethostbyname() PHP 4.0
  //host web-server IP caller location
  //NOTE: it will be the loopback when testing on my laptop
  //TODO: we may be able to download a copy of this geo-IP-DB to speed up requests
  //http://ipinfo.io/developers
  //  "You are limited to 1,000 API requests per day. If you need to make more requests, or need SSL support, see our paid plans."
  if ($hostIP == '127.0.0.1') $hostIP = '46.107.207.154';
  $ipinfo = IIRS_0_http_request("http://ipinfo.io/$hostIP/json");
  //we are not using json_decode() here in order to maintain backward compatability to PHP 4
  //preg_match('/"city": "([^"]+)"/mi',    $ipinfo, $matchesCity);
  //preg_match('/"region": "([^"]+)"/mi',  $ipinfo, $matchesRegion);
  preg_match('/"country": "([^"]+)"/mi', $ipinfo, $matchesCountryCode);
  //$remote_city        = $matchesCity[1];
  //$remote_region      = $matchesRegion[1];
  return ($matchesCountryCode && isset($matchesCountryCode[1]) ? strtolower($matchesCountryCode[1]) : NULL);
}

// this is copied from Drupal 8.0 UserAgent Class code
// file:///var/www/drupal/drupal-8.0-alpha13/core/lib/Drupal/Component/Utility/UserAgent.php
// function names are sandboxed in to the IIRS_ namespace
function IIRS_0_getBestMatchingLangcode($http_accept_language, $langcodes, $mappings = array()) {
  // The Accept-Language header contains information about the language
  // preferences configured in the user's user agent / operating system.
  // RFC 2616 (section 14.4) defines the Accept-Language header as follows:
  //   Accept-Language = "Accept-Language" ":"
  //                  1#( language-range [ ";" "q" "=" qvalue ] )
  //   language-range  = ( ( 1*8ALPHA *( "-" 1*8ALPHA ) ) | "*" )
  // Samples: "hu, en-us;q=0.66, en;q=0.33", "hu,en-us;q=0.5"
  $ua_langcodes = array();
  if (preg_match_all('@(?<=[, ]|^)([a-zA-Z-]+|\*)(?:;q=([0-9.]+))?(?:$|\s*,\s*)@', trim($http_accept_language), $matches, PREG_SET_ORDER)) {
    foreach ($matches as $match) {
      if ($mappings) {
        $langcode = strtolower($match[1]);
        foreach ($mappings as $ua_langcode => $standard_langcode) {
          if ($langcode == $ua_langcode) {
            $match[1] = $standard_langcode;
          }
        }
      }
      // We can safely use strtolower() here, tags are ASCII.
      // RFC2616 mandates that the decimal part is no more than three digits,
      // so we multiply the qvalue by 1000 to avoid floating point
      // comparisons.
      $langcode = strtolower($match[1]);
      $qvalue = isset($match[2]) ? (float) $match[2] : 1;
      // Take the highest qvalue for this langcode. Although the request
      // supposedly contains unique langcodes, our mapping possibly resolves
      // to the same langcode for different qvalues. Keep the highest.
      $ua_langcodes[$langcode] = max(
        (int) ($qvalue * 1000),
        (isset($ua_langcodes[$langcode]) ? $ua_langcodes[$langcode] : 0)
      );
    }
  }

  // We should take pristine values from the HTTP headers, but Internet
  // Explorer from version 7 sends only specific language tags (eg. fr-CA)
  // without the corresponding generic tag (fr) unless explicitly configured.
  // In that case, we assume that the lowest value of the specific tags is the
  // value of the generic language to be as close to the HTTP 1.1 spec as
  // possible.
  // See http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.4 and
  // http://blogs.msdn.com/b/ie/archive/2006/10/17/accept-language-header-for-internet-explorer-7.aspx
  asort($ua_langcodes);
  foreach ($ua_langcodes as $langcode => $qvalue) {
    // For Chinese languages the generic tag is either zh-hans or zh-hant, so
    // we need to handle this separately, we can not split $langcode on the
    // first occurrence of '-' otherwise we get a non-existing language zh.
    // All other languages use a langcode without a '-', so we can safely
    // split on the first occurrence of it.
    if (strlen($langcode) > 7 && (substr($langcode, 0, 7) == 'zh-hant' || substr($langcode, 0, 7) == 'zh-hans')) {
      $generic_tag = substr($langcode, 0, 7);
    }
    else {
      $generic_tag = strtok($langcode, '-');
    }
    if (!empty($generic_tag) && !isset($ua_langcodes[$generic_tag])) {
      // Add the generic langcode, but make sure it has a lower qvalue as the
      // more specific one, so the more specific one gets selected if it's
      // defined by both the user agent and us.
      $ua_langcodes[$generic_tag] = $qvalue - 0.1;
    }
  }

  // Find the added language with the greatest qvalue, following the rules
  // of RFC 2616 (section 14.4). If several languages have the same qvalue,
  // prefer the one with the greatest weight.
  $best_match_langcode = FALSE;
  $max_qvalue = 0;
  foreach ($langcodes as $langcode_case_sensitive) {
    // Language tags are case insensitive (RFC2616, sec 3.10).
    $langcode = strtolower($langcode_case_sensitive);

    // If nothing matches below, the default qvalue is the one of the wildcard
    // language, if set, or is 0 (which will never match).
    $qvalue = isset($ua_langcodes['*']) ? $ua_langcodes['*'] : 0;

    // Find the longest possible prefix of the user agent supplied language
    // ('the language-range') that matches this site language ('the language
    // tag').
    $prefix = $langcode;
    do {
      if (isset($ua_langcodes[$prefix])) {
        $qvalue = $ua_langcodes[$prefix];
        break;
      }
    }
    while ($prefix = substr($prefix, 0, strrpos($prefix, '-')));

    // Find the best match.
    if ($qvalue > $max_qvalue) {
      $best_match_langcode = $langcode_case_sensitive;
      $max_qvalue = $qvalue;
    }
  }

  return $best_match_langcode;
}


//this is copied from Drupal 8.0 LanguageManager Class code
//file:///var/www/drupal/drupal-8.0-alpha13/core/lib/Drupal/Core/Language/LanguageManager.php
//replaced all occurrences of LanguageInterface::*
function IIRS_0_getStandardLanguageList() {
  return array(
    'af' => array('Afrikaans', 'Afrikaans'),
    'am' => array('Amharic', 'አማርኛ'),
    'ar' => array('Arabic', /* Left-to-right marker "‭" */ 'العربية'), //, LanguageInterface::DIRECTION_RTL),
    'ast' => array('Asturian', 'Asturianu'),
    'az' => array('Azerbaijani', 'Azərbaycanca'),
    'be' => array('Belarusian', 'Беларуская'),
    'bg' => array('Bulgarian', 'Български'),
    'bn' => array('Bengali', 'বাংলা'),
    'bo' => array('Tibetan', 'བོད་སྐད་'),
    'bs' => array('Bosnian', 'Bosanski'),
    'ca' => array('Catalan', 'Català'),
    'cs' => array('Czech', 'Čeština'),
    'cy' => array('Welsh', 'Cymraeg'),
    'da' => array('Danish', 'Dansk'),
    'de' => array('German', 'Deutsch'),
    'dz' => array('Dzongkha', 'རྫོང་ཁ'),
    'el' => array('Greek', 'Ελληνικά'),
    'en' => array('English', 'English'),
    'eo' => array('Esperanto', 'Esperanto'),
    'es' => array('Spanish', 'Español'),
    'et' => array('Estonian', 'Eesti'),
    'eu' => array('Basque', 'Euskera'),
    'fa' => array('Persian, Farsi', /* Left-to-right marker "‭" */ 'فارسی'), //, LanguageInterface::DIRECTION_RTL),
    'fi' => array('Finnish', 'Suomi'),
    'fil' => array('Filipino', 'Filipino'),
    'fo' => array('Faeroese', 'Føroyskt'),
    'fr' => array('French', 'Français'),
    'fy' => array('Frisian, Western', 'Frysk'),
    'ga' => array('Irish', 'Gaeilge'),
    'gd' => array('Scots Gaelic', 'Gàidhlig'),
    'gl' => array('Galician', 'Galego'),
    'gsw-berne' => array('Swiss German', 'Schwyzerdütsch'),
    'gu' => array('Gujarati', 'ગુજરાતી'),
    'he' => array('Hebrew', /* Left-to-right marker "‭" */ 'עברית'), //, LanguageInterface::DIRECTION_RTL),
    'hi' => array('Hindi', 'हिन्दी'),
    'hr' => array('Croatian', 'Hrvatski'),
    'ht' => array('Haitian Creole', 'Kreyòl ayisyen'),
    'hu' => array('Hungarian', 'Magyar'),
    'hy' => array('Armenian', 'Հայերեն'),
    'id' => array('Indonesian', 'Bahasa Indonesia'),
    'is' => array('Icelandic', 'Íslenska'),
    'it' => array('Italian', 'Italiano'),
    'ja' => array('Japanese', '日本語'),
    'jv' => array('Javanese', 'Basa Java'),
    'ka' => array('Georgian', 'ქართული ენა'),
    'kk' => array('Kazakh', 'Қазақ'),
    'km' => array('Khmer', 'ភាសាខ្មែរ'),
    'kn' => array('Kannada', 'ಕನ್ನಡ'),
    'ko' => array('Korean', '한국어'),
    'ku' => array('Kurdish', 'Kurdî'),
    'ky' => array('Kyrgyz', 'Кыргызча'),
    'lo' => array('Lao', 'ພາສາລາວ'),
    'lt' => array('Lithuanian', 'Lietuvių'),
    'lv' => array('Latvian', 'Latviešu'),
    'mg' => array('Malagasy', 'Malagasy'),
    'mk' => array('Macedonian', 'Македонски'),
    'ml' => array('Malayalam', 'മലയാളം'),
    'mn' => array('Mongolian', 'монгол'),
    'mr' => array('Marathi', 'मराठी'),
    'ms' => array('Bahasa Malaysia', 'بهاس ملايو'),
    'my' => array('Burmese', 'ဗမာစကား'),
    'ne' => array('Nepali', 'नेपाली'),
    'nl' => array('Dutch', 'Nederlands'),
    'nb' => array('Norwegian Bokmål', 'Bokmål'),
    'nn' => array('Norwegian Nynorsk', 'Nynorsk'),
    'oc' => array('Occitan', 'Occitan'),
    'pa' => array('Punjabi', 'ਪੰਜਾਬੀ'),
    'pl' => array('Polish', 'Polski'),
    'pt-pt' => array('Portuguese, Portugal', 'Português, Portugal'),
    'pt-br' => array('Portuguese, Brazil', 'Português, Brasil'),
    'ro' => array('Romanian', 'Română'),
    'ru' => array('Russian', 'Русский'),
    'sco' => array('Scots', 'Scots'),
    'se' => array('Northern Sami', 'Sámi'),
    'si' => array('Sinhala', 'සිංහල'),
    'sk' => array('Slovak', 'Slovenčina'),
    'sl' => array('Slovenian', 'Slovenščina'),
    'sq' => array('Albanian', 'Shqip'),
    'sr' => array('Serbian', 'Српски'),
    'sv' => array('Swedish', 'Svenska'),
    'sw' => array('Swahili', 'Kiswahili'),
    'ta' => array('Tamil', 'தமிழ்'),
    'ta-lk' => array('Tamil, Sri Lanka', 'தமிழ், இலங்கை'),
    'te' => array('Telugu', 'తెలుగు'),
    'th' => array('Thai', 'ภาษาไทย'),
    'tr' => array('Turkish', 'Türkçe'),
    'tyv' => array('Tuvan', 'Тыва дыл'),
    'ug' => array('Uyghur', 'Уйғур'),
    'uk' => array('Ukrainian', 'Українська'),
    'ur' => array('Urdu', /* Left-to-right marker "‭" */ 'اردو'), //, LanguageInterface::DIRECTION_RTL),
    'vi' => array('Vietnamese', 'Tiếng Việt'),
    'xx-lolspeak' => array('Lolspeak', 'Lolspeak'),
    'zh-hans' => array('Chinese, Simplified', '简体中文'),
    'zh-hant' => array('Chinese, Traditional', '繁體中文'),
  );
}
?>
