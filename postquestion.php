<?php
session_start();
if (empty($_SESSION['user_login'])) {
	header('Location: ./index.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="Centralized Q&A for city traffic and trip discussion">
	<meta name="author" content="Michi">
	<title>Arlin - Ask A Question</title>
</head>
<body>
	<button onclick="location.href='index.php'"><< BACK</button>
	<hr/>
	<?php
	$question_title = "";
	$question_content = "";
	
	if(isset($_POST["question_title"]) or isset($_POST["question_content"])) {
		require_once('classes/Questions.php');
		$question_title = $_POST["question_title"];
		$question_content = $_POST["question_content"];
		$questions = new Questions();
		$response = json_decode($questions->insertQuestion($question_title, $question_content));
		if($response->success == 1) {
			header('Location: ./index.php');
		} else {
			echo $response->message;
		}
	}
	$user = json_decode($_SESSION['user_login']);
	?>
	<form action="postquestion.php" method="POST">
	<p>Question title:</p>
	<p><input type="text" name="question_title" value="<?php echo $question_title ?>"/></p>
	<p>Question content:</p>
	<p><textarea name="question_content" cols="100" rows="5"><?php echo $question_content ?></textarea></p>
	<input type="submit" value="SUBMIT QUESTION"/>
	</form>
</body>
</html>