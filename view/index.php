<div id="IIRS_0_debug"><pre>
debug output:
<?php
global $debug_environment;
require_once('framework_abstraction_layer.php');
require_once('utility.php');
require_once('environment.php');
print($debug_environment);

// ------------------------------------------------------- external configuration
// need to get a specific TI in this view
// by default we use the users TI
// but also this view can display one if already set
// and also the mode to view the data is sent through:
//   list_mode: true indicates that the TI is appearing in a list
if ( ! isset( $TI )        ) $TI        = IIRS_0_details_TI_user();
if ( ! isset( $list_mode ) ) $list_mode = false;
$full_mode = ! $list_mode;

// ------------------------------------------------------- field pre-formatting
$website   = ( empty( $TI['domain'] ) ? IIRS_0_translation( 'currently no website' ) : '<a target="_blank" href="http://' . $TI['domain'] . '">' . IIRS_0_translation( 'website' ) . '</a>' );
$maps_href = "https://www.google.com/maps/@$TI[location_latitude],$TI[location_longitude],16z";
?>
</pre></div>

<div id="IIRS_0">
  <div>
    <!-- the framework always prints the title -->
    <?php if ( ! $list_mode ) IIRS_0_print_language_selector(); ?>
  </div>

  <div class="IIRS_0_website"><?php print($website); ?></div>

  <?php if ( $list_mode ) { ?>
    <a target="_blank" href="<?php print( $maps_href ); ?>">
      <img class="IIRS_0_map_thumb" src="/IIRS/images/google_map_icon.png" />
    </a>
  <?php } ?>

  <p class="IIRS_0_summary"><?php print($TI['summary']); ?></p>

  <?php if ( $full_mode ) { ?>
    <div class="IIRS_0_map">
      map loading...
      <?php IIRS_0_print_HTML_encode_array( $TI ); ?>
      <div class="markers">
        <div class="TI">
          <?php IIRS_0_print_HTML_encode_array( $TI ); ?>
        </div>
      </div>
    </div>
  <?php } ?>
</div>