<?php
/* Copyright 2015, 2016 Transition Network ltd
 * This program is distributed under the terms of the GNU General Public License
 * as detailed in the COPYING file included in the root of this plugin
 */

require_once( IIRS__COMMON_DIR . 'define.php' );     // IIRS plugin settings fixed for this version
require_once( IIRS__COMMON_DIR . 'IIRS_Error.php' ); // Error object. check for this return with IIRS_is_error( $ret )
require_once( IIRS__COMMON_DIR . 'framework_abstraction_layer.php' );
require_once( IIRS__COMMON_DIR . 'whois.php' );

//hold the debug in a string because some functions output headers later
global $debug_environment;
$debug_environment = '';

//---------------------------------------------------------- misc settings
global $IIRS_common_dir, $IIRS_widget_mode, $IIRS_plugin_mode, $IIRS_0_default_lat, $IIRS_0_default_lng;
$IIRS_widget_mode     = (IIRS_0_input('IIRS_widget_mode') == 'true');
$IIRS_plugin_mode     = ! $IIRS_widget_mode;
$IIRS_0_default_lat   = 52.1359783;
$IIRS_0_default_lng   = -0.4666513;

// ...?IIRS_widget_mode=true is sent through by the JavaScript widget on all requests
// Access-Control-Allow-Origin is required by AJAX calls from teh widget
if ( $IIRS_widget_mode ) header('Access-Control-Allow-Origin: *'); //allow cross domain AJAX access to this page

//---------------------------------------------------------- directories
//this system accepts a strict URL structure for IIRS calls:
//  /IIRS/<widget_folder>/<page>
$IIRS_common_dir    = __DIR__;                                //var/www/IIRS_common (sym linked on dev)
$IIRS_image_dir     = "$IIRS_common_dir/images";              //var/www/IIRS_common/images (sym linked on dev)
$current_path        = IIRS_0_current_path();                 // /IIRS/registration/index
//$dirs:
// note that PREG_SPLIT_NO_EMPTY ignores trailing slashes and empty strings
//    [IIRS]
//    [IIRS, registration]
// or [IIRS, registration, index]
//preg_split() PHP 4
$dirs               = preg_split('/[\/\\\\]/mi', $current_path, -1, PREG_SPLIT_NO_EMPTY);
//fix URL slash issues
if (count($dirs) == 1) {
  //  /IIRS
  array_push($dirs, 'registration');
  $current_path .= '/registration';
  $debug_environment .= "widget_folder not specified, defaulting to registration procedure: [$current_path]\n";
}
if (count($dirs) == 2) {
  //  /IIRS/registration
  array_push($dirs, 'index');
  $current_path .= '/index';
  $debug_environment .= "page not specified, defaulting to index: [$current_path]\n";
}
$filename           = array_pop($dirs);                       // index or location_summary
$host_directory     = implode('/', $dirs);                    // IIRS/registration
$last_directory     = array_pop($dirs);                       // registration
$prefix_directory   = implode('/', $dirs);                    // IIRS or initiative_profile...! or sumink else
$plugin_directory   = 'IIRS';                                 // always IIRS so images can be found in other situs
$process_group      = $last_directory;                        // registration

//---------------------------------------------------------- URLs
//useful URL bases for the various HREFs to IIRS content from the widget scenario
//so that we can make more requests from the same domain
global $IIRS_host_domain, $IIRS_user_ip, $IIRS_user_agent, $IIRS_HTTP_referer, $IIRS_host_TLD;
global $IIRS_is_home_domain, $IIRS_is_dev_domain, $IIRS_is_live_domain;
global $IIRS_domain_stem, $IIRS_URL_stem, $IIRS_URL_common_stem, $IIRS_URL_process_stem, $IIRS_URL_image_stem;
$IIRS_host_domain      = $_SERVER['HTTP_HOST'];                     // blah.com $_SERVER PHP >= 4.1.0
$IIRS_host_parts       = explode( '.', $IIRS_host_domain );
$IIRS_host_TLD         = end( $IIRS_host_parts );                   // com
$IIRS_user_ip          = $_SERVER['REMOTE_ADDR'];                   // 92.160.10.12
$IIRS_user_agent       = $_SERVER['HTTP_USER_AGENT'];               // Mozilla etc.
$IIRS_HTTP_referer     = (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NULL);
$request_protocol      = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] ? 'https' : 'http');
$IIRS_is_home_domain   = ($IIRS_host_domain == 'transitionnetwork.org'); //live setup
$IIRS_is_dev_domain    = ($IIRS_host_domain == 'tnv3.dev');              //debug settings only
$IIRS_is_live_domain   = ( ! $IIRS_is_dev_domain );
$IIRS_domain_stem      = "$request_protocol://$IIRS_host_domain";        // http://blah.com
$IIRS_URL_stem         = "$IIRS_domain_stem/$plugin_directory";     // http://blah.com/IIRS
$IIRS_URL_common_stem  = "$IIRS_URL_stem";                          // http://blah.com/IIRS
$IIRS_URL_process_stem = "$IIRS_URL_stem/$process_group";           // http://blah.com/IIRS/registration
$IIRS_URL_image_stem   = "$IIRS_URL_stem/images";                   // http://blah.com/IIRS/images

//----------------------------------------------------------- debug
$debug_environment .= "-------------- URL parse\n";
$debug_environment .= "plugin_mode: $IIRS_plugin_mode\n";
$debug_environment .= "widget_mode: $IIRS_widget_mode\n";
$debug_environment .= "current_path: $current_path\n";
$debug_environment .= "host_directory: $host_directory\n";
$debug_environment .= "last_directory: $last_directory\n";
$debug_environment .= "prefix_directory: $prefix_directory\n";
$debug_environment .= "plugin_directory: $plugin_directory\n";
$debug_environment .= "process_group: $process_group\n";
$debug_environment .= "IIRS_host_domain: $IIRS_host_domain\n";
$debug_environment .= "IIRS_host_TLD: $IIRS_host_TLD\n";
$debug_environment .= "request_protocol: $request_protocol\n";
$debug_environment .= "IIRS_is_home_domain: $IIRS_is_home_domain\n";
$debug_environment .= "IIRS_is_dev_domain: $IIRS_is_dev_domain\n";
$debug_environment .= "IIRS_URL_common_stem: $IIRS_URL_common_stem\n";
$debug_environment .= "IIRS_URL_process_stem: $IIRS_URL_process_stem\n";
$debug_environment .= "IIRS_URL_image_stem: $IIRS_URL_image_stem\n";
$debug_environment .= "filename: $filename\n";
$debug_environment .= "\n";

//----------------------------------------------------------- home server location
//NOT_CURRENTLY_USED: superceeded by region_bias calculated from the TLD or overridden
//for:
//  country sensitive results when searching a TI's entered town location
//  showing country context results in the TI lists
//  centering the maps on the country location
//auto-detection of country location of this server
//  1) explicit host web-server language setting
//  2) host domain whois record:
//     a) Registrant Country
//     b) Admin Country
//     c) Tech Country
//  3) host web-server location usual language (ip location lookup => language of country)
//     this requires that the web-server is IN the appropriate country!
//     works for plugin (this server) as for JavaScript widget (the referer server)
//  4) Unknown: admin message the installer to carry out (1)
$debug_environment .= "-------------- Language resolution\n";
$country_domain  = NULL;
$whoIs_entries   = NULL;
$language_domain = NULL;
$server_country  = NULL;

/*
//------- 1) explicit host web-server location setting (plugin mode only)
if (empty($server_country) && $IIRS_plugin_mode) {
  $server_country = IIRS_0_setting('server_country');
  if (!empty($server_country)) $debug_environment .= "1) explicit host web-server location setting (plugin mode only): [$server_country]\n";
}

//------- 2) host domain whois record
if (empty($server_country)) {
  $country_domain      = $IIRS_host_domain;
  if ($IIRS_widget_mode) {
    $country_domain    = preg_replace('/^(https?:\/\/)?(www\.)?([^\/?]*).* /i', '$3', $IIRS_HTTP_referer);
  }
  if ($whoIs_entries || ($whoIs_entries = IIRS_0_whois($country_domain))) {
    if     (in_array($whoIs_entries['Registrant Country'], $available_languages)) $server_country = $whoIs_entries['Registrant Country'];
    elseif (in_array($whoIs_entries['Admin Country'], $available_languages))      $server_country = $whoIs_entries['Admin Country'];
    elseif (in_array($whoIs_entries['Tech Country'], $available_languages))       $server_country = $whoIs_entries['Tech Country'];
  }
  if (!empty($server_country)) $debug_environment .= "2) host domain whois record: [$server_country]\n";
}

//------- 3) host web-server location usual language (ip location lookup)
if (empty($server_country)) {
  $server_country = IIRS_0_lookup_country_code_of_IPaddress($country_domain);
  $debug_environment .= "IIRS_widget_mode: $IIRS_widget_mode, countryDomain: $country_domain\n";
  $debug_environment .= "server_country: $server_country\n";
  if (!empty($server_country)) $debug_environment .= "3) host web-server location usual language: [$server_country]\n";
}

//------- 4) Unknown: admin message the installer to carry out (1)
//TODO: server message to do (1)
*/

//----------------------------------------------------------- language selection
//choose which language we want to present:
//  1) widget forced language code
//     e.g. widget loader: http://transitionnetwork.org/IIRS/registration/widgetloader?lang_code=hu
//  2) users laptop preference(s)
//     from HTTP_ACCEPT_LANGUAGE
//  3) explicit host web-server language setting
//     plugin mode only, because the widget should always try to detect (1) can override
//  4) host domain whois record:
//     a) Registrant Country
//     b) Admin Country
//     c) Tech Country
//  5) host web-server location usual language (ip location lookup => language of country)
//     this requires that the web-server is IN the appropriate country!
//     works for plugin (this server) as for JavaScript widget (the referer server)
//  6) default language code: English
//all of this MUST be filtered through IIRS_0_available_languages().
//that is the languages for which this host framework has a translation.
//if the user speaks only Spanish but we do not have a Spanish translation then we must failover to the next option
//
//modes and server locations:
//  JavaScript Widget mode: need to work out where the widget is being hosted and the location / language of that server
//    the HTTP Referer will contain the domain that the JavaScript is running on
//    cache the results in the DB
//  WordPress / Drupal Plugin mode: need to get the location / langauge of this server (where this code is running)
//    WordPress / Drupal Plugin settings should be used to allow overriding
//    TIs will register in that country
//    we have the host domain, but not the public IP
//    we can lookup the public IP for its location
//  Note that $_SERVER['REMOTE_ADDR'] contains the public IP of the *users* laptop
//    their location is not useful to us because it does not indicate the language of the user
//    use $_SERVER['HTTP_ACCEPT_LANGUAGE'] for information on the user's laptop language preferences

//language lists (code from Drupal 8.0)
global $lang_list, $lang_code, $available_languages;
$lang_list           = IIRS_0_getStandardLanguageList();   //FULL list of language codes
$available_languages = IIRS_0_available_languages(); //e.g. [en, hu, sp] only translations that are available on this server
$lang_code           = '';
$lang_code_warning    = NULL; //e.g. the specified language is not available
$debug_environment  .= "availableLanguages: [" . implode(',', $available_languages) . "]\n";
$debug_environment  .= "langList count: [" . count($lang_list) . "]\n";

//------- 1) widget forced lang-code
if (empty($lang_code)) {
  $lang_code = IIRS_0_input('lang_code'); //?lang_code=es
  if (!empty($lang_code)) {
    if (!in_array($lang_code, $available_languages)) {
      $lang_code_warning = "the language you requested [$lang_code] is not available (1)";
      $lang_code        = '';
    }
    if (!empty($lang_code)) $debug_environment .= "1) widget forced lang-code: [$lang_code]\n";
  }
}

//------- 2) users laptop language preferences
if (empty($lang_code)) {
  if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
    $lang_code = IIRS_0_getBestMatchingLangcode($_SERVER['HTTP_ACCEPT_LANGUAGE'], $available_languages);
    if (!empty($lang_code)) $debug_environment .= "2) users laptop language preferences: [$lang_code]\n";
  }
}

//------- 3) explicit host web-server language setting (plugin mode only)
if (empty($lang_code)) {
  if ($IIRS_plugin_mode) {
    $lang_code = IIRS_0_setting('lang_code');
    if (!in_array($lang_code, $available_languages)) {
      $lang_code_warning = "the language you requested [$lang_code] is not available (3)";
      $lang_code        = '';
    }
    if (!empty($lang_code)) $debug_environment .= "3) explicit host web-server language setting: [$lang_code]\n";
  }
}

//------- 4) host domain whois record
if (empty($lang_code)) {
  $language_domain     = $IIRS_host_domain;
  if ($IIRS_widget_mode) {
    $IIRS_HTTP_referer = (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NULL);
    $language_domain   = preg_replace('/^(https?:\/\/)?(www\.)?([^\/?]*).*/i', '$3', $IIRS_HTTP_referer);
  }

  /* WHOIS lookups are disabled at the moment
  if ($whoIs_entries || ($whoIs_entries = IIRS_0_whois($language_domain))) {
    if ( $whoIs_entries && ! IIRS_is_error( $whoIs_entries ) ) {
      if     (in_array($whoIs_entries['Registrant Country'], $available_languages)) $lang_code = $whoIs_entries['Registrant Country'];
      elseif (in_array($whoIs_entries['Admin Country'], $available_languages))      $lang_code = $whoIs_entries['Admin Country'];
      elseif (in_array($whoIs_entries['Tech Country'], $available_languages))       $lang_code = $whoIs_entries['Tech Country'];
    }
  }
  */
}

//------- 5) host web-server location usual language (ip location lookup)
if (empty($lang_code)) {
  $remote_countryCode = IIRS_0_lookup_country_code_of_IPaddress($language_domain);
  if (in_array($remote_countryCode, $available_languages)) $lang_code = $remote_countryCode;
  $debug_environment .= "IIRS_widget_mode: $IIRS_widget_mode, languageDomain: $language_domain\n";
  $debug_environment .= "remote_countryCode: $remote_countryCode\n";
  if (!empty($lang_code)) $debug_environment .= "4) host web-server location usual language: [$lang_code]\n";
}

//------- 6) default language code: English
if (empty($lang_code)) {
  $lang_code = 'en';
  if (!empty($lang_code)) $debug_environment .= "5) default language code: English: [$lang_code]\n";
}

if ($lang_code_warning) $debug_environment .= "$lang_code_warning\n";

//----------------------------------------------------------- development modes
//setup development error reporting
if ($IIRS_is_dev_domain) {
  $debug_environment .= "!is_home_domain: setting error_reporting(E_ALL & !E_STRICT)\n";
  error_reporting(E_ALL | ~E_STRICT | ~E_NOTICE);
  ini_set('display_errors', TRUE);
  ini_set('display_startup_errors', TRUE);
} else $debug_environment .= "is_home_domain: live mode, error_reporting off\n";
?>
