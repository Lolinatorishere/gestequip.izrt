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

    $login_err = "Invalid Email or Password";
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

    
    //checks if is a manager
    $sql = "SELECT * FROM users_inside_groups WHERE user_id = ?";
    $statement = $pdo->prepare($sql);
    if(!$statement)
        return $sql_error;
    $statement->bindParam(1 , $id, PDO::PARAM_STR);
    $statement->execute();

    $rows = $statement->fetchAll();
    foreach($rows as $row){
        if($row["user_permission_level"] == 0){
            continue;
        }
        if($row["user_permission_level"] == 1){
            $auth = 2;
        }
    }

    //checks if is an admin
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

    session_start();
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
    header("Location: /pages/dashboard.php");
}
?>