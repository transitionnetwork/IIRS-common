<?php
/* Copyright 2015, 2016 Transition Network ltd
 * This program is distributed under the terms of the GNU General Public License
 * as detailed in the COPYING file included in the root of this plugin
 */

// view-source:tnv3.dev/wp-content/plugins/IIRS/IIRS_common/read_translations.php?format=po&start_directory=/var/www/wordpress/tnv3/wp-content/plugins/IIRS_0_print_translated_HTML_text
$default_start_directory = $_SERVER["DOCUMENT_ROOT"] . "/wp-content/plugins/IIRS";
$start_directory         = (isset($_GET['start_directory']) ? $_GET['start_directory'] : $default_start_directory );
$format                  = $_GET['format'];

// get all file paths
$matches            = array();
$overall_word_count = 0;
$files              = IIRS_0_recurse_directory($start_directory);

// analyse contents
foreach ($files as $file) {
  $content          = file_get_contents($file);
  $all_new_matches  = array();
  $nicefilename     = substr(strstr($file, '/IIRS/'), 6);
  preg_match_all('/<title>([^<]+)<\/title>/', $content, $file_title_match);
  $file_title       = (count($file_title_match) ? $file_title_match[1][0] : '');

  // IIRS translations
  $num_matches = preg_match_all('/IIRS_0_translation\(\s*\'([^\']+)/', $content, $new_matches);
  if ($num_matches) $all_new_matches = array_merge($all_new_matches, $new_matches[1]);
  $num_matches = preg_match_all('/IIRS_0_print_translated_HTML_text\(\s*\'([^\']+)/', $content, $new_matches);
  if ($num_matches) $all_new_matches = array_merge($all_new_matches, $new_matches[1]);

  // these one here to catch the occassional $widget_folder translations
  $num_matches = preg_match_all('/IIRS_0_translation\(\s*"([^"]+)/', $content, $new_matches);
  if ($num_matches) $all_new_matches = array_merge($all_new_matches, $new_matches[1]);
  $num_matches = preg_match_all('/IIRS_0_print_translated_HTML_text\(\s*"([^"]+)/', $content, $new_matches);
  if ($num_matches) $all_new_matches = array_merge($all_new_matches, $new_matches[1]);

  // Error translations
  $num_matches = preg_match_all('/new IIRS_Error\(\s*[A-Z_]+,\s*\'([^\']+)/', $content, $new_matches);
  if ($num_matches) $all_new_matches = array_merge($all_new_matches, $new_matches[1]);
  $num_matches = preg_match_all('/new IIRS_Error\(\s*[A-Z_]+,\s*"([^"]+)/', $content, $new_matches);
  if ($num_matches) $all_new_matches = array_merge($all_new_matches, $new_matches[1]);

  // Wordpress specific
  $num_matches = preg_match_all('/[^a-zA-Z0-9_]__\(\s*\'([^\']+)/', $content, $new_matches);
  if ($num_matches) $all_new_matches = array_merge($all_new_matches, $new_matches[1]);

  // add, unique, with info
  array_push($matches, array('file' => $nicefilename, 'title' => $file_title));
  foreach ($all_new_matches as $match) {
    $found = false;
    foreach ($matches as &$match_existing) {
      if ($match_existing['match'] == $match) {
        $found = true;
        break;
      }
    }

    if ($found) {
      //append the different locations if there are new ones
      if ($match_existing['file'] != $nicefilename) $match_existing['file'] .= ', ' . $nicefilename;
    } else {
      //add the unique one
      array_push($matches, array('match' => $match, 'file' => $nicefilename));
    }
  }
}

//unique
//$matches = array_unique($matches);

// output in required format
switch (strtoupper($format)) {
  case 'PO': {
    header('Content-type: text/plain;', true);
    print("# PO language file for the IIRS project\n");
    print("# https://www.gnu.org/savannah-checkouts/gnu/gettext/manual/html_node/PO-Files.html\n");
    print("# Project-Id-Version: TransitionNetwork IIRS\n");
    print("# Report-Msgid-Bugs-To: Transition Network IIRS team <" . IIRS_EMAIL_TEAM_EMAIL . ">\n");
    print("# POT-Creation-Date: 2014-07-21 04:00+0730\n");
    print("# PO-Revision-Date: 2014-07-21 04:00+0730\n");
    print("# Last-Translator: Transition Network IIRS team <" . IIRS_EMAIL_TEAM_EMAIL . ">\n");
    print("# Number-Translations: " . count($matches) . "\n");
    print("# Folder: " . $start_directory . "\n");
    print("# URL: /wp-content/plugins/IIRS/IIRS_common/read_translations.php?format=$format&start_directory=$start_directory\n");
    print("# msgfmt IIRS-en_EN.po -o IIRS-en_EN.mo\n");
    print("\n");
    foreach ($matches as $match_details) {
      if ($match = $match_details['match']) {
        if (strstr($match, '$')) {
          print("# -------------- (variable detected)\n");
          print("# msgid \"$match\"\n");
          print("# msgstr \"$match\"\n");
        } elseif (strstr($match, '\\')) {
          print("# -------------- (escape detected)\n");
          print("# msgid \"$match\"\n");
          print("# msgstr \"$match\"\n");
        } else {
          $match = str_replace( '"', '\"', $match );
          print("msgid \"$match\"\n");
          print("msgstr \"$match\"\n");
        }
      }
    }
    break;
  }
  case 'WPML': {
    header('Content-type: text/plain;', true);
    print("<?php\n");
    print("// this code can be saved in to the Wordpress plugin and WPML will automatically pick up the translations.\n");
    print("// some texts appear in multiple files. and often the files are utitlity files, rather than the actual URL for the appropriate screen.\n");
    $new_file_details = null;
    foreach ($matches as $match_details) {
      $match    = $match_details['match'];
      $file     = $match_details['file'];
      $filename = substr(strrchr($file, '/'), 1);
      if ($match) {
        if ($new_file_details) {
          print("\n// --------------------------------- $new_file_details[title], $new_file_details[file]\n");
          $new_file_details = null;
        }
        if (strstr($match, '$')) {
          print("// variable detected: __( '$match', 'iirs' ); //$file\n");
        } elseif (strstr($match, '\\')) {
          print("// escape detected: __( '$match', 'iirs' ); //$file\n");
        } else {
          $match = str_replace( "'", "\'", $match );
          print("__( '$match', 'iirs' ); //$file\n");
        }
      } else {
        if ($file) $new_file_details = $match_details;
      }
    }
    print('?>');
    break;
  }
  default: {
    ?>
    <style>
      img {border:2px dotted #888888;}
      li span {color:#888888;}
      .new_file {margin-top:20px; font-weight:bold; font-size:16px; list-style:none;}
    </style>
    <h1>translations found in the code</h1>
    <p>The IIRS has it's own translation function calls. This PHP process gathers them all together
      by reading the code and looking for those calls and parsing them.
      This information can then be saved in several ways.
      For example it can be saved in WordPress PHP code translation function calls __('...').
      This would have the effect of allowing translation plugins like WPML to find the Wordpress calls and translate them.
      Direct PO files can be generated ready for translations to be manually entered.<br/>
      <code>msgfmt IIRS-en_EN.po -o IIRS-en_EN.mo</code>
    </p>
    <ul>
    <?php
    $new_file_details = null;
    foreach ($matches as $match_details) {
      $match    = $match_details['match'];
      $file     = $match_details['file'];
      if ($match) {
        if (!strstr($match, '$') && !strstr($match, '\\')) {
          if ($new_file_details) {
            $file     = $new_file_details['file'];
            $title    = $new_file_details['title'];
            $filepath = "images/" . preg_replace('/[^A-Za-z0-9]+/', '_', $title) . ".png";
            print("<li class=\"new_file\">--------------------------------- $title, $file</li>");
            if (file_exists($filepath)) print("<img src=\"/IIRS/$filepath\"/>");
            $new_file_details = null;
          }
          $word_count = str_word_count($match);
          $overall_word_count += $word_count;
          print("<li>$match <span>($word_count words)</span></li>");
        }
      } else {
        if ($file) $new_file_details = $match_details;
      }
    }
    print("</ul>");
    print("<p>word count: $overall_word_count, <a href='http://icanlocalize.com'>icanlocalize.com</a> cost (@ 0.09USD / word) $" . $overall_word_count * 0.09 . "</p>");
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