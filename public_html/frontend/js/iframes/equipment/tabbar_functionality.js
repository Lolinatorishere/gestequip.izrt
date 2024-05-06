async function addTabbarFunctionality(){
        button_your = document.getElementById('your_equipment');
        button_group = document.getElementById('groups_equipment');
        button_all = document.getElementById('all_equipment');
        button_search = document.getElementById('search');
        button_add = document.getElementById('add_equipment');
        button_remove = document.getElementById('remove_equipment');
        button_logs = document.getElementById('logs');

    if(button_your != undefined || button_your != null)
        button_your.addEventListener('click' , function(){
            console.log('button_your has been pressed');
    });

    if(button_group != null || button_group != undefined)
        button_group.addEventListener('click' , function(){
            console.log('button_group has been pressed');
    });

    if(button_all != null || button_all != undefined)
        button_all.addEventListener('click' , function(){
            console.log('button_all has been pressed');
    });

    if(button_search != null || button_search != undefined)
        button_search.addEventListener('click' , function(){
            console.log('button_search has been pressed');
    });

    if(button_add != null || button_add != undefined)
        button_add.addEventListener('click' , function(){
            console.log('button_add has been pressed');
    });

    if(button_remove != null || button_remove != undefined)
        button_remove.addEventListener('click' , function(){
            console.log('button_remove has been pressed');
    });

    if(button_logs != null || button_logs != undefined)
        button_logs.addEventListener('click' , function(){
            console.log('button_logs has been pressed');
    });

}