async function urlCreateBackendRequest(request){
    let page_controller = request.type + '/' + request.type +'_controller.php?'
       ,i = 0;
    url = '/backend/controllers/' 
        + page_controller; 
    for(let [key , value] of Object.entries(request.custom)){
        if(i !== 0){
            url += '&';
        }
        url += key + '=' + value;
        i++;
    }
    return url;
}


