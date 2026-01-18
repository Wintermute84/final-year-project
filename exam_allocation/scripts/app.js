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

 
let rooms = []

const exams = document.querySelectorAll(".js-exam-div")
const hiddenInput = document.getElementById('selectedExamId');
const roomsDiv = document.querySelectorAll(".js-room-select-div");
let roomCapacity = 0

roomsDiv.forEach(room => {
  room.addEventListener('click',()=>{
    room.classList.toggle("dinkey")
    if(rooms.includes(room.dataset.eid)){
      rooms = rooms.filter(function(val) {
        if(val === room.dataset.eid){
          roomCapacity -= parseInt(room.dataset.capacity)
        }
        return val !== room.dataset.eid; 
      })
    }
    else{
      rooms.push(room.dataset.eid)
      roomCapacity += parseInt(room.dataset.capacity)
    }
  })
})

exams.forEach(exam => {
  exam.addEventListener('click',()=>{
    exams.forEach(e => e.classList.remove('dinkey'));
    exam.classList.toggle("dinkey")
    hiddenInput.value = exam.dataset.eid;
  })
})

document.getElementById("selectAllRooms")?.addEventListener("change", function () {
   if(this.checked){
    rooms = []
    roomCapacity = 0
    roomsDiv.forEach(room => {
      room.classList.add("dinkey")
      rooms.push(room.dataset.eid)
      roomCapacity += parseInt(room.dataset.capacity)
    })
   }
   else{
    roomCapacity = 0
    roomsDiv.forEach(room => {
      room.classList.remove("dinkey")
      rooms = [];
    })
   }
});

document.getElementById("proceedBtn")?.addEventListener("click", () => {

  if (rooms.length === 0) {
    alert("Select at least one room");
    return;
  }

  fetch("./routes/setRooms.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify({
      rooms: rooms,
      roomCapacity: roomCapacity
    })
  })
  .then(res => res.json())
  .then(data => {
    if(data.error){
      alert(data.error + " Discrepancy on " + data.discrepancy)
    }
    window.location.href="seating_plan.php";
  })
  .catch(err => {
    console.error(err);
    alert("Failed to generate seating");
  });

});


document.querySelectorAll(".js-slot-div").forEach(div => {
  div.addEventListener("click", () => {
    const { eid, edate: date, session: session } = div.dataset;
    const key = `semGroups_${eid}_${date}_${session}`;
    fetch("./routes/get_sems.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ eid, date, session })
    })
    .then(r => r.json())
    .then(d => {
      if (!d.success) return;

      const sems = d.sems || [];
      sem = sems
      availableSems = new Set(sems)
      if (sems.length < 2) {
        const auto = sems.map(s => [s]);
        sessionStorage.setItem(key, JSON.stringify(auto));
        markGrouped(div);
        loadSemGroupingUI(sems, div, auto);
      } else {
        const saved = sessionStorage.getItem(key);
        const parsed = saved ? JSON.parse(saved) : null;
        const grouping = parsed || null;
        if (grouping) {
          markGrouped(div);
          loadSemGroupingUI(sems, div, grouping);
        } else {
          resetSlotGroupingUI(sems, div);
        }
      }
    });
  });
});

function markGrouped(slotDiv) {
  slotDiv.classList.add("grouped");
}

function resetSlotGroupingUI(sems, slotDiv) {
  selected.clear();
  groups = [];
  loadSemGroupingUI(sems, slotDiv, null);
}


let selected = new Set();
let groups = [];
let currentSlot = null;
let availableSems = new Set()
let sem = []
let sem_groupings = []
function loadSemGroupingUI(sems, slotDiv, grouping) {
  currentSlot = slotDiv;
  const container = document.getElementById("numberContainer");
  container.innerHTML = "";
  document.getElementById("groupPreview").innerHTML = "";
  if (grouping) {
    renderGroupPreview(grouping);
    if(currentSlot.classList.contains("grouped")){
      return;
    }
  }

  sems.forEach(n => {
    const chip = document.createElement("div");
    chip.className = "num";
    chip.innerText = `S${n}`;
    chip.dataset.sem = n;
    chip.onclick = () => {
      if (selected.has(n)) {
        selected.delete(n);
        availableSems.add(n)
        chip.classList.remove("selected");
      } else {
        if (selected.size === 2) {
          alert("Max 2 sems per group");
          return;
        }
        selected.add(n);
        availableSems.delete(n);
        chip.classList.add("selected");
      }
      currentSlot.classList.remove("grouped");
    };
    container.appendChild(chip);
  });
}

function renderGroupPreview(groups) {
  let html = `<p class="m-2 p-2">Final Groups</p>`;
  groups.forEach(grp =>{
    html += `<div class="flex gap-2 w-fit m-4 p-2 border border-2-white">`;
    grp.forEach(sem => {
      html += `<div class="selected">S${sem}</div>`
    })
    html+=`</div>`
  })
  document.getElementById("groupPreview").innerHTML = html;
}

document.getElementById("createGroupBtn")?.addEventListener("click", () => {
  if (!currentSlot) return alert("Select a slot first");
  const sems = [...selected];
  if (sems.length === 0) return alert("Select 1 or 2 sems");
  if (sems.length > 2) return alert("Max 2 sems per group");
  groups.push(sems);
  sems.forEach(s => {
    if(availableSems.has(s)){
      availableSems.delete(s)
    }
  })
  
  if(availableSems.size <= 2 && availableSems.size > 0){
    let remainingSems = []
    availableSems.forEach(s => {
      remainingSems.push(s)
    })
    groups.push(remainingSems)
    availableSems.clear()
  }
  selected.clear();
  if(groups.length > 2){
    alert("There can only be at most 2 groups! Please regroup!")
    groups = [];
    availableSems = new Set(sem)
    currentSlot.classList.remove("grouped")
    loadSemGroupingUI(sem,currentSlot,null);
    return;
  }
 

  loadSemGroupingUI(availableSems,currentSlot,groups)
  if(availableSems.size === 0){
    markGrouped(currentSlot);
    saveGroupingForCurrentSlot();
  }
});

function saveGroupingForCurrentSlot() {
  const { eid, edate: date, session } = currentSlot.dataset;
  const key = `semGroups_${eid}_${date}_${session}`;
  groups.sort((a, b) => a.length - b.length);
  sessionStorage.setItem(key, JSON.stringify(groups));
}

document.getElementById("deleteGroupBtn")?.addEventListener("click", () => {
  const { eid, edate: date, session } = currentSlot.dataset;
  const key = `semGroups_${eid}_${date}_${session}`;
  if(sessionStorage.getItem(key)){
    sessionStorage.removeItem(key)
    groups = []
    availableSems = new Set(sem)
    currentSlot.classList.remove("grouped")
    loadSemGroupingUI(sem,currentSlot,null);
  }
})

document.getElementById("proceedfBtn")?.addEventListener("click", () => {
  const allSlots = [...document.querySelectorAll(".js-slot-div")];
  const allGrouped = allSlots.every(d => d.classList.contains("grouped"));
  if (!allGrouped) return alert("Complete grouping for all slots first!");
  else{
    const payload = {};
    allSlots.forEach(d => {
      const { eid, edate: date, session } = d.dataset;
      const key = `semGroups_${eid}_${date}_${session}`;
      const saved = sessionStorage.getItem(key);
      if (saved) payload[key] = JSON.parse(saved);
      localStorage.setItem('groupings',JSON.stringify(payload))
    });
    sessionStorage.clear()
    fetch("./routes/seating.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ sem_groupings: payload })
    })
    .then(r => r.json())
    .then(d => {
      if (d.success) window.location.href = "seating_plan.php?step=4";
    })
    .catch(e => alert("Allocation send failed"));
  }
})

let shuffleSems = []
let pairs = []
let oddBranch = null;
let groupMatching = {}
let availableData = []
let grid1 = []
let grid2 = []
let grid3 = []
let grid4 = []
let slotkey = ``

document.querySelectorAll(".js-shuffle-div").forEach(div => {
  div.addEventListener("click", () => {
    groupMatching = {}
    availableData = []
    grid1 = []
    grid2 = []
    grid3 = []
    grid4 = []
    document.querySelector(".js-shuffle-one-div").innerHTML = ""
    document.querySelector(".js-shuffle-two-div").innerHTML = ""
    document.querySelector(".js-shuffle-three-div").innerHTML = ""
    document.querySelector(".js-shuffle-four-div").innerHTML = ""
    document.querySelector(".available-branches-div").innerHTML = ""
    oddBranch = null;
    const { eid, edate: date, session: session } = div.dataset;
    const key = `semGroups_${eid}_${date}_${session}`;
    slotkey = `S${eid}_${date}_${session}`
    let sg = JSON.parse(localStorage.getItem('groupings'))
    let semGroups = sg[key]
    pairs = semGroups

    fetch("./routes/get_sems.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ eid, date, session })
    })
    .then(r => r.json())
    .then(d => {
      if (!d.success) return;
      const sems = d.sems || [];
      shuffleSems = sems;
      document.querySelectorAll(".shuffle-div").forEach(div =>{
        div.innerText = ``;
      })
      if(shuffleSems.length ===  1){
        document.querySelector(".js-shuffle-one").innerText = `Semester S${semGroups[0][0]} Side A`;
        document.querySelector(".js-shuffle-two").innerText = `Semester S${semGroups[0][0]} Side B`;
        
        oddBranch = semGroups[0][0]
        groupMatching[`${oddBranch}_A`] = 1
        groupMatching[`${oddBranch}_B`] = 2
      }
      else if(shuffleSems.length ===  2){
        document.querySelector(".js-shuffle-one").innerText = `Semester S${semGroups[0][0]}`;
        document.querySelector(".js-shuffle-two").innerText = `Semester S${semGroups[0][1]}`;
        groupMatching[semGroups[0][0]] = 1;
        groupMatching[semGroups[0][1]] = 2;
      }
      else if(shuffleSems.length ===  3){
        document.querySelector(".js-shuffle-one").innerText = `Semester S${semGroups[1][0]}`;
        document.querySelector(".js-shuffle-two").innerText = `Semester S${semGroups[1][1]}`;
        document.querySelector(".js-shuffle-three").innerText = `Semester S${semGroups[0][0]}`;
        
        groupMatching[semGroups[1][0]] = 1;
        groupMatching[semGroups[1][1]] = 2;
        groupMatching[semGroups[0][0]] = 3
      }
      else if(shuffleSems.length ===  4){
        document.querySelector(".js-shuffle-one").innerText = `Semester S${semGroups[0][0]}`;
        document.querySelector(".js-shuffle-two").innerText = `Semester S${semGroups[0][1]}`;
        document.querySelector(".js-shuffle-three").innerText = `Semester S${semGroups[1][0]}`;
        document.querySelector(".js-shuffle-four").innerText = `Semester S${semGroups[1][1]}`;
        groupMatching[semGroups[0][0]] = 1;
        groupMatching[semGroups[0][1]] = 2;
        groupMatching[semGroups[1][0]] = 3;
        groupMatching[semGroups[1][1]] = 4;
      }
      return fetch("./routes/get_branches.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ eid, date, session })
    })
    .then(r => r.json())
    .then(d => {
      if (!d.success) return;
      const branches = d.branches || [];
      branches.forEach(branch => {
        availableData.push({branch:branch.branch,sem:branch.sem})
      })
      if(sessionStorage.getItem(slotkey)){
        document.querySelector(`.${slotkey}`).classList.add('grouped')
        const {no_sems,grids} = JSON.parse(sessionStorage.getItem(slotkey))
        console.log(grids)
        availableData = []
        grid1 = grids.grid1
        grid2 = grids.grid2
        grid3 = grids.grid3
        grid4 = grids.grid4
        let l = no_sems
        if(l == 1){
          renderMatchingDiv(grids.grid1,".js-shuffle-one-div","A")
          renderMatchingDiv(grids.grid2,".js-shuffle-two-div","B")
        }
        else if(l == 2){
          renderMatchingDiv(grids.grid1,".js-shuffle-one-div",null)
          renderMatchingDiv(grids.grid2,".js-shuffle-two-div",null)
        }
        else if(l == 3){
          renderMatchingDiv(grids.grid1,".js-shuffle-one-div",null)
          renderMatchingDiv(grids.grid2,".js-shuffle-two-div",null)
          renderMatchingDiv(grids.grid3,".js-shuffle-three-div",null)
        }
        else if(l == 4){
          renderMatchingDiv(grids.grid1,".js-shuffle-one-div",null)
          renderMatchingDiv(grids.grid2,".js-shuffle-two-div",null)
          renderMatchingDiv(grids.grid3,".js-shuffle-three-div",null)
          renderMatchingDiv(grids.grid4,".js-shuffle-four-div",null)
        }
      }
      displayAvailableBranches();
    });
    });
  });
});


function displayAvailableBranches(){
  let html = ``
  availableData.forEach(data => {
    html += getAvailableBranch(data.sem, data.branch)
  })
  if(availableData.length === 0 && !sessionStorage.getItem(slotkey)){
    sessionStorage.setItem(slotkey, JSON.stringify({no_sems:shuffleSems.length,groups:pairs,grids:{grid1:grid1,grid2:grid2,grid3:grid3,grid4:grid4}}));
    document.querySelector(`.${slotkey}`).classList.add('grouped')
  }
  else{
    if(sessionStorage.getItem(slotkey) && availableData.length > 0){
      sessionStorage.removeItem(slotkey)
      document.querySelector(`.${slotkey}`).classList.remove('grouped')
    }
  }
  document.querySelector(".available-branches-div").innerHTML = html;
  addShiftListeners();
}

function getAvailableBranch(sem, branch){
  let html = ``
  if(oddBranch == sem){
    html += `
      <div class="flex items-center justify-between w-full h-[50px] bg-[#191717] p-2 border border-2-white rounded-[4px]" data-sem="${sem}" data-branch="${branch}" >
        <div class="flex gap-2 items-center">
          <div class="w-[30px] h-[30px] text-[13px] rounded-full border border-dashed border-2-white flex justify-center items-center">
            S${sem}
          </div>
          <p>${branch}</p>
        </div>
        <div class="flex gap-2">
          <button data-sem="${sem}" data-branch="${branch}" data-special="A" type="button" class="js-add-to-shuffle-div h-[30px] w-[30px] flex items-center justify-center border border-[#201d1d] rounded-[4px] bg-white">
            A
          </button>
          <button data-sem="${sem}" data-branch="${branch}" data-special="B" type="button" class="js-add-to-shuffle-div h-[30px] w-[30px] flex items-center justify-center border border-[#201d1d] rounded-[4px] bg-white">
            B
          </button>
        </div>
      </div>
    `
  }
  else{
    html = `<div class="flex items-center justify-between w-full h-[50px] bg-[#191717] p-2 border border-2-white rounded-[4px]" data-sem="${sem}" data-branch="${branch}">
              <div class="flex gap-2 items-center">
                <div class="w-[30px] h-[30px] text-[13px] rounded-full border border-dashed border-2-white flex justify-center items-center">
                  S${sem}
                </div>
                <p>${branch}</p>
              </div>
              <button data-sem="${sem}" data-branch="${branch}" type="button" class="h-[30px] w-[30px] flex items-center justify-center border border-[#201d1d] rounded-[4px] bg-white js-add-to-shuffle-div">
                <img src="assets/arrow_forward.png" class="h-5" alt="arrow forward png">
              </button>
            </div>`
    }
  return html;
}

function addShiftListeners(){
  document.querySelectorAll('.js-add-to-shuffle-div').forEach(button => {
    button.addEventListener('click',()=>{
      renderShuffleDivs(button.dataset.sem,button.dataset.branch,button.dataset.special?button.dataset.special:null)  
    })
  })      
}

function renderShuffleDivs(sem, branch,special){
  availableData = availableData.filter(obj => !(obj.sem == sem && obj.branch === branch))
  let data = []
  let jsClass = ``

  if(sem == oddBranch && special){
    if(groupMatching[`${sem}_${special}`] === 1){
      grid1.push({sem:sem,branch:branch})
      data = grid1
      jsClass = ".js-shuffle-one-div"
    }
    else if(groupMatching[`${sem}_${special}`] === 2){
      grid2.push({sem:sem,branch:branch})
      data = grid2
      jsClass = ".js-shuffle-two-div"
    }
    else if(groupMatching[`${sem}_${special}`] === 3){
      grid3.push({sem:sem,branch:branch})
      data = grid3
      jsClass = ".js-shuffle-three-div"
    }
    else if(groupMatching[`${sem}_${special}`] === 4){
      grid4.push({sem:sem,branch:branch})
      data = grid4
      jsClass = ".js-shuffle-four-div"
    }
  }
  else if(groupMatching[sem] === 1){
    grid1.push({sem:sem,branch:branch})
    data = grid1
    jsClass = ".js-shuffle-one-div"
  }
  else if(groupMatching[sem] === 2){
    grid2.push({sem:sem,branch:branch})
    data = grid2
    jsClass = ".js-shuffle-two-div"
  }
  else if(groupMatching[sem] === 3){
    grid3.push({sem:sem,branch:branch})
    data = grid3
    jsClass = ".js-shuffle-three-div"
  }
  else if(groupMatching[sem] === 4){
    grid4.push({sem:sem,branch:branch})
    data = grid4
    jsClass = ".js-shuffle-four-div"
  }
  displayAvailableBranches();
  renderMatchingDiv(data,jsClass,special)
}

function renderMatchingDiv(data,jsClass,special){
  let html = ``
  data.forEach(child => {
    html += getShuffleBranch(child.sem,child.branch,special)
  })
  document.querySelector(jsClass).innerHTML = html;
  addUnshiftListeners(jsClass);
}

function addUnshiftListeners(jsClass){
  const container = document.querySelector(jsClass)
  const child = [...container.querySelectorAll(".js-add-to-available-div")]
  child.forEach(c => {
    let sem = c.dataset.sem
    let branch = c.dataset.branch
    let special = c.dataset.special
    let data = []
    c.addEventListener('click',()=>{
      if(sem == oddBranch && special){
        if(groupMatching[`${sem}_${special}`] === 1){
          grid1 = grid1.filter(obj => !(obj.sem == sem && obj.branch === branch))
          data = grid1
        }
        else if(groupMatching[`${sem}_${special}`] === 2){
          grid2 = grid2.filter(obj => !(obj.sem == sem && obj.branch === branch))
          data = grid2
        }
        else if(groupMatching[`${sem}_${special}`] === 3){
          grid3 = grid3.filter(obj => !(obj.sem == sem && obj.branch === branch))
          data = grid3
        }
        else if(groupMatching[`${sem}_${special}`] === 4){
          grid4 = grid4.filter(obj => !(obj.sem == sem && obj.branch === branch))
          data = grid4
        }
      }
      else if(groupMatching[sem] === 1){
        grid1 = grid1.filter(obj => !(obj.sem == sem && obj.branch === branch))
        data = grid1
      }
      else if(groupMatching[sem] === 2){
        grid2 = grid2.filter(obj => !(obj.sem == sem && obj.branch === branch))
        data = grid2
      }
      else if(groupMatching[sem] === 3){
        grid3 = grid3.filter(obj => !(obj.sem == sem && obj.branch === branch))
        data = grid3
      }
      else if(groupMatching[sem] === 4){
        grid4 = grid4.filter(obj => !(obj.sem == sem && obj.branch === branch))
        data = grid4
      }
      renderMatchingDiv(data,jsClass,special)
      availableData.push({sem:sem,branch:branch})
      displayAvailableBranches();
    })
  })
}

function getShuffleBranch(sem, branch, special){
  let html = `<div class="flex items-center justify-between w-full h-[50px] bg-[#191717] p-2 border border-2-white rounded-[4px]" data-sem="${sem}" data-branch="${branch}" data-special="${special}">
              <div class="flex gap-2 items-center">
                <div class="w-[30px] h-[30px] text-[13px] rounded-full border border-dashed border-2-white flex justify-center items-center">
                  S${sem}
                </div>
                <p>${branch}</p>
              </div>
              <button data-sem="${sem}" data-branch="${branch}" data-special="${special}" type="button" class="h-[30px] w-[30px] flex items-center justify-center border border-[#201d1d] rounded-[4px] bg-white js-add-to-available-div">
                <img src="assets/arrow_forward.png" class="h-5 rotate-180" alt="arrow forward png">
              </button>
            </div>`
  return html;
}

document.getElementById("proceeddBtn")?.addEventListener("click", () => {
  console.log()
  const allSlots = [...document.querySelectorAll(".js-shuffle-div")];
  const allGrouped = allSlots.every(d => d.classList.contains("grouped"));
  if (!allGrouped) return alert("Complete grouping for all slots first!");
  else{
    console.log("done")
    const payload = {};
    allSlots.forEach(d => {
      const { eid, edate: date, session } = d.dataset;
      const key = `S${eid}_${date}_${session}`;
      const saved = sessionStorage.getItem(key);
      if (saved) payload[key] = JSON.parse(saved);
    });
    
    fetch("./routes/allocation_engine.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ seating_data: payload })
    })
    .then(r => r.json())
    .then(d => {
    if (d.success) window.location.href = "ding.php";
    })
    .catch(e => alert("Allocation send failed"));
  };
})

const reapplyEventListener = (ename) => document.querySelectorAll('.js-room-blocks').forEach(button => {
  button.addEventListener('click',()=>{
    const edate = button.dataset.edate;
    const session = button.dataset.session;
    const aid = button.dataset.aid;
    const roomId = button.dataset.roomId;
    const examName = ename;
    const roomType = button.dataset.roomType;
     fetch("./routes/getSeatedStudentData.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ edate:edate,session:session,aid:aid,roomId:roomId })
    })
    .then(r => r.json())
    .then(d => {
      let students = d.students;
      if(roomType == 'Drawing'){
        localStorage.setItem('roomReport',JSON.stringify({edate:edate,session:session,aid:aid,roomId:roomId,examName:examName,roomType:roomType,aSlots:d.students,bSlots:[]}))
      let html = `<div class="w-[90%] flex flex-col border">
    <table class="font-['sans-serif']">
      <tr>
        <th>Muthoot Institute of Technology and Science (Autonomous)</th>
      </tr>
      <tr>
        <th>${examName}</th>
      </tr>
      <tr>
        <th>Date of Exam: ${edate} <span class="mx-8"></span>Session: ${session}</th>
      </tr>
      <tr>
        <th>Hall Seating Arrangement</th>
      </tr>
      <tr>
        <th>Hall No: ${roomId}</th>
      </tr>
    </table>
    <div class="flex w-full justify-between gap-30">
        <table class="w-[100%] h-fit">
          <tr border="0">
            <th>Branch</th>
            <th>Roll No</th>
            <th>Seat</th>
          </tr>`
      students.forEach(student => {
        html += `<tr>
            <td align="center">S${student.semester}  ${student.branch}</td>
            <td align="center">${student.rollno}</td>
            <td align="center">${student.seat}</td>
          </tr>`
      })
      html += `</table>
    </div>
  </div>`
      document.querySelector('.js-seating-data-container').innerHTML = html;
    }
    else{
      let html = `<div class="w-[90%] flex flex-col border">
    <table class="font-['sans-serif']">
      <tr>
        <th>Muthoot Institute of Technology and Science (Autonomous)</th>
      </tr>
      <tr>
        <th>${examName}</th>
      </tr>
      <tr>
        <th>Date of Exam: ${edate} <span class="mx-8"></span>Session: ${session}</th>
      </tr>
      <tr>
        <th>Hall Seating Arrangement</th>
      </tr>
      <tr>
        <th>Hall No: ${roomId}</th>
      </tr>
    </table>
    <div class="flex w-full justify-between gap-10">
        <table class="w-[50%] h-fit">
          <tr border="0">
            <th>Branch</th>
            <th>Roll No</th>
            <th>Seat</th>
          </tr>`
    let aSlot = students.filter(student => student['seat'][0] === 'A')
    let bSlot = students.filter(student => student['seat'][0] === 'B')
    localStorage.setItem('roomReport',JSON.stringify({edate:edate,session:session,aid:aid,roomId:roomId,examName:examName,roomType:roomType,aSlots:aSlot,bSlots:bSlot}))
    aSlot.forEach(student => {
      html += `<tr>
            <td align="center">S${student.semester}  ${student.branch}</td>
            <td align="center">${student.rollno}</td>
            <td align="center">${student.seat}</td>
          </tr>`
    })
    html += `</table>
    <table class="w-[50%] h-fit">
          <tr border="0">
            <th>Branch</th>
            <th>Roll No</th>
            <th>Seat</th>
          </tr>
        `;
    bSlot.forEach(student => {
      html += `<tr>
            <td align="center">S${student.semester}  ${student.branch}</td>
            <td align="center">${student.rollno}</td>
            <td align="center">${student.seat}</td>
          </tr>`
    })
    html += `</table></div></div>`
    document.querySelector('.js-seating-data-container').innerHTML = html; 
    }
  })
    .catch(e => alert("Allocation send failed"));
  })
})


document.querySelectorAll('.js-seating-blocks').forEach(button => {
  button.addEventListener('click',()=>{
    const edate = button.dataset.edate;
    const session = button.dataset.session;
    const aid = button.dataset.aid;
    const ename = button.dataset.ename;
    generateReports(edate,session,aid,ename);
  })
})


function formatRanges(nums) {
  if (!nums.length) return "";

  let result = [];
  let start = nums[0];
  let prev = nums[0];

  for (let i = 1; i <= nums.length; i++) {
    if (nums[i] === prev + 1) {
      prev = nums[i];
    } else {
      result.push(start === prev ? `${start}` : `${start}-${prev}`);
      start = nums[i];
      prev = nums[i];
    }
  }

  return result.join(", ");
}

async function generateReports(edate,session,aid,ename){
  localStorage.setItem('downloadReport',JSON.stringify({ edate: edate, session:session, aid:aid, ename:ename }))
  const res1 = await fetch("./routes/getSeatingRoomData.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ edate: edate, session:session, aid:aid })
    });
  const d1 = await res1.json();
  let data1 = d1.roomData;
  let html = ``;
  Object.entries(data1).forEach(([rid, roomInfo]) => {
    html += `<div data-edate=${roomInfo.edate} data-session=${roomInfo.session} data-aid=${roomInfo.aid} data-room-id=${roomInfo.room} data-room-type=${roomInfo.type} class="w-[95%] min-h-[80px] max-h-[85px] cursor-pointer bg-[#151515] mr-2 border rounded-sm flex items-center justify-between hover:opacity-80 transition-all ease-in-out js-room-blocks">
                  <div class="w-fit flex flex-col ml-2">
                    <p class="text-sm">No - ${roomInfo.room}</p>
                    <p class="text-sm">Capacity - ${roomInfo.capacity}</p>
                    <p class="text-sm">Room Type - ${roomInfo.type}</p>
                  </div>
              </div>`
  });
  document.querySelector('.js-seated-rooms-div').innerHTML = html;
  document.querySelector('.js-seating-data-container').innerHTML = ''; 
  reapplyEventListener(ename);
  const res2 = await fetch("./routes/getReports.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ edate, session, aid })
    });

  const d2 = await res2.json();
  let data2 = d2.reportData;
  let table = ``;
  Object.entries(data2).forEach(([rid, roomInfo]) => {
    table += `<table class="w-[88%] h-fit report pdf-page m-4">
    <tr>
      <th colspan="4">Muthoot Institute of Technology and Science (Autonomous)</th>
    </tr>
    <tr>
      <th colspan="4">S${rid}, ${ename}</th>
    </tr>
    <tr>
      <th colspan="4">Hall Allotment Plan</th>
    </tr>
    <tr>
      <th colspan="4">Date of Exam: ${edate} <span class="mx-4"></span> Session: ${session}</th>
    </tr>
    <tr>
      <th>Branch</th>
      <th>Hall</th>
      <th>Roll No.</th>
      <th>Total no of students</th>
    </tr>
    `
     Object.entries(roomInfo).forEach(([branch,rinfo]) => {
      table += `<tr>
           <th rowspan=${Object.keys(rinfo).length+1}>${branch}</th>`
      Object.entries(rinfo).forEach(([room,rinfo]) => {
        table += `<tr>
                    <td>${room}</td>
                    <td>${formatRanges(rinfo)}</td>
                    <td>${rinfo.length}</td>
                  </tr>`
      })
      table+=`</tr>`
     })
      table+=` </table>`
    })
    document.querySelector('.js-hall-reports-div').innerHTML = table; 
}


document.getElementById('download-hall-reports').addEventListener('click',()=>{
  downloadPDF();
})


function downloadPDF() {
  const {edate,aid,session,ename} = JSON.parse(localStorage.getItem('downloadReport'))
  fetch("./routes/generatePDFs.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      edate: edate,
      session: session,
      aid: aid,
      ename:ename
    })
  })
  .then(res => res.blob())
  .then(blob => {

    const url = window.URL.createObjectURL(blob);

    const a = document.createElement("a");
    a.href = url;
    a.download = `${edate}_${session}_${ename}_Seating_Report.pdf`;
    a.click();

  })
  .catch(err => alert("PDF generation failed"));
}

document.querySelector('.js-download-room-report').addEventListener('click',()=>{
  const {edate,session,aid,roomId,examName,roomType,aSlots,bSlots} = JSON.parse(localStorage.getItem('roomReport'))
  fetch("./routes/generateStudPdfs.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      edate: edate,
      session: session,
      aid: aid,
      ename:examName,
      roomId:roomId,
      roomType:roomType,
      aSlots:aSlots,
      bSlots:bSlots
    })
  })
  .then(res => res.blob())
  .then(blob => {

    const url = window.URL.createObjectURL(blob);

    const a = document.createElement("a");
    a.href = url;
    a.download = `${edate}_${session}_${examName}_Seating_Report.pdf`;
    a.click();

  })
  .catch(err => alert("PDF generation failed"));

})