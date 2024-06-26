
function calculateUiWidthPercentages(widths , parent_width , padding){
    let total_width = 0
       ,ret = {
            widths: undefined
           ,percentage: undefined
           ,wrapText: "nowrap"
       };
    for(let i = 0 ; i < widths.length ; i++){
        total_width += widths[i];
    }
    parsed_widths = [];
    parsed_percentage = [];
    for(let i = 0 ; i < widths.length ; i++){
        parsed_percentage[i] = parseFloat((widths[i]/total_width));
        parsed_widths[i] = parent_width * parsed_percentage[i];
    }
    total_parsed_widths = 0;
    for(let i = 0 ; i < widths.length ; i++){
        total_parsed_widths += parsed_widths[i] + padding*2;
    }
    ret.widths = parsed_widths;
    ret.percentage = parsed_percentage;
    return ret;
}

function getWidthsForUI(set_items , title , title_properties , individual_item_height , total_children , equipment_type){
    let widths = [];
    for(let i = 0 ; i < set_items.length-title ; i++){
        let width = []
           ,itempos = i+title;
        set_items[itempos].style.height = individual_item_height;
        for(let j = 0 ; j < total_children ; j++){
            item = set_items[itempos].children[j];
            if((item.attributes.class.nodeValue) === "equipment_type"){
                for(let k = 0 ; k < equipment_type.length ; k++){
                    if(parseInt(item.innerText) === equipment_type[k].id){
                        item.innerText = equipment_type[k].equipment_type;
                        break;
                    }
                }
            }
            if((item.attributes.class.nodeValue) === "equipment_status"){
                 for(let k = 0 ; k < 2 ; k++){
                    if(parseInt(item.innerText) === 1){
                        item.innerText = "Active";
                        break;
                    }else{
                        item.innerText = "Inactive";
                    }
                }
            }
            width.push(item.clientWidth);
        }
        widths[i] = width;
    }
    if(title === 1){
        let titles = title_properties.children[0].children
           ,width = [];
        for(let i = 0 ; i < titles.length ; i++){
            width.push(titles[i].clientWidth);
        }
        widths[widths.length] = width;
    }
    return widths;
}

function findMinMaxAttributeFromList(list_input , min_or_max , attribute){
    if(list_input === undefined)
        return "error";
    if(min_or_max === undefined)
        return "error";
    let smallest = undefined
       ,largest = undefined
       ,attribute_int = 0;
    list_input.forEach(input => {
        if(attribute === undefined){
            attribute_int = parseInt(input);
        }else{
            attribute_int = parseInt(input[attribute]);
        }
        if(isNaN(attribute_int) || attribute_int === null)
        return;
        if(smallest === undefined){
            smallest = attribute_int;
        }
        if(largest === undefined){
            largest = attribute_int
        }
        if(largest < attribute_int){
            largest = attribute_int;
        }
        if(smallest > attribute_int){
            smallest = attribute_int;
        }
    });
    switch (min_or_max) {
        case "min":
            return smallest;
        case "max":
            return largest;
        case "both":
            return {small: smallest, large: largest};
        default:
            return "error";
    }
}

function itemsUIparseWidthRows(widths){
    let width_by_row = [];
    for(let i = 0 ; i < widths[0].length ; i++){
        let width_row = [];
        for(let j = 0 ; j < widths.length ; j++){
            width_row.push(widths[j][i]);
        }
    width_by_row[i] = findMinMaxAttributeFromList(width_row , "max");
    }
    return width_by_row;
}


async function setFetchedItemsUI(item_set_id , limit , equipment_type , padding){
    let item_location_height = document.getElementById(item_set_id).clientHeight
       ,item_location_width = document.getElementById(item_set_id).clientWidth
       ,individual_item_height = item_location_height/limit
       ,set_items = document.getElementById(item_set_id).children
       ,title_properties = set_items[0]
       ,title = 0;
    if(title_properties.id === "title-bar"){
        title_properties.style.paddingTop = 3
        title_properties.style.paddingBottom = 3
        individual_item_height = (item_location_height-title_properties.clientHeight)/limit;
        title_properties.children[0].style.display = "flex";
        title_properties.children[0].style.flexDirection = "row";
        let title_content = title_properties.children[0].children;
        title = 1;
    }
    let total_children = set_items[title].children.length
    let widths = getWidthsForUI(set_items , title , title_properties , individual_item_height , total_children , equipment_type);
    individual_widths = itemsUIparseWidthRows(widths);
    parsed_widths = calculateUiWidthPercentages(individual_widths , item_location_width , padding);
    aligned_widths = parsed_widths.widths; 
    aligned_percentage = parsed_widths.percentage; 
    wrap_text = parsed_widths.wrapText;
    for(let i = 0 ; i < set_items.length-title ; i++){
        for(let j = 0 ; j < total_children ; j++){
            item = set_items[i+title].children[j];
            item.style.width = aligned_percentage[j]*100 + "%";
            item.style.textAlign = "";
            item.style.textWrap = wrap_text;
            item.style.marginLeft = "8px";
            item.style.paddingRight = "-8px";
                item.style.fontSize = "0.8rem"
            if(parseInt(j)+parseInt(1) < parseInt(total_children)){
                item.style.borderStyle = "none dotted none none"
                item.style.borderColor = "rgb(170, 170, 170)"
                item.style.borderWidth = "2px"
            }
        }
    }
    if(title === 1){
        let titles = title_properties.children[0].children
        for(let i = 0 ; i < titles.length ; i++){
            titles[i].style.width = aligned_percentage[i]*100 + "%";
            titles[i].style.fontSize = "0.9rem";
            titles[i].style.marginLeft = "8px";
            titles[i].style.paddingRight = "-8px";
        }
    }
}


