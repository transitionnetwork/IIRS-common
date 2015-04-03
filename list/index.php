<?php
/* Copyright 2015, 2016 Transition Network ltd
 * This program is distributed under the terms of the GNU General Public License
 * as detailed in the COPYING file included in the root of this plugin
 */

//<title>LIST SCREEN</title>
?>

<div id="IIRS_0_debug"><pre>
debug output:
<?php
/* Manual Initiative Profile listing page
 * NOTE: this should NOT normally be used
 * The framework system should list the posts / nodes / whatevers natively
 * thus using the local templating system and all fitting in rather nicely
 * use that page instead and override the edit function
 *
 * Redirect all TI editing to /IIRS/edit to prevent users from going in to the host framework editing suite
 */

global $debug_environment;
require_once( IIRS__COMMON_DIR . 'utility.php' );
require_once( IIRS__COMMON_DIR . 'framework_abstraction_layer.php' );
require_once( IIRS__COMMON_DIR . 'environment.php' );
IIRS_0_debug_print( $debug_environment );

$all_TIs = IIRS_0_TIs_all();
usort($all_TIs, 'IIRS_0_sort_date_desc');

$usersTI = IIRS_0_details_TI_user();
?>
</pre></div>

<div id="IIRS_0">
  <style>
    body .entry-meta {display:block;}
  </style>

  <div class="IIRS_0_h1"><?php IIRS_0_print_translated_HTML_text( IGNORE_TRANSLATION, 'list of Transition Initiative around the world' ); ?>
    <?php IIRS_0_print_language_selector(); ?>
  </div>

  <ul id="list" class="IIRS_0_bare_list">
    <?php
      foreach ($all_TIs as $TI) {
        $date      = $TI['date'];
        $editable  = ($usersTI && $usersTI['native_ID'] == $TI['native_ID']);
        $edit_link = ($editable ? '<a class="IIRS_0_edit_link post-edit-link" href="/IIRS/edit">' . IIRS_0_translation( IGNORE_TRANSLATION, 'edit' ) . '</a>' : '');
        $name_escaped = IIRS_0_escape_for_HTML_text( $TI['name'] );
        $html      = <<<"HTML"
        <li>
          <h2 class="entry-title"><a href="/IIRS/view?ID=$TI[native_ID]">$name_escaped</a></h2>
          <div class="entry-meta">
            <span class="edit-link">$edit_link</span>
          </div>
          <div class="IIRS_0_status">$date</div>
        </li>
HTML;
        IIRS_0_print_HTML( $html );
      }
    ?>
  </ul>
</div>