<?php
session_start();
$question_id = trim(strip_tags($_GET['id']));
if (empty($question_id)) {
	header('Location: ./index.php');
}
if (empty($_SESSION['user_login'])) {
	header('Location: ./index.php');
}

require_once('classes/Questions.php');
$questions = new Questions();
require_once('classes/Answers.php');
$answers = new Answers();

$response = json_decode($questions->getSpecifiedQuestion($question_id));
if ($response->success == 0) {
	echo $response->message;
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
	<title>Arlin - <?php echo $response->question[0]->question_title ?></title>
</head>
<body>
	<button onclick="location.href='index.php'"><< BACK</button>
	<hr/>
	<p><b><?php echo $response->question[0]->question_title ?></b></p>
	<p>Asked by <?php echo $response->question[0]->username ?> on <?php echo $response->question[0]->created_date ?></p>
	<p><?php echo $response->question[0]->question_content ?></p>
	<hr/>
	<?php
	$answer_content = "";
	
	if(isset($_POST["answer_content"])) {
		$answer_question_id = $_POST["answer_question_id"];
		$answer_content = $_POST["answer_content"];
		$response = json_decode($answers->insertAnswer($answer_question_id, $answer_content));
		if($response->success == 1) {
			header('Location: ./question.php?id=' . $answer_question_id);
		} else {
			echo $response->message;
		}
	}
	$user = json_decode($_SESSION['user_login']);
	?>
	<form action="question.php?id=<?php echo $question_id ?>" method="POST">
	<input type="hidden" name="answer_question_id" value="<?php echo $question_id ?>"/>
	<p>Your answer:</p>
	<p><textarea name="answer_content" cols="100" rows="5"><?php echo $answer_content ?></textarea></p>
	<input type="submit" value="SUBMIT ANSWER"/>
	</form>
	<hr/>
	<?php
	$response = json_decode($answers->getQuestionAnswers($question_id));
	if($response->success == 1) {
		foreach($response->answer as $answer) {
			echo $answer->username . " says...<br/>";
			echo "<pre>" . $answer->answer_content . "</pre><br/>";
			echo "Answered on " . $answer->created_date . "<hr/>";
		}
	} else {
		echo $response->message;
	}
	?>
</body>
</html>