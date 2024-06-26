
async function getSpecificEquipment(user_id , group_id , equipment_id){
    let request = {
             type: 'equipment'
            ,custom: {
                 tab: 'speceq'
                ,type: 'data' 
                ,crud: 'read'
                }
            }
    return fetchPOST();
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

async function getAuthEquipments(){
    let request = {
             type: 'equipment'
            ,custom: {
                 tab: 'autheq'
                ,type: 'data' 
                ,crud: 'read'
                }
            }
    let user_request = await fetch(await urlCreateBackendRequest(request));
    let user_info = await user_request.json();
    return user_info;
}

async function getUser(){
    let request = {
             type: 'user'
            ,custom: {
                 tab: 'yurusr'
                ,type: 'data' 
                ,crud: 'read'
                }
            }
    let user_request = await fetch(await urlCreateBackendRequest(request));
    let user_info = await user_request.json();
    return user_info;
}

async function getUserGroups(){
    let request = {
             type: 'group'
            ,custom: {
                 tab: 'yurgrp'
                ,type: 'data' 
                ,crud: 'read'
                }
            }
    let user_request = await fetch(await urlCreateBackendRequest(request));
    let user_info = await user_request.json();
    return user_info;
}

async function getUserEquipments(){
    let request = {
             type: 'equipment'
            ,custom: {
                 tab: 'youreq'
                ,type: 'data' 
                ,crud: 'read'
                }
            }
    let user_request = await fetch(await urlCreateBackendRequest(request));
    let user_info = await user_request.json();
    return user_info;
}

