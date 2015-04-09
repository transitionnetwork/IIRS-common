<?php
/* Copyright 2015, 2016 Transition Network ltd
 * This program is distributed under the terms of the GNU General Public License
 * as detailed in the COPYING file included in the root of this plugin
 */

if ($_SERVER['HTTP_HOST'] !== 'tnv3.dev') {
  print('development only tool');
  exit(0);
}

// view-source:tnv3.dev/wp-content/plugins/IIRS/IIRS_common/read_translations.php?format=po&start_directory=/var/www/wordpress/tnv3/wp-content/plugins/IIRS_0_print_translated_HTML_text
$start_directory    = ( isset($_GET['start_directory']) ? $_GET['start_directory'] : dirname(__FILE__) );
$fix_file           = ( $_GET['fix_file'] );
$standard_wordpress = ( ! isset( $_GET['standard'] ) || $_GET['standard'] == 'wordpress' );
$standard_drupal    = ( isset( $_GET['standard'] ) && $_GET['standard'] == 'drupal' );
$include_common     = ( $_GET['include_common'] == 'true' );
?>

<h1>file report</h1>
<p>
  <a href="?standard=wordpress">check against WordPress standard</a>
  | <a href="?standard=drupal">check against Drupal standard</a>
  | <a href="?include_common=true">check IIRS_common</a>
  | <a href="?start_directory=/var/www/wordpress/tnv3/wp-content/plugins/IIRS/">scan IIRS WordPress plugin</a>
</p>

<?php
// get all file paths
$files = IIRS_0_recurse_directory($start_directory);

// analyse and update contents
foreach ($files as $file) {
  $extension = substr(strrchr($file, '.'), 1);
  $points    = array();
  $content   = file_get_contents($file);
  $fix_this  = ( $fix_file == $file ); // NOT_CURRENTLY_USED

  if ( $extension == 'js' && $standard_wordpress ) {
    // ----------------------------------------------------------------------- Javascript PHP
    // filename
    if ( strstr( strrchr($file, '/'), '_' ) !== FALSE ) {
      // array_push( $points, 'filename contains underscores (_)' );
    }

    // brackets
    /*
    if ( $num_matches = preg_match_all('/[a-zA-Z0-9_]+\([^ )][^)]*\)/', $content, $new_matches) ) {
      foreach ($new_matches[0] as $new_match) array_push( $points, 'brackets without spaces:' . $new_match );
    }
    */

    // sandboxed function names
    if ( $num_matches = preg_match_all('/^\s*function\s+((?!IIRS_)[a-zA-Z0-9_]+)\(/m', $content, $new_matches) ) {
      foreach ($new_matches[0] as $new_match) array_push( $points, 'function not sandboxed with IIRS_:' . $new_match );
    }

  } elseif ( $extension == 'php' && $standard_wordpress ) {
    // ----------------------------------------------------------------------- WordPress PHP
    // filename
    if ( strstr( strrchr($file, '/'), '-' ) !== FALSE ) {
      // array_push( $points, 'filename contains dashes (-)');
    }

    // brackets
    if ( $num_matches = preg_match_all('/[a-zA-Z0-9_]+\([^ )][^)]*\)/', $content, $new_matches) ) {
      // foreach ($new_matches[0] as $new_match) array_push($points, 'brackets without spaces:' . $new_match);
    }

    // variable naming
    if ( $num_matches = preg_match_all( '/$[a-zA-Z0-9_]*[a-z]+[A-Z]/', $content, $new_matches ) ) {
      foreach ($new_matches[0] as $new_match) array_push($points, 'variable naming:' . $new_match);
    }

    // sandboxed function names
    // ignore class functions
    if ( $num_matches = preg_match_all('/^\s*function\s+((?!IIRS_)[a-zA-Z0-9_]+)\(/m', $content, $new_matches) ) {
      foreach ($new_matches[0] as $new_match) array_push( $points, 'function not sandboxed with IIRS_:' . $new_match );
    }

  }

  // output
  if ( count( $points ) ) {
    print( "<h3>analysing [$file]... <a href=\"?fix_file=$file\">fix file</a></h3>\n");
    print( "<ul>\n");
    foreach ( $points as $point ) {
      print( '<li>' . htmlentities( $point ) . "</li>\n" );
    }
    print( "</ul>\n" );
  }
}


function IIRS_0_recurse_directory($base_dir) {
  $files = array();
  foreach (scandir($base_dir) as $file) {
    if ( ! (
         empty($file)
      || $file == '.' || $file == '..'
      || $file[0] == '.'
      || $file == 'jquery'
      || ( $file == 'IIRS_common' || $include_common )
    ) ) {
      $full_path = $base_dir.DIRECTORY_SEPARATOR.$file;
      if (is_dir($full_path)) {
        $files = array_merge($files, IIRS_0_recurse_directory($full_path));
      } else {
        if ($extension = substr(strrchr($file, '.'), 1)) {
          if ($extension == 'js' || $extension == 'php') {
            array_push($files, $full_path);
          }
        }
      }
    }
  }
  return $files;
}
?>