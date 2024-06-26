

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
            let title = document.createElement('div')
               ,title_div = document.createElement('div');
            data.title.forEach(element => {
                let HTMLinner = '';
                HTMLinner += `
                            <div id="title-${element}">
                                ${element}
                            <div>
                             `
                title.innerHTML += HTMLinner;
            });
            title.classname = appends[0] + "-title";
            title_div.classname = "title-bar";
            title_div.id = "title-bar";
            title_div.appendChild(title);
            itemsDiv.appendChild(title_div);
        }
        //equipment type, brand, model, purchase_date, equipment state
        for(let i = 0 ; i < info.items.length ; i++){
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

async function setInventoryItems(data , append_items){
    let itemsDiv = document.createElement('div')
    let group_items = data.group_equipments.items;
    let equipment_type = ""
    let highlight = "";
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
        itemsDiv.appendChild(title_div);
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
    append_items.innerHTML = itemsDiv.innerHTML;
}

async function groupTabFunctionality(){
    group_equipments = await getAuthEquipments();
    equipment_types = await getEquipmentTypes();
    append_controls = document.getElementById("items-controls");
    append_items = document.getElementById("items-content");
    append_details = document.getElementById("info-selected");
    appends = [["grp"],["users_name","group_name","equipment_type","brand","model","purchase_date","equipment_status"]];
    custom_data = {
            group_equipments: group_equipments.information
           ,control_location: undefined
           ,appends: appends
           ,title: ["group" , "user" , "type" , "brand" , "model" , "purchase date" , "status"]
           ,equipment_types: equipment_types.information
    };
    setInventoryItems(custom_data , append_items);
}
