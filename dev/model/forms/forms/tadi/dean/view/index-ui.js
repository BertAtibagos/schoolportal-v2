document.querySelector(".subj-table").style.display = "none";
document.querySelector(".instr-table").style.display = "block";

document.getElementById("exportBtn").addEventListener("click", function() {
    document.querySelector(".instr-table").style.display = "none";
    document.getElementById("tadiBtn").style.display = "";
    document.getElementById("exportBtn").style.display = "none";
    document.getElementById("search_button").style.display = "none";
    document.getElementById("reportSearch").style.display = "";

    const repCont = document.getElementById("reportContainer");
    repCont.style.display = "block";
    repCont.innerHTML = `<div class="tadi-empty-state py-5">
        <i class="fas fa-chart-bar d-block mb-2" style="font-size:2rem;opacity:.3"></i>
        <p class="mb-1">Select all filters above and click <strong>Generate Report</strong>.</p>
        <p class="text-muted" style="font-size:.82rem">Start date and end date are optional.</p>
    </div>`;
    document.getElementById("tadiTitle").innerText = "TADI Report";

    document.querySelector(".export-header").style.display = "block";

    document.querySelectorAll(".date-range-xport").forEach(el => el.style.display = "");
});

document.getElementById("tadiBtn").addEventListener("click", function() {
    document.querySelector(".instr-table").style.display = "block";
    document.getElementById("tadiBtn").style.display = "none";
    document.querySelector(".export-content").innerHTML = '';
    document.getElementById("exportBtn").style.display = "";
    document.getElementById("search_button").style.display = "";
    document.getElementById("reportSearch").style.display = "none";
    const start_date = document.getElementById("startDate");
    const end_date = document.getElementById("endDate");
    start_date.type = "text";
    end_date.type = "text";
    end_date.value = "";

    const repCont = document.getElementById("reportContainer");
    repCont.innerHTML = '';
    repCont.style.display = "none";

    document.getElementById("tadiTitle").innerText = "TADI \u2014 Dean";

    document.querySelector(".export-header").style.display = "none";

    document.querySelectorAll(".date-range-xport").forEach(el => el.style.display = "none");
});

document.getElementById("startDate").addEventListener("focus", function(){
    this.type = "date";
});

document.getElementById("endDate").addEventListener("focus", function(){
    this.type = "date";
    const date = new Date().toLocaleDateString('en-CA');
    this.value = date;
});

function showAlertModal(message) {
  const modalEl = document.getElementById('alertModal');
  const modalBody = document.getElementById('alertModalBody');
  modalBody.textContent = message;
  const modal = new bootstrap.Modal(modalEl);
  modal.show();
}

function showInstructorTadiModal() {
  const subjectModalEl = document.getElementById("Instructor_Subject_List");
  const tadiModalEl = document.getElementById("Instructor_Tadi_List");
  const subjectModal = bootstrap.Modal.getOrCreateInstance(subjectModalEl);
  const tadiModal = bootstrap.Modal.getOrCreateInstance(tadiModalEl);

  tadiModalEl.addEventListener("hidden.bs.modal", () => {
    subjectModal.show();
  }, { once: true });

  const openTadiModal = () => tadiModal.show();
  if (subjectModalEl.classList.contains("show")) {
    subjectModalEl.addEventListener("hidden.bs.modal", openTadiModal, { once: true });
    subjectModal.hide();
  } else {
    openTadiModal();
  }
}

function invalidStartDateInput(){
    const startDate = document.getElementById("startDate");
      startDate.classList.remove("border-dark");
      startDate.classList.add("border-danger");
      startDate.classList.add("is-invalid");
}

function invalidEndDateInput(){
    const endDate = document.getElementById("endDate");
      endDate.classList.remove("border-dark");
      endDate.classList.add("border-danger");
      endDate.classList.add("is-invalid");
}

function resetStartEndDateInput(){
    const startDate = document.getElementById("startDate");
    const endDate = document.getElementById("endDate");

      startDate.classList.add("border-dark");
      startDate.classList.remove("border-danger");
      startDate.classList.remove("is-invalid");

      endDate.classList.add("border-dark");
      endDate.classList.remove("border-danger");
      endDate.classList.remove("is-invalid");

}

function emptyCriteriaReport(){
    const academiclevel = document.getElementById('academiclevel');
    const academicyearlevel = document.getElementById('academicyearlevel');
    const academicperiod = document.getElementById('academicperiod');
    const acadyear = document.getElementById('acadyear');

    academiclevel.classList.remove("border-dark");
    academiclevel.classList.add("border-danger");
    academiclevel.classList.add("is-invalid");

    academicyearlevel.classList.remove("border-dark");
    academicyearlevel.classList.add("border-danger");
    academicyearlevel.classList.add("is-invalid");

    academicperiod.classList.remove("border-dark");
    academicperiod.classList.add("border-danger");
    academicperiod.classList.add("is-invalid");

    acadyear.classList.remove("border-dark");
    acadyear.classList.add("border-danger");
    acadyear.classList.add("is-invalid");
}

function resetCriteriaReport(){
    const academiclevel = document.getElementById('academiclevel');
    const academicyearlevel = document.getElementById('academicyearlevel');
    const academicperiod = document.getElementById('academicperiod');
    const acadyear = document.getElementById('acadyear');

    academiclevel.classList.remove("border-danger");
    academiclevel.classList.remove("is-invalid");
    academiclevel.classList.add("border-dark");
    
    academicyearlevel.classList.remove("border-danger");
    academicyearlevel.classList.remove("is-invalid");
    academicyearlevel.classList.add("border-dark");

    academicperiod.classList.remove("border-danger");
    academicperiod.classList.remove("is-invalid");
    academicperiod.classList.add("border-dark");

    acadyear.classList.remove("border-danger");
    acadyear.classList.remove("is-invalid");
    acadyear.classList.add("border-dark");
}

function errorMessageBox(message){
    const errBox = document.querySelector('.err-message-box');
    errBox.style.display = "block";
    errBox.innerHtml = '';
    errBox.innerHTML = `<span class="text-dark">${message}</span>`;
}

function clearErrorMessageBox(){
    const errBox = document.querySelector('.err-message-box');
    errBox.innerHtml = '';
    errBox.style.display = "none";
}

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
          data-prof-id="${item.SchlProf_ID}"
          data-suboff-id="${item.sub_off_id}"
          data-sub-desc="${item.subj_desc}"
          data-sub-sect="${item.schl_sec || 'No Section'}">
          <i class="fas fa-eye me-1"></i> View TADI
          <span class="tadi-badge tadi-badge-muted ms-1">${item.unverified_count}</span>
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

      showInstructorTadiModal();
      GETALL_TADI_RECORDS(prof_id, subj_id);
    });
  });
}

function showImageModal(data) {

      const imgModal = new bootstrap.Modal(
          document.getElementById("imageModal"),
          { backdrop: true }
      );

      const carousel = bootstrap.Carousel.getOrCreateInstance(
          document.getElementById("imageCarousel"),
          {
              interval: false,
              wrap: true
          }
      );

      // Always start from first image
      carousel.to(0);

      const format = (d, t) =>
          new Date(`${d}T${t}`).toLocaleString("en-US", {
              year: "numeric",
              month: "long",
              day: "numeric",
              hour: "2-digit",
              minute: "2-digit",
              hour12: true
          });

      document.getElementById("startdateTimeTaken").innerText =
          data.startexif_date
              ? `Start of class taken: ${format(data.startexif_date, data.startexif_time)}`
              : "Start of class taken: Not Available";

      document.getElementById("enddateTimeTaken").innerText =
          data.endtexif_date
              ? `End of class taken: ${format(data.endexif_date, data.endexif_time)}`
              : "End of class taken: Not Available";

      document.getElementById("dateTimeUpld").innerText =
          `Uploaded: ${format(data.upld_date, data.upld_time)}`;

      document.getElementById("closeModalBtn").onclick = () => {

          imgModal.hide();

          document.getElementById("start_attchPrev").src = "";
          document.getElementById("end_attchPrev").src = "";

          carousel.to(0);
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