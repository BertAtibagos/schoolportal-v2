function getStatusText(statusCode, approver) {
    const statusMap = {
        0: `Grades are encoded but not yet submitted.<sup>[${statusCode}]</sup>`,
        1: `Grades are submitted to <u>${approver} (Dean / Program Head)</u> for approval.<sup>[${statusCode}]</sup>`,
        2: `Grade Submission to <u>${approver} (Registrar)</u> was denied.<sup>[${statusCode}]</sup>`,
        3: `Grade Submission was approved by <u>${approver} (Dean / Program Head)</u> and forwarded to the Registrar.<sup>[${statusCode}]</sup>`,
        4: `Grade Submission was denied by <u>${approver} (Dean / Program Head)</u> and returned to the instructor.<sup>[${statusCode}]</sup>`,
        5: `Grade Submission was approved by  <u>${approver} (Registrar)</u> and are now viewable by students.<sup>[${statusCode}]</sup>`,
        6: `<div class='text-info-emphasis'>Instructor requested to edit the grades. Request approved by <u>${approver}</u>. <sup>[${statusCode}]</sup></div>`,
        7: `Request to edit grades was denied. Request denied by <u>${approver}</u>.<sup>[${statusCode}]</sup>`
    };

    return statusMap[statusCode] || "Unknown status code.";
}

function cleanScheduleStr(rawsched){
    try {
        let schedule = rawsched.split(",").length ? 
            rawsched.split(",").map(item => 
                item.split("=").slice(1, -1).join(", ")
        ).join('<br>') : 'No Schedule Yet.';
        return schedule;
    } catch {
        return rawsched;
    }
}

function MyDropdown(result, id) {
    try {
        const ret = JSON.parse(result);
        let options = ret.length 
            ? ret.map(item => `<option value='${item.ID}'>${item.NAME}</option>`).join('')
            : "<option value='0'>NONE</option>";

        $(id).html(options);
    } catch (error) {
        console.error("Error parsing JSON: ", error);
        $(id).html("<option value='0'>Error Loading</option>");
    }
}

function fetchData(type, params = {}) {
    return $.ajax({
        type: 'POST',
        url: '../../model/forms/academic/grade-history/grade-history-controller.php',
        data: { type, ...params }
    });
}

async function updateDropdown(type, selector, params = {}) {
    try {
        let result = await fetchData(type, params);
        MyDropdown(result, selector);
        return $(selector).val(); // Return selected value after update
    } catch (error) {
        $('#errormessage').html('Error loading ' + type);
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

    } catch (error) {
        $('#errormessage').html('Error!');
    }
}

// Event Listeners for Dynamic Updates
$(document).ready(function () {
    LoadDropdowns(); // Initial Load

    $("#acadlevel").change(async function () {
        $("#table-body").empty();
        
        let levelid = $(this).val();
        let yearid = await updateDropdown('ACADYEAR', "#acadyear", { levelid });
        let periodid = await updateDropdown('ACADPERIOD', "#acadperiod", { levelid, yearid });
        let yearlevelid = await updateDropdown('ACADYEARLEVEL', "#acadyearlevel", { levelid, yearid, periodid });
        let courseid = await updateDropdown('ACADCOURSE', "#acadcourse", { levelid, yearid, periodid, yearlevelid });
        await updateDropdown('ACADSECTION', "#acadsection", { levelid, yearid, periodid, yearlevelid, courseid });
    });

    $("#acadyear").change(async function () {
        $("#table-body").empty();
        
        let levelid = $("#acadlevel").val();
        let yearid = $(this).val();
        let periodid = await updateDropdown('ACADPERIOD', "#acadperiod", { levelid, yearid });
        let yearlevelid = await updateDropdown('ACADYEARLEVEL', "#acadyearlevel", { levelid, yearid, periodid });
        let courseid = await updateDropdown('ACADCOURSE', "#acadcourse", { levelid, yearid, periodid, yearlevelid });
        await updateDropdown('ACADSECTION', "#acadsection", { levelid, yearid, periodid, yearlevelid, courseid });
    });

    $("#acadperiod").change(async function () {
        $("#table-body").empty();
        
        let levelid = $("#acadlevel").val();
        let yearid = $("#acadyear").val();
        let periodid = $(this).val();
        let yearlevelid = $("#acadyearlevel").val();
        // let yearlevelid = await updateDropdown('ACADYEARLEVEL', "#acadyearlevel", { levelid, yearid, periodid });
        let courseid = await updateDropdown('ACADCOURSE', "#acadcourse", { levelid, yearid, periodid, yearlevelid });
        await updateDropdown('ACADSECTION', "#acadsection", { levelid, yearid, periodid, yearlevelid, courseid });
    });

    $("#acadyearlevel").change(async function () {
        $("#table-body").empty();
        
        let levelid = $("#acadlevel").val();
        let yearid = $("#acadyear").val();
        let periodid = $("#acadperiod").val();
        let yearlevelid = $(this).val();
        let courseid = await updateDropdown('ACADCOURSE', "#acadcourse", { levelid, yearid, periodid, yearlevelid });
        await updateDropdown('ACADSECTION', "#acadsection", { levelid, yearid, periodid, yearlevelid, courseid });
    });

    $("#acadcourse").change(async function () {
        $("#table-body").empty();

        let levelid = $("#acadlevel").val();
        let yearid = $("#acadyear").val();
        let periodid = $("#acadperiod").val();
        let yearlevelid = $("#acadyearlevel").val();
        let courseid = $(this).val();
        await updateDropdown('ACADSECTION', "#acadsection", { levelid, yearid, periodid, yearlevelid, courseid });
    });

    
    $("#acadcourse").change(async function () {
        $("#table-body").empty();

    });

    $("#btnSearch").click(async function () {
        $("input[type='submit'], select").prop("disabled", true);
        $("#table-body").empty();
        
        let levelid = $("#acadlevel").val();
        let yearid = $("#acadyear").val();
        let periodid = $("#acadperiod").val();
        let yearlevelid = $("#acadyearlevel").val();
        let courseid = $("#acadcourse").val();
        let sectionid = $("#acadsection").val();
        
        let result = await fetchData("SUBJECT_LIST", { levelid, yearid, periodid, yearlevelid, courseid, sectionid });

        try {
            const ret = JSON.parse(result);
            let counter = 1;

            let table_builder = ret.length 
                ? ret.map(item => `
                    <tr>
                        <td class='text-center'>${counter++}</td>
                        <td>${item.CODE}</td>
                        <td>${item.DESCRIPTION}</td>
                        <td class='text-center'>${item.UNIT}</td>
                        <td>${cleanScheduleStr(item.SCHEDULE)}</td>
                        <td>${item.INSTRUCTOR}</td>
                        <td class="text-center"><button class="btn btn-sm btn-primary btnView" data-bs-toggle="modal" data-bs-target="#recordHistoryModal" value='${item.OFF_ID}'>View</button></td>
                    </tr>`).join('')
                : "<tr class='text-center'><td colspan='99' class='text-danger'>No Record Found.</td></tr>";
    
            $("#table-body").html(table_builder);
            $("input[type='submit'], select").prop("disabled", false);
        } catch (error) {
            console.error("Error parsing JSON: ", error);
            $("#table-body").html("<option value='0'>Error Loading</option>");
        }
    })
});

$(document).on("click", ".btnView", async function () {
    let subjid = $(this).val();
    
    let result = await fetchData("GET_HISTORY", {subjid});
    const ret = JSON.parse(result);
    let counter = 1;

    // Get the current request status
    let lastStatus = ret.length ? parseInt(ret[ret.length - 1].CURRENT_STATUS) : 0;

    // Status nodes and icons mapping
    const statusMapping = [
        { node: "#node1", icon: "#icon1", line: "#line1 > div" },
        { node: "#node2", icon: "#icon2", line: "#line2 > div" },
        { node: "#node3", icon: "#icon3", line: "#line3 > div" },
        { node: "#node4", icon: "#icon4", line: "#line4 > div" }
    ];

    // Loop through the mapping for each status
    statusMapping.forEach((status, index) => {
        // Apply background color and icon update based on the current REQ_STATUS
        if (lastStatus >= index) {
            $(status.node).css("background-color", "var(--bs-success)");
            $(status.icon).removeClass("fa-circle").addClass("fa-circle-check");
            $(status.line).css("background-color", "var(--bs-success)");
        } else {
            $(status.node).css("background-color", "var(--bs-gray-600)");
            $(status.icon).addClass("fa-circle").removeClass("fa-circle-check");
            $(status.line).css("background-color", "var(--bs-gray-600)");
        }
    });

    let table_builder = ret.length 
        ? ret.map(item => `
            <tr>
                <td>${counter++}</td>
                <td class="text-start">${item.DATE}</td>
                <td class="text-start">${getStatusText(item.REQ_STATUS, item.APPROVER)}</td>
                <td class="text-start d-none">${item.APPROVER}</td>
            </tr>`).join('')
        : "<tr class='text-center'><td colspan='99' class='text-danger'>No Submission History Found.</td></tr>";

    $("#history_body").html(table_builder);
});