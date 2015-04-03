<?php
/* Copyright 2015, 2016 Transition Network ltd
 * This program is distributed under the terms of the GNU General Public License
 * as detailed in the COPYING file included in the root of this plugin
 */
?>

<?php
/* code adapted from the Akismet site:
 * http://akismet.com/development/api/#comment-check
 * use viagra-test-123 as the author to trigger a true response
 *
 * we could have linked in to the Wordpress Akismet plugin if it is installed
 * but we want this to work independent of framework.
 * we oculd also have used a PHP class from Akismet but want to keep it short and sweet so let's try this function first
 * also official and from Akismet :)
 *
 * returns "true" if it is SPAM
 * returns "false" if it is HAM
 */
require_once( IIRS__COMMON_DIR . 'environment.php' );

function IIRS_0_akismet_check_ti_registration_name( $user_array, $name ) {
  return IIRS_0_akismet_check( $user_array, $name, 'ti_registration_name' );
}

function IIRS_0_akismet_check_ti_registration_summary( $user_array, $summary ) {
  return IIRS_0_akismet_check( $user_array, $summary, 'ti_registration_summary' );
}

function IIRS_0_akismet_check( $user_array, $text, $type = 'comment' ) {
  // Passes back TRUE (its spam) or FALSE (its ham)
  // or an IIRS_Error if a system level issue occurs
  global $IIRS_domain_stem, $IIRS_user_ip, $IIRS_user_agent, $IIRS_HTTP_referer;

  $author_data = array(
                'blog'               => $IIRS_domain_stem,
                'user_ip'            => $IIRS_user_ip,
                'user_agent'         => $IIRS_user_agent,
                'referrer'           => $IIRS_HTTP_referer,
                'permalink'          => $IIRS_domain_stem,
                'comment_type'       => $type,
                'comment_author'     => $user_array['name'],
                'comment_author_email' => $user_array['email'],
                // 'comment_author_url' => null,
                'comment_content'    => $text
  );

  return IIRS_0_akismet_comment_check( $author_data );
}

function IIRS_0_akismet_comment_check( $author_data ) {
  // Passes back TRUE (its spam) or FALSE (its ham)
  // or an IIRS_Error if a system level issue occurs
  $ret           = FALSE; // not SPAM
  $url           = 'http://' . IIRS_AKISMET_API_KEY . '.rest.akismet.com/1.1/comment-check';
  $akismet_ua    = 'WordPress/3.8.1 | Akismet/2.5.9';
  $response_body = null;

  IIRS_0_debug_print( "akismet spam check:" );
  IIRS_0_debug_var_dump( $author_data );
  $response_body = IIRS_0_http_request( $url, $author_data, null, $akismet_ua );
  if ( IIRS_is_error( $response_body ) ) {
    // the low level HTTP request got an error, so return it
    $ret = $response_body;
  } elseif ( '' == $response_body ) {
    $ret = new IIRS_Error( IIRS_AKISMET_NOTHING, 'Failed to check the entries against the Akismet SPAM database. Please try again tomorrow :)', 'Akismet returned an invalid response (empty string)', IIRS_MESSAGE_EXTERNAL_SYSTEM_ERROR, IIRS_MESSAGE_NO_USER_ACTION, $author_data );
  } elseif ( is_null($response_body) ) {
    $ret = new IIRS_Error( IIRS_AKISMET_FAILED,  'Failed to check the entries against the Akismet SPAM database. Please try again tomorrow :)', 'Akismet returned a big fat nothing', IIRS_MESSAGE_EXTERNAL_SYSTEM_ERROR, IIRS_MESSAGE_NO_USER_ACTION, $author_data );
  } else {
    IIRS_0_debug_print( "Akismet HTTP response:" );
    IIRS_0_debug_var_dump( $response_body );
    $ret = ( 'true' == $response_body );
    IIRS_0_debug_print( $ret ? 'is SPAM' : 'not spam');
  }

  return $ret;
}
?>
