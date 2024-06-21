<?php

include_once query_generator_dir;

function get_pre_updated_user_information($data_request , $pdo){
    $previous_info = array();
    if(isset($data_request["user"])){
        $request = array("fetch" => " * "
                         ,"table" => " users "
                         ,"counted" => 1
                         ,"specific" => "id=\"" . $data_request["user_id"] . "\""
                     );
        $previous_info["users"] = get_queries($request , $pdo)["items"];
    }
    $request = array("fetch" => " * "
                  ,"table" => " sudo_group "
                  ,"specific" => " id_user=" . $data_request["user_id"]
                  );
    $sudo_user = get_queries($request , $pdo);
    if($sudo_user["total_items"] === 1){
        $previous_info["sudo_user"] = $sudo_user["items"];
    }
    return $previous_info;
}

function get_pre_updated_user_references($data_request , $pdo){
    $previous_info = array();
    $request = array("fetch" => " * "
                    ,"table" => " users_inside_groups "
                    ,"specific" => "user_id=" . $data_request["user_id"] . "group_id=" . $data_request["group_id"] 
                    );
    $user_references = get_queries($request , $pdo);
    if($user_references["total_items"] > 0){
        $previous_info["user_references"] = $sudo_user["items"];
    }
    return $previous_info;
}

// todo everything kek
// i swear to god im jumping off a cliff due to stupidity
function update_user($data_request , $pdo){
    printLog($data_request);
    $loggable = array("origin" => "User_Update"
                     ,"type" => ""
                     ,"status" => ""
                     ,"exception" => array()
                     ,"message" => array()
                     ,"action_by_user_id" => $_SESSION["id"]
                     ,"user_id" => $data_request["user_id"]
                     );
try{
    $loggable["message"]["userInput"] = $data_request;
    $internal_message = array();
    $error_message = array();
    $ret = array("server_message" => ""
                ,"message" => array()
                );
    if($_SESSION["user_type"] !== "Admin")
        throw new Exception("Authentication");
    $validation_guard = validate_external_update_inputs($data_request , $pdo , $error_message);
    if($validation_guard !== 1){
        $loggable["type"] = "Input_Error";
        $loggable["status"] = "Warning";
        if($validation_guard !== 0){
            $loggable["status"] = "Error";
            $loggable["exception"]["validation"] = "Invalid Validation Check, check validation code for possible bugs";
        }
        throw new Exception("Validation");
    }
    $loggable["message"]["previousInfo"] = get_pre_updated_user_information($data_request , $pdo); 
    if(isset($data_request["user"])){
    try{
        $columns = array();
        $values = array();
        foreach($data_request["user"] as $key => $value){
            array_push($columns , $key);
            if($key === "pass"){
                array_push($values , password_hash($data_request["pass"] , PASSWORD_DEFAULT));
                continue;
            }
            if($key === "email"){
                array_push($values , $data_request["email"]);
                continue;
            }
            array_push($values , $value);
        }
        $request = array("table" => " users "
                        ,"columns" => $columns
                        ,"values" => $values
                        ,"specific" => "id =" . $data_request["user_id"]
                        );
        $update = update_query($request , $pdo);
        if(isset($update["PDOException"]))
            throw new PDOException($update["PDOException"]);
    }catch(PDOException $e){
        $loggable["exception"]["user"] = $e->getMessage();
        $loggable["message"]["user"] =  $data_request["user"];
        $error = explode(':' , $e->getMessage());
        if(trim($error[1]) === "SQLSTATE[23000]"){
            $error_messages;
            preg_match_all("/'([^']+)'/" , $error[3] , $error_messages);
            $error_messages[0][1] = explode('.' , $error_messages[0][1])[1];
            $error_messages[0][1] = explode('\'' , $error_messages[0][1])[0];
            $exception = $error_messages[0][1] . " is not unique, inserted " . $error_messages[0][0];
            throw new Exception($exception);
        }
        throw new Exception("Server_Error_0001");
    }
    }
    if(isset($data_request["admin"])){
    try{
        if($_SESSION["user_type"] === "Admin"){
            $request = array("fetch" => " * "
                            ,"table" => " sudo_group "
                            ,"specific" => " id_user=" . $data_request["user_id"]
                            );
            $sudo = get_queries($request , $pdo);
            if($sudo["total_items"] === 1){
                $columns = array("admin_status");
                $values = array($data_request["admin"]);
                $request = array("table" => " sudo_group "
                                ,"columns" => $columns
                                ,"values" => $values
                                ,"specific" => "id_user =" . $data_request["user_id"]
                            );
                $update = update_query($request , $pdo);
                if(isset($update["PDOException"]))
                    throw new PDOException($update["PDOException"]);
            }else{
                if($data_request["admin"] === "1"){
                    $request["data"] = array("id_user" => $user_id
                                            ,"admin_status" => "1"
                                            );
                    $sql = create_insertion_generator($request , " sudo_group " , "data" , 0);
                    $statement = $pdo->prepare($sql);
                    $statement->execute();
                    $loggable["message"]["admin"] = "User is an Admin";
                }
            }
        }
    }catch(PDOException $e){
        $loggable["exception"]["sudo"] = $e->getMessage();
        throw new Exception("Server_Error_0001");
    }
    }
    throw new Exception("Updated");
}catch(Exception $e){
    $ret["server_message"] = "";
    switch($e->getMessage()){
        case "Updated":
            $loggable["type"] = "Updated_User";
            $loggable["status"] = "OK";
            $ret["server_message"] = "Updated User";
            break;
        case "Authentication":
            $loggable["type"] = "Auth_Error";
            $loggable["status"] = "Error";
            $loggable["exception"]["authentication"] = "Unauthorised Request";
            $ret["server_message"] = "User not Authorized";
            $ret["message"]["error"] = "User Credentials Invalid for Action";
            break;
        case "Validation":
            $loggable["type"] = "Input_Error";
            $loggable["status"] = "Error";
            $loggable["message"]["user_input_error"] = $error_message;
            $ret["server_message"] = "Invalid User Inputs";
            $ret["message"] = $error_message;
            break;
        default:
            $loggable["type"] = "Server_Error";
            $loggable["status"] = "Error";
            $ret["message"]["title"] =  "Issue Updating The User";
            $loggable["exception"]["thrown_exception"] = $e->getMessage();
            $ret["server_message"] = "Opperation could not Be Completed";
            $ret["message"] = $e->getMessage();
            break;
    }
    create_log($loggable , "equipment_logs" , $pdo);
    return $ret;
}
}

function update_user_inside_group($data_request , $pdo){
    
}

?>
