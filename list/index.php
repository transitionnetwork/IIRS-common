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
require_once('framework_abstraction_layer.php');
require_once('utility.php');
require_once('environment.php');
print($debug_environment);

$aAllTIs = IIRS_0_TIs_all();
usort($aAllTIs, 'sort_date_desc');

$usersTI = IIRS_0_details_TI_user();
?>
</pre></div>

<div id="IIRS_0">
  <style>
    body .entry-meta {display:block;}
  </style>

  <div class="IIRS_0_h1"><?php IIRS_0_print_translation('list of transition towns around the world'); ?>
    <?php IIRS_0_print_language_selector(); ?>
  </div>

  <ul id="list" class="IIRS_0_bare_list">
    <?php
      foreach ($aAllTIs as $TI) {
        $date      = $TI['date'];
        $editable  = ($usersTI && $usersTI['native_ID'] == $TI['native_ID']);
        $edit_link = ($editable ? '<a class="IIRS_0_edit_link post-edit-link" href="/IIRS/edit">' . IIRS_0_translation('edit') . '</a>' : '');
        $html      = <<<"HTML"
        <li>
          <h2 class="entry-title"><a href="/IIRS/view">$TI[name]</a></h2>
          <div class="entry-meta">
            <span class="edit-link">$edit_link</span>
          </div>
          <div class="IIRS_0_status">$date</div>
        </li>
HTML;
        print($html);
      }
    ?>
  </ul>
</div>