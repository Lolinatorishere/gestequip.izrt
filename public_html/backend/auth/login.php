<?php 

if(!defined('query_generator_dir'))
    define('query_generator_dir' , '/var/www/html/gestequip.izrt/public_html/backend/crud/common/query_generators.php');

if(!defined('pdo_config_dir'))
    define('pdo_config_dir' , '/var/www/html/gestequip.izrt/public_html/backend/config/pdo_config.php');
 
session_start();

if(isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] === true)
    header("Location: /pages/dashboard.php");

require_once pdo_config_dir;
require_once "/var/www/html/gestequip.izrt/public_html/backend/crud/read/common_query.php";
require_once "/var/www/html/gestequip.izrt/public_html/backend/auth/user_auth.php";
$error_message = "";
$email = "";
$password = "";
$errors[2] = array("" , "");

if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(empty(trim($_POST["email"]))){
        $errors[0] = "Please enter an Email";
    }else{
        $email = trim($_POST["email"]);
    }

    if(empty(trim($_POST["password"]))){
        $errors[1]= "Please enter a Password";
    }else{
        $password = trim($_POST["password"]);
    }
    $error_message = login_check($pdo , $errors , $email , $password);
    unset($statement);
    unset($pdo);
}

function login_check($pdo , $errors , $email , $password ){

    $login_err = "Invalid Email or Password";
    $sql_error = "Well thats weird, Something didnt go well.<br> Try again Later.";
    $param_email = $_POST["email"];
    $auth = 0;
    $sql ="SELECT id, email, pass, account_status, username, users_name
      FROM users
      WHERE email = ?";

    if(!empty($errors[0]))//email empty
        return $errors[0];
    if(!empty($errors[1]))//password empty
        return $errors[1];

    $statement = $pdo->prepare($sql);
    if(!$statement)
        return $sql_error;
    
    $statement->bindParam(1 , $param_email, PDO::PARAM_STR);
    $param_email = trim($_POST["email"]);
    $statement->execute();

    if(!$statement)
        return $sql_error;
    if($statement->rowCount() != 1)
        return $login_err;

    $row = $statement->fetch();

    if(!$row)
        return $sql_error;
    if($row["account_status"] == 0)
        return "Your User has been Deactivated\n Call Support if you Think this was an Error";

    $hashed_password = $row["pass"];
    if(!(password_verify($password , $hashed_password)))
        return $login_err;
    $statement->execute();

    $id = $row["id"];
    $email = $row["email"];
    $username = $row["username"];
    $users_name = $row["users_name"];

    // loads the auth levels of every group
    $request = array("fetch" => " * "
                    ,"table" => "users_inside_groups"
                    ,"specific" => "user_id = ". $id 
                    ,"counted" => 1
                );
    $group_auth = get_user_group_auth($request , $pdo);
    if(count($group_auth["auth"]) >= 1){
        $auth = 2;
    }
    // checks if is an admin 
    $sql = "SELECT * FROM sudo_group WHERE id_user = ?";
    $statement = $pdo->prepare($sql);
    if(!$statement)
        return $sql_error;
    $statement->bindParam(1 , $id, PDO::PARAM_STR);
    $statement->execute();

    if($statement->rowCount() == 1){
        $row = $statement->fetch();
        if($row["admin_status"] == 1){
            $auth = 1;
        }
    }
    $_SESSION["logged_in"] = true;
    $_SESSION["id"] = $id;
    $_SESSION["email"] = $email;
    $_SESSION["username"] = $username;
    $_SESSION["users_name"] = $users_name;
    switch($auth){
        case 1:
            $_SESSION["user_type"] = "Admin";
            break;
        case 2:
            $_SESSION["user_type"] = "Manager";
            break;
        default:
            $_SESSION["user_type"] = "User";
            break;
    }
    $_SESSION["group_auth"] = $group_auth;
    header("Location: /pages/dashboard.php");
}
?>
