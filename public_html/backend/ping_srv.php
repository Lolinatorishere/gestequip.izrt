<?php

function server_connection_test($HOST , $DATABASE , $USER , $PW){
    // MySQL server configuration
    $host = $HOST;
    $dbname = $DATABASE;
    $username = $USER;
    $password = $PW;
    
    // PDO connection string
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    
    // PDO options (optional but recommended for error handling)
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    // Try to establish the PDO connection
    try {
        $pdo = new PDO($dsn, $username, $password, $options);
        return "Connected to the database successfully!";
    } catch (PDOException $e) {
        // Handle connection errors
        die("Connection failed: " . $e->getMessage());
    }
    // Now you can perform database operations using $pdo
}
?>

