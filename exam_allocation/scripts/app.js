import Alpine from "../node_modules/alpinejs/dist/module.esm.js"
import {viewRooms}  from "./view_rooms.js"

window.Alpine = Alpine
Alpine.start()  


document.querySelectorAll(".js-room-div").forEach(div => (
  div.addEventListener('click',()=>{
    const capacity = div.dataset.capacity;
    const id = div.dataset.roomId; 
    viewRooms(capacity,id);
  })
))