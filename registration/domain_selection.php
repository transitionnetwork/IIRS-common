<div id="IIRS_0_debug"><pre>
debug output:
<?php
require_once('framework_abstraction_layer.php');
require_once('utility.php');
require_once('environment.php');
require_once('inputs.php');

//------------------------------------- register the TI!
//NOTE: the $pass might not have been sent through which means a random one will be generated
$TISaveError = '';
if ($userID = IIRS_0_TI_addUser($name, $email, $pass, $phone)) {
  if ($tiID = IIRS_0_TI_addTI($userID, $initiative_name, $townname, $place_centre_lat, $place_centre_lng, $place_description, $place_country, $domain)) {
    //all ok
  } else {
    $TISaveError = IIRS_0_translation('could not save the initiative') . ": [$initiative_name] " . IIRS_0_translation(' is already in use');
  }
} else {
  $TISaveError = IIRS_0_translation('could not save the user');
}
if ($TISaveError) print($TISaveError);

if ($tiID) {
  //------------------------------------- debug
  print("userID:$userID (don't forget that all emails on dev point to annesley_newholm@yahoo.it)<br/>");
  print("tiID:$tiID<br/>");

  //------------------------------------- get some nice domain names for this town
  $domains_found     = false;
  $domain_part       = ($is_example ? 'bedford' : $townname);
  $nice_domains_html = '';

  $nice_domains = array();
  $nice_domains[] = "transition$domain_part";
  $nice_domains[] = "transitiontown$domain_part";
  $nice_domains[] = "{$domain_part}transitiontown";
  $nice_domains[] = "{$domain_part}transition";
  $nice_domains[] = "{$domain_part}intransition";

  //using our tld list is too time consuming
  //could use threads but beyond scope for now
  /*
  $effective_tld_names = file_get_contents(__DIR__ . '/effective_tld_names.dat.txt');
  $aAllFileEntries     = explode("\n", $effective_tld_names);
  $aTLDs               = array();
  foreach ($aAllFileEntries as $sEntry) {
    if (strlen($sEntry) && substr($sEntry, 0, 2) != '//') {
      $aTLDs[] = $sEntry;
    }
  }
  print("check potential domain string [$domain_part] combinations against [" . count($aTLDs) . "] TLDs:\n");
  */
  $aTLDs = array('org', 'org.uk', 'com', 'net');

  $option = 1;
  foreach ($nice_domains as $nice_domain) {
    foreach ($aTLDs as $tld) {
      $full_domain = strtolower("$nice_domain.$tld");
      //checkdnsrr($full_domain) PHP >= 4.0
      //could also use gethostbyname($full_domain)
      $valid_dns   = checkdnsrr($full_domain);
      if ($valid_dns) {
        $domains_found  = true;
        $selected       = ($option == 1 ? 'checked="1"' : '');
        $selected_class = ($option == 1 ? 'selected' : '');
        $nice_domains_html .= <<<"HTML"
          <li class="$selected_class">
            <input $selected name="domain" class="IIRS_0_radio" value="$full_domain" type="radio" id="IIRS_0_domain_{$option}_input" />
            <label for="IIRS_0_domain_{$option}_input">
              $full_domain
              <div class="IIRS_0_status"><a target="_blank" href="http://$full_domain">view in new window</a></div>
            </label>
          </li>
HTML;
        $option++;
      }
    }
  }
}
?>
</pre></div>

<style>
</style>

<div id="IIRS_0">
  <?php if (!$tiID) { ?>
    <div class="IIRS_0_errors">
      <div id="IIRS_0_popup_title" class="IIRS_0_h1"><?php print($TISaveError); ?></div>
      <div class="IIRS_0_horizontal_section">
        <input class="IIRS_0_bigbutton IIRS_0_back IIRS_0_error_back" type="button" value="&lt;&lt; <?php IIRS_0_print_translation('back'); ?>" />
      </div>
    </div>
  <?php } else {
      $message = IIRS_0_translation('you are now registered') . ". $initiative_name " . IIRS_0_translation('is go!');
      if (!$is_home_domain) $message .= "\n" . IIRS_0_translation('you will need to log in to') . ' <a target="_blank" href="http://transitionnetwork.org">Transition Network</a> ' . IIRS_0_translation('to manage your registration, NOT this website') . '.';
      if ($tiID == 1) $message .= ' (testing mode, no save done)';
      IIRS_0_set_message($message, $IIRS_widget_mode);
    ?>

    <form method="POST" id="IIRS_0_form_popup_domain_selection" action="summary_projects" class="IIRS_0_clear IIRS_0_formPopupNavigate"><div>
      <?php IIRS_0_printEncodedPostParameters(); ?>
      <input name="userID" type="hidden" value="<?php print($userID); ?>" />
      <input name="tiID" type="hidden" value="<?php print($tiID); ?>" />

      <div class="IIRS_0_h1"><?php print(IIRS_0_translation('website selection')); ?> </div>
      <ul id="IIRS_0_list_selector">
        <?php if (!$domains_found) { ?>
          <!--li class="IIRS_0_domain IIRS_0_message">
            <img src="< ?php print("$IIRSURLImageStem/information"); ?>" />
            < ?php print(IIRS_0_translation('no registered websites found for this town') . " $townname " . '<br/>' . IIRS_0_translation('you will need to') . ' <a href="mailto:annesley_newholm@yahoo.it">' . IIRS_0_translation('register by email') . '</a> ' . IIRS_0_translation('please type your website name in below if you have one')); ?>
          </li -->
        <?php } ?>
        <?php print($nice_domains_html); ?>
        <li>
          <input name="domain" class="IIRS_0_radio" value="none" type="radio" id="IIRS_0_domain_none_input" />
          <label for="IIRS_0_domain_none_input">
            <?php IIRS_0_print_translation('no website'); ?>
            <div class="IIRS_0_status"><?php IIRS_0_print_translation("we don't currently have a website"); ?></div>
          </label>
        </li>
        <li id="IIRS_0_other">
          <?php print($domains_found ? IIRS_0_translation('other') : IIRS_0_translation('your website')); ?>:
          <input id="IIRS_0_research_domain_other" name="domain_other" />
        </li>
      </ul>

      <div class="IIRS_0_horizontal_section">
        <input class="IIRS_0_bigbutton IIRS_0_back" type="button" value="&lt;&lt; <?php IIRS_0_print_translation('back'); ?>" />
        <input class="IIRS_0_bigbutton" type="submit" value="<?php IIRS_0_print_translation('save and continue'); ?> &gt;&gt;" />
      </div>
    </form>
  <?php } ?>
</div> <!-- /IIRS_0 -->
