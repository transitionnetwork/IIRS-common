<?php
//--------------------------------------------------- optional functions
if (!function_exists('IIRS_0_translation')) {
  function IIRS_0_translation($string_to_translate) {
    return $string_to_translate;
  }
}

if (!function_exists('IIRS_0_input')) {
  function IIRS_0_input($sKey) {
    return (isset($_POST[$sKey]) ? $_POST[$sKey] : (isset($_GET[$sKey]) ? $_GET[$sKey] : NULL));
  }
}

if (!function_exists('IIRS_0_setting')) {
  function IIRS_0_setting($setting) {
    switch ($setting) {
      case 'offer_buy_domains': return false;
      case 'add_projects': return false;
      case 'advanced_settings': return false;
      case 'imageEntry': return false;
      case 'langCode': return 'en';
      case 'serverCountry': return NULL;
      default: return false;
    }
  }
}

if (!function_exists('IIRS_0_TI_is_registered')) {
  function IIRS_0_TI_is_registered($town_name, $location_latitude, $location_longitude, $location_description) {
    //$town_name = the townname typed in that generated this search
    //$geokml, $location_description = potential matches to test for against the database
    $registered  = false;
    $TIs_nearby   = IIRS_0_TIs_nearby($location_latitude, $location_longitude, $location_description);

    $town_nameBase = IIRS_0_remove_transition_words($town_name);

    //look at the data and decide...
    /*
    foreach ($TIs_nearby as $town_name => $aDetails) {
      //direct parameter comparison
      if (isset($aDetails['name'])) {
        if (strcasecmp($aDetails['name'], $town_nameBase)) $registered = true;
      }
      if (isset($aDetails['location_description'])) {
        if (strcasecmp($aDetails['location_description'], $location_description)) $registered = true;
      }

      //try to asses how close / similar the areas are
      if (isset($aDetails['geokml'])) {
        $geokml = $aDetails['geokml'];
        //TODO: area comparison
      }
    }
    */

    return $registered;
  }
}

if (!function_exists('IIRS_0_current_path')) {
  function IIRS_0_current_path() {
    return $_SERVER['PHP_SELF'];
  }
}

if (!function_exists('IIRS_0_http_request')) {
  function IIRS_0_http_request($url) {
    return file_get_contents($url, 'r');
  }
}

if (!function_exists('IIRS_0_set_message')) {
  function IIRS_0_set_message($message, $IIRS_widget_mode = true) {
    print("<div class=\"IIRS_0_message\">$message</div>");
  }
}

if (!function_exists('IIRS_0_details_TI_page')) {
  function IIRS_0_details_TI_page() {
    IIRS_0_set_message('IIRS_0_details_TI_page() not supported');
    return NULL;
  }
}

if (!function_exists('IIRS_0_TIs_all')) {
  function IIRS_0_TIs_all($page_size = 0, $page_offset = 0) {
    //TODO: use the IIRS_0_TIs_nearby() with unlimited results
    //setting sensible limit of 5000 for performance purposes
    //TODO: admin warning when limit is at 4000
    $TIs = IIRS_0_TIs_nearby(0, 0, '', 5000);
    return $TIs;
  }
}

if (!function_exists('IIRS_0_available_languages')) {
  function IIRS_0_available_languages() {
    return array('en');
  }
}


//--------------------------------------------------- required functions
if (true) {
  //querying
  if (!function_exists('IIRS_0_TIs_nearby'))       {print('IIRS_0_TIs_nearby() required'); exit;}
  if (!function_exists('IIRS_0_TIs_viewport'))     {print('IIRS_0_TIs_viewport() required'); exit;}
  if (!function_exists('IIRS_0_details_user'))     {print('IIRS_0_details_user() required'); exit;}
  if (!function_exists('IIRS_0_details_TI_user'))  {print('IIRS_0_details_TI_user() required'); exit;}

  //registering
  if (!function_exists('IIRS_0_TI_add_user'))      {print('IIRS_0_TI_add_user() required'); exit;}
  if (!function_exists('IIRS_0_TI_add_TI'))        {print('IIRS_0_TI_add_TI() required'); exit;}
  if (!function_exists('IIRS_0_TI_update_TI'))     {print('IIRS_0_TI_update_TI() required'); exit;}
  if (!function_exists('IIRS_0_TI_update_user'))   {print('IIRS_0_TI_update_user() required'); exit;}
  if (!function_exists('IIRS_0_next_initnumber'))  {print('IIRS_0_next_initnumber() required'); exit;}

  //authentication
  if (!function_exists('IIRS_0_logged_in'))        {print('IIRS_0_logged_in() required'); exit;}
  if (!function_exists('IIRS_0_login'))            {print('IIRS_0_login() required'); exit;}
}
?>
