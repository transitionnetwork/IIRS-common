<?php
/* Copyright 2015, 2016 Transition Network ltd
 * This program is distributed under the terms of the GNU General Public License
 * as detailed in the COPYING file included in the root of this plugin
 */
?>

<div id="IIRS_0_debug"><pre>
debug output:
<?php
require_once( IIRS__COMMON_DIR . 'utility.php');
require_once( IIRS__COMMON_DIR . 'framework_abstraction_layer.php');
require_once( IIRS__COMMON_DIR . 'environment.php');
?>
</pre></div>

<style>
  <?php require_once( IIRS__COMMON_DIR . 'general.css'); ?>

  /* ------------------------ IIRS basic popup: host override as required */
  .IIRS_0_popup {
    position:fixed;
    display:none;
    background-color:white;
    z-index:1000;
    border:1px solid black;
    width:80%;
    height:80%;
    position:fixed;
    top:10%;
    left:10%;
    padding:10px;
    overflow-y:scroll;
  }
  .IIRS_0_popup #IIRS_0_throbber {
    position:absolute;
    top:50%;
    left:50%;
  }
  .IIRS_0_h1 {
    font-weight:bold;
    font-size:16px;
  }

  /* ------------------------ IIRS basic popup: these can be display:none if required */
  .IIRS_0_popup .IIRS_0_systemmenu {
    top:10%;
    right:10%;
    position:fixed;
    z-index:1000;
  }
  .IIRS_0_popup .IIRS_0_systemmenu img {
    float:right;
    margin:10px 2px;
    cursor:pointer;
  }
</style>

<div id="IIRS_0_popup" class="IIRS_0_popup">
  <div class="IIRS_0_systemmenu">
    <img id="IIRS_0_popup_close" src="<?php IIRS_0_print_HTML_image_src("$IIRS_URL_image_stem/close"); ?>" />
    <!-- img id="IIRS_0_popup_refresh" src="<?php IIRS_0_print_HTML_image_src("$IIRS_URL_image_stem/refresh"); ?>" / -->
  </div>
  <div class="IIRS_0_content">
    <img id="IIRS_0_throbber" src="<?php IIRS_0_print_HTML_image_src("$IIRS_URL_image_stem/throbber-active"); ?>" /> page loading...
  </div>
</div>
