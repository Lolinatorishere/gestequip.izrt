
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


function tabLoadUi(tab , request , content_id , highlight_id , tab_html){
    let tab_content = document.getElementById(content_id)
       ,tab_element = document.getElementById(tab.id);
    tab.addEventListener('click' , async function(){
        setTabHighlight(tab_element , highlight_id);
        await fetch('/frontend/html/pages/equipment/tabs/' + tab_html +'.html')
        .then(function(response){
            return response.text()
        })
        .then(function(html){
            document.getElementById(content_id).innerHTML = html;
        }); 
        if(typeof callback === 'function'){
            callback(tab);
        }
    });
}

async function defaultSearchTab(callback){
    let tab_content = document.getElementById("tabbar-options")
       ,tab_element = document.getElementById("user-group-search");
        setTabHighlight(tab_element , "selected-search-tab");
    await fetch('/frontend/html/pages/equipment/tabs/search_tab/group_user.html')
    .then(function(response){
        return response.text()
    })
    .then(function(html){
        document.getElementById("tabbar-options").innerHTML = html;
    }); 
    if(typeof callback === 'function'){
        callback();
    }
    await searchTabUserControler(1);
    await searchTabGroupControler(1);
}

async function generateUserItemsHTML(data){
    let itemsDiv = document.createElement('div')
    let users = data.users.information.items;
    let highlight = "";
    let ret = {};
    title = createTitleHTML(data);
    if(title !== undefined){
        itemsDiv.appendChild(title);
    }
    for(let i = 0 ; i < users.length ; i++){
        htmlData = {
            users_name: users[i].users_name,
            email: users[i].email
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
    controls_data = data.users.information;
    controls_data.control_location = "user";
    ret.controls = controlsHtml(controls_data);
    return ret;
}

async function generateGroupItemsHTML(data){
    let itemsDiv = document.createElement('div')
    let users = data.groups.information.items;
    let highlight = "";
    let ret = {};
    title = createTitleHTML(data);
    if(title !== undefined){
        itemsDiv.appendChild(title);
    }
    for(let i = 0 ; i < users.length ; i++){
        htmlData = {
            users_name: users[i].users_name,
            email: users[i].email
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
    controls_data = data.groups.information;
    controls_data.control_location = "user";
    ret.controls = controlsHtml(controls_data);
    return ret;
}

async function searchItemsFetchedFunctionality(information , item_location , item_function , callback){
    items = document.getElementById(item_location);
    for(let i = 1 ; i < items.children.length ; i++){
        item = items.children[i];
        item.addEventListener('click' , async function(){
            if(typeof item_function !== "function")
                return
            item_function(information[i-1] , 1);
        })
    }
}

async function searchTabUserControler(page){
    let users = await getUsers(page , 10);
    let append_page_controls = document.getElementById("users-controls");
    let append_items = document.getElementById("users-items");
    console.log(append_items);
    append_items.innerHTML = "";
    append_page_controls.innerHTML = "";
    appends = [["usr"],["users_name","email"]];
    custom_data = {
            users: users
           ,appends: appends
           ,title: ["Name" , "Email"]
    };
    htmlData = await generateUserItemsHTML(custom_data);
    console.log(htmlData);
    append_items.innerHTML = htmlData.items;
    append_page_controls.innerHTML = htmlData.controls.pageControl;
    document.getElementById("title-bar-usr").style.height = "";
    await setFetchedItemsUI("users-items" , 10 , custom_data.users , 5);
    await searchItemsFetchedFunctionality(users.information.items , "users-items" , searchGroupFromUserControler , "callback");
    await pageControlsFunctionality("#page-controls-user" , searchTabUserControler);
}

async function searchGroupFromUserControler(information , page){
    console.log(information);

}

async function searchTabGroupControler(page){
    let groups = await getGroups(page , 10);
    let append_page_controls = document.getElementById("groups-controls");
    let append_items = document.getElementById("groups-items");
    append_items.innerHTML = "";
    append_page_controls.innerHTML = "";
    appends = [["grp"],["group_name","grp_status"]];
    custom_data = {
            groups: groups
           ,appends: appends
           ,title: ["Group" , "Status"]
    };
    for(let i = 0 ; i < groups.information.items.length ; i++){
        console.log(i);
        if(groups.information.items[i].status === "1"){
            custom_data.groups.information.items[i]["grp_status"] = "Active";
        }else{
            custom_data.groups.information.items[i]["grp_status"] = "Inactive";
        }
    }
    console.log(custom_data.groups);
    htmlData = await generateGroupItemsHTML(custom_data);
    append_items.innerHTML = htmlData.items;
    append_page_controls.innerHTML = htmlData.controls.pageControl;
    document.getElementById("title-bar-grp").style.height = "";
    console.log(document.getElementById("title-bar"));
    await setFetchedItemsUI("groups-items" , 10 , custom_data.groups , 5);
    await searchItemsFetchedFunctionality(groups.information.items , "groups-items" , searchUserFromGroupControler , "callback");
    await pageControlsFunctionality("#page-controls-user" , searchTabGroupControler);
}

async function searchUserFromGroupControler(page){
    console.log(information)
}

async function searchControler(datarequest , page){
    
}

async function searchTabFunctionality(){
    defaultSearchTab();
    internalTabSetter("#parameter-tabbar" , searchTabbarFunctionality)
    searchControler(getAuthEquipments , 1);
}

async function searchTabbarFunctionality(tab){
    switch(tab.id){
        case 'user-group-search':
            await tabLoadUi(tab , "default" , "tabbar-options" , "selected-search-tab" , 'search_tab/group_user')
            tab.addEventListener("click" , async function(){
                await searchTabUserControler(1);
                await searchTabGroupControler(1);
            })
            break;
        case 'equipment-specific':
            tabLoadUi(tab , "default" ,"tabbar-options" , "selected-search-tab" , 'search_tab/equipment_type')
            break;
        default:
            break;
    }
}

