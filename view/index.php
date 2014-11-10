<div id="IIRS_0_debug"><pre>
debug output:
<?php
global $debug_environment;
require_once( IIRS__COMMON_DIR . 'utility.php' );
require_once( IIRS__COMMON_DIR . 'framework_abstraction_layer.php' );
require_once( IIRS__COMMON_DIR . 'environment.php' );
IIRS_0_debug_print( $debug_environment );

// ------------------------------------------------------- external configuration
// need to get a specific TI in this view
// by default we use the users TI
// but also this view can display one if already set
// and also the mode to view the data is sent through:
//   list_mode: true indicates that the TI is appearing in a list
$IIRS_error = NULL;
if ( ! isset( $TI )        ) $TI        = IIRS_0_details_TI_user();
if ( ! isset( $list_mode ) ) $list_mode = false;
$full_mode = ! $list_mode;
if ( ! $TI || ! is_array( $TI ) ) {
  $IIRS_error = new IIRS_Error( IIRS_USER_NO_ASSOCIATED_TI, 'There is no Initiative associated with this user', 'TI not linked to this user',  IIRS_MESSAGE_USER_ERROR );
  IIRS_0_debug_print( $IIRS_error );
}

// ------------------------------------------------------- field pre-formatting
$website = NULL;
if ( ! isset( $TI['domain'] ) || empty( $TI['domain'] ) ) $website = IIRS_0_translation( 'currently no website' );
else $website = '<a target="_blank" href="http://' . IIRS_0_escape_for_HTML_href( $TI['domain'] ) . '">' . IIRS_0_translation( 'website' ) . '</a>';
$maps_href = "https://www.google.com/maps/@$TI[location_latitude],$TI[location_longitude],16z";
?>
</pre></div>

<div id="IIRS_0">
  <?php
  if ( $IIRS_error ) {
      // IIRS_0_set_translated_error_message( ... ) uses IIRS_0_set_message( ... )
      IIRS_0_set_translated_error_message( $IIRS_error );
  } else {
  ?>
    <div>
      <!-- the framework always prints the title -->
      <?php if ( ! $list_mode ) IIRS_0_print_language_selector(); ?>
    </div>

    <div class="IIRS_0_website"><?php IIRS_0_print_HTML( $website ); ?></div>

    <?php if ( $list_mode ) { ?>
      <a target="_blank" href="<?php IIRS_0_print_HTML_href( $maps_href ); ?>">
        <img class="IIRS_0_map_thumb" src="/IIRS/images/google_map_icon.png" />
      </a>
    <?php } ?>

    <p class="IIRS_0_summary"><?php IIRS_0_print_HTML( $TI['summary'] ); ?></p>

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
  <?php } ?>
</div>