function shouldLoadSummary(load, isSearch = false) {
    let summaryLoad = true;

    if (isSearch) {
      summaryLoad = false;
    }else{
      if(load){
        summaryLoad = true;
      }
    }
    return summaryLoad;
}

  let skipTadiSummaryAutoLoad = false;

function GET_ACADEMICLEVEL() {
    let isFirstLoad = true;

    fetch("forms/tadi/prof/controller/index-info.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: new URLSearchParams({
            type: "GET_ACADEMIC_LEVEL"
        })
    })
    .then(handleRateLimitJson)
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
  .then(handleRateLimitJson)
  .then(result => {
    const select = document.querySelector("#academicYearLevel");
    select.innerHTML = result.length
      ? result.map(value => `<option value="${value.ACAD_YRLVL_ID}">${value.ACAD_YRLVL_NAME}</option>`).join("")
      : "<option>No Year Level Found.</option>";
  })
  .catch(err => console.error("Error fetching year levels:", err));
}

function getAcademicPeriods(lvlid) {
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
    .then(handleRateLimitJson)
    .then(result => {
        periodSelect.innerHTML = result.length
            ? result.map(value => `<option value="${value.acad_prd_id}" ${value.is_current == 1 ? "selected" : ""}>${value.acad_prd_name}</option>`).join("")
            : "<option>No Period Found.</option>";

        const changeHandler = function() {
            const lvlid = document.querySelector("#academiclevel").value;
            const prdid = this.value;
            getAcademicYears(lvlid, prdid, true);
        };

        periodSelect._changeHandler = changeHandler;

        periodSelect.addEventListener("change", changeHandler);

        if (!periodSelect._initialized) {
            periodSelect.dispatchEvent(new Event("change"));
            periodSelect._initialized = true;
        }
    })
    .catch(err => console.error("Error fetching periods:", err));
}

function getAcademicYears(lvlid, prdid) {
  let LoadSummary = shouldLoadSummary(true, skipTadiSummaryAutoLoad);
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
  .then(handleRateLimitJson)
  .then(result => {
    const select = document.querySelector("#acadyear");
    select.innerHTML = result.length
      ? result.map(value => `<option value="${value.Period_id}">${value.YEAR_NAME}</option>`).join("")
      : "<option>No Year Found.</option>";

    if (LoadSummary) {
      tadiSummary();
      TOTAL_COUNT_SUMMARY(prdid);
      skipTadiSummaryAutoLoad = false;
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
  .then(handleRateLimitJson)
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

function showToastMessage(message, variant = "success", title = "Success") {
    const toastEl = document.getElementById("successToast");
    if (!toastEl) {
        alert(message);
        return;
    }

    const headerEl = document.getElementById("toastHeader");
    const titleEl = document.getElementById("toastTitle");
    const closeBtn = document.getElementById("toastCloseBtn");

    if (headerEl) {
        headerEl.classList.remove("bg-success", "bg-warning", "text-white", "text-dark");
        if (variant === "warning") {
            headerEl.classList.add("bg-warning", "text-dark");
        } else {
            headerEl.classList.add("bg-success", "text-white");
        }
    }

    if (titleEl) {
        titleEl.textContent = title;
    }

    if (closeBtn) {
        closeBtn.classList.remove("btn-close-white");
        if (variant !== "warning") {
            closeBtn.classList.add("btn-close-white");
        }
    }

    document.getElementById("toastMessage").textContent = message;
    const toast = new bootstrap.Toast(toastEl);
    toast.show();
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
    .then(handleRateLimitJson)
    .then(data => {
      if (!data) {
            return;
        }
      if (data && data.starttadi_filepath && data.endtadi_filepath) {
        if (!isSafeAttachmentPath(data.starttadi_filepath) || !isSafeAttachmentPath(data.endtadi_filepath)) {
                showToastMessage("Invalid image path.", "warning", "Notice");
                return;
            }
        const startimgPrev = document.getElementById('start_attchPrev');
        const endimgPrev = document.getElementById('end_attchPrev');
        startimgPrev.src = `/schoolportal-v2/dev/public/${data.starttadi_filepath}`;
        endimgPrev.src = `/schoolportal-v2/dev/public/${data.endtadi_filepath}`;

        const carousel = bootstrap.Carousel.getOrCreateInstance(
            document.getElementById("imageCarousel"),
            {
                interval: false,
                ride: false,
                wrap: true
            }
        );
        carousel.to(0);

        const dateTimeUpldStr = `${data.upld_date}T${data.upld_time}`;
        const upldObj = new Date(dateTimeUpldStr);

        const optionsFullDate = { year: "numeric", month: "long", day: "numeric" };

        let starttakenText = "Not Available";
        let endtakenText = "Not Available";
        if (data.startexif_date && data.startexif_time) {
          const startdateTimeTakenStr = `${data.startexif_date}T${data.startexif_time}`;
          const enddateTimeTakenStr = `${data.endexif_date}T${data.endexif_time}`;
          const starttakenObj = new Date(startdateTimeTakenStr);
          const endtakenObj = new Date(enddateTimeTakenStr);
          const startformatTakenDate = starttakenObj.toLocaleDateString("en-US", optionsFullDate);
          const startformatTakenTime = starttakenObj.toLocaleTimeString("en-US", { hour: "2-digit", minute: "2-digit", hour12: true });
          const endformatTakenDate = endtakenObj.toLocaleDateString("en-US", optionsFullDate);
          const endformatTakenTime = endtakenObj.toLocaleTimeString("en-US", { hour: "2-digit", minute: "2-digit", hour12: true });
          starttakenText = startformatTakenDate + " " + startformatTakenTime;
          endtakenText = endformatTakenDate + " " + endformatTakenTime;
        }

        const formatUpldDate = upldObj.toLocaleDateString("en-US", optionsFullDate);
        const formatUpldTime = upldObj.toLocaleTimeString("en-US", { hour: "2-digit", minute: "2-digit", hour12: true });

        const imgexStartDateTimeTaken = document.getElementById('startdateTimeTaken');
        const imgexEndDateTimeTaken = document.getElementById('enddateTimeTaken');
        const imgDateTimeUpld = document.getElementById('dateTimeUpld');

        imgexStartDateTimeTaken.innerText = "Start of class taken: " + starttakenText;
        imgexEndDateTimeTaken.innerText = "End of class taken: " +  endtakenText;
        imgDateTimeUpld.innerText = "Uploaded: " + formatUpldDate + " " + formatUpldTime;
        
        const imgModalEl = document.getElementById('imageModal');
        const imgModal = new bootstrap.Modal(imgModalEl, {
          backdrop: true
        });

        imgModal.show();

        const closeBtn = document.getElementById('closeModalBtn');
        closeBtn.onclick = function () {
          imgModal.hide();
          startimgPrev.src = '';
          endimgPrev.src = '';
          carousel.to(0);
        };
      } else {
        console.error("No image found for the given TADI ID.");
      }
    })
    .catch(err => console.error("Error fetching image:", err));
}

function isSafeAttachmentPath(value) {
    if (typeof value !== "string") {
        return false;
    }

    if (value.includes("..") || value.includes("\\") || value.includes(":")) {
        return false;
    }

    return /^attachment\/[A-Za-z0-9._-]+\/\d{4}-\d{2}-\d{2}\/[A-Za-z0-9._-]+$/.test(value);
}
// function UPLOAD_IMAGE_PROF(){
//   const tadiId = document.querySelector('.profUploadBtn').value;
//   const fileInput = document.getElementById('attach');
//   const file = fileInput.files[0];

//    if (!file) {
//     alert("Please select a file to upload.");
//     return;
//   }
  
//   const formData =new FormData();
//     formData.append("type", "UPLOAD_IMAGE_PROF");
//     formData.append("tadi_id", tadiId); 
//     formData.append("attach", file);
  
//   fetch(`forms/tadi/prof/controller/index-post.php`, {
//           method: "POST",
//           body: formData
//         })
//     .then(response => response.text())
//     .then(text => {
//       try {
//         const data = JSON.parse(text);

//         if (data.success) {
//           alert("Uploading Successful");

//         const uploadModalEl = document.getElementById('uploadModal');
//         const uploadModal = bootstrap.Modal.getInstance(uploadModalEl);
//         if (uploadModal) uploadModal.hide();

//         const sectionListModalEl = document.getElementById('sectionList');
//         const sectionListModal = bootstrap.Modal.getOrCreateInstance(sectionListModalEl);
//         sectionListModal.show();

//         const viewTadi = document.querySelector('.pass').value;
//         DISPLAYALL_TADI_RECORDS(viewTadi);

//         } else {
//           alert("Upload failed: " + (data.message || "Unknown error"));
//         }

//       } catch (err) {
//         console.error("Failed to parse JSON:");
//       }
//     })
//     .catch(error =>{
//        console.error("Error");
//     })
// }

// function UPLOAD_IMAGE_PROF_MODAL() {
//    const modalEl = document.getElementById('uploadModal');
//     const imageModal = new bootstrap.Modal(modalEl);
//     imageModal.show();
//     const upldbtnmain = document.querySelector('.upldprof').value;
//     document.querySelector('.profUploadBtn').value = upldbtnmain;

//     document.querySelectorAll('.profUploadBtn').forEach(button => {
//       button.addEventListener('click', UPLOAD_IMAGE_PROF);
//       })

//   document.getElementById('uploadcloseModalBtn').onclick = function () {
//     imageModal.hide();
//   };
// }

function parseDateOnly(s) {
  if (!s) return null;
  // Parse strings like YYYY-MM-DD (ignore any time part) as local date
  const m = String(s).match(/^(\d{4})-(\d{2})-(\d{2})/);
  if (m) {
    return new Date(Number(m[1]), Number(m[2]) - 1, Number(m[3]));
  }
  // If it's a numeric epoch string, use it
  if (/^\d+$/.test(String(s))) {
    return new Date(Number(s));
  }
  // Fallback to Date constructor
  return new Date(s);
}

function disable_due_verify_button(date) {
  const inputDate = parseDateOnly(date);
  if (!inputDate || isNaN(inputDate.getTime())) return false; // invalid date -> don't disable

  const today = new Date();
  today.setHours(0, 0, 0, 0);

  const maxPastDays = 3;
  const pastLimit = new Date(today);
  pastLimit.setDate(today.getDate() - maxPastDays);

  inputDate.setHours(0, 0, 0, 0);

  return inputDate < pastLimit; // true = should be disabled
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
                                    <td colspan="8">
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
    .then(handleRateLimitJson)
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
          <td>${record.mkup_date === null ? '--' : record.mkup_date}</td>
          <td>${new Date('1970-01-01T' + record.tadi_timein).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' })} - 
              ${new Date('1970-01-01T' + record.tadi_timeout).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' })}</td>
          
          <td>
            ${viewUploadCell}
            <input type="hidden" class="pass" id="pass${record.sub_off_id}" value="${record.sub_off_id}">
          </td>
          <td>
            <button class="btn acknw btn-success" 
              value="${record.schltadi_ID}" 
              name="${record.tadi_status}" 
              data-subj-off="${record.sub_off_id}" 
              data-from-summary="${summary}" 
              data-approved="${record.approve}">
                Verify
              </button>
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
                                    <td colspan="8">
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
  .then(handleRateLimitJson)
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
        <td>${record.mkup_date === null ? '--' : record.mkup_date}</td>
        <td>${new Date('1970-01-01T' + record.tadi_timein).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' })} - 
            ${new Date('1970-01-01T' + record.tadi_timeout).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' })}</td>
        
        <td>
          ${viewUploadCell}
          <input type="hidden" class="pass" id="pass${record.sub_off_id}" value="${record.sub_off_id}">
        </td>
        <td><button class="btn acknw" 
            value="${record.schltadi_ID}" 
            name="${record.tadi_status}" 
            data-subj-off="${record.sub_off_id}" 
            data-from-summary="${summary ? 'true' : 'false'}"
            data-approved="${record.approve}"
            data-period="${record.acad_prd_id}"
            data-due-verify="${disable_due_verify_button(record.tadi_date) ? 'true' : 'false'}">
              Verify
            </button>
        </td>
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
      const approve = button.getAttribute('data-approved');
      const period = button.getAttribute('data-period');
      const subjId = button.getAttribute('data-subj-off');

      const response = await fetch('forms/tadi/prof/controller/index-post.php', {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({
          type: "UPDATE_TADI_STATUS",
          tadi_status: status,
          tadi_ID: tadiId,
          sub_off_id: subOffId,
        })
      });

      if (!response.ok) throw new Error('Network response was not ok');
      const data = await handleRateLimitJson(response);

      if (data.status === 'session_expired') {
        alert('Your session has expired. Please log in again.');
        window.location.href = 'index.php'; 
        return;
      }

      if (data.error) {
        console.log(data.error);
        return;
      }

      const span = document.createElement('span');
      span.className = 'tadi-badge tadi-badge-pending';
      span.style.whiteSpace = 'nowrap';
      span.style.fontWeight = 'bold';
      span.style.color = '#eed038';
      span.textContent = 'Pending Approval';
      button.replaceWith(span);

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

        const result = await handleRateLimitJson(countResponse);
        
        if (result.status === 'session_expired') {
          alert('Your session has expired. Please log in again.');
          window.location.href = 'index.php';
          return;
        }

        if (result.error) {
          console.log(result.error);
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
        TOTAL_COUNT_SUMMARY(period);
        UPDATE_TADI_COUNT(subOffId);
      }

    } catch (error) {
      console.error("Error:", error);
      button.disabled = false;
      if (error.message.includes('session expired')) {
        alert('Session expired. Please log in again.');
        window.location.href = 'index.php';
      } else {
        alert("An error occurred");
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
                                  <td colspan="5">
                                      <div class="text-center">
                                          <div class="spinner-border " role="status">
                                              <span class="sr-only"></span>
                                          </div>
                                      </div>
                                  </td>
                              </tr>`;
  
  TOTAL_COUNT_SUMMARY(prdid);
  
  try{
    const response = await fetch('forms/tadi/prof/controller/index-info.php',{
                method: "POST",
                body: formData
    });

    const result = await handleRateLimitJson(response);
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
                        <span id="overdue-${value.sub_off_id}" class="text-${value.schl_sec == null ? 'secondary' : 'secondary-emphasis'}" style="font-size: 1.4rem; font-weight: bold;">
                          ${value.overdue_count}
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

async function TOTAL_COUNT_SUMMARY(prd){
  try{
    const total_summary = await fetch('forms/tadi/prof/controller/index-info.php',{
      method: "POST",
      headers: {"Content-Type": "application/x-www-form-urlencoded"},
      body: new URLSearchParams({
        type: 'GET_TOTAL_COUNT_SUMMARY',
        prd_id: prd
      })
    });

    const totalResult = await total_summary.json();

    const totalCount = document.getElementById('totalCount');
    const totalUnverified = document.getElementById('totalUnverified');
    const totalVerified = document.getElementById('totalVerified');
    const totalOverDue = document.getElementById('totalDue');

    totalCount.textContent = totalResult.total_count;
    totalUnverified.textContent = totalResult.total_unverified;
    totalVerified.textContent = totalResult.verified_count;
    totalOverDue.textContent = totalResult.total_overdue;
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

document.getElementById("searchButton").addEventListener("click", function () {
  skipTadiSummaryAutoLoad = true;
  const lvlid = document.getElementById("academiclevel").value;
  const yrlvlid = document.getElementById("academicYearLevel").value;
  const prdid = document.getElementById("period").value;
  const yrid = document.getElementById("acadyear").value;
  const searchQuery = document.getElementById("subjectSearch").value;

  const dashBoardReturn = document.getElementById('summaryTadiBtn');
  dashBoardReturn.style.display = 'block';
  
  if ((!lvlid || !yrlvlid || !prdid || !yrid) && !searchQuery) {
      showAlertModal("Please select all the filters or enter a Subject Code before searching.");
        emptyCriteriaReport();
      return;
  }else{
      resetCriteriaReport();
  }

  const formData = new FormData();
  formData.append('type', 'GET_SUBJECT_LIST');
  formData.append('lvl_id', lvlid);
  formData.append('yrlvl_id', yrlvlid);
  formData.append('prd_id', prdid);
  formData.append('yr_id', yrid);
  formData.append('search', searchQuery);

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

  const thead = document.getElementById('theadTable');
  thead.innerHTML = '';
  thead.innerHTML = `<tr id="searchResultHeader" >
                      <th scope="col" style="background-color: #181a46; color: white;">Section</th>
                      <th scope="col" style="background-color: #181a46; color: white;">Subject Code</th>
                      <th scope="col" style="background-color: #181a46; color: white;">Description</th>
                      <th scope="col" style="background-color: #181a46; color: white;"></th>
                  </tr>`;

      const summary = document.querySelector('.summary');
      const tableWrapper = document.querySelector('.inst_list_tbl_wrapper');
      tableWrapper.classList.remove('dashboard');
      summary.classList.add("summary-hide");
      document.getElementById('summaryId').style.display = 'none';

  fetch('forms/tadi/prof/controller/index-info.php', {
      method: 'POST',
      body: formData
  })
  .then(handleRateLimitJson)
  .then((result) => {
      DISPLAY_PROFESSOR_SUBJECT(result);
  })
  .catch((err) => console.error("Fetch error:", err));
});

GET_ACADEMICLEVEL();

UPDATE_TADI_STATUS();