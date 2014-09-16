<div id="IIRS_0_debug"><pre>
debug output:
<?php
require_once('framework_abstraction_layer.php');
require_once('utility.php');
require_once('environment.php');
require_once('inputs.php');

require_once('whois.php');

//------------------------------------- values
$summaryFromDomain    = '';
$summary_from_website = '';
$valid_dns            = TRUE;
$domain_entered       = ($domain && $domain != 'none');
$is_unchecked_domain  = !empty($domain_other);

if ($domain_entered) {
  print("domain: carrying out DNS and WHOIS lookup on [$domain]\n");
  //need to make sure this lookup is valid
  //before we actually start looking for about us pages
  //because it will take a long time from the timeouts
  //domain_other will be filled out if it has not already been checked by the previous domain_lookup
  if ($is_unchecked_domain) {
    $domain_other = trim($domain_other);
    $domain_other = preg_replace('/^(https?:\/\/)?(www\.)?([^\/?]*).*/i', '$3', $domain_other);
    $valid_dns    = checkdnsrr($domain_other);
  }

  if ($valid_dns) {
    print("DNS valid, updating domain to [$domain]\n");
    IIRS_0_TI_updateTI(array('domain' => $domain));

    print("whois lookup of [$domain]...\n");
    if ($aEntries = whois($domain)) {
      var_dump($aEntries);

      //email domain checkup only
      //$is_email_valid = checkdnsrr($sRegistrantEmail, "MX");

      //location lookup
      //TODO: Open Street Map does not support post codes!
      //if ($sRegistrantPostalCode && $sRegistrantCountry) $townname = "$sRegistrantPostalCode, $sRegistrantCountry";
      /*
      if ($sRegistrantCity)       $townname = "$sRegistrantCity";
      else {
        //get the town from the domain name
        print("no registrant city found, lets look in the domain name for a city name\n");
        $city = preg_replace('/\..*|transition|town|transicao/i', '', $domain);
        if ($city) $townname = $city;
        else {
          print("no registrant city found in the domain name either, try to get the city name from the IP\n");
          //TODO: try to get the town from the IP address
          //...
        }
      }
      */

      //------------------------------------- about us section
      $old_error_reporting = error_reporting(0);
      $timeout             = 1.0;
      if ( ($aboutus = IIRS_0_http_request("http://$domain/aboutus",  $timeout))
        || ($aboutus = IIRS_0_http_request("http://$domain/about",    $timeout))
        || ($aboutus = IIRS_0_http_request("http://$domain/about_us", $timeout))
        || ($aboutus = IIRS_0_http_request("http://$domain/",         $timeout))
      ) {
        $oAboutUs = new DOMDocument();
        $oAboutUs->loadHTML($aboutus);
        $xpath = new DOMXpath($oAboutUs);

        $summary_from_website = ' (' . IIRS_0_translation('from the website') . ')';

        $elements = $xpath->query("//*[@id='content']");
        if (!is_null($elements)) {
          foreach ($elements as $element) {
            $summaryFromDomain .= $element->textContent . "\n";
          }
          $summaryFromDomain = preg_replace('/\n\s*\n/', "\n", $summaryFromDomain);
          $summaryFromDomain = preg_replace('/\n\s*\n/', "\n", $summaryFromDomain);
          $summaryFromDomain = preg_replace('/^\s+|\s+$|^\s+/', "", $summaryFromDomain);
        }  else print("cannot find content in the aboutus response.\n");
      } else print("aboutus attemps all returned blank strings.\n");
      error_reporting($old_error_reporting);
    } else {
      //whois lookup failed
      //offer to buy the domain
      print("whois lookup returned empty text.\n");
    }
  } //$valid_dns
} else print("no domain entered, or 'none' entered\n");
?>
</pre></div>

<style>
  #IIRS_0_searchcontrol {
    float:right;
  }
  #IIRS_0_searchForm,
  #IIRS_0_searchcontrol .gs-text-box,
  #IIRS_0_searchcontrol .gs-snippet,
  #IIRS_0_searchcontrol .gsc-resultsHeader,
  #IIRS_0_searchcontrol .gsc-above-wrapper-area,
  #IIRS_0_searchcontrol .gs-size,
  #IIRS_0_searchcontrol .gs-visibleUrl,
  #IIRS_0_searchcontrol .gsc-title,
  #IIRS_0_searchcontrol .gsc-results-selector
    {display:none;}
  #IIRS_0_summary {
  }
</style>

<div id="IIRS_0">
  <?php if (!$valid_dns) { ?>
    <div class="IIRS_0_errors">
      <div class="IIRS_0_h1"><?php IIRS_0_print_translation("website [$domain_other] not valid"); ?></div>
      <div class="IIRS_0_horizontal_section">
        <input class="IIRS_0_bigbutton IIRS_0_back IIRS_0_error_back" type="button" value="&lt;&lt; <?php IIRS_0_print_translation('back'); ?>" />
      </div>
    </div>
  <?php } else { ?>
    <div class="IIRS_0_h1" id="IIRS_0_popup_title"><?php IIRS_0_print_translation('add ideas, summary, projects, descriptions so everyone can help and get advice'); ?></div>
    <form method="POST" id="IIRS_0_form_popup_summary_projects" action="finished" class="IIRS_0_clear IIRS_0_formPopupNavigate"><div>
      <?php IIRS_0_printEncodedPostParameters(); ?>

      <h3 class="IIRS_0_horizontal_section"><?php print(IIRS_0_translation('summary') . $summary_from_website); ?>:</h3>
      <textarea class="IIRS_0_textarea" name="summary"><?php print($summaryFromDomain); ?></textarea>

      <?php if (IIRS_0_setting('imageEntry')) { ?>
        <script src="https://www.google.com/jsapi?key=<?php print($GoogleAPIKey); ?>" type="text/javascript"></script>

        <script language="Javascript" type="text/javascript">
          function OnLoad() {
            var searchControl = new google.search.SearchControl();
            var drawOptions   = new google.search.DrawOptions();
            var imageOptions  = new google.search.SearcherOptions();

            searchControl.setResultSetSize(1);
            imageOptions.setExpandMode(google.search.SearchControl.EXPAND_MODE_OPEN);
            searchControl.addSearcher(new google.search.ImageSearch(), imageOptions);

            //drawOptions.setDrawMode(google.search.SearchControl.DRAW_MODE_TABBED);
            drawOptions.setSearchFormRoot(document.getElementById("IIRS_0_searchForm"));
            searchControl.draw(document.getElementById("IIRS_0_searchcontrol"), drawOptions);
            searchControl.execute("<?php print($townname); ?> town");
          }

          if (window.google) google.load('search', '1', {'callback':'OnLoad()'});
        </script>

        <h3 class="IIRS_0_horizontal_section"><?php IIRS_0_print_translation('image'); ?>:</h3>
        <div id="IIRS_0_searchcontrol"><?php print(IIRS_0_translation('Loading Google images for') . " $townname"); ?></div>
        <div id="IIRS_0_searchForm"></div>
        <?php IIRS_0_print_translation('or upload'); ?>:
      <?php } ?>

      <div class="IIRS_0_horizontal_section">
        <input class="IIRS_0_bigbutton IIRS_0_back" type="button" value="&lt;&lt; <?php IIRS_0_print_translation('back'); ?>" />
        <input class="IIRS_0_bigbutton" type="submit" value="<?php IIRS_0_print_translation('save and continue'); ?> &gt;&gt;" />
      </div>
    </form>

    <?php if ($addProjects) { ?>
    <form method="POST" id="IIRS_0_form_popup_2" action="advanced.php" class="IIRS_0_horizontal_section IIRS_0_formPopupNavigate"><div>
      <?php IIRS_0_print_translation('project or idea thing'); ?>:
      <table id="IIRS_0_details">
        <tr><td><?php IIRS_0_print_translation('name of thing'); ?></td><td><input /></td></tr>
        <tr><td><?php IIRS_0_print_translation('description of thing'); ?></td><td><input /></td></tr>
      </table>
      <div id="IIRS_0_details_teaser">
        <img src="<?php print($imageURLStem); ?>network_paper" />
        <?php IIRS_0_print_translation('Your nearest Transition Town is only 4km away and we will connect them with you.
        They have budget, and have done loads of stuff.
        But you probably know that already don\'t you? :)'); ?>
      </div>

      <input disabled="1" class="IIRS_0_bigbutton" type="button" value="<?php IIRS_0_print_translation('add another thing'); ?>" />
    </div></form>
    <?php } ?>
  <?php } ?>
</div>

