<?php
//database credentials for phpwebsite user
$server_ip = "localhost";
$server_username = "phpwebsite";
$pw = "0scCgFhfzEySLmdihNpPwERltLY0lfYws92hGMARYCrnAyMv";
$database_name = "webapp";

if(!defined('DB_SERVER'))
define('DB_SERVER' , $server_ip);

if(!defined('DB_USERNAME'))
define('DB_USERNAME' , $server_username);

if(!defined('DB_PW'))
define('DB_PW' , $pw);

if(!defined('DB_NAME'))
define('DB_NAME' , $database_name);

try{
    $pdo = new pdo("mysql:host=" . DB_SERVER .";dbname=" . DB_NAME , DB_USERNAME , DB_PW);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch(PDOException $e){
    die("ERROR: could not connect. " . $e->getMessage());
}
?>