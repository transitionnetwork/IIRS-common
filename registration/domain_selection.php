<div id="IIRS_0_debug"><pre>
debug output:
<?php
global $debug_environment;
require_once('framework_abstraction_layer.php');
require_once('utility.php');
require_once('environment.php');
require_once('inputs.php');
require_once('location.php');
require_once('akismet.php');
print($debug_environment);

//------------------------------------- register the TI!
//if the $pass is not being captured a random one will be generated
$TI_save_error = null;
if ($user_ID = IIRS_0_TI_add_user($name, $email, $pass, $phone)) {
  if ($ti_ID = IIRS_0_TI_verify_add_TI($user_ID, $IIRS_host_domain, $initiative_name, $town_name, $location_latitude, $location_longitude, $location_description, $location_country, $location_full_address, $location_granuality, $location_bounds, $domain)) {
    //all ok
  } else {
    IIRS_0_delete_user($user_ID);
    $TI_save_error = IIRS_0_translation('could not save the initiative') . ": [$initiative_name] " . IIRS_0_translation(' is already in use');
  }
} else {
  $TI_save_error = IIRS_0_translation('could not save the user');
}
// TODO: pretty error report
if ( $TI_save_error ) print( $TI_save_error );

if ( ! $TI_save_error ) {
  //------------------------------------- debug
  print("userID:$user_ID (don't forget that all emails on dev point to annesley_newholm@yahoo.it)<br/>");
  print("tiID:$ti_ID<br/>");

  //------------------------------------- get some nice domain names for this town
  $domains_found     = false;
  $domain_part       = ($location_is_example ? 'bedford' : $town_name);
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
  $all_TLDs               = array();
  foreach ($aAllFileEntries as $entry) {
    if (strlen($entry) && substr($entry, 0, 2) != '//') {
      $all_TLDs[] = $entry;
    }
  }
  print("check potential domain string [$domain_part] combinations against [" . count($all_TLDs) . "] TLDs:\n");
  */
  $all_TLDs = array('org', 'org.uk', 'com', 'net');

  $option = 1;
  foreach ($nice_domains as $nice_domain) {
    foreach ($all_TLDs as $tld) {
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
  <?php if ( $TI_save_error ) { ?>
    <div class="IIRS_0_errors">
      <div id="IIRS_0_popup_title" class="IIRS_0_h1"><?php print( $TI_save_error ); ?></div>
      <div class="IIRS_0_horizontal_section">
        <input class="IIRS_0_bigbutton IIRS_0_back IIRS_0_error_back" type="button" value="&lt;&lt; <?php IIRS_0_print_translation('back'); ?>" />
      </div>
    </div>
  <?php } else {
      $message = IIRS_0_translation('you are now registered') . ". $initiative_name " . IIRS_0_translation('is go!');
      if (!$IIRS_is_home_domain) $message .= "\n" . IIRS_0_translation('you will need to log in to') . ' <a target="_blank" href="http://transitionnetwork.org">Transition Network</a> ' . IIRS_0_translation('to manage your registration, NOT this website') . '.';
      if ($ti_ID == 1) $message .= ' (testing mode, no save done)';
      IIRS_0_set_message($message, $IIRS_widget_mode);
    ?>

    <form method="POST" id="IIRS_0_form_popup_domain_selection" action="summary_projects" class="IIRS_0_clear IIRS_0_formPopupNavigate"><div>
      <?php IIRS_0_printEncodedPostParameters(); ?>
      <input name="userID" type="hidden" value="<?php print($user_ID); ?>" />
      <input name="tiID" type="hidden" value="<?php print($ti_ID); ?>" />

      <div class="IIRS_0_h1"><?php print(IIRS_0_translation('website selection')); ?> </div>
      <ul id="IIRS_0_list_selector">
        <?php if (!$domains_found) { ?>
          <!--li class="IIRS_0_domain IIRS_0_message">
            <img src="< ?php print("$IIRS_URL_image_stem/information"); ?>" />
            < ?php print(IIRS_0_translation('no registered websites found for this town') . " $town_name " . '<br/>' . IIRS_0_translation('you will need to') . ' <a href="mailto:annesley_newholm@yahoo.it">' . IIRS_0_translation('register by email') . '</a> ' . IIRS_0_translation('please type your website name in below if you have one')); ?>
          </li -->
        <?php } ?>
        <?php print($nice_domains_html); ?>
        <li>
          <input name="domain" class="IIRS_0_radio" value="none" type="radio" id="IIRS_0_domain_none_input" />
          <label for="IIRS_0_domain_none_input">
            <?php IIRS_0_print_translation('no website'); ?>
            <div class="IIRS_0_status"><?php IIRS_0_print_translation('we do not currently have a website'); ?></div>
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
