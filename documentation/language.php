<?php
global $debug_environment;
require_once('framework_abstraction_layer.php');
require_once('utility.php');
require_once('environment.php');

/* IIRS Language translations
 * the framework portion of this documentation should present details
 * on how to carry out translations if the installation language is not available
 * <a name="translation"> Anchor the #translation section so the jump here works
 */
?>

<p>
<?php if ( IIRS_0_language_is_supported() ) { ?>
  The plugin should be displaying in your language, <strong><?php print(IIRS_0_locale()); ?></strong>.
  Contact <?php print( IIRS_EMAIL_TEAM_LINK ); ?> if there are problems.
<?php } else { ?>
  This plugin is not in your language: contact <?php print( IIRS_EMAIL_TEAM_LINK ); ?> and we will work with you to translate.
  <?php print( IIRS_0_framework() ); ?> framework installed in <strong><?php print(IIRS_0_locale()); ?></strong>.
  Current available IIRS Plugin languages are <strong>[<?php print( implode( ', ', IIRS_0_languages() )); ?>]</strong>.
  See below in the <a href="#translation">translation help section</a> for framework help with translation.
<?php } ?>
</p>
