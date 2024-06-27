
function pageControlsFunctionality(control_location , loadingFunction){
    control_div = document.querySelectorAll(control_location);
    total_controls = control_div[0].children
    for(i = 0 ; i < total_controls.length ; i++){
        if(total_controls[i].attributes.page === undefined)
            continue;
        controls = document.getElementById(total_controls[i].attributes.id.nodeValue);
        controls.addEventListener('click' , async function(){
            loadingFunction(getAuthEquipments , controls.attributes.page.nodeValue);
        });
        controls.style.cursor = "pointer";
    }
}

function itemReadDetails(content_location , append_to , information , information_types){
    let control_div = document.querySelectorAll(content_location);
    let append_details = document.getElementById(append_to);
    let total_controls = control_div[0].children
    let htmlInformation = [];
    let html = [];
    let title = 0;
    for(let i = 0 ; i < total_controls.length ; i++){
        controls = document.getElementById(total_controls[i].attributes.id.nodeValue);
        if(total_controls[i].attributes.id.nodeValue === "title-bar"){
            title = 1;
            continue;
        }
        for(let j = 0 ; j < information_types.length ; j++){
            htmlInformation[j] = createDetailsHtml(information.items[i-title][information_types[j]] , information_types[j]);
            html[i-title] += htmlInformation[j].innerHTML;
            console.log(html[i-title]);
        }
        controls.addEventListener('click' , async function(){
            append_details.innerHTML = html[i-title];
        });
        controls.style.cursor = "pointer";
    }
}

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

async function inventoryControler(datarequest , page){
    group_equipments = await datarequest(page);
    equipment_types = await getEquipmentTypes();
    append_page_totals = document.getElementById("group-items-total");
    append_page_controls = document.getElementById("group-page-controls");
    append_items = document.getElementById("items-content");
    append_details = document.getElementById("info-selected");
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
    pageControlsFunctionality("#page-controls-group" , inventoryControler);
    itemReadDetails("#items-content" , "info-selected" , group_equipments.information , ["user" , "group" , "equipment"])
}

async function groupTabFunctionality(){
     inventoryControler(getAuthEquipments , 1);
}
