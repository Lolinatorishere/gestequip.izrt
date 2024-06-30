<?php
function read_your_user($pdo){
    if(empty($_SESSION["id"]))
        return "Unauthorised Request";
    $ret = array();
    $request = array("fetch" => " id, users_name, username, email, phone_number, regional_indicator "
        ,"table" => " users "
        ,"counted" => 1
        ,"specific" => "id=" . $_SESSION["id"]
    );
    $your_user = get_query($request , $pdo);
    return $your_user;
}
?>
