<?php
function query_merge_array($arrays){
    $combined = array();
    foreach($arrays as $part){
        $i = 1;
        foreach($part as $key => $info){
            if($i%2 !== 0){
                $combined[$key] = $info;
            }
            $i++;
        }
    }
    return $combined;
}

function filter_key($filter_array , $check){
    foreach ($filter_array as $value) {
        if($check == $value){
            return 1;
        }
    }
    return 0;
}

function merge_arrays(){
    // if the first array is of "filter" nomeclature it will load
    // all the string values in that array and any key with the filter
    // name will be ignored
    $combined = array();
    $arrays = func_get_args();
    if(isset($arrays[0]["filter"])){
        $filter = array();
        foreach($arrays[0]["filter"] as $to_filter){
            array_push($filter , $to_filter);
        }
    }
    if(!isset($filter)){
        foreach($arrays as $array){
            foreach ($array as $key => $value) {
                $combined[$key] = $value;
            }
        }
    }else{
        for($i = 1 ; $i < count($arrays) ; $i++){
            foreach($arrays[$i] as $key => $value){
                if(filter_key($filter , $key) !== 1)
                    $combined[$key] = $value;
            }
        }
    }
    return $combined;
}
?>
