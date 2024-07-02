
async function getSpecificEquipment(user_id , group_id , equipment_id){
    let request = {
             type: 'equipment'
            ,custom: {
                 tab: 'speceq'
                ,type: 'data' 
                ,crud: 'read'
                }
            }
    let post ={
        data:{
            query:{
                user_id:user_id,
                group_id:group_id,
                equipment_id:equipment_id,
            }
        }
    }
    return await fetchPOST(request , post);
}

async function getSearchEquipment(page , limit , data){
    let request = {
             type: 'equipment'
            ,custom: {
                 tab: 'sch_eq'
                ,type: 'data' 
                ,crud: 'read'
                }
            }
    let post ={
        data:{
            query:data
        }
    }
    if(typeof page !== 'undefined' && page !== null){
        request.custom.page = page;
    }
    if(typeof limit !== 'undefined' && limit !== null){
        request.custom.lmit = limit;
    }
    return fetchPOST(request , post);
}

async function getEquipmentTypes(){
    let request = {
             type: 'equipment'
            ,custom: {
                 tab: 'eqtype'
                ,type: 'data' 
                ,crud: 'read'
                }
            }
    let user_request = await fetch(await urlCreateBackendRequest(request));
    let user_info = await user_request.json();
    return user_info;
}

async function getEquipmentTableDescription(equipment_type){
    let request = {
             type: 'equipment'
            ,custom: {
                 tab: 'tbdesc'
                ,type: 'data' 
                ,crud: 'read'
                }
            }
    let post ={
        data:{
            query:{
            }
        }
    }
    if(typeof equipment_type  !== 'undefined' && equipment_type !== null){
        post.data.query["equipment_type"] = equipment_type;
    }
    return fetchPOST(request , post);
}

async function getAuthEquipments(page , limit){
        let request = {
             type: 'equipment'
            ,custom: {
                 tab: 'autheq'
                ,type: 'data' 
                ,crud: 'read'
                ,pgng: 1
                }
            }
    if(typeof page !== 'undefined' && page !== null) 
        request.custom.page = page;
    if(typeof limit !== 'undefined' && limit !== null) 
        request.custom.lmit = limit;
    let user_request = await fetch(await urlCreateBackendRequest(request));
    let user_info = await user_request.json();
    return user_info;
}

async function getEquipmentLogs(page , limit){
    let request = {
             type: 'equipment'
            ,custom: {
                 tab: 'getlog'
                ,type: 'data' 
                ,crud: 'read'
                ,pgng: 1
                }
            }
    if(typeof page !== 'undefined' && page !== null) 
        request.custom.page = page;
    if(typeof limit !== 'undefined' && limit !== null) 
        request.custom.lmit = limit;
    let user_request = await fetch(await urlCreateBackendRequest(request));
    let user_info = await user_request.json();
    return user_info;
}

async function getUserEquipments(page){
    let request = {
             type: 'equipment'
            ,custom: {
                 tab: 'youreq'
                ,type: 'data' 
                ,crud: 'read'
                ,pgng: 1
                ,page: 1
                }
            }
    if(typeof page !== 'undefined' && page !== null) 
        request.custom.page = page;
    if(typeof limit !== 'undefined' && limit !== null) 
        request.custom.lmit = limit;
    let user_request = await fetch(await urlCreateBackendRequest(request));
    let user_info = await user_request.json();
    return user_info;
}

async function postEquipmentUpdate(data){
    if(typeof data !== "object")
        return;
    
    let request = {
        type: 'equipment'
        ,custom: {
            type: 'data' 
            ,crud: 'update'
            }
        }
    let post = {
        data:data
    }
    return await fetchPOST(request , post);
}

async function postEquipmentReferenceDelete(user_id , group_id , equipment_id){
    let request = {
        type: 'equipment'
        ,custom: {
            type: 'data' 
            ,crud: 'delete'
            ,rgin: 'reference'
            }
        }
    let post = {
        data:{
            user_id:user_id,
            group_id:group_id,
            equipment_id:equipment_id
        }
    }

    if(Object.keys(edit_tables.information.specific) !== 0){
    }
    return await fetchPOST(request , post);
}

