
function setControls(data , append_to , refresh , loadingFunction){
    let totalDiv = document.createElement('div')
       ,controlDiv = document.createElement('div')
       ,controls = document.createElement('div')
       ,info = data.information
       ,control_location = '';
    if(data.control_location !== undefined && data.control_page !== ""){
        control_location = data.control_location;
    }else{
        data.control_location = undefined;
    }
    info.control_location = control_location;
    totalDiv.className = 'total';
    totalDiv.innerHTML = `
                            Total: ${info.total_items}
                         `
    controlDiv.className = 'controls';
    controlDiv.innerHTML = controlsHtml(info);
    controls.appendChild(totalDiv);
    controls.appendChild(controlDiv);
    append_to.innerHTML = controls.innerHTML;
    controlsFunctionality(data , refresh , loadingFunction);
}

async function setInventoryHtml(data , append_items){
    let itemsDiv = document.createElement('div')
    let group_items = data.group_equipments.items;
    let equipment_type = ""
    let highlight = "";
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
    append_items.innerHTML = itemsDiv.innerHTML;
    custom_data.control_location = "group";
    controlsHtml(custom_data);
}

async function groupTabFunctionality(){
    group_equipments = await getAuthEquipments();
    equipment_types = await getEquipmentTypes();
    append_controls = document.getElementById("items-controls");
    append_items = document.getElementById("items-content");
    append_details = document.getElementById("info-selected");
    appends = [["grp"],["users_name","group_name","equipment_type","brand","model","purchase_date","equipment_status"]];
    custom_data = {
            group_equipments: group_equipments.information
           ,control_location: "group"
           ,appends: appends
           ,title: ["group" , "user" , "type" , "brand" , "model" , "purchase date" , "status"]
           ,equipment_types: equipment_types.information
    };
    setInventoryHtml(custom_data , append_items);
}
