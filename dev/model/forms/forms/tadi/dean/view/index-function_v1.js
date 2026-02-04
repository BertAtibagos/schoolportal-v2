function GETACADEMICLEVEL() {

  const formData = new FormData();
  formData.append('type','GET_ACADEMIC_LEVEL');

  fetch("tadi/dean/controller/index-info.php", {
    method: "POST",
    body:formData
  })
    .then(response => response.json())
    .then(result => {
      let optLevel = "";
      if (result.length) {
        result.forEach(value => {
          optLevel += `<option value="${value.SchlAcadLvl_ID}">${value.SchlAcadLvl_NAME}</option>`;
        });
      } else {
        optLevel = "<option>No Academic Level Found.</option>";
      }
      document.querySelector("#academiclevel").insertAdjacentHTML("beforeend", optLevel);
    })
    .catch(error => console.error("Error fetching academic level:", error));
}

let subj_instr = [];
let prof_id = null;
let subj_id = null;

function GET_SUBJECT_BY_INSTRUCTOR(value) {
  const { SchlProf_ID } = value;

  const lvlid = document.getElementById("academiclevel").value;
  const yrlvlid = document.getElementById("academicyearlevel").value;
  const prdid = document.getElementById("academicperiod").value;
  const yrid = document.getElementById("acadyear").value;

  const formData = new FormData();
  formData.append('type','GET_SUBJECT_BY_INSTRUCTOR');
  formData.append('prof_id',SchlProf_ID);
  formData.append('lvl_id',lvlid);
  formData.append('prd_id',prdid);
  formData.append('yr_id',yrid);
  formData.append('yrlvl_id',yrlvlid);

  fetch(`tadi/dean/controller/index-info.php`,{
    method: "POST",
    body: formData
  })
  .then(response => response.json())
  .then(result => {
    displaySubjList(result);
    subj_instr = result;
  })
  .catch(error => console.error("Error fetching instructor subjects:", error));
}

function GET_INSTRUCTOR_BY_SUBJECT(value) {
  const { subj_id } = value;
  const { lvlid } = value;
  const { prdid } = value;
  const { yrid } = value;
  const { yrlvlid } = value;

  const formData = new FormData();
  formData.append('type','GET_INSTRUCTOR_BY_SUBJECT');
  formData.append('subj_id',subj_id);
  formData.append('lvlid',lvlid);
  formData.append('prdid',prdid);
  formData.append('yrid',yrid);
  formData.append('yrlvlid',yrlvlid);

  fetch(`tadi/dean/controller/index-info.php`,{
    method: "POST",
    body: formData
  })
  .then(response => response.json())
  .then(result => {
    displaySubjToIns(result);
  })
  .catch(error => console.error("Error fetching instructors by subject:", error));
}

function displaySubjToIns(result) {
  const tableRows = result
    .map(item => {
    //   if (!item.prof_name) {
    //     return `<tr><td colspan="2"><span class="btn btn-sm w-100" style="background-color: #95A5A6; color: white; pointer-events: none;">No Instructor</span></td></tr>`;
    //   }
      return `<tr key="${item.subj_id}">
                <td class="col">${item.prof_name ?? "No Instructor"}</td>
                <td class="col">
                  <button class="btn btn-sm w-100 button-bg-change view-sections" 
                    data-bs-target="#Instructor_Section_List" 
                    data-bs-toggle="modal"
                    data-prof-id="${item.SchlProf_ID}"
                    data-lvl-id="${item.lvlid}"
                    data-prd-id="${item.prdid}"
                    data-yr-id="${item.yrid}"
                    data-yrlvl-id="${item.yrlvlid}" ${item.prof_name ?? "disabled"}>VIEW SECTIONS</button>
                </td>
              </tr>`;
    })
    .join("");

  document.querySelector("#subj_instr_list").innerHTML = tableRows;

  document.querySelectorAll(".view-sections").forEach(button => {
    button.addEventListener("click", () => {
      const data = {
        SchlProf_ID: button.getAttribute("data-prof-id"),
        lvlid: button.getAttribute("data-lvl-id"),
        prdid: button.getAttribute("data-prd-id"),
        yrid: button.getAttribute("data-yr-id"),
        yrlvlid: button.getAttribute("data-yrlvl-id")
      };
      getSectionList(data);
    });
  });
}

function getSectionList(value) {
  const { SchlProf_ID } = value;
  const { lvlid } = value;
  const { prdid } = value;
  const { yrid } = value;
  const { yrlvlid } = value;

  const formData = new FormData();
  formData.append('type','GET_SECTION_LIST');
  formData.append('prof_id',SchlProf_ID);
  formData.append('lvlid',lvlid);
  formData.append('prdid',prdid);
  formData.append('yrid',yrid);
  formData.append('yrlvlid',yrlvlid);

  fetch(`tadi/dean/controller/index-info.php`,{
    method: "POST",
    body: formData
  })
  .then(response => response.json())
  .then(result => {
    displaySectionList(result);
  })
  .catch(error => console.error("Error fetching section list:", error));
}

function displaySectionList(result) {
  const tableRows = result.length
    ? result.map((item) => `
        <tr key="${item.sub_off_id}">
          <td>${item.section_name}</td>
          <td>
            <button class="btn btn-sm w-100 button-bg-change view-button instructor_Tadi_List" 
                    data-bs-toggle="modal" 
                    data-bs-target="#Instructor_Tadi_List" 
                    data-suboff-id="${item.subj_id}"
                    data-prof-id="${item.prof_id}"
                    data-sub-desc="${item.subj_desc}"
                    data-sub-sect="${item.section_name || 'No Section'}">VIEW</button>
          </td>
        </tr>`
      ).join('')
    :`<tr>
        <td colspan="5" class="text-center">No tadi forms available</td>
      </tr>`;

  document.getElementById('prof_section_list_table').innerHTML = tableRows;

  const viewButtons = document.querySelectorAll('.view-button');
  if (viewButtons.length > 0) {
    viewButtons.forEach(button => {
      button.addEventListener('click', function() {
        const subj_id = this.getAttribute('data-suboff-id');
        const prof_id = this.getAttribute('data-prof-id');
        if (subj_id && prof_id) {
          GETALL_TADI_RECORDS(prof_id, subj_id);
        } else {
          console.error('Missing required attributes for TADI records');
        }
      });
    });
  }
}

let selectedProfId = null;
let selectedSubjId = null;
let selectedSubDesc = null;
let selectedSubSection = null;

document.addEventListener("click", function (e) {
  if (e.target.classList.contains("instructor_Tadi_List")) {
    selectedProfId = e.target.dataset.profId;
    selectedSubjId = e.target.dataset.suboffId;
    selectedSubDesc = e.target.dataset.subDesc;
    selectedSubSection = e.target.dataset.subSect;

    document.getElementById("tadi_subj_name").innerText = selectedSubDesc;
    document.getElementById("section_name").innerText = selectedSubSection;
  }
});

function GETALL_TADI_RECORDS(prof_id, subj_id) {

  const formData = new FormData();
  formData.append('type','GETALL_TADI_RECORDS');
  formData.append('prof_id',prof_id);
  formData.append('subj_off_id',subj_id);

  fetch(`tadi/dean/controller/index-info.php`,{
    method: "POST",
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    const tbody = document.getElementById('prof_tadi_list_table');
    if (!data.length) {
      tbody.innerHTML = "<tr><td colspan='6' class='text-center'>No records found</td></tr>";
      return;
    }
    const rows = data.map(record => {
      const viewUploadCell = record.tadi_filepath
        ? `<button class="btn btn-sm w-70 viewAttch" style="background-color: #2980B9; color: white" id="viewAttch${record.schltadi_ID}" value="${record.schltadi_ID}">VIEW</button>`
        : `<span class="btn btn-sm w-70" style="background-color: #95A5A6; color: white; pointer-events: none;">No Attachment</span>`;

      const modeTypeMap = {
        'online_learning regular': 'Online Regular',
        'online_learning makeup': 'Online Make-Up',
        'onsite_learning regular': 'Onsite Regular',
        'onsite_learning makeup': 'Onsite Make-Up'
      };

      const statusConfig = record.tadi_status == 1 
        ? { text: "Verified", color: "green" }
        : { text: "Unverified", color: "red" };

      const row = document.createElement('tr');
      row.innerHTML = `
        <td class="text-center">${record.stud_name}</td>
        <td class="text-center">${record.tadi_date} ${formatTimeToAmPm(record.tadi_timeIn)} - ${formatTimeToAmPm(record.tadi_timeOut)}</td>
        <td class="text-center">${modeTypeMap[record.tadi_modeType] || record.tadi_modeType}</td>
        <td class="text-center">
          <span class="activity-text" style="cursor: pointer;">${record.tadi_act}</span>
        </td>
        <td class="text-center">
          ${viewUploadCell}
          <input type="hidden" id="imgProf_id" value="${record.SchlProf_ID}">
        </td>
        <td class="text-center">
          <span class="acknw" value="${record.schltadi_ID}" name="${record.tadi_status}" 
                style="color:${statusConfig.color}; font-weight:bold;">${statusConfig.text}</span>
        </td>`;

      const text = row.querySelector('.activity-text');
      setupActivityText(text);
      return row;
    });

    tbody.innerHTML = '';
    tbody.append(...rows);

    document.querySelectorAll('.viewAttch').forEach(button => 
      button.addEventListener('click', GET_IMAGE));
  })
  .catch(error => console.error("Error loading TADI records:", error));
}

function setupActivityText(element) {
  const initialStyle = {
    display: '-webkit-box',
    webkitLineClamp: '2',
    webkitBoxOrient: 'vertical',
    overflow: 'hidden'
  };

  Object.assign(element.style, initialStyle);
  
  element.addEventListener('click', function() {
    const isCollapsed = this.style.display === '-webkit-box';
    this.style.display = isCollapsed ? 'block' : '-webkit-box';
    this.style.webkitLineClamp = isCollapsed ? 'none' : '2';
  });
}

function GET_IMAGE(event) {
  const button = event.target;
  const tadi_id = button.value;
  const prof_id = document.getElementById("imgProf_id").value;

  const formData = new FormData();
  formData.append('type','GET_IMAGE');
  formData.append('tadi_id',tadi_id);
  formData.append('prof_id', prof_id);

  fetch(`tadi/dean/controller/index-info.php`,{
    method: "POST",
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    if (data && data.tadi_filepath) {
      const imgPrev = document.getElementById('attchPrev');
      imgPrev.src = `tadi/${data.tadi_filepath}`;

      const dateTimeUpldStr = `${data.upld_date}T${data.upld_time}`;
      const upldObj = new Date(dateTimeUpldStr);

      const optionsFullDate = { year: "numeric", month: "long", day: "numeric" };

      let takenText = "Not Available";
      if (data.exif_date && data.exif_time) {
        const dateTimeTakenStr = `${data.exif_date}T${data.exif_time}`;
        const takenObj = new Date(dateTimeTakenStr);
        const formatTakenDate = takenObj.toLocaleDateString("en-US", optionsFullDate);
        const formatTakenTime = takenObj.toLocaleTimeString("en-US", { hour: "2-digit", minute: "2-digit", hour12: true });
        takenText = formatTakenDate + " " + formatTakenTime;
      }

      const formatUpldDate = upldObj.toLocaleDateString("en-US", optionsFullDate);
      const formatUpldTime = upldObj.toLocaleTimeString("en-US", { hour: "2-digit", minute: "2-digit", hour12: true });

      const imgexDateTimeTaken = document.getElementById('dateTimeTaken');
      const imgDateTimeUpld = document.getElementById('dateTimeUpld');

      imgexDateTimeTaken.innerText = "Taken: " + takenText;
      imgDateTimeUpld.innerText = "Uploaded: " + formatUpldDate + " " + formatUpldTime;

      const imgModalEl = document.getElementById('imageModal');
      const imgModal = new bootstrap.Modal(imgModalEl, {
        backdrop: true
      });

      imgModal.show();

      const closeBtn = document.getElementById('closeModalBtn');
      closeBtn.onclick = function () {
        imgModal.hide();
        imgPrev.src = '';
      };
    } else {
      console.error("No image found for the given TADI ID.");
    }
  })
  .catch(err => console.error("Error fetching image:", err));
}

function displaySubjList(result) {

  const tableRows = result
    .map((item, index) => {
      return `<tr value="${item.SchlProf_ID}">
                <td class="col">${item.schl_sec || 'No Section'}</td>
                <td class="col">${item.subj_code}</td>
                <td class="col">${item.subj_desc}</td>
                <td class="col">
                  <button class="btn btn-sm w-100 button-bg-change vw_tadi instructor_Tadi_List" id="tadiModalSubjList${index}" 
                  data-bs-target="#Instructor_Tadi_List" 
                  data-bs-toggle="modal"
                  data-suboff-id="${item.sub_off_id}"
                  data-prof-id="${item.SchlProf_ID}"
                  data-sub-desc="${item.subj_desc}"
                  data-sub-sect="${item.schl_sec || 'No Section'}">VIEW TADI</button>
                </td>
              </tr>`;
    })
    .join('');

  document.querySelector("#subj_list").innerHTML = tableRows;
  document.querySelector(".tadi_inst_name").textContent = result[0].prof_name;

    document.querySelectorAll(".vw_tadi").forEach(btn => {
    btn.addEventListener("click", () => {
      prof_id = btn.getAttribute("data-prof-id");
      subj_id = btn.getAttribute("data-suboff-id");
      GETALL_TADI_RECORDS(prof_id, subj_id);
    });
  });
}

document.getElementById("deanDate_srch").addEventListener("click", function () {

  const prof_id = selectedProfId;
  const subj_id = selectedSubjId;
  const subj_desc = selectedSubDesc;
  const sub_sect = selectedSubSection;

  document.getElementById("tadi_subj_name").innerText = subj_desc;
  document.getElementById("section_name").innerText = sub_sect;

  if (!prof_id || !subj_id) {
    alert("Please select a subject first!");
    return;
  }

  const strtDateSearch = document.getElementById("strtDateSearch").value;
  const endDateSearch = document.getElementById("endDateSearch").value;
  const tadiStatus = document.getElementById("verification").value;

  if (!strtDateSearch && !tadiStatus) {
    alert("Fill at least one field!");
    return;
  }

  const formData = new FormData();
  formData.append('type','GET_TADI_RECORDS');
  formData.append('prof_id',prof_id);
  formData.append('subj_off_id',subj_id);
  formData.append('strtDateSearch',strtDateSearch);
  formData.append('endDateSearch',endDateSearch);
  formData.append('tadiStatus',tadiStatus);

  fetch(`tadi/dean/controller/index-info.php`,{
    method: "POST",
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    const tbody = document.getElementById('prof_tadi_list_table');
    
    if (!data.length) {
      tbody.innerHTML = "<tr><td colspan='6' class='text-center'>No records found</td></tr>";
      return;
    }

    tbody.innerHTML = '';
    
    const rows = data.map(record => {
      const viewUploadCell = record.tadi_filepath
        ? `<button class="btn btn-sm w-70 viewAttch" style="background-color: #2980B9; color: white" id="viewAttch${record.schltadi_ID}" value="${record.schltadi_ID}">VIEW</button>`
        : `<span class="btn btn-sm w-70" style="background-color: #95A5A6; color: white; pointer-events: none;">No Attachment</span>`;

      const modeTypeMap = {
        'online_learning regular': 'Online Regular',
        'online_learning makeup': 'Online Make-Up',
        'onsite_learning regular': 'Onsite Regular',
        'onsite_learning makeup': 'Onsite Make-Up'
      };

      const row = document.createElement('tr');
      row.innerHTML = `
        <td class="text-center">${record.stud_name}</td>
        <td class="text-center">${record.tadi_date} ${formatTimeToAmPm(record.tadi_timeIn)} - ${formatTimeToAmPm(record.tadi_timeOut)}</td>
        <td class="text-center">${modeTypeMap[record.tadi_modeType] || record.tadi_modeType}</td>
        <td class="text-center">
          <span class="activity-text" style="cursor: pointer;">${record.tadi_act}</span>
        </td>
        <td class="text-center">
          ${viewUploadCell}
          <input type="hidden" id="imgProf_id" value="${record.SchlProf_ID}">
        </td>
        <td class="text-center">
          <span class="acknw" value="${record.schltadi_ID}" name="${record.tadi_status}" 
                style="color:${record.tadi_status == 1 ? 'green' : 'red'}; font-weight:bold;">
            ${record.tadi_status == 1 ? 'Verified' : 'Unverified'}
          </span>
        </td>
      `;

      const text = row.querySelector('.activity-text');
      setupActivityText(text);
      return row;
    });

    tbody.append(...rows);
    document.querySelectorAll('.viewAttch').forEach(button => 
      button.addEventListener('click', GET_IMAGE));
  })
  .catch(error => console.error("Error loading TADI records:", error));
});



document.getElementById('Instructor_Tadi_List').addEventListener('hidden.bs.modal', function () {
  document.getElementById('strtDateSearch').value = '';
  document.getElementById('endDateSearch').value = '';
  document.getElementById('verification').value = '';
  
  document.getElementById('prof_tadi_list_table').innerHTML = '';
});

document.getElementById("searchSubjBtn").addEventListener("click", function() {
  let BySubjDesc = document.getElementById("BySubjDesc").value;
  let BySubjCode = document.getElementById("ByCode").value;
  let BySection = document.getElementById("BySection").value;

  if (!BySubjDesc && !BySubjCode && !BySection) {
    alert("Please enter at least one search criteria.");
    return;
  }

  const formData = new FormData();
  formData.append('type','SEARCH_SUBJECT_BY_INSTRUCTOR');
  formData.append('lvlid',subj_instr[0].lvlid);
  formData.append('prdid',subj_instr[0].prdid)
  formData.append('yrid',subj_instr[0].yrid)
  formData.append('yrlvlid',subj_instr[0].yrlvlid);
  formData.append('prof_id',subj_instr[0].SchlProf_ID)
  formData.append('subjDesc',BySubjDesc)
  formData.append('subjCode',BySubjCode);
  formData.append('section',BySection);

  fetch(`tadi/dean/controller/index-info.php`,{
    method: "POST",
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    const tbody = document.getElementById('subj_list');
    tbody.innerHTML = data.length ? "" : "<tr><td colspan='6' class='text-center'>No records found</td></tr>";

     data.forEach(record => {
      const row = document.createElement('tr');
        row.innerHTML =`
          <td>${record.schl_sec}</td>
          <td>${record.subj_code}</td>
          <td>${record.subj_desc}</td>
          <td>
            <button class="btn btn-sm w-100 button-bg-change vw_tadi instructor_Tadi_List" id="tadiModalSubjList${record.SchlProf_ID}" data-bs-target="#Instructor_Tadi_List" data-bs-toggle="modal"
            data-suboff-id="${record.sub_off_id}"
            data-prof-id="${record.SchlProf_ID}"
            data-sub-desc="${record.subj_desc}"
            data-sub-sect="${record.schl_sec || 'No Section'}">VIEW TADI</button>
          </td>`;
        tbody.appendChild(row);
     });
     document.querySelectorAll(".vw_tadi").forEach(btn => {
      btn.addEventListener("click", () => {
        prof_id = btn.getAttribute("data-prof-id");
        subj_id = btn.getAttribute("data-suboff-id");
        GETALL_TADI_RECORDS(prof_id, subj_id);
      });
    });
  })
  .catch(error => console.error("Error searching subjects by instructor:", error));
});

document.getElementById('Instructor_Subject_List').addEventListener('hidden.bs.modal', function () {
  document.getElementById('BySubjDesc').value = '';
  document.getElementById('ByCode').value = '';
  document.getElementById('BySection').value = '';

  if (subj_instr.length > 0) {
    displaySubjList(subj_instr);
  }
});

function formatTimeToAmPm(timeString) {
  const [hours, minutes] = timeString.split(":");
  let hoursInt = parseInt(hours, 10);
  const period = hoursInt >= 12 ? "PM" : "AM";
  hoursInt = hoursInt % 12 || 12;
  return `${hoursInt}:${minutes} ${period}`;
}

function formatString(str) {
  return str
    .split("_")
    .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
    .join(" ");
}

const BASE_BACKDROP = 1050;
const BASE_MODAL = 1055;

document.addEventListener("show.bs.modal", function (e) {
  const openModals = document.querySelectorAll(".modal.show").length;

  const backdrops = document.querySelectorAll(".modal-backdrop");
  if (backdrops.length) {
    backdrops[backdrops.length - 1].style.zIndex = BASE_BACKDROP + (openModals * 20);
  }

  e.target.style.zIndex = BASE_MODAL + (openModals * 20);
});

document.addEventListener("hidden.bs.modal", function (e) {
  const openModals = document.querySelectorAll(".modal.show");
  const backdrops = document.querySelectorAll(".modal-backdrop");

  if (openModals.length > 0) {
    backdrops[backdrops.length - 1].style.zIndex = BASE_BACKDROP + ((openModals.length - 1) * 20);

    const topModal = openModals[openModals.length - 1];
    topModal.focus();
  } else {
    backdrops.forEach(b => b.remove());
  }
});

// document.querySelectorAll(".inst-subj-list").forEach(el => {
//   el.addEventListener("click", function () {
//     const row = this.closest("tr"); // get the current row
//     const subjCode = row.querySelector(".bysub-subj-code").innerText; // get subj code
//     document.getElementById("subj_code").innerText = subjCode; // update <p>
//   });
// });






