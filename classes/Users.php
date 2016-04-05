<?php
require_once "DBConnection.php";

class Users {
	
	public function checkExistedUser($user_id) {
		$db = new DBConnection();
		$query = mysql_query("SELECT * FROM users WHERE user_id='$user_id'");
		return mysql_num_rows($query);
	}
	
	public function insertUser($user_id, $username, $avatar) {
		$db = new DBConnection();
		$query = mysql_query("INSERT INTO users SET user_id='$user_id', created_date=CURRENT_TIMESTAMP, username='$username', avatar='$avatar'");
		if($query) {
			$response["success"] = 1;
			$response["message"] = "User has been successfully inserted!";
		} else {
			$response["success"] = 0;
			$response["message"] = "Failed to insert new user!";
		}
		return json_encode($response);
	}
	
	public function updateUser($user_id, $username, $avatar) {
		$db = new DBConnection();
		$query = mysql_query("UPDATE users SET username='$username', avatar='$avatar' WHERE user_id='$user_id'");
		if($query) {
			$response["success"] = 1;
			$response["message"] = "User has been successfully updated!";
		} else {
			$response["success"] = 0;
			$response["message"] = "Failed to update this user!";
		}
		return json_encode($response);
	}
	
	public function registerUser($user_id, $username, $avatar) {
		return ($this->checkExistedUser($user_id) > 0) ? $this->updateUser($user_id, $username, $avatar) : $this->insertUser($user_id, $username, $avatar);
	}
}