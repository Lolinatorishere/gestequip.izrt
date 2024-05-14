async function createEquipmentContent(){
    let iframe = document.getElementById('equipment_internal')
       ,div = document.createElement('div')
       ,link = document.createElement('link')
       ,script = document.createElement('script')
       ,tabs = {
            buttons:['your_equipment'
                    ,'groups_equipment'
                    ,'search'
                    ,'all_equipment'
                    ,'add_equipment'
                    ,'remove_equipment'
                    ,'logs'],

                tab:['yur_eq'
                    ,'grp_eq'
                    ,'sch_eq'
                    ,'all_eq'
                    ,'add_eq'
                    ,'rem_eq'
                    ,'log_eq']
        }
       ,tab_conent = undefined
       ,tabbar_conent = undefined;
    await fetch('/frontend/iframes/equipment/tabbar.php')
    .then(response => response.text())
    .then(data =>{
        tabbar_conent = data;
    })
    .catch((error) => {
        console.error('Error:' , error);
    });
    await fetch('/frontend/iframes/equipment/default_selection.php')
    .then(response => response.text())
    .then(data => {
        tab_conent = data; 
    })
    .catch((error) => {
        console.error('Error:' , error);
    });

    link.rel = 'stylesheet';
    link.href = '/frontend/css/iframes/equipment/iframe_content.css';
    iframe.contentDocument.head.appendChild(link);
    script.src = '/frontend/js/iframes/equipment/tab_controler.js';
    iframe.contentDocument.head.appendChild(script);
    div.className = 'tabbar';
    div.innerHTML = tabbar_conent;
    iframe.contentDocument.body.appendChild(div);
    div = document.createElement('div');
    div.className = 'tab-content';
    div.id = 'tab-content';
    div.innerHTML = tab_conent;
    iframe.contentDocument.body.appendChild(div);
    script.addEventListener('load' , () => {
        iframe.contentWindow.tabFunctionality(tabs);
    })
}