<?php

/**
 * Contains deployment constants for the GuitarShop application.
 * 
 * @author jam
 * @version 180428
 */

// Access files base directory
define ('ACCESS_BASE_DIR', APP_NON_WEB_BASE_DIR . 'access/');

// Database access credentials location
// define ('DB_ACCESS_CREDENTIALS_FILE', ACCESS_BASE_DIR . 'dbAccess.csv');
define ('DB_ACCESS_CREDENTIALS_FILE', ACCESS_BASE_DIR . 'clubsticker-dbAccess.csv');

// dan: for use in sessionFunctions
define ('INVALID_SESSION_PAGE_TITLE', 'Club Sticker - Please Log In');
define ('INVALID_SESSION_PAGE', 'views/invalidSession.php');



