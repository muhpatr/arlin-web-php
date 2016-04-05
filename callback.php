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
$user_profile = json_encode($content);
$user_profile = json_decode($user_profile, TRUE);
$user['user_id'] = $user_profile['id'];
$user['username'] = $user_profile['screen_name'];
$user['avatar'] = str_replace("_normal", "", $user_profile['profile_image_url']);

/* Insert to database */
$users = new Users();
$response = json_decode($users->registerUser($user['user_id'], $user['username'], $user['avatar']));
if ($response->success == 1) {
	$_SESSION['user_login'] = json_encode($user);
}

/* If HTTP response is 200 continue otherwise send to connect page to retry */
if (200 != $connection->http_code) {
	/* Save HTTP status for error dialog on connnect page.*/
	session_destroy();
}
header('Location: ./index.php');