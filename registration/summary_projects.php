<?php
/* Copyright 2015, 2016 Transition Network ltd
 * This program is distributed under the terms of the GNU General Public License
 * as detailed in the COPYING file included in the root of this plugin
 */

//<title>Registration SCREEN #4</title>
?>

<div id="IIRS_0_debug"><pre>
debug output:
<?php
global $debug_environment;
require_once( IIRS__COMMON_DIR . 'utility.php' );
require_once( IIRS__COMMON_DIR . 'framework_abstraction_layer.php' );
require_once( IIRS__COMMON_DIR . 'environment.php' );
require_once( IIRS__COMMON_DIR . 'registration/inputs.php' );
IIRS_0_debug_print( $debug_environment );

//------------------------------------- values
$summaryFromDomain    = '';
$summary_from_website = IIRS_0_translation( 'summary' );
$valid_dns            = TRUE;
$IIRS_error           = null;
$domain_entered       = ($domain && $domain != 'none');
$is_unchecked_domain  = ! empty( $domain_other );
$ip_address           = NULL;

if ( $domain_entered ) {
  // ----------------------------------------------------------------- DNS lookup
  //need to make sure this lookup is valid
  //before we actually start looking for about us pages
  //because it will take a long time from the timeouts
  //domain_other will be filled out if it has not already been checked by the previous domain_lookup
  if ( $is_unchecked_domain ) {
    $domain     = trim($domain);
    $domain     = preg_replace('/^(https?:\/\/)?(www\.)?([^\/?]*).*/i', '$3', $domain);
    IIRS_0_debug_print("domain: carrying out DNS [and WHOIS] lookup on [$domain]");
    $ip_address = gethostbyname( $domain ); // gethostbyname PHP 4,5
    $valid_dns  = ( $ip_address != $domain );
  }

  if ($valid_dns) {
    IIRS_0_debug_print("DNS valid [$ip_address], updating domain to [$domain]");
    IIRS_0_TI_update_TI(array('domain' => $domain));

    //----------------------------------------------------- about us section from domain
    $old_error_reporting = error_reporting(0);
    $timeout             = 1.0;
    if ( (!IIRS_is_error($aboutus = IIRS_0_http_request("http://$domain/aboutus",  null, $timeout)))
      || (!IIRS_is_error($aboutus = IIRS_0_http_request("http://$domain/about",    null, $timeout)))
      || (!IIRS_is_error($aboutus = IIRS_0_http_request("http://$domain/about_us", null, $timeout)))
      || (!IIRS_is_error($aboutus = IIRS_0_http_request("http://$domain/",         null, $timeout)))
    ) {
      $oAboutUs = new DOMDocument();
      $oAboutUs->loadHTML($aboutus);
      $xpath = new DOMXpath($oAboutUs);

      $elements = $xpath->query("//*[@id='content'] | //*[@class='layout-container']");
      if ( ! is_null( $elements ) && $elements->length ) {
        $summary_from_website .= ' (' . IIRS_0_translation( 'from the website' ) . ')';
        foreach ($elements as $element) {
          $summaryFromDomain .= $element->textContent . "\n";
        }
        $summaryFromDomain = preg_replace('/\n\s*\n/', "\n", $summaryFromDomain);
        $summaryFromDomain = preg_replace('/\n\s*\n/', "\n", $summaryFromDomain);
        $summaryFromDomain = preg_replace('/^\s+|\s+$|^\s+/', "", $summaryFromDomain);
      }  else IIRS_0_debug_print("cannot find content in the aboutus response.");
    } else IIRS_0_debug_print("aboutus attemps all returned blank strings or [404] errors.");
    error_reporting($old_error_reporting);
  } else { // $valid_dns
    $IIRS_error = new IIRS_Error( IIRS_INVALID_WEBSITE_DNS, 'Your website was not found, please re-enter it or select "No Website"', 'DNS lookup failed', IIRS_MESSAGE_USER_WARNING, IIRS_MESSAGE_NO_USER_ACTION, array( '$domain' => $domain ) );
    IIRS_0_debug_print( $IIRS_error );
  }
} else { // $domain_entered
  IIRS_0_debug_print("no domain entered, or 'none' entered");
}
?>
</pre></div>

<?php if (IIRS_0_setting('image_entry')) { ?>
  <script src="https://www.google.com/jsapi?key=<?php IIRS_0_print(IIRS_GOOGLE_API_KEY); ?>" type="text/javascript"></script>

  <script type="text/javascript">
    function IIRS_0_OnLoad() {
      var searchControl     = new google.search.SearchControl();
      var drawOptions       = new google.search.DrawOptions();
      var imageOptions      = new google.search.SearcherOptions();
      var oDOMSearchControl = document.getElementById("IIRS_0_searchcontrol");
      var oDOMSearchForm    = document.getElementById("IIRS_0_searchForm");

      searchControl.setResultSetSize(1);
      imageOptions.setExpandMode(google.search.SearchControl.EXPAND_MODE_OPEN);
      searchControl.addSearcher(new google.search.ImageSearch(), imageOptions);

      //drawOptions.setDrawMode(google.search.SearchControl.DRAW_MODE_TABBED);
      drawOptions.setSearchFormRoot(oDOMSearchForm);
      searchControl.draw(oDOMSearchControl, drawOptions);
      searchControl.execute(oDOMSearchForm.text);
    }

    if (window.google) google.load("search", "1", {"callback":"IIRS_0_OnLoad()"});
  </script>

  <style>
    /* Google images search control setup, CURRENTLY_NOT_USED; */
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
<?php } ?>

<div id="IIRS_0">
  <?php if ( $IIRS_error ) {
      // IIRS_0_set_translated_error_message( ... ) uses IIRS_0_set_message( ... )
      IIRS_0_set_translated_error_message( $IIRS_error );
  } else { ?>
    <form method="POST" id="IIRS_0_form_popup_summary_projects" action="finished" class="IIRS_0_clear IIRS_0_formPopupNavigate"><div>
      <?php IIRS_0_printEncodedPostParameters(); ?>

      <h3 class="IIRS_0_horizontal_section"><?php IIRS_0_print_HTML_text( $summary_from_website ); ?>:</h3>
      <?php IIRS_0_HTML_editor($summaryFromDomain, 'summary'); ?>

      <?php if (IIRS_0_setting('image_entry')) { ?>
        <h3 class="IIRS_0_horizontal_section"><?php IIRS_0_print_translated_HTML_text(IGNORE_TRANSLATION, 'image'); ?>:</h3>
        <div id="IIRS_0_searchcontrol"><?php IIRS_0_print_translated_HTML_text(IGNORE_TRANSLATION, 'Loading Google images for'); IIRS_0_print_HTML_text( " $town_name"); ?></div>
        <div id="IIRS_0_searchForm"><?php IIRS_0_print_HTML_text( "$town_name town" ); ?></div>
        <?php IIRS_0_print_translated_HTML_text(IGNORE_TRANSLATION, 'or upload'); ?>:
      <?php } ?>

      <div class="IIRS_0_horizontal_section">
        <input class="IIRS_0_bigbutton IIRS_0_back" type="button" value="&lt;&lt; <?php IIRS_0_print_translated_HTML_text('back'); ?>" />
        <input class="IIRS_0_bigbutton" type="submit" value="<?php IIRS_0_print_translated_HTML_text('complete registration'); ?> &gt;&gt;" />
      </div>
    </form>

    <?php if ($add_projects) { ?>
      <form method="POST" id="IIRS_0_form_popup_2" action="advanced.php" class="IIRS_0_horizontal_section IIRS_0_formPopupNavigate"><div>
        <?php IIRS_0_print_translated_HTML_text(IGNORE_TRANSLATION, 'project or idea thing'); ?>:
        <table id="IIRS_0_details">
          <tr><td><?php IIRS_0_print_translated_HTML_text(IGNORE_TRANSLATION, 'name of thing'); ?></td><td><input /></td></tr>
          <tr><td><?php IIRS_0_print_translated_HTML_text(IGNORE_TRANSLATION, 'description of thing'); ?></td><td><input /></td></tr>
        </table>
        <div id="IIRS_0_details_teaser">
          <img src="<?php IIRS_0_print_HTML_image_src( "$imageURLStem/network_paper" ); ?>" />
          <?php IIRS_0_print_translated_HTML_text(IGNORE_TRANSLATION, 'Your nearest Transition Initiative is only 4km away and we will connect them with you. They have budget, and have done loads of stuff. But you probably know that already do you? :)'); ?>
        </div>

        <input disabled="1" class="IIRS_0_bigbutton" type="button" value="<?php IIRS_0_print_translated_HTML_text(IGNORE_TRANSLATION, 'add another thing'); ?>" />
      </div></form>
    <?php } ?>
  <?php } ?>
</div>

