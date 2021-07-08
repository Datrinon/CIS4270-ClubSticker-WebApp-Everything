<?php
// All files that need to be included by all the CIS 4270 applications.
// @author jam
// @version 180426

include_once(NON_WEB_BASE_DIR .'common/includes/validationFunctions.php');
include_once(NON_WEB_BASE_DIR .'common/includes/sanitizationFunctions.php');
//Remember to uncomment line 10 after preparing all SQL statements.
include_once(NON_WEB_BASE_DIR .'common/db/DBAccess.php');
// include_once(NON_WEB_BASE_DIR .'common/db/DBAccessAchtung.php');
include_once(NON_WEB_BASE_DIR .'common/db/DAM.php');

// addition for registration assignment
include_once(NON_WEB_BASE_DIR .'common/includes/csrfFunctions.php');
// addition for login assignment
include_once(NON_WEB_BASE_DIR .'common/includes/sessionFunctions.php');

// addition for upload assignment
include_once(NON_WEB_BASE_DIR .'common/includes/uploadFunctions.php');



