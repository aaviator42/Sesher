<?php
/*
Sesher - PHP session handling
by @aaviator42
v2.3

2021-07-28
--
https://github.com/aaviator42
*/


namespace Sesher;

//===CONFIGURATION===
// Name of the session id cookie
const sessionName = 'secID';

// How long before an idle session is terminated? 
// (in seconds)
const sessionTimeout = 60*60*24; 

// How long should the session cookie remain valid 
// for? (in seconds)
const cookieLifetime = 4*60*60*24;

// --Fingerprint Settings--
	// - Use fingerprints?
	const useFingerprint = true;

	// - Use user agent in fingerprint?
	const f_useUserAgent = true;

	// - Use IP address in fingerprint?
	const f_useIPaddress = true;

// Restrict sessions to HTTPS connections?
const httpsOnly = true;

//===END CONFIGURATION===

//PHP ini settings
ini_set( 'session.use_only_cookies', 	true);	// Use only cookies for session IDs
ini_set( 'session.use_strict_mode', 	true);	// Accept only valid session IDs
ini_set( 'session.use_trans_sid', 		false);	// Do not attach session ID to URLs
ini_set( 'session.cookie_httponly', 	true);	// Refuse access to session cookies from JS
ini_set( 'session.cookie_secure', 	httpsOnly);	// HTTPS only?
ini_set( 'session.sid_length', 	48);			// Session ID length
ini_set( 'session.cookie_samesite', 	"strict");		// Strict samesite
ini_set( 'session.gc_maxlifetime', 	cookieLifetime);	// Cookie lifetime
ini_set( 'session.cookie_lifetime', cookieLifetime);	// Cookie lifetime
ini_set( 'session.name', sessionName );
session_name(sessionName);

function stop(){
	if(empty($_SESSION)){
		return true;
	}
	if($_SESSION["s_active"]){
		$_SESSION["s_active"] = false;
		if(session_destroy()){
			return true;
		} else {
			return false;
		}
	}

}

function start(){
	if(!session_id()){
		return false;
	}
	if(!$_SESSION["s_active"]){
		session_regenerate_id(true);
		$_SESSION["s_active"] = true;
		$_SESSION["s_lastActivity"] = time();
		if(useFingerprint){
			$_SESSION["s_fingerprint"] = generateFingerprint();
		}
	}
	return true;
}

function generateFingerprint(){
	$fingerprint = "";
	if(f_useUserAgent){
		$fingerprint .= $_SERVER['HTTP_USER_AGENT'];
	}
	$fingerprint .= '_._'; //separator
	if(f_useIPaddress){
		$fingerprint .= $_SERVER['REMOTE_ADDR'];
	}
	$fingerprint = md5($fingerprint);
	return $fingerprint;
}

function check(){
	if($_SESSION["s_active"]){
		if(useFingerprint && (generateFingerprint() !== $_SESSION["s_fingerprint"])){
			//fingerprint doesn't match
			$_SESSION["s_active"] = false;
			session_destroy();
			return false;
		}
		
		if((time() - $_SESSION['s_lastActivity']) > sessionTimeout){
			//session has timed out
			$_SESSION["s_active"] = false;
			session_destroy();
			return false;
		} else {
			//all good
			$_SESSION['s_lastActivity'] = time();
			return true;	
		}	
		
	} else {
		//no session active
		return false;
	}
}
