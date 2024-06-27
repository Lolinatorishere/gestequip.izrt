
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

async function tabLoadUi(tab , callback){
    await fetch('/frontend/html/pages/equipment/tabs/' + tab +'.html')
        .then(function(response){
            return response.text()
        })
        .then(function(html){
            document.getElementById("tab-content").innerHTML = html;
        }); 
    if(typeof callback === 'function'){
        callback(tab);
    }
}


function tabLoadUi(tab , request , content_id , highlight_id, rfsh , rgin , ){
    let tab_content = document.getElementById(content_id)
       ,tab_element = document.getElementById(tab.id);
    tab.addEventListener('click' , async function(){
        setTabHighlight(tab_element , highlight_id);
        request.custom.rfsh = rfsh;
        request.custom.rgin = rgin;
        response = await fetch(await urlCreateBackendRequest(request));
        tab_html = await response.json();
        tab_content.innerHTML = tab_html.html;
    });
}


async function searchControler(datarequest , page){
    
}

async function searchTabFunctionality(){
    internalTabSetter("#parameter-tabbar" , searchTabbarFunctionality)
     //searchControler(getAuthEquipments , 1);
}

async function searchTabbarFunctionality(tab){
    switch(tab.id){
        case 'user-group-search':
            tabLoadUi(tab , request , "tabbar-options" , "selected-search-tab" , 'search_tab/group_user' , 'none' )
            break;
        case 'equipment-default':
            tabLoadUi(tab , request , "tabbar-options" , "selected-search-tab" , 'search_tab/equipment' , 'none' )
            break;
        case 'equipment-specific':
            tabLoadUi(tab , request , "tabbar-options" , "selected-search-tab" , 'search_tab/equipment_type' , 'none' )
            break;
        default:
            break;
    }
}

