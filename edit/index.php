<div id="IIRS_0_debug"><pre>
debug output:
<?php
global $debug_environment;
require_once('framework_abstraction_layer.php');
require_once('utility.php');
require_once('environment.php');
require_once('registration/inputs.php');
print($debug_environment);

//specific
$townname_new = IIRS_0_input('townname_new');

//------------------------------------------------------------- authentication
if ($form == 'login') {
  print("logging in [$name]...");
  IIRS_0_login($name, $pass);
}

//------------------------------------------------------------- updates
$TI = NULL;

if ($form == 'update account details') {
  //current user logged in is required here
  print("updating account details...\n");
  $aValues = array(
    'name'     => $name,
    'email'    => $email,
    'password' => ($pass == IIRS_0_CLEAR_PASSWORD ? NULL : $pass),
  );
  IIRS_0_TI_updateUser($aValues);
}

if ($form == 'update transition initiative') {
  //current user logged in is required here
  //to get the correct TI
  print("updating TI details...\n");
  $aValues = array(
    'name'    => $initiative_name,
    'summary' => $summary,
    'domain'  => $domain,
  );
  IIRS_0_TI_updateTI($aValues);
}

//------------------------------------------------------------- load current values
$is_user_with_one_TI = false;
if (IIRS_0_logged_in()) {
  print("loading user details...\n");
  if ($aUser = IIRS_0_detailsUser()) {
    $name  = $aUser['name'];
    $email = $aUser['email'];
    //$phone = $aUser['phone'];

    print("loading TI [" . IIRS_0_CONTENT_TYPE . "] details...\n");
    if ($aTI = IIRS_0_detailsTI_user()) {
      $is_user_with_one_TI = true;
      $initiative_name = $aTI['name'];
      $summary         = $aTI['summary'];
      $domain          = $aTI['domain'];
      //if ($location = $TI->location) {
        //TODO: process location
      //}
    } else {
      print("could not load associated TI. invalid user for this screen. show login screen.\n");
    }
  } else {
    print("could not load user: show login screen\n");
  }
}

//------------------------------------------------------------- location control
$towns_found   = true;
$place_options = '';
?>
</pre></div>

<div id="IIRS_0">
  <style>
  </style>

  <div class="IIRS_0_h1"><?php IIRS_0_print_translation('setup editor'); ?>
    <?php printLanguageSelector(); ?>
  </div>

  <?php if ($is_user_with_one_TI) { ?>
    <form action="index" class="IIRS_0_clear IIRS_0_formPopupNavigate" method="POST">
      <h2><?php IIRS_0_print_translation('your details'); ?>:</h2>
      <input type="text" name="name" value="<?php print($name); ?>" />
      <input type="text" name="email" value="<?php print($email); ?>" />
      <input type="password" name="pass" value="<?php print(IIRS_0_CLEAR_PASSWORD); ?>" />
      <input type="hidden" name="form" value="update account details" />
      <input type="submit" name="submit" class="IIRS_0_bigbutton IIRS_0_clear" value="<?php IIRS_0_print_translation('update account details'); ?>" />
    </form>

    <form action="index" class="IIRS_0_clear IIRS_0_formPopupNavigate" method="POST">
      <h2><?php IIRS_0_print_translation('transition initiative details'); ?>:</h2>
      <input type="text" name="initiative_name" value="<?php print($initiative_name); ?>" />
      <input type="text" name="domain" value="<?php print($domain); ?>" />

      <ul id="IIRS_0_list_selector">
        <?php if (!$towns_found) { ?>
          <li class="IIRS_0_place IIRS_0_message">
            <img src="<?php print("$IIRSURLImageStem/information"); ?>" />
            <?php print(IIRS_0_translation('no towns found matching') . " $townname " . '<br/>' . IIRS_0_translation('you will need to') . ' <a href="mailto:annesley_newholm@yahoo.it">' . IIRS_0_translation('register by email') . '</a> ' . IIRS_0_translation('because we cannot find your town on our maps system!')); ?>
          </li>
        <?php } ?>
        <?php print($place_options); ?>
        <li id="IIRS_0_other" class="IIRS_0_place">
          <?php IIRS_0_print_translation('change location'); ?>:
          <input id="IIRS_0_research_townname_new" value="<?php if ($townname) print($townname); ?>" />
          <input id="IIRS_0_research" type="button" value="<?php IIRS_0_print_translation('search'); ?>" />
        </li>
      </ul>

      <textarea class="IIRS_0_textarea" name="summary"><?php print($summary); ?></textarea>
      <input type="hidden" name="form" value="update transition initiative" />
      <input type="submit" class="IIRS_0_bigbutton IIRS_0_clear" name="submit" value="<?php IIRS_0_print_translation('update transition initiative'); ?>" />
    </form>
  <?php } else { ?>

    <form action="index" class="IIRS_0_clear IIRS_0_formPopupNavigate" method="POST">
      <h2><?php IIRS_0_print_translation('login required to edit'); ?>:</h2>
      <input type="text" name="name" value="<?php print($name); ?>" />
      <input type="password" name="pass" />
      <input type="hidden" name="form" value="login" />
      <input name="submit" type="submit" class="IIRS_0_bigbutton IIRS_0_clear" value="login" />
    </form>
  <?php } ?>
</div>