GETACADEMICLEVEL();

document.querySelector(".subj-table").style.display = "none";
document.querySelector(".instr-table").style.display = "block";

// document.getElementById("type").addEventListener("change", function () {
//     const optionValue = this.value;

//     document.querySelectorAll(".box").forEach(box => {
//         box.style.display = "none";
//     });

//     if (optionValue === "instructor") {
//         document.querySelector(".instr-table").style.display = "block";
//     } else if (optionValue === "subject") {
//         document.querySelector(".subj-table").style.display = "block";
//     }
// });

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
                ? result.map(value => `<option value="${value.acad_prd_id}">${value.acad_prd_name}</option>`).join("")
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
    tbodySpinner.innerHTML =`<tr class="loading-spinner hide">
                                    <td colspan="4">
                                        <div class="text-center">
                                            <div class="spinner-border " role="status">
                                                <span class="sr-only"></span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>`;
    
    const srchBtn = document.getElementById("search_button");
    const genReportBtn = document.getElementById("exportBtn"); 
    srchBtn.disabled = true;
    genReportBtn.disabled = true;


    fetch(`forms/tadi/dean/controller/index-info.php`, {
        method: "POST",
        body: formData1
    })
        .then(res => res.json())
        .then(result => {
            const tableRows = result.length
                ? result.map((item, index) => `
                    <tr class="inst_name" key="${item.subj_id}">
                        <td>${item.prof_name ? item.prof_name : "No instructor"}</td>
                        <td class="col-2 text-center">
                            <button class="btn btn-sm justify-content-md-center w-75 button-bg-change position-relative" ${item.prof_name ? "" : "disabled"} id="instructorModalHandler${index}" data-bs-toggle="modal" data-bs-target="#Instructor_Subject_List">
                            SECTION LIST
                            ${item.unverified_count > 0 ? `<span class="position-absolute top-0 start-100 translate-middle  p-2 bg-danger border border-light rounded-circle"></span>` : ''}
                            </button>
                        </td>
                    </tr>
                `).join("")
                : `<tr>
                    <td colspan="5" class="text-center">No data available</td>
                    </tr>`;

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

// Add to your existing search button handler
document.getElementById("exportBtn").addEventListener("click", function() {
    document.querySelector(".instr-table").style.display = "none";
    document.getElementById("tadiBtn").style.display = "block";
    document.getElementById("exportBtn").style.display = "none";
    document.getElementById("search_button").style.display = "none";
    document.getElementById("reportSearch").style.display = "block";

    const repCont = document.getElementById("reportContainer");
    repCont.style.display = "block";
    repCont.innerHTML = `<div style="text-align: center;">
                            <p>Select all filters above and click "Generate Report" to generate report.</p>
                            <p>The start date and end date can be blank.</p>
                        </div>`;
    document.getElementById("tadiTitle").innerText = "TADI Report";

    document.querySelector(".export-header").style.display = "block";
    document.querySelector(".report-container").style.display = "block";

    document.querySelectorAll(".date-range-xport").forEach(element => {
        element.style.display = "block";
    });

});

document.getElementById("tadiBtn").addEventListener("click", function() {
    document.querySelector(".instr-table").style.display = "block";
    document.getElementById("tadiBtn").style.display = "none";
    document.querySelector(".export-content").innerHTML = '';
    document.getElementById("exportBtn").style.display = "block";
    document.getElementById("search_button").style.display = "block";
    document.getElementById("reportSearch").style.display = "none";
    const start_date = document.getElementById("startDate");
    const end_date = document.getElementById("endDate");
    start_date.type = "text";
    end_date.type = "text";
    end_date.value = "";

    const repCont = document.getElementById("reportContainer");
    repCont.innerHTML = `<div style="text-align: center;">
                            <p>Select all filters above and click "Generate Report" to generate report.</p>
                            <p>The start date and end date can be blank.</p>
                        </div>`;
    repCont.style.display = "none";
    
    document.getElementById("tadiTitle").innerText = "TADI - Dean";


    document.querySelector(".export-header").style.display = "none";
    document.querySelector(".report-container").style.display = "none";

    document.querySelectorAll(".date-range-xport").forEach(element=>{
        element.style.display = "none";
    })
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