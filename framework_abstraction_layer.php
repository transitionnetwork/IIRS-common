<?php
//--------------------------------------------------- optional functions
if (!function_exists('IIRS_0_translation')) {
  function IIRS_0_translation($sString) {
    return $sString;
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
      case 'offerBuyDomains': return false;
      case 'addProjects': return false;
      case 'advancedSettings': return false;
      case 'imageEntry': return false;
      case 'langCode': return 'en';
      case 'serverCountry': return NULL;
      default: return false;
    }
  }
}

if (!function_exists('IIRS_0_TI_isRegistered')) {
  function IIRS_0_TI_isRegistered($townname, $centre_lat, $centre_lng, $place_description) {
    //$townname = the townname typed in that generated this search
    //$geokml, $place_description = potential matches to test for against the database
    $bRegistered = false;
    $aTIsNearby  = IIRS_0_TIs_nearby($centre_lat, $centre_lng, $place_description);

    $townnameBase = removeTransitionWords($townname);

    //look at the data and decide...
    /*
    foreach ($aTIsNearby as $townname => $aDetails) {
      //direct parameter comparison
      if (isset($aDetails['name'])) {
        if (strcasecmp($aDetails['name'], $townnameBase)) $bRegistered = true;
      }
      if (isset($aDetails['place_description'])) {
        if (strcasecmp($aDetails['place_description'], $place_description)) $bRegistered = true;
      }

      //try to asses how close / similar the areas are
      if (isset($aDetails['geokml'])) {
        $geokml = $aDetails['geokml'];
        //TODO: area comparison
      }
    }
    */

    return $bRegistered;
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

if (!function_exists('IIRS_0_detailsTI_page')) {
  function IIRS_0_detailsTI_page() {
    IIRS_0_set_message('IIRS_0_detailsTI_page() nto supported');
    return NULL;
  }
}

if (!function_exists('IIRS_0_TIs_all')) {
  function IIRS_0_TIs_all($page_size = 0, $page_offset = 0) {
    //TODO: use the IIRS_0_TIs_nearby() with unlimited results
    //setting sensible limit of 5000 for performance purposes
    //TODO: admin warning when limit is at 4000
    $aTIs = IIRS_0_TIs_nearby(0, 0, '', 5000);
    return $aTIs;
  }
}

if (!function_exists('IIRS_0_availableLanguages')) {
  function IIRS_0_availableLanguages() {
    return array('en');
  }
}


//--------------------------------------------------- required functions
if (true) {
  //querying
  if (!function_exists('IIRS_0_TIs_nearby'))      {print('IIRS_0_TIs_nearby() required'); exit;}
  if (!function_exists('IIRS_0_TIs_viewport'))    {print('IIRS_0_TIs_viewport() required'); exit;}
  if (!function_exists('IIRS_0_detailsUser'))     {print('IIRS_0_detailsUser() required'); exit;}
  if (!function_exists('IIRS_0_detailsTI_user'))  {print('IIRS_0_detailsTI_user() required'); exit;}

  //registering
  if (!function_exists('IIRS_0_TI_addUser'))      {print('IIRS_0_TI_addUser() required'); exit;}
  if (!function_exists('IIRS_0_TI_addTI'))        {print('IIRS_0_TI_addTI() required'); exit;}
  if (!function_exists('IIRS_0_TI_updateTI'))     {print('IIRS_0_TI_updateTI() required'); exit;}
  if (!function_exists('IIRS_0_TI_updateUser'))   {print('IIRS_0_TI_updateUser() required'); exit;}
  if (!function_exists('IIRS_0_next_initnumber')) {print('IIRS_0_next_initnumber() required'); exit;}

  //authentication
  if (!function_exists('IIRS_0_logged_in'))       {print('IIRS_0_logged_in() required'); exit;}
  if (!function_exists('IIRS_0_login'))           {print('IIRS_0_login() required'); exit;}
}
?>
