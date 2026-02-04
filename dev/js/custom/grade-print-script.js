const cbxCheckAll = document.getElementById('cbxCheckAll');
const tblStudent = document.getElementById('tblStudentList');
const acadlevel = document.getElementById('acadlevel')
const acadyear = document.getElementById('acadyear')
const acadperiod = document.getElementById('acadperiod')
const acadyearlevel = document.getElementById('acadyearlevel')
const acadcourse = document.getElementById('acadcourse')
const acadsection = document.getElementById('acadsection')
const acadinfotype = document.getElementById('acadinfotype')
const acadinfotext = document.getElementById('acadinfotext')
const tbodyStudentList = document.querySelector('#tblStudentList tbody');
const errormessage = document.getElementById('errormessage');
const btnSearchName = document.getElementById('btnSearchName');
const divStudentTable = document.getElementById('divStudentTable');
const divGradeTable = document.getElementById('divGradeTable');

const btnExportPdf = document.getElementById('btnExportPdf');
const btnBack = document.getElementById('btnBack');
const btnPrint = document.getElementById('btnPrint');

const tbodygrade = document.getElementById('tbody-grade');
const total_unit = document.getElementById('total_unit');
const final_grade = document.getElementById('final_grade');
const final_equivalent = document.getElementById('final_equivalent');
const studidno = document.getElementById('studidno');
const studname = document.getElementById('studname');
const studcrse = document.getElementById('studcrse');
const studyrlvl = document.getElementById('studyrlvl');


const levels = {
    'TERTIARY': 'College Department',
    'GRADUATE SCHOOL': 'Graduate School',
    'BASIC EDUCATION': 'Basic Education Department'
}

errormessage.style.display = 'none';

function MyDropdown(result, id) {
    try {
        let ret = result;
        const with_all = ['#acadyearlevel', '#acadsection', '#acadcourse']
        let options = '';

        if(with_all.includes(id)){
            let all_id = ret.length ? ret.map(item => item.ID).join(',') : ''; 
            options = `<option value='${all_id}'>ALL</option>`;
        }

        options += ret.length 
            ? ret.map(item => `<option value='${item.ID}'>${item.NAME}</option>`).join('')
            : "<option>NONE</option>";

        document.querySelector(id).innerHTML = options;
    } catch (error) {
        console.error("Error parsing JSON: ", error);
        document.querySelector(id).innerHTML = "<option>Error Loading</option>";
    }
}

async function fetchData(type, params = {}) {
    return fetch('../../model/forms/enrollment/student-grades/grade-print-controller.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({ type, ...params })
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            
            return response.json();
        })
}

async function updateDropdown(type, selector, params = {}) {
    try {
        let result = await fetchData(type, params);
        MyDropdown(result, selector);
        return document.querySelector(selector).value; // Return selected value after update
    } catch (error) {
        errormessage.innerHTML = 'Error loading ' + type;
        return null;
    }
}

async function LoadDropdowns() {
    try {
        let levelid = await updateDropdown('ACADLEVEL', "#acadlevel");
        let yearid = await updateDropdown('ACADYEAR', "#acadyear", { levelid });
        let periodid = await updateDropdown('ACADPERIOD', "#acadperiod", { levelid, yearid });
        let yearlevelid = await updateDropdown('ACADYEARLEVEL', "#acadyearlevel", { levelid, yearid, periodid });
        let courseid = await updateDropdown('ACADCOURSE', "#acadcourse", { levelid, yearid, periodid, yearlevelid });
        await updateDropdown('ACADSECTION', "#acadsection", { levelid, yearid, periodid, yearlevelid, courseid });

        btnSearchName.click();

    } catch (error) {
        errormessage.innerHTML = 'Error!';
    }
}
// Event Listeners for Dynamic Updates
LoadDropdowns(); // Initial Load

acadlevel.addEventListener('change', async () => {        
    let levelid = acadlevel.value;
    let yearid = await updateDropdown('ACADYEAR', "#acadyear", { levelid });
    let periodid = await updateDropdown('ACADPERIOD', "#acadperiod", { levelid, yearid });
    let yearlevelid = await updateDropdown('ACADYEARLEVEL', "#acadyearlevel", { levelid, yearid, periodid });
    let courseid = await updateDropdown('ACADCOURSE', "#acadcourse", { levelid, yearid, periodid, yearlevelid });
    await updateDropdown('ACADSECTION', "#acadsection", { levelid, yearid, periodid, yearlevelid, courseid });
    
    btnSearchName.click();
});

acadyear.addEventListener('change', async () => {        
    let levelid = acadlevel.value;
    let yearid = acadyear.value;
    let periodid = await updateDropdown('ACADPERIOD', "#acadperiod", { levelid, yearid });
    let yearlevelid = await updateDropdown('ACADYEARLEVEL', "#acadyearlevel", { levelid, yearid, periodid });
    let courseid = await updateDropdown('ACADCOURSE', "#acadcourse", { levelid, yearid, periodid, yearlevelid });
    await updateDropdown('ACADSECTION', "#acadsection", { levelid, yearid, periodid, yearlevelid, courseid });
    
    btnSearchName.click();
});

acadperiod.addEventListener('change', async () => {        
    let levelid = acadlevel.value;
    let yearid = acadyear.value;
    let periodid = acadperiod.value;
    // let yearlevelid = acadyearlevel.value;
    let yearlevelid = await updateDropdown('ACADYEARLEVEL', "#acadyearlevel", { levelid, yearid, periodid });
    let courseid = await updateDropdown('ACADCOURSE', "#acadcourse", { levelid, yearid, periodid, yearlevelid });
    await updateDropdown('ACADSECTION', "#acadsection", { levelid, yearid, periodid, yearlevelid, courseid });
    
    btnSearchName.click();
});

acadyearlevel.addEventListener('change', async () => {        
    let levelid = acadlevel.value;
    let yearid = acadyear.value;
    let periodid = acadperiod.value;
    let yearlevelid = acadyearlevel.value;
    let courseid = await updateDropdown('ACADCOURSE', "#acadcourse", { levelid, yearid, periodid, yearlevelid });
    await updateDropdown('ACADSECTION', "#acadsection", { levelid, yearid, periodid, yearlevelid, courseid });
    
    btnSearchName.click();
});

acadcourse.addEventListener('change', async () => {    
    let levelid = acadlevel.value;
    let yearid = acadyear.value;
    let periodid = acadperiod.value;
    let yearlevelid = acadyearlevel.value;
    let courseid = acadcourse.value;
    await updateDropdown('ACADSECTION', "#acadsection", { levelid, yearid, periodid, yearlevelid, courseid });
    
    btnSearchName.click();
});

acadsection.addEventListener('change', async () => {    
    btnSearchName.click();
});

document.getElementById('btnSearchName').addEventListener('click', async () => {
    divStudentTable.style.display = 'block';
    divGradeTable.style.display = 'none';

    let levelid = acadlevel.value;
    let yearid = acadyear.value;
    let periodid = acadperiod.value;
    let yearlevelid = acadyearlevel.value;
    let courseid = acadcourse.value;
    let sectionid = acadsection.value;
    let infotype = acadinfotype.value;
    let infotext = acadinfotext.value;

    let result = await fetchData('STUDENT_LIST', { levelid, yearid, periodid, yearlevelid, courseid, sectionid, infotype, infotext});
    
    try {
        let table_builder = result.length 
            ? result.map(item => `
                <tr>
                    <td class="text-center align-middle"><input type="checkbox" name="cbxStudentRow" class="cbxStudentRow" value="${item.STUD_ID}-${item.ASS_ID}"></td>
                    <td class="text-start align-middle">${item.STUD_NO}</td>
                    <td class="text-start align-middle">${item.FULL_NAME}</td>
                    <td class="text-start align-middle">${item.YRLVL_NAME}</td>
                    <td class="text-start align-middle">${item.CRSE_NAME}</td>
                    <td class="text-start align-middle">${item.SEC_NAME}</td>
                    <td class="text-center align-middle">
                        <button class="btn btn-sm btn-success btnGrade" value="${item.STUD_ID}-${item.ASS_ID}-${item.CRSE_ID}"> View </button>
                    </td>
                </tr>`).join('')
            : "<tr class='text-center text-danger'><td colspan='99'>No Record Found.</td></tr>";

        tbodyStudentList.innerHTML = table_builder;
        
        document.querySelectorAll('.btnGrade').forEach(btn => {
            btn.addEventListener('click', async function() {
                const tr = this.closest('tr');

                studidno.innerHTML = tr.cells[1].innerText;
                studname.innerHTML = tr.cells[2].innerText;
                studyrlvl.innerHTML = tr.cells[3].innerText;
                studcrse.innerHTML = tr.cells[4].innerText;

                total_unit.innerHTML = '';
                final_grade.innerHTML = '';
                final_equivalent.innerHTML = '';

                let studid = this.value.split('-')[0];
                let courseid = this.value.split('-')[2];
                let result = await fetchData('GRADES', {studid, levelid, yearid, periodid, courseid});

                let tbody_builder = '';
                let units = 0;
                let fe = 0;
                let fg = 0
                let counter = 0;
                if(result.length){
                    result.forEach(item => {
                        tbody_builder += `<tr>
                            <td class="text-center align-middle">${item.CODE}</td>
                            <td class="text-start align-middle desc">${item.DESCRIPTION}</td>
                            <td class="text-center align-middle">${item.UNIT}</td>
                            <td class="text-center align-middle"><span class="status">${item.STATUS}</span></td>
                            <td class="text-center align-middle">${item.FINAL_GRADE}</td>
                            <td class="text-center align-middle">${item.EQUIVALENT}</td>
                            <td class="text-center align-middle">${item.REMARKS}</td>
                        </tr>`;

                        units += parseFloat(item.UNIT)

                        if(!parseFloat(item.FINAL_GRADE) || item.FINAL_GRADE === ""){
                            var [grade, equiv] = [0, 0];
                        } else {
                            var [grade, equiv] = [parseFloat(item.FINAL_GRADE), parseFloat(item.EQUIVALENT)];
                            counter++
                        }

                        fg += grade * parseFloat(item.UNIT)
                        fe += equiv * parseFloat(item.UNIT)
                    });
                } else {
                    tbody_builder += "<tr class='text-center text-danger'><td colspan='99'>No Record Found.</td></tr>";
                }

                tbodygrade.innerHTML = tbody_builder;

                if(result.length === counter){
                    total_unit.innerHTML = units.toFixed(2)
                    final_grade.innerHTML = (fg / units).toFixed(2)
                    final_equivalent.innerHTML = (fe / units).toFixed(2)
                }

                let output = await fetchData('EQUIVALENT', { levelid, yearid, periodid });

                let additional = [
                    { EQUIV: 'INC', RANGE: 'Incomplete' },
                    { EQUIV: 'NG', RANGE: 'No Grade (Due to absences more than 20% of class days)' },
                    { EQUIV: 'IP', RANGE: 'In Progress' }
                ];

                // merge data
                output = [...output, ...additional];

                let html = "<table>";
                let rowsPerCol = 4;
                let totalCols = Math.ceil(output.length / rowsPerCol);
                for (let r = 0; r < rowsPerCol; r++) {
                    html += "<tr>";
                    for (let c = 0; c < totalCols; c++) {
                        let index = c * rowsPerCol + r;

                        if (index < output.length) {
                            let item = output[index];
                            let equiv = isNaN(item.EQUIV) ? item.EQUIV : Number(item.EQUIV).toFixed(2);
                            html += `<td style="padding-right: 2.5rem;"><span class="value fw-medium">${equiv}</span> - ${item.RANGE}</td>`;
                        } else {
                            html += "<td></td>";
                        }
                    }
                    html += "</tr>";
                }

                html += "</table>";

                document.getElementById("equiv-container").innerHTML = html;

                divStudentTable.style.display = 'none';
                divGradeTable.style.display = 'block';
            });
        });
    } catch (error) {
        console.error("Error parsing JSON: ", error);
        tbodyStudentList.innerHTML = "<tr class='text-center'><td colspan='7'>An error occured!</td></tr>";
    }
});

cbxCheckAll.addEventListener("change", () => {
    document.querySelectorAll('.cbxStudentRow').forEach(cbx => {
        cbx.checked = document.getElementById('cbxCheckAll').checked
    });
})

btnBack.addEventListener('click', () => {
    divStudentTable.style.display = 'block';
    divGradeTable.style.display = 'none';
})

btnExportPdf.addEventListener('click', () => {
    let checkedCheckboxes = document.querySelectorAll('input[name="cbxStudentRow"]:checked');
    let studid = Array.from(checkedCheckboxes).map(checkbox => checkbox.value.split('-')[0]);
    let assid = Array.from(checkedCheckboxes).map(checkbox => checkbox.value.split('-')[1]);
    console.log(studid)
    console.log(assid)
})

btnPrint.addEventListener('click', async() => {
    document.querySelectorAll('.status').forEach(element => {
        element.style.display = 'none';
    });

    const date = new Date();
    const formatted = date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
    const data = {
        name: studname.innerText,
        idno: studidno.innerText,
        yrlvl: studyrlvl.innerText,
        crse: studcrse.innerText,
        level: levels[acadlevel.options[acadlevel.selectedIndex].innerText],
        year: acadyear.options[acadyear.selectedIndex].innerText,
        period: acadperiod.options[acadperiod.selectedIndex].innerText,
        grade_table: document.getElementById('tblStudentGrades').outerHTML,
        equiv_container: document.getElementById('equiv-container').outerHTML,
        current_date: formatted
    }

    const response = await fetch('../../../public/template/print.php');
    let template = await response.text();
	console.log(data.grade_table);
	
    template = template.replace(/{{(.*?)}}/g, (match, key) => data[key] || "");
    // Open in new tab
    const newTab = window.open("");
    newTab.document.write(template);
    newTab.document.close(); // Important to finish writing
	
    document.querySelectorAll('.status').forEach(element => {
        element.style.display = 'block';
    });
})