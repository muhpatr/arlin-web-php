<?php
if(!isset($_SESSION)) { 
	session_start(); 
}
require_once "DBConnection.php";
require_once "Answers.php";

class Questions {
	
	public function getQuestions() {
		$db = new DBConnection();
		$query = mysql_query("SELECT question_id, questions.created_date, users.username, 
					question_title, answer_count FROM questions INNER JOIN users ON 
					users.user_id = questions.created_by");
		if($query) {
			if(mysql_num_rows($query) > 0) {
				$rows = array();
				
				while($r = mysql_fetch_assoc($query)) {
					$rows[] = $r;
				}
		
				$response["success"] = 1;
				$response["question"] = $rows;
			} else {
				$response["success"] = 0;
				$response["message"] = "There is no question data.";
			}
		} else {
			$response["success"] = 0;
			$response["message"] = "Failed to get all questions data!";
		}
		
		return json_encode($response);
	}
	
	public function getSpecifiedQuestion($question_id) {
		$db = new DBConnection();
		$query = mysql_query("SELECT questions.created_date, users.username, 
					question_title, question_content, answer_count FROM questions 
					INNER JOIN users ON users.user_id = questions.created_by 
					WHERE question_id='$question_id'");
		if($query) {
			if(mysql_num_rows($query) > 0) {
				$rows = array();
				
				while($r = mysql_fetch_assoc($query)) {
					$rows[] = $r;
				}
		
				$response["success"] = 1;
				$response["question"] = $rows;
			} else {
				$response["success"] = 0;
				$response["message"] = "There is no question data.";
			}
		} else {
			$response["success"] = 0;
			$response["message"] = "Failed to get specified question!";
		}
		
		return json_encode($response);
	}
	
	public function insertQuestion($question_title, $question_content) {
		$db = new DBConnection();
		$user = json_decode($_SESSION['user_login']);
		$user_id = $user->user_id;
		$question_title = trim(strip_tags($question_title));
		$question_content = trim(strip_tags($question_content));
		if(empty($question_title)){
			$response["success"] = 0;
			$response["message"] = "Question title must be filled!";
		} else if (empty($question_content)) {
			$response["success"] = 0;
			$response["message"] = "Question content must be filled!";
		} else {
			$query = mysql_query("INSERT INTO questions SET created_date=CURRENT_TIMESTAMP, created_by='$user_id', question_title='$question_title', question_content='$question_content'");
			if($query) {
				$response["success"] = 1;
				$response["message"] = "Question has been successfully inserted!";
			} else {
				$response["success"] = 0;
				$response["message"] = "Failed to insert new question!";
			}
		}
		return json_encode($response);
	}

	public function addAnswerCount($question_id) {
		$db = new DBConnection();
		$answers = new Answers();
		$answer_count = $answers->checkAnsweredQuestion($question_id);
		if($answer_count > 0) {
			$query = mysql_query("UPDATE questions SET is_answered=1, answer_count='$answer_count' WHERE question_id='$question_id'");
		} else {
			$query = mysql_query("UPDATE questions SET is_answered=0, answer_count='$answer_count' WHERE question_id='$question_id'");
		}
		if($query) {
			$response["success"] = 1;
			$response["message"] = "Answer count has been successfully updated!";
		} else {
			$response["success"] = 0;
			$response["message"] = "Failed to update the answer count!";
		}
		return json_encode($response);
	}
}