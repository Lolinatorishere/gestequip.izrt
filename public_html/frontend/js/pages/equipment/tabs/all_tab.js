
function pageAllControlsFunctionality(control_location , controlerFunction , loadingFunction){
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

function itemAllDetails(content_location , append_to , information , information_types , encapsulate , callback){
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
            htmlInformation[j] = createDetailsHtml(information.items[i-title] , information_types[j]);
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


async function generateFullInventoryHtml(data){
    let itemsDiv = document.createElement('div')
    let group_items = data.group_equipments.items;
    let equipment_type = ""
    let highlight = "";
    let ret = {};
    title = createTitleHTML(data);
    if(title !== undefined){
        itemsDiv.appendChild(title);
    }
    for(i = 0 ; i < group_items.length ; i++){
        for(j = 0 ; j < data.equipment_types.items.length ; j++){
            if(group_items[i].equipment_type === data.equipment_types.items[j].id){
                equipment_type = data.equipment_types.items[j].equipment_type;
                break;
            }
        }
        htmlData = {
            equipment_type: equipment_type,
            business_unit: group_items[i].business_unit,
            brand: group_items[i].brand,
            model: group_items[i].model,
            purchase_date: group_items[i].purchase_date,
        }
        if(i%2 === 0){
            highlight = "true";
        }else{
            highlight = "false";
        }
        item = createItemHTML(htmlData , data.appends , highlight , i);
        item.style.display = "flex";
        item.style.flexDirection = "row";
        itemsDiv.appendChild(item);
    }
    ret.items = itemsDiv.innerHTML;
    controls_data = data.group_equipments;
    controls_data.control_location = "group";
    ret.controls = controlsHtml(controls_data);
    return ret;
}

async function all_equipment_controls(information){
    controls_location = document.getElementById(information.append_to);
    control_location = document.getElementById("group-details-info");
    let table_info = {
        description_func: getEquipmentTableDescription,
        update_info: information.details.equipment_type, 
    }
    setUpdateableInfo(table_info);
    document.getElementById("update-button")
    .addEventListener("click" , async function(){
        user_id = 1
        group_id = 1 
        equipment_id = information.details.equipment_id
        user_input = await getInputInformation();
        parsed_user_input = await prepareInputForEquipment(user_input , user_id , group_id , equipment_id , information.details.equipment_type);
        server_response = await postEquipmentUpdate(parsed_user_input);
        setServerResponse(server_response.information , "server-response");
        page = document.getElementsByClassName("control-current-group-page");
        current_page = page[0].innerText.replace("Page: " , "");
        allInventoryControler(getAllEquipments , parseInt(current_page));
    });
    document.getElementById("delete-button")
    .addEventListener("click" , async function(){
        equipment_id = information.details.equipment_id
        server_response = await postEquipmentDelete(equipment_id);
        setServerResponse(server_response.information , "server-response");
        page = document.getElementsByClassName("control-current-group-page");
        current_page = page[0].innerText.replace("Page: " , "");
        allInventoryControler(getAllEquipments , parseInt(current_page));
    });
    
    document.getElementById("create-button").style.display = "flex";
    document.getElementById("update-button").style.display = "flex";
    document.getElementById("delete-button").style.display = "flex";
}

async function createEquipmentCreationArea(equipment_type){
    let table_request = await getEquipmentTableDescription(equipment_type)
    let table_description = table_request.information;
    create_new_equipment_html = `
                                <div class="input-new-equipment">
                                    <div class="new-equipment-title">
                                        New Equipment:
                                    </div>
                                    <div class="new-equipiment-input-area" id="new-equipiment-input-area">`;
    for(let [key , value] of Object.entries(table_description.default)){
        input_type = parseTableType(value.Type);
        create_new_equipment_html += `<div class="input-new-equipment" style="display:flex; flex-direction:row;">
                                        <div id="input-new-equipment-title">
                                            ${value.Field}
                                        </div>
                                        <div>
                                            <input id="${value.Field}" input_table="default" type="${input_type}">
                                        </div>
                                    </div>`
    }
    for(let [key , value] of Object.entries(table_description.specific)){
        input_type = parseTableType(value.Type);
        create_new_equipment_html += `<div class="input-new-equipment" style="display:flex; flex-direction:row;">
                                        <div id="input-new-equipment-title">
                                            ${value.Field}
                                        </div>
                                        <div>
                                            <input id="${value.Field}" input_table="specific" type="${input_type}">
                                        </div>
                                    </div>`
    }
    create_new_equipment_html += `
                                </div>`;
    return create_new_equipment_html;
}

async function createEquipmentButton(){
    document.getElementById("create-button")
    .addEventListener("click" , async function(){
    document.getElementById("update-button").style.display = "none";
    document.getElementById("delete-button").style.display = "none";
    let equipment_request = await getEquipmentTypes();
    let equipment_types = equipment_request.information.items
    let information_append = document.getElementById("info-selected");
    information_append.innerHTML = "";
    select_equipment = `<div class="equipment-types-selection-types">
                            <div class="select-equipment-type">
                                Select Equipment Type:
                            </div>
                            <div class="equipment-type-selection-area" id="equipment-type-selection-area">`
    for(let i = 0 ; i < equipment_types.length ; i++){
        select_equipment += `<div id="${equipment_types[i].equipment_type}" class="generic-button"> 
                                <div class="title-generic-button">
                                    ${equipment_types[i].equipment_type} 
                                </div>
                            </div>`
    }
    select_equipment += `</div></div>`;
    information_append.innerHTML = select_equipment;
    equipment_buttons = document.getElementById("equipment-type-selection-area").children
    for(let i = 0 ; i < equipment_buttons.length ; i++){
        equipment_buttons[i]
        .addEventListener("click" , async function(){
            information_append.innerHTML = "";
            html = await createEquipmentCreationArea(equipment_types[i].equipment_type);
            information_append.innerHTML = html;
            information_append.innerHTML += `
                            <div class="generic-button" id="send-equipment">
                                <div class="title-generic-button">
                                    Add Equipment
                                </div>
                            </div>`
            document.getElementById("send-equipment")
            .addEventListener("click" , async function(){
                input_settings = {Get_Input_table:"true"}
                info = await getInputNewEquipment(input_settings);
                let default_info = {};
                let specific_info = {};
                let equipment_type = equipment_types[i];
                let parsed_info = {};
                for(let [key , value] of Object.entries(info)){
                    if(value.table === "default"){
                        if(value.value === undefined){
                            continue;
                        }
                        default_info[key] = value.value;
                    }
                    if(value.table === "specific"){
                        if(value.value === undefined){
                            continue;
                        }
                        specific_info[key] = value.value;
                    }
                }
                parsed_info["default"] = default_info;
                parsed_info["specific"] = specific_info;
                parsed_info["equipment_type"] = equipment_type.equipment_type;
                server_response = await postEquipmenCreate(parsed_info);
                setServerResponse(server_response.information , "server-response");
                if(server_response.information.message.success === "success"){
                    allInventoryControler(getAllEquipments , 1);
                }
            });
        });
    }
    });
}

async function getInputNewEquipment(){
    let information = {};
    let inputs = document.getElementsByTagName('input');
    for(let i = 0 ; i < inputs.length ; i++){
        info = {};
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
            info["value"] = checkBoxValue; 
        }else if(inputs[i].value !== ""){
            info["value"] = inputs[i].value;
        }
        info["table"] = inputs[i].attributes.input_table.nodeValue;
        information[inputs[i].attributes.id.nodeValue] = info;
    }
    return information;
}


async function allInventoryControler(datarequest , page , limit){
    limit = 19;
    let all_equipments = await datarequest(page , limit);
    let equipment_types = await getEquipmentTypes();
    let append_page_totals = document.getElementById("group-items-total");
    let append_page_controls = document.getElementById("group-page-controls");
    let append_items = document.getElementById("items-content");
    let append_details = document.getElementById("info-selected");
    document.getElementById("info-selected").innerHTML = "";
    document.getElementById("update-button").style.display = "none"
    document.getElementById("delete-button").style.display = "none"
    let encapsulate = {
        function: encapsulateAndFilter,
        filter: [["id","equipment_id","serial_brand_md5","registration_date","equipment_type","registration_lock"]],
        conditionals:[
            [
                //[what text] , [[conditional] , [results]] 
                /// if conditional = array creates for loop with the array 
                // [array content] then you specify whats in the array more arays or an object
                // ["compare array with component " , "replace  with array component"]
                ["equipment_status","roaming","delivery_status","computer_type"],
                [
                    [["1"],["Active","Inactive"]],
                    [["1"],["Active","Inactive"]],
                    [["1"],["Active","Inactive"]],
                    [[equipment_types.information.items] , ["id" , "equipment_type"]]
                ]
            ]
        ]
    }
    appends = [["all"],["equipment_type","business_unit","brand","model","purchase_date"]];
    custom_data = {
            group_equipments: all_equipments.information
           ,control_location: "group"
           ,appends: appends
           ,title: ["type" , "business_unit" , "brand" , "model" , "purchase date" ]
           ,equipment_types: equipment_types.information
    };
    append_items.innerHTML = "";
    append_page_controls.innerHTML = "";
    htmlData = await generateFullInventoryHtml(custom_data);
    append_items.innerHTML = htmlData.items;
    append_page_controls.innerHTML = htmlData.controls.pageControl;
    append_page_totals.innerHTML = htmlData.controls.totalItems;
    await setFetchedItemsUI("items-content" , 19 , custom_data.equipment_types.items , 5);
    information = {
        function: all_equipment_controls,
        append_to: "group-details-controls",
        equipment_types: equipment_types
    }
    appends_to = ["all" , "info-selected"]
    itemAllDetails("#items-content" , appends_to , all_equipments.information , ["equipment"] , encapsulate , information)
    pageAllControlsFunctionality("#page-controls-group" , allInventoryControler , getAllEquipments);
}

async function allTabFunctionality(){
    allInventoryControler(getAllEquipments , 1 );
    createEquipmentButton();
}
