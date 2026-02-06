function dnutChartBuilder(result){

    let valueNames = ["Verified","Unverified"];
    let valueData = [result.verified, result.unverified];
    let valueColors = ["#ffd700", "#032a74"];

    new Chart("totalChart", {
        type: "doughnut",
        data: {
            labels: valueNames,
            datasets: [{
                backgroundColor: valueColors,
                data: valueData
            }]
        },
        options:{
            plugins: {
                title: {
                    display: true,
                    text: "TADI Records Summary"
                },
                legend: {
                    display: true
                }
            }
        }
    });
}

function barChartMonthlyBuilder(result){
    
    let monthNames = [];
    let verifiedData = [];
    let unverifiedData = [];

    if(Array.isArray(result)){
        result.forEach(data =>{
            monthNames.push(data.month_name);
            verifiedData.push(data.verified);
            unverifiedData.push(data.unverified);
        });
    }

    new Chart("monthlyTotalChart", {
        type: "bar",
        data: {
            labels: monthNames,
            datasets: [
                {
                    label: "Verified",
                    backgroundColor: "#ffd700",
                    data: verifiedData
                },
                {
                    label: "Unverified",
                    backgroundColor: "#032a74",
                    data: unverifiedData
                }
            ]
        },
        options:{
            plugins: {
                legend: {
                    display: true
                },
                title: {
                    display: true,
                    text: "Monthly TADI Records"
                }
            }
        }
    });
}

function barChartPerCutBuilder(result){
    
    let periodNames = [];
    let verifiedData = [];
    let unverifiedData = [];

    if(Array.isArray(result)){
        result.forEach(data =>{
            periodNames.push(data.cutoff_period);
            verifiedData.push(data.verified);
            unverifiedData.push(data.unverified);
        });
    }

    new Chart("perCutOffChart", {
        type: "bar",
        data: {
            labels: periodNames,
            datasets: [
                {
                    label: "Verified",
                    backgroundColor: "#ffd700",
                    data: verifiedData
                },
                {
                    label: "Unverified",
                    backgroundColor: "#032a74",
                    data: unverifiedData
                }
            ]
        },
        options:{
            responsive: true,
            plugins: {
                legend: {
                    display: true
                },
                title: {
                    display: true,
                    text: "Per Cut-off TADI Records"
                }
            }
        }
    });
}

function vertBarChartPerDeptBuilder(result){
    
    let programNames = [];
    let verifiedData = [];
    let unverifiedData = [];

    if(Array.isArray(result)){
        result.forEach(data =>{
            programNames.push(data.program_name);
            verifiedData.push(data.verified_count);
            unverifiedData.push(data.unverified_count);
        });
    }

    new Chart("vertPerDeptChart", {
        type: "bar",
        data: {
            labels: programNames,
            datasets: [
                {
                    label: "Verified",
                    backgroundColor: "#ffd700",
                    data: verifiedData
                },
                {
                    label: "Unverified",
                    backgroundColor: "#032a74",
                    data: unverifiedData
                }
            ]
        },
        options:{
            indexAxis: 'y',
            responsive: true,
            plugins: {
                legend: {
                    display: true
                },
                title: {
                    display: true,
                    text: "Per Program TADI Records"
                }
            }
        }
    });
}

function detailedReportView(result, filterRange, date, dept, filterType){

    if(!Array.isArray(result) || result.length === 0){
        const reportCard = document.getElementById('reportView'); 
        const srchBtn = document.getElementById('generateBtn');
        srchBtn.disabled = false;
        reportCard.innerHTML = '<div class="alert alert-warning" role="alert">No records found for the selected criteria.</div>';
        return;
    }

    const reportCard = document.getElementById('reportView'); 
    const srchBtn = document.getElementById('generateBtn');
    srchBtn.disabled = false;
    reportCard.innerHTML = '';
    const timeFormat = getCutoffDates();
    let fileName = '';

    // Clear existing report content if any
    let reportTable = document.getElementById('reportTable');
    if(reportTable) reportTable.remove();
    let exportContainer = document.getElementById('exportContainer');
    if(exportContainer) exportContainer.remove();
    let reportSummary = document.getElementById('reportSummary');
    if(reportSummary) reportSummary.remove();

    // Group records by teacher and then by subject
    const teacherGroups = {};
    let totalTeachers = 0;
    let totalSessions = 0;
    let verifiedSessions = 0;

    result.forEach(data => {
        const profId = data.prof_name || 'Unknown';
        const subjectKey = data.subject_code || 'Unknown';

        if (!teacherGroups[profId]) {
            teacherGroups[profId] = {
                prof_name: data.prof_name,
                subjects: {}
            };
            totalTeachers++;
        }

        if (!teacherGroups[profId].subjects[subjectKey]) {
            teacherGroups[profId].subjects[subjectKey] = {
                subject_code: data.subject_code,
                subject_desc: data.subject_desc,
                section_name: data.section_name,
                sessions: []
            };
        }

        teacherGroups[profId].subjects[subjectKey].sessions.push({
            date: data.tadi_date,
            time_in: formatTime(data.time_in),
            time_out: formatTime(data.time_out),
            duration: data.duration,
            mode: (data.mode || '-').replace(/_/g, ' '),
            type: data.type,
            stud_name: data.student_name,
            activity: data.activity,
            late_status: data.late_status,
            status: data.status,
            approved: data.approved
        });

        totalSessions++;
        if(data.status == 1) verifiedSessions++;
    });

    const stats = {
        totalTeachers: totalTeachers,
        totalSessions: totalSessions,
        verifiedSessions: verifiedSessions
    };

    // Create export button
    const exportDiv = document.createElement('div');
    exportDiv.id = 'exportContainer';
    exportDiv.className = 'mb-3 d-flex justify-content-between';
    
    const exportBtn = document.createElement('button');
    exportBtn.className = 'btn btn-success';
    exportBtn.textContent = 'Export to CSV';

    const reportLabel = document.createElement('h3');
    reportLabel.className = 'me-3 fw-bold';

    result.forEach(data => {

        let deptName = '';
            switch(dept){
                case 'COAM':
                    deptName = 'CAMS';
                    break;
                case 'COLA':
                    deptName = 'CAS';
                    break;
                case 'COCS':
                    deptName = 'CCS';
                    break;
                case 'COCJ':
                    deptName = 'CCJ';
                case 'COE':
                    deptName = 'CE';
                default:
                    deptName = dept;
            }

        if(filterRange === 'currCutOff'){

            if (filterType === 'deptName_all') {
                reportLabel.textContent = `Current Cut-off Report as of ${timeFormat.current_cutoff_start} to ${timeFormat.current_cutoff_end} for All Departments`;
                fileName = `CURRENT_CUT-OFF_REPORT_${timeFormat.current_cutoff_start}_TO_${timeFormat.current_cutoff_end}_ALL_DEPARTMENTS.csv`;
            }else if(filterType === 'byName'){
                reportLabel.textContent = `Current Cut-off Report as of ${timeFormat.current_cutoff_start} to ${timeFormat.current_cutoff_end} for ${data.prof_name}`;
                fileName = `CURRENT_CUT-OFF_REPORT_${timeFormat.current_cutoff_start}_TO_${timeFormat.current_cutoff_end}_${data.prof_name.replace(/[,]/g, '_').toUpperCase()}.csv`;
            }else if(filterType === 'byDept'){
                reportLabel.textContent = `Current Cut-off Report as of ${timeFormat.current_cutoff_start} to ${timeFormat.current_cutoff_end} for ${deptName}`;
                fileName = `CURRENT_CUT-OFF_REPORT_${timeFormat.current_cutoff_start}_TO_${timeFormat.current_cutoff_end}_${deptName}.csv`;
            }
        };

        if(filterRange === 'prevCutOff'){

            if (filterType === 'deptName_all') {
                reportLabel.textContent = `Previous Cut-off Report as of ${timeFormat.prev_cutoff_start} to ${timeFormat.prev_cutoff_end} for All Departments`;
                fileName = `PREVIOUS_CUT-OFF_REPORT_${timeFormat.prev_cutoff_start}_TO_${timeFormat.prev_cutoff_end}_ALL_DEPARTMENTS.csv`;
            }else if(filterType === 'byName'){
                reportLabel.textContent = `Previous Cut-off Report as of ${timeFormat.prev_cutoff_start} to ${timeFormat.prev_cutoff_end} for ${data.prof_name}`;
                fileName = `PREVIOUS_CUT-OFF_REPORT_${timeFormat.prev_cutoff_start}_TO_${timeFormat.prev_cutoff_end}_${data.prof_name.replace(/[,]/g, '_').toUpperCase()}.csv`;
            }else if(filterType === 'byDept'){
                reportLabel.textContent = `Previous Cut-off Report as of ${timeFormat.prev_cutoff_start} to ${timeFormat.prev_cutoff_end} for ${deptName}`;
                fileName = `PREVIOUS_CUT-OFF_REPORT_${timeFormat.prev_cutoff_start}_TO_${timeFormat.prev_cutoff_end}_${deptName}.csv`;
            }
        }

        if(filterRange === 'date'){

            if (filterType === 'deptName_all') {
                reportLabel.textContent = `Report as of ${date.startDate} to ${date.endDate} for All Departments`;
                fileName = `REPORT_${date.startDate}_TO_${date.endDate}_ALL_DEPARTMENTS.csv`;
            }else if(filterType === 'byName'){
                reportLabel.textContent = `Report as of ${date.startDate} to ${date.endDate} for ${data.prof_name}`;
                fileName = `REPORT_${date.startDate}_TO_${date.endDate}_${data.prof_name.replace(/[,]/g, '_').toUpperCase()}.csv`;
            }else if(filterType === 'byDept'){
                reportLabel.textContent = `Report as of ${date.startDate} to ${date.endDate} for ${deptName}`;
                fileName = `REPORT_${date.startDate}_TO_${date.endDate}_${deptName}.csv`;
            }
        }
    })

    
    exportBtn.addEventListener('click', () => exportTableToCSV('reportTable', fileName));
    exportDiv.appendChild(reportLabel);
    exportDiv.appendChild(exportBtn);
    reportCard.appendChild(exportDiv);

    // Create report summary
    const summaryDiv = document.createElement('div');
    summaryDiv.id = 'reportSummary';
    summaryDiv.innerHTML = `
        <div class="card mb-4">
            <div class="card-header card-header-summary text-white">
                <h5 class="mb-0">Report Summary</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="border rounded p-3 text-center">
                            <h6>Total Teachers</h6>
                            <h3>${stats.totalTeachers}</h3>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded p-3 text-center">
                            <h6>Total Sessions</h6>
                            <h3>${stats.totalSessions}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    reportCard.appendChild(summaryDiv);

    // Create teacher cards
    Object.entries(teacherGroups).sort().forEach(([profId, teacher]) => {
        const teacherCard = document.createElement('div');
        teacherCard.className = 'card mb-4';
        
        const totalSessions = Object.values(teacher.subjects).reduce((sum, subj) => sum + subj.sessions.length, 0);
        
        teacherCard.innerHTML = `
            <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">${teacher.prof_name}</h5>
                <span class="badge bg-light text-dark">${totalSessions} sessions</span>
            </div>
            <div class="card-body">
                ${Object.values(teacher.subjects).map((subject, idx) => `
                    <div class="mb-4" ${idx > 0 ? 'style="border-top: 1px solid #dee2e6; padding-top: 1rem;"' : ''}>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0"><strong>${subject.subject_code}</strong> - ${subject.subject_desc}</h6>
                            <span class="badge badge-bg">${subject.section_name || 'No Section'}</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th class="col-1 text-black">Date</th>
                                        <th class="col-1 text-black">Time In</th>
                                        <th class="col-1 text-black">Time Out</th>
                                        <th class="col-1 text-black">Duration</th>
                                        <th class="col-1 text-black">Mode</th>
                                        <th class="col-1 text-black">Type</th>
                                        <th class="text-black">Activity</th>
                                        <th class="col-1 text-black">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${subject.sessions.map(session => `
                                        ${session.approved == 0 ? `<tr style="background-color: #fff3cd;">
                                                                    <td colspan="8" class="text-center py-3">
                                                                        <i class="fas fa-exclamation-triangle text-warning"></i>
                                                                        <span class="text-muted ms-2">Record pending Approval</span>
                                                                    </td>
                                                                </tr>` : 
                                        `<tr>
                                            <td>${session.date}</td>
                                            <td>${session.time_in}</td>
                                            <td>${session.time_out}</td>
                                            <td>${session.duration}</td>
                                            <td>${session.mode == 'online learning' ? 'Online' : 'Onsite'}</td>
                                            <td>${session.type == 'regular' ? 'Regular' : 'Make-up'}</td>
                                            <td>${session.activity || '-'}</td>
                                            <td style="text-align: center;">
                                                <span class="badge ${session.status == 1 ? 'bg-success' : 'bg-warning'}">
                                                    ${session.status == 1 ? 'Verified' : 'Unverified'}
                                                </span>
                                                ${session.late_status == 1 ? `<br><span class="badge bg-warning">Late Submission</span>` : ''}
                                            </td>
                                        </tr>`
                                        }
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                `).join('')}
            </div>
        `;

        reportCard.appendChild(teacherCard);
    });

    // Add hidden table for CSV export
    const hiddenTable = document.createElement('table');
    hiddenTable.id = 'reportTable';
    hiddenTable.style.display = 'none';
    
    const thead = document.createElement('thead');
    const headerRow = document.createElement('tr');
    headerRow.innerHTML = `
        <th>Teacher</th>
        <th>Subject Code</th>
        <th>Subject Description</th>
        <th>Section</th>
        <th>Date</th>
        <th>Time In</th>
        <th>Time Out</th>
        <th>Duration</th>
        <th>Mode</th>
        <th>Type</th>
        <th>Activity</th>
        <th>Late Submission</th>
        <th>Status</th>
    `;
    thead.appendChild(headerRow);
    hiddenTable.appendChild(thead);

    const tbody = document.createElement('tbody');
    Object.values(teacherGroups).forEach(teacher => {
        Object.values(teacher.subjects).forEach(subject => {
            subject.sessions.forEach(session => {
                // Only include verified records in CSV export
                if(session.status == 1) {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${teacher.prof_name}</td>
                        <td>${subject.subject_code}</td>
                        <td>${subject.subject_desc}</td>
                        <td>${subject.section_name || '-'}</td>
                        <td>${session.date}</td>
                        <td>${session.time_in}</td>
                        <td>${session.time_out}</td>
                        <td>${session.duration}</td>
                        <td>${session.mode}</td>
                        <td>${session.type || '-'}</td>
                        <td>${session.activity || '-'}</td>
                        <td>${session.late_status == 1 ? 'Yes' : 'No'}</td>
                        <td>${session.status == 1 ? 'Verified' : 'Unverified'}</td>
                    `;
                    tbody.appendChild(row);
                }
            });
        });
    });
    hiddenTable.appendChild(tbody);
    reportCard.appendChild(hiddenTable);
}

function summaryReportView(result, filterRange, date, dept){

    if(!Array.isArray(result) || result.length === 0){
        const reportCard = document.getElementById('reportView'); 
        const srchBtn = document.getElementById('generateBtn');
        srchBtn.disabled = false;
        reportCard.innerHTML = '<div class="alert alert-warning" role="alert">No records found for the selected criteria.</div>';
        return;
    }


    const reportCard = document.getElementById('reportView'); 
    const srchBtn = document.getElementById('generateBtn');
    srchBtn.disabled = false;
    reportCard.innerHTML = '';
    const timeFormat = getCutoffDates();
    let fileName = '';

    // Clear existing report content if any
    let reportTable = document.getElementById('reportTable');
    if(reportTable) reportTable.remove();
    let exportContainer = document.getElementById('exportContainer');
    if(exportContainer) exportContainer.remove();
    let reportSummary = document.getElementById('reportSummary');
    if(reportSummary) reportSummary.remove();

    // Group instructors by department
    const deptGroups = {};
    let totalInstructors = 0;
    let totalVerified = 0;
    let totalUnverified = 0;
    let totalRecords = 0;

    result.forEach(data => {
        const deptName = data.dept_code;

        let deptInitial = '';
            switch(deptName){
                case 'COAM':
                    deptInitial = 'CAMS';
                    break;
                case 'COLA':
                    deptInitial = 'CAS';
                    break;
                case 'COCS':
                    deptInitial = 'CCS';
                    break;
                case 'COCJ':
                    deptInitial = 'CCJ';
                case 'COE':
                    deptInitial = 'CE';
                default:
                    deptInitial = deptName;
            }
        
        if (!deptGroups[deptInitial]) {
            deptGroups[deptInitial] = {
                department: deptInitial,
                instructors: []
            };
        }

        deptGroups[deptInitial].instructors.push({
            prof_name: data.prof_name,
            verified_count: data.verified_count,
            unverified_count: data.unverified_count,
            total_count: data.total_count
        });

        totalInstructors++;
        totalVerified += parseInt(data.verified_count) || 0;
        totalUnverified += parseInt(data.unverified_count) || 0;
        totalRecords += parseInt(data.total_count) || 0;
    });

    const stats = {
        totalInstructors: totalInstructors,
        totalVerified: totalVerified,
        totalUnverified: totalUnverified,
        totalRecords: totalRecords
    };

    // Create export button
    const exportDiv = document.createElement('div');
    exportDiv.id = 'exportContainer';
    exportDiv.className = 'mb-3 d-flex justify-content-between';
    
    const exportBtn = document.createElement('button');
    exportBtn.className = 'btn btn-success';
    exportBtn.textContent = 'Export to CSV';

    const reportLabel = document.createElement('h3');
    reportLabel.className = 'me-3 fw-bold';

     let deptName = '';
            switch(dept){
                case 'COAM':
                    deptName = 'College of Allied Medicine';
                    break;
                case 'COLA':
                    deptName = 'College of Arts and Sciences';
                    break;
                case 'COCS':
                    deptName = 'College of Computer Studies';
                    break;
                case 'COCJ':
                    deptName = 'College of Criminal Justice';
                    break;
                case 'COE':
                    deptName = 'College of Engeneering';
                    break;
                case 'COED':
                    deptName = 'College of Education';
                    break;
                case 'COA':
                    deptName = 'College of Accountancy';
                    break;
                case 'COBM':
                    deptName = 'College of Business Management';
                    break;
                default:
                    deptName = "Unknown Department";
            }


    if(filterRange === 'currCutOff'){
        reportLabel.textContent = `Current Cut-off Instructor Summary ${timeFormat.current_cutoff_start} to ${timeFormat.current_cutoff_end} for ${deptName}`;
        fileName = `INSTRUCTOR_SUMMARY_${timeFormat.current_cutoff_start}_TO_${timeFormat.current_cutoff_end}_${deptName}.csv`;
    }

    if(filterRange === 'prevCutOff'){
        reportLabel.textContent = `Previous Cut-off Instructor Summary  ${timeFormat.prev_cutoff_start} to ${timeFormat.prev_cutoff_end} for ${deptName}`;
        fileName = `INSTRUCTOR_SUMMARY_${timeFormat.prev_cutoff_start}_TO_${timeFormat.prev_cutoff_end}_${deptName}.csv`;
    }

    if(filterRange === 'date'){
        reportLabel.textContent = `Instructor Summary Report  ${date.startDate} to ${date.endDate} for ${deptName}`;
        fileName = `INSTRUCTOR_SUMMARY_${date.startDate}_TO_${date.endDate}_${deptName}.csv`;
    }

    exportBtn.addEventListener('click', () => exportTableToCSV('reportTable', fileName));
    exportDiv.appendChild(reportLabel);
    exportDiv.appendChild(exportBtn);
    reportCard.appendChild(exportDiv);

    // Create report summary
    const summaryDiv = document.createElement('div');
    summaryDiv.id = 'reportSummary';
    summaryDiv.innerHTML = `
        <div class="card mb-4">
            <div class="card-header card-header-summary text-white">
                <h5 class="mb-0">Summary Statistics</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="border rounded p-3 text-center">
                            <h6>Total Instructors</h6>
                            <h3>${stats.totalInstructors}</h3>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3 text-center" style="background-color: #9bbdff70;">
                            <h6>Total Verified</h6>
                            <h3>${stats.totalVerified}</h3>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3 text-center" style="background-color: #ff970670;">
                            <h6>Total Unverified</h6>
                            <h3>${stats.totalUnverified}</h3>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3 text-center" style="background-color: #c9c9c970;">
                            <h6>Total Records</h6>
                            <h3>${stats.totalRecords}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    reportCard.appendChild(summaryDiv);

    // Create department cards with instructor tables
    Object.entries(deptGroups).sort().forEach(([deptInitial, dept]) => {
        const deptCard = document.createElement('div');
        deptCard.className = 'card mb-4';
        
        const deptTotal = dept.instructors.reduce((sum, instr) => sum + parseInt(instr.total_count), 0);
        
        deptCard.innerHTML = `
            <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">${deptInitial}</h5>
                <span class="badge bg-light text-dark">${dept.instructors.length} instructors</span>
            </div> 
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th class="text-black">Instructor Name</th>
                                <th class="text-center text-black" style="background-color: #9bbdff70;">Verified</th>
                                <th class="text-center text-black" style="background-color: #ff970678;">Unverified</th>
                                <th class="text-center text-black">Total</th>
                                <th class="text-center text-black">Verification Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${dept.instructors.map(instr => {
                                const total = parseInt(instr.total_count) || 0;
                                const verified = parseInt(instr.verified_count) || 0;
                                const rate = total > 0 ? ((verified / total) * 100).toFixed(2) : 0;
                                return `
                                    <tr>
                                        <td>${instr.prof_name}</td>
                                        <td class="text-center"><strong>${instr.verified_count}</strong></td>
                                        <td class="text-center"><strong>${instr.unverified_count}</strong></td>
                                        <td class="text-center"><strong>${instr.total_count}</strong></td>
                                        <td class="text-center">
                                            <span class="badge ${instr.verified_count == 0 && instr.unverified_count == 0 && instr.total_count == 0 ? 'bg-secondary' 
                                                                : rate >= 80 ? 'bg-success' : rate >= 50 ? 'bg-warning' : 'bg-danger'}">
                                                ${rate}%
                                            </span>
                                        </td>
                                    </tr>
                                `;
                            }).join('')}
                        </tbody>
                    </table>
                </div>
            </div>
        `;

        reportCard.appendChild(deptCard);
    });

    // Add hidden table for CSV export
    const hiddenTable = document.createElement('table');
    hiddenTable.id = 'reportTable';
    hiddenTable.style.display = 'none';
    
    const thead = document.createElement('thead');
    const headerRow = document.createElement('tr');
    headerRow.innerHTML = `
        <th>Department</th>
        <th>Instructor Name</th>
        <th>Verified</th>
        <th>Unverified</th>
        <th>Total</th>
        <th>Verification Rate</th>
    `;
    thead.appendChild(headerRow);
    hiddenTable.appendChild(thead);

    const tbody = document.createElement('tbody');
    Object.values(deptGroups).forEach(dept => {
        dept.instructors.forEach(instr => {
            const total = parseInt(instr.total_count) || 0;
            const verified = parseInt(instr.verified_count) || 0;
            const rate = total > 0 ? ((verified / total) * 100).toFixed(2) : 0;
            
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${dept.department}</td>
                <td>${instr.prof_name}</td>
                <td>${instr.verified_count}</td>
                <td>${instr.unverified_count}</td>
                <td>${instr.total_count}</td>
                <td>${rate}%</td>
            `;
            tbody.appendChild(row);
        });
    });
    hiddenTable.appendChild(tbody);
    reportCard.appendChild(hiddenTable);
}