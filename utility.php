<?php
//--------------------------------------------- forms
function IIRS_0_printEncodedPostParameters() {
  //copy all POST and GET values in to a new form
  //using hidden inputs
  print('<!-- auto passed form -->');
  foreach ($_POST as $key => $value) print('<input name="' . IIRS_0_escape_for_form_value($key) . '" value="' . IIRS_0_escape_for_form_value($value) . '" type="hidden" />');
  foreach ($_GET  as $key => $value) print('<input name="' . IIRS_0_escape_for_form_value($key) . '" value="' . IIRS_0_escape_for_form_value($value) . '" type="hidden" />');
  print('<!-- /auto passed form -->');
  print("\n\n");
}
function IIRS_0_escape_for_form_value($sString) {
  //Wordpress may encode some $_POST variables as WP_User Objects sometimes
  //the escaped output will come here
  return is_string($sString) ? str_replace("'", '&#39;', str_replace('"', '&quot;', $sString)) : "";
}
function IIRS_0_debug_print_inputs() {
  var_dump($_POST);
}

//--------------------------------------------- translation, printing and escaping functions
function IIRS_0_print_translation($sString, $escape = false) {
  $sTString = IIRS_0_translation($sString);
  if ($escape) print(IIRS_0_escape_for_javascript($sTString));
  else print($sTString);
}
function IIRS_0_escape_for_javascript($sString) {
  return str_replace("'", "\\'", $sString);
}
function IIRS_0_print_javascript_variable($sName, $sString) {
  print('var ' . $sName . "='" . IIRS_0_escape_for_javascript($sString) . "';\n");
}
function IIRS_0_print_translated_javascript_variable($sName, $sText) {
  IIRS_0_print_javascript_variable($sName, IIRS_0_translation($sText));
}
function IIRS_0_print_javascript_array($aArray) {
  print('[');
  foreach ($aArray as $key => $value) {
    $key_escaped = IIRS_0_escape_for_javascript($key);
    print("'$key_escaped':");
    if (is_array($value)) {
      $value_escaped = IIRS_0_print_javascript_array($value);
      print("$value_escaped,");
    } else {
      $value_escaped = IIRS_0_escape_for_javascript($value);
      print("'$value_escaped',");
    }
  }
  print(']');
}
function IIRS_0_register_translation($sString) {
  $sTranslationID    = 'IIRS_translation_' . preg_replace('/[^a-z0-9]/i', '_', $sString);
  $sTranslatedString = IIRS_0_translation($sString);
  print("<div style=\"display:none;\" id=\"$sTranslationID\">$sTranslatedString</div>");
}

function IIRS_0_set_message_translated($sString, $IIRS_widget_mode = true) {
  return IIRS_0_set_message(IIRS_0_translation($sString, $IIRS_widget_mode));
}

//--------------------------------------------- misc
function IIRS_0_remove_transition_words($sTownName) {
  $sTownNameStub = str_ireplace('InTransition', '', $sTownName);
  $sTownNameStub = str_ireplace('Transition',   '', $sTownNameStub);
  $sTownNameStub = str_ireplace('Towns',        '', $sTownNameStub);
  return $sTownNameStub;
}

function IIRS_0_get_DOM_value($startNode, $xpathString) {
  $ret = '';
  $oXPath    = new DOMXpath($startNode->ownerDocument);
  $nodelist = $oXPath->query($xpathString, $startNode);
  if ($nodelist->length) {
    $ret = $nodelist->item(0)->textContent;
  }
  return $ret;
}

function sort_date_desc($a, $b) {return $a['date'] < $b['date'];}

//--------------------------------------------- language detection functions
function IIRS_0_print_language_selector() {
  global $lang_list, $lang_code, $available_languages;
  $html = '<select id="IIRS_0_language_control">';
  foreach ($available_languages as $code) {
    if (isset($lang_list[$code])) {
      $details = $lang_list[$code];
      $selected = ($code == $lang_code ? 'selected' : '');
      $html .= <<<HTML
        <option $selected value="$code">$details[1]</option>
HTML;
    }
  }
  $html .= '</select>';
  print($html);
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

//this is copied from Drupal 8.0 UserAgent Class code
//file:///var/www/drupal/drupal-8.0-alpha13/core/lib/Drupal/Component/Utility/UserAgent.php
function getBestMatchingLangcode($http_accept_language, $langcodes, $mappings = array()) {
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
function getStandardLanguageList() {
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
