
async function setTabUI(tab_html_content){
    div = document.getElementById('tab-content');
    div.innerHTML = tab_html_content;
}

function internalTabSetter(tab_id , Functionality){
    tab_row = document.querySelector(tab_id).children;
    for(let i = 0 ; i < tab_row.length ; i++){
        if(tab_row[i].children.length === 0)
            continue;
        if(tab_row[i].children === undefined)
            continue;
        tab_row[i].children[0].style.paddingLeft = 1 + 'rem';
        tab_row[i].children[0].style.paddingRight = 1 + 'rem';
        Functionality(tab_row[i].children[0]);
    }
}

function setTabHighlight(tab_node , tab_id){
    let previous_tab = document.getElementById(tab_id);
    unsetPreviousHighlight(previous_tab , tab_node);
    tab_node.parentNode.id = tab_id;
}

function unsetPreviousHighlight(previous_tab , tab_node){
    let first_tab = 1;
    if(previous_tab === null) 
        return;
    previous_tab.id = '';
    if(tab_node.id === "your_equipment")
        return;
}

function onTabLoad(tab){
    switch(tab){
        case "grp_eq":
            if(typeof groupTabFunctionality === "function"){
                groupTabFunctionality();
            }else{
                console.log("nuh uh Tab");
            }
            break;
        case "sch_eq":
            if(typeof searchTabFunctionality === "function"){
                searchTabFunctionality();
            }else{
                console.log("nuh uh Tab");
            }
            break;

            break;
        case "all_eq":
            break;
        case "add_eq":
            break;
        case "adeqty":
            break;
        case "rem_eq":
            break;
        case "log_eq":
            break;
        default:
            console.log("Invalid Tab");
            break;
    }
}

function tabFunctionality(tabs){
    let default_button = document.getElementById(tabs.buttons[0])
    let default_tab = tabs.tab[0];
    for(let i = 0 ; i < tabs.buttons.length ; i++){
        let button = document.getElementById(tabs.buttons[i]);
        if(button === null || button === undefined)
            continue;
        tabbarFunctionality(button , tabs.tab[i] , tabs.buttons[i]);
    }
    setTab(default_button , default_tab);
}

function tabbarFunctionality(button_element , tab ){
    button_element.addEventListener('click' , function(){
        setTab(button_element , tab);
    });
}

async function setTabHTML(tab , callback){
    await fetch('/frontend/html/pages/equipment/tabs/' + tab +'.html')
        .then(function(response){
            return response.text()
        })
        .then(function(html){
            document.getElementById("tab-content").innerHTML = html;
        }); 
    if(typeof callback === 'function'){
        callback(tab);
    }
}

function setTab(button_element , tab){
    setTabHighlight(button_element , 'current-tab');
    setTabHTML(tab , onTabLoad);
}

function onloadTabsFunctions(){
    let tabs = {
            buttons:['groups_equipment'
                    ,'search'
                    ,'all_equipment'
                    ,'add_equipment'
                    ,'add_equipment_type'
                    ,'remove_equipment'
                    ,'logs'],
            tab:['grp_eq'
                ,'sch_eq'
                ,'all_eq'
                ,'add_eq'
                ,'adeqty'
                ,'rem_eq'
                ,'log_eq']
       }
    window.addEventListener("load" , function(){
        tabFunctionality(tabs);
    });
}


