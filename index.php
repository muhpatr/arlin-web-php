<?php
session_start();
require_once('classes/TwitterOAuth.php');
require_once('classes/TwitterConfig.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="Centralized Q&A for city traffic and trip discussion">
	<meta name="author" content="Michi">
	<title>Arlin</title>
</head>
<body>
	<?php
	if (empty($_SESSION['access_token']) || empty($_SESSION['access_token']['oauth_token']) || empty($_SESSION['access_token']['oauth_token_secret'])) {
		session_destroy();
		?>
		<a href="login.php">Sign In Via Twitter</a>
		<?php
	} else {
		/* Get user access tokens out of the session. */
		$access_token = $_SESSION['access_token'];

		/* Create a TwitterOauth object with consumer/user tokens. */
		$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);

		/* If method is set change API call made. Test is called by default. */
		$content = $connection->get('account/verify_credentials');
		$user = json_encode($content);
		$user = json_decode($user, TRUE);
		$avatar = str_replace("_normal", "", $user["profile_image_url"]);
		?>
		<h3>Hello, <?php echo $user['name']; ?>!</h3>
		<p>Your username is: <b><?php echo $user['screen_name']; ?></b> (<a href="logout.php">Click here</a> to logout)</p>
		<p>This is your profile picture:</p>
		<img src="<?php echo $avatar; ?>"/>
		<?php
	} 
	?>
</body>
</html>