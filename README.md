# Sesher
Simple library for secure PHP session handling

Current version: `2.3`  
License: `AGPLv3`

## About

Sesher simplifies secure PHP session management. It allows you to:
* Easily start/end/check active sessions
* Configure session cookies for maximum security
* End sessions that've been idle for too long
* Prevent session hijacking using rudimentary server-side fingerprinting (using the user-agent and/or IP address)

## Functions
A simple usage example can be found [here](https://github.com/aaviator42/simple-login).

### `\Sesher\start()`  

Starts session management.  
Takes no arguments. Returns `true` if session management is successfully started, `false` if it isn't. 
Must be called after `session_start()`.
Typical usage would be to call this immediately after a user logs in with the correct password.

```php
<?php
session_start();

//once user log in is successful
\Sesher\start();

```

### `\Sesher\stop()`  
Ends session management.  
Takes no arguments. Returns `true` if session management is successfully stopped, `false` if it isn't.  
Typical usage would be to call this immediately after a user logs out, or if we would like to forcefully log a user out.  
It also calls `session_destroy()`.

```php
<?php
session_start();

//once user has logged out
\Sesher\stop();

```

### `\Sesher\check()`
Checks if the session is active, and ensures that the fingerprint hasn't changed. 
Takes no arguments. Returns `true` if the session hasn't timed out, and the user's fingerprint hasn't changed. Returns `false` if it has.
If a session has timed out or the user's fingerprint has changed, then `session_destroy()` is called automatically.

```php
<?php
session_start();

if(\Sesher\check()){
  echo 'All ok!';
} else {
  echo 'User has been logged out!';
}
```


## Configuration options
At the top of `Sesher.php` there's a bunch of configuration options. Change these according to your requirements. Here's an explanation:

* `sessionName`: Change the name of the cookie that stores the session ID
* `sessionTimeout`: The amount of time (in seconds) before Sesher terminates a session for being inactive. Default is 24 hours.
* `cookieLifetime`: The amount of time (in seconds) the session cookie is valid for. Default is 4 days.
* `useFingerprint`: Should Sesher use fingerprinting to try and prevent session hijacking? 
* `f_useUserAgent`: Should Sesher use the browser's user agent while fingerprinting? 
* `f_useIPaddress`: Should Sesher use the user's IP address while fingerprinting? 
* `httpsOnly`: Restrict session cookies to HTTPS connections. Disable if you need your website to be accessible on insecure HTTP connections. 

There's also a bunch of PHP ini settings under the above, but it is recommended that you don't change those values unless you know what you're doing. 

## Fingerprinting
Sesher can use the user's User Agent and/or IP address while fingerprinting. By default, it'll use both, so if the user's IP address or the browser's UA is changed it'll terminate the session.

This is the most secure configuration, but may be inconvenient for users, because it'll terminate their sessions if they change their network (for eg, if they connect to a different WiFi hotspot). If you'd like users to remain logged in even if the IP address is changed, configure `f_useIPaddress` to `false`.

However, it is recommended that you keep fingerprinting using the UA for some basic protection.


## Requirements
1. [Supported versions of PHP](https://www.php.net/supported-versions.php). At the time of writing, that's PHP `7.3+`. Sesher will almost certainly work on older versions, but we don't test it on those, so be careful, do your own testing.


## Installation
1. Save `Sesher.php` on your server. You can rename it.
2. Include the file: `require "Sesher.php";`.




## Misc. Considerations
You really should be using TLS, because this library won't provide you much protection otherwise, except maybe some against session hijacking through the fingerprinting technique.


-----
Documentation updated `2020-09-14`
