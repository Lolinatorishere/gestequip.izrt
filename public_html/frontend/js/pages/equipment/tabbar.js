async function createEquipmentContent(){
    let iframe = document.getElementById('equipment_internal')
       ,div = document.createElement('div')
       ,link = document.createElement('link')
       ,script = document.createElement('script')
       ,document_conent = undefined;
    await fetch('/frontend/iframes/equipment/tabbar.php')
    .then(response => response.text())
    .then(data =>{
        document_conent = data;
    })
    .catch((error) => {
        console.error('Error:' , error);
    });
    link.rel = 'stylesheet';
    link.href = '/frontend/css/iframes/equipment/iframe_content.css';
    iframe.contentDocument.head.appendChild(link);
    script.src = '/frontend/js/iframes/equipment/tabbar_functionality.js';
    iframe.contentDocument.head.appendChild(script);
    div.className = 'tabbar';
    div.innerHTML = document_conent;
    iframe.contentDocument.body.appendChild(div);
    script.addEventListener('load' , () => {
        iframe.contentWindow.addTabbarFunctionality();
    })
}