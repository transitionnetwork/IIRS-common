<?php
require_once('framework_abstraction_layer.php');
//hold the debug in a string because some functions output headers later
global $debug_environment;
$debug_environment = '';

//---------------------------------------------------------- misc settings
//?IIRS_widget_mode=true is sent through by the JavaScript widget on all requests
define('IIRS_0_CLEAR_PASSWORD', '****');
$IIRS_widget_mode   = (IIRS_0_input('IIRS_widget_mode') == 'true');
$IIRS_plugin_mode   = !$IIRS_widget_mode;
if ($IIRS_widget_mode) header('Access-Control-Allow-Origin: *'); //allow cross domain AJAX access to this page
$google_API_key       = 'AIzaSyCZjrltZvehXP1dnAZCw41NN8VbZCKFf44';
$default_lat         = 52.1359783;
$default_lng         = -0.4666513;

//---------------------------------------------------------- directories
//this system accepts a strict URL structure for IIRS calls:
//  /IIRS/<widget_folder>/<page>
$IIRS_common_dir    = __DIR__;                                //var/www/IIRS_common (sym linked on dev)
$IIRS_image_dir     = "$IIRS_common_dir/images";              //var/www/IIRS_common/images (sym linked on dev)
$current_path        = IIRS_0_current_path();                  // /IIRS/registration/index
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
$prefix_directory   = implode('/', $dirs);                    // IIRS
$process_group      = $last_directory;                         // registration
$debug_environment .= "-------------- URL parse\n";
$debug_environment .= "currentPath: $current_path\n";
$debug_environment .= "hostDirectory: $host_directory\n";
$debug_environment .= "lastDirectory: $last_directory\n";
$debug_environment .= "filename: $filename\n";
$debug_environment .= "\n";

//---------------------------------------------------------- URLs
//useful URL bases for the various HREFs to IIRS content from the widget scenario
//so that we can make more requests from the same domain
$host_domain           = $_SERVER['HTTP_HOST'];                  //blah.com $_SERVER PHP >= 4.1.0
$request_protocol      = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] ? 'https' : 'http');
$is_home_domain        = ($host_domain == 'transitionnetwork.org'); //debug settings only
$IIRS_domain_stem      = "$request_protocol://$host_domain";       // http://blah.com
$IIRS_URL_stem         = "$IIRS_domain_stem/$prefix_directory";     // http://blah.com/IIRS
$IIRS_URL_common_stem  = "$IIRS_URL_stem/IIRS_common";             // http://blah.com/IIRS/IIRS_common
$IIRS_URL_process_stem = "$IIRS_URL_stem/$process_group";           // http://blah.com/IIRS/registration
$IIRS_URL_image_stem   = "$IIRS_URL_stem/images";                  // http://blah.com/IIRS/images

//----------------------------------------------------------- home server location
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
$country_domain  = NULL;
$whoIs_entries   = NULL;
$language_domain = NULL;
$server_country  = NULL;

//------- 1) explicit host web-server location setting (plugin mode only)
if (empty($server_country) && $IIRS_plugin_mode) {
  $server_country = IIRS_0_setting('serverCountry');
  if (!empty($server_country)) $debug_environment .= "1) explicit host web-server location setting (plugin mode only): [$server_country]\n";
}

//------- 2) host domain whois record
if (empty($server_country)) {
  $country_domain      = $host_domain;
  if ($IIRS_widget_mode) {
    $HTTP_referer      = (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NULL);
    $country_domain    = preg_replace('/^(https?:\/\/)?(www\.)?([^\/?]*).*/i', '$3', $HTTP_referer);
  }
  /*
  if ($whoIs_entries || ($whoIs_entries = whois($country_domain))) {
    if     (in_array($whoIs_entries['Registrant Country'], $available_languages)) $server_country = $whoIs_entries['Registrant Country'];
    elseif (in_array($whoIs_entries['Admin Country'], $available_languages))      $server_country = $whoIs_entries['Admin Country'];
    elseif (in_array($whoIs_entries['Tech Country'], $available_languages))       $server_country = $whoIs_entries['Tech Country'];
  }
  */
  if (!empty($server_country)) $debug_environment .= "2) host domain whois record: [$server_country]\n";
}

//------- 3) host web-server location usual language (ip location lookup)
if (empty($server_country)) {
  $server_country = IIRS_0_lookup_country_code_of_IPaddress($country_domain);
  $debug_environment .= "IIRS_widget_mode: $IIRS_widget_mode, countryDomain: $country_domain\n";
  $debug_environment .= "serverCountry: $server_country\n";
  if (!empty($server_country)) $debug_environment .= "3) host web-server location usual language: [$server_country]\n";
}

//------- 4) Unknown: admin message the installer to carry out (1)
//TODO: server message to do (1)

//----------------------------------------------------------- language selection
//choose which language we want to present:
//  1) widget forced language code
//     e.g. widget loader: http://transitionnetwork.org/IIRS/registration/widgetloader?langCode=hu
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
$lang_list           = getStandardLanguageList();   //FULL list of language codes
$available_languages = IIRS_0_available_languages(); //e.g. [en, hu, sp] only translations that are available on this server
$lang_code           = '';
$lang_code_warning    = NULL; //e.g. the specified language is not available
$debug_environment  .= "availableLanguages: [" . implode(',', $available_languages) . "]\n";
$debug_environment  .= "langList count: [" . count($lang_list) . "]\n";

//------- 1) widget forced lang-code
if (empty($lang_code)) {
  $lang_code = IIRS_0_input('langCode'); //?langCode=es
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
    $lang_code = getBestMatchingLangcode($_SERVER['HTTP_ACCEPT_LANGUAGE'], $available_languages);
    if (!empty($lang_code)) $debug_environment .= "2) users laptop language preferences: [$lang_code]\n";
  }
}

//------- 3) explicit host web-server language setting (plugin mode only)
if (empty($lang_code)) {
  if ($IIRS_plugin_mode) {
    $lang_code = IIRS_0_setting('langCode');
    if (!in_array($lang_code, $available_languages)) {
      $lang_code_warning = "the language you requested [$lang_code] is not available (3)";
      $lang_code        = '';
    }
    if (!empty($lang_code)) $debug_environment .= "3) explicit host web-server language setting: [$lang_code]\n";
  }
}

//------- 4) host domain whois record
if (empty($lang_code)) {
  $language_domain     = $host_domain;
  if ($IIRS_widget_mode) {
    $HTTP_referer      = (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NULL);
    $language_domain   = preg_replace('/^(https?:\/\/)?(www\.)?([^\/?]*).*/i', '$3', $HTTP_referer);
  }
  if ($whoIs_entries || ($whoIs_entries = whois($language_domain))) {
    if     (in_array($whoIs_entries['Registrant Country'], $available_languages)) $lang_code = $whoIs_entries['Registrant Country'];
    elseif (in_array($whoIs_entries['Admin Country'], $available_languages))      $lang_code = $whoIs_entries['Admin Country'];
    elseif (in_array($whoIs_entries['Tech Country'], $available_languages))       $lang_code = $whoIs_entries['Tech Country'];
  }
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
if (!$is_home_domain && false) {
  $debug_environment .= "!is_home_domain: setting error_reporting(E_ALL & !E_STRICT)\n";
  error_reporting(E_ALL | ~E_STRICT | ~E_NOTICE);
  ini_set('display_errors', TRUE);
  ini_set('display_startup_errors', TRUE);
} else $debug_environment .= "is_home_domain: live mode, error_reporting off\n";

//------------------------------------------------------------ component configuration options
$accept_website_address = IIRS_0_setting('accept_website_address');
$offer_buy_domains      = IIRS_0_setting('offer_buy_domains');
$add_projects           = IIRS_0_setting('add_projects');
$advanced_settings      = IIRS_0_setting('advanced_settings');
?>
