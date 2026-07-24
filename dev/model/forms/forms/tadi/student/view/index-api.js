function GET_SUBJECTLIST() {
    const formData = new FormData();
    formData.append('type','GET_SUBJECT_LIST');

    const tbody = document.getElementById('card_container');
    tbody.innerHTML = spinner;
    
    fetch("forms/tadi/student/controller/index-info.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        displaySubjectTable(result);
    })
    .catch(err => {
        alert("Failed to load subject list. Please try again.");
    })
}

function GET_IMAGE(event) {
  const button = event.target;
  const tadi_id = button.value;
  const prof_id = document.getElementById("imgProf_id").value;

  const formData = new FormData();
  formData.append('type','GET_IMAGE');
  formData.append('tadi_id',tadi_id);
  formData.append('prof_id', prof_id);

  fetch(`forms/tadi/student/controller/index-info.php`,{
    method: "POST",
    body: formData
  })
    .then(res => {
        if (res.status === 429) {
            return res.json().then(data => {
                showToastMessage(
                    data.message || "Too many requests. Please try again later.",
                    "warning",
                    "Notice"
                );
                return null;
            });
        }

        return res.json();
    })
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

function viewSubmitted(subj_Id, prof_Id){

    const params = new URLSearchParams({
        type: 'GET_SUBMITTED_REC',
        subj_Id: subj_Id,
        prof_Id: prof_Id
    });

    fetch(`forms/tadi/student/controller/index-info.php`, {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: params
    })
        .then(res => res.json())
        .then(data => {
            const navTabs = document.getElementById('nav-tab');          
            const navTabContent = document.getElementById('nav-tabContent'); 

            navTabs.innerHTML = '';
            navTabContent.innerHTML = '';

            if (!data.length) {
                navTabContent.innerHTML = "<div class='p-3 text-center'>No records found</div>";
                return;
            }

            data.forEach((record, index) => {
                const isActive = index === 0 ? "active" : "";

                
                const tabBtn = document.createElement('button');
                tabBtn.className = `nav-link ${isActive} ${isActive ? 'fw-bold' : 'text-secondary'}`.trim();
                tabBtn.id = `nav-tab-${record.schltadi_ID}`;
                tabBtn.setAttribute('data-bs-toggle', 'tab');
                tabBtn.setAttribute('data-bs-target', `#tab-pane-${record.schltadi_ID}`);
                tabBtn.type = 'button';
                tabBtn.role = 'tab';
                tabBtn.innerText = `Record ${index + 1}`;
                navTabs.appendChild(tabBtn);

                
                const viewUploadCell = record.tadi_filepath
                    ? `<button class="btn-tadi btn-tadi-view viewAttch" value="${escapeHtml(record.schltadi_ID)}">View</button>`
                    : `<span class="btn-tadi" style="background: #cbd5e1; color: #64748b; cursor: default;">No Attachment</span>`;

                const modeTypeMap = {
                    'online_learning regular': 'Online Regular',
                    'online_learning makeup': 'Online Make-Up',
                    'onsite_learning regular': 'Onsite Regular',
                    'onsite_learning makeup': 'Onsite Make-Up'
                };

                const deletebtn = record.tadi_status == 0
                    ? `<button class="btn-tadi btn-tadi-danger delete-tadi" data-tadi-id="${escapeHtml(record.schltadi_ID)}" data-subj-id="${escapeHtml(record.sub_off_id)}" data-prof-id="${escapeHtml(record.SchlProf_ID)}">Delete Record</button>`
                    : '';

                const activity = formatActivityText(record.tadi_act);
                const statusConfig = record.tadi_status == 1
                    ? { text: "Verified", badgeClass: "badge-verified" }
                    : { text: "Unverified", badgeClass: "badge-unverified" };

                const tabPane = document.createElement('div');
                tabPane.className = `tab-pane fade show ${isActive} bg-white`;
                tabPane.id = `tab-pane-${record.schltadi_ID}`;
                tabPane.role = "tabpanel";
                tabPane.setAttribute("aria-labelledby", `nav-tab-${record.schltadi_ID}`);

                document.getElementById('tadi-date').textContent = `${new Date(record.tadi_date).toLocaleDateString("en-PH", {
                    month: "long",
                    day: "numeric",
                    year: "numeric",
                })}`;

                tabPane.innerHTML = `
                    <div class="record-panel" id="preview-${record.schltadi_ID}">
                        <div class="record-field" id="timeLabel${record.schltadi_ID}">
                            <span class="field-label">Time</span>
                            <span class="field-value">${formatTimeToAmPm(record.tadi_timeIn)} &ndash; ${formatTimeToAmPm(record.tadi_timeOut)}</span>
                        </div>
                        <div class="record-field" id="classTypeLabel${record.schltadi_ID}">
                            <span class="field-label">Class Type</span>
                            <span class="field-value">${escapeHtml(modeTypeMap[record.tadi_modeType] || record.tadi_modeType)}</span>
                        </div>
                        <div class="record-field" id="actLabel${record.schltadi_ID}">
                            <span class="field-label">Activity</span>
                            <span class="activity-text field-value">${activity}</span>
                        </div>
                        <div class="record-field" id="attchLabel${record.schltadi_ID}">
                            <span class="field-label">Attachment</span>
                            <span class="field-value">${viewUploadCell}</span>
                            <input type="hidden" id="imgProf_id" value="${escapeHtml(record.SchlProf_ID)}">
                        </div>
                        <div class="record-field" id="status${record.schltadi_ID}">
                            <span class="field-label">Status</span>
                            <span class="${statusConfig.badgeClass}" value="${escapeHtml(record.schltadi_ID)}" name="${escapeHtml(record.tadi_status)}">${statusConfig.text}</span>
                        </div>
                        <div class="d-flex justify-content-end gap-2 pt-2">
                            ${deletebtn}
                        </div>
                    </div>`;

                navTabContent.appendChild(tabPane);

                const deleteBtn = tabPane.querySelector('.delete-tadi');
                if (deleteBtn) {
                    deleteBtn.addEventListener('click', () => {
                        const tadiId = Number(deleteBtn.dataset.tadiId);
                        const subOffId = Number(deleteBtn.dataset.subjId);
                        const profId = Number(deleteBtn.dataset.profId);

                        if (!Number.isFinite(tadiId) || !Number.isFinite(subOffId) || !Number.isFinite(profId)) {
                            alert("Missing necessary information to delete the record. Please try again.");
                            return;
                        }

                        revertTADISubmission(tadiId, subOffId, profId);
                    });
                }

                const text = tabPane.querySelector('.activity-text');
                setupActivityText(text);
            });

            navTabs.addEventListener('shown.bs.tab', function (e) {
                if (e.relatedTarget) {
                    e.relatedTarget.classList.add('text-secondary');
                    e.relatedTarget.classList.remove('fw-bold');
                }
                e.target.classList.remove('text-secondary');
                e.target.classList.add('fw-bold');
            });

            document.querySelectorAll('.viewAttch').forEach(button =>
                button.addEventListener('click', GET_IMAGE)
            );
        })
        .catch(err => {
            console.error("Error loading records:", err);
        });
}

async function revertTADISubmission(tadiId, subOffId, profId){

    if(tadiId && subOffId && profId){
        if(confirm("Are you sure you want to delete this record? This action cannot be undone.")){
            try{
                const req = await fetch(`forms/tadi/student/controller/index-info.php`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: new URLSearchParams({
                        type: 'REVERT_SUBMISSION',
                        tadi_id: tadiId,
                        subj_id: subOffId,
                        prof_id: profId
                    })
                });

                const res = await req.json();

                if(res.success){
                    alert(res.message);
                    viewSubmitted(subOffId, profId);
                }else{
                    alert(res.message || "Failed to revert submission. Please try again.");
                }
            }catch(err){
                alert("Failed to revert submission. Please try again.");
            }
        }
    }else{
        alert("Missing necessary information to delete the record. Please try again.");
        return;
    }
}