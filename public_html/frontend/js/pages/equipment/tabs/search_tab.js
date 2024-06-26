
async function searchTabFunctionality(tab){
    switch(tab.id){
        case 'user-group-search':
            tabLoadUi(tab , request , "tabbar-options" , "selected-search-tab" , 'search_tab/group_user' , 'none' )
            break;
        case 'equipment-default':
            tabLoadUi(tab , request , "tabbar-options" , "selected-search-tab" , 'search_tab/equipment' , 'none' )
            break;
        case 'equipment-specific':
            tabLoadUi(tab , request , "tabbar-options" , "selected-search-tab" , 'search_tab/equipment_type' , 'none' )
            break;
        default:
            break;
    }
}

