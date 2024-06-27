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

async function fetchPOST(endpoint, POST_request) {
    try {
        const response = await fetch(await urlCreateBackendRequest(endpoint), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(POST_request)
        });
        const data = await response.json(); // Properly await and parse the JSON response
        return data; // Return the data
    } catch (error) {
        console.error('Error:', error);
        return "error";
    }
}
