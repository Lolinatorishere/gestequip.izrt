async function isFirstTab(string){
    let parser = new DOMParser()
       ,doc = parser.parseFromString(string , 'text/html')
       ,id = doc.querySelector('#your_equipment');
    if(id === null){
        return 1;
    }else{
        return 0
    }
}

async function setTabHighlight(tab_node){
    let previous_tab = document.getElementById('current-tab');
    await unsetPreviousHighlight(previous_tab , tab_node);
    tab_node.parentNode.id = 'current-tab';
}

async function setTabUI(tab_html_content){
    div = document.getElementById('tab-content');
    div.innerHTML = tab_html_content.html;
}

async function urlCreateBackendRequest(request){
    let page_controler = request.page + '/' + request.page +'_controler.php?'
       ,i = 0;
    url = '/backend/controlers/' 
        + page_controler; 
    for(let [key , value] of Object.entries(request.custom)){
        if(i !== 0){
            url += '&';
        }
        url += key + '=' + value;
        i++;
    }
    return url;
} 

async function unsetPreviousHighlight(previous_tab , tab_node){
    let first_tab = 1;
    if(previous_tab === null) 
        return;
    previous_tab.id = '';
    if(tab_node.id === "your_equipment")
        return;
    first_tab = await isFirstTab(previous_tab.innerHTML);
    if(first_tab === 1) 
        return;
    previous_tab.id = 'first-tab';
}

function setUserHasNoItems(append_controls , append_items , append_details , message){
    append_controls.innerHTML = `
                                <div class="${message.controlsclass}">
                                    User has no ${message.controls}
                                </div>
                                `
    append_items.innerHTML =    `
                                <div class="${message.itemsclass}">
                                    ${message.items}
                                </div>
                                `
    append_details.innerHTML =  `
                                <div class="${message.detailsclass}">
                                    ${message.details}
                                </div>
                                `
}

function controlsHtml(data){
    let html = ''; 
    control_location = '';
    if(data.control_location !== ""){
        control_location = "-" + data.control_location;
    }
    if(data.current_page > 1){
        //render reversing pages
        let page_render;
        for(let i = 1 ; i <= 6 ; i++){
            if(data.current_page - i >= 1){
                page_render--;
            }
        }
        html + `
               <div class="control-arrow" id="control-arrow-backward${control_location}">
                   <
               </div>  
               `
        if(data.current_page >= 7){
        html + `
               <div id="first-page${control_location}">
                    1
               </div> 
               `
            if(data.current_page > 7){
                html + `
                <div id="dots">
                    ...
                </div>
                `
            }
        }
        for(let i = page_render ; i < data.current_page ; i++){
            html + `
                   <div id="control-page-${i}${control_location}">
                       ${i}
                   </div>
                   `
        }
    }
    html += `
            <div id="control-page-${data.current_page}${control_location}">
                ${data.current_page}
            </div>
            `;
    if(data.current_page !== data.pages){
        //dont render forwarding pages controls
        for(let i = data.current_page+1 ; i < data.pages ; i++){
            html + `
            <div id="control-page-${i}${control_location}">
                ${i}
            </div>
            `    
        }
        if(data.current_page+6 < data.pages){
            if(data.current_page+6 < data.pages-1){
                html + `
                <div id="dots">
                    ...
                </div>
                `
            }
            html + `
            <div id="last-page${control_location}">
                ${data.pages}
            </div>
            `
        }
        html + `
            <div class="control-arrow" id="control-arrow-forward${control_location}">
                >
            </div>  
            `
    }
    return html;
}

function controlsFunctionality(data , refresh , loadingFunction){
    let current_page = parseInt(data.information.current_page)
       ,total_items = parseInt(data.information.total_items)
       ,page_max = parseInt(data.information.pages)
       ,page_controls = []
       ,arrow_backward
       ,arrow_forward
       ,control_location = '';
    if(data.control_location !== undefined){
        control_location = "-" + data.control_location;
    }
    let request = {
            page: 'equipment'
           ,custom: undefined
        }
    if(current_page !== 1){
        element_arrow = 'control-arrow-backward' + control_location;
        document.getElementById(element_arrow)
        .addEventListener('click' , async function(){
            // fetch first time tab info
            let tab_information = undefined;
            request.custom = {
                tab: data.tab
               ,type: 'data' 
               ,crud: 'read'
               ,page: current_page-1
               ,t_i: total_items
            }
            if(refresh !== undefined){
                request.custom.rfsh = refresh[0];
                request.custom.rgin = refresh[1];
            }
            response = await fetch(await urlCreateBackendRequest(request));
            if(response !== undefined){
                tab_information = await response.json();
            }
            if(tab_information !== undefined){
                tab_information.control_location = control_location;
                await loadingFunction(tab_information);
            }
                // TODO: got to create the loading of an error response
        });
        for(let i = 1 ; i <= 6 ; i++){
            page_control = 'control-page-' + i + control_location;
            if(current_page - i >= 1){
                page_controls.push(document.getElementById(page_control));
            }
        }
        if(current_page > 6){
            page_control = 'first-page' + control_location;
            page_controls.push(document.getElementById(page_control));
        }
    }
    control_page = 'control-page-' + current_page + control_location
    page_controls.push(document.getElementById(control_page));
    if(current_page !== page_max){
        element_arrow = 'control-arrow-forward' + control_location;
        document.getElementById(element_arrow)
        .addEventListener('click' , async function(){
            // fetch first time tab info
            let tab_information = undefined;
            request.custom = {
                tab: data.tab
               ,type: 'data' 
               ,crud: 'read'
               ,page: current_page+1
               ,t_i: total_items
            }   
            if(refresh !== undefined){
                request.custom.rfsh = refresh[0];
                request.custom.rgin = refresh[1];
            }
            response = await fetch(await urlCreateBackendRequest(request));
            if(response !== undefined){
                tab_information = await response.json();
            }
            if(tab_information !== undefined){
                tab_information.control_location = control_location;
                await loadingFunction(tab_information);
            }
                // TODO: got to create the loading of an error response
        });
        for(let i = 1 ; i <= 6 ; i++){
            control_page = 'control-page-' + i + control_location;
            if(current_page + i <= page_max){
                page_controls.push(document.getElementById(control_page));
            }
        }
        if(current_page+6 < page_max){
            last_page = 'last-page' + control_location;
            page_controls.push(document.getElementById(last_page));
        }
    }
    page_controls.forEach(page => {
        page.addEventListener('click' , async function(){
            // fetch first time tab info
            let tab_information = undefined;
            request.custom = {
                tab: data.tab
               ,type: 'data' 
               ,crud: 'read'
               ,page: page.innerHTML.trim()
               ,t_i: total_items
            }   
            if(refresh !== undefined){
                request.custom.rfsh = refresh[0];
                request.custom.rgin = refresh[1];
            }
            response = await fetch(await urlCreateBackendRequest(request));
            if(response !== undefined){
                tab_information = await response.json();
            }
            if(tab_information !== undefined){
                tab_information.control_location = control_location;
                await loadingFunction(tab_information);
            }
                // TODO: got to create the loading of an error response
        });
    });
}

function setControls(data , append_to , refresh , loadingFunction){
    let totalDiv = document.createElement('div')
       ,controlDiv = document.createElement('div')
       ,controls = document.createElement('div')
       ,info = data.information
       ,control_location = '';
    if(data.control_location !== undefined){
        control_location = data.control_location;
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

function itemsHtml(data , appends){
    let itemsDiv = document.createElement('div')
       ,info = data.information;
    if(data.data !== "success"){
        itemsDiv.className = 'info-message';
        itemsDiv.innerHTML = `
            no information is available
        `;
        return itemsDiv;
    }else{
        if(data.title !== undefined){
            let title = document.createElement('div');
            data.title.forEach(element => {
                let HTMLinner = '';
                HTMLinner += `
                            <div id="title-${element}">
                                ${element}
                            <div>
                             `
                title.innerHTML += HTMLinner;
            });
            itemsDiv.appendChild(title);
        }
        //equipment type, brand, model, purchase_date, equipment state
        for(let i = 0 ; i < info.total_items ; i++){
            let item = info.items[i];
            let items = document.createElement('div');
            let HTMLinner = '';
            items.className = 'item-' + appends[0];
            if(i%2 !== 0){
                items.className = 'item-' + appends[0] + ' highlight';
            }
            items.id = 'item-' + appends[0] + "-" + i;
            appends[1].forEach(element => {
                if(item !== undefined){
                    HTMLinner += `
                                <div class="${element}">
                                    ${item[element]}
                                </div>
                                `
                }
            })
            items.innerHTML = HTMLinner
            itemsDiv.appendChild(items);
        }
    }
    return itemsDiv;
}

function itemDetailsHtml(info){
    let html = ``;
        Object.keys(info).forEach(key => {
            let value = info[key];
            html +=`
            <div id="${key}">
                ${value}
            </div>
            ` 
        });
    return html;
}

function itemsFunctionality(data , append_details , appends){
    let info = data.information; 
    for(let i = 0 ; i < info.total_items ; i++){
        elementid = 'item-' + appends[0] + '-' + i;
        document.getElementById(elementid)
        .addEventListener('click' ,  function(){
            append_details.innerHTML = itemDetailsHtml(data.information.items[i]);
        });
    }
}

async function addGroupsFunctionality(data , append_details , appends){
    let info = data.information
       ,request = {}
    for(let i = 0 ; i < info.total_items ; i++){
       let fetch_request = undefined
          ,group_users = undefined;
        elementid = 'item-' + appends[0] + '-' + i;
        document.getElementById(elementid)
        .addEventListener('click' , async function(){
            defaultHTML = `
                          <div class="unselected-user">
                              Group User Not Selected
                          </div>
                          `;
            document.getElementById("selected-user").innerHTML = defaultHTML
                
            request[i] = {
                page: "equipment"
               ,custom: {
                     tab: data.tab
                    ,type: 'data' 
                    ,crud: 'read'
                    ,rfsh: 'grp_usrs'
                    ,rgin: info.items[i].id
                    }
                }
            custom_details = {
                    selected_group_id: info.items[i].id
                   ,selected_group_name: info.items[i].group_name
                   ,selected_group_type: info.items[i].group_type
                   ,selected_group_status: info.items[i].group_status
                }
            append_details.attributes.class.nodeValue = "neutral-input"
            append_details.innerHTML = itemDetailsHtml(custom_details);
            fetch_request = await fetch(await urlCreateBackendRequest(request[i]));
            if(fetch_request !== undefined)
                group_users = await fetch_request.json();
            if(group_users !== undefined){
                addEqUsersControlFunctionality(group_users , info.items[i].id)
            }
        });
    }
}

async function addUsersFunctionality(data , append_details , appends){
    let info = data.information
    for(let i = 0 ; i < info.total_items ; i++){
       let fetch_request = undefined
          ,group_users = undefined;
        elementid = 'item-' + appends[0] + '-' + i;
        document.getElementById(elementid)
        .addEventListener('click' , async function(){
            custom_details = {
                    selected_user_id: info.items[i].id
                   ,selected_user_name: info.items[i].users_name
                   ,selected_user_email: info.items[i].email
                   ,selected_user_phone_number: info.items[i].phone_number
                   ,selected_user_regional_indicator: info.items[i].regional_indicator
                }
            append_details.attributes.class.nodeValue = "neutral-input"
            append_details.innerHTML = itemDetailsHtml(custom_details);
        });
    }
}

function setFetchItems(Functionality , data , appends , append_to , append_details){
    append_to.innerHTML = itemsHtml(data , appends).innerHTML;;
    if(append_details !== null || append_details !== undefined){
        Functionality(data , append_details , appends);
    }
}

function setFetchTypes(data , append_to){
    let info = data.items
       ,totalHTML = "";
    info.forEach(element => {
        type = element.Type.match(/(\w+)/);
        max_input = element.Type.match(/(\d+)/);
        max_input_html = "";
        label = element.Field.replace(/_/g, ' ');
        switch(type[0]){
            case "tinyint":
                input_specific = `
                                 <div class="label" id="${data.input_area_id}label">
                                    ${label}
                                 </div>
                                 <input id="${data.input_area_id}input" class="neutral-input" type="checkbox">
                                 `
                break;
            default:
                if(max_input !== null)
                    max_input_html = "maxlength=\"" + max_input[0] + "\"";
                input_specific = `
                                    <div class="label" id="${data.input_area_id}label">
                                        ${label}
                                    </div>
                                    <input class="neutral-input" id="${data.input_area_id}input" placeholder="${label}" type="text" ${max_input_html} >
                                 `
                break;
        }
        HTMLinner = `
                    <div class="${data.input_class}" 
                         id="${data.input_id}"
                         input_field="${element.Field}"
                         is_nullable="${element.Null}"
                         input_type="${type[0]}"
                         error="NO"
                    >
                        ${input_specific}
                    </div>
                    `
        totalHTML += HTMLinner;
    });
    append_to.innerHTML = totalHTML;
}
function minClientWidthSet(list_of_inputs , list_of_labels){
    let smallestInput = undefined
       ,largestLabel = undefined
       ,padding = 10;
    list_of_inputs.forEach(input => {
        let clientWidth = parseInt(input.clientWidth);
        if(isNaN(clientWidth) || clientWidth === null)
            return;
        if(smallestInput === undefined){
            smallestInput = clientWidth;
            return;
        }
        if(smallestInput > clientWidth){
            smallestInput = parseInt(input.clientWidth);
        }
    });
    list_of_labels.forEach(label =>{
        let clientWidth = parseInt(label.clientWidth);
        if(isNaN(clientWidth) || clientWidth === null)
            return; 
        if(largestLabel === undefined){
            largestLabel = clientWidth;
            return; 
        }
        if(largestLabel < clientWidth){
            largestLabel = parseInt(label.clientWidth);
        }
    });
    list_of_inputs.forEach(input => {
        input.style.width = smallestInput - padding;
    });
    list_of_labels.forEach(label => {
        label.style.width = largestLabel + padding;
    });
}

async function setEqTypes(data){
    let info = data.items
       ,append_dropdown = document.getElementById("type-dropdown")
       ,request = {}
       ,append_to = document.getElementById("type-load")
       ,dropdown_html = "";
    info.forEach(element => {
        HTMLinner = `
                    <div id="eq-${element.equipment_type}" class="equipment-type">
                    ${element.equipment_type}
                    </div>
                    `
         dropdown_html += HTMLinner; 
    });
    append_dropdown.innerHTML = dropdown_html;
    info.forEach(element => {
        request[element.equipment_type] = {
            page: 'equipment'
            ,custom:{
                tab: data.tab
                ,type: 'data' 
                ,crud: 'read'
                ,rfsh: 'eq_tables'
                ,rgin: element.equipment_type
            }
        };
    });
    info.forEach(element => {
        document.getElementById("eq-" + element.equipment_type)
            .addEventListener('click' , async function(){
                selected = document.getElementById("selected-eq-type");
                selected.innerHTML = `
                                     <div class="selected-class-label">
                                         selected equipment: 
                                     </div>
                                     <div class="selected-equipment-type">
                                         ${element.equipment_type}
                                     </div>
                                     `
                selected.attributes.class.nodeValue = "selected-eq-type";
                response = await fetch(await urlCreateBackendRequest(request[element.equipment_type]));
                if(response !== undefined){
                    type_information = await response.json();
                    inputs = type_information.information.types_specific;
                    inputs.input_class = "specific_input";
                    inputs.input_id = "equipment_specific";
                    inputs.input_area_id = "spec_";
                    setFetchTypes(inputs , append_to);
                    minClientWidthSet(document.querySelectorAll('#spec_input') , document.querySelectorAll('#spec_label'));
                }
            });
    });
}

async function setEqDefault(data){
    let apend_to = document.getElementById("add-inputs");
    data.input_class = "default_input";
    data.input_id = "equipment_default";
    data.input_area_id = "def_";
    setFetchTypes(data , apend_to);
    minClientWidthSet(document.querySelectorAll('#def_input') , document.querySelectorAll('#def_label'));
}

function selectedInput(input_info){
    selected = document.getElementById("selected-group");
    if(selected.innerText === "Group Not Selected"){
        if(input_info.error === undefined){
            input_info.error = {};
        }
        input_info.error["group_select"] = "false";
        selected.attributes.class.nodeValue = "error-input-select";
    }else{
        selected.attributes.class.nodeValue = "neutral-input";
        input_info.data.selected_group = {
            group_id: document.getElementById("selected_group_id").innerText
        }
    }
    selected = document.getElementById("selected-user");
    if(selected.innerText === "Group User Not Selected"){
        selected.attributes.class.nodeValue = "error-input-select";
        if(input_info.error === undefined){
            input_info.error = {};
        }
        input_info.error["user_select"] = "false";
    }else{
        selected.attributes.class.nodeValue = "neutral-input";
        input_info.data.selected_user = {
            user_id: document.getElementById("selected_user_id").innerText
        }
    }
}

function inputNullUi(input , label , input_area , input_info){
    if(input.attributes.is_nullable.nodeValue === "NO"){
        let widthSize = parseInt(label.style.width);
        if(input_area.value === ''){
            input.attributes.class.nodeValue = "error-input-text";
            if(input.attributes.error.nodeValue === "NO"){
                label.style.width = widthSize-4;
            }
            input.attributes.error.nodeValue = "YES"
            if(input_info.error === undefined){
                input_info.error = {};
            }
            input_info.error[input.attributes.input_field.nodeValue] = "missing input";
            return;
        }
        if(input.attributes.error.nodeValue === "NO")
            return;
        input.attributes.class.nodeValue = "default_input";
        input.attributes.error.nodeValue = "NO"
        label.style.width = widthSize+4;
    }
}

function defaultTextInput(input_info){
    inputs = document.querySelectorAll("#equipment_default");
    let default_info = {};
    if(inputs.length === 0){
        document.getElementById("add-inputs").innerHTML = "Default Inputs Have not Loaded";
        if(input_info.error === undefined){
            input_info.error = {};
        }
        input_info.error["default_load"] = "false";
        return;
    }
    inputs.forEach(input => {
        let input_area = input.children.def_input
           ,label = input.children.def_label;
        //checks if the user input can or not be null
        inputNullUi(input , label , input_area , input_info);
        if(input.children.def_input.attributes.type.nodeValue === "checkbox"){
            default_info[input.attributes.input_field.nodeValue] = input.children.def_input.checked;
            return;
        }
        default_info[input.attributes.input_field.nodeValue] = input.children.def_input.value;
    });
    input_info.data.default = default_info;
}

function specificTextInput(input_info){
    inputs = document.querySelectorAll("#equipment_specific");
    eq_type_drpdwn = document.getElementById("type-dropdown");
    eq_drpdwn_text_default = eq_type_drpdwn.attributes.default_text;
    let specific_info = {}
    if(inputs.length === 0){
        document.getElementById("selected-eq-type").innerHTML = "please select Equipment";
        document.getElementById("selected-eq-type").attributes.class.nodeValue = "error-input-text" ;
        if(input_info.error === undefined){
            input_info.error = {};
        }
        input_info.error["specific_load"] = "false";
        return;
    }
    inputs.forEach(input => {
        let input_area = input.children.spec_input
           ,label = input.children.spec_label;
        inputNullUi(input , label , input_area , input_info);
        if(input.children.spec_input.attributes.type.nodeValue === "checkbox"){
            specific_info[input.attributes.input_field.nodeValue] = input.children.spec_input.checked;
            return;
        }
        specific_info[input.attributes.input_field.nodeValue] = input.children.spec_input.value;
    });
    input_info.data.specific = specific_info;
}

function setAddButtons(buttons){
    buttons.add_eq
        .addEventListener('click' , async function(){
            let input_info = {
                    data: {}
                }
               ,server_response = document.getElementById("server-response");
            selectedInput(input_info);
            defaultTextInput(input_info);
            specificTextInput(input_info);

            if(input_info.error !== undefined){
                console.log(input_info.error);
                server_response.innerHTML = `
                                            <div class="server-response-content error-input-text-background">
                                                Missing Inputs
                                            </div>
                                            `
                server_response.attributes.class.nodeValue = "error-input-text";
            }else{
                server_response.innerHTML = ``;
                server_response.attributes.class.nodeValue = "server-response";
                console.log(input_info);
            }
        }
    );
}

async function addEqGroupControlFunctionality(data){
    apnd_controls = document.getElementById("groups-controls");
    apnd_items = document.getElementById("groups-items");
    apnd_details = document.getElementById("selected-group");
    apnds = [["group"] , ["group_name","group_status","group_type"]];
    refresh = ["groups" , "none"];
    custom_data = {
        tab: data.tab
       ,information: data.information.groups
       ,data: data.data
       ,title: ["name" , "status" , "type"]
       ,control_location: "groups"
    }
    setControls(custom_data , apnd_controls , refresh , addEqGroupControlFunctionality);
    setFetchItems(addGroupsFunctionality , custom_data , apnds , apnd_items , apnd_details);
}

async function addEqUsersControlFunctionality(data , group_id){
    apnd_controls = document.getElementById("users-controls");
    apnd_items = document.getElementById("users-items");
    apnd_details = document.getElementById("selected-user");
    apnds = [["user"] , ["users_name","email","phone_number","regional_indicator"]];
    refresh = ["grp_usrs" , document.getElementById("selected_group_id").innerText];
    custom_data ={
        tab: data.tab
       ,information: data.information.users
       ,data: data.data
       ,title: ["name","email","phone_number","regional_indicator"]
       ,control_location: "users"
    }
    setControls(custom_data , apnd_controls , refresh , addEqUsersControlFunctionality);
    setFetchItems(addUsersFunctionality , custom_data , apnds , apnd_items , apnd_details);
}

async function setTabContent(data){
    empty = [];
    switch(data.tab){
        case 'yur_eq':
            append_controls = document.getElementById("items-controls");
            append_items = document.getElementById("items-content");
            append_details = document.getElementById("info-selected");
            appends = [["yur"],["equipment_type","brand","model","purchase_date","equipment_status"]];
            if(data.information.error !== undefined){
                message = {
                    controlsclass: "control_error_msg"
                   ,controls: "user has no equipment"
                   ,itemsclass: "items_error_msg" 
                   ,items: "no equipment has been assigned to you"
                   ,detailsclass: "details_error_msg"
                   ,details: "at this current moment there are no items assigned to you"
                }
                setUserHasNoItems(append_controls , append_items , append_details , message);
                break;
            }
            setControls(data , append_controls , empty , setTabContent);
            setFetchItems(itemsFunctionality , data , appends , append_items , append_details);
            break;
        case'grp_eq':
            append_controls = document.getElementById("items-controls");
            append_items = document.getElementById("items-content");
            append_details = document.getElementById("info-selected");
            appends = [["grp"],["users_name","group_name","equipment_type","brand","model","purchase_date","equipment_status"]];
            if(data.information.error !== undefined){
                message = {
                    controlsclass: "control_error_msg"
                   ,controls: "user has no groups"
                   ,itemsclass: "items_error_msg" 
                   ,items: "no groups or equipment has been assigned to you"
                   ,detailsclass: "details_error_msg"
                   ,details: "at this current moment there are no groups assigned to you"
                }
                setUserHasNoItems(append_controls , append_items , append_details , message);
                break;
            }
            setControls(data , append_controls , empty , setTabContent);
            setFetchItems(itemsFunctionality , data , appends , append_items , append_details);
            break;
        case'add_eq':
            apnd_controls = document.getElementById("groups-controls");
            apnd_items = document.getElementById("groups-items");
            apnd_details = document.getElementById("selected-group");
            buttons = {
                add_eq: document.getElementById("create-new-equipment")
               ,clear_info: document.getElementById("clear-website-info")
            }
            apnds = [["group"] , ["group_name","group_status","group_type"]];
            refresh = ["groups" , "none"];
            custom_data = {
                group: { 
                    tab: data.tab
                   ,information: data.information.groups
                   ,data: data.data
                   ,title: ["name" , "status" , "type"]
                   ,control_location: "groups"
                }
               ,types:{
                    tab: data.tab
                   ,items: data.information.types.items
                }
               ,default_eq:{
                    tab:data.tab 
                   ,items: data.information.default.items
                }
            };
            setControls(custom_data.group , apnd_controls , refresh , addEqGroupControlFunctionality);
            setFetchItems(addGroupsFunctionality , custom_data.group , apnds , apnd_items , apnd_details);
            setEqDefault(custom_data.default_eq);
            setEqTypes(custom_data.types);
            setAddButtons(buttons);
            break;
        default:
            break;
    }
}

async function tabbarFunctionality(button , tab){
    let request = {
        page: 'equipment'
       ,custom: undefined
    }
    button.addEventListener('click' , async function(){
        setTabHighlight(button);
        tab_css = document.getElementById("tab-css");
        tab_css.href = '/frontend/css/iframes/equipment/tabs/' + tab +'.css';
        // fetches the correct tab ui on click
        request.custom = {
            tab: tab
           ,type: 'usri'
        }
        let response = await fetch(await urlCreateBackendRequest(request));
        let userInterface = await response.json();
        await setTabUI(userInterface);
        // fetch first time tab info
        request.custom = {
            tab: tab
           ,type: 'data' 
           ,crud: 'read'
        }
        response = await fetch(await urlCreateBackendRequest(request));
        tab_information = await response.json();
        console.log(tab_information);
        await setTabContent(tab_information);
    });
}

async function tabFunctionality(tabs){
    for(let i = 0 ; i < tabs.buttons.length ; i++){
        let button = document.getElementById(tabs.buttons[i]);
        if(button === null || button === undefined)
            continue;
        tabbarFunctionality(button , tabs.tab[i]);
    }
}
