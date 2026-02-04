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
        const allIdSet = new Set(["#acadyearlevel", "#acadsection", "#acadcourse"]);
    
        // Generate "ALL" option if applicable
        const addtnl = allIdSet.has(id) && ret.length 
            ? `<option value='${ret.map(item => item.ID).join(',')}'>ALL</option>`
            : '';
    
        // Generate main options
        const options = ret.length 
            ? ret.map(item => `<option value='${item.ID}'>${item.NAME}</option>`).join('')
            : "<option value='0'>NONE</option>";
    
        $(id).html(addtnl + options);
    } catch (error) {
        console.error("Error parsing JSON: ", error);
        $(id).html("<option value='0'>Error Loading</option>");
    }
}

function fetchData(type, params = {}) {
    return $.ajax({
        type: 'GET',
        url: '../../model/forms/view/enrollmentlist/enrollmentlist-controller.php',
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

        // $("#btnSearch").click();
    } catch (error) {
        $('#errormessage').html('Error!');
    }
}

// Event Listeners for Dynamic Updates
$(document).ready(function () {
    LoadDropdowns(); // Initial Load

    $("#acadlevel").change(async function () {
        $("#table-body, #divStudentCount").empty();
        
        let levelid = $(this).val();
        let yearid = await updateDropdown('ACADYEAR', "#acadyear", { levelid });
        let periodid = await updateDropdown('ACADPERIOD', "#acadperiod", { levelid, yearid });
        let yearlevelid = await updateDropdown('ACADYEARLEVEL', "#acadyearlevel", { levelid, yearid, periodid });
        let courseid = await updateDropdown('ACADCOURSE', "#acadcourse", { levelid, yearid, periodid, yearlevelid });
        await updateDropdown('ACADSECTION', "#acadsection", { levelid, yearid, periodid, yearlevelid, courseid });
    });

    $("#acadyear").change(async function () {
        $("#table-body, #divStudentCount").empty();
        
        let levelid = $("#acadlevel").val();
        let yearid = $(this).val();
        let periodid = await updateDropdown('ACADPERIOD', "#acadperiod", { levelid, yearid });
        let yearlevelid = await updateDropdown('ACADYEARLEVEL', "#acadyearlevel", { levelid, yearid, periodid });
        let courseid = await updateDropdown('ACADCOURSE', "#acadcourse", { levelid, yearid, periodid, yearlevelid });
        await updateDropdown('ACADSECTION', "#acadsection", { levelid, yearid, periodid, yearlevelid, courseid });
    });

    $("#acadperiod").change(async function () {
        $("#table-body, #divStudentCount").empty();
        
        let levelid = $("#acadlevel").val();
        let yearid = $("#acadyear").val();
        let periodid = $(this).val();
        // let yearlevelid = $("#acadyearlevel").val();
        let yearlevelid = await updateDropdown('ACADYEARLEVEL', "#acadyearlevel", { levelid, yearid, periodid });
        let courseid = await updateDropdown('ACADCOURSE', "#acadcourse", { levelid, yearid, periodid, yearlevelid });
        await updateDropdown('ACADSECTION', "#acadsection", { levelid, yearid, periodid, yearlevelid, courseid });
    });

    $("#acadyearlevel").change(async function () {
        $("#table-body, #divStudentCount").empty();
        
        let levelid = $("#acadlevel").val();
        let yearid = $("#acadyear").val();
        let periodid = $("#acadperiod").val();
        let yearlevelid = $(this).val();
        let courseid = $("#acadcourse").val();
        // let courseid = await updateDropdown('ACADCOURSE', "#acadcourse", { levelid, yearid, periodid, yearlevelid });
        await updateDropdown('ACADSECTION', "#acadsection", { levelid, yearid, periodid, yearlevelid, courseid });
    });

    $("#acadcourse").change(async function () {
        $("#table-body, #divStudentCount").empty();

        let levelid = $("#acadlevel").val();
        let yearid = $("#acadyear").val();
        let periodid = $("#acadperiod").val();
        // let yearlevelid = $("#acadyearlevel").val();
        let yearlevelid = await updateDropdown('ACADYEARLEVEL', "#acadyearlevel", { levelid, yearid, periodid });
        let courseid = $(this).val();
        await updateDropdown('ACADSECTION', "#acadsection", { levelid, yearid, periodid, yearlevelid, courseid });
    });

    
    $("#acadcourse").change(async function () {
        $("#table-body, #divStudentCount").empty();

    });

    $("#btnSearch").click(async function () {
        $("input[type='submit'], select, button").prop("disabled", true);
        $("#table-body, #divStudentCount").empty();
        
        let levelid = $("#acadlevel").val();
        let yearid = $("#acadyear").val();
        let periodid = $("#acadperiod").val();
        let yearlevelid = $("#acadyearlevel").val();
        let courseid = $("#acadcourse").val();
        let sectionid = $("#acadsection").val();
        
        let result = await fetchData("STUDENT_LIST", { levelid, yearid, periodid, yearlevelid, courseid, sectionid });

        try {
            const ret = JSON.parse(result);
            let counter = 1;

            let table_builder = ret.length 
                ? ret.map(item => `
                    <tr>
                        <td>${counter++}</td>
                        <td class="text-start">${item.STUDENT_ID_NUMBER}</td>
                        <td class="text-start">${item.STUDENT_NAME}</td>
                        <td class="text-start">${item.SECTION} - ${item.YEAR_LEVEL}</td>
                        <td class="text-center">${item.GENDER}</td>
                        <td>${item.MOBILE}</td>
                        <td>${item.EMAIL}</td>
                    </tr>`).join('')
                : "<tr class='text-center'><td colspan='7'>No Record Found.</td></tr>";
    
            let totalcount = "<h4>Total Count: " + ret.length + " student(s)</h4>";

            $("#divStudentCount").html(totalcount);
            $("#table-body").html(table_builder);
            $("input[type='submit'], select, button").prop("disabled", false);
        } catch (error) {
            console.error("Error parsing JSON: ", error);
            $("#table-body").html("<option value='0'>Error Loading</option>");
        }
    })
});

$(document).on("click", ".btnView", async function () {
    alert();
});