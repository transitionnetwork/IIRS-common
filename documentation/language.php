<?php
/* Copyright 2015, 2016 Transition Network ltd
 * This program is distributed under the terms of the GNU General Public License
 * as detailed in the COPYING file included in the root of this plugin
 */
?>

<?php
global $debug_environment;
require_once( IIRS__COMMON_DIR . 'utility.php' );
require_once( IIRS__COMMON_DIR . 'framework_abstraction_layer.php' );
require_once( IIRS__COMMON_DIR . 'environment.php' );

/* IIRS Language translations
 * the framework portion of this documentation should present details
 * on how to carry out translations if the installation language is not available
 * <a name="translation"> Anchor the #translation section so the jump here works
 */
global $IIRS_host_TLD;
?>

<p>
<?php if ( IIRS_0_language_is_supported() ) { ?>
  The plugin should be displaying in your language, <strong><?php print(IIRS_0_locale()); ?></strong>.
  Contact <?php print( IIRS_EMAIL_TEAM_LINK ); ?> if there are problems.
<?php } else { ?>
  This plugin is not in your language: contact <?php print( IIRS_EMAIL_TEAM_LINK ); ?> and we will work with you to translate.
  <?php print( IIRS_0_framework_name() ); ?> framework installed in <strong><?php print(IIRS_0_locale()); ?></strong>.
  Current available IIRS Plugin languages are <strong>[<?php print( implode( ', ', IIRS_0_available_languages() )); ?>]</strong>.
  See below in the <a href="#translation">translation help section</a> for framework help with translation.
<?php } ?>
</p>

<?php
// http://en.wikipedia.org/wiki/Top-level_domain
// http://en.wikipedia.org/wiki/Generic_top-level_domain
// com, org, net, info
// aero, biz, coop, info, museum, name, and pro
if ( IIRS_0_is_generic_TLD( $IIRS_host_TLD ) ) {
?>
<h3>country specific location results bias</h3>
<p>
  Your website domain (<i>.<?php print( $IIRS_host_TLD ); ?></i>) is not a <a target="_blank" href="http://en.wikipedia.org/wiki/List_of_Internet_top-level_domains#Country_code_top-level_domains">country code Top-Level-Domain</a>.<br/>
  <strong>This means that location results will not centre on a specific country.</strong>
</p>
<p>
  Location results for town names have a "bias". That means that results will be centered on a particular country.<br/>
  On an English server (.co.uk) searching for "Oakley" will show "Oakley, Hampshire, UK".<br/>
  On an American server (.com) searching for "Oakley" will show "Oakley, New Hampshire, USA".
</p>
<p>
  You need to translate the "<i>region_bias</i>" string with your in-built translator:
  change it to the <a target="_blank" href="http://en.wikipedia.org/wiki/List_of_Internet_top-level_domains#Country_code_top-level_domains">Top-Level-Domain of the country</a> where you would like the results to come from.
  Note that this is a bias, not a limit.
</p>
<?php } ?>
