<?php
if(!isset($_SESSION)) { 
	session_start(); 
}
require_once "DBConnection.php";
require_once "Questions.php";

class Answers {
	
	public function checkAnsweredQuestion($question_id) {
		$query = mysql_query("SELECT * FROM answers WHERE answer_question_id='$question_id'");
		return mysql_num_rows($query);
	}
	
	public function getQuestionAnswers($answer_question_id) {
		$db = new DBConnection();
		$query = mysql_query("SELECT answers.created_date, users.username, 
					answer_content FROM answers  
					INNER JOIN users ON users.user_id = answers.created_by 
					WHERE answer_question_id='$answer_question_id'");
		if($query) {
			if(mysql_num_rows($query) > 0) {
				$rows = array();
				
				while($r = mysql_fetch_assoc($query)) {
					$rows[] = $r;
				}
		
				$response["success"] = 1;
				$response["answer"] = $rows;
			} else {
				$response["success"] = 0;
				$response["message"] = "This question has not been answered yet.";
			}
		} else {
			$response["success"] = 0;
			$response["message"] = "Failed to get all question answers!";
		}
		
		return json_encode($response);
	}
	
	public function insertAnswer($answer_question_id, $answer_content) {
		$db = new DBConnection();
		$user = json_decode($_SESSION['user_login']);
		$user_id = $user->user_id;
		$answer_question_id = trim(strip_tags($answer_question_id));
		$answer_content = trim(strip_tags($answer_content));
		if (empty($answer_content)) {
			$response["success"] = 0;
			$response["message"] = "Answer content must be filled!";
		} else {
			$query = mysql_query("INSERT INTO answers SET created_date=CURRENT_TIMESTAMP, created_by='$user_id', answer_question_id='$answer_question_id', answer_content='$answer_content'");
			$questions = new Questions();
			$respons = json_decode($questions->addAnswerCount($answer_question_id));
			if($query and $respons->success == 1) {
				$response["success"] = 1;
				$response["message"] = "Answer has been successfully inserted!";
			} else if($respons->success == 0) {
				$response["success"] = 0;
				$response["message"] = $respons->message;
			} else {
				$response["success"] = 0;
				$response["message"] = "Failed to insert new answer!";
			}
		}
		return json_encode($response);
	}

}