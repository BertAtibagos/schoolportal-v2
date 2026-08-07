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

function disable_acknw_bttn() {
    document.querySelectorAll('.acknw').forEach(button => {
      const status = button.getAttribute('name');
      const approved = button.getAttribute('data-approved');
      const dueDate = button.getAttribute('data-due-verify') === 'true';
      let acknowledgedText;
      if(status == 0 && approved == 0 && dueDate){
            acknowledgedText = document.createTextNode('Overdue');
            let span = document.createElement('span');
            span.className = 'tadi-badge tadi-badge-duedate';
            span.appendChild(acknowledgedText);
            button.replaceWith(span);
      } else if(status == 0 && approved == 1){
            acknowledgedText = document.createTextNode('Unavailable');
            let span = document.createElement('span');
            span.className = 'tadi-badge tadi-badge-duedate';
            span.appendChild(acknowledgedText);
            button.replaceWith(span);
      } else if (status == 1 && approved == 0) {
            acknowledgedText = document.createTextNode('Pending Approval');
            let span = document.createElement('span');
            span.className = 'tadi-badge tadi-badge-pending';
            span.style.color = '#eed038';
            span.style.fontWeight = 'bold';
            span.style.whiteSpace = 'nowrap';
            span.appendChild(acknowledgedText);
            button.replaceWith(span);
      } else if (status == 1 && approved == 1){
            acknowledgedText = document.createTextNode('Approved');
            let span = document.createElement('span');
            span.className = 'tadi-badge tadi-badge-approved';
            span.appendChild(acknowledgedText);
            button.replaceWith(span);
      }
    });
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

document.getElementById('summaryTadiBtn').addEventListener("click",()=>{
        displaySummary();
        document.getElementById('summaryTadiBtn').style.display = 'none';
    })

document.getElementById("subjectSearch").addEventListener("keypress", function (e) {
    if (e.key === "Enter") {
        document.getElementById("searchButton").click();
    }
});

document.querySelectorAll('.button-bg-change').forEach(btn => {
    btn.addEventListener("click", DISPLAYALL_TADI_RECORDS);
});

document.getElementById('date_srch').addEventListener("click", function(){
    const summary = this.getAttribute('data-summary');
    DISPLAY_TADI_LOG(this.value, summary);
});

document.addEventListener("show.bs.modal", function (e) {
    const openModals = document.querySelectorAll(".modal.show").length;
    const baseZIndex = 1050 + (openModals * 20);
    const modalZIndex = baseZIndex + 10;
    
    // Set modal z-index immediately
    e.target.style.zIndex = modalZIndex;
});

document.addEventListener("shown.bs.modal", function (e) {
    // After modal is fully shown, fix the backdrop z-index
    const openModals = document.querySelectorAll(".modal.show").length;
    const baseZIndex = 1050 + ((openModals - 1) * 20);
    
    const backdrops = document.querySelectorAll(".modal-backdrop");
    if (backdrops.length) {
        // Set backdrop z-index lower than modal
        backdrops[backdrops.length - 1].style.zIndex = baseZIndex;
        backdrops[backdrops.length - 1].classList.add('modal-stack');
    }
    
    // Ensure the current modal stays on top
    e.target.style.zIndex = baseZIndex + 10;
});

document.addEventListener("hidden.bs.modal", function (e) {
    const openModals = document.querySelectorAll(".modal.show");
    const backdrops = document.querySelectorAll(".modal-backdrop");

    if (openModals.length > 0) {
        // Still have modals open, ensure body keeps modal-open class
        document.body.classList.add('modal-open');
        
        // Fix remaining backdrop z-index
        if (backdrops.length) {
            const baseZIndex = 1050 + ((openModals.length - 1) * 20);
            backdrops[backdrops.length - 1].style.zIndex = baseZIndex;
        }
        const topModal = openModals[openModals.length - 1];
        topModal.style.zIndex = 1050 + ((openModals.length - 1) * 20) + 10;
        topModal.focus();
    } else {
        // No modals open, clean up all backdrops
        backdrops.forEach(b => b.remove());
        document.body.classList.remove('modal-open');
        document.body.style.paddingRight = '';
        document.body.style.overflow = '';
    }
});

document.addEventListener("keydown", function(e) {
    if (e.key === 'Escape') {
        const visibleModals = document.querySelectorAll('.modal.show');
        if (visibleModals.length > 1) {
            e.preventDefault();
            e.stopImmediatePropagation();
            
            // Find the topmost modal (highest z-index)
            let topModal = null;
            let highestZ = 0;
            visibleModals.forEach(function(modal) {
                const z = parseInt(modal.style.zIndex) || parseInt(window.getComputedStyle(modal).zIndex) || 0;
                if (z > highestZ) {
                    highestZ = z;
                    topModal = modal;
                }
            });
            
            if (topModal) {
                const bsModal = bootstrap.Modal.getInstance(topModal);
                if (bsModal) {
                    bsModal.hide();
                }
            }
        }
    }
}, true);

function showAlertModal(message) {
  const modalEl = document.getElementById('alertModal');
  const modalBody = document.getElementById('alertModalBody');
  modalBody.textContent = message;
  const modal = new bootstrap.Modal(modalEl);
  modal.show();
}

function emptyCriteriaReport(){
    const academiclevel = document.getElementById('academiclevel');
    const academicyearlevel = document.getElementById('academicYearLevel');
    const academicperiod = document.getElementById('period');
    const acadyear = document.getElementById('acadyear');
    const subjCode = document.getElementById('subjectSearch');

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

    subjCode.classList.remove("border-dark");
    subjCode.classList.add("border-danger");
    subjCode.classList.add("is-invalid");
}

function resetCriteriaReport(){
    const academiclevel = document.getElementById('academiclevel');
    const academicyearlevel = document.getElementById('academicYearLevel');
    const academicperiod = document.getElementById('period');
    const acadyear = document.getElementById('acadyear');
    const subjCode = document.getElementById('subjectSearch');

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

    subjCode.classList.remove("border-danger");
    subjCode.classList.remove("is-invalid");
    subjCode.classList.add("border-dark");
}

function invalidStartDateInput(){
    const startDate = document.getElementById("strtDateSearch");
      startDate.classList.remove("border-dark");
      startDate.classList.add("border-danger");
      startDate.classList.add("is-invalid");
}

function invalidEndDateInput(){
    const endDate = document.getElementById("endDateSearch");
      endDate.classList.remove("border-dark");
      endDate.classList.add("border-danger");
      endDate.classList.add("is-invalid");
}

function resetStartEndDateInput(){
    const startDate = document.getElementById("strtDateSearch");
    const endDate = document.getElementById("endDateSearch");

      startDate.classList.add("border-dark");
      startDate.classList.remove("border-danger");
      startDate.classList.remove("is-invalid");

      endDate.classList.add("border-dark");
      endDate.classList.remove("border-danger");
      endDate.classList.remove("is-invalid");
}

function displaySummary() {
    const thead = document.getElementById('theadTable');
    const summaryCard = document.querySelector('.summary');

    summaryCard.classList.remove("summary-hide");
    document.querySelector('.inst_list_tbl_wrapper').classList.add("dashboard");
    thead.innerHTML = '';
    thead.innerHTML = `<tr id="defaultHeader">
                            <th scope="col">Section</th>
                                <th scope="col">Subject</th>
                                <th scope="col" class="text-center">Total Records</th>
                                <th scope="col" class="text-center">Unverified</th>
                                <th scope="col" class="text-center">Over Due</th>
                                <th scope="col"></th>
                        </tr>`;

    tadiSummary();
    if (!skipTadiSummaryAutoLoad) {
        tadiSummary();
        TOTAL_COUNT_SUMMARY(document.getElementById("period").value);
    } else {
        skipTadiSummaryAutoLoad = false;
    }
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