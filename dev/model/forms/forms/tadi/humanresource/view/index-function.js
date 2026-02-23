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

function exportTableToCSV(tableId, filename){
    const table = document.getElementById(tableId);
    let csv = [];
    
    // Get headers
    const headers = [];
    table.querySelectorAll('thead th').forEach(th => {
        headers.push('"' + th.textContent.trim().replace(/"/g, '""') + '"');
    });
    csv.push(headers.join(','));
    
    // Get rows (excluding professor header rows)
    table.querySelectorAll('tbody tr').forEach(tr => {
        if(!tr.classList.contains('table-info')) {
            const row = [];
            tr.querySelectorAll('td').forEach(td => {
                let text = td.textContent.trim().replace(/"/g, '""');
                row.push('"' + text + '"');
            });
            if(row.length > 0) csv.push(row.join(','));
        }
    });
    
    // Create blob and download
    const csvContent = 'data:text/csv;charset=utf-8,' + csv.join('\n');
    const link = document.createElement('a');
    link.setAttribute('href', encodeURI(csvContent));
    link.setAttribute('download', filename);
    link.click();
}

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
  const modal = new bootstrap.Modal(document.getElementById('loadingModal'));
  modal.show();
}

function hideLoadingModal() {
  const modalEl = document.getElementById('loadingModal');
  if (modalEl) {
    const modal = bootstrap.Modal.getInstance(modalEl);
    if (modal) {
      modal.hide();
    }
    modalEl.addEventListener('hidden.bs.modal', () => modalEl.remove(), { once: true });
    // Fallback removal in case event doesn't fire
    setTimeout(() => { if (document.getElementById('loadingModal')) modalEl.remove(); }, 500);
  }
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
            ? result.map(value => `<option value="${value.acad_prd_id}">${value.acad_prd_name}</option>`).join("")
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