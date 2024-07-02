
async function search_controls(information){
    controls_location = document.getElementById(information.append_to);
    control_location = document.getElementById("search-details-info");
    let table_info = {
        description_func: getEquipmentTableDescription,
        update_info: information.details.equipment.equipment_type, 
    }
}

async function generateSearchedHtml(data){
    let itemsDiv = document.createElement('div')
    let search_items = data.search_equipments.items;
    if(search_items === undefined){
        return;
    }
    let equipment_type = ""
    let highlight = "";
    let ret = {};
    title = createTitleHTML(data);
    if(title !== undefined){
        itemsDiv.appendChild(title);
    }
    for(i = 0 ; i < search_items.length ; i++){
        for(j = 0 ; j < data.equipment_types.items.length ; j++){
            if(search_items[i].equipment_type === data.equipment_types.items[j].id){
                equipment_type = data.equipment_types.items[j].equipment_type;
                break;
            }
        }
        if(search_items[i].status === "1"){
            equipment_status = "Active";
        }else{
            equipment_status = "Inactive";
        }
        htmlData = {
            equipment_type: equipment_type,
            business_unit: search_items[i].business_unit,
            brand: search_items[i].brand,
            model: search_items[i].model,
            purchase_date: search_items[i].purchase_date,
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
    // todo probably removing this and replacing it outside the function may fix the lack of controls Ui updating
    let controls_data = {}
    controls_data = data.search_equipments;
    controls_data.control_location = "search";
    ret.controls = controlsHtml(controls_data);
    return ret;
}


async function encapsulateSearchAndFilter(encapsulation_location , filter , conditionals){
    content = encapsulation_location.children;
    let content_width = [];
    let parent_height = encapsulation_location.clientHeight;
    let content_height = [];
    let total_height = 0;
    for(let i = 0 ; i < content.length ; i++){
        let width = content[i].clientWidth;
        let height = content[i].clientHeight;
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
        total_height = children_height;
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
            item_content.children[0].style.fontSize = "0.8rem";
            item_content.children[0].style.display = "flex";
            item_content.children[0].style.flexDirection = "column";
            item_content.children[0].style.justifyContent = "center";
            item_content.children[0].style.textWrap = "wrap";
            item_content.children[0].style.marginLeft = "6px";
            item_content.children[0].style.paddingRight = "-6px";
            item_content.children[0].style.borderWidth = "2px";
            item_content.children[0].style.borderStyle = "none dotted dotted none";
            item_content.children[0].style.borderColor = "rgb(170, 170, 170)";
            item_content.children[1].style.width = value_width+"%";
            item_content.children[1].style.textWrap = "wrap";
            item_content.children[1].style.fontSize = "0.8rem";
            item_content.children[1].style.marginLeft = "6px";
            item_content.children[1].style.paddingRight = "-6px";
            item_content.children[1].style.borderWidth = "2px";
            item_content.children[1].style.borderStyle = "none none dotted none";
            item_content.children[1].style.borderColor = "rgb(170, 170, 170)";
            item_content.children[1].style.display = "flex";
            item_content.children[1].style.flexDirection = "column";
            item_content.children[1].style.justifyContent = "center";
        }
    }
    content[0].style.height = total_height*1.1 + "px";
}

function itemSearchedDetails(content_location , append_to , information , encapsulate , callback){
    let control_div = document.querySelectorAll(content_location);
    let append_details = document.getElementById(append_to[1]);
    let total_controls = control_div[0].children
    let htmlInformation;
    let html = [];
    let title = 0;
    for(let i = 0 ; i < total_controls.length ; i++){
        controls = document.getElementById(total_controls[i].attributes.id.nodeValue);
        html[i] = ""
        if(total_controls[i].attributes.id.nodeValue === "title-bar-"+append_to[0]){
            title = 1;
            continue;
        }
        information.items[parseInt(i)-parseInt(title)];
        htmlInformation = createDetailsHtml(information.items[i-title] , "searched-equipment");
        html[i-title] += htmlInformation.innerHTML;

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

async function searchControler(page){
    query = await getQueryInputs();
    if(query === undefined){
        alert("No Query Requested")
        return;
    }
    let searched_equipments = await getSearchEquipment(page , 4 , query);
    let equipment_types = await getEquipmentTypes();
    let append_page_totals = document.getElementById("search-items-total");
    let append_page_controls = document.getElementById("search-page-controls");
    let append_items = document.getElementById("results-content");
    let append_details = document.getElementById("info-selected");
    if(searched_equipments.information.items === undefined){
        document.getElementById("search-page-controls").innerHTML = "";
        document.getElementById("search-items-total").innerHTML = "";
        document.getElementById("results-content").innerHTML = "";
        document.getElementById("search-page-controls").innerHTML = searched_equipments.information.message;
        return;
    }
    document.getElementById("info-selected").innerHTML = "";
    let encapsulate = {
        function: encapsulateSearchAndFilter,
        filter: [["id","equipment_id","has_battery","registration_lock","serial_brand_md5","registration_date","equipment_type", "IMEI"]],
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
    appends = [["sch"],["equipment_type", "business_unit" ,"brand","model","purchase_date"]];
    custom_data = {
            search_equipments: searched_equipments.information.items
           ,control_location: "search"
           ,appends: appends
           ,title: ["type" , "business unit" , "brand" , "model" , "purchase date" ]
           ,equipment_types: equipment_types.information
    };
    append_items.innerHTML = "";
    append_page_controls.innerHTML = "";
    htmlData = await generateSearchedHtml(custom_data);
    append_items.innerHTML = htmlData.items;
    append_page_controls.innerHTML = htmlData.controls.pageControl;
    append_page_totals.innerHTML = htmlData.controls.totalItems;
    await setFetchedItemsUI("results-content" , 5 , custom_data.search_equipments.items , 5);
    information = {
        function: search_controls,
        append_to: "group-details-controls",
        equipment_types: equipment_types
    }
    appends_to = ["sch" , "info-selected"]
    itemSearchedDetails("#results-content" , appends_to , searched_equipments.information.items ,  encapsulate)
    pageControlsFunctionality("#page-controls-search" , searchControler);
}

async function tabLoadUi(tab , request , content_id , highlight_id , tab_html , callback){
    let tab_content = document.getElementById(content_id)
       ,tab_element = document.getElementById(tab.id);
    tab.addEventListener('click' , async function(){
        setTabHighlight(tab_element , highlight_id);
        await fetch('/frontend/html/pages/equipment/tabs/' + tab_html +'.html')
        .then(async function(response){
            return response.text()
        })
        .then(async function(html){
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
    let group = data.groups.information.items;
    let highlight = "";
    let ret = {};
    title = createTitleHTML(data);
    if(title !== undefined){
        itemsDiv.appendChild(title);
    }
    for(let i = 0 ; i < group.length ; i++){
        htmlData = {
            group_name: group[i].group_name,
            grp_status: group[i].grp_status
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
    controls_data.control_location = "group";
    ret.controls = controlsHtml(controls_data);
    return ret;
}

async function generateItemTypesHTML(data){
    let itemsDiv = document.createElement('div')
    let types = data.information.items;
    let highlight = "";
    let ret = {};
    let appends = [["type"] , ["equipment_type"]];
    for(let i = 0 ; i < types.length ; i++){
        htmlData = {
            equipment_type: types[i].equipment_type,
        }
        if(i%2 === 0){
            highlight = "true";
        }else{
            highlight = "false";
        }
        item = createItemHTML(htmlData , appends , highlight , i);
        itemsDiv.appendChild(item);
    }
    ret = itemsDiv.innerHTML;
    return ret;
}

async function searchItemsFetchedFunctionality(information , item_location , item_function , callback){
    items = document.getElementById(item_location);
    for(let i = 1 ; i < items.children.length ; i++){
        item = items.children[i];
        item.style.cursor = "pointer";
        item.addEventListener('click' , async function(){
            if(typeof item_function !== "function")
                return
            item_function(information[i-1]);
        })
    }
}

async function searchTypesFechedFunctionality(information , item_location){
    items = document.getElementById(item_location);
    for(let i = 0 ; i < items.children.length ; i++){
        item = items.children[i];
        item.style.cursor = "pointer";
        item.addEventListener('click' , async function(){
            onclickEventListenerEquipmentType(information[i]);
            controlEquipmentTableDescription(information[i].id);
        })
    }
}

async function onclickEventListenerEquipmentType(information){
    if(document.getElementById("selected-type-query") !== null){
        document.getElementById("selected-type-query").remove();
    }
    if(document.getElementById("selected-type-query-full") !== null){
        document.getElementById("selected-type-query-full").remove();
    }
    selected = document.createElement('div');
    stored = document.createElement('div');
    selected.id = "selected-type-query";
    selected.style.borderStyle = "none none solid none"
    selected.style.borderColor = "rgb(170, 170, 170)"
    selected.style.borderWidth = "2px"
    selected.style.backgroundColor = "rgb(239, 239, 239)";
    selected.innerHTML = `
                        <div class="selected-type-query-title" style = "font-size: 1.3rem;">
                            Type:
                        </div>
                        <div class="selected-type-query-content">
                            ${information.equipment_type}
                        </div>
                         `;
    stored.id = "selected-type-query-full";
    stored.innerHTML = `
                        <div id="equipment_type_id">
                            ${information.id}
                        </div>
                        <div id="equipment_type_name">
                            ${information.equipment_type}
                        </div>
                        `
    document.getElementById("search-query-full").appendChild(stored);
    document.getElementById("query-content").appendChild(selected);
}

async function createUserEquipmentSpecificTableHTML(edit_tables){
    input_table = document.getElementById("description-type-table");
    input_table.innerHTML = "";
    user_inputs = document.createElement("div");
    user_inputs.id = "search-query-inputs";
    user_inputs.className = "search-query-inputs";
    for(let [key , value] of Object.entries(edit_tables.default)){
        input_div = document.createElement("div");
        input_title = document.createElement("div");
        if(value.Type === "tinyint(1)"){
            continue;
        }
        let value_parse = value.Field.replace("_" , " ");
        title = `${value_parse}:`
        input_title.className = "title-input-search-tab";
        input_title.innerHTML = title;
        input = {
            className: "search-query-inputs-content",
            id: value.Field,
            type: parseTableType(value.Type),
            placeholder: " "
        }
        let inputdiv = createInputHtml(input);
        inputdiv.attributes["tableType"] = "default";
        if(document.getElementById("default-" + value.id) !== null){
            inputdiv.value = document.getElementById("default-" + value.id).innerText;
        }
        input_div.appendChild(input_title);
        input_div.appendChild(inputdiv);
        user_inputs.appendChild(input_div);
    }
    for(let [key , value] of Object.entries(edit_tables.specific)){
        input_div = document.createElement("div");
        input_title = document.createElement("div");
        if(value.Type === "tinyint(1)"){
            continue;
        }
        let value_parse1 = value.Field.replace("_" , " ");
        let value_parse = value_parse1.replace("_" , " ");
        title = `${value_parse}:`
        input_title.className = "title-input-search-tab";
        input_title.innerHTML = title;
        input = {
            className: "search-query-inputs-content",
            id: value.Field,
            type: parseTableType(value.Type),
            placeholder: " "
        }
        let inputdiv = createInputHtml(input);
        inputdiv.attributes["tableType"] = "specific";
        if(document.getElementById("specific-" + value.id) !== null){
            inputdiv.value = document.getElementById("specific-" + value.id).innerText;
        }
        input_div.appendChild(input_title);
        input_div.appendChild(inputdiv);
        user_inputs.appendChild(input_div);
    }
    input_table.appendChild(user_inputs);
}


async function addQueryButtonFunctionality(){
    let search_query = document.getElementById("query-content");
    let parsed_search_query = document.getElementById("search-query-full");
    document.getElementById("button-add-query")
    .addEventListener("click" , async function(){
        if(document.getElementById("selected-default-query") !== null){
        document.getElementById("selected-default-query").remove();
        }
        if(document.getElementById("selected-default-query-full") !== null){
            document.getElementById("selected-default-query-full").remove();
        }
        if(document.getElementById("selected-specific-query") !== null){
            document.getElementById("selected-specific-query").remove();
        }
        if(document.getElementById("selected-specific-query-full") !== null){
            document.getElementById("selected-specific-query-full").remove();
        }
        parsed = {
            default:[],
            specific:[]
        };
        default_total = 0;
        checkbox_guard = 0;
        inputs = document.getElementById("search-query-inputs")
        for(let i = 0 ; i < inputs.children.length ; i++){
            input = inputs.children[i].children[1];
            if(input.value !== ""){
                if(input.type === "checkbox"){
                    if(input.checked !== false){
                        checkbox_guard++;
                    }
                }else{
                    checkbox_guard++;
                }
            };
            if(input.attributes.tableType === "default"){
                default_total++;
            }
            if(input.type === "checkbox"){
                continue;
            }else{
                parsed[input.attributes.tableType][i] = {
                    id:input.id , value:input.value
                }
            }
        }
        if(checkbox_guard !== 0){
            parsed_default = document.createElement("div");
            parsed_specific = document.createElement("div");

            query_default = document.createElement("div");
            query_specific = document.createElement("div");

            query_default.id = "selected-default-query";
            query_default.className = "selected-default-query";
            query_default.style.backgroundColor = "rgb(239, 239, 239)";
            query_default.style.borderColor = "rgb(170, 170, 170)";
            query_default.style.borderStyle = "none none solid none";
            query_default.style.borderWidth = "2px";
            query_default.innerHTML = `
                                        <div class="selected-default-query-title" style="font-size: 1.3rem;">
                                            Default:
                                        </div>
                                        `

            query_specific.id = "selected-specific-query";
            query_specific.className = "selected-specific-query";
            query_specific.style.backgroundColor = "rgb(239, 239, 239)";
            query_specific.style.borderColor = "rgb(170, 170, 170)";
            query_specific.style.borderStyle = "none none solid none";
            query_specific.style.borderWidth = "2px";
            query_specific.innerHTML = `
                                        <div class="selected-specific-query-title" style="font-size: 1.3rem;">
                                            Specific:
                                        </div>
                                        `

            parsed_default.id = "selected-default-query-full";
            parsed_specific.id = "selected-specific-query-full";

            for(let i = 0 ; i < parsed.default.length ; i++){
                parsed_input_default = document.createElement("div");

                query_default_item = document.createElement("div");
                query_default_title = document.createElement("div");
                query_default_content = document.createElement("div");
                
                parsed_input_default.id = "default-" + parsed.default[i].id;
                parsed_input_default.innerHTML = parsed.default[i].value;

                query_default_item.className = "selected-default-content"
                query_default_item.style.display = "flex";
                query_default_item.style.fontSize = "0.8rem";
                query_default_item.style.flexDirection = "row";
                parsed_default.appendChild(parsed_input_default);
                if(parsed.default[i].value !== ""){
                    query_default_title.innerHTML = parsed.default[i].id;
                    query_default_content.innerHTML = parsed.default[i].value;
                    query_default_content.style.textWrap = "wrap"

                    query_default_item.appendChild(query_default_title);
                    query_default_item.appendChild(query_default_content);
                    query_default.appendChild(query_default_item);
                }
            }
            for(let i = default_total ; i < parsed.specific.length ; i++){
                parsed_input_specific = document.createElement("div");

                query_specific_item = document.createElement("div");
                query_specific_title = document.createElement("div");
                query_specific_content = document.createElement("div");
                
                parsed_input_specific.id = "specific-" + parsed.specific[i].id;
                parsed_input_specific.innerHTML = parsed.specific[i].value;

                query_specific_item.class = "selected-specific-content"
                query_specific_item.style.display = "flex";
                query_specific_item.style.fontSize = "0.8rem";
                query_specific_item.style.flexDirection = "row";
                parsed_specific.appendChild(parsed_input_specific);
                if(parsed.specific[i].value !== ""){
                    query_specific_title.innerHTML = parsed.specific[i].id;
                    query_specific_content.innerHTML = parsed.specific[i].value;
                    query_specific_content.style.textWrap = "wrap"
                    query_specific_item.appendChild(query_specific_title);
                    query_specific_item.appendChild(query_specific_content);
                    query_specific.appendChild(query_specific_item);
                }
            }
            search_query.appendChild(query_default);
            search_query.appendChild(query_specific);
            parsed_search_query.appendChild(parsed_default);
            parsed_search_query.appendChild(parsed_specific);
        }
    });
}

async function controlEquipmentTableDescription(equipment_types){
    let equipment_type;
    if(document.getElementById("equipment_type_name") !== null){
        equipment_type = document.getElementById("equipment_type_name").innerText;
    }else{
        equipment_type = equipment_types
    }
    tb_desc = await getEquipmentTableDescription(equipment_type);
    createUserEquipmentSpecificTableHTML(tb_desc.information);
    document.getElementById("add-search-query-tab").style.display = "flex";
    if(document.getElementById("selected-default-query") !== null){
        document.getElementById("selected-default-query").remove();
    }
    if(document.getElementById("selected-default-query-full") !== null){
        document.getElementById("selected-default-query-full").remove();
    }
    if(document.getElementById("selected-specific-query") !== null){
        document.getElementById("selected-specific-query").remove();
    }
    if(document.getElementById("selected-specific-query-full") !== null){
        document.getElementById("selected-specific-query-full").remove();
    }
    addQueryButtonFunctionality();
}

async function searchTabUserControler(page){
    let users = await getUsers(page);
    let append_page_controls = document.getElementById("users-controls");
    let append_items = document.getElementById("users-items");
    if(append_items !== null){
        append_items.innerHTML = "";
        append_page_controls.innerHTML = "";
    }
    appends = [["usr"],["users_name","email"]];
    custom_data = {
            users: users
           ,appends: appends
           ,title: ["User" , "Email"]
    };
    htmlData = await generateUserItemsHTML(custom_data);
    append_items.innerHTML = htmlData.items;
    append_page_controls.innerHTML = htmlData.controls.pageControl;
    document.getElementById("title-bar-usr").style.height = "";
    let group_controls = document.getElementById("users-controls")
    group_controls.children[0].style.fontSize = "0.8rem";
    await setFetchedItemsUI("users-items" , 10 , custom_data.users , 5);
    await searchItemsFetchedFunctionality(users.information.items , "users-items" , selectedUserFromControler , "callback");
    await pageControlsFunctionality("#page-controls-user" , searchTabUserControler);
}

async function selectedUserFromControler(information){
    if(document.getElementById("selected-user-query") !== null){
        document.getElementById("selected-user-query").remove();
    }
    if(document.getElementById("selected-user-query-full") !== null){
        document.getElementById("selected-user-query-full").remove();
    }
    selected = document.createElement('div');
    stored = document.createElement('div');
    selected.id = "selected-user-query";
    selected.style.borderStyle = "none none solid none"
    selected.style.borderColor = "rgb(170, 170, 170)"
    selected.style.borderWidth = "2px"
    selected.style.backgroundColor = "rgb(239, 239, 239)";
    selected.innerHTML = `
                        <div class="selected-user-query-title"style = "font-size: 1.3rem;">
                            User:
                        </div>
                        <div class="selected-user-query-content">
                            <div>
                                ${information.users_name}
                            </div>
                            <div>
                                ${information.email}
                            </div>
                        </div>
                         `;
    stored.id = "selected-user-query-full";
    stored.innerHTML = `
                        <div id="user_id">
                            ${information.id}
                        </div>
                        `
    document.getElementById("search-query-full").appendChild(stored);
    document.getElementById("query-content").appendChild(selected);
}

async function searchTabGroupControler(page){
    let groups = await getGroups(page);
    let append_page_controls = document.getElementById("groups-controls");
    let append_items = document.getElementById("groups-items");
    if(append_items !== null){
        append_items.innerHTML = "";
        append_page_controls.innerHTML = "";
    }
    appends = [["grp"],["group_name","grp_status"]];
    custom_data = {
            groups: groups
           ,appends: appends
           ,title: ["Group" , "Status"]
    };
    for(let i = 0 ; i < groups.information.items.length ; i++){
        if(groups.information.items[i].status === "1"){
            custom_data.groups.information.items[i]["grp_status"] = "Active";
        }else{
            custom_data.groups.information.items[i]["grp_status"] = "Inactive";
        }
    }
    htmlData = await generateGroupItemsHTML(custom_data);
    append_items.innerHTML = htmlData.items;
    append_page_controls.innerHTML = htmlData.controls.pageControl;
    let group_controls = document.getElementById("groups-controls")
    group_controls.children[0].style.fontSize = "0.8rem";
    await setFetchedItemsUI("groups-items" , 10 , groups , 5);
    await searchItemsFetchedFunctionality(groups.information.items , "groups-items" , selectedGroupFromControler , "callback");
    await pageControlsFunctionality("#page-controls-group" , searchTabGroupControler);
}

async function selectedGroupFromControler(information){
    if(document.getElementById("selected-group-query") !== null){
        document.getElementById("selected-group-query").remove();
    }
    if(document.getElementById("selected-group-query-full") !== null){
        document.getElementById("selected-group-query-full").remove();
    }
    selected = document.createElement('div');
    stored = document.createElement('div');
    selected.id = "selected-group-query";
    selected.style.borderStyle = "none none solid none"
    selected.style.borderColor = "rgb(170, 170, 170)"
    selected.style.borderWidth = "2px"
    selected.style.backgroundColor = "rgb(239, 239, 239)";
    selected.innerHTML = `
                        <div class="selected-group-query-title" style = "font-size: 1.3rem;">
                            Group:
                        </div>
                        <div class="selected-group-query-content">
                            ${information.group_name}
                        </div>
                         `;
    stored.id = "selected-group-query-full";
    stored.innerHTML = `
                        <div id="group_id">
                            ${information.id}
                        </div>
                        `
    document.getElementById("search-query-full").appendChild(stored);
    document.getElementById("query-content").appendChild(selected);
}

async function equipmentSearchControler(){
    let typeAppend = document.getElementById("eq-type-load")
    equipment_types = await getEquipmentTypes();
    htmlTypes = await generateItemTypesHTML(equipment_types);
    typeAppend.innerHTML = "";
    typeAppend.innerHTML = htmlTypes;
    searchTypesFechedFunctionality(equipment_types.information.items , "eq-type-load")
}

async function groupUserSearchControler(){
    await searchTabUserControler(1);
    await searchTabGroupControler(1);
}

async function searchTabbarFunctionality(tab){
    switch(tab.id){
        case 'user-group-search':
            await tabLoadUi(tab , "default" , "tabbar-options" , "selected-search-tab" , 'search_tab/group_user' , groupUserSearchControler)
            break;
        case 'equipment-specific':
            let specific = await tabLoadUi(tab , "default" ,"tabbar-options" , "selected-search-tab" , 'search_tab/equipment_type' , equipmentSearchControler)
            break;
        default:
            break;
    }
}

async function getQueryInputs(){
    let group_id = undefined;
    let user_id = undefined;
    let equipment_type = undefined;
    let default_query = {}
    let specific_query = {}
    let total_query = {}
    query = document.getElementById("search-query-full");
    if(query.children.length === 0){
        return undefined;
    }
    for(let i = 0 ; i < query.children.length ; i++){
        switch(query.children[i].id){
            case "selected-group-query-full":
                group_id = parseInt(query.children[i].children[0].innerText);
                break;
            case "selected-user-query-full":
                user_id = parseInt(query.children[i].children[0].innerText);
                break;
            case "selected-type-query-full":
                equipment_type = query.children[i].children[1].innerText.trim();
                break;
            case "selected-default-query-full":
                for(let j = 0 ; j < query.children[i].children.length ; j++ ){
                    let default_children = query.children[i].children[j];
                    if(default_children.innerText === "")
                        continue;
                    let default_key = default_children.id.replace("default-" , "");
                    default_query[default_key] = default_children.innerText.trim();
                }
                break;
            case "selected-specific-query-full":
                for(let j = 0 ; j < query.children[i].children.length ; j++ ){
                    let specific_children = query.children[i].children[j];
                    if(specific_children.innerText === "")
                        continue;
                    let specific_key = specific_children.id.replace("specific-" , "");
                    specific_query[specific_key] = specific_children.innerText.trim();
                }
                break;
        }
    }
    if(group_id !== undefined){
        total_query["group_id"] = group_id;
    }
    if(user_id !== undefined){
        total_query["user_id"] = user_id;
    }
    if(equipment_type !== undefined){
        total_query["equipment_type"] = equipment_type;
    }
    if(Object.keys(default_query).length !== 0){
        total_query["default"] = default_query;
    }
    if(Object.keys(specific_query).length !== 0){
        total_query["specific"] = specific_query;
    }
    return total_query;
}

async function createSearchButton(){
    document.getElementById("search-button")
    .addEventListener("click" , async function(){
        searchControler(1);
    });
}

async function createWipeButton(){
    document.getElementById("clear-button")
    .addEventListener("click" , async function(){
        document.getElementById("search-query-full").innerHTML = "";
        document.getElementById("query-content").innerHTML = "";
        document.getElementById("results-content").innerHTML = "";
        document.getElementById("search-items-total").innerHTML = ""
        document.getElementById("search-page-controls").innerHTML = ""
        document.getElementById("info-selected").innerHTML = "";
    });
}


async function createSearchButton(){
    document.getElementById("search-button")
    .addEventListener("click" , async function(){
        searchControler(1);
    });
}

async function searchTabFunctionality(){
    defaultSearchTab();
    internalTabSetter("#parameter-tabbar" , searchTabbarFunctionality)
    createSearchButton();
    createWipeButton();
}


