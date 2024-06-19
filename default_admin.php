<?php 
include_once __DIR__."/../config/pdo_congfig.php";

function make_admin($pdo){
    $values = array(
 "admin",
 password_hash("password", PASSWORD_DEFAULT),
 "admin name lol",
 "sudo@admin.com",
 133769420,
 "+351",
 1,
 0);
    $sql = " INSERT INTO users (
        username,
        pass,
        users_name,
        email,
        phone_number,
        regional_indicator,
        account_status,
        active_directory_user
      )
    VALUES (
        ?,?,?,?,?,?,?,?
      )";
    $statement = $pdo->prepare($sql);
    if(!$statement) 
        throw new Exception('SQL query preparation failed');
    for($i = 1 ; $i < 9 ; $i++){
        $statement->bindParam($i, $values[$i-1]);
    }

    $statement->execute();
    unset($statement);

}

make_admin($pdo);
unset($pdo);
?>
