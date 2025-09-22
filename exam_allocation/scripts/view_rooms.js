export function viewRooms(capacity,id){
  const div = document.querySelector('.js-view-room-container');
  let html = ``
  for(let i =0;i<parseInt(capacity);i+=1){
    html += `<div class="w-[30px] h-[30px] bg-[#9E9B9B] border-3 rounded-md border-[#FFFEFE] ml-3"></div>`
  }
  div.innerHTML = html;
}