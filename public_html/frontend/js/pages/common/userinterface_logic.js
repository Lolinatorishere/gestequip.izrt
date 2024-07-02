
function internalTabSetter(tab_id , functionality){
    tab_row = document.querySelector(tab_id).children;
    for(let i = 0 ; i < tab_row.length ; i++){
        if(tab_row[i].children.length === 0)
            continue;
        if(tab_row[i].children === undefined)
            continue;
        tab_row[i].children[0].style.paddingleft = 1 + 'rem';
        tab_row[i].children[0].style.paddingright = 1 + 'rem';
        functionality(tab_row[i].children[0]);
    }
}


function calculateUiWidthPercentages(widths , parent_width , padding){
    let total_width = 0
       ,ret = {
            widths: undefined
           ,percentage: undefined
           ,wrapText: "nowrap"
       };
    for(let i = 0 ; i < widths.length ; i++){
        total_width += widths[i];
    }
    parsed_widths = [];
    parsed_percentage = [];
    for(let i = 0 ; i < widths.length ; i++){
        parsed_percentage[i] = parseFloat((widths[i]/total_width));
        parsed_widths[i] = parent_width * parsed_percentage[i];
    }
    total_parsed_widths = 0;
    for(let i = 0 ; i < widths.length ; i++){
        total_parsed_widths += parsed_widths[i] + padding*2;
    }
    ret.widths = parsed_widths;
    ret.percentage = parsed_percentage;
    return ret;
}

function getWidthsForUI(set_items , title , title_properties , individual_item_height , total_children , equipment_type){
    let widths = [];
    for(let i = 0 ; i < set_items.length-title ; i++){
        let width = []
           ,itempos = i+title;
        set_items[itempos].style.height = individual_item_height;
        for(let j = 0 ; j < total_children ; j++){
            item = set_items[itempos].children[j];
            if(item === undefined){
                break;
            }
            if((item.attributes.class.nodeValue) === "equipment_type"){
                for(let k = 0 ; k < equipment_type.length ; k++){
                    if(parseInt(item.innerText) === equipment_type[k].id){
                        item.innerText = equipment_type[k].equipment_type;
                        break;
                    }
                }
            }
            if((item.attributes.class.nodeValue) === "equipment_status"){
                 for(let k = 0 ; k < 2 ; k++){
                    if(parseInt(item.innerText) === 1){
                        item.innerText = "Active";
                        break;
                    }else{
                        item.innerText = "Inactive";
                    }
                }
            }
            width.push(item.clientWidth);
        }
        widths[i] = width;
    }
    if(title === 1){
        let titles = title_properties.children[0].children
           ,width = [];
        for(let i = 0 ; i < titles.length ; i++){
            width.push(titles[i].clientWidth);
        }
        widths[widths.length] = width;
    }
    return widths;
}

function findMinMaxAttributeFromList(list_input , min_or_max , attribute){
    if(list_input === undefined)
        return "error";
    if(min_or_max === undefined)
        return "error";
    let smallest = undefined
       ,largest = undefined
       ,attribute_int = 0;
    list_input.forEach(input => {
        if(attribute === undefined){
            attribute_int = parseInt(input);
        }else{
            attribute_int = parseInt(input[attribute]);
        }
        if(isNaN(attribute_int) || attribute_int === null)
        return;
        if(smallest === undefined){
            smallest = attribute_int;
        }
        if(largest === undefined){
            largest = attribute_int
        }
        if(largest < attribute_int){
            largest = attribute_int;
        }
        if(smallest > attribute_int){
            smallest = attribute_int;
        }
    });
    switch (min_or_max) {
        case "min":
            return smallest;
        case "max":
            return largest;
        case "both":
            return {small: smallest, large: largest};
        default:
            return "error";
    }
}

function itemsUIparseWidthRows(widths){
    let width_by_row = [];
    for(let i = 0 ; i < widths[0].length ; i++){
        let width_row = [];
        for(let j = 0 ; j < widths.length ; j++){
            width_row.push(widths[j][i]);
        }
    width_by_row[i] = findMinMaxAttributeFromList(width_row , "max");
    }
    return width_by_row;
}


async function setFetchedItemsUI(item_set_id , limit , equipment_type , padding){
    let item_location_height = document.getElementById(item_set_id).clientHeight
    let item_location_width = document.getElementById(item_set_id).clientWidth
    let individual_item_height = item_location_height/limit
    let set_items = document.getElementById(item_set_id).children
    let title_properties = set_items[0]
    let title = 0;
    if(title_properties.id === "title-bar"){
        title_properties.style.paddingTop = 3
        title_properties.style.paddingBottom = 3
        individual_item_height = (item_location_height-title_properties.clientHeight)/limit;
        title_properties.children[0].style.display = "flex";
        title_properties.children[0].style.flexDirection = "row";
        let title_content = title_properties.children[0].children;
        title = 1;
    }
    let total_children = set_items[title].children.length
    let widths = getWidthsForUI(set_items , title , title_properties , individual_item_height , total_children , equipment_type);
    individual_widths = itemsUIparseWidthRows(widths);
    parsed_widths = calculateUiWidthPercentages(individual_widths , item_location_width , padding);
    aligned_widths = parsed_widths.widths; 
    aligned_percentage = parsed_widths.percentage; 
    wrap_text = parsed_widths.wrapText;
    for(let i = 0 ; i < set_items.length-title ; i++){
        for(let j = 0 ; j < total_children ; j++){
            item = set_items[i+title].children[j];
            if(item === undefined){
                break;
            }
            item.style.height = individual_item_height + "px";
            item.style.width = aligned_percentage[j]*100 + "%";
            item.style.textAlign = "";
            item.style.textWrap = wrap_text;
            item.style.marginLeft = "8px";
            item.style.paddingRight = "-8px";
            item.style.fontSize = "0.8rem"
            item.style.flexDirection = "column";
            item.style.justifyContent = "center";
            if(parseInt(j)+parseInt(1) < parseInt(total_children)){
                item.style.borderStyle = "none dotted none none"
                item.style.borderColor = "rgb(170, 170, 170)"
                item.style.borderWidth = "2px"
            }
        }
    }
    if(title === 1){
        document.getElementById("title-bar").style.height = individual_item_height + "px";
        let titles = title_properties.children[0].children
        for(let i = 0 ; i < titles.length ; i++){
            titles[i].style.height = individual_item_height + "px";
            titles[i].style.width = aligned_percentage[i]*100 + "%";
            titles[i].style.flexDirection = "column";
            titles[i].style.justifyContent = "center";
            titles[i].style.fontSize = "0.9rem";
            titles[i].style.marginLeft = "8px";
            titles[i].style.paddingRight = "-8px";
        }
    }
}

async function encapsulateAndFilter(encapsulation_location , filter , conditionals){
    content = encapsulation_location.children;
    let content_width = [];
    let parent_height = encapsulation_location.clientHeight;
    let content_height = [];
    let total_height = 0;
    for(let i = 0 ; i < content.length ; i++){
        total_height += content[i].clientHeight;
        let width = content[i].clientWidth;
        let height = content[i].clientHeight;
        content[i].style.height = height + "px";
        content[i].style.marginTop = 16 + "px";
        content[i].style.paggingBottom = -32 + "px";
        content[i].style.marginBottom = 16 + "px";
        content[i].style.marginLeft = 16 + "px";
        content[i].style.marginRight = 16 + "px";
        content[i].style.paggingRight = -32 + "px";
        content[i].style.borderWidth = "2px";
        content[i].style.borderStyle = "solid";
        content[i].style.borderColor = "rgb(170, 170, 170)";
        content[i].style.display = "flex";
        content[i].style.flexDirection = "column";
        content[i].style.justifyContent = "center";
        content[i].style.backgroundColor = "rgb(239, 239, 239)";
        let children_width = [[],[]];
        let children_height = 0;
        let displaying_children = 0;
        let title_width = 0;
        let value_width = 0;
        for(let j = 0 ; j < content[i].children.length ; j++){
            let continue_guard = 0;
            item_content = content[i].children[j]
            for(let k = 0 ; k < filter[i].length ; k++){
                if(item_content.children[1].attributes.id.nodeValue === filter[i][k]){
                    item_content.style.display = "none";
                    continue_guard = 1;
                    break;
                }
            }
            if(continue_guard === 1){
                continue;
            }
            //checking conditionals
            if(conditionals[i][0].length !== 0){
                let string_check = item_content.children[1].attributes.id.nodeValue;
                let string_value = item_content.children[1].innerText;
                conditions_length = conditionals[i][0].length
                condition_check = conditionals[i][0];
                conditions = conditionals[i][1];
                for(let k = 0 ; k < conditions_length ; k++){
                    if(string_check !== condition_check[k])
                        continue;
                    if(typeof conditions[k][0][0] === "object" || typeof conditions[k][0][0] === "array"){
                        let object_length = conditions[k][0][0].length;
                        let equal = conditions[k][1][0]
                        let replace = conditions[k][1][1]
                        for(let l = 0 ; l < object_length ; l++){
                            if(string_value == conditions[k][0][0][l][equal]){
                                item_content.children[1].innerText = conditions[k][0][0][l][replace];
                            }
                        }
                    }else{
                        if(string_value == conditions[k][0]){
                            item_content.children[1].innerText = conditions[k][1][0]
                        }else{
                            item_content.children[1].innerText = conditions[k][1][1]
                        }
                    }
                }
            }
            children_height += parseInt(item_content.clientHeight);
            children_width[0][displaying_children] = item_content.children[0].clientWidth;
            children_width[1][displaying_children] = item_content.children[1].clientWidth;
            displaying_children++;
        }
        if(displaying_children !== 0){
            child_height_multiplier = ((parseFloat(children_height/height)-1)*-1);
            child_height_px = (parseFloat(children_height/displaying_children*(1+child_height_multiplier)));
            title_width = findMinMaxAttributeFromList(children_width[0] , "max");
            value_width = findMinMaxAttributeFromList(children_width[1] , "max");
            combo_width = (title_width+value_width);
            title_width = parseFloat(title_width/combo_width)*100;
            value_width = parseFloat(value_width/combo_width)*100;
        }
        for(let j = 0 ; j < content[i].children.length ; j++){
            item_content = content[i].children[j]
            item_content.style.height = child_height_px + "px";
            item_content.children[0].style.width = title_width+"%";
            item_content.children[0].style.fontSize = "0.9rem";
            item_content.children[0].style.display = "flex";
            item_content.children[0].style.flexDirection = "column";
            item_content.children[0].style.justifyContent = "center";
            item_content.children[0].style.marginLeft = "6px";
            item_content.children[0].style.paddingRight = "-6px";
            item_content.children[0].style.borderWidth = "2px";
            item_content.children[0].style.borderStyle = "none dotted none none";
            item_content.children[0].style.borderColor = "rgb(170, 170, 170)";
            item_content.children[1].style.width = value_width+"%";
            item_content.children[1].style.fontSize = "0.9rem";
            item_content.children[1].style.marginLeft = "6px";
            item_content.children[1].style.paddingRight = "-6px";
            item_content.children[1].style.display = "flex";
            item_content.children[1].style.flexDirection = "column";
            item_content.children[1].style.justifyContent = "center";
        }
    }
    encapsulation_location.style.height = total_height + "px";
}

function parseTableType(type){
    ret = "";
    switch(type){
        case "tinyint(1)":
            ret = "checkbox";
            break;
        case "date":
            ret = "date";
            break;
        default:
            ret = "text";
            break;
    }
    return ret;
}

async function setUpdateableInfo(functions){
    if(functions.update_info !== undefined){
        edit_tables = await functions.description_func(functions.update_info);
    }else{
        return;
    }
    for(let [key , value] of Object.entries(edit_tables.information.default)){
        if(document.getElementById(value.Field) === undefined)
            continue;
        inputdiv = document.getElementById(value.Field);
        input = {
            className: value.Type + "-input",
            id: value.Field,
            type: parseTableType(value.Type),
            placeholder: inputdiv.innerText
        }
        inputdiv.innerText = ""
        inputdiv.appendChild(createInputHtml(input));
    }
    if(edit_tables.information.specific !== undefined){
        for(let [key , value] of Object.entries(edit_tables.information.specific)){
            if(document.getElementById(value.Field) === undefined)
                continue;
            inputdiv = document.getElementById(value.Field);
            input = {
                className: value.Type + "-input",
                id: value.Field,
                type: parseTableType(value.Type),
                placeholder: inputdiv.innerText
            }
            inputdiv.innerText = ""
            inputdiv.appendChild(createInputHtml(input));
        }
    }
}

async function prepareInputForEquipment(user_input , user_id , group_id , equipment_id , equipment_type){
    information = {
        default:{},
        specific:{},
        user_id:user_id,
        group_id:group_id,
        equipment_id:equipment_id,
    }
    desc_response = await getEquipmentTableDescription(equipment_type);
    table_description = desc_response.information;
    for(let [key , value] of Object.entries(table_description.default)){
        for(let [input_id , input_value] of Object.entries(user_input)){
            if(value.Field === input_id){
                information.default[input_id] = input_value;
                break;
            }
        }
    }
    for(let [key , value] of Object.entries(table_description.specific)){
        for(let [input_id , input_value] of Object.entries(user_input)){
            if(value.Field === input_id){
                information.specific[input_id] = input_value;
                break;
            }
        }
    }
    let equipment_types = await getEquipmentTypes();
    for(let i = 0 ; i < equipment_types.information.items.length ; i++){
        if(equipment_type === equipment_types.information.items[i].id){
            equipment_table = equipment_types.information.items[i].equipment_type;
            break;
        }
    }
    information["equipment_type"] = equipment_table;
    return information;
}

async function getInputInformation(functions){
    if(document.getElementById("update-button") === undefined){
        return;
    }
    let information = {};
    let inputs = document.getElementsByTagName('input');
    for(let i = 0 ; i < inputs.length ; i++){
        if(inputs[i].attributes.type.nodeValue === "checkbox"){
            let checkBoxValue = undefined;
            if(inputs[i].value === "on"){
                checkBoxValue = 1;
            }else{
                checkBoxValue = 0;
            }
            if(checkBoxValue === undefined){
                continue;
            }
            information[inputs[i].attributes.id.nodeValue] = checkBoxValue;
        }else if(inputs[i].value !== ""){
            information[inputs[i].attributes.id.nodeValue] = inputs[i].value;
        }
    }
    return information;
}

async function setServerResponse(information , append_to){
    if(document.getElementById(append_to) === undefined){
        return;
    }else{
        document.getElementById(append_to).innerHTML = "";
    }
    let response = document.getElementById(append_to);
    response.innerHTML = "";
    HTMLinner = "";
    for(let [key , value] of Object.entries(information)){
        if(typeof value === "object"){
            for(let [m_key , message] of Object.entries(value)){
                if(typeof message === "object")
                    break;
                html = `
                    <div class=${m_key} style="text-wrap:wrap">
                        ${message}
                    </div>
                   `
                HTMLinner += html;
            }
        }else{
            html = `
              <div class=${key} style="text-wrap:wrap">
                  ${value}
              </div>
             `
            HTMLinner += html;
        }
    }
    response.innerHTML = HTMLinner;
}

function createButtonsFunctionality(buttons){
    if(document.getElementById("update-button") !== undefined){
        delete(document.getElementById("update-button"));
    }
    let functional_buttons = document.createElement('div');
    for(let i = 0 ; i < buttons.html.length ; i++){
        button = createButtonHtml(buttons.html[i]);
        button.addEventListener("click" , async function(){
            if(typeof buttons.functions[i] !== "function")
                return;
            if(typeof buttons.internal === "object"){
                buttons.functions[i](buttons.internal[i]);
            }else{
                buttons.functions[i]();
            }
            if(typeof buttons.callback[i].function !== "function")
                return;
            if(typeof buttons.callback[i].internal === "object"){
                buttons.callback[i].function(buttons.callback[i].internal);
            }else{
                buttons.callback[i].function();
            }
        });
        functional_buttons.appendChild(button);
    }
    return functional_buttons;
}

function itemReadDetails(content_location , append_to , information , information_types , encapsulate , callback){
    let control_div = document.querySelectorAll(content_location);
    let append_details = document.getElementById(append_to[1]);
    let total_controls = control_div[0].children
    let htmlInformation = [];
    let html = [];
    let title = 0;
    for(let i = 0 ; i < total_controls.length ; i++){
        controls = document.getElementById(total_controls[i].attributes.id.nodeValue);
        if(total_controls[i].attributes.id.nodeValue === "title-bar-"+append_to[0]){
            title = 1;
            continue;
        }
        html[i-title] = "";
        for(let j = 0 ; j < information_types.length ; j++){
            htmlInformation[j] = createDetailsHtml(information.items[i-title][information_types[j]] , information_types[j]);
            html[i-title] += htmlInformation[j].innerHTML;
        }
        controls.addEventListener('click' , async function(){
            append_details.innerHTML = html[i-title];
            if(typeof encapsulate.function === "function"){
                await encapsulate.function(append_details , encapsulate.filter , encapsulate.conditionals);
            }
            if(callback !== undefined){
                if(typeof callback.function === "function"){
                    callback.details = information.items[i-title]
                    await callback.function(callback);
                }
            }
        });
        controls.style.cursor = "pointer";
    }
}

function pageControlsFunctionality(control_location , controlerFunction , loadingFunction){
    control_div = document.querySelectorAll(control_location);
    total_controls = control_div[0].children
    for(let i = 0 ; i < total_controls.length ; i++){
        if(total_controls[i].attributes.page === undefined)
            continue;
        controls = document.getElementById(total_controls[i].attributes.id.nodeValue);
        controls.addEventListener('click' , async function(){
            if(typeof loadingFunction === "function"){
                controlerFunction(loadingFunction , total_controls[i].attributes.page.nodeValue);
            }else{
                controlerFunction(total_controls[i].attributes.page.nodeValue);
            }
        });
        controls.style.cursor = "pointer";
        controls.style.userSelect = "none";
    }
}

