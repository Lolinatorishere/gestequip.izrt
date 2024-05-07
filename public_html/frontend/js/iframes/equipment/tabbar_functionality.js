
async function setTabHeighlight(tabNode){
    if(document.getElementById('current-tab') !== null){
        document.getElementById('current-tab').id = '';
    }
    tabNode.parentNode.id = 'current-tab';
}

async function setTabUI(tabhtmlcontent){
    div = document.getElementById('tab-content');
    div.innerHTML = tabhtmlcontent.html;
}

async function addTabbarFunctionality(){

    let buttons = ['your_equipment'
                  ,'groups_equipment'
                  ,'all_equipment'
                  ,'search'
                  ,'add_equipment'
                  ,'remove_equipment'
                  ,'logs']

       ,ui_req = [ 'yur_eq'
                  ,'grp_eq'
                  ,'all_eq'
                  ,'sch_eq'
                  ,'add_eq'
                  ,'rem_eq'
                  ,'log_eq'];

        for(let i = 0 ; i < buttons.length ; i++){
            let button = document.getElementById(buttons[i]);
            if(button === null || button === undefined){
                continue;
            }
            button.addEventListener('click' , async function(){
                setTabHeighlight(button);
                fetch_url = 
                '/backend/controlers/equipment/tab_controler.php'
                + '?request="' 
                + ui_req[i] 
                + '"&type="usri"';
                let response = await fetch(fetch_url);
                let  userInterface = await response.json();
                setTabUI(userInterface);
            });   
        }
   }
