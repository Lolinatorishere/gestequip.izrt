
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

async function setTabHighlight(tab_node){
    let previous_tab = document.getElementById('current-tab');
    await unsetPreviousHighlight(previous_tab , tab_node);
    tab_node.parentNode.id = 'current-tab';
}

async function setTabUI(tab_html_content){
    div = document.getElementById('tab-content');
    div.innerHTML = tab_html_content.html;
}

function createControls(data){
    let html = ''; 
    if(data.current_page > 1){
        //render reversing pages
        let page_render;
        for(let i = 1 ; i <= 6 ; i++){
            if(data.current_page - i > 1){
                page_render--;
            }
        }
        html + `
            <div class="control-arrow" id="control-arrow-backward">
                <
            </div>  
            `
        for(let i = page_render ; i < data.current_page ; i++){
            html + `
            <div id="control-page">
                ${i}
            </div>
            `    
        }
    }
    html += `
    <div id="control-page">
        ${data.current_page}
    </div>
    `;
    if(data.current_page !== data.pages){
        //dont render forwarding pages controls
        for(let i = data.current_page+1 ; i < data.pages ; i++){
            html + `
            <div id="control-page">
                ${i}
            </div>
            `    
        }
        html + `
            <div class="control-arrow" id="control-arrow-foreward">
                >
            </div>  
            `
    }
    return html;
}

function setPageControls(data , append_to){
    let totalDiv = document.createElement('div')
       ,controlDiv = document.createElement('div')
       ,controls = document.createElement('div');
    let info = data.information
    totalDiv.className = 'total';
    totalDiv.innerHTML = `
        Total: ${info.total_items}
    `
    controlDiv.className = 'controls';
    controlDiv.innerHTML = createControls(info);
    controls.appendChild(totalDiv);
    controls.appendChild(controlDiv);
    append_to.innerHTML = controls.innerHTML;
}

function setItems(items , append_to){
    let itemDiv 
       ,item_info = undefined;
    if(items.data !== "success"){
        itemDiv.className = 'info-message';
        itemDiv.innerHTML = `
            no equipment is available
        `;
        return;
    }else{
        console.log(items);
        //equipment type, brand, model, purchase_date, equipment state
        for(let i = 0 ; i < items.information.total_items ; i++){
            let item = items.information.items[i];
            itemDiv = document.createElement('div');
            itemDiv.className = 'item-eq';
            if(i%2 !== 0){
                itemDiv.className = 'item-eq highlight';
            }
            itemDiv.id = 'item' + i;
            itemDiv.innerHTML = `
                <div class="equipment-type">
                    ${item.equipment_type}
                </div>
                <div class="brand">
                    ${item.brand}
                </div>
                <div class="model">
                    ${item.model}
                </div>
                <div class="purchase_date">
                    ${item.purchase_date}
                </div>
                <div class="equipment_status">
                    ${item.equipment_status}
                </div>
            `
            append_to.appendChild(itemDiv);
        }
    }
    return;
}

async function setTabContent(data){
    let controls_html = document.getElementById("items-controls")
       ,items_html = document.getElementById("items-content");
    switch(data.tab){
        case 'yur_eq':
            setPageControls(data , controls_html);
            setItems(data , items_html);
            break;
    }
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

async function tabbarFunctionality(button , tab){
    let request = {
        page: 'equipment'
       ,custom: undefined
    }
    button.addEventListener('click' , async function(){
        setTabHighlight(button);
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
        await setTabContent(tab_information);
    });
}

async function addTabFunctionality(tabs){
    for(let i = 0 ; i < tabs.buttons.length ; i++){
        let button = document.getElementById(tabs.buttons[i]);
        if(button === null || button === undefined)
            continue;
        tabbarFunctionality(button , tabs.tab[i]);
    }
}
