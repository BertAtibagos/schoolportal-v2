import {buildDropdown, fetchData} from '../custom/dropdown.js';
import {toggleLoader} from '../custom/loader.js';

async function Initialize(url) {
    toggleLoader('disable'); // disables all

    let typeid = document.getElementById('acadtype').value;
    let levelid = await buildDropdown(url, 'acadlevel', { type: 'LEVEL', typeid});
    let yearid = await buildDropdown(url, 'acadyear', { type: 'YEAR', typeid, levelid});
    let periodid = await buildDropdown(url, 'acadperiod', { type: 'PERIOD', typeid, levelid, yearid});
    let courseid = await buildDropdown(url, 'acadcourse', { type: 'COURSE', typeid, levelid, yearid, periodid});
    let data = await fetchData(url, { type: 'SCHEDULE', typeid, levelid, yearid, periodid, courseid });

    document.getElementById('table-container').innerHTML = data;
    toggleLoader('enable');  // enables all
}

window.onload = ()=>{
    const url = '../../model/forms/academic/schedule/schedule-controller.php';
    Initialize(url);

    // Event Listeners
    const doc = {
        acadtype: document.getElementById('acadtype'),
        acadlevel: document.getElementById('acadlevel'),
        acadyear: document.getElementById('acadyear'),
        acadperiod: document.getElementById('acadperiod'),
        acadcourse: document.getElementById('acadcourse'),
        btnSearch: document.getElementById('btnSearch'),
        tblSchedule: document.getElementById('table-container'),
    };
    
    doc.acadtype.addEventListener('change', async () => {
        toggleLoader('disable'); // disables all

        let typeid = doc.acadtype.value;
        let levelid = await buildDropdown(url, 'acadlevel', { type: 'LEVEL', typeid});
        let yearid = await buildDropdown(url, 'acadyear', { type: 'YEAR', typeid, levelid});
        let periodid = await buildDropdown(url, 'acadperiod', { type: 'PERIOD', typeid, levelid, yearid});
        let courseid = await buildDropdown(url, 'acadcourse', { type: 'COURSE', typeid, levelid, yearid, periodid});
        let data = await fetchData(url, { type: 'SCHEDULE', typeid, levelid, yearid, periodid, courseid });
        
        doc.tblSchedule.innerHTML = data;
        toggleLoader('enable');  // enables all
    })

    doc.acadlevel.addEventListener('change', async () => {
        toggleLoader('disable'); // disables all

        let typeid = doc.acadtype.value;
        let levelid = doc.acadlevel.value;
        let yearid = await buildDropdown(url, 'acadyear', { type: 'YEAR', typeid, levelid});
        let periodid = await buildDropdown(url, 'acadperiod', { type: 'PERIOD', typeid, levelid, yearid});
        let courseid = await buildDropdown(url, 'acadcourse', { type: 'COURSE', typeid, levelid, yearid, periodid});
        let data = await fetchData(url, { type: 'SCHEDULE', typeid, levelid, yearid, periodid, courseid });
        
        doc.tblSchedule.innerHTML = data;
        toggleLoader('enable');  // enables all
    })

    doc.acadyear.addEventListener('change', async () => {
        toggleLoader('disable'); // disables all
        let typeid = doc.acadtype.value;
        let levelid = doc.acadlevel.value;
        let yearid = doc.acadyear.value;
        let periodid = await buildDropdown(url, 'acadperiod', { type: 'PERIOD', typeid, levelid, yearid});
        let courseid = await buildDropdown(url, 'acadcourse', { type: 'COURSE', typeid, levelid, yearid, periodid});
        let data = await fetchData(url, { type: 'SCHEDULE', typeid, levelid, yearid, periodid, courseid });
        
        doc.tblSchedule.innerHTML = data;
        toggleLoader('enable');  // enables all
    })

    doc.acadperiod.addEventListener('change', async () => {
        toggleLoader('disable'); // disables all
        let typeid = doc.acadtype.value;
        let levelid = doc.acadlevel.value;
        let yearid = doc.acadyear.value;
        let periodid = doc.acadperiod.value;
        let courseid = await buildDropdown(url, 'acadcourse', { type: 'COURSE', typeid, levelid, yearid, periodid});
        let data = await fetchData(url, { type: 'SCHEDULE', typeid, levelid, yearid, periodid, courseid });
        
        doc.tblSchedule.innerHTML = data;
        toggleLoader('enable');  // enables all
    })

    doc.acadcourse.addEventListener('change', async () => {
        toggleLoader('disable'); // disables all
        let typeid = doc.acadtype.value;
        let levelid = doc.acadlevel.value;
        let yearid = doc.acadyear.value;
        let periodid = doc.acadperiod.value;
        let courseid = doc.acadcourse.value;
        let data = await fetchData(url, { type: 'SCHEDULE', typeid, levelid, yearid, periodid, courseid });
        
        doc.tblSchedule.innerHTML = data;
        toggleLoader('enable');  // enables all
    })

    doc.btnSearch.addEventListener('click', async () => {
        toggleLoader('disable'); // disables all
        let typeid = doc.acadtype.value;
        let levelid = doc.acadlevel.value;
        let yearid = doc.acadyear.value;
        let periodid = doc.acadperiod.value;
        let courseid = doc.acadcourse.value;
        let data = await fetchData(url, { type: 'SCHEDULE', typeid, levelid, yearid, periodid, courseid });
        
        doc.tblSchedule.innerHTML = data;
        toggleLoader('enable');  // enables all
    })


}