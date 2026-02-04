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
    }else{
        summaryGenReport();
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
    reportCard.innerHTML = loadingRow();

    try{
        
        const request = await fetch(`forms/tadi/humanresource/controller/index-post.php`, {
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: params
        });

        const result = await request.json();
        summaryReportView(result, filterRange, dateRange, dept);
        
    }
    catch(error){
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
    reportCard.innerHTML = loadingRow();

    try{
        
        const request = await fetch(`forms/tadi/humanresource/controller/index-post.php`, {
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: params
        });

        const result = await request.json();
        detailedReportView(result, filterRange, dateRange, dept, filterType);
        
    }
    catch(error){
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

function loadingRow() {
  return `
    <tr>
      <td colspan="4">
        <div class="text-center p-3">
          <div class="spinner-border" role="status">
        </div>
      </td>
    </tr>`;
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