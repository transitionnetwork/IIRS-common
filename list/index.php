<div id="IIRS_0_debug"><pre>
debug output:
<?php
global $debug_environment;
require_once('framework_abstraction_layer.php');
require_once('utility.php');
require_once('environment.php');
print($debug_environment);

global $TI;
$aAllTIs = IIRS_0_TIs_all();
usort($aAllTIs, 'sort_date_desc');
?>
</pre></div>

<div id="IIRS_0">
  <style>
  </style>

  <div class="IIRS_0_h1"><?php IIRS_0_print_translation('list of transition towns around the world'); ?>
    <?php printLanguageSelector(); ?>
  </div>
  <ul id="list" class="IIRS_0_bare_list">
    <?php
      foreach ($aAllTIs as $TI) {
        $date = $TI['date'];
        $html = <<<"HTML"
        <li>
          <a href="/IIRS/view">$TI[name]</a>
          <div class="IIRS_0_status">$date</div>
        </li>
HTML;
        print($html);
      }
    ?>
  </ul>
</div>