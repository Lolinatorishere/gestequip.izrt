
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

    if(tab_node.id === "your_equipment"){
        return;
    }

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

async function addTabbarFunctionality(){

    let buttons = ['your_equipment'
                  ,'groups_equipment'
                  ,'search'
                  ,'all_equipment'
                  ,'add_equipment'
                  ,'remove_equipment'
                  ,'logs']

       ,request = ['yur_eq'
                  ,'grp_eq'
                  ,'sch_eq'
                  ,'all_eq'
                  ,'add_eq'
                  ,'rem_eq'
                  ,'log_eq'];
        
        for(let i = 0 ; i < buttons.length ; i++){
            let button = document.getElementById(buttons[i]);
            if(button === null || button === undefined){
                continue;
            }
            button.addEventListener('click' , async function(){
                setTabHighlight(button);
                // fetches the correct tab ui on click
                fetch_url = '/backend/controlers/equipment/tab_controler.php'
                          + '?request=' 
                          + request[i] 
                          + '&type=usri';
                let response = await fetch(fetch_url);
                let  userInterface = await response.json();
                await setTabUI(userInterface);
                // fetch first time tab info
                fetch_url = 
                          '/backend/controlers/equipment/tab_controler.php'
                          + '?request='
                          + request[i]
                          + '&type=data'
                          + '&crud=read';
                response = await fetch(fetch_url);
                let tab_information = await response.json();
                console.log(tab_information);
            });   
        }
   }
