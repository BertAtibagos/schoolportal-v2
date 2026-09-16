function setupTabulationCheckboxes() {
    document.addEventListener('change', event => {
        const checkbox = event.target;

        if (checkbox.matches('#selectAllRows')) {
            const table = checkbox.closest('table');
            table.querySelectorAll('.row-checkbox').forEach(rowCheckbox => {
                rowCheckbox.checked = checkbox.checked;
            });
            checkbox.indeterminate = false;
            return;
        }

        if (checkbox.matches('.row-checkbox')) {
            const table = checkbox.closest('table');
            const selectAllCheckbox = table.querySelector('#selectAllRows');
            const rowCheckboxes = [...table.querySelectorAll('.row-checkbox')];
            const checkedCount = rowCheckboxes.filter(rowCheckbox => rowCheckbox.checked).length;

            selectAllCheckbox.checked = rowCheckboxes.length > 0 && checkedCount === rowCheckboxes.length;
            selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < rowCheckboxes.length;
        }
    });
}

setupTabulationCheckboxes();

async function dashBoardContent(){

    try{

        const totalStatsRequest = await fetch(`forms/tadi/humanresource/controller/index-post.php`,{
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: new URLSearchParams({
                type: "GET_ALL_TOTAL"
            })
        });

        const fetchMonthlyTotalRequest = await fetch(`forms/tadi/humanresource/controller/index-post.php`,{
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: new URLSearchParams({
                type: "GET_TOTAL_PER_MONTH"
            })
        });

        const fetchDeptTotalRequest = await fetch(`forms/tadi/humanresource/controller/index-post.php`,{
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: new URLSearchParams({
                type: "GET_ALL_PROG_TOTAL"
            })
        });

        const fetchPerCutOffTotalRequest = await fetch(`forms/tadi/humanresource/controller/index-post.php`,{
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: new URLSearchParams({
                type: "GET_TOTAL_PER_CUTOFF"
            })
        });

        const totalStatsResult = await totalStatsRequest.json();
        const fetchMonthlyTotalResult = await fetchMonthlyTotalRequest.json();
        const fetchPerCutOffTotalResult = await fetchPerCutOffTotalRequest.json();
        const fetchDeptTotalResult = await fetchDeptTotalRequest.json();

        document.getElementById("verified").textContent = totalStatsResult.verified;
        document.getElementById("unverified").textContent = totalStatsResult.unverified;
        document.getElementById("total").textContent = totalStatsResult.total_rec;

        barChartMonthlyBuilder(fetchMonthlyTotalResult);

        barChartPerCutBuilder(fetchPerCutOffTotalResult);

        vertBarChartPerDeptBuilder(fetchDeptTotalResult);
        document.getElementById('generateBtn').disabled = false;

    }
    catch(error){
        const srchBtn = document.getElementById('generateBtn');
        document.getElementById('reportView').innerHTML = '<div class="alert alert-danger" style="text-align: center">Error loading Dashboard. Please log in again.</div>';
        srchBtn.disabled = false;
        console.log("ERROR: ", error);
    }
}
dashBoardContent();

document.getElementById('generateBtn').addEventListener("click", (e)=>{
    const filterMode = document.getElementById('filterMode').value;
    if(filterMode == 'detailed'){
        detailedGenReport();
    }else if(filterMode == 'summary'){
        summaryGenReport();
    }else{
        tabulationReport();
    }
});

async function summaryGenReport(){
    const byDateOrByCutOff = document.getElementById('perCutoffByDate').value;

    let filterRange = '';
    let dateRange = {startDate: '', endDate: ''};
    let dept = '';

    const params = new URLSearchParams({
        type: "GET_INSTRUCTOR_LIST_DEPT_SUMMARY"
    });

    if(byDateOrByCutOff == 'date'){
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;

        filterRange = 'date';
        dateRange.startDate = startDate;
        dateRange.endDate = endDate;

        const deptSelect = document.getElementById('deptSelect').value;
        params.append('rangeType', 'byDate');
        params.append('filterType', 'dept_Search');
        params.append('startDate', startDate);
        params.append('endDate', endDate);
        params.append('dept', deptSelect);
        dept = deptSelect;
    }
    
    if(byDateOrByCutOff == 'currCutOff'){

        filterRange = 'currCutOff';

        const deptSelect = document.getElementById('deptSelect').value;
        params.append('rangeType', 'currCutOff');
        params.append('filterType', 'dept_Search');
        params.append('dept', deptSelect);
        dept = deptSelect;
    }
    
    if(byDateOrByCutOff == 'prevCutOff'){

        filterRange = 'prevCutOff';
        
        const deptSelect = document.getElementById('deptSelect').value;
        params.append('rangeType', 'prevCutOff');
        params.append('filterType', 'dept_Search');
        params.append('dept', deptSelect);
        dept = deptSelect;
    }

    const reportCard = document.getElementById('reportView');
    const srchBtn = document.getElementById('generateBtn');
    srchBtn.disabled = true;
    showLoadingModal();

    try{
        
        const request = await fetch(`forms/tadi/humanresource/controller/index-post.php`, {
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: params
        });

        const result = await request.json();
        hideLoadingModal();
        summaryReportView(result, filterRange, dateRange, dept);
        
    }
    catch(error){
         hideLoadingModal();
         console.log("ERROR: ", error);
         srchBtn.disabled = false;
         document.getElementById('reportView').innerHTML = '<div class="alert alert-danger">Error loading report. Please try again.</div>';
    }
}

async function detailedGenReport(){
    const byDateOrByCutOff = document.getElementById('perCutoffByDate').value;
    const byAllOrByNameDept = document.getElementById('byAllNameDept').value;

    let filterRange = '';
    let dateRange = {startDate: '', endDate: ''};
    let dept = '';
    let filterType = '';

    const params = new URLSearchParams({
        type: "GET_TADI_DETAILS_BY_CUTOFF"
    });

    if(byDateOrByCutOff == 'date'){
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;

        if (!startDate || !endDate) {
            alert('Please select both start and end dates.');
            document.getElementById('startDate').classList.add('is-invalid');
            document.getElementById('endDate').classList.add('is-invalid');
            return;
        }else if (startDate > endDate) {
            alert('Start date cannot be later than end date.');
            document.getElementById('startDate').classList.add('is-invalid');
            document.getElementById('endDate').classList.add('is-invalid');
            return;
        }else{
            document.getElementById('startDate').classList.remove('is-invalid');
            document.getElementById('endDate').classList.remove('is-invalid');
        }

        filterRange = 'date';
        dateRange.startDate = startDate;
        dateRange.endDate = endDate;

        if(byAllOrByNameDept == 'all'){
            params.append('rangeType', 'byDate');
            params.append('startDate', startDate);
            params.append('endDate', endDate);
            params.append('filterType', 'deptName_all');
            filterType = 'deptName_all';
        }else if(byAllOrByNameDept == 'byName'){
            const nameSearch = document.getElementById('nameSearch').value;

            if(!nameSearch){
                document.getElementById('nameSearch').classList.add('is-invalid');
                alert("Please enter a name to search.");
                return;
            }else{
                document.getElementById('nameSearch').classList.remove('is-invalid');
            }
            params.append('rangeType', 'byDate');
            params.append('filterType', 'name_Search');
            params.append('startDate', startDate);
            params.append('endDate', endDate);
            params.append('name', nameSearch);
            filterType = 'byName';
        }else if(byAllOrByNameDept == 'byDept'){
            const deptSelect = document.getElementById('deptSelect').value;
            params.append('rangeType', 'byDate');
            params.append('filterType', 'dept_Search');
            params.append('startDate', startDate);
            params.append('endDate', endDate);
            params.append('dept', deptSelect);
            dept = deptSelect;
            filterType = 'byDept';
        }
    }
    
    if(byDateOrByCutOff == 'currCutOff'){

        filterRange = 'currCutOff';

        if(byAllOrByNameDept == 'all'){
            params.append('rangeType', 'currCutOff');
            params.append('filterType', 'deptName_all');
            filterType = 'deptName_all';
        }else if(byAllOrByNameDept == 'byName'){
            const nameSearch = document.getElementById('nameSearch').value;

            if(!nameSearch){
                document.getElementById('nameSearch').classList.add('is-invalid');
                alert("Please enter a name to search.");
                return;
            }else{
                document.getElementById('nameSearch').classList.remove('is-invalid');
            }

            params.append('rangeType', 'currCutOff');
            params.append('filterType', 'name_Search');
            params.append('name', nameSearch);
            filterType = 'byName';
        }else if(byAllOrByNameDept == 'byDept'){
            const deptSelect = document.getElementById('deptSelect').value;
            params.append('rangeType', 'currCutOff');
            params.append('filterType', 'dept_Search');
            params.append('dept', deptSelect);
            dept = deptSelect;
            filterType = 'byDept';
        }
    }
    
    if(byDateOrByCutOff == 'prevCutOff'){

        filterRange = 'prevCutOff';
        if(byAllOrByNameDept == 'all'){
            params.append('rangeType', 'prevCutOff');
            params.append('filterType', 'deptName_all');
            filterType = 'deptName_all';
        }else if(byAllOrByNameDept == 'byName'){
            const nameSearch = document.getElementById('nameSearch').value;

            if(!nameSearch){
                document.getElementById('nameSearch').classList.add('is-invalid');
                alert("Please enter a name to search.");
                return;
            }else{
                document.getElementById('nameSearch').classList.remove('is-invalid');
            }

            params.append('rangeType', 'prevCutOff');
            params.append('filterType', 'name_Search');
            params.append('name', nameSearch);
            filterType = 'byName';
        }else if(byAllOrByNameDept == 'byDept'){
            const deptSelect = document.getElementById('deptSelect').value;
            params.append('rangeType', 'prevCutOff');
            params.append('filterType', 'dept_Search');
            params.append('dept', deptSelect);
            dept = deptSelect;
            filterType = 'byDept';
        }
    }

    const reportCard = document.getElementById('reportView');
    const srchBtn = document.getElementById('generateBtn');
    srchBtn.disabled = true;
    showLoadingModal();

    try{
        
        const request = await fetch(`forms/tadi/humanresource/controller/index-post.php`, {
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: params
        });

        const result = await request.json();
        hideLoadingModal();
        detailedReportView(result, filterRange, dateRange, dept, filterType);
        
    }
    catch(error){
         hideLoadingModal();
         console.log("ERROR: ", error);
         srchBtn.disabled = false;
         document.getElementById('reportView').innerHTML = '<div class="alert alert-danger">Error loading report. Please try again.</div>';
    }
}

function formatTime(timeString){
    if(!timeString || timeString === '-') return '-';
    
    try {
        const [hours, minutes, seconds] = timeString.split(':');
        let hour = parseInt(hours);
        const minute = minutes;
        const ampm = hour >= 12 ? 'PM' : 'AM';
        
        hour = hour % 12;
        hour = hour ? hour : 12;
        
        return `${hour}:${minute} ${ampm}`;
    } catch(e) {
        return timeString;
    }
}

async function exportTableToExcel(tableId, filename){
    const table = document.getElementById(tableId);

    if(!table || typeof ExcelJS === 'undefined'){
        alert('Excel export is unavailable. Please reload the page and try again.');
        return;
    }

    const headers = Array.from(table.querySelectorAll('thead th'), th => th.textContent.trim());
    const rows = Array.from(table.querySelectorAll('tbody tr'))
        .filter(tr => !tr.classList.contains('table-info'))
        .map(tr => Array.from(tr.querySelectorAll('td'), td => {
            const value = td.textContent.trim();
            return value !== '' && !Number.isNaN(Number(value)) ? Number(value) : value;
        }))
        .filter(row => row.length > 0);

    const NAME_COL_INDEX = 0; // adjust if needed
    const TOTAL_HOURS_COL_INDEX = headers.findIndex(h =>
        h.toLowerCase().includes('total accumulated hours')
    );

    // Group index per row, based on the name column defining block boundaries
    let currentGroup = -1;
    const rowGroupIndex = rows.map(row => {
        if (row[NAME_COL_INDEX] !== '' && row[NAME_COL_INDEX] !== undefined && row[NAME_COL_INDEX] !== null) {
            currentGroup++;
        }
        return currentGroup;
    });

    const workbook = new ExcelJS.Workbook();
    workbook.creator = 'School Portal';
    workbook.created = new Date();
    const worksheet = workbook.addWorksheet('Report', {
        views: [{ state: 'frozen', ySplit: 1 }]
    });

    worksheet.addRow(headers);
    rows.forEach(row => worksheet.addRow(row));

    // --- Merge any column using the SAME group boundaries as the name column ---
    const mergeColumnByGroup = (colIndex) => {
        if (colIndex < 0) return;

        let blockStartIdx = 0;
        for (let i = 1; i <= rowGroupIndex.length; i++) {
            const isNewGroup = i === rowGroupIndex.length || rowGroupIndex[i] !== rowGroupIndex[blockStartIdx];
            if (isNewGroup) {
                const endIdx = i - 1;
                if (endIdx > blockStartIdx) {
                    const startRow = blockStartIdx + 2;
                    const endRow = endIdx + 2;
                    worksheet.mergeCells(startRow, colIndex + 1, endRow, colIndex + 1);
                    // alignment now handled centrally in the eachRow loop below
                }
                blockStartIdx = i;
            }
        }
    };

    mergeColumnByGroup(NAME_COL_INDEX);
    mergeColumnByGroup(TOTAL_HOURS_COL_INDEX);

    const borderColor = { argb: 'FFD9E2F0' };
    const headerRow = worksheet.getRow(1);
    headerRow.height = 28;
    headerRow.eachCell(cell => {
        cell.font = { bold: true, color: { argb: 'FFFFFFFF' } };
        cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF032A74' } };
        cell.alignment = { horizontal: 'center', vertical: 'middle', wrapText: true };
        cell.border = { top: { style: 'thin', color: borderColor }, left: { style: 'thin', color: borderColor }, bottom: { style: 'thin', color: borderColor }, right: { style: 'thin', color: borderColor } };
    });

    worksheet.autoFilter = { from: 'A1', to: `${String.fromCharCode(64 + headers.length)}1` };
    worksheet.eachRow((row, rowNumber) => {
        if(rowNumber === 1) return;
        row.height = 22;
        const groupIdx = rowGroupIndex[rowNumber - 2];
        row.eachCell((cell, colNumber) => {
            const isTotalHoursCol = colNumber - 1 === TOTAL_HOURS_COL_INDEX;

            cell.alignment = {
                vertical: 'middle',
                horizontal: isTotalHoursCol ? 'center' : undefined,
                wrapText: true,
            };
            cell.border = { top: { style: 'thin', color: borderColor }, left: { style: 'thin', color: borderColor }, bottom: { style: 'thin', color: borderColor }, right: { style: 'thin', color: borderColor } };
            if(groupIdx % 2 === 0) cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFF3F6FA' } };
            if(typeof cell.value === 'number') cell.numFmt = Number.isInteger(cell.value) ? '0' : '0.00';
        });
    });

    worksheet.columns.forEach(column => {
        let width = 12;
        const header = String(headers[column.number - 1] || '').toLowerCase();
        const isSubjectColumn = header.includes('subject');

        column.eachCell({ includeEmpty: true }, cell => {
            const longestLine = String(cell.value || '')
                .split(/\r?\n/)
                .reduce((longest, line) => Math.max(longest, line.length), 0);
            width = Math.max(width, longestLine + 2);
        });

        if (isSubjectColumn) {
            width = Math.max(width, 18);
            column.width = width;
        } else {
            column.width = Math.min(width, 35);
        }
    });

    const buffer = await workbook.xlsx.writeBuffer();
    const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = filename.replace(/\.csv$/i, '.xlsx');
    link.click();
    URL.revokeObjectURL(link.href);
}

let loadingModalInstance = null;

function showLoadingModal() {
    // Remove existing modal if any
    hideLoadingModal();

  const modalHTML = `
    <div class="modal fade" id="loadingModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-body text-center p-5">
            <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;"></div>
            <p class="mb-0 fw-semibold">Loading, please wait...</p>
          </div>
        </div>
      </div>
    </div>`;

    document.body.insertAdjacentHTML('beforeend', modalHTML);
    const modalEl = document.getElementById('loadingModal');
    loadingModalInstance = new bootstrap.Modal(modalEl);
    loadingModalInstance.show();
}

function hideLoadingModal() {
    const modalEl = document.getElementById('loadingModal');
    const modal = loadingModalInstance || (modalEl ? bootstrap.Modal.getInstance(modalEl) : null);

    if (!modalEl) {
        document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('padding-right');
        loadingModalInstance = null;
        return;
    }

    if (modal) {
        modalEl.addEventListener('hidden.bs.modal', () => {
            modal.dispose();
            modalEl.remove();
            document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('padding-right');
            loadingModalInstance = null;
        }, { once: true });
        modal.hide();
        return;
    }

    modalEl.remove();
    document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
    document.body.classList.remove('modal-open');
    document.body.style.removeProperty('padding-right');
    loadingModalInstance = null;
}

function getCutoffDates() {
    const today = new Date();
    const current_day = today.getDate();
    const current_month = String(today.getMonth() + 1).padStart(2, '0');
    const current_year = today.getFullYear();

    let current_cutoff_start, current_cutoff_end;
    let prev_cutoff_start, prev_cutoff_end;

    // Determine current cut-off period
    if (current_day <= 15) {
        current_cutoff_start = `${current_year}-${current_month}-01`;
        current_cutoff_end = `${current_year}-${current_month}-15`;
        
        // Previous cut-off is 16-end of previous month
        const prevMonth = new Date(current_year, parseInt(current_month) - 2, 1);
        const prevMonthStr = String(prevMonth.getMonth() + 1).padStart(2, '0');
        const prevYear = prevMonth.getFullYear();
        const lastDayPrevMonth = new Date(prevYear, parseInt(prevMonthStr), 0).getDate();
        
        prev_cutoff_start = `${prevYear}-${prevMonthStr}-16`;
        prev_cutoff_end = `${prevYear}-${prevMonthStr}-${lastDayPrevMonth}`;
    } else {
        current_cutoff_start = `${current_year}-${current_month}-16`;
        const lastDay = new Date(current_year, parseInt(current_month), 0).getDate();
        current_cutoff_end = `${current_year}-${current_month}-${lastDay}`;
        
        // Previous cut-off is 1-15 of current month
        prev_cutoff_start = `${current_year}-${current_month}-01`;
        prev_cutoff_end = `${current_year}-${current_month}-15`;
    }

    return {
        current_cutoff_start,
        current_cutoff_end,
        prev_cutoff_start,
        prev_cutoff_end
    };
}

function GET_ACADEMICLEVEL() {
    let isFirstLoad = true;  // Flag to track initial load

    fetch("forms/tadi/humanresource/controller/index-post.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: new URLSearchParams({
            type: "GET_ACADEMIC_LEVEL"
        })
    })
    .then(res => res.json())
    .then(result => {
        let optLevel = result.length
            ? result.map(value => `<option value="${value.AcadLvl_ID}">${value.AcadLvl_Name}</option>`).join("")
            : "<option>No Academic Level Found.</option>";
        document.querySelector("#academiclevel").insertAdjacentHTML('beforeend', optLevel);

        const lvlid = document.getElementById('academiclevel');
        
        // Only trigger on first load
        if (isFirstLoad) {
            getAcademicPeriods(lvlid.value);
            isFirstLoad = false;
        }

        // Event listener for subsequent changes
        lvlid.addEventListener("change", function() {
            const lvlid = this.value;
            getAcademicPeriods(lvlid);
        });
    })
    .catch(err => console.error("Error fetching academic levels:", err));
}

function getAcademicPeriods(lvlid) {
    // Remove existing event listener first
    const periodSelect = document.querySelector("#period");
    const existingHandler = periodSelect._changeHandler;
    if (existingHandler) {
        periodSelect.removeEventListener("change", existingHandler);
    }

    fetch("forms/tadi/humanresource/controller/index-post.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: new URLSearchParams({
            type: "GET_ACADEMIC_PERIOD",
            lvl_id: lvlid
        })
    })
    .then(res => res.json())
    .then(result => {
        periodSelect.innerHTML = result.length
            ? result.map(value => `<option value="${value.acad_prd_id}" ${value.is_current == 1 ? "selected" : ""}>${value.acad_prd_name}</option>`).join("")
            : "<option>No Period Found.</option>";

        // Create new handler
        const changeHandler = function() {
            const lvlid = document.querySelector("#academiclevel").value;
            const prdid = this.value;
            getAcademicYears(lvlid, prdid, true);
        };

        // Store handler reference
        periodSelect._changeHandler = changeHandler;

        // Add new event listener
        periodSelect.addEventListener("change", changeHandler);

        // Only dispatch change event on first load
        if (!periodSelect._initialized) {
            periodSelect.dispatchEvent(new Event("change"));
            periodSelect._initialized = true;
        }
    })
    .catch(err => console.error("Error fetching periods:", err));
}

function getAcademicYears(lvlid, prdid) {
  const searchButton = document.getElementById("searchButton");
  fetch("forms/tadi/humanresource/controller/index-post.php", {
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
  .then(res => res.json())
  .then(result => {
    const select = document.querySelector("#acadyear");
    select.innerHTML = result.length
      ? result.map(value => `<option value="${value.Period_id}">${value.YEAR_NAME}</option>`).join("")
      : "<option>No Year Found.</option>";
  })
  .catch(err => console.error("Error fetching academic years:", err));
}

async function tabulationReport(dateStart = '', dateEnd = ''){
    const byAllNameDept = document.getElementById('byAllNameDept').value;
    const lvlid = document.getElementById('academiclevel').value;
    const prdid = document.getElementById('period').value;
    const acadyr = document.getElementById('acadyear').value;
    let filterType = '';
    let dept = '';
    let dateRange = { startDate: dateStart, endDate: dateEnd };

    const params = new URLSearchParams({
        type: "GET_TABULATION",
        lvlid: lvlid,
        prdid: prdid,
        acadyr: acadyr
    });

    if(dateStart && dateEnd){
        params.append('startDate', dateStart);
        params.append('endDate', dateEnd);
    }

    if(byAllNameDept == 'byName'){
        const nameSearch = document.getElementById('nameSearch').value;
        filterType = byAllNameDept;
        params.append('name', nameSearch);
        params.append('filterType', filterType);
    }else if(byAllNameDept == 'byDept'){
        const deptSelect = document.getElementById('deptSelect').value;
        dept = deptSelect;
        filterType = byAllNameDept;
        params.append('dept', deptSelect);
        params.append('filterType', filterType);
    }else{
        dept = 'all';
        filterType = 'all';
        params.append('filterType', 'all');
    }

    const reportCard = document.getElementById('reportView');
    const srchBtn = document.getElementById('generateBtn');
    srchBtn.disabled = true;
    showLoadingModal();

    try{
        const request = await fetch(`forms/tadi/humanresource/controller/index-post.php`, {
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: params
        });

        const result = await request.json();
        hideLoadingModal();
        tabulationReportView(result, filterType, dept, dateRange);

    }catch(error){
        hideLoadingModal();
        console.log("Error fetching: ", error);
    }
}

function parseProfUnitHours(value) {
    const parsedHours = {};

    if (value === null || value === undefined || value === '') {
        return parsedHours;
    }

    const normalizedValue = Array.isArray(value) ? value.join(',') : String(value);

    normalizedValue.split(',').forEach(entry => {
        const trimmedEntry = entry.trim();
        if (!trimmedEntry) {
            return;
        }

        const parts = trimmedEntry.split(/[:=]/);
        if (parts.length < 2) {
            return;
        }

        const profId = parseInt(parts[0].trim(), 10);
        const profHours = parseFloat(parts[1].trim());

        if (Number.isFinite(profId) && Number.isFinite(profHours)) {
            parsedHours[profId] = profHours;
        }
    });

    return parsedHours;
}

function getAssignedProfHours(data) {
    const profId = parseInt(String(data.prof_id ?? '').trim(), 10);
    const profHoursMap = parseProfUnitHours(data.prof_unit_hrs ?? data.SchlProf_UNIT_HRS);
    const subjectUnitHrs = parseFloat(data.subj_unit_hrs ?? data.SchlEnrollSubjOff_UNIT_HRS);
    const assignedProfHours = Number.isFinite(profId) && Object.prototype.hasOwnProperty.call(profHoursMap, profId)
        ? profHoursMap[profId]
        : 0;
    const totalAssignedHours = Object.values(profHoursMap).reduce((sum, hours) => sum + (parseFloat(hours) || 0), 0);
    const unitsMatch = Number.isFinite(subjectUnitHrs) && Math.abs(totalAssignedHours - subjectUnitHrs) < 0.0001;

    return {
        profId,
        profHoursMap,
        assignedProfHours,
        subjectUnitHrs,
        totalAssignedHours,
        isValid: assignedProfHours > 0 && unitsMatch
    };
}

async function confirmProfDetails(profId, subjCode, profHrs, subjHrs) {
    if ([profId, subjCode, profHrs, subjHrs].some(v => v === undefined || v === null || v === "")) {
        console.error("Missing required parameters for confirming professor details.");
        return null;
    }

    const params = new URLSearchParams({
        type: "RECORD_TABULATION",
        prof_id: profId,
        subj_code: subjCode,
        prof_hrs: profHrs,
        subj_hrs: subjHrs
    });

    let res = null;

    try {
        const req = await fetch(`forms/tadi/humanresource/controller/index-post.php`, {
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: params
        });

        res = await req.json();

        if (!req.ok) {
            console.error("Tabulation request failed:", res.error || req.status);
        }

    } catch(error) {
        console.error("Error confirming professor details: ", error);
    }

    return res;
}

// document.querySelector('.conf-record').addEventListener('click',(e)=>{
//     const btn = e.currentTarget;
//     const profId   = btn.dataset.profId;
//     const subjCode = btn.dataset.subjCode;
//     const profHrs  = btn.dataset.profHrs;
//     const subjHrs  = btn.dataset.subjHrs;

    
// })

async function creditMark(data){
    if (!confirm('Are you sure you want to credit this generated tabulation?')) return;

    if (!Array.isArray(data) || data.length === 0) {
        alert('No TADI records found to credit.');
        return;
    }

    try {
        const params = new URLSearchParams({
            type: "CREDIT_RECORD_TABULATION"
        });
        data.forEach(tadiId => params.append('tadiIds[]', String(tadiId)));

        const req = await fetch(`forms/tadi/humanresource/controller/index-post.php`, {
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: params
        });

        const res = await req.json();

        if (!req.ok) {
            console.error("Credit request failed:", res.error || req.status);
            alert("Failed to credit the tabulation. Please try again.");
        } else {
            alert("Tabulation credited successfully.");
            tabulationReport();
        }
    } catch(e) {
        console.error("Error crediting tabulation: ", e);
        alert("An error occurred while crediting the tabulation. Please try again.");
    }
}