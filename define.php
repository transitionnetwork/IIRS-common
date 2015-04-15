<?php
/* Copyright 2015, 2016 Transition Network ltd
 * This program is distributed under the terms of the GNU General Public License
 * as detailed in the COPYING file included in the root of this plugin
 */

// keys
define( 'IIRS_GOOGLE_API_KEY', 'AIzaSyCZjrltZvehXP1dnAZCw41NN8VbZCKFf44' );
define( 'IIRS_AKISMET_API_KEY', '2cd22c7e9c7e' );

// settings
define( 'IIRS_0_CLEAR_PASSWORD', '****' );
define( 'IIRS_ADMIN_ERROR_EMAILS', TRUE );
define( 'IIRS_EMAIL_TEAM_EMAIL', 'registrar@transitionnetwork.org' ); // => annesley_newholm@yahoo.it
define( 'IIRS_EMAIL_TEAM_LINK', '<a href="mailto:' . IIRS_EMAIL_TEAM_EMAIL . '">' . IIRS_EMAIL_TEAM_EMAIL . '</a>' );
define( 'IIRS_0_MAX_NEARBY', 10 );
define( 'IIRS_VERSION', '1.0 beta' );
define( 'IIRS_TRANSITION_NAMESPACE', 'http://transitionnetwork.org/namespaces/2014/transition' );
define( 'IIRS_JAVASCRIPT_BACK', 'javascript:history.go(-1)' );
define( 'IIRS_TRACKER_HREF', 'https://docs.google.com/spreadsheets/d/16BiaSIH10eCoEVGiPae1Ib6zsmfO3QcHEHR77QzRJUw/edit#gid=0' );

// message levels
define( 'IIRS_MESSAGE_USER_INFORMATION', 1 );
define( 'IIRS_MESSAGE_USER_WARNING', 2 );
define( 'IIRS_MESSAGE_USER_ERROR', 3 );
define( 'IIRS_MESSAGE_SYSTEM_ERROR', 4 );
define( 'IIRS_MESSAGE_EXTERNAL_SYSTEM_ERROR', 5 ); // e.g. Google Maps down

// message ids
define( 'IIRS_MESSAGE_SUCCESS_REGISTRATION', 1);
define( 'IIRS_MESSAGE_LOOKS_LIKE_A_DOMAIN', 2);

// function defaults
define( 'IIRS_MESSAGE_NO_USER_ACTION', NULL );
define( 'IIRS_RAW_USER_INPUT', TRUE );

//defines
define( 'IGNORE_TRANSLATION', FALSE ); // simply allows IGNORE_TRANSLATION to be placed in code to stop the translator adding the string

// global unique error code definitions
define( 'IIRS_AKISMET_NOTHING', 1000 );
define( 'IIRS_AKISMET_FAILED',  2000 );
define( 'IIRS_AKISMET_SAYS_SPAM',  2001 );
define( 'IIRS_HTTP_FAILED',  1010 );
define( 'IIRS_HTTP_NOT_FOUND',  1011 );
define( 'IIRS_INVALID_TI_INPUTS',  1020 );
define( 'IIRS_USER_ALREADY_HAS_TI', 1022 );
define( 'IIRS_NO_INPUTS',  1023 );
define( 'IIRS_USER_ALREADY_REGISTERED',  1024 );
define( 'IIRS_TI_EXISTS_SAME_NAME',  1025 );
define( 'IIRS_TI_EXISTS_SAME_LOCATION',  1026 );
define( 'IIRS_FAILED_USER_DELETE', 1027 );
define( 'IIRS_FAILED_USER_UPDATE', 1028 );
define( 'IIRS_LOGIN_FAILED', 1029 );
define( 'IIRS_USER_NO_ASSOCIATED_TI', 1030 );
define( 'IIRS_LOCATION_XML_INVALID', 1130 );
define( 'IIRS_GEOCODE_RESULTS_EMPTY', 1131 );
define( 'IIRS_GEOCODE_REGISTRATION_WITHOUT_LOCATION', 1132 );
define( 'IIRS_LOCATION_PROVIDER_INVALID', 1135 );
define( 'IIRS_REGISTRATION_EMAIL_FAILED', 1140 );
define( 'IIRS_INVALID_WEBSITE_DNS', 1141 );
define( 'IIRS_WHOIS_ERROR', 1142 );
define( 'IIRS_WHOIS_NO_DOMAIN_SERVER', 1143 );
define( 'IIRS_URL_404', 1150 );              // general IIRS widget_folder not found
?>
