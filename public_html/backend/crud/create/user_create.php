<?php

if($_SESSION["user_type"] !== "Admin")
    die;

include_once query_generator_dir;

function create_user($data_request , $pdo){
try{
    $ret = array("server_message" => ""
                ,"message" => "default"
                );
    $insert_error = "User not created";
    $error_message = array();
    $loggable = array("origin" => "User_Create"
                     ,"type" => ""
                     ,"status" => ""
                     ,"exception" => array()
                     ,"message" => array()
                     ,"action_by_user_id" => $_SESSION["id"]
                     ,"group_id" => $data_request["group_id"]
                     ,"user_id" => "0"
                     );
    $loggable["message"]["userInput"] = $data_request["user"];
    $loggable["message"]["userInput"]["email"] = $data_request["email"];
    if($_SESSION["user_type"] !== "Admin")
        throw new Exception("Authentication");
    $validation_guard = validate_external_create_inputs($data_request ,  $pdo , $error_message);
    if($validation_guard !== 1)
        throw new Exception("Validation");

    if($data_request["admin"] === "1" && $data_request["virtual"] === "1"){
        $error_message = " You cannot create a virtual admin user";
        throw new Exception("Validation");
    }
    try{
        $sql = create_insertion_generator($data_request , " users " , "user" , 1);
        $statement = $pdo->prepare($sql);
        foreach($data_request["user"] as $key => &$value){
            if($key === "pass"){
                if($data_request["virtual"] !== "1"){
                    $statement->bindParam(":" . $key , password_hash($data_request["pass"] , PASSWORD_DEFAULT));
                }
                $password = "virtual";
                $statement->bindParam(":" . $key , $password , PDO::PARAM_STR); 
                continue;
            }
            if($key === "email"){
                $statement->bindParam(":" . $key , $data_request["email"] , PDO::PARAM_STR); 
                continue;
            }
            $statement->bindParam(":" . $key , $value);
        }
        $statement->execute();
        $user_id = $pdo->lastInsertId();
    }catch(PDOException $e){
        $loggable["exception"]["PDOMessage"] = $e->getMessage();
        $loggable["message"]["sql"] =  $sql;
        $error = explode(':' , $e->getMessage());
        if($error[0] === "SQLSTATE[23000]"){
            $error_messages;
            preg_match_all("/'([^']+)'/" , $error[2] , $error_messages);
            $error_messages[0][1] = explode('.' , $error_messages[0][1])[1];
            $error_messages[0][1] = explode('\'' , $error_messages[0][1])[0];
            $exception = $error_messages[0][1] . " is not unique inserted " . $error_messages[0][0];
            throw new Exception($exception);
        }
        throw new Exception("Server_Error_CU0001");
    }
    try{
        if($data_request["admin"] === "1"){
            if($_SESSION["user_type"] === "Admin"){
                $request["data"] = array("id_user" => $user_id
                                        ,"admin_status" => "1"
                                        );
                $sql = create_insertion_generator($request , " sudo_group " , "data" , 0);
                $statement = $pdo->prepare($sql);
                $statement->execute();
                $loggable["message"]["admin"] = "User is an Admin";
            }
        }
        throw new Exception("User Created");
    }catch(PDOException $e){
        $loggable["exception"]["PDOMessage"] = $e->getMessage();
        $loggable["message"]["sql"] =  $sql;
        throw new Exception("Server_Error_CU0002");
    }
        throw new Exception("User_Created");
}catch(Exception $e){
    switch($e->getMessage()){
        case 'User Created':
            $loggable["type"] = "Created_User";
            $loggable["status"] = "OK";
            $loggable["user_id"] = $user_id;
            $ret["server_message"] = "Equipment Created";
            $user_return = " id , username , users_name , email , phone_number , regional_indicator , date_created , account_status";
            $request = array("fetch" => $user_return
                            ,"table" => " users "
                            ,"counted" => 1
                            ,"specific" => " id =" . $user_id
                            );
            $ret["message"] = get_query($request , $pdo);
            break;
        case 'Authentication':
            $loggable["type"] = "Auth_Error";
            $loggable["status"] = "Error";
            $loggable["exception"]["authentication"] = "Unauthorised Request";
            $ret["server_message"] = "Unauthorised Access";
            $ret["message"] = array("User Credentials Invalid for Action");
            break;
        case 'Validation':
            $loggable["type"] = "Input_Error";
            $loggable["status"] = "Error";
            $loggable["message"]["user_input_error"] = $error_message;
            $ret["server_message"] = "Invalid User Inputs";
            $ret["message"] = $error_message;
            break;
        default:
            $loggable["type"] = "Server_Error";
            $loggable["log_status"] = "Error";
            if(isset($equipment_id)){
                $loggable["equipment_id"] = $equipment_id;
                $loggable["exception"]["incomplete_creation"] = "The following User had an error inserting information " . $equipment_id;
            }
            $loggable["exception"]["thrown_exception"] = $e->getMessage();
            $ret["server_message"] = "Opperation could not Be Completed";
            $ret["message"] = $e->getMessage();
            break;
    }
    create_log($loggable , "user_logs" , $pdo);
    return $ret;
}
}

?>
