
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

async function getUsers(page , limit){
    let request = {
             type: 'user'
            ,custom: {
                 tab: 'allusr'
                ,type: 'data' 
                ,crud: 'read'
                ,pgng: "1"
                ,page: page
                ,lmit: limit
                }
            }
    let user_request = await fetch(await urlCreateBackendRequest(request));
    let user_info = await user_request.json();
    return user_info;
}

