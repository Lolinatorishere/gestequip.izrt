<?php

function LoadDBConfigs($filepath){
    $path = $filepath;
    if(file_exists($path)){
        //open the file location
        $fileHandle = fopen($path, 'r');
        
        return fread($fileHandle , filesize($path));
        
        fclose($fileHandle);

    }else{
        return 1;
    }
}

function ParseDBConfigs($config_path){
    $data_to_be_parsed = LoadDBConfigs($config_path);
    if($data_to_be_parsed == 1){
        return "Config file inexistent"; 
    }
    return $data_to_be_parsed;
}
?>
