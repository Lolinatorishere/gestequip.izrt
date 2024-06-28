
async function genertateInventoryHtml(data){
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
            if(group_items[i].equipment.equipment_type === data.equipment_types.items[j].id){
                equipment_type = data.equipment_types.items[j].equipment_type;
                break;
            }
        }
        if(group_items[i].equipment.status === "1"){
            equipment_status = "Active";
        }else{
            equipment_status = "Inactive";
        }
        htmlData = {
            group_name: group_items[i].group.group_name,
            users_name: group_items[i].user.users_name,
            equipment_type: equipment_type,
            brand: group_items[i].equipment.brand,
            model: group_items[i].equipment.model,
            purchase_date: group_items[i].equipment.purchase_date,
            equipment_status: equipment_status
        }
        if(i%2 === 0){
            highlight = "true";
        }else{
            highlight = "false";
        }
        item = createItemHTML(htmlData , data.appends , highlight , i);
        itemsDiv.appendChild(item);
    }
    ret.items = itemsDiv.innerHTML;
    controls_data = data.group_equipments;
    controls_data.control_location = "group";
    ret.controls = controlsHtml(controls_data);
    return ret;
}

async function equipment_controls(information){
    controls_location = document.getElementById(information.append_to);
    user_auth = await getUserGroupAuth(information.details.group.id);
    control_location = document.getElementById("group-details-info");
    switch(user_auth.information.items.user_permission_level){
        case 0:
            //no buttons for you deadass
            break
        case 1:
            button = {
                className:"update-button",
                id:"update-button",
                message:"Alter Equipment",
                function:"update-button"
            }
            control_location.appendChild(createButtonHtml(button)); 
            break
        case 2:
            alter_button = {
                className:"alter-button",
                id:"alter-button",
                message:"Alter Equipment",
            }
            delete_button = {
                className:"delete-button",
                id:"delete-button",
                message:"Delete Reference",
            }
            update_button = {
                className: "update-button",
                id:"update-button",
                message:"Update Equipment"
            }
            alter_button_internal = {
                description_data: getTableDescription,
                update_info: information.details.equipment.equipment_type, 
            }
            buttons = {
                html: [alter_button , delete_button ,update_button],
                functions: [setUpdateableInfo , "" , ""],
                internal: [alter_button_internal , "" , ""],
                callback: [{
                    function:updateButtonControler,
                    internal:{
                        request: postEquipmentUpdate,
                        information: information.details
                    }
                }]
            }
            control_location.innerHTML = "";
            control_location.appendChild(createButtonsFunctionality(buttons));
            break
    }
}

async function inventoryControler(datarequest , page){
    let group_equipments = await datarequest(page);
    let equipment_types = await getEquipmentTypes();
    let append_page_totals = document.getElementById("group-items-total");
    let append_page_controls = document.getElementById("group-page-controls");
    let append_items = document.getElementById("items-content");
    let append_details = document.getElementById("info-selected");
    for(let i = 0 ; i < group_equipments.information.items.length ; i++){
        item = group_equipments.information.items[i];
        group_equipments.information.items[i].user.phone = item.user.phone_number;
        delete(group_equipments.information.items[i].user.phone_number);
    }
    let encapsulate = {
        function: encapsulateAndFilter,
        filter: [["id","username","regional_indicator"]
                ,["id","group_status","group_type"]
                ,["id","equipment_id","has_battery","registration_lock","serial_brand_md5","registration_date","equipment_type", "IMEI"]
                ],
        conditionals:[
            [[],[]],
            [[],[]],
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
    appends = [["grp"],["group_name","users_name","equipment_type","brand","model","purchase_date"]];
    custom_data = {
            group_equipments: group_equipments.information
           ,control_location: "group"
           ,appends: appends
           ,title: ["group" , "user" , "type" , "brand" , "model" , "purchase date" ]
           ,equipment_types: equipment_types.information
    };
    htmlData = await genertateInventoryHtml(custom_data);
    append_items.innerHTML = htmlData.items;
    append_page_controls.innerHTML = htmlData.controls.pageControl;
    append_page_totals.innerHTML = htmlData.controls.totalItems;
    await setFetchedItemsUI("items-content" , 20 , custom_data.equipment_types.items , 5);
    information = {
        function: equipment_controls,
        append_to: "group-details-controls"
    }
    itemReadDetails("#items-content" , "info-selected" , group_equipments.information , ["user" , "group" , "equipment"] , encapsulate , information)
    pageControlsFunctionality("#page-controls-group" , inventoryControler , getAuthEquipments);
}

async function groupTabFunctionality(){
    inventoryControler(getAuthEquipments , 1);
}
