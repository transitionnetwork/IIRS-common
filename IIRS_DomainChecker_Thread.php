<?php
/* Copyright 2015, 2016 Transition Network ltd
 * This program is distributed under the terms of the GNU General Public License
 * as detailed in the COPYING file included in the root of this plugin
 */
?>

<?php
if ( ! class_exists( 'Thread' ) ) {
  IIRS_0_debug_print( 'no PECL Thread class: implementing synchronous domain checking' );

  class Thread {
    // implement a non-Threaded solution
    public function start() {$this->run();}
  }
}

class IIRS_DomainChecker_Thread extends Thread {
  /* DNS checking domains asynchronously
   * PECL pthreads >= 2.0.0
   * http://php.net/manual/en/class.thread.php
   */
  static $thread_count  = 0;
  static $valid_domains = array();

  public function __construct( $_domain ) {
    $this->domain     = $_domain;
    $this->ip_address = NULL;
    $this->valid_dns  = NULL;

    // immediate start
    // execute the implemented run() function in a new thread
    self::$thread_count++;
    $this->start();
  }

  public static function waitAllFinished() {
    $max_seconds = 30;
    while ( ! self::allFinished() && $max_seconds ) {
      sleep( 1 );
      $max_seconds--;
    }
    return self::$valid_domains;
  }

  public static function allFinished() {
    return ( self::$thread_count == 0 );
  }

  public function run() {
    $this->ip_address = gethostbyname( $this->domain );
    $this->valid_dns  = ( $this->ip_address != $this->domain );

    if ( $this->valid_dns ) array_push( self::$valid_domains, $this->domain );
    self::$thread_count--;
  }
}
?>