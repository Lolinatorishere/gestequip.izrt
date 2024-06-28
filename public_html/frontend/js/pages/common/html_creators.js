
function createButtonHtml(button){
    let button_div = document.createElement('div');
    button_div.className = button.className;
    button_div.id = button.id;
    html = `
        <div  class="title-${button.className}-content">
            ${button.message}
        </div>
             `
    button_div.innerHTML += html;
    return button_div;
}

function createInputHtml(input){
    let input_div = document.createElement('input');
    input_div.className = input.className;
    input_div.id = input.id;
    input_div.type = input.type
    if(input.type === "checkbox"){
        if(input.placeholder === "Active"){
            input_div.value = "on"; 
            input_div.checked = true; 
        }else{
            input_div.value = "off"; 
            input_div.checked = false; 
        }
    }else{
        input_div.placeholder = input.placeholder;
    }
    return input_div;
}

function controlPagesHtml(data){
    let html = ''; 
    control_location = '';
    if(data.control_location !== ""){
        control_location = "-" + data.control_location;
    }
    html += `<div class="control-current-page-title${control_location}">
                <div class="control-current${control_location}-page">
                    Page: ${data.page}
                </div>
            </div>
            `
    html += `<div class="page-controls${control_location}" id="page-controls${control_location}">`
    if(parseInt(data.page) > 1){
        //render reversing pages
        let page_render = 0;
        for(let i = 1 ; i <= 4 ; i++){
            if(data.page - i >= 1){
                page_render--;
            }
        }
        html += `
               <div class="control${control_location}-arrow" id="control-arrow-backward${control_location}" page="${parseInt(data.page)-parseInt(1)}">
                   <
               </div>  
               `
        if(parseInt(data.page) > 5){
        html += `
               <div id="first-page${control_location}" class="page-controls${control_location}-content" page="1">
                    1
               </div> 
               `
            if(parseInt(data.page) > 5){
                html += `
                <div id="dots" class="page-controls${control_location}-content">
                    ...
                </div>
                `
            }
        }
        for(let i = parseInt(data.page)+parseInt(page_render) ; parseInt(i) < parseInt(data.page) ; i++){
            html += `
                   <div id="control-page-${i}${control_location}" page="${i}" class="page-controls${control_location}-content">
                       ${i}
                   </div>
                   `
        }
    }
    html += `
            <div id="control-page-${data.page}${control_location}" page="${data.page}" class="page-controls${control_location}-content current${control_location}-page">
                ${data.page}
            </div>
            `;
    if(parseInt(data.page) < parseInt(data.pages)){
        //dont render forwarding pages controls
        for(let i = parseInt(data.page) + parseInt(1) ; parseInt(i) <= parseInt(data.pages) && parseInt(i) <= parseInt(data.page) + parseInt(4) ; i++){
            html += `
            <div id="control-page-${i}${control_location}" page="${i}" class="page-controls${control_location}-content">
                ${i}
            </div>
            `    
        }
        if(parseInt(data.page) + parseInt(4) < parseInt(data.pages)){
            if(parseInt(data.page)+parseInt(4) < parseInt(data.pages)){
                html += `
                <div id="dots" class="page-controls${control_location}-content">
                    ...
                </div>
                `
            }
            html += `
            <div id="last-page${control_location}" class="page-controls${control_location}-content" page="${data.pages}">
                ${data.pages}
            </div>
            `
        }
        html += `
            <div class="control${control_location}-arrow" id="control-arrow-forward${control_location}" class="page-controls${control_location}-content" page="${parseInt(data.page)+parseInt(1)}">
                >
            </div>  
            `
    }
    html += `</div>`;
    return html;
}

function totalItemsHtml(data){
    html = `
           <div class="total${control_location}-div-content">
                Total: ${data.total_items}
           </div>
           `
    return html;
}

function controlsHtml(data){
    ret = {};
    ret.pageControl = controlPagesHtml(data);
    ret.totalItems = totalItemsHtml(data);
    return ret;
}

function createDetailsHtml(data , div_name){
    let item_div = document.createElement('div');
    let html_div = document.createElement('div');
    let HTMLinner;
    html_div.className = div_name;
    html_div.id = div_name;
    let i = 0;
    for(let [key , value] of Object.entries(data)){
        let key_parse = key.replace("_" , " ");
        html = `
        <div id="${div_name}-content-${i}" class="${div_name}-content-details">
            <div id="title-${key}" class="title-${key}">
                ${key_parse}
            </div>
            <div id="${key}" class="${key}">
                ${value}
            </div>
        </div>
                 `
        i++;
        html_div.innerHTML += html;
        item_div.appendChild(html_div);
    }
    return item_div;
}

function createItemHTML(data , appends , highlight , iteration){
    let item = data;
    let item_div = document.createElement('div');
    let HTMLinner = '';
    item_div.className = 'item-' + appends[0];
    if(highlight === "true"){
        item_div.className = 'item-' + appends[0] + ' highlight';
    }
    item_div.id = 'item-' + appends[0] + "-" + iteration;
    appends[1].forEach(element => {
        if(item !== undefined){
            HTMLinner += `
                        <div class="item-${appends[0]}-div">
                            ${item[element]}
                        </div>
                        `
        }
    })
    item_div.innerHTML = HTMLinner
    return item_div;
}

function createTitleHTML(data){
    if(data.title !== undefined){
        let title = document.createElement('div')
           ,title_div = document.createElement('div');
        data.title.forEach(element => {
            let HTMLinner = '';
            HTMLinner += `
                        <div id="title-${element}" class="${data.appends[0]}-title-div">
                            ${element}
                        <div>
                         `
            title.innerHTML += HTMLinner;
        });
        title.className = data.appends[0] + "-title";
        title_div.className = "title-bar";
        title_div.id = "title-bar";
        title_div.appendChild(title);
        return title_div;
    }else{
        return undefined;
    }
}


