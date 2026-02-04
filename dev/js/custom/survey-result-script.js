import {buildDropdown, fetchData} from '../custom/dropdown.js';
import {toggleLoader} from '../custom/loader.js';

async function Initialize(url) {
    toggleLoader('disable'); // disables all

    let levelid = await buildDropdown(url, 'acadlevel', { type: 'LEVEL'});
    let yearid = await buildDropdown(url, 'acadyear', { type: 'YEAR', levelid});
    let periodid = await buildDropdown(url, 'acadperiod', { type: 'PERIOD', levelid, yearid});
    let data = await fetchData(url, { type: 'RESULTS', levelid, yearid, periodid });

    // document.getElementById('table-container').innerHTML = data;
    toggleLoader('enable');  // enables all
}

window.onload = ()=>{
    const url = '../../model/forms/forms/surveyresults/survey-result-controller.php';
    Initialize(url);

    // Event Listeners
    const doc = {
        acadlevel: document.getElementById('acadlevel'),
        acadyear: document.getElementById('acadyear'),
        acadperiod: document.getElementById('acadperiod'),
        btnSearch: document.getElementById('btnSearch'),
        tblSchedule: document.getElementById('table-container'),
    }

    doc.acadlevel.addEventListener('change', async () => {
        toggleLoader('disable'); // disables all

        let levelid = doc.acadlevel.value;
        let yearid = await buildDropdown(url, 'acadyear', { type: 'YEAR', levelid});
        let periodid = await buildDropdown(url, 'acadperiod', { type: 'PERIOD', levelid, yearid});
        let data = await fetchData(url, { type: 'RESULTS', levelid, yearid, periodid });
        
        // doc.tblSchedule.innerHTML = data;
        toggleLoader('enable');  // enables all
    })

    doc.acadyear.addEventListener('change', async () => {
        toggleLoader('disable'); // disables all
        let levelid = doc.acadlevel.value;
        let yearid = doc.acadyear.value;
        let periodid = await buildDropdown(url, 'acadperiod', { type: 'PERIOD', levelid, yearid});
        let data = await fetchData(url, { type: 'RESULTS', levelid, yearid, periodid });
        
        // doc.tblSchedule.innerHTML = data;
        toggleLoader('enable');  // enables all
    })

    doc.acadperiod.addEventListener('change', async () => {
        toggleLoader('disable'); // disables all
        let levelid = doc.acadlevel.value;
        let yearid = doc.acadyear.value;
        let periodid = doc.acadperiod.value;
        let data = await fetchData(url, { type: 'RESULTS', levelid, yearid, periodid });
        
        // doc.tblSchedule.innerHTML = data;
        toggleLoader('enable');  // enables all
    })

    doc.btnSearch.addEventListener('click', async () => {
        toggleLoader('disable'); // disables all
        let levelid = doc.acadlevel.value;
        let yearid = doc.acadyear.value;
        let periodid = doc.acadperiod.value;
        let data = await fetchData(url, { type: 'RESULTS', levelid, yearid, periodid });
        
        // doc.tblSchedule.innerHTML = data;
        toggleLoader('enable');  // enables all
    })


}