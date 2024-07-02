
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
    document.getElementById("update-button").style.display = "flex";
    document.getElementById("delete-button").style.display = "flex";
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
    await setFetchedItemsUI("items-content" , 20 , custom_data.equipment_types.items , 5);
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
}
