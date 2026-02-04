import {toggleLoader} from '../custom/loader.js';

function calcTertiaryGS(result) {
    let units = []
    let grades = []
    let equivs = []

    let count = result.length;
    const centeredKeys = ["UNIT", "FINAL_GRADE", "EQUIVALENT", "REMARKS"];

    let keys = Object.keys(result[0]).slice(0, -1); // get keys of the first record in result except last
    let head = '<tr>'
    head += keys.length ? keys .map(item => {
                const isCentered = centeredKeys.includes(item);
                return `<th${isCentered ? ' class="text-center"' : ''}> ${item.replaceAll("_", " ")} </th>`;
            }).join(''): '';
    head += '</tr>';
    let toDisplay = 0;

    let row = count ? result.map((item) => {
        toDisplay = item.DISPLAY;

        item.UNIT && units.push(parseFloat(item.UNIT));
        item.FINAL_GRADE && grades.push(parseFloat(item.FINAL_GRADE) * parseFloat(item.UNIT));
        item.EQUIVALENT && equivs.push(parseFloat(item.EQUIVALENT) * parseFloat(item.UNIT));

        return `<tr>
            <td>${item.CODE}</td>
            <td>${item.DESCRIPTION}</td>
            <td class="text-center">${item.UNIT}</td>
            <td>${item.INSTRUCTOR}</td>
            <!-- <th>${item.SCHEDULE}</th> -->
            <td class="text-center">${item.FINAL_GRADE}</td>
            <td class="text-center">${item.EQUIVALENT}</td>
            <td class="text-center">${item.REMARKS}</td>
            <td>${item.STATUS}</td>
        </td>`}).join('') :
        `<tr>
            <td colspan="9"> No Matching Record Found! </td>
        </td>`;

    let unit = count === units.length ? (units.reduce((accumulator, currentValue) => accumulator + currentValue, 0)).toFixed(2) : 0;
    let gwa = count === grades.length ? (grades.reduce((accumulator, currentValue) => accumulator + currentValue, 0) / parseFloat(unit)).toFixed(2) : '';
    let equivalent = count === equivs.length ? (equivs.reduce((accumulator, currentValue) => accumulator + currentValue, 0) / parseFloat(unit)).toFixed(2) : '';

    let foot = count ? `
        <tr>
            <td colspan="2" class="text-end"><strong><u>TOTAL UNITS EARNED:</u></strong></td>
            <td id="totalUnits" class="text-center"><strong><u>${gwa && unit}</u></strong></td>
            <td class="text-end"><strong><u>GWA:</u></strong></td>
            <td id="gwaGrade" class="text-center"><strong><u>${gwa}</u></strong></td>
            <td id="gwaEquiv" class="text-center"><strong><u>${equivalent}</u></strong></td>
            <td colspan="2"></td>
        </tr>
    ` : '';
    return { head: head, row: row, foot: foot, toDisplay: toDisplay };
}

function calcBasicEd(result) {
    // let grades = []

    let keys = Object.keys(result[0]).slice(0, -1); // get keys of the first record in result except last
    let head = `
        <tr>
            ${keys.length ? keys.map(item => `<th rowspan="2">${item.replaceAll("_", " ")}</th>`).join('') : ''}
            <th colspan="4">QUARTER</th>
            <th rowspan="2">FINAL GRADE</th>
            <th rowspan="2">REMARKS</th>
            <th rowspan="2">STATUS</th>
        </tr>
        <tr>
            <th>1</th>
            <th>2</th>
            <th>3</th>
            <th>4</th>
        </tr>
    `;

    let count = result.length;
    let toDisplay = 0;

    let row = count ? result.map((item) => {
        toDisplay = item.DISPLAY;
        // item.FINAL_GRADE && grades.push(parseFloat(item.FINAL_GRADE));

        return `<tr>
            <td>${item.DESCRIPTION}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </td>`}).join('') :
        `<tr>
            <td colspan="8"> No Matching Record Found! </td>
        </td>`;

    // let gwa = count === grades.length ? (grades.reduce((accumulator, currentValue) => accumulator + currentValue, 0) / count) : '';

    let foot = count ? `
        <tr>
            <td colspan="5" class="text-end">General Average:</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    ` : '';
    return { head: head, row: row, foot: foot, toDisplay: toDisplay };
}

function buildTable(result) {
    const thead = document.getElementById('thead-grades');
    const tbody = document.getElementById('tbody-grades');
    const tfoot = document.getElementById('tfoot-grades');
    const message = document.getElementById('errormessage');

    const level = document.querySelector('#acadlevel');
    const level_name = level.options[level.selectedIndex].text;

    const tertiary_and_GS = ['TERTIARY', 'GRADUATE SCHOOL']
    var tableContent = {};

    if (tertiary_and_GS.includes(level_name.toUpperCase())) {
        tableContent = calcTertiaryGS(result);
    } else {
        tableContent = calcBasicEd(result);

    }

    if (tableContent.toDisplay == 0) {
        let mess = `<p style='color: red; margin-bottom: 0;'><i><b><u>NOTE:</b></u></i></p>
                    <p style='color: black; margin-bottom: 0;'><i>This is a gentle reminder that there is an outstanding balance on your account. To access your grades, please settle your account promptly.</p>`;
        message.innerHTML = mess;
        message.style.display = 'block';
    }

    thead.innerHTML = tableContent.head;
    tbody.innerHTML = tableContent.row;
    tfoot.innerHTML = tableContent.foot;
}

async function buildDropdown(url, id, data) {
    const params = new URLSearchParams(data);
    const response = await fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: params.toString()
    });

    if (!response.ok) {
        throw new Error('Network Error!');
    }

    const html = await response.text();

    // ✅ Set the innerHTML of the select tag here, fetch return is html code of options
    document.getElementById(id).innerHTML = html;

    return document.getElementById(id).value;
}

async function fetchData(url, data) {
    const params = new URLSearchParams(data);
    const response = await fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: params.toString()
    });

    if (!response.ok) {
        throw new Error('Network Error!');
    }

    return response.json();
}


async function Initialize(url) {
    toggleLoader('disable'); // disables all
    let levelid = await buildDropdown(url, 'acadlevel', { type: 'LEVEL' });
    let yearid = await buildDropdown(url, 'acadyear', { type: 'YEAR', levelid });
    let periodid = await buildDropdown(url, 'acadperiod', { type: 'PERIOD', levelid, yearid });
    let courseid = await buildDropdown(url, 'acadcourse', { type: 'COURSE', levelid, yearid, periodid });
    let data = await fetchData(url, { type: 'DISPLAY', levelid, yearid, periodid, courseid });
    buildTable(data);
	toggleLoader('enable');  // enables all
}

window.onload = () => {
    const url = '../../model/forms/academic/subjectschedulegrades/subjectschedulegrades-controller.php';

    Initialize(url);

    // Event Listeners
    const doc = {
        acadlevel: document.getElementById('acadlevel'),
        acadyear: document.getElementById('acadyear'),
        acadperiod: document.getElementById('acadperiod'),
        acadcourse: document.getElementById('acadcourse'),
        btnSearch: document.getElementById('btnSearch'),
        theadGrades: document.getElementById('thead-grades'),
        tbodyGrades: document.getElementById('tbody-grades'),
        tfootGrades: document.getElementById('tfoot-grades'),
        message: document.getElementById('errormessage')

    }

    doc.acadlevel.addEventListener('change', async () => {
    	toggleLoader('disable'); // disables all
        doc.theadGrades.innerHTML = '';
        doc.tbodyGrades.innerHTML = '';
        doc.tfootGrades.innerHTML = '';
        doc.message.innerHTML = '';
        doc.message.style.display = 'none';

        let levelid = doc.acadlevel.value;
        let yearid = await buildDropdown(url, 'acadyear', { type: 'YEAR', levelid });
        let periodid = await buildDropdown(url, 'acadperiod', { type: 'PERIOD', levelid, yearid });
        let courseid = await buildDropdown(url, 'acadcourse', { type: 'COURSE', levelid, yearid, periodid });
        let data = await fetchData(url, { type: 'DISPLAY', levelid, yearid, periodid, courseid });
        buildTable(data);
    	toggleLoader('enable');  // enables all
    })

    doc.acadyear.addEventListener('change', async () => {
    	toggleLoader('disable'); // disables all
        doc.theadGrades.innerHTML = '';
        doc.tbodyGrades.innerHTML = '';
        doc.tfootGrades.innerHTML = '';
        doc.message.innerHTML = '';
        doc.message.style.display = 'none';

        let levelid = doc.acadlevel.value;
        let yearid = doc.acadyear.value;
        let periodid = await buildDropdown(url, 'acadperiod', { type: 'PERIOD', levelid, yearid });
        let courseid = await buildDropdown(url, 'acadcourse', { type: 'COURSE', levelid, yearid, periodid });
        let data = await fetchData(url, { type: 'DISPLAY', levelid, yearid, periodid, courseid });
        buildTable(data);
    	toggleLoader('enable');  // enables all
    })

    doc.acadperiod.addEventListener('change', async () => {
    	toggleLoader('disable'); // disables all
        doc.theadGrades.innerHTML = '';
        doc.tbodyGrades.innerHTML = '';
        doc.tfootGrades.innerHTML = '';
        doc.message.innerHTML = '';
        doc.message.style.display = 'none';

        let levelid = doc.acadlevel.value;
        let yearid = doc.acadyear.value;
        let periodid = doc.acadperiod.value;
        let courseid = await buildDropdown(url, 'acadcourse', { type: 'COURSE', levelid, yearid, periodid });
        let data = await fetchData(url, { type: 'DISPLAY', levelid, yearid, periodid, courseid });
        buildTable(data);
    	toggleLoader('enable');  // enables all
    })

    doc.acadcourse.addEventListener('change', async () => {
    	toggleLoader('disable'); // disables all
        doc.theadGrades.innerHTML = '';
        doc.tbodyGrades.innerHTML = '';
        doc.tfootGrades.innerHTML = '';
        doc.message.innerHTML = '';
        doc.message.style.display = 'none';

        let levelid = doc.acadlevel.value;
        let yearid = doc.acadyear.value;
        let periodid = doc.acadperiod.value;
        let courseid = doc.acadcourse.value;
        let data = await fetchData(url, { type: 'DISPLAY', levelid, yearid, periodid, courseid });
        buildTable(data);
    	toggleLoader('enable');  // enables all
    })

    doc.btnSearch.addEventListener('click', async () => {
    	toggleLoader('disable'); // disables all
        doc.theadGrades.innerHTML = '';
        doc.tbodyGrades.innerHTML = '';
        doc.tfootGrades.innerHTML = '';
        doc.message.innerHTML = '';
        doc.message.style.display = 'none';

        let levelid = doc.acadlevel.value;
        let yearid = doc.acadyear.value;
        let periodid = doc.acadperiod.value;
        let courseid = doc.acadcourse.value;
        let data = await fetchData(url, { type: 'DISPLAY', levelid, yearid, periodid, courseid });
        buildTable(data);
    	toggleLoader('enable');  // enables all
    })

}