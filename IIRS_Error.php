<?php
/* Copyright 2015, 2016 Transition Network ltd
 * This program is distributed under the terms of the GNU General Public License
 * as detailed in the COPYING file included in the root of this plugin
 */

class IIRS_Error {
  /* IIRS_Error is an object wrapper for an IIRS_0_set_message( ... ) call
   * use in function-return situations
   * for user interaction level, process stopping, user warnings
   *   fatal system errors
   *   external system availability notices
   *   input format user errors
   *   validation
   * use IIRS_0_set_message( ... ) directly for friendlier messages in a non-function-return situation, e.g.
   *   input checking
   *   positive message display, e.g. everything ok!, you are registered
   *   also validation
   *
   * it is an object wrapper for a user message that can be returned from an IIRS_0_*() call
   * use IIRS_0_set_translated_error_message(error) to display the error
   * which, in turn uses the standard IIRS_0_set_message(error) system to display
   *
   * see also:
   *   IIRS_is_error(function_return)
   *   IIRS_0_set_translated_error_message(error)
   */
  public $err_no;
  public $friendly_err_message;
  public $technical_err_message;
  public $level;
  public $user_action;
  public $args;

  public function __construct( $_err_no, $_friendly_err_message, $_technical_err_message = null, $_level = IIRS_MESSAGE_USER_INFORMATION, $_user_action = null, $_args = null ) {
    global $IIRS_is_live_domain, $IIRS_user_agent, $IIRS_host_domain, $IIRS_user_ip;

    $this->err_no                = $_err_no;
    $this->friendly_err_message  = $_friendly_err_message;
    $this->technical_err_message = $_technical_err_message;
    $this->level                 = $_level;
    $this->user_action           = $_user_action;
    $this->args                  = $_args;

    if ( $this->level > IIRS_MESSAGE_USER_INFORMATION && function_exists('IIRS_0_error_log') ) IIRS_0_error_log( $this );

    if ( ( $IIRS_is_live_domain || IIRS_0_debug() ) && IIRS_ADMIN_ERROR_EMAILS && $this->level > IIRS_MESSAGE_USER_WARNING ) {
      $body  = "<h1>IIRS_Error</h1>";
      $body .= "<ul>";

      // -------------------------- basic info
      $body .= "<li>$this->err_no</li>";
      $body .= "<li>$this->friendly_err_message</li>";
      $body .= "<li>$this->technical_err_message</li>";
      $body .= "<li>$this->level</li>";
      $body .= "<li>$this->user_action</li>";

      // -------------------------- context info
      $body .= "<li>User Agent: $IIRS_user_agent</li>";
      $body .= "<li>Host Domain: $IIRS_host_domain</li>";
      $body .= "<li>CLient IP: $IIRS_user_ip</li>";
      if (IIRS_0_debug()) $body .= "<li>WP_DEBUG is on so this email goes to annesley_newholm@yahoo.it</li>";

      $body .= "</ul>";

      // -------------------------- extensible args
      if ( $this->args && is_array( $this->args ) ) {
        $body .= "<ul>";
        foreach ( $this->args as $arg ) {
          ob_start(); // PHP 4,5
          var_dump( $arg );
          $body .=  "<li>" . ob_get_contents() . "</li>";
          ob_end_clean();
        }
        $body .= "</ul>";
      }

      // -------------------------- send emails to admin(s)
      // $subject = $this->__toString()
      if ( function_exists('IIRS_0_send_email') ) {
        //email to developer team at TN
        if (IIRS_0_debug()) $email_address = 'annesley_newholm@yahoo.it';
        else                $email_address = IIRS_EMAIL_TEAM_EMAIL;
        IIRS_0_send_email( $email_address, $this, $body );

        //additional email to local plugin administrator
        if ($email_address = IIRS_0_setting('additional_error_notification_email')) {
          IIRS_0_send_email( $email_address, $this, $body );
        }
      }

    }
  }

  public function __toString() {
    return "IIRS_Error($this->err_no, $this->technical_err_message)";
  }
}

function IIRS_is_error( $thing ) {
  return ( isset( $thing ) && is_object( $thing ) && get_class( $thing ) == 'IIRS_Error' );
}
?>