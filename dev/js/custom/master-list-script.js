import {toggleLoader} from '../custom/loader.js';

function toggleInteractiveElements(action) {
    const elements = document.querySelectorAll('input, select, button, textarea, a');

    elements.forEach(el => {
        if (el.id !== 'disableBtn') {
            const isDisable = action === 'disable';

            if ('disabled' in el) {
                el.disabled = isDisable;
            }

            // For elements like <a> and non-form elements
            el.style.pointerEvents = isDisable ? 'none' : 'auto';
            el.style.opacity = isDisable ? '0.6' : '1';
        }
    });
}

async function buildDropdown(url, id, data) {
    const formData = new FormData();
    for (const key in data) {
        formData.append(key, data[key]);
    }

    const response = await fetch(url, {
        method: 'POST',
        headers: {
            'Accept': 'text/html' // ✅ Expect HTML response
        },
        body: formData // ✅ FormData sets content type automatically
    });

    if (!response.ok) {
        throw new Error('Network Error!');
    }

    const html = await response.text();

    // // ✅ Basic safety check: ensure only <option> tags are inserted
    // if (!html.trim().startsWith('<option')) {
    //     throw new Error(`Unexpected HTML for dropdown: ${id}`);
    // }

    const selectElem = document.getElementById(id);
    selectElem.innerHTML = html;

    return selectElem.value;
}

async function fetchData(type, url, data) {
    const formData = new FormData();
    for (const key in data) {
        formData.append(key, data[key]);
    }

    const response = await fetch(url, {
        method: 'POST',
        headers: {
            'Accept': 'application/json' // ✅ Expect JSON response
        },
        body: formData // ✅ FormData sets content type automatically
    });

    if (!response.ok) {
        throw new Error('Network Error!');
    }

    let result = type === 'text' ? response.text() : response.json();
    return result;
}


async function Initialize(url) {
    toggleLoader('disable'); // disables all
    toggleInteractiveElements('disable'); // disables all

    let levelid = await buildDropdown(url, 'acadlevel', { type: 'LEVEL' });
    let yearid = await buildDropdown(url, 'acadyear', { type: 'YEAR', levelid });
    let periodid = await buildDropdown(url, 'acadperiod', { type: 'PERIOD', levelid, yearid });
    let courseid = await buildDropdown(url, 'acadcourse', { type: 'COURSE', levelid, yearid, periodid });
    let yearlevelid = await buildDropdown(url, 'acadyearlevel', { type: 'YEARLEVEL', levelid, yearid, periodid, courseid });
    let sectionid = await buildDropdown(url, 'acadsection', { type: 'SECTION', levelid, yearid, periodid, yearlevelid, courseid });
    // let data = await fetchData('json', url, { type: 'DISPLAY', levelid, yearid, periodid, yearlevelid, courseid, sectionid });

    // document.querySelector('#userTable tbody').innerHTML = data.TABLE_CONTENT;
    // document.querySelector('#userTable #tableRowCount').innerHTML = data.TABLE_COUNT === 1 ? data.TABLE_COUNT + " enrolled student" : data.TABLE_COUNT + " enrolled students";

    toggleLoader('enable'); // disables all
    toggleInteractiveElements('enable');  // enables all
}


window.onload = () => {
    const url = '../../model/forms/enrollment/master-list/master-list-controller.php';
    Initialize(url);

    // Event Listeners
    const masterlist = {
        acadlevel: document.getElementById('acadlevel'),
        acadyear: document.getElementById('acadyear'),
        acadperiod: document.getElementById('acadperiod'),
        acadyearlevel: document.getElementById('acadyearlevel'),
        acadcourse: document.getElementById('acadcourse'),
        acadsection: document.getElementById('acadsection'),
        btnSearch: document.getElementById('btnSearch'),
        tableContainer: document.getElementById('divTableContainer'),
        userTable: document.querySelector('#userTable tbody'),
        rowCount: document.querySelector('#tableRowCount'),
        message: document.getElementById('errormessage'),

        inputtext: document.getElementById('acadinfotext'),
        inputtype: document.getElementById('acadinfotype')

    }

    masterlist.acadlevel.addEventListener('change', async () => {
        toggleLoader('disable'); // disables all
        toggleInteractiveElements('disable'); // disables all
        masterlist.userTable.innerHTML = '';
        masterlist.rowCount.innerHTML = 0;
        masterlist.message.innerHTML = '';
        masterlist.message.style.display = 'none';

        let levelid = masterlist.acadlevel.value;
        let yearid = await buildDropdown(url, 'acadyear', { type: 'YEAR', levelid });
        let periodid = await buildDropdown(url, 'acadperiod', { type: 'PERIOD', levelid, yearid });
        let courseid = await buildDropdown(url, 'acadcourse', { type: 'COURSE', levelid, yearid, periodid });
        let yearlevelid = await buildDropdown(url, 'acadyearlevel', { type: 'YEARLEVEL', levelid, yearid, periodid, courseid });
        let sectionid = await buildDropdown(url, 'acadsection', { type: 'SECTION', levelid, yearid, periodid, yearlevelid, courseid });
        // let data = await fetchData('json', url, { type: 'DISPLAY', levelid, yearid, periodid, yearlevelid, courseid, sectionid});

        // masterlist.userTable.innerHTML = data.TABLE_CONTENT;
        // masterlist.rowCount.innerHTML = data.TABLE_COUNT;
        toggleLoader('enable'); // disables all
        toggleInteractiveElements('enable');  // enables all
    })

    masterlist.acadyear.addEventListener('change', async () => {
        toggleLoader('disable'); // disables all
        toggleInteractiveElements('disable'); // disables all
        masterlist.userTable.innerHTML = '';
        masterlist.rowCount.innerHTML = 0;
        masterlist.message.innerHTML = '';
        masterlist.message.style.display = 'none';

        let levelid = masterlist.acadlevel.value;
        let yearid = masterlist.acadyear.value;
        let periodid = await buildDropdown(url, 'acadperiod', { type: 'PERIOD', levelid, yearid });
        let courseid = await buildDropdown(url, 'acadcourse', { type: 'COURSE', levelid, yearid, periodid });
        let yearlevelid = await buildDropdown(url, 'acadyearlevel', { type: 'YEARLEVEL', levelid, yearid, periodid, courseid });
        let sectionid = await buildDropdown(url, 'acadsection', { type: 'SECTION', levelid, yearid, periodid, yearlevelid, courseid });
        // let data = await fetchData('json', url, { type: 'DISPLAY', levelid, yearid, periodid, yearlevelid, courseid, sectionid});

        // masterlist.userTable.innerHTML = data.TABLE_CONTENT;
        // masterlist.rowCount.innerHTML = data.TABLE_COUNT;
        toggleLoader('enable'); // disables all
        toggleInteractiveElements('enable');  // enables all
    })

    masterlist.acadperiod.addEventListener('change', async () => {
        toggleLoader('disable'); // disables all
        toggleInteractiveElements('disable'); // disables all
        masterlist.userTable.innerHTML = '';
        masterlist.rowCount.innerHTML = 0;
        masterlist.message.innerHTML = '';
        masterlist.message.style.display = 'none';

        let levelid = masterlist.acadlevel.value;
        let yearid = masterlist.acadyear.value;
        let periodid = masterlist.acadperiod.value;
        let courseid = await buildDropdown(url, 'acadcourse', { type: 'COURSE', levelid, yearid, periodid });
        let yearlevelid = await buildDropdown(url, 'acadyearlevel', { type: 'YEARLEVEL', levelid, yearid, periodid, courseid });
        let sectionid = await buildDropdown(url, 'acadsection', { type: 'SECTION', levelid, yearid, periodid, yearlevelid, courseid });
        // let data = await fetchData('json', url, { type: 'DISPLAY', levelid, yearid, periodid, yearlevelid, courseid, sectionid});

        // masterlist.userTable.innerHTML = data.TABLE_CONTENT;
        // masterlist.rowCount.innerHTML = data.TABLE_COUNT;
        toggleLoader('enable'); // disables all
        toggleInteractiveElements('enable');  // enables all
    })

    masterlist.acadcourse.addEventListener('change', async () => {
        toggleLoader('disable'); // disables all
        toggleInteractiveElements('disable'); // disables all
        masterlist.userTable.innerHTML = '';
        masterlist.rowCount.innerHTML = 0;
        masterlist.message.innerHTML = '';
        masterlist.message.style.display = 'none';

        let levelid = masterlist.acadlevel.value;
        let yearid = masterlist.acadyear.value;
        let periodid = masterlist.acadperiod.value;
        let courseid = masterlist.acadcourse.value;
        let yearlevelid = await buildDropdown(url, 'acadyearlevel', { type: 'YEARLEVEL', levelid, yearid, periodid, courseid });
        let sectionid = await buildDropdown(url, 'acadsection', { type: 'SECTION', levelid, yearid, periodid, yearlevelid, courseid });
        // let data = await fetchData('json', url, { type: 'DISPLAY', levelid, yearid, periodid, yearlevelid, courseid, sectionid});

        // masterlist.userTable.innerHTML = data.TABLE_CONTENT;
        // masterlist.rowCount.innerHTML = data.TABLE_COUNT;
        toggleLoader('enable'); // disables all
        toggleInteractiveElements('enable');  // enables all
    })

    masterlist.acadyearlevel.addEventListener('change', async () => {
        toggleLoader('disable'); // disables all
        toggleInteractiveElements('disable'); // disables all
        masterlist.userTable.innerHTML = '';
        masterlist.rowCount.innerHTML = 0;
        masterlist.message.innerHTML = '';
        masterlist.message.style.display = 'none';

        let levelid = masterlist.acadlevel.value;
        let yearid = masterlist.acadyear.value;
        let periodid = masterlist.acadperiod.value;
        let courseid = masterlist.acadcourse.value;
        let yearlevelid = masterlist.acadyearlevel.value;
        let sectionid = await buildDropdown(url, 'acadsection', { type: 'SECTION', levelid, yearid, periodid, yearlevelid, courseid });
        // let data = await fetchData('json', url, { type: 'DISPLAY', levelid, yearid, periodid, yearlevelid, courseid, sectionid});

        // masterlist.userTable.innerHTML = data.TABLE_CONTENT;
        // masterlist.rowCount.innerHTML = data.TABLE_COUNT;
        toggleLoader('enable'); // disables all
        toggleInteractiveElements('enable');  // enables all
    })

    masterlist.acadsection.addEventListener('change', async () => {
        toggleLoader('disable'); // disables all
        toggleInteractiveElements('disable'); // disables all
        masterlist.userTable.innerHTML = '';
        masterlist.rowCount.innerHTML = 0;
        masterlist.message.innerHTML = '';
        masterlist.message.style.display = 'none';

        let levelid = masterlist.acadlevel.value;
        let yearid = masterlist.acadyear.value;
        let periodid = masterlist.acadperiod.value;
        let courseid = masterlist.acadcourse.value;
        let yearlevelid = masterlist.acadyearlevel.value;
        let sectionid = masterlist.acadsection.value;
        // let data = await fetchData('json', url, { type: 'DISPLAY', levelid, yearid, periodid, yearlevelid, courseid, sectionid});

        // masterlist.userTable.innerHTML = data.TABLE_CONTENT;
        // masterlist.rowCount.innerHTML = data.TABLE_COUNT;
        toggleLoader('enable'); // disables all
        toggleInteractiveElements('enable');  // enables all
    })

    masterlist.btnSearch.addEventListener('click', async () => {
        toggleLoader('disable'); // disables all
        toggleInteractiveElements('disable'); // disables all
        masterlist.userTable.innerHTML = '';
        masterlist.rowCount.innerHTML = 0;
        masterlist.message.innerHTML = '';
        masterlist.message.style.display = 'none';

        let inputtext = masterlist.inputtext.value.trim();
        let inputtype = masterlist.inputtype.value.trim();

        let levelid = masterlist.acadlevel.value;
        let yearid = masterlist.acadyear.value;
        let periodid = masterlist.acadperiod.value;
        let yearlevelid = masterlist.acadyearlevel.value;
        let courseid = masterlist.acadcourse.value;
        let sectionid = masterlist.acadsection.value;
        let data = await fetchData('json', url, { type: 'DISPLAY', levelid, yearid, periodid, yearlevelid, courseid, sectionid, inputtext, inputtype });

        masterlist.userTable.innerHTML = data.TABLE_CONTENT;
        masterlist.rowCount.innerHTML = data.TABLE_COUNT === 1 ? data.TABLE_COUNT + " enrolled student" : data.TABLE_COUNT + " enrolled students";
        toggleLoader('enable'); // disables all
        toggleInteractiveElements('enable');  // enables all
    })

}
