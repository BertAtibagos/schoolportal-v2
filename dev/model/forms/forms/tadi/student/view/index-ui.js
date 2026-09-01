const spinner = `<div class="tadi-loading">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <span>Loading subjects...</span>
                </div>`;

const htmlEscapes = {
    "&": "&amp;",
    "<": "&lt;",
    ">": "&gt;",
    '"': "&quot;",
    "'": "&#39;"
};

function escapeHtml(value) {
    return String(value ?? "").replace(/[&<>"']/g, (match) => htmlEscapes[match]);
}

function formatActivityText(value) {
    return escapeHtml(value ?? "").replace(/\\r\\n|\r?\n/g, "<br>");
}

function isSafeAttachmentPath(value) {
    if (typeof value !== "string") {
        return false;
    }

    if (value.includes("..") || value.includes("\\") || value.includes(":")) {
        return false;
    }

    // Allow optional subject folder between the date and filename
    return /^attachment\/[A-Za-z0-9._-]+\/\d{4}-\d{2}-\d{2}(?:\/[A-Za-z0-9._-]+)?\/[A-Za-z0-9._-]+$/.test(value);
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

function displaySubjectTable(result) {

    // const late_sub_sec = document.getElementById('late_submss_section');

    // if(late_sub_sec && result.length > 0 && result[0].user_id == 957){
    //     document.getElementById('chckbox_late_submss_div').classList.remove('d-none');
    //     late_sub_sec.classList.remove('d-none');
    //     late_sub_sec.innerHTML = `
    //                             <div class="row mb-4 late-submission-fields d-none">
    //                                 <div class="col-md-6 col-lg-4">
    //                                     <label for="late_class_date" class="form-label">
    //                                         Date of actual class held <span class="text-danger">*</span></label>
    //                                     </label>
    //                                     <input class="form-control" type="date" name="late_class_date" id="late_class_date" required>
    //                                     <div class="invalid-feedback">Please set a date</div>
    //                                 </div>

    //                                 <div class="col-md-6 col-lg-4">
    //                                     <label for="late_reason" class="form-label">
    //                                         Reason for late submission <span class="text-danger">*</span></label>
    //                                     </label>
    //                                     <select class="form-select" name="late_reason" id="late_reason" required>
    //                                         <option value="">-- Select a reason --</option>
    //                                         <option value="1">No Internet Connection</option>
    //                                         <option value="2">Device Unavailable</option>
    //                                     </select>
    //                                     <div class="invalid-feedback">Please select a reason</div>
    //                                 </div>
    //                             </div>
    //                             `;

    //     document.getElementById("chck_late_submt").addEventListener("change", (e) =>{
    //     const isChecked = e.target.checked;
    //     const lateSubmtField = document.querySelector(".late-submission-fields");

    //         if(isChecked){
    //             lateSubmtField.classList.remove("d-none");
    //         }

    //         if(!isChecked){
    //             lateSubmtField.classList.add("d-none");

    //             document.getElementById("late_class_date").classList.remove("is-invalid");
    //             document.getElementById("late_reason").classList.remove("is-invalid");
                
    //             document.getElementById("late_class_date").value = "";
    //             document.getElementById("late_reason").value = "";
    //         }
    //     })

    // }else if(!late_sub_sec){
    //     console.log("Not loaded yet")
    // }
        
    const tbody = document.getElementById('card_container');
    
    tbody.innerHTML = result.map((item, index) => 
        `<div class="subj-card" style="${item.record_count_today == 3 ? 'border-left: 4px solid #9798ac' : ''}">
            <div class="subj-info">
                <span class="subj-code">${escapeHtml(item.subj_code)}</span>
                <div class="subj-desc">${escapeHtml(item.subj_desc)}</div>
                <div class="subj-faculty">${item.prof_name ? escapeHtml(item.prof_name) : "No faculty assigned"}</div>
            </div>
            <div class="subj-actions">
                ${item.record_count_today == 3 
                    ? `<span class="sub-limit">Submission limit reached</span>` 
                    : `<button 
                            class="btn-tadi btn-tadi-primary"
                            ${item.prof_name ? "" : "disabled"}
                            id="tadiModalHandler${index}" 
                            data-bs-toggle="modal" 
                            data-bs-target="#modal">
                            Submit
                        </button>`}
                ${item.record_count_today == 0 
                    ? `` 
                    : `<button 
                            class="btn-tadi btn-tadi-success vw_tadi_rec"
                            data-subj-id="${escapeHtml(item.subj_id)}"
                            data-prof-id="${escapeHtml(item.prof_id)}"
                            data-bs-toggle="modal" 
                            data-bs-target="#Instructor_Tadi_List">
                            View
                        </button>`}
            </div>
        </div>`
    ).join("");

    result.forEach((value, index) => {
        const btn = document.getElementById(`tadiModalHandler${index}`);
        if (btn) {
            btn.addEventListener("click", () => {
                displayTadi(value); 
            });
        }
    });

    document.querySelectorAll('.vw_tadi_rec').forEach(button => {
        button.addEventListener("click", function () {
            const subj_Id = this.dataset.subjId;
            const prof_Id = this.dataset.profId;

            viewSubmitted(subj_Id, prof_Id);
        });
    });

}

function displayTadi(value) {
    const formattedDate = new Date().toLocaleDateString("en-PH", {
        month: "long",
        day: "numeric",
        year: "numeric",
    });

    document.getElementById("subjoff_id").value = value.subj_id;
    document.getElementById("tadi_modal_label").textContent = value.subj_desc;
    document.getElementById("subject_details").textContent = `Course Code: ${value.subj_code}`;
    document.getElementById("date_now").textContent = formattedDate;

    const instructorSelect = document.getElementById("instructor");
    instructorSelect.innerHTML = "";

    if (value.prof_name && value.prof_id) {
        const profNames = value.prof_name.split(/[\/,]\s*/);
        const profIds = value.prof_id.split(/[\/,]\s*/);

        if (profNames.length > 1) {
            const placeholder = document.createElement("option");
            placeholder.value = "";
            placeholder.selected = true;
            placeholder.disabled = true;
            placeholder.textContent = "Select an Instructor";
            instructorSelect.appendChild(placeholder);
        }

        profNames.forEach((name, index) => {
            const option = document.createElement("option");
            option.value = (profIds[index] || "").trim();
            option.textContent = name.trim();
            instructorSelect.appendChild(option);
        });
    } else {
        const placeholder = document.createElement("option");
        placeholder.value = "";
        placeholder.selected = true;
        placeholder.disabled = true;
        placeholder.textContent = "No faculty assigned";
        instructorSelect.appendChild(placeholder);
    }
}

function formatTimeToAmPm(timeString) {
  const [hours, minutes] = timeString.split(":");
  let hoursInt = parseInt(hours, 10);
  const period = hoursInt >= 12 ? "PM" : "AM";
  hoursInt = hoursInt % 12 || 12;
  return `${hoursInt}:${minutes} ${period}`;
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

document.getElementById('session_type').addEventListener('change', (e)=>{
    const sessionType = e.target.value;
    const mkupSection = document.getElementById('makeup_date_section');

    if(sessionType === 'makeup'){
        mkupSection.classList.remove('d-none');
        mkupSection.innerHTML = `
                                <div class="row mt-2 makeup-sub-field">
                                    <div class="col-md-6 col-lg-12">
                                        <label for="makeup_class_date" class="form-label">
                                            Originally Scheduled Class Date <span class="text-danger">*</span></label>
                                        </label>
                                        <input class="form-control mkup-class-in" type="date" name="makeup_class_date" id="makeup_class_date" required>
                                        <div class="invalid-feedback">Please set a date</div>
                                    </div>
                                </div>
        `;
    }else{
        mkupSection.innerHTML ='';
    }
})

const tadiModal = document.getElementById('modal');
if (tadiModal) {
    tadiModal.addEventListener('hidden.bs.modal', () => {
        const lateSubmtField = document.querySelector(".late-submission-fields");
        const lateSubmtCheckbox = document.getElementById("chck_late_submt");
        
        if (lateSubmtField) {
            lateSubmtField.classList.add("d-none");
        }
        
        if (lateSubmtCheckbox) {
            lateSubmtCheckbox.checked = false;
        }
        
        const lateClassDate = document.getElementById("late_class_date");
        const lateReason = document.getElementById("late_reason");
        
        if (lateClassDate) {
            lateClassDate.classList.remove("is-invalid");
            lateClassDate.value = "";
        }
        
        if (lateReason) {
            lateReason.classList.remove("is-invalid");
            lateReason.value = "";
        }

        const mkupSection = document.getElementById('makeup_date_section');
        if (mkupSection) {
            mkupSection.classList.add('d-none');
            mkupSection.innerHTML = '';
        }

        const sessionType = document.getElementById('session_type');
        if (sessionType) {
            sessionType.value = '';
        }
    });
}
