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

function controlsFunctionality(data ){
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
            response = await fetch(await urlCreateBackendRequest(request));
            tab_information = await response.json();
            await setTabContent(tab_information);
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
            response = await fetch(await urlCreateBackendRequest(request));
            tab_information = await response.json();
            await setTabContent(tab_information);
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
            response = await fetch(await urlCreateBackendRequest(request));
            tab_information = await response.json();
            await setTabContent(tab_information);
        });
    });
}

function setControls(data , append_to , ){
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
    controlsFunctionality(data);
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
            console.log(info);
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

function setEqTypes(data) {
    let info = data;
    console.log(info);
    for(let i = 0 ; i < info.total_items ; i++){
        document.getElementById('item-' + i)
        .addEventListener('click' , function(){
            request.custom = {
                tab: data.tab
               ,type: 'data' 
               ,crud: 'read'
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

async function setTabContent(data){
    switch(data.tab){
        case 'yur_eq':
            append_controls = document.getElementById("items-controls");
            append_items = document.getElementById("items-content");
            append_details = document.getElementById("info-selected");
            appends = [["yur"],["equipment_type","brand","model","purchase_date","equipment_status"]];
            if(data.information.error !== undefined){
                message = {controlsclass: "control_error_msg"
                          ,controls: "user has no equipment"
                          ,itemsclass: "items_error_msg" 
                          ,items: "no equipment has been assigned to you"
                          ,detailsclass: "details_error_msg"
                          ,details: "at this current moment there are no items assigned to you"
                          }
                setUserHasNoItems(append_controls , append_items , append_details , message);
                break;
            }
            setControls(data , append_controls);
            setFetchItems(itemsFunctionality , data , appends , append_items , append_details);
            break;
        case'grp_eq':
            append_controls = document.getElementById("items-controls");
            append_items = document.getElementById("items-content");
            append_details = document.getElementById("info-selected");
            appends = [["grp"],["users_name","group_name","equipment_type","brand","model","purchase_date","equipment_status"]];
            if(data.information.error !== undefined){
                message = {controlsclass: "control_error_msg"
                          ,controls: "user has no groups"
                          ,itemsclass: "items_error_msg" 
                          ,items: "no groups or equipment has been assigned to you"
                          ,detailsclass: "details_error_msg"
                          ,details: "at this current moment there are no groups assigned to you"
                          }
                setUserHasNoItems(append_controls , append_items , append_details , message);
                break;
            }
            setControls(data , append_controls);
            setFetchItems(itemsFunctionality , data , appends , append_items , append_details);
            break;
        case'add_eq':
            append_controls = {
                
            }
            group_data = {tab: data.tab
                         ,information: data.information.groups
                         ,data: data.data
                         ,title: ["name" , "status" , "type"]
                         };
            appends = [["group"] , ["group_name","group_status","group_type"]];
            setControls(group_data , document.getElementById("groups-controls"));
            setFetchItems(itemsFunctionality , group_data , appends , document.getElementById("groups-items") , document.getElementById("selected-group"));
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
