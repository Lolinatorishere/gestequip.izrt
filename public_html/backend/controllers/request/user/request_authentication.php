<?php

function tab_auth_handle($auth_level){
    if($_SESSION["user_type"] === 'Admin')
        return 1;
    return 0;
}

?>
