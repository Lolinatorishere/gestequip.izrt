function controlsHtml(data){
    console.log(data);
    let html = ''; 
    control_location = '';
    if(data.control_location !== ""){
        control_location = "-" + data.control_location;
    }
    if(parseInt(data.page) > 1){
        //render reversing pages
        let page_render = 0;
        for(let i = 1 ; i <= 4 ; i++){
            if(data.page - i >= 1){
                page_render--;
            }
        }
        html += `
               <div class="control-arrow" id="control-arrow-backward${control_location}">
                   <
               </div>  
               `
        if(parseInt(data.page) > 5){
        html += `
               <div id="first-page${control_location}">
                    1
               </div> 
               `
            if(parseInt(data.page) > 5){
                html += `
                <div id="dots">
                    ...
                </div>
                `
            }
        }
        for(let i = parseInt(data.page)+parseInt(page_render) ; parseInt(i) < parseInt(data.page) ; i++){
            html += `
                   <div id="control-page-${i}-${control_location}">
                       ${i}
                   </div>
                   `
        }
    }
    html += `
            <div id="control-page-${data.page}${control_location}">
                ${data.page}
            </div>
            `;
    if(data.page !== data.pages){
        //dont render forwarding pages controls
        for(let i = parseInt(data.page) + parseInt(1) ; parseInt(i) <= parseInt(data.pages) && parseInt(i) <= parseInt(data.page) + parseInt(4) ; i++){
            html += `
            <div id="control-page-${i}${control_location}">
                ${i}
            </div>
            `    
        }
        if(parseInt(data.page) + parseInt(4) < parseInt(data.pages)){
            if(parseInt(data.page)+parseInt(4) < parseInt(data.pages)){
                html += `
                <div id="dots">
                    ...
                </div>
                `
            }
            html += `
            <div id="last-page${control_location}">
                ${data.pages}
            </div>
            `
        }
        html += `
            <div class="control-arrow" id="control-arrow-forward${control_location}">
                >
            </div>  
            `
    }
    console.log(html);
    return html;
}


function createItemHTML(data , appends , highlight , iteration){
    console.log(data);
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


