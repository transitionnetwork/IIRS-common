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
$GoogleAPIKey       = 'AIzaSyCZjrltZvehXP1dnAZCw41NN8VbZCKFf44';
$defaultLat         = 52.1359783;
$defaultLng         = -0.4666513;

//---------------------------------------------------------- directories
//this system accepts a strict URL structure for IIRS calls:
//  /IIRS/<widget_folder>/<page>
$IIRS_common_dir    = __DIR__;                                //var/www/IIRS_common (sym linked on dev)
$IIRS_image_dir     = "$IIRS_common_dir/images";              //var/www/IIRS_common/images (sym linked on dev)
$currentPath        = IIRS_0_current_path();                  // /IIRS/registration/index
//$dirs:
// note that PREG_SPLIT_NO_EMPTY ignores trailing slashes and empty strings
//    [IIRS]
//    [IIRS, registration]
// or [IIRS, registration, index]
//preg_split() PHP 4
$dirs               = preg_split('/[\/\\\\]/mi', $currentPath, -1, PREG_SPLIT_NO_EMPTY);
//fix URL slash issues
if (count($dirs) == 1) {
  //  /IIRS
  array_push($dirs, 'registration');
  $currentPath .= '/registration';
  $debug_environment .= "widget_folder not specified, defaulting to registration procedure: [$currentPath]\n";
}
if (count($dirs) == 2) {
  //  /IIRS/registration
  array_push($dirs, 'index');
  $currentPath .= '/index';
  $debug_environment .= "page not specified, defaulting to index: [$currentPath]\n";
}
$filename           = array_pop($dirs);                       // index or location_summary
$hostDirectory      = implode('/', $dirs);                    // IIRS/registration
$lastDirectory      = array_pop($dirs);                       // registration
$prefixDirectory    = implode('/', $dirs);                    // IIRS
$processGroup       = $lastDirectory;                         // registration
$debug_environment .= "-------------- URL parse\n";
$debug_environment .= "currentPath: $currentPath\n";
$debug_environment .= "hostDirectory: $hostDirectory\n";
$debug_environment .= "lastDirectory: $lastDirectory\n";
$debug_environment .= "filename: $filename\n";
$debug_environment .= "\n";

//---------------------------------------------------------- URLs
//useful URL bases for the various HREFs to IIRS content from the widget scenario
//so that we can make more requests from the same domain
$hostDomain         = $_SERVER['HTTP_HOST'];                  //blah.com $_SERVER PHP >= 4.1.0
$requestProtocol    = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] ? 'https' : 'http');
$is_home_domain     = ($hostDomain == 'transitionnetwork.org'); //debug settings only
$IIRSDomainStem     = "$requestProtocol://$hostDomain";       // http://blah.com
$IIRSURLStem        = "$IIRSDomainStem/$prefixDirectory";     // http://blah.com/IIRS
$IIRSURLCommonStem  = "$IIRSURLStem/IIRS_common";             // http://blah.com/IIRS/IIRS_common
$IIRSURLProcessStem = "$IIRSURLStem/$processGroup";           // http://blah.com/IIRS/registration
$IIRSURLImageStem   = "$IIRSURLStem/images";                  // http://blah.com/IIRS/images

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
$countryDomain  = NULL;
$aWhoIsEntries  = NULL;
$languageDomain = NULL;
$serverCountry  = NULL;

//------- 1) explicit host web-server location setting (plugin mode only)
if (empty($serverCountry) && $IIRS_plugin_mode) {
  $serverCountry = IIRS_0_setting('serverCountry');
  if (!empty($serverCountry)) $debug_environment .= "1) explicit host web-server location setting (plugin mode only): [$serverCountry]\n";
}

//------- 2) host domain whois record
if (empty($serverCountry)) {
  $countryDomain      = $hostDomain;
  if ($IIRS_widget_mode) {
    $httpReferer      = (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NULL);
    $countryDomain    = preg_replace('/^(https?:\/\/)?(www\.)?([^\/?]*).*/i', '$3', $httpReferer);
  }
  /*
  if ($aWhoIsEntries || ($aWhoIsEntries = whois($countryDomain))) {
    if     (in_array($aWhoIsEntries['Registrant Country'], $availableLanguages)) $serverCountry = $aWhoIsEntries['Registrant Country'];
    elseif (in_array($aWhoIsEntries['Admin Country'], $availableLanguages))      $serverCountry = $aWhoIsEntries['Admin Country'];
    elseif (in_array($aWhoIsEntries['Tech Country'], $availableLanguages))       $serverCountry = $aWhoIsEntries['Tech Country'];
  }
  */
  if (!empty($serverCountry)) $debug_environment .= "2) host domain whois record: [$serverCountry]\n";
}

//------- 3) host web-server location usual language (ip location lookup)
if (empty($serverCountry)) {
  $serverCountry = lookupCountryCodeOfIPAddress($countryDomain);
  $debug_environment .= "IIRS_widget_mode: $IIRS_widget_mode, countryDomain: $countryDomain\n";
  $debug_environment .= "serverCountry: $serverCountry\n";
  if (!empty($serverCountry)) $debug_environment .= "3) host web-server location usual language: [$serverCountry]\n";
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
//all of this MUST be filtered through IIRS_0_availableLanguages().
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
global $langList, $langCode, $availableLanguages;
$langList           = getStandardLanguageList();   //FULL list of language codes
$availableLanguages = IIRS_0_availableLanguages(); //e.g. [en, hu, sp] only translations that are available on this server
$langCode           = '';
$langCodeWarning    = NULL; //e.g. the specified language is not available
$debug_environment .= "availableLanguages: [" . implode(',', $availableLanguages) . "]\n";
$debug_environment .= "langList count: [" . count($langList) . "]\n";

//------- 1) widget forced lang-code
if (empty($langCode)) {
  $langCode = IIRS_0_input('langCode'); //?langCode=es
  if (!empty($langCode)) {
    if (!in_array($langCode, $availableLanguages)) {
      $langCodeWarning = "the language you requested [$langCode] is not available (1)";
      $langCode        = '';
    }
    if (!empty($langCode)) $debug_environment .= "1) widget forced lang-code: [$langCode]\n";
  }
}

//------- 2) users laptop language preferences
if (empty($langCode)) {
  if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
    $langCode = getBestMatchingLangcode($_SERVER['HTTP_ACCEPT_LANGUAGE'], $availableLanguages);
    if (!empty($langCode)) $debug_environment .= "2) users laptop language preferences: [$langCode]\n";
  }
}

//------- 3) explicit host web-server language setting (plugin mode only)
if (empty($langCode)) {
  if ($IIRS_plugin_mode) {
    $langCode = IIRS_0_setting('langCode');
    if (!in_array($langCode, $availableLanguages)) {
      $langCodeWarning = "the language you requested [$langCode] is not available (3)";
      $langCode        = '';
    }
    if (!empty($langCode)) $debug_environment .= "3) explicit host web-server language setting: [$langCode]\n";
  }
}

//------- 4) host domain whois record
if (empty($langCode)) {
  $languageDomain     = $hostDomain;
  if ($IIRS_widget_mode) {
    $httpReferer      = (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NULL);
    $languageDomain   = preg_replace('/^(https?:\/\/)?(www\.)?([^\/?]*).*/i', '$3', $httpReferer);
  }
  if ($aWhoIsEntries || ($aWhoIsEntries = whois($languageDomain))) {
    if     (in_array($aWhoIsEntries['Registrant Country'], $availableLanguages)) $langCode = $aWhoIsEntries['Registrant Country'];
    elseif (in_array($aWhoIsEntries['Admin Country'], $availableLanguages))      $langCode = $aWhoIsEntries['Admin Country'];
    elseif (in_array($aWhoIsEntries['Tech Country'], $availableLanguages))       $langCode = $aWhoIsEntries['Tech Country'];
  }
}

//------- 5) host web-server location usual language (ip location lookup)
if (empty($langCode)) {
  $remote_countryCode = lookupCountryCodeOfIPAddress($languageDomain);
  if (in_array($remote_countryCode, $availableLanguages)) $langCode = $remote_countryCode;
  $debug_environment .= "IIRS_widget_mode: $IIRS_widget_mode, languageDomain: $languageDomain\n";
  $debug_environment .= "remote_countryCode: $remote_countryCode\n";
  if (!empty($langCode)) $debug_environment .= "4) host web-server location usual language: [$langCode]\n";
}

//------- 6) default language code: English
if (empty($langCode)) {
  $langCode = 'en';
  if (!empty($langCode)) $debug_environment .= "5) default language code: English: [$langCode]\n";
}

if ($langCodeWarning) $debug_environment .= "$langCodeWarning\n";

//----------------------------------------------------------- development modes
//setup development error reporting
if (!$is_home_domain && false) {
  $debug_environment .= "!is_home_domain: setting error_reporting(E_ALL & !E_STRICT)\n";
  error_reporting(E_ALL | ~E_STRICT | ~E_NOTICE);
  ini_set('display_errors', TRUE);
  ini_set('display_startup_errors', TRUE);
} else $debug_environment .= "is_home_domain: live mode, error_reporting off\n";

//------------------------------------------------------------ component configuration options
$acceptWebsiteAddress = IIRS_0_setting('acceptWebsiteAddress');
$offerBuyDomains      = IIRS_0_setting('offerBuyDomains');
$addProjects          = IIRS_0_setting('addProjects');
$advancedSettings     = IIRS_0_setting('advancedSettings');
?>
