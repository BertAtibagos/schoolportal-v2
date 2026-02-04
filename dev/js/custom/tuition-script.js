import {arrayToTable, toTransactionTable, collegeTuition, collegeTransactionHistory, collegePaymentPlan} from '../custom/tuition-functions.js';
import {buildDropdown, fetchData} from '../custom/dropdown.js';
import {toggleLoader} from '../custom/loader.js';

async function Initialize(url) {
    toggleLoader('disable'); // disables all

    let levelid = await buildDropdown(url, 'acadlevel', { type: 'LEVEL'});
    let yearid = await buildDropdown(url, 'acadyear', { type: 'YEAR', levelid});
    let periodid = await buildDropdown(url, 'acadperiod', { type: 'PERIOD', levelid, yearid});
    let courseid = await buildDropdown(url, 'acadcourse', { type: 'COURSE', levelid, yearid, periodid});
    let data = await fetchData(url, { type: 'TUITION FEE', levelid, yearid, periodid, courseid });

	// Tuition computation
	const tuition = collegeTuition(data);

	// Transaction history
	const transactions = collegeTransactionHistory(data)
	
	// Payment plan
	const paymentPlan = collegePaymentPlan(data, transactions);
	
    document.getElementById('tbody_summary').innerHTML = arrayToTable(tuition);
    document.getElementById('tbody_history').innerHTML = toTransactionTable(transactions);
    document.getElementById('tbody_plan').innerHTML = arrayToTable(paymentPlan);
    toggleLoader('enable');  // enables all
}

window.onload = ()=>{
    const url = '../../model/forms/fees/tuition/tuition-controller.php';
    Initialize(url);

    // Event Listeners
    const doc = {
        acadlevel: document.getElementById('acadlevel'),
        acadyear: document.getElementById('acadyear'),
        acadperiod: document.getElementById('acadperiod'),
        acadcourse: document.getElementById('acadcourse'),
        btnSearch: document.getElementById('btnSearch'),
        tblSummary: document.getElementById('tbody_summary'),
        tblPlan: document.getElementById('tbody_plan'),
        tblHistory: document.getElementById('tbody_history')
    }

    doc.acadlevel.addEventListener('change', async () => {
        toggleLoader('disable'); // disables all

        let levelid = doc.acadlevel.value;
        let yearid = await buildDropdown(url, 'acadyear', { type: 'YEAR', levelid});
        let periodid = await buildDropdown(url, 'acadperiod', { type: 'PERIOD', levelid, yearid});
        let courseid = await buildDropdown(url, 'acadcourse', { type: 'COURSE', levelid, yearid, periodid});
        let data = await fetchData(url, { type: 'TUITION FEE', levelid, yearid, periodid, courseid });
        
		// Tuition computation
		const tuition = collegeTuition(data);

		// Transaction history
		const transactions = collegeTransactionHistory(data)
		// Payment plan
		const paymentPlan = collegePaymentPlan(data, transactions);
		
        doc.tblSummary.innerHTML = arrayToTable(tuition);
        doc.tblHistory.innerHTML = toTransactionTable(transactions);
        doc.tblPlan.innerHTML = arrayToTable(paymentPlan);
        toggleLoader('enable');  // enables all
    })

    doc.acadyear.addEventListener('change', async () => {
        toggleLoader('disable'); // disables all
        let levelid = doc.acadlevel.value;
        let yearid = doc.acadyear.value;
        let periodid = await buildDropdown(url, 'acadperiod', { type: 'PERIOD', levelid, yearid});
        let courseid = await buildDropdown(url, 'acadcourse', { type: 'COURSE', levelid, yearid, periodid});
        let data = await fetchData(url, { type: 'TUITION FEE', levelid, yearid, periodid, courseid });
        
		// Tuition computation
		const tuition = collegeTuition(data);

		// Transaction history
		const transactions = collegeTransactionHistory(data)
		// Payment plan
		const paymentPlan = collegePaymentPlan(data, transactions);
		
        doc.tblSummary.innerHTML = arrayToTable(tuition);
        doc.tblHistory.innerHTML = toTransactionTable(transactions);
        doc.tblPlan.innerHTML = arrayToTable(paymentPlan);
        toggleLoader('enable');  // enables all
    })

    doc.acadperiod.addEventListener('change', async () => {
        toggleLoader('disable'); // disables all
        let levelid = doc.acadlevel.value;
        let yearid = doc.acadyear.value;
        let periodid = doc.acadperiod.value;
        let courseid = await buildDropdown(url, 'acadcourse', { type: 'COURSE', levelid, yearid, periodid});
        let data = await fetchData(url, { type: 'TUITION FEE', levelid, yearid, periodid, courseid });
        
		// Tuition computation
		const tuition = collegeTuition(data);

		// Transaction history
		const transactions = collegeTransactionHistory(data)
		// Payment plan
		const paymentPlan = collegePaymentPlan(data, transactions);
		
        doc.tblSummary.innerHTML = arrayToTable(tuition);
        doc.tblHistory.innerHTML = toTransactionTable(transactions);
        doc.tblPlan.innerHTML = arrayToTable(paymentPlan);
        toggleLoader('enable');  // enables all
    })

    doc.acadcourse.addEventListener('change', async () => {
        toggleLoader('disable'); // disables all
        let levelid = doc.acadlevel.value;
        let yearid = doc.acadyear.value;
        let periodid = doc.acadperiod.value;
        let courseid = doc.acadcourse.value;
        let data = await fetchData(url, { type: 'TUITION FEE', levelid, yearid, periodid, courseid });
        
		// Tuition computation
		const tuition = collegeTuition(data);

		// Transaction history
		const transactions = collegeTransactionHistory(data)
		// Payment plan
		const paymentPlan = collegePaymentPlan(data, transactions);
		
        doc.tblSummary.innerHTML = arrayToTable(tuition);
        doc.tblHistory.innerHTML = toTransactionTable(transactions);
        doc.tblPlan.innerHTML = arrayToTable(paymentPlan);
        toggleLoader('enable');  // enables all
    })

    doc.btnSearch.addEventListener('click', async () => {
        toggleLoader('disable'); // disables all
        let levelid = doc.acadlevel.value;
        let yearid = doc.acadyear.value;
        let periodid = doc.acadperiod.value;
        let courseid = doc.acadcourse.value;
        let data = await fetchData(url, { type: 'TUITION FEE', levelid, yearid, periodid, courseid });
        
		// Tuition computation
		const tuition = collegeTuition(data);

		// Transaction history
		const transactions = collegeTransactionHistory(data)
		// Payment plan
		const paymentPlan = collegePaymentPlan(data, transactions);
		
        doc.tblSummary.innerHTML = arrayToTable(tuition);
        doc.tblHistory.innerHTML = toTransactionTable(transactions);
        doc.tblPlan.innerHTML = arrayToTable(paymentPlan);
        toggleLoader('enable');  // enables all
    })


}