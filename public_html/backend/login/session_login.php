<?php 
session_start();

if(isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] === true)
    header("Location: /pages/dashboard.php");

require_once __DIR__."/../config/pdo_config.php";
$error_message = "";
$email = "";
$password = "";
$errors[2];
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
    $login_err = "Invalid Username or Password";
    $sql_error = "Well thats weird, Something didnt go well.<br> Try again Later.";
    $param_email = $_POST["email"];
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
    $param_email = trim($_POST["email"]); $statement->execute();

    if(!$statement)
        return $sql_error;
    if($statement->rowCount() != 1)
        return $login_err;

    $row = $statement->fetch();
    if(!$row)
        return $sql_error;

    if($row["account_status"] == 0)
        return "Your User has been Deactivated\n Call Support if you Think this was an Error";

    $id = $row["id"];
    $email = $row["email"];
    $hashed_password = $row["pass"];
    $username = $row["username"];
    $users_name = $row["users_name"];
    if(!(password_verify($password , $hashed_password)))
        return $login_err . $hashed_password;

    session_start();
    $_SESSION["logged_in"] = true;
    $_SESSION["id"] = $id;
    $_SESSION["email"] = $email;
    $_SESSION["username"] = $username;
    $_SESSION["users_name"] = $users_name;

    header("Location: /pages/dashboard.php");
}
?>