# Cameo\GoogleAuth

Uses Google OAuth2 API to authenticate user and get their profile.

## Usage

 1. Acquire an API access in the Google API Console (https://code.google.com/apis/console/)
 1. Create a file `auth.php` in the root of your web server
 1. Put this code:
 
```php
<?php

session_start();

require_once __DIR__ . "/../vendor/autoload.php";

$consumer = new Cameo\GoogleAuth\Consumer(array(
     "client_id" => "xxx.apps.googleusercontent.com",
     "client_secret" => "your client secret",
     "redirect_uri" => "http://localhost:8000/auth.php"
));

if (!$consumer->isCodeSent()) {
     $consumer->getConsent();
} else {
     $session = $consumer->getSession();
     $profile = $session->getProfile();
     var_dump($profile);
}
```
 1. Goto to http://localhost:8000/auth.php in your browser
 1. Enjoy!

The strategy in this example is to use the same page for the redirect to google 
and the callback that receives the authorization code. There is no storage or 
session handling... it's up to you to keep that wherever you want.

## TODO

 * Implement anti-forgery state token
 * Id token validation
 * Documentation
 * Abstract request and response
 * Unit tests

## Links

 * https://developers.google.com/accounts/docs/OAuth2WebServer
 * https://developers.google.com/accounts/docs/OAuth2
 * https://developers.google.com/accounts/docs/OAuth2Login
 * https://developers.google.com/oauthplayground/
 * https://github.com/jamesattard/googleapi/blob/master/get_google_tokens.php
 * http://www.lornajane.net/posts/2012/using-oauth2-for-google-apis-with-php