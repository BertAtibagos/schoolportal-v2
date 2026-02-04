function GET_ACADEMICLEVEL() {
    let isFirstLoad = true;  // Flag to track initial load

    fetch("forms/tadi/prof/controller/index-info.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: new URLSearchParams({
            type: "GET_ACADEMIC_LEVEL"
        })
    })
    .then(res => res.json())
    .then(result => {
        let optLevel = result.length
            ? result.map(value => `<option value="${value.AcadLvl_ID}">${value.AcadLvl_Name}</option>`).join("")
            : "<option>No Academic Level Found.</option>";
        document.querySelector("#academiclevel").insertAdjacentHTML('beforeend', optLevel);

        const lvlid = document.getElementById('academiclevel');
        
        // Only trigger on first load
        if (isFirstLoad) {
            getAcademicYearLevels(lvlid.value);
            getAcademicPeriods(lvlid.value);
            isFirstLoad = false;
        }

        // Event listener for subsequent changes
        lvlid.addEventListener("change", function() {
            const lvlid = this.value;
            getAcademicYearLevels(lvlid);
            getAcademicPeriods(lvlid);
        });
    })
    .catch(err => console.error("Error fetching academic levels:", err));
}

function getAcademicYearLevels(lvlid) {
  fetch("forms/tadi/prof/controller/index-info.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded"
    },
    body: new URLSearchParams({
      type: "GET_ACADEMIC_YEAR_LEVEL",
      lvl_id: lvlid
    })
  })
  .then(res => res.json())
  .then(result => {
    const select = document.querySelector("#academicYearLevel");
    select.innerHTML = result.length
      ? result.map(value => `<option value="${value.ACAD_YRLVL_ID}">${value.ACAD_YRLVL_NAME}</option>`).join("")
      : "<option>No Year Level Found.</option>";
  })
  .catch(err => console.error("Error fetching year levels:", err));
}

function getAcademicPeriods(lvlid) {
    // Remove existing event listener first
    const periodSelect = document.querySelector("#period");
    const existingHandler = periodSelect._changeHandler;
    if (existingHandler) {
        periodSelect.removeEventListener("change", existingHandler);
    }

    fetch("forms/tadi/prof/controller/index-info.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: new URLSearchParams({
            type: "GET_ACADEMIC_PERIOD",
            lvl_id: lvlid
        })
    })
    .then(res => res.json())
    .then(result => {
        periodSelect.innerHTML = result.length
            ? result.map(value => `<option value="${value.acad_prd_id}">${value.acad_prd_name}</option>`).join("")
            : "<option>No Period Found.</option>";

        // Create new handler
        const changeHandler = function() {
            const lvlid = document.querySelector("#academiclevel").value;
            const prdid = this.value;
            getAcademicYears(lvlid, prdid, true);
        };

        // Store handler reference
        periodSelect._changeHandler = changeHandler;

        // Add new event listener
        periodSelect.addEventListener("change", changeHandler);

        // Only dispatch change event on first load
        if (!periodSelect._initialized) {
            periodSelect.dispatchEvent(new Event("change"));
            periodSelect._initialized = true;
        }
    })
    .catch(err => console.error("Error fetching periods:", err));
}

function getAcademicYears(lvlid, prdid) {
  let shouldLoadSummary = true;
  const searchButton = document.getElementById("searchButton");
  fetch("forms/tadi/prof/controller/index-info.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded"
    },
    body: new URLSearchParams({
      type: "GET_ACAD_YEAR",
      lvl_id: lvlid,
      prd_id: prdid
    })
  })
  .then(res => res.json())
  .then(result => {
    const select = document.querySelector("#acadyear");
    select.innerHTML = result.length
      ? result.map(value => `<option value="${value.Period_id}">${value.YEAR_NAME}</option>`).join("")
      : "<option>No Year Found.</option>";

    if (shouldLoadSummary) {
      tadiSummary();
      shouldLoadSummary = false;
    }
    searchButton.disabled = false;
  })
  .catch(err => console.error("Error fetching academic years:", err));
}

function searchTadiDataByDate(searchDate) {
  fetch("forms/tadi/prof/controller/index-info.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded"
    },
    body: new URLSearchParams({
      type: "SEARCH_TADI_DATA_BY_DATE",
      search_date: searchDate
    })
  })
  .then(res => res.json())
  .then(result => {
    displaySubjectTadi(result);
  })
  .catch(error => console.error("Error performing search:", error));
}

function DISPLAY_PROFESSOR_SUBJECT(result) {

  const tableRows = result.length
    ? result.reduce((acc, value, index) => {
        acc += `
          <tr key="${value.sub_off_id}">
              <td>${value.schl_sec}</td>
              <td>${value.subj_code}</td>
              <td>${value.subj_desc}</td>
              <td class="btn_tadi">
                <button class="btn btn-sm w-100 button-bg-change position-relative viewTadi" 
                  id="viewTadiRecord${index}" 
                  data-bs-toggle="modal" 
                  data-bs-target="#sectionList" 
                  name="${value.sub_off_id}">
                  VIEW TADI  <span class="badge bg-secondary ms-2">${value.total_count}</span>
				   ${value.unverified_count > 0 ? `<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">${value.unverified_count}</span>` : ''}
                </button>
              </td>
          </tr>
        `;
        return acc;
      }, "")
    : "<tr><td colspan='4'>No subjects available</td></tr>";

  const profTable = document.querySelector('.prof_dashboard_table');
  profTable.innerHTML = "";
  profTable.innerHTML = tableRows;

  document.querySelectorAll('.btn_tadi').forEach(button => {
    button.addEventListener('click', function() {
      const buttonElement = this.querySelector('button');
      const sub_off_id = buttonElement.getAttribute('name');

      DISPLAYALL_TADI_RECORDS(sub_off_id);

      const tr = this.closest('tr');

      const tds = tr.querySelectorAll('td');

      const subjName = tds[2].textContent;
      const subjCode = tds[0].textContent;

      document.getElementById('subj_name').innerHTML = subjName;
      document.getElementById('subj_code').innerHTML = subjCode;
      document.getElementById('date_srch').value = sub_off_id;
    });
  });
}

function disable_acknw_bttn() {
    document.querySelectorAll('.acknw').forEach(button => {
      const status = button.getAttribute('name');
      if (status == 1) {
        let acknowledgedText = document.createTextNode('Verified');
            let span = document.createElement('span');
            span.style.color = '#198754';
            span.style.fontWeight = 'bold';
            span.appendChild(acknowledgedText);
            button.replaceWith(span);
      }
    });
}

function GET_IMAGE(event) {
  const button = event.target;
  const tadi_id = button.value;

  const formData = new FormData();
  formData.append('type', 'GET_IMAGE');
  formData.append('tadi_id', tadi_id);

  fetch('forms/tadi/prof/controller/index-info.php', {
    method: 'POST',
    body: formData
  })
    .then(res => res.json())
    .then(data => {
      if (data && data.tadi_filepath) {
        const imgPrev = document.getElementById('attchPrev');
        imgPrev.src = `forms/tadi/${data.tadi_filepath}`;

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

function UPLOAD_IMAGE_PROF(){
  const tadiId = document.querySelector('.profUploadBtn').value;
  const fileInput = document.getElementById('attach');
  const file = fileInput.files[0];

   if (!file) {
    alert("Please select a file to upload.");
    return;
  }
  
  const formData =new FormData();
    formData.append("type", "UPLOAD_IMAGE_PROF");
    formData.append("tadi_id", tadiId); 
    formData.append("attach", file);
  
  fetch(`forms/tadi/prof/controller/index-post.php`, {
          method: "POST",
          body: formData
        })
    .then(response => response.text())
    .then(text => {
      try {
        const data = JSON.parse(text);

        if (data.success) {
          alert("Uploading Successful");

        const uploadModalEl = document.getElementById('uploadModal');
        const uploadModal = bootstrap.Modal.getInstance(uploadModalEl);
        if (uploadModal) uploadModal.hide();

        const sectionListModalEl = document.getElementById('sectionList');
        const sectionListModal = bootstrap.Modal.getOrCreateInstance(sectionListModalEl);
        sectionListModal.show();

        const viewTadi = document.querySelector('.pass').value;
        DISPLAYALL_TADI_RECORDS(viewTadi);

        } else {
          alert("Upload failed: " + (data.message || "Unknown error"));
        }

      } catch (err) {
        console.error("Failed to parse JSON:", err.message);
      }
    })
    .catch(error =>{
       console.error("Error:", error);
    })
}

function UPLOAD_IMAGE_PROF_MODAL() {
   const modalEl = document.getElementById('uploadModal');
    const imageModal = new bootstrap.Modal(modalEl);
    imageModal.show();
    const upldbtnmain = document.querySelector('.upldprof').value;
    document.querySelector('.profUploadBtn').value = upldbtnmain;

    document.querySelectorAll('.profUploadBtn').forEach(button => {
      button.addEventListener('click', UPLOAD_IMAGE_PROF);
      })

  document.getElementById('uploadcloseModalBtn').onclick = function () {
    imageModal.hide();
  };
}


function DISPLAY_TADI_LOG(subj_off_id, summary = false) {
  const strtDateSearch = document.getElementById('strtDateSearch').value;
  const endDateSearch = document.getElementById('endDateSearch').value;

  if (!strtDateSearch && endDateSearch) {
    showAlertModal("Please enter a start date");
    invalidStartDateInput();
    return;
  }
  if (!strtDateSearch && !endDateSearch) {
    showAlertModal("Please enter both start and end dates");
    invalidStartDateInput();
    invalidEndDateInput();
    return;
  }
  if(strtDateSearch > endDateSearch){
    showAlertModal("Start date must be earlier than or equal to end date");
    return;
  }
  resetStartEndDateInput();

  const formData = new FormData();
  formData.append('type', 'GET_TADI_RECORD');
  formData.append('strtDateSearch', strtDateSearch);
  formData.append('endDateSearch', endDateSearch);
  formData.append('subj_off_id', subj_off_id);
	
  const tbody = document.getElementById('rcrd_tbl_body');
  tbody.innerHTML = `<tr class="loading-spinner hide">
                                    <td colspan="4">
                                        <div class="text-center">
                                            <div class="spinner-border " role="status">
                                                <span class="sr-only"></span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>`;

  fetch('forms/tadi/prof/controller/index-info.php', {
    method: 'POST',
    body: formData
  })
    .then(response => response.json())
    .then(data => {

      tbody.innerHTML = data.length ? "" : "<tr><td colspan='6' class='text-center'>No records found</td></tr>";

      data.forEach(record => {
        const viewUploadCell = record.tadi_filepath
          ? `<button class="btn btn-sm w-70 viewAttch" id="viewAttch${record.schltadi_ID}" value="${record.schltadi_ID}">VIEW</button>`
          : `<button class="btn btn-sm w-70 upldprof" id="upldprof${record.schltadi_ID}" value="${record.schltadi_ID}">UPLOAD</button>`;

        const row = document.createElement('tr');
        row.className = record.late_status == 1 ? 'table-warning' : '';
        row.innerHTML = `
          <td>${record.tadi_date}</td>
          <td>${record.stud_name}</td>
          <td>${record.tadi_mode === 'online_learning' ? 'Online' : 
                record.tadi_mode === 'onsite_learning' ? 'Onsite' : 
                record.tadi_mode}
          </td>
          <td>${record.tadi_type}</td>
          <td>${new Date('1970-01-01T' + record.tadi_timein).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' })} - 
              ${new Date('1970-01-01T' + record.tadi_timeout).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' })}</td>
          
          <td>
            ${viewUploadCell}
            <input type="hidden" class="pass" id="pass${record.sub_off_id}" value="${record.sub_off_id}">
          </td>
          <td>
            <button class="btn acknw btn-success" value="${record.schltadi_ID}" name="${record.tadi_status}" data-subj-off="${record.sub_off_id}" data-from-summary="${summary}">Verify</button>
          </td>
        `;
        tbody.appendChild(row);
      });

      document.querySelectorAll('.viewAttch').forEach(button => {
        button.addEventListener('click', GET_IMAGE);
      });

      document.querySelectorAll('.upldprof').forEach(button => {
        button.addEventListener('click', UPLOAD_IMAGE_PROF_MODAL);
      });

      disable_acknw_bttn();
    })
    .catch(error => console.error('Error fetching data:', error));
}

function DISPLAYALL_TADI_RECORDS(subj_off_id,subjDesc = null,subjSec = null, summary = false) {
  const formData = new FormData();
  formData.append('type', 'GETALL_TADI_RECORD');
  formData.append('subj_off_id', subj_off_id);

  if(subjDesc && subjSec){
    document.getElementById('subj_name').textContent = subjDesc;
    document.getElementById('subj_code').textContent = subjSec;
    document.getElementById('date_srch').value = subj_off_id;
  }
	
  let tbody = document.getElementById('rcrd_tbl_body');
  tbody.innerHTML = `<tr class="loading-spinner hide">
                                    <td colspan="4">
                                        <div class="text-center">
                                            <div class="spinner-border " role="status">
                                                <span class="sr-only"></span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>`;

  fetch('forms/tadi/prof/controller/index-info.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    tbody.innerHTML = data.length ? "" : "<tr><td colspan='6' class='text-center'>No records found</td></tr>";

    for (let record of data) {
      let viewUploadCell = '';
      if (record.tadi_filepath) {
        viewUploadCell = `<button class="btn btn-sm btn-secondary w-70 viewAttch" id="viewAttch${record.schltadi_ID}" value="${record.schltadi_ID}">VIEW</button>`;
      } else {
        viewUploadCell = `<button class="btn btn-sm btn-dark w-70 upldprof" id="upldprof${record.schltadi_ID}" value="${record.schltadi_ID}">UPLOAD</button>`;
      }

      let row = document.createElement('tr');
      row.className = record.late_status == 1 ? 'table-warning' : '';
      row.innerHTML = `
        <td>${record.tadi_date}</td>
        <td>${record.stud_name}</td>
        <td>${record.tadi_mode === 'online_learning' ? 'Online' : 
             record.tadi_mode === 'onsite_learning' ? 'Onsite' : 
             record.tadi_mode}</td>
        <td>${record.tadi_type}</td>
        <td>${new Date('1970-01-01T' + record.tadi_timein).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' })} - 
            ${new Date('1970-01-01T' + record.tadi_timeout).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' })}</td>
        
        <td>
          ${viewUploadCell}
          <input type="hidden" class="pass" id="pass${record.sub_off_id}" value="${record.sub_off_id}">
        </td>
        <td><button class="btn acknw btn-success" value="${record.schltadi_ID}" name="${record.tadi_status}" data-subj-off="${record.sub_off_id}" data-from-summary="${summary ? 'true' : 'false'}">Verify</button></td>
      `;
      tbody.appendChild(row);
    }

    document.getElementById('date_srch').dataset.summary = summary ? "true" : "false";
    disable_acknw_bttn();

    document.querySelectorAll('.viewAttch').forEach(button => {
      button.addEventListener('click', GET_IMAGE);
    });

    document.querySelectorAll('.upldprof').forEach(button => {
      button.addEventListener('click', UPLOAD_IMAGE_PROF_MODAL);
    });
  })
  .catch(error => console.error('Error fetching data:', error));
}



function attachSubjectClickHandlers(results) {
  results.forEach((value, index) => {
    const button = document.getElementById(`viewTadiRecord${index}`);
    if (button){
      button.addEventListener("click", () => {
        const sub_off_id = button.getAttribute("name");
        getSectionList(sub_off_id);
        displayModalHeader(value);

        const modal = new bootstrap.Modal(document.getElementById("sectionList"));
        modal.show();
      });
    }
  });
}

function UPDATE_TADI_STATUS() {
  if (window.UPDATE_TADI_STATUS_initialized) return;
  window.UPDATE_TADI_STATUS_initialized = true;

  document.addEventListener('click', async function(e) {
    if (!e.target.classList.contains('acknw')) return;

    const button = e.target;
    if (!confirm('Are you sure you want to verify this record?')) return;

    try {
      button.disabled = true;
      
      const status = button.getAttribute('name');
      const tadiId = button.value;
      const row = button.closest('tr');
      const hiddenInput = row.querySelector('.pass');
      const subOffId = hiddenInput ? hiddenInput.value : null;
      const summary = button.getAttribute('data-from-summary');

      const response = await fetch('forms/tadi/prof/controller/index-post.php', {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({
          type: "UPDATE_TADI_STATUS",
          tadi_status: status,
          tadi_ID: tadiId
        })
      });

      if (!response.ok) throw new Error('Network response was not ok');
      const data = await response.json();

      // Check for session expiry
      if (data.status === 'session_expired') {
        alert('Your session has expired. Please log in again.');
        window.location.href = 'index.php'; // Redirect to login page
        return;
      }

      // Check for error message from PHP
      if (data.error) {
        alert(data.error);
        return;
      }

      // Replace button with verified text
      const span = document.createElement('span');
      span.style.cssText = 'color: #198754; font-weight: bold;';
      span.textContent = 'Verified';
      button.replaceWith(span);

      // Update unverified count if subOffId exists
      if (subOffId) {
        const countResponse = await fetch("forms/tadi/prof/controller/index-post.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: new URLSearchParams({
            type: "GET_UNVERIFIED_COUNT",
            sub_off_id: subOffId
          })
        });

        if (!countResponse.ok) {
          throw new Error('Failed to get unverified count');
        }

        const result = await countResponse.json();
        
        // Check for session expiry in count response
        if (result.status === 'session_expired') {
          alert('Your session has expired. Please log in again.');
          window.location.href = 'index.php';
          return;
        }

        if (result.error) {
          alert(result.error);
          return;
        }

        const mainTableButton = document.querySelector(`button[name="${subOffId}"]`);
        
        if (mainTableButton) {
          const badge = mainTableButton.querySelector('.badge.bg-danger');
          if (result.unverified_count > 0) {
            if (badge) {
              badge.textContent = result.unverified_count;
            } else {
              const newBadge = document.createElement('span');
              newBadge.className = 'position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger';
              newBadge.textContent = result.unverified_count;
              mainTableButton.appendChild(newBadge);
            }
          } else if (badge) {
            badge.remove();
          }
        }
      }

      if (summary === "true") {
        TOTAL_COUNT_SUMMARY();
        UPDATE_TADI_COUNT(subOffId);
      }

    } catch (error) {
      console.error("Error:", error);
      button.disabled = false;
      if (error.message.includes('session expired')) {
        alert('Session expired. Please log in again.');
        window.location.href = 'index.php';
      } else {
        alert(error.message || "An error occurred");
      }
    }
  });
}


async function tadiSummary(){
  const lvlid = document.getElementById("academiclevel").value;
  const prdid = document.getElementById("period").value;
  const yrid = document.getElementById("acadyear").value;

  if (!lvlid || !prdid ) {
      showAlertModal("Please select all the filters or enter a Subject Code before searching.");
        emptyCriteriaReport();
      return;
  }else{
      resetCriteriaReport();
  }

  const formData = new FormData();
  formData.append('type', 'GET_ALL_TADI_SUMMARY');
  formData.append('lvl_id', lvlid);
  formData.append('prd_id', prdid);
  formData.append('yr_id', yrid);


  const tbodySpinner = document.querySelector('.prof_dashboard_table');
  tbodySpinner.innerHTML =`<tr class="loading-spinner hide">
                                  <td colspan="4">
                                      <div class="text-center">
                                          <div class="spinner-border " role="status">
                                              <span class="sr-only"></span>
                                          </div>
                                      </div>
                                  </td>
                              </tr>`;
  
  TOTAL_COUNT_SUMMARY();
  
  try{
    const response = await fetch('forms/tadi/prof/controller/index-info.php',{
                method: "POST",
                body: formData
    });

    const result = await response.json();
    const dashTable = document.querySelector('.prof_dashboard_table');
    dashTable.innerHTML = result.length ? "" : "<tr><td colspan='4' class='text-center'>No subjects available</td></tr>";

    result.forEach(value=> {
      const row = document.createElement('tr');

      row.innerHTML = `<td style="font-weight: bold;" class="${value.schl_sec == null ? 'text-muted': ''}">
                        ${value.schl_sec == null ? 'No Section': value.schl_sec}
                      </td>
                      <td style="font-weight: bold;" class="${value.schl_sec == null ? 'text-muted': ''}">
                        ${value.subj_desc}
                      </td>
                      <td class="text-center">
                        <span id="total-${value.sub_off_id}" class="text-${value.schl_sec == null ? 'secondary' : 'success'}" style="font-size: 1.4rem; font-weight: bold;">
                          ${value.total_count}
                        </span>
                      </td>
                      <td class="text-center">
                        <span id="unverified-${value.sub_off_id}" class="text-${value.schl_sec == null ? 'secondary' : 'danger'}" style="font-size: 1.4rem; font-weight: bold;">
                          ${value.unverified_count}
                        </span>
                      </td>
                      <td class="text-center">
                        <button class="btn btn-sm view-tadi-summary bg-dark text-white" 
                        data-bs-toggle="modal" 
                        data-bs-target="#sectionList" 
                        data-subj-off= "${value.sub_off_id}"
                        data-subj-desc= "${value.subj_desc}"
                        data-section= "${value.schl_sec}"
                        data-summary= "true"
                        ${value.schl_sec == null ? 'disabled' : ''}>
                        VIEW
                        </button>
                      </td>`;

      dashTable.appendChild(row);
      });

      document.getElementById('date_srch').dataset.summary = "true";
      document.querySelectorAll('.view-tadi-summary').forEach(button =>{
        button.addEventListener("click", e => {
          const subjOffId = e.target.getAttribute('data-subj-off');
          const subjDesc = e.target.getAttribute('data-subj-desc');
          const subjSec = e.target.getAttribute('data-section');
          const summary = e.target.getAttribute('data-summary');

          DISPLAYALL_TADI_RECORDS(subjOffId,subjDesc,subjSec,summary);
        })
      });
      
  }catch(error){
     console.error("Error:", error);
  };
}

async function TOTAL_COUNT_SUMMARY(){

  try{
    const total_summary = await fetch('forms/tadi/prof/controller/index-info.php',{
      method: "POST",
      headers: {"Content-Type": "application/x-www-form-urlencoded"},
      body: new URLSearchParams({
        type: 'GET_TOTAL_COUNT_SUMMARY'
      })
    });

    const totalResult = await total_summary.json();

    const totalCount = document.getElementById('totalCount');
    const totalUnverified = document.getElementById('totalUnverified');
    const totalVerified = document.getElementById('totalVerified');

    totalCount.textContent = totalResult.total_count;
    totalUnverified.textContent = totalResult.total_unverified;
    totalVerified.textContent = totalResult.verified_count;
  }catch(error){
    console.error("Error:", error);
  }
}


async function UPDATE_TADI_COUNT(subjOff){

  try{
    const count = await fetch(`forms/tadi/prof/controller/index-info.php`,{
      method: "POSt",
      headers: {"Content-Type": "application/x-www-form-urlencoded"},
      body: new URLSearchParams({
        type: 'UPDATE_SUBJECT_COUNT',
        sub_off_id: subjOff
      })
    });

    const result = await count.json();

    const total = document.getElementById('total-'+subjOff);
    const unverified = document.getElementById('unverified-'+subjOff);

    total.textContent = result.total_count;
    unverified.textContent = result.total_unverified;
  }
  catch(error){
    console.error("Error:", error);
  }
}

document.getElementById('summaryTadiBtn').addEventListener("click",()=>{
        displaySummary();
        document.getElementById('summaryTadiBtn').style.display = 'none';
    })
