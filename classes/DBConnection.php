<?php
class DBConnection {
 
    function __construct() {
        $this->connect();
    }
 
    function __destruct() {
        $this->close();
    }
	
    function connect() {
        $host = "localhost";
		$user = "root";
		$pass = "";
		$dtbs = "arlin";
		
        $con = mysql_connect($host, $user, $pass) or die(mysql_error());
 
        $db = mysql_select_db($dtbs) or die(mysql_error()) or die(mysql_error());

        return $con;
    }
	
    function close() {
        mysql_close();
    }
}
?>