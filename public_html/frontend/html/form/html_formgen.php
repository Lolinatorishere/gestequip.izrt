<?php
function make_form($form_config , $form_action , $form_method , $form_inputs){
    
    $form_class = $form_config["form_class"];
    $form_title_class = $form_config["form_title_class"];
    $form_title = $form_config["form_title"];
    $button_div_class = $form_config["button_div_class"];
    $form_div_class = $form_config["form_div_class"];
    $input_list_class = $form_config["input_list_class"];
    $ret = ("<form 
        class=\"$form_class\" 
        action=\"$form_action\" 
        method=\"$form_method\"
        >");
        if(isset($form_title)){
            $ret .= ("
            <h1 
            class=\"$form_title_class\">
                $form_title
            </h1>");
        }
        if(isset($form_inputs["form_input"])){
            $ret .=("
            <div class=\"$form_div_class\">
            ");
            foreach($form_inputs["form_input"] as $input){
                $ret .= ("
                <label 
                    for=\"$input[label_for]\"
                    class=\"$input[label_class]\"
                >
                    $input[label_text]
                </label>
                    <input ");
                    if(isset($input["input_type"]))
                        $ret .= ("type=\"$input[input_type]\"");
                    if(isset($input["input_id"]))
                        $ret .= ("id=\"$input[input_id]\"");  
                    if(isset($input["input_class"]))
                        $ret .= ("class=\"$input[input_class]\"");
                    if(isset($input["input_placeholder"]))
                        $ret .= ("placeholder=\"$input[input_placeholder]\"");
                    if(isset($input["input_required"]))
                        $ret .= ("required=\"$input[input_required]\"");
                    if(isset($input["input_maxlength"]))
                        $ret .= ("maxlength=\"$input[input_maxlength]\"");
                    if(isset($input["input_autofocus"]))
                        $ret .= ("autofocus=\"$input[input_autofocus]\"");
                    $ret .= (">");
            }
            $ret .= ("
            </div>
            ");
        }
        if(isset($form_inputs["button"])){
            $ret .= ("
                <div class=\"$button_div_class\"> 
            ");
            foreach($form_inputs["button"] as $buttons){
                $ret .= ("
                    <button
                        href=\"$buttons[button_href]\"
                        class=\"$buttons[button_class]\"
                        type=\"$buttons[button_type]\"
                        name=\"$buttons[button_name]\"
                        value=\"$buttons[button_value]\"
                    >
                        $buttons[button_text]
                    </button>
                ");
            }
            $ret .= ("
                </div> 
            ");
        }
    $ret .= ("
        </form>
    ");
    return $ret;
}

function make_form_inputs($inputs){
    $exit = array();
    if(isset($inputs["input_type"])){
        for($i = 0; $i < count($inputs["input_type"]); $i++){
            $exit[$i] = array(
                "label_for" => $inputs["label_for"][$i],
                "label_class" => $inputs["label_class"][$i],
                "label_text" => $inputs["label_text"][$i],
                "label_for" => $inputs["label_for"][$i],
                "input_type" => $inputs["input_type"][$i],
                "input_id" => $inputs["input_id"][$i],
                "input_class" => $inputs["input_class"][$i],
                "input_placeholder" => $inputs["input_placeholder"][$i],
                "input_required" => $inputs["input_required"][$i],
                "input_maxlength" => $inputs["input_maxlength"][$i],
                "input_autofocus" => $inputs["input_autofocus"][$i]
            );
        }
    }
    return $exit;
}

function make_form_buttons($buttons){
    $exit = array();
    if(isset($buttons["button_href"])){
        for($i = 0; $i < count($buttons["button_href"]); $i++){
            $exit[$i] = array(
                "button_href" => $buttons["button_href"][$i],
                "button_class" => $buttons["button_class"][$i],
                "button_type" => $buttons["button_type"][$i],
                "button_name" => $buttons["button_name"][$i],
                "button_value" => $buttons["button_value"][$i],
                "button_text" => $buttons["button_text"][$i]
            );
        }
    }
    return $exit;
}

?>