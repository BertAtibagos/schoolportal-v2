GETACADEMICLEVEL();

function GETYEARLVL(){
    const lvlid = document.getElementById('academiclevel').value;

    const formData = new FormData();
    formData.append('type', 'GET_ACADEMIC_YEAR_LEVEL');
    formData.append('lvl_id', lvlid)

    fetch(`forms/tadi/dean/controller/index-info.php`, {
        method: "POST",
        body: formData
    })
        .then(res => res.json())
        .then(result => {
            let optYearLevel = result.length
                ? result.map(value => `<option value="${value.ACAD_YRLVL_ID}">${value.ACAD_YRLVL_NAME}</option>`).join("")
                : "<option>No Year Level Found.</option>";
            document.getElementById("academicyearlevel").innerHTML = optYearLevel;
        });

    const formData1 = new FormData();
    formData1.append('type', 'GET_ACADEMIC_PERIOD');
    formData1.append('lvl_id', lvlid);

    fetch(`forms/tadi/dean/controller/index-info.php`, {
        method: "POST",
        body: formData1
    })
        .then(res => res.json())
        .then(result => {
            let optPeriod = result.length
                ? result.map(value => `<option value="${value.acad_prd_id}" ${value.is_current == 1 ? "selected" : ""}>${value.acad_prd_name}</option>`).join("")
                : "<option>No Period Found.</option>";
            document.getElementById("academicperiod").innerHTML = optPeriod;

            document.getElementById("academicperiod").dispatchEvent(new Event("change"));
        });
};

document.getElementById("academicperiod").addEventListener("change", function () {
    const lvlid = document.getElementById("academiclevel").value;
    const prdid = this.value;

    const formData = new FormData();
    formData.append('type', 'GET_ACAD_YEAR');
    formData.append('lvl_id', lvlid);
    formData.append('prd_id', prdid)

    fetch(`forms/tadi/dean/controller/index-info.php`, {
        method: "POST",
        body: formData
    })
        .then(res => res.json())
        .then(result => {
            let optYear = result.length
                ? result.map(value => `<option value="${value.SchlAcadYrSms_ID}">${value.YEAR_NAME}</option>`).join("")
                : "<option>No Year Found.</option>";
            document.getElementById("acadyear").innerHTML = optYear;
        });
});

document.getElementById("search_button").addEventListener("click", function () {
    const lvlid = document.getElementById("academiclevel").value;
    const yrlvlid = document.getElementById("academicyearlevel").value;
    const prdid = document.getElementById("academicperiod").value;
    const yrid = document.getElementById("acadyear").value;

    if(!lvlid || !yrlvlid || !prdid || !yrid){
        showAlertModal("Please select all filters to generate the report");
        emptyCriteriaReport();
        return;
    }else{
        resetCriteriaReport();
    }

    const formData1 = new FormData();
    formData1.append('type', 'GET_INSTRUCTOR_LIST');
    formData1.append('lvl_id', lvlid);
    formData1.append('prd_id', prdid);
    formData1.append('yr_id', yrid);
    formData1.append('yrlvl_id', yrlvlid);

    const tbodySpinner = document.getElementById('instructor');
    tbodySpinner.innerHTML = `<tr><td colspan="2"><div class="text-center p-3"><div class="spinner-border" role="status"><span class="visually-hidden"></span></div></div></td></tr>`;
    
    const srchBtn = document.getElementById("search_button");
    const genReportBtn = document.getElementById("exportBtn"); 
    srchBtn.disabled = true;
    genReportBtn.disabled = true;


    fetch(`forms/tadi/dean/controller/index-info.php`, {
        method: "POST",
        body: formData1
    })
        .then(handleRateLimitJson)
        .then(result => {
            const tableRows = result.length
                ? result.map((item, index) => `
                    <tr>
                        <td>${item.prof_name ? item.prof_name : '<span class="text-muted fst-italic">No instructor</span>'}</td>
                        <td class="tc">
                            <button class="tadi-btn tadi-btn-ghost tadi-btn-sm position-relative" ${item.prof_name ? '' : 'disabled'}
                                id="instructorModalHandler${index}" data-bs-toggle="modal" data-bs-target="#Instructor_Subject_List">
                                <i class="fas fa-list-ul me-1"></i> Section List
                                ${item.unverified_count > 0 ? '<span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle" style="width:10px;height:10px;"></span>' : ''}
                            </button>
                        </td>
                    </tr>
                `).join('')
                : `<tr><td colspan="2" class="tadi-empty-state"><i class="fas fa-inbox d-block mb-2" style="font-size:1.6rem;opacity:.35"></i>No data available.</td></tr>`;

            document.getElementById("instructor").innerHTML = tableRows;

            result.forEach((value, index) => {
                document.getElementById(`instructorModalHandler${index}`)?.addEventListener("click", function () {
                    GET_SUBJECT_BY_INSTRUCTOR(value);
                });
            });
        })
        .catch(err => console.error("Error:", err))
        .finally(()=>{
            srchBtn.disabled = false;
            genReportBtn.disabled = false;
        });
});

async function approveTadiRequest(tadiId, profId, subjId) {
    if(confirm("Are you sure you want to approve this TADI request?") == true){
        document.querySelectorAll(".approve").forEach(btn => btn.disabled = true);
        const formData = new FormData();
        formData.append('type', 'APPROVE_TADI_REQUEST');
        formData.append('tadi_id', tadiId);
        formData.append('prof_id', profId);
        formData.append('subj_id', subjId);

        try{
            const resquest = await fetch(`forms/tadi/dean/controller/index-info.php`, {
                method: "POST",
                body: formData
            });

            const respond = await resquest.json();

            if(respond.status === 'success'){
                const currentProfId = profId;
                const currentSubjId = subjId;
                GETALL_TADI_RECORDS(currentProfId, currentSubjId);
            }else{
                showAlertModal("Failed to approve TADI request. Please try again.");
            }
        }catch(err){
            console.error("Error:", err);
            showAlertModal("An error occurred while processing the request. Please try again.");
        }
    }else{
        document.querySelectorAll(".approve").forEach(btn => btn.disabled = false);
        return;
    }
}

async function rejectTadiRequest(tadiId, profId, subjId) {
    if(confirm("Are you sure you want to reject this TADI?") == true){
        document.querySelectorAll(".reject").forEach(btn => btn.disabled = true);
        const formData = new FormData();
        formData.append('type', 'REJECT_TADI_REQUEST');
        formData.append('tadi_id', tadiId);
        formData.append('prof_id', profId);
        formData.append('subj_id', subjId);

        try{
            const resquest = await fetch(`forms/tadi/dean/controller/index-info.php`, {
                method: "POST",
                body: formData
            });

            const respond = await resquest.json();

            if(respond.status === 'success'){
                const currentProfId = profId;
                const currentSubjId = subjId;
                GETALL_TADI_RECORDS(currentProfId, currentSubjId);
            }else{
                showAlertModal("Failed to reject TADI request. Please try again.");
            }
        }catch(err){
            console.error("Error:", err);
            showAlertModal("An error occurred while processing the request. Please try again.");
        }
    }else{
        document.querySelectorAll(".reject").forEach(btn => btn.disabled = false);
        return;
    }
}

function GETACADEMICLEVEL() {
  const formData = new FormData();
  formData.append('type', 'GET_ACADEMIC_LEVEL');

  fetch("forms/tadi/dean/controller/index-info.php", { method: "POST", body: formData })
    .then(handleRateLimitJson)
    .then(result => {
      const select = document.querySelector("#academiclevel");
      select.innerHTML = result.length
        ? result.map(v => `<option value="${v.SchlAcadLvl_ID}">${v.SchlAcadLvl_NAME}</option>`).join("")
        : `<option>No Academic Level Found.</option>`;

        if(result.length){
              GETYEARLVL()
        }
    })
    .catch(err => console.error("Error fetching academic level:", err));
}

function GET_SUBJECT_BY_INSTRUCTOR({ single_prof_id }) {
  const lvlid = document.getElementById("academiclevel").value;
  const yrlvlid = document.getElementById("academicyearlevel").value;
  const prdid = document.getElementById("academicperiod").value;
  const yrid = document.getElementById("acadyear").value;

  const formData = new FormData();
  formData.append('type', 'GET_SUBJECT_BY_INSTRUCTOR');
  formData.append('prof_id', single_prof_id);
  formData.append('lvl_id', lvlid);
  formData.append('prd_id', prdid);
  formData.append('yr_id', yrid);
  formData.append('yrlvl_id', yrlvlid);

  const tbody = document.getElementById('subj_list');
  tbody.innerHTML = loadingRow(4);

  fetch("forms/tadi/dean/controller/index-info.php", { method: "POST", body: formData })
    .then(handleRateLimitJson)
    .then(result => {
      displaySubjList(result);
      tbody.dataset.source = JSON.stringify(result); // Cache inside DOM instead of global var
    })
    .catch(err => console.error("Error fetching instructor subjects:", err));
}

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

function dynamic_button(tadiId, profId, subjId, approve, date) {

    const isPastDue = disable_due_verify_button(date);
    
    if(isPastDue && approve === 0){
        return `<td><span class="tadi-badge tadi-badge-pastDue"><i class="fas fa-exclamation-triangle"></i> Overdue</span></td>`
    }else if(approve === 0){
       return `<td>
                <button class="tadi-btn tadi-btn-approve approve"
                    value="${tadiId}"
                    data-prof="${profId}"
                    data-subj-id="${subjId}">
                     Approve
                </button>
                <button class="tadi-btn tadi-btn-return reject"
                    value="${tadiId}"
                    data-prof="${profId}"
                    data-subj-id="${subjId}">
                     Reject
                </button>
                </td>`;
    }else if(approve === 1){
        return `<td><span class="tadi-badge tadi-badge-approved"><i class="fas fa-check-circle"></i> Approved</span></td>`
    }
}

function GETALL_TADI_RECORDS(prof_id, subj_id) {
  const tbody = document.getElementById('prof_tadi_list_table');
  tbody.innerHTML = loadingRow(8);

  const formData = new FormData();
  formData.append('type', 'GETALL_TADI_RECORDS');
  formData.append('prof_id', prof_id);
  formData.append('subj_off_id', subj_id);

  fetch("forms/tadi/dean/controller/index-info.php", { method: "POST", body: formData })
    .then(handleRateLimitJson)
    .then(data => {
      if (!data.length) {
        tbody.innerHTML = "<tr><td colspan='8' class='text-center'>No records found</td></tr>";
        return;
      }

      tbody.innerHTML = data.map(record => {
        const activity = record.tadi_act.replace(/\\r\\n/g, "<br>");
        const viewBtn = record.tadi_filepath
          ? `<button class="tadi-btn tadi-btn-view viewAttch" value="${record.schltadi_ID}" data-prof="${record.SchlProf_ID}"><i class="fas fa-eye"></i> View</button>`
          : `<span class="tadi-badge tadi-badge-muted" style="pointer-events:none;">No Attachment</span>`;

        const modeTypeMap = {
          'online_learning regular': 'Online Regular',
          'online_learning makeup': 'Online Make-Up',
          'onsite_learning regular': 'Onsite Regular',
          'onsite_learning makeup': 'Onsite Make-Up'
        };

        const status = record.tadi_status == 1
          ? `<span class="tadi-badge tadi-badge-success"><i class="fas fa-check-circle"></i> Verified</span>`
          : `<span class="tadi-badge tadi-badge-danger"><i class="fas fa-times-circle"></i> Unverified</span>`;

        const rowClass = record.late_status == 1 ? 'tadi-row-late' : '';
        const lateBadge = record.late_status == 1 ? `<br><span class="tadi-badge tadi-badge-warning mt-1"><i class="fas fa-clock"></i> Late</span>` : '';
        return `
          <tr class="text-center ${rowClass}">
            <td>${record.stud_name}${lateBadge}</td>
            <td>${record.tadi_date}<br><small class="text-muted">${formatTimeToAmPm(record.tadi_timeIn)} &mdash; ${formatTimeToAmPm(record.tadi_timeOut)}</small></td>
            <td><span class="tadi-badge tadi-badge-primary">${modeTypeMap[record.tadi_modeType] || record.tadi_modeType}</span></td>
            <td>${record.mkup_date === null ? '<span class="text-muted">&#8212;</span>' : record.mkup_date}</td>
            <td><span class="activity-text">${activity}</span></td>
            <td>${status}</td>
            <td>${viewBtn}</td>
            ${dynamic_button(record.schltadi_ID, record.SchlProf_ID, record.sub_off_id, record.approved, record.date_approved)}
          </tr>`;
      }).join('');

      tbody.querySelectorAll(".viewAttch").forEach(btn =>
        btn.addEventListener("click", e => GET_IMAGE(e.currentTarget.value, e.currentTarget.dataset.prof))
      );

      tbody.querySelectorAll(".approve").forEach(btn =>
        btn.addEventListener("click", e => {
          const tadiId = e.currentTarget.value;
          const profId = e.currentTarget.dataset.prof;
          const subjId = e.currentTarget.dataset.subjId;
          approveTadiRequest(tadiId, profId, subjId);
        })
      );

      tbody.querySelectorAll(".reject").forEach(btn =>
        btn.addEventListener("click", e => {
          const tadiId = e.currentTarget.value;
          const profId = e.currentTarget.dataset.prof;
          const subjId = e.currentTarget.dataset.subjId;
          rejectTadiRequest(tadiId, profId, subjId);
        })
      );

      tbody.querySelectorAll(".activity-text").forEach(setupActivityText);
    })
    .catch(err => console.error("Error loading TADI records:", err));
}

function isSafeAttachmentPath(value) {
    if (typeof value !== "string") {
        return false;
    }

    if (value.includes("..") || value.includes("\\") || value.includes(":")) {
        return false;
    }

    return /^attachment\/[A-Za-z0-9._-]+\/\d{4}-\d{2}-\d{2}(?:\/[A-Za-z0-9._-]+)?\/[A-Za-z0-9._-]+$/.test(value);
}

function GET_IMAGE(tadi_id, prof_id) {
  const formData = new FormData();
  formData.append('type', 'GET_IMAGE');
  formData.append('tadi_id', tadi_id);
  formData.append('prof_id', prof_id);

  fetch("forms/tadi/dean/controller/index-info.php", { method: "POST", body: formData })
    .then(handleRateLimitJson)
    .then(data => {
        if (!data) {
            return;
        }
        if (!data || !isSafeAttachmentPath(data.starttadi_filepath) || !isSafeAttachmentPath(data.endtadi_filepath)) {
                showToastMessage("Invalid image path.", "warning", "Notice");
                return;
        }
        document.getElementById("start_attchPrev").src =`/schoolportal-v2/dev/public/${data.starttadi_filepath}`;
        document.getElementById("end_attchPrev").src =`/schoolportal-v2/dev/public/${data.endtadi_filepath}`;
        showImageModal(data);
    })
    .catch(err => console.error("Error fetching image:", err));
}

document.getElementById("searchSubjBtn").addEventListener("click", function() {
  let BySubjDesc = document.getElementById("BySubjDesc").value;
  let BySubjCode = document.getElementById("ByCode").value;
  let BySection = document.getElementById("BySection").value;

  if (!BySubjDesc && !BySubjCode && !BySection) {
    errorMessageBox("Please enter at least one search criteria.");
    return;
  }

  const tbody = document.getElementById('subj_list');
  const cachedData = JSON.parse(tbody.dataset.source || '[]');
  
  if (!cachedData.length) {
    showAlertModal("No subject data available");
    return;
  }else{
    clearErrorMessageBox();
  }

  const formData = new FormData();
  formData.append('type', 'SEARCH_SUBJECT_BY_INSTRUCTOR');
  formData.append('lvlid', cachedData[0].lvlid);
  formData.append('prdid', cachedData[0].prdid);
  formData.append('yrid', cachedData[0].yrid);
  formData.append('yrlvlid', cachedData[0].yrlvlid);
  formData.append('prof_id', cachedData[0].SchlProf_ID);
  formData.append('subjDesc', BySubjDesc);
  formData.append('subjCode', BySubjCode);
  formData.append('section', BySection);

  fetch(`forms/tadi/dean/controller/index-info.php`, {
    method: "POST",
    body: formData
  })
  .then(handleRateLimitJson)
  .then(data => {
    tbody.innerHTML = data.length ? "" : "<tr><td colspan='6' class='text-center'>No records found</td></tr>";

    data.forEach(record => {
      const row = document.createElement('tr');
      row.innerHTML = `
        <td>${record.schl_sec || 'No Section'}</td>
        <td>${record.subj_code}</td>
        <td>${record.subj_desc}</td>
        <td>
          <button 
            class="tadi-btn tadi-btn-ghost tadi-btn-sm w-100 position-relative vw_tadi" 
            data-bs-target="#Instructor_Tadi_List" 
            data-bs-toggle="modal"
            data-prof-id="${record.SchlProf_ID}"
            data-suboff-id="${record.sub_off_id}"
            data-sub-desc="${record.subj_desc}"
            data-sub-sect="${record.schl_sec || 'No Section'}">
            <i class="fas fa-eye me-1"></i> View TADI
            <span class="tadi-badge tadi-badge-muted ms-1">${record.verified_count || 0}</span>
          </button>
        </td>`;
      tbody.appendChild(row);
    });

    tbody.querySelectorAll(".vw_tadi").forEach(btn => {
      btn.addEventListener("click", () => {
        const prof_id = btn.dataset.profId;
        const subj_id = btn.dataset.suboffId;
        const subj_desc = btn.dataset.subDesc;
        const subj_sect = btn.dataset.subSect;

        document.getElementById("tadi_subj_name").innerText = subj_desc;
        document.getElementById("section_name").innerText = subj_sect;

        GETALL_TADI_RECORDS(prof_id, subj_id);
      });
    });
  })
  .catch(error => console.error("Error searching subjects by instructor:", error));
});

document.getElementById("reportSearch").addEventListener("click", function(){

  document.querySelector(".export-content").innerHTML = '';
  
  const lvlid = document.getElementById("academiclevel").value;
  const yrlvlid = document.getElementById("academicyearlevel").value;
  const prdid = document.getElementById("academicperiod").value;
  const yrid = document.getElementById("acadyear").value;
  const startDate = document.getElementById("startDate").value;
  const endDate = document.getElementById("endDate").value;

  if(!lvlid || !yrlvlid || !prdid || !yrid){
   showAlertModal("Please select all filters to generate the report");
   emptyCriteriaReport();
    return;
  }else{
    resetCriteriaReport();
  }

  const formData = new FormData();
  formData.append('type', 'GET_TEACHER_TADI_REPORT');
  formData.append('lvl_id', lvlid);
  formData.append('prd_id', prdid);
  formData.append('yr_id', yrid);
  formData.append('yrlvl_id', yrlvlid);

  let lvl = null;
  let yrlvl = null;
  let prd = null;
  
  switch(lvlid){
    case '1':
      lvl = "Basic Education";
      break;
    case '2':
      lvl = "Tertiary";
      break;
    case '3':
      lvl = "Graduate School";
      break;
      default:
        lvl = null;
  }

  switch(yrlvlid){
    case '6':
      yrlvl = "1st Year";
      break;
    case '7':
      yrlvl = "2nd Year";
      break;
    case '8':
      yrlvl = "3rd Year";
      break;
    case '9':
      yrlvl = "4th Year";
      break;
    default:
      yrlvl = null;
  }

  switch(prdid){
    case '5':
      prd = "1st Semester";
      break;
    case '6':
      prd = "2nd Semester";
      break;
    case '7':
      prd = "Mid Year";
      break;
    default:
      prd = null;
  }

  let headerLabel = `${lvl} ${yrlvl} - ${prd}`;
  let exprtname = `TADI-REPORT-${lvl.toUpperCase()}-${yrlvl.toUpperCase()}-${prd.toUpperCase()}`;

  if(startDate && endDate){
    formData.append('startDate', startDate);
    formData.append('endDate', endDate);
    headerLabel = `${headerLabel} (${startDate} to ${endDate})`; 
    exprtname = `${exprtname}-(${startDate}-TO-${endDate})`;
    
    if(startDate > endDate){
      showAlertModal("Start date must be earlier than end date.");
      invalidStartDateInput()
      return;
    }else{
      resetStartEndDateInput();
    }
  };

  if(!startDate && endDate){
    showAlertModal("Please select a start date.");
    invalidStartDateInput();
    return;
  }else if(startDate && !endDate){
    showAlertModal('Please select an end date');
    invalidEndDateInput();
    return;
  }else{
    resetStartEndDateInput();
  }

  const reportContainer = document.getElementById('reportContainer');
  reportContainer.innerHTML = loadingRow(4);

  const repBtn = document.getElementById("reportSearch");
  const backTadi = document.getElementById("tadiBtn");
  repBtn.disabled = true;
  backTadi.disabled = true;

  fetch("forms/tadi/dean/controller/index-info.php", { 
      method: "POST", 
      body: formData 
  })
  .then(handleRateLimitJson)
  .then(data => {
    console.log('Raw data:', data); // For debugging

    // Check if data is empty or has error
    if (!data || data.error || data.length === 0) {
        reportContainer.innerHTML = `
            <div class="tadi-empty-state py-5">
                <i class="fas fa-exclamation-triangle d-block mb-2" style="font-size:2rem;opacity:.45;color:#f59e0b"></i>
                <p>${data?.error ? data.message : 'No TADI records found for the selected criteria.'}</p>
            </div>
        `;
        document.querySelector(".export-content").innerHTML = `
            <h5 style="font-weight:700;color:#181a46;margin:0">${headerLabel}</h5>
            <button class="tadi-btn tadi-btn-success" id="exportAll" disabled>
                <i class="fas fa-file-excel"></i> Export to Excel
            </button>
        `;
        return;
    }

    // Store raw data for export
    window.tadiReportData = data;

    // Group by professor first
    const teacherGroups = data.reduce((groups, record) => {
        const profId = record.prof_name; // Use prof_name as key since it's unique
        
        if (!groups[profId]) {
            groups[profId] = {
                prof_name: record.prof_name,
                subjects: {}
            };
        }

        const subjKey = record.subject_code;
        if (!groups[profId].subjects[subjKey]) {
            groups[profId].subjects[subjKey] = {
                subject_code: record.subject_code,
                subject_desc: record.subject_desc,
                section_name: record.section_name,
                sessions: []
            };
        }

        // Add session details if valid
        if (record.schltadi_id) {
            groups[profId].subjects[subjKey].sessions.push({
                date: record.tadi_date,
                time_in: formatTimeToAmPm(record.time_in),
                time_out: formatTimeToAmPm(record.time_out),
                duration: record.duration,
                mode: record.mode === 'online_learning' ? 'Online' : 'Onsite',
                type: record.type === 'makeup' ? 'Make-up' : 'Regular',
                status: record.status,
                late_status: record.late_status,
                activity: record.activity ? record.activity.replace(/\\r\\n/g, "<br>") : 'No activity recorded',
                stud_name: record.student_name,
                makeup_date: record.mkup_date || null
            });
        }

        return groups;
    }, {});

    // Generate summary stats
    const stats = Object.values(teacherGroups).reduce((acc, teacher) => {
        const teacherStats = Object.values(teacher.subjects).reduce((subAcc, subject) => {
            subAcc.totalSessions += subject.sessions.length;
            subAcc.verifiedSessions += subject.sessions.filter(s => s.status == 1).length;
            return subAcc;
        }, { totalSessions: 0, verifiedSessions: 0 });
        
        acc.totalTeachers++;
        acc.totalSessions += teacherStats.totalSessions;
        acc.verifiedSessions += teacherStats.verifiedSessions;
        return acc;
    }, { totalTeachers: 0, totalSessions: 0, verifiedSessions: 0 });

    const hasMakeupDate = Object.values(teacherGroups).some(teacher => 
    Object.values(teacher.subjects).some(subject => 
        subject.sessions.some(session => session.makeup_date)
    )
);

    // Generate HTML output with summary
    reportContainer.innerHTML = `
        <div class="tadi-content-card mb-3">
            <div class="tadi-content-card-header">
                <h5><i class="fas fa-chart-bar me-2" style="opacity:.75"></i>Report Summary</h5>
            </div>
            <div class="p-3">
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <div class="text-center p-3 rounded" style="background:#f0f2f8;border:1px solid #e2e6f0">
                            <div style="font-size:.8rem;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.4px;">Total Teachers</div>
                            <div style="font-size:2rem;font-weight:700;color:#181a46;line-height:1.2;margin-top:4px">${stats.totalTeachers}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-center p-3 rounded" style="background:#f0f2f8;border:1px solid #e2e6f0">
                            <div style="font-size:.8rem;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.4px;">Total Sessions</div>
                            <div style="font-size:2rem;font-weight:700;color:#181a46;line-height:1.2;margin-top:4px">${stats.totalSessions}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-center p-3 rounded" style="background:#f0fff4;border:1px solid #a7f3d0">
                            <div style="font-size:.8rem;font-weight:600;color:#065f46;text-transform:uppercase;letter-spacing:.4px;">Verified</div>
                            <div style="font-size:2rem;font-weight:700;color:#059669;line-height:1.2;margin-top:4px">${stats.verifiedSessions}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-center p-3 rounded" style="background:#fff5f5;border:1px solid #fca5a5">
                            <div style="font-size:.8rem;font-weight:600;color:#991b1b;text-transform:uppercase;letter-spacing:.4px;">Unverified</div>
                            <div style="font-size:2rem;font-weight:700;color:#dc2626;line-height:1.2;margin-top:4px">${stats.totalSessions - stats.verifiedSessions}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        ${Object.entries(teacherGroups).map(([profId, teacher]) => `
            <div class="tadi-content-card mb-3">
                <div class="tadi-content-card-header">
                    <h5><i class="fas fa-user-tie me-2" style="opacity:.75"></i>${teacher.prof_name}</h5>
                    <span class="tadi-badge tadi-badge-muted">
                        ${Object.values(teacher.subjects).reduce((sum, subj) => sum + subj.sessions.length, 0)} session(s)
                    </span>
                </div>
                <div class="p-3">
                    ${Object.values(teacher.subjects).map(subject => `
                        <div class="mb-4">
                            <div class="d-flex flex-wrap justify-content-between align-items-center mb-2 gap-2">
                                <div>
                                    <span style="font-weight:600;font-size:.9rem;color:#181a46">${subject.subject_code}</span>
                                    <span style="color:#6b7280;font-size:.85rem;"> &mdash; ${subject.subject_desc}</span>
                                </div>
                                <span class="tadi-badge tadi-badge-primary">${subject.section_name || 'No Section'}</span>
                            </div>
                            <div class="tadi-modal-table-card">
                                <div class="tadi-table-wrapper" style="max-height:none">
                                    <table class="tadi-table">
                                        <thead>
                                            <tr style="text-align:center">
                                                <th>Date</th>
                                                <th>Time</th>
                                                <th>Duration</th>
                                                <th>Session Type</th>
                                                <th>Make-up Date</th>
                                                <th>Submitted By</th>
                                                <th>Activity</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${subject.sessions.map(session => `
                                                <tr style="text-align:center" class="${session.late_status == 1 ? 'tadi-row-late' : ''}">
                                                    <td>${session.date}</td>
                                                    <td style="white-space:nowrap">${session.time_in} &mdash; ${session.time_out}</td>
                                                    <td>${session.duration}</td>
                                                    <td><span class="tadi-badge tadi-badge-primary">${session.mode} ${session.type}</span></td>
                                                    <td>${hasMakeupDate == "null" || !session.makeup_date ? '<span style="color:#9ca3af">&#8212;</span>' : session.makeup_date}</td>
                                                    <td>${session.stud_name}</td>
                                                    <td style="text-align:left"><span class="activity-text">${session.activity}</span></td>
                                                    <td>
                                                        <span class="tadi-badge ${session.status == 1 ? 'tadi-badge-success' : 'tadi-badge-danger'}">
                                                            ${session.status == 1 ? '<i class="fas fa-check-circle"></i> Verified' : '<i class="fas fa-times-circle"></i> Unverified'}
                                                        </span>
                                                        ${session.late_status == 1 ? '<br><span class="tadi-badge tadi-badge-warning mt-1"><i class="fas fa-clock"></i> Late</span>' : ''}
                                                    </td>
                                                </tr>
                                            `).join('')}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
        `).join('')}
    `;

    // Add Export button
    document.querySelector(".export-content").innerHTML = `
        <h5 style="font-weight:700;color:#181a46;margin:0">${headerLabel}</h5>
        <button class="tadi-btn tadi-btn-success" id="exportAll">
            <i class="fas fa-file-excel"></i> Export to Excel
        </button>
    `;
    document.getElementById("exportAll").addEventListener("click",() =>{
      tadiReportExport(exprtname)
    });

    })
.catch(err => console.error("Error generating TADI report:", err))
.finally(() => {
  repBtn.disabled = false;
  backTadi.disabled = false;
});
})
