function showRateLimitToast(message) {
  const toastEl = document.getElementById("successToast");
  const toastHeader = document.getElementById("toastHeader");
  const toastTitle = document.getElementById("toastTitle");
  const toastMessage = document.getElementById("toastMessage");

  if (!toastEl || !toastHeader || !toastTitle || !toastMessage) {
    return;
  }

  toastTitle.textContent = "Notice";
  toastMessage.textContent = message || "Too many submissions. Please try again later.";

  toastHeader.classList.remove(
    "bg-success",
    "bg-danger",
    "bg-info",
    "bg-primary",
    "bg-secondary",
    "bg-warning",
    "text-white",
    "text-dark"
  );
  toastHeader.classList.add("bg-warning", "text-dark");

  const toast = bootstrap.Toast.getOrCreateInstance(toastEl);
  toast.show();
}

function handleRateLimitJson(response) {
  if (response.status === 429) {
    return response.json().then(data => {
      const message = data && data.message ? data.message : "Too many submissions. Please try again later.";
      showRateLimitToast(message);
      throw new Error("Rate limit reached");
    }).catch(() => {
      showRateLimitToast("Too many submissions. Please try again later.");
      throw new Error("Rate limit reached");
    });
  }

  if (!response.ok) {
    throw new Error("Network response was not ok");
  }

  return response.json();
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

function displaySubjList(data) {
  const tbody = document.querySelector("#subj_list");
  tbody.innerHTML = data.map((item, i) => `
    <tr>
      <td>${item.schl_sec || 'No Section'}</td>
      <td>${item.subj_code}</td>
      <td>${item.subj_desc}</td>
      <td>
        <button 
          class="tadi-btn tadi-btn-ghost tadi-btn-sm w-100 position-relative vw_tadi" 
          data-bs-target="#Instructor_Tadi_List" 
          data-bs-toggle="modal"
          data-prof-id="${item.SchlProf_ID}"
          data-suboff-id="${item.sub_off_id}"
          data-sub-desc="${item.subj_desc}"
          data-sub-sect="${item.schl_sec || 'No Section'}">
          <i class="fas fa-eye me-1"></i> View TADI
          <span class="tadi-badge tadi-badge-muted ms-1">${item.verified_count}</span>
        </button>
      </td>
    </tr>`).join('');

  document.querySelector(".tadi_inst_name").textContent = data[0]?.prof_name || "No Instructor";

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
            <td>
            ${record.approved === 0
              ? `<button class="tadi-btn tadi-btn-approve approve"
                  value="${record.schltadi_ID}"
                  data-prof="${record.SchlProf_ID}"
                  data-subj-id="${record.sub_off_id}">
                    <i class="fas fa-check"></i> Approve
                </button>`
              : `<span class="tadi-badge tadi-badge-approved"><i class="fas fa-check-circle"></i> Approved</span>`}
            </td>
          </tr>`;
      }).join('');

      tbody.querySelectorAll(".viewAttch").forEach(btn =>
        btn.addEventListener("click", e => GET_IMAGE(e.currentTarget.value, e.currentTarget.dataset.prof))
      );

      tbody.querySelectorAll(".approve").forEach(btn =>
        btn.addEventListener("click", e => {
          const tadiId = e.target.value;
          const profId = e.target.dataset.prof;
          const subjId = e.target.dataset.subjId;
          approveTadiRequest(tadiId, profId, subjId);
        })
      );

      tbody.querySelectorAll(".activity-text").forEach(setupActivityText);
    })
    .catch(err => console.error("Error loading TADI records:", err));
}

function GET_IMAGE(tadi_id, prof_id) {
  const formData = new FormData();
  formData.append('type', 'GET_IMAGE');
  formData.append('tadi_id', tadi_id);
  formData.append('prof_id', prof_id);

  fetch("forms/tadi/dean/controller/index-info.php", { method: "POST", body: formData })
    .then(handleRateLimitJson)
    .then(data => {
      if (!data || !data.tadi_filepath) {
        console.error("No image found for TADI ID", tadi_id);
        return;
      }
      const imgPrev = document.getElementById('attchPrev');
      imgPrev.src = `/schoolportal-v2/dev/public/${data.tadi_filepath}`;
      showImageModal(data);
    })
    .catch(err => console.error("Error fetching image:", err));
}

function showImageModal(data) {
  const imgModal = new bootstrap.Modal(document.getElementById('imageModal'), { backdrop: true });
  const format = (d, t) => new Date(`${d}T${t}`).toLocaleString('en-US', {
    year: "numeric", month: "long", day: "numeric", hour: "2-digit", minute: "2-digit", hour12: true
  });

  document.getElementById('dateTimeTaken').innerText = data.exif_date ? `Taken: ${format(data.exif_date, data.exif_time)}` : 'Taken: Not Available';
  document.getElementById('dateTimeUpld').innerText = `Uploaded: ${format(data.upld_date, data.upld_time)}`;

  document.getElementById('closeModalBtn').onclick = () => {
    imgModal.hide();
    document.getElementById('attchPrev').src = '';
  };

  imgModal.show();
}

function setupActivityText(el) {
  Object.assign(el.style, {
    display: '-webkit-box',
    WebkitLineClamp: '2',
    WebkitBoxOrient: 'vertical',
    overflow: 'hidden',
    cursor: 'pointer'
  });

  el.addEventListener('click', () => {
    const expanded = el.style.WebkitLineClamp === 'none';
    el.style.WebkitLineClamp = expanded ? '2' : 'none';
    el.style.display = expanded ? '-webkit-box' : 'block';
  });
}

function loadingRow(cols) {
  return `
    <tr>
      <td colspan="${cols}">
        <div class="text-center p-3">
          <div class="spinner-border" role="status"><span class="sr-only"></span></div>
        </div>
      </td>
    </tr>`;
}

function formatTimeToAmPm(timeString) {
  const [h, m] = timeString.split(":");
  let hour = parseInt(h, 10);
  const ampm = hour >= 12 ? "PM" : "AM";
  hour = hour % 12 || 12;
  return `${hour}:${m} ${ampm}`;
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

function tadiReportExport(exprtname){
        const data = window.tadiReportData;
        if (!data || !data.length) {
            showAlertModal('No data available to export');
            return;
        }

        // Create workbook and worksheet
        const wb = XLSX.utils.book_new();
        const allRows = [];

        // Add headers
        allRows.push([
            'Professor Name',
            'Subject Code',
            'Subject Description',
            'Section',
            'Student Name',
            'Date',
            'Time In',
            'Time Out',
            'Duration',
            'Mode',
            'Session Type',
            'Activity',
            'Late Submission',
            'Status'
        ]);

        // Sort by professor name to group them together
        data.sort((a, b) => a.prof_name.localeCompare(b.prof_name));

        let currentProf = null;

        // Process data and insert blank rows between professors
        data.forEach(record => {
            if (!record.schltadi_id) return;

            // Insert a blank row when the professor changes (skip before first)
            if (currentProf && record.prof_name !== currentProf) {
                allRows.push([]); // blank row separator
            }

            allRows.push([
                record.prof_name,
                record.subject_code,
                record.subject_desc,
                record.section_name || 'No Section',
                record.student_name,
                record.tadi_date,
                record.time_in,
                record.time_out,
                record.duration,
                record.mode,
                record.type,
                record.activity || 'No activity recorded',
                record.late_status == 1 ? 'Yes' : 'No',
                record.status == 1 ? 'Verified' : 'Unverified'
            ]);

            currentProf = record.prof_name;
        });

        // Create worksheet
        const ws = XLSX.utils.aoa_to_sheet(allRows);

        // Set column widths
        ws['!cols'] = [
            { wch: 30 }, // Professor Name
            { wch: 15 }, // Subject Code
            { wch: 40 }, // Subject Description
            { wch: 15 }, // Section
            { wch: 30 }, // Student Name
            { wch: 12 }, // Date
            { wch: 10 }, // Time In
            { wch: 10 }, // Time Out
            { wch: 10 }, // Duration
            { wch: 15 }, // Mode
            { wch: 12 }, // Session Type
            { wch: 50 }, // Activity
            { wch: 10 },  // Late Status
            { wch: 10 }  // Status
        ];

        // Style headers
        const range = XLSX.utils.decode_range(ws['!ref']);
        for (let C = range.s.c; C <= range.e.c; ++C) {
            const address = XLSX.utils.encode_cell({ r: 0, c: C });
            if (!ws[address]) continue;
            ws[address].s = {
                fill: { fgColor: { rgb: "FFFF00" } },
                font: { bold: true }
            };
        }

        // Append worksheet to workbook
        XLSX.utils.book_append_sheet(wb, ws, "TADI Records");

        // Generate filename
        const filename = `${exprtname}.xlsx`;

        // Save file
        XLSX.writeFile(wb, filename);
};