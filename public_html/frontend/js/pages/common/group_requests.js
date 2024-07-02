async function getUserGroupAuth(group_id){
    let request = {
             type: 'group'
            ,custom: {
                 tab: 'yurath'
                ,type: 'data' 
                ,crud: 'read'
                }
            }
    let post ={
        data:{
            query:{
                group_id:group_id,
            }
        }
    }
    return await fetchPOST(request , post);
}

async function getUserGroups(page , limit){
    let request = {
             type: 'group'
            ,custom: {
                 tab: 'yurgrp'
                ,type: 'data' 
                ,crud: 'read'
                ,pgng: 1
                ,page: 1
                }
            }
    if(page){
        request.custom.page = page;
    }
    if(typeof page !== 'undefined' && page !== null) 
        request.custom.page = page;
    if(typeof limit !== 'undefined' && limit !== null) 
        request.custom["lmit"] = limit;
    let group_request = await fetch(await urlCreateBackendRequest(request));
    let group_info = await group_request.json();
    return group_info;
}

async function getGroups(page , limit){
let request = {
             type: 'group'
            ,custom: {
                 tab: 'allgrp'
                ,type: 'data' 
                ,crud: 'read'
                ,pgng: 1
                ,page: 1
                }
            }
    if(page){
        request.custom.page = page;
    }
    if(typeof page !== 'undefined' && page !== null) 
        request.custom.page = page;
    if(typeof limit !== 'undefined' && limit !== null) 
        request.custom["lmit"] = limit;
    let user_request = await fetch(await urlCreateBackendRequest(request));
    let user_info = await user_request.json();
    return user_info;
}
