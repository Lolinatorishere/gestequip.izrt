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
