<?php
/* Copyright 2015, 2016 Transition Network ltd
 * This program is distributed under the terms of the GNU General Public License
 * as detailed in the COPYING file included in the root of this plugin
 */
?>

<?php
  $nice_domains = IIRS_0_get_nice_domains( '{$domain_part}' );
  $all_TLDs     = IIRS_0_get_nice_TLDs();
?>
<h3>domain checking</h3>
<p>
  When someone registers their Initiative, the IIRS will look for their website.
  You can edit the list of domain strings to check by translating the "<i>nice_domains</i>" string through your standard translation plugin (like WPML for Wordpress).
  It should look exactly like this:
  <pre>transition{$domain_part},transitiontown{$domain_part},{$domain_part}transitiontown,{$domain_part}transition,{$domain_part}intransition</pre>
  where {$domain_part} will be replaced with the dynamic town name entered by the user.<br />
  Below is the current list of domain name checks that will be run.
  Do not create more than 5 because, currently, the checking is time consuming.
  <?php if ( IIRS_0_translation( 'nice_domains' ) == 'nice_domains' ) print( 'It is currently the <b>English</b> default list.' ); ?>
  <?php if ( count( $nice_domains ) * count( $all_TLDs ) > 20 ) print( '<br/><b style="color:red">Too many domain checks (nice_domains * nice_tlds > 20). This will take too much time!</b>' ); ?>
</p>
<ul>
  <?php
    $nice_domains = IIRS_0_get_nice_domains( '{$domain_part}' );
    foreach ( $nice_domains as $nice_domain ) {
      print( "<li>$nice_domain</li>" );
    }
  ?>
</ul>

<p>
  The Top-Level-Domains are also configurable by translating the "<i>nice_tlds</i>" string.
  It should look something like this:
  <pre>org,org.uk,com,net</pre>
  Below is the current list of domain name checks that will be run.
  <?php if ( IIRS_0_translation( 'nice_tlds' ) == 'nice_tlds' ) print( 'It is currently the <b>English</b> default list with your domain added.' ); ?>
</p>
<ul>
  <?php
    foreach ( $all_TLDs as $TLD ) {
      print( "<li>$TLD</li>" );
    }
  ?>
</ul>
