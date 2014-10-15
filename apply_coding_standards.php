<?php
// view-source:tnv3.dev/wp-content/plugins/IIRS/IIRS_common/read_translations.php?format=po&start_directory=/var/www/wordpress/tnv3/wp-content/plugins/IIRS_0_print_translation
$start_directory    = ( isset($_GET['start_directory']) ? $_GET['start_directory'] : dirname(__FILE__) );
$fix_file           = ( $_GET['fix_file'] );
$standard_wordpress = ( ! isset( $_GET['standard'] ) || $_GET['standard'] == 'wordpress' );
$standard_drupal    = ( isset( $_GET['standard'] ) && $_GET['standard'] == 'drupal' );
?>

<h1>file report</h1>
<p>
  <a href="?standard=wordpress">check against WordPress standard</a>
</p>

<?php
// get all file paths
$files           = IIRS_0_recurse_directory($start_directory);

// analyse and update contents
foreach ($files as $file) {
  $extension = substr(strrchr($file, '.'), 1);
  $points    = array();
  $content   = file_get_contents($file);
  $fix_this  = ( $fix_file == $file );

  if ( $extension == 'js' && $standard_wordpress ) {
    // ----------------------------------------------------------------------- Javascript PHP
    // filename
    if ( strstr( $file, '_' ) !== FALSE ) {
      array_push( $points, 'filename contains underscores (_)');
    }

    // brackets
    if ( $num_matches = preg_match_all('/[a-zA-Z0-9_]+\([^ )][^)]*\)/', $content, $new_matches) ) {
      if ( $fix_this ) {
        $points = array_merge($points, $new_matches[0]);
      } else {
        $points = array_merge($points, $new_matches[0]);
      }
    }

  } elseif ( $extension == 'php' && $standard_wordpress ) {
    // ----------------------------------------------------------------------- WordPress PHP
    // filename
    if ( strstr( $file, '-' ) !== FALSE ) {
      array_push( $points, 'filename contains dashes (-)');
    }

    // brackets
    if ( $num_matches = preg_match_all('/[a-zA-Z0-9_]+\([^ )][^)]*\)/', $content, $new_matches) ) {
      if ( $fix_this ) {
        $points = array_merge($points, $new_matches[0]);
      } else {
        $points = array_merge($points, $new_matches[0]);
      }
    }

    // variable naming
    $num_matches = preg_match_all('/$[a-zA-Z0-9_]*[a-z]+[A-Z]/', $content, $new_matches);
    if ($num_matches) $points = array_merge($points, $new_matches[0]);
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