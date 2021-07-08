<?php


// Must call session_start() before this loads

// Generate a token for use with CSRF protection.
// Does not store the token.
function csrf_token()
{
    return md5(uniqid(rand(), TRUE));
}

// Generate and store CSRF token in user session.
// Requires session to have been started already.
function create_csrf_token()
{
    $token = csrf_token();
    $_SESSION['csrf_token'] = $token;
    $_SESSION['csrf_token_time'] = time();
    return $token;
}

// Destroys a token by removing it from the session.
function destroy_csrf_token()
{
    $_SESSION['csrf_token'] = null;
    $_SESSION['csrf_token_time'] = null;
    return true;
}

// Return an HTML tag including the CSRF token 
// for use in a form.
// Usage: echo csrf_token_tag();
function csrf_token_tag()
{
    $token = create_csrf_token();
    return "<input type=\"hidden\" name=\"csrf_token\" value=\"" . $token . "\">";
}

// Returns true if user-submitted POST token is
// identical to the previously stored SESSION token.
// Returns false otherwise.
function csrf_token_is_valid()
{
    
    if (!empty(hPOST('csrf_token'))) {

        // echo "hSession token: " . hSession('csrf_token'); // debug

        $user_token = hPOST('csrf_token');
        $stored_token = $_SESSION['csrf_token'] ? hSession('csrf_token') : '';
        return $user_token === $stored_token;
    } else {
        return false;
    }
}

// You can simply check the token validity and 
// handle the failure yourself, or you can use 
// this "stop-everything-on-failure" function. 
function die_on_csrf_token_failure()
{
    if (!csrf_token_is_valid()) {
        die("CSRF token validation failed.");
    }
}

// Optional check to see if token is also recent
function csrf_token_is_recent()
{
    $max_elapsed = 60 * 60; // minutes.
    // check to see that a time was set.
    if (!empty(hSession('csrf_token_time'))) {
        // get the token's time.
        $stored_time = hSession('csrf_token_time');
        
        // use token birth timestamp and current time to see if it passes $max_elapsed limit
        if (($stored_time + $max_elapsed) >= time()) {
            return true;
        } else {
            return false;
        }
    // If no time was set, destroy the token.
    } else {
        destroy_csrf_token();
        return false;
    }
}
