<?php
    $dbname = "socialmedia";
    // connection to MySQL
    $pdo = new PDO('mysql:host=localhost', 'root', '');
    $stmt = $pdo->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.SCHEMATA 
    WHERE SCHEMA_NAME = '$dbname'");

    if((bool) $stmt->fetchColumn()){
        // database exists
        $pdo = new PDO("mysql:host=localhost;dbname=$dbname", "root", "");
        global $pdo;
    }
    else {
        // database doesn't exist
        if (isset($_SESSION['user_id'])) {
            session_destroy();
        }
        // create database (used IF NOT EXISTS to be sure)
        $statement_database = $pdo->prepare("CREATE DATABASE IF NOT EXISTS $dbname");
        if($statement_database->execute()){ // if statement works out
            // define PDO new so the tables are stored in the new database
            $pdo = new PDO("mysql:host=localhost;dbname=$dbname", "root", "");
            global $pdo;
            
            $tables = array( // define tables in array
                "user",
                "post",
                "likes",
                "comment"
            );
            // foreach table
            foreach($tables as $table){
                switch($table){
                    case "user":
                        // define columns with datatypes for table user
                        $columns = "id INT(11) PRIMARY KEY AUTO_INCREMENT, 
                        username VARCHAR(255), 
                        email VARCHAR(255), 
                        password VARCHAR(255), 
                        date_created DATETIME, 
                        date_modified DATETIME";
                    break;
                    case "post":
                        // define columns with datatypes for table post
                        $columns = "id INT(11) PRIMARY KEY AUTO_INCREMENT, 
                        user_id INT(11), 
                        caption VARCHAR(500), 
                        image_name VARCHAR(255), 
                        image MEDIUMBLOB, 
                        date_created DATETIME, 
                        date_modified DATETIME";
                    break;
                    case "likes":
                        // define columns with datatypes for table post
                        $columns = "id INT(11) PRIMARY KEY AUTO_INCREMENT, 
                        user_id INT(11), 
                        post_id INT(11), 
                        date_created DATETIME";
                    break;
                    case "comment":
                        // define columns with datatypes for table post
                        $columns = "id INT(11) PRIMARY KEY AUTO_INCREMENT, 
                        user_id INT(11), 
                        post_id INT(11), 
                        text VARCHAR(500), 
                        date_created DATETIME";
                    break;
                }
                
                // create table
                $statement_table = $pdo->prepare("CREATE TABLE IF NOT EXISTS $table ($columns)");
                if(!$statement_table->execute()){
                    die("Couldn't create table: $table");
                }
            }
        }
        else{
            die("Couldn't create database: $dbname");
        }
    }
?>