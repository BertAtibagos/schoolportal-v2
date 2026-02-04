GETACADEMICLEVEL();

document.querySelector(".subj-table").style.display = "none";
document.querySelector(".instr-table").style.display = "block";

document.getElementById("type").addEventListener("change", function () {
    const optionValue = this.value;

    document.querySelectorAll(".box").forEach(box => {
        box.style.display = "none";
    });

    if (optionValue === "instructor") {
        document.querySelector(".instr-table").style.display = "block";
    } else if (optionValue === "subject") {
        document.querySelector(".subj-table").style.display = "block";
    }
});

document.getElementById("academiclevel").addEventListener("change", function () {
    const lvlid = this.value;

    const formData = new FormData();
    formData.append('type', 'GET_ACADEMIC_YEAR_LEVEL');
    formData.append('lvl_id', lvlid)

    fetch(`tadi/dean/controller/index-info.php`, {
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

    fetch(`tadi/dean/controller/index-info.php`, {
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
});

document.getElementById("academicperiod").addEventListener("change", function () {
    const lvlid = document.getElementById("academiclevel").value;
    const prdid = this.value;

    const formData = new FormData();
    formData.append('type', 'GET_ACAD_YEAR');
    formData.append('lvl_id', lvlid);
    formData.append('prd_id', prdid)

    fetch(`tadi/dean/controller/index-info.php`, {
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

    const category = document.getElementById("type").value;

    const formData = new FormData();
    formData.append('type', 'GET_DEPARTMENTAL_SUBJECT');
    formData.append('lvl_id', lvlid);
    formData.append('prd_id', prdid);
    formData.append('yr_id', yrid);
    formData.append('yrlvl_id', yrlvlid);
    formData.append('category', category);

    fetch(`tadi/dean/controller/index-info.php`, {
        method: "POST",
        body: formData
    })
        .then(res => res.json())
        .then(result => {

            const tableRows = result.length
                ? result.map((item, index) => `
                    <tr key="${item.subj_Code}">
                        <td class="bysub-subj-code" id="bysubSubjCode">${item.subj_code}</td>
                        <td class="col-6 bysub-subj-desc" id="bysubSubjDesc">${item.subj_desc}</td>
                        <td>
                            <button class="btn btn-sm w-100 button-bg-change inst-subj-list" id="subjectModalHandler${index}" data-bs-toggle="modal" data-bs-target="#Subject_Instructor_List">INSTRUCTOR LIST</button>
                        </td>
                    </tr>
                `).join("")
                : `<tr class="flex justify-center align-center">
                        <td colspan="5" class="text-center">No data available</td>
                    </tr>`;

            document.getElementById("subject").innerHTML = tableRows;

            result.forEach((value, index) => {
                document.getElementById(`subjectModalHandler${index}`)?.addEventListener("click", function () {
                    GET_INSTRUCTOR_BY_SUBJECT(value);
                });
            });
        });

    const formData1 = new FormData();
    formData1.append('type', 'GET_INSTRUCTOR_LIST');
    formData1.append('lvl_id', lvlid);
    formData1.append('prd_id', prdid);
    formData1.append('yr_id', yrid);
    formData1.append('yrlvl_id', yrlvlid);

    fetch(`tadi/dean/controller/index-info.php`, {
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
                            <button class="btn btn-sm justify-content-md-center w-75 button-bg-change" ${item.prof_name ? "" : "disabled"} id="instructorModalHandler${index}" data-bs-toggle="modal" data-bs-target="#Instructor_Subject_List">SECTION LIST</button>
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
        });
});

