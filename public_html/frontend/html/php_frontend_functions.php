<?php

function convert_to_array($input){
    if(is_array($input))
        return $input;
    $input = array($input);
    return $input;
}

?>