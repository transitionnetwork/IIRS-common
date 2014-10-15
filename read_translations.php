<?php
header('Content-type: text/plain;', true);

// view-source:tnv3.dev/wp-content/plugins/IIRS/IIRS_common/read_translations.php?format=po&start_directory=/var/www/wordpress/tnv3/wp-content/plugins/IIRS_0_print_translation
$start_directory = (isset($_GET['start_directory']) ? $_GET['start_directory'] : dirname(__FILE__));
$format          = $_GET['format'];
$matches         = array();

// get all file paths
$files           = IIRS_0_recurse_directory($start_directory);

// analyse contents
foreach ($files as $file) {
  $content      = file_get_contents($file);

  // IIRS translations
  $num_matches = preg_match_all('/IIRS_0_translation\(\s*\'([^\']+)/',       $content, $new_matches);
  if ($num_matches) $matches = array_merge($matches, $new_matches[1]);
  $num_matches = preg_match_all('/IIRS_0_print_translation\(\s*\'([^\']+)/', $content, $new_matches);
  if ($num_matches) $matches = array_merge($matches, $new_matches[1]);

  // these one here to catch the occassional $widget_folder translations
  $num_matches = preg_match_all('/IIRS_0_translation\(\s*"([^"]+)/',         $content, $new_matches);
  if ($num_matches) $matches = array_merge($matches, $new_matches[1]);
  $num_matches = preg_match_all('/IIRS_0_print_translation\(\s*"([^"]+)/',         $content, $new_matches);
  if ($num_matches) $matches = array_merge($matches, $new_matches[1]);

  //Wordpress specific
  $num_matches = preg_match_all('/[^a-zA-Z0-9_]__\(\s*\'([^\']+)/',          $content, $new_matches);
  if ($num_matches) $matches = array_merge($matches, $new_matches[1]);
}

//unique
$matches = array_unique($matches);

// output in required format
switch ($format) {
  case 'PO': {
    print("# PO language file for the IIRS project\n");
    print("# https://www.gnu.org/savannah-checkouts/gnu/gettext/manual/html_node/PO-Files.html\n");
    print("# Project-Id-Version: TransitionNetwork IIRS\n");
    print("# Report-Msgid-Bugs-To: Annesley Newholm <annesley_newholm@yahoo.it>\n");
    print("# POT-Creation-Date: 2014-07-21 04:00+0730\n");
    print("# PO-Revision-Date: 2014-07-21 04:00+0730\n");
    print("# Last-Translator: Annesley Newholm <annesley_newholm@yahoo.it>\n");
    print("# Number-Translations: " . count($matches) . "\n");
    print("# Folder: " . $start_directory . "\n");
    print("# URL: /wp-content/plugins/IIRS/IIRS_common/read_translations.php?format=$format&start_directory=$start_directory\n");
    print("\n");
    foreach ($matches as $match) {
      if (strstr($match, '$')) {
        print("# ---------------------------------------------------------------- variable detected\n");
        print("# msgid \"$match\"\n");
        print("# msgstr \"$match\"\n");
      } elseif (strstr($match, '\\')) {
        print("# ---------------------------------------------------------------- escape detected\n");
        print("# msgid \"$match\"\n");
        print("# msgstr \"$match\"\n");
      } else {
        print("msgid \"$match\"\n");
        print("msgstr \"$match\"\n");
      }
    }
    break;
  }
  case 'WPML': {
    print("<?php\n");
    print("// this code can be saved in to the Wordpress plugin and WPML will automatically pick up the translations.\n");
    foreach ($matches as $match) {
      if (strstr($match, '$')) {
        print("// ---------------------------------------------------------------- variable detected\n");
        print("// __('$match','iirs');\n");
      } elseif (strstr($match, '\\')) {
        print("// ---------------------------------------------------------------- escape detected\n");
        print("// __('$match','iirs');\n");
      } else {
        print("__('$match','iirs');\n");
      }
    }
    print('?>');
    break;
  }
  default: {
    print("<ul>");
    foreach ($matches as $match) {
      print("<li>$match</li>");
    }
    print("</ul>");
  }
}


function IIRS_0_recurse_directory($base_dir) {
  $files = array();
  foreach (scandir($base_dir) as $file) {
    if (
         $file == '.' || $file == '..'
      || empty($file)
      || $file[0] == '.'
      || $file == 'read_translations.php'
      || $file == 'generated_fake_translations.php'
    ) continue;

    $full_path = $base_dir.DIRECTORY_SEPARATOR.$file;
    if (is_dir($full_path)) {
      $files = array_merge($files, IIRS_0_recurse_directory($full_path));
    } else {
      $last_dot_part = strrchr($file, '.');
      if ($last_dot_part === FALSE) continue;
      $extension = substr($last_dot_part, 1);
      if ($extension == 'js' || $extension == 'txt' || $extension == 'po' || $extension == 'mo') continue;
      array_push($files, $full_path);
    }
  }
  return $files;
}
?>