<?php
session_start();
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
	if (empty($_SESSION['user_login'])) {
		session_destroy();
		?>
		<a href="login.php">Sign In Via Twitter</a>
		<?php
	} else {
		$user = json_decode($_SESSION['user_login']);
		?>
		<h3>Hello, <?php echo $user->user_id; ?>!</h3>
		<p>Your username is: <b><?php echo $user->username; ?></b> (<a href="logout.php">Click here</a> to logout)</p>
		<p>This is your profile picture:</p>
		<img width="50px" height="50px" src="<?php echo $user->avatar; ?>"/>
		<hr/>
		<button onclick="location.href='postquestion.php'">ASK A QUESTION</button>
		<hr/>
		<?php
		require_once('classes/Questions.php');
		$questions = new Questions();
		$response = json_decode($questions->getQuestions());
		if($response->success == 1) {
			foreach($response->question as $question) {
				echo $question->answer_count . " answer(s)<br/>";
				echo "Asked by " . $question->username . "<br/>";
				echo $question->created_date . "<br/>";
				echo "<a href='question.php?id=" . $question->question_id . "'>" . $question->question_title . "</a><hr/>";
			}
		} else {
			echo $response->message;
		}
	} 
	?>
</body>
</html>