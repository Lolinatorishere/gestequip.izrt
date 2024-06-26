async function printUser(){
    window
    .addEventListener("load" , async function(){
        let user_info = await getUser();
        setUser(user_info.information.items);
    });
}

async function printUserGroups(){
    window
    .addEventListener("load" , async function(){
        let user_groups = await getUserGroups();
        document.getElementById("group-content-items").innerHTML = htmlGroupGenerate(user_groups.information.items);
    });
}

async function printUserEquipments(){
    window
    .addEventListener("load" , async function(){
        let user_equipment = await getUserEquipments();
        html = await htmlEquipmentGenerate(user_equipment.information);
        console.log(html);
        document.getElementById("equipment-content-items").innerHTML = html;
    });
}
