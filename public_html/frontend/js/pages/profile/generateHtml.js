
function generateItemDiv(parent , key , info , i){
    let html = `
                <div id="${parent}-${key}-${i}">
                    ${info}
                </div>
               `
    return html;
}

function setUser(information){
    for(let [key , value] of Object.entries(information)){
        if(key === "id")
            continue;
        document.getElementById(key).innerText = value;
    }
}

//todo get this shit workin
function htmlGroupGenerate(information){
    itemHtml = "";
    for(i = 0 ; i < information.length ; i++){
        internalHTML = "";
        internalHTML += `<div group-item-${i}>`
        for(let [key , value] of Object.entries(information[i])){
        if(key === "group_type" || key === "id")
            continue;
        internalHTML += `
                   <div id="group-${key}-${i}id">
                        ${value}
                   </div>
                   `
        }
        internalHTML += "</div>";
        itemHtml += internalHTML;
    }
    return itemHtml;
}

async function htmlEquipmentGenerate(information){
    let equipment_type = await getEquipmentTypes();
    let equipment_types = equipment_type.information.items;
    itemHtml = "";
    items = information.items;
    for(i = 0 ; i < items.length ; i++){
        console.log(items[i]);
        let eq_type = "";
        let status = "";
        innerHtml = "";
        innerHtml = `<div class="your-equipment">`;
        innerHtml += generateItemDiv("equipment" , "group_name" , items[i].group.group_name , i);
        for(j = 0 ; j < equipment_types.length ; j++){
            if(items[i].equipment.equipment_type === equipment_types[j].id){
                eq_type = equipment_types[j].equipment_type;
                break;
            }
        }
        innerHtml += generateItemDiv("equipment" , "type" , eq_type , i);
        innerHtml += generateItemDiv("equipment" , "brand" , items[i].equipment.brand , i);
        innerHtml += generateItemDiv("equipment" , "purcahse_date" , items[i].equipment.purchase_date , i);
        if(items[i].status === 1){
            status = "Active";
        }else{
            status = "Inactive";
        }
        innerHtml += generateItemDiv("equipment" , "status" , status , i);
        innerHtml += "</div>";
        itemHtml += innerHtml;
    }
    return itemHtml
}


