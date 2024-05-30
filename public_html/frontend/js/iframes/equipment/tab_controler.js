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
    if(data.current_page > 1){
        //render reversing pages
        let page_render;
        for(let i = 1 ; i <= 6 ; i++){
            if(data.current_page - i >= 1){
                page_render--;
            }
        }
        html + `
            <div class="control-arrow" id="control-arrow-backward">
                <
            </div>  
            `
        if(data.current_page >= 7){
        html + `
            <div id="first-page">
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
            <div id="control-page-${i}">
                ${i}
            </div>
            `    
        }
    }
    html += `
    <div id="control-page-${data.current_page}">
        ${data.current_page}
    </div>
    `;
    if(data.current_page !== data.pages){
        //dont render forwarding pages controls
        for(let i = data.current_page+1 ; i < data.pages ; i++){
            html + `
            <div id="control-page-${i}">
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
            <div id="last-page">
                ${data.pages}
            </div>
            `
        }
        html + `
            <div class="control-arrow" id="control-arrow-forward">
                >
            </div>  
            `
    }
    return html;
}

function controlsFunctionality(data , refresh , loadingFunction){
    console.log(data);
    console.log(data.information.current_page);
    let current_page = parseInt(data.information.current_page)
       ,total_items = parseInt(data.information.total_items)
       ,page_max = parseInt(data.information.pages)
       ,page_controls = []
       ,arrow_backward
       ,arrow_forward;
    let request = {
            page: 'equipment'
           ,custom: undefined
        }
    console.log(current_page);
    if(current_page !== 1){
        document.getElementById('control-arrow-backward')
        .addEventListener('click' , async function(){
            // fetch first time tab info
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
            tab_information = await response.json();
            await loadingFunction(tab_information);
        });
        for(let i = 1 ; i <= 6 ; i++){
            if(current_page - i >= 1){
                page_controls.push(document.getElementById('control-page-' + i));
            }
        }
        if(current_page > 6){
            page_controls.push(document.getElementById('first-page'));
        }
    }
    page_controls.push(document.getElementById('control-page-' + current_page));
    if(current_page !== page_max){
        document.getElementById('control-arrow-forward')
        .addEventListener('click' , async function(){
            // fetch first time tab info
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
            tab_information = await response.json();
            await loadingFunction(tab_information);
        });
        for(let i = 1 ; i <= 6 ; i++){
            if(current_page + i <= page_max){
                page_controls.push(document.getElementById('control-page-' + i));
            }
        }
        if(current_page+6 < page_max){
            page_controls.push(document.getElementById('last-page'));
        }
    }
    page_controls.forEach(page => {
        page.addEventListener('click' , async function(){
            // fetch first time tab info
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
            tab_information = await response.json();
            await loadingFunction(tab_information);
        });
    });
}

function setControls(data , append_to , refresh , loadingFunction){
    let totalDiv = document.createElement('div')
       ,controlDiv = document.createElement('div')
       ,controls = document.createElement('div');
    let info = data.information;
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
            <div id=${key}>
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
            append_details.innerHTML = itemDetailsHtml(info.items[i]);
            fetch_request = await fetch(await urlCreateBackendRequest(request[i]));
            if(fetch_request !== undefined)
                group_users = await fetch_request.json();
            if(group_users !== undefined){
                console.log(group_users);
            }
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
        switch (type) {
            case "tinyint":
                input_specific = `
                                 <div class="input-area">
                                     <div class="label">
                                         ${element.Field}
                                     </div>
                                     <input type="checkbox">
                                 </div>
                                 `
                break;
            default:
                if(max_input !== null)
                    max_input_html = "maxlength=\"" + max_input[0] + "\"";
                input_specific = `
                                 <div class="input-area">
                                     <input id="input" placeholder="${element.Field}" type="text" ${max_input_html} >
                                 </div>
                                 `
                break;
        }
        HTMLinner = `
                    <div class="specific-input" 
                         id="specific-input"
                         input_field="${element.Field}"
                         is_nullable="${element.Null}"
                         input_type="${type[0]}"
                    >
                        ${input_specific}
                    </div>
                    `
        totalHTML += HTMLinner;
    });
    append_to.innerHTML = totalHTML;
}

async function setEqTypes(data){
    let info = data.items
       ,append_dropdown = document.getElementById("type-dropdown")
       ,request = {}
       ,append_to = document.getElementById("type-load")
       ,dropdown_html = "";
    info.forEach(element => {
        HTMLinner = `
                    <div id="eq-${element.equipment_type}">
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
                response = await fetch(await urlCreateBackendRequest(request[element.equipment_type]));
                if(response !== undefined){
                    type_information = await response.json();
                    setFetchTypes(type_information.information.types_specific , append_to);
                }
            });
    });
}

async function addEqGroupControlFunctionality(data){
    apnd_controls = document.getElementById("groups-controls");
    apnd_items = document.getElementById("groups-items");
    apnd_details = document.getElementById("selected-group");
    apnds = [["group"] , ["group_name","group_status","group_type"]];
    refresh = ["groups" , "none"];
    custom_data ={
        tab: data.tab
       ,information: data.information.groups
       ,data: data.data
       ,title: ["name" , "status" , "type"]
    }
    setControls(custom_data , apnd_controls , refresh , addEqGroupControlFunctionality);
    setFetchItems(addGroupsFunctionality , custom_data , apnds , apnd_items , apnd_details);
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
            apnds = [["group"] , ["group_name","group_status","group_type"]];
            refresh = ["groups" , "none"];
            custom_data = {
                group: { 
                    tab: data.tab
                   ,information: data.information.groups
                   ,data: data.data
                   ,title: ["name" , "status" , "type"]
                }
               ,types:{
                    tab: data.tab
                   ,items: data.information.types.items
                }
            };
            setControls(custom_data.group , apnd_controls , refresh , addEqGroupControlFunctionality);
            setFetchItems(addGroupsFunctionality , custom_data.group , apnds , apnd_items , apnd_details);
            setEqTypes(custom_data.types);
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
