<?php

class ConnectionManager {
   
    public function getConnection() {
	$password = "";  
        if(strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN'){
	    $password = 'ykJ7OaMCEexF';
	}
        $host = "localhost";
        $username = "root";
        $dbname = "G7T3";
        $port = 3306;    

        $url  = "mysql:host={$host};dbname={$dbname};port={$port}";
        $options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',);
        $conn = new PDO($url, $username, $password,$options);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
        return $conn;  
        
    }
    
}
