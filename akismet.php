<?php
/* code from the Akismet site:
 * http://akismet.com/development/api/#comment-check
 *
 * we could have linked in to the Wordpress Akismet plugin if it is installed
 * but we want this to work independent of framework.
 * we oculd also have used a PHP class from Akismet but want to keep it short and sweet so let's try this function first
 * also official and from Akismet :)
 *
 * returns false if it doesn't like it
 */
require_once( 'environment.php' );

function akismet_check_ti_registration_name( $name ) {
  return akismet_check( $name, 'ti_registration_name' );
}

function akismet_check_ti_registration_summary( $summary ) {
  return akismet_check( $summary, 'ti_registration_summary' );
}

function akismet_check( $text, $type = 'comment' ) {
  global $IIRS_domain_stem, $IIRS_user_ip, $akismet_API_key, $IIRS_user_agent, $IIRS_HTTP_referer;
  $ret = false;
  $user_array = IIRS_0_details_user();

  // Call to comment check
  $data = array('blog'               => $IIRS_domain_stem,
                'user_ip'            => $IIRS_user_ip,
                'user_agent'         => $IIRS_user_agent,
                'referrer'           => $IIRS_HTTP_referer,
                'permalink'          => $IIRS_domain_stem,
                'comment_type'       => $type,
                'comment_author'     => $user_array['name'],
                'comment_author_email' => $user_array['email'],
                'comment_author_url' => null,
                'comment_content'    => $text);

  print( "akismet spam check:\n" );
  var_dump( $data );
  $ret = akismet_comment_check( $akismet_API_key, $data );
  print("$ret\n");

  return $ret;
}

// Passes back true (it's spam) or false (it's ham)
function akismet_comment_check( $key, $data ) {
  $ret = false;
  $request = 'blog='. urlencode($data['blog']) .
              '&user_ip='. urlencode($data['user_ip']) .
              '&user_agent='. urlencode($data['user_agent']) .
              '&referrer='. urlencode($data['referrer']) .
              '&permalink='. urlencode($data['permalink']) .
              '&comment_type='. urlencode($data['comment_type']) .
              '&comment_author='. urlencode($data['comment_author']) .
              '&comment_author_email='. urlencode($data['comment_author_email']) .
              // '&comment_author_url='. urlencode($data['comment_author_url']) .
              '&comment_content='. urlencode($data['comment_content']);
  $host = $http_host = $key.'.rest.akismet.com';
  $path = '/1.1/comment-check';
  $port = 80;
  $akismet_ua = "WordPress/3.8.1 | Akismet/2.5.9";
  $content_length = strlen( $request );
  $http_request  = "POST $path HTTP/1.0\r\n";
  $http_request .= "Host: $host\r\n";
  $http_request .= "Content-Type: application/x-www-form-urlencoded\r\n";
  $http_request .= "Content-Length: {$content_length}\r\n";
  $http_request .= "User-Agent: {$akismet_ua}\r\n";
  $http_request .= "\r\n";
  $http_request .= $request;
  $http_response = '';
  $response_body = 'false';

  // TODO: use the IIRS_0_HTTP_request instead
  $fs = @fsockopen( $http_host, $port, $errno, $errstr, 10 );
  if( $fs == false ) {
    print( "Akismet cannot open socket\n" );
    // if Akismet is down or un-contactable then ok everything
    $ret = true;
  } else {
    fwrite( $fs, $http_request );
    while ( !feof( $fs ) )
        $http_response .= fgets( $fs, 1160 ); // One TCP-IP packet
    fclose( $fs );
    print( "Akismet HTTP response:\n" );
    var_dump( $http_response );
    $response_array = explode( "\r\n\r\n", $http_response, 2 );
    if ( count($response_array) == 2 ) $ret = ( 'true' == $response_array[1] );
  }


  return $ret;
}
?>
