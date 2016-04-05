<?php
if(!isset($_SESSION)) { 
	session_start(); 
}
require_once "DBConnection.php";

class Questions {
	
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

}