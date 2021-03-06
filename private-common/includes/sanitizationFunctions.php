<?php 
/**
 * Sanitization functions.
 * Make sanitizing easy, and you will do it often.
 * 
 * @author jam
 * @version 180415
 */

// Sanitize for HTML output 
function h($string) {
	return htmlspecialchars($string);
}

// Unsanitize for HTML output 
function unH($string) {
	return htmlspecialchars_decode($string);
}

// Sanitize for JavaScript output
function j($string) {
	return json_encode($string);
}

// Sanitize for use in a URL
function u($string) {
	return urlencode($string);
}

// Sanitize the request method
function hRequestMethod () {
    return htmlspecialchars($_SERVER['REQUEST_METHOD']);
}

// Usage examples, leave commented out
// echo h("<h1>Test string</h1><br />");
// echo j("'}; alert('Gotcha!'); //");
// echo u("?title=Working? Or not?");

// custom method
// dan : sanitize elements from the session superglobal.
// one case where this might be useful is during login -- what if the user's chosen name were
// actually some javascript code?
function hSession($parameter) {
	// return $parameter;
	return htmlspecialchars($_SESSION[$parameter]);
}

