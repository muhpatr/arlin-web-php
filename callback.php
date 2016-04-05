<?php
/**
 * @file
 * Take the user when they return from Twitter. Get access tokens.
 * Verify credentials and redirect to based on response from Twitter.
 */

/* Start session and load lib */
session_start();
require_once('classes/TwitterOAuth.php');
require_once('classes/TwitterConfig.php');
require_once('classes/Users.php');

/* If the oauth_token is old redirect to the connect page. */
if (isset($_REQUEST['oauth_token']) && $_SESSION['oauth_token'] !== $_REQUEST['oauth_token']) {
	$_SESSION['oauth_status'] = 'oldtoken';
	session_destroy();
	header('Location: ./index.php');
}

/* Create TwitteroAuth object with app key/secret and token key/secret from default phase */
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);

/* Request access tokens from twitter */
$access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);

/* Remove no longer needed request tokens */
unset($_SESSION['oauth_token']);
unset($_SESSION['oauth_token_secret']);

$content = $connection->get('account/verify_credentials');
$user = json_encode($content);
$user = json_decode($user, TRUE);

/* Insert to database */
$users = new Users();
$response = json_decode($users->registerUser($user['id'], $user['screen_name'], str_replace("_normal", "", $user['profile_image_url'])));
if ($response->success == 0) {
	session_destroy();
}

/* If HTTP response is 200 continue otherwise send to connect page to retry */
if (200 != $connection->http_code) {
	/* Save HTTP status for error dialog on connnect page.*/
	session_destroy();
}
header('Location: ./index.php');