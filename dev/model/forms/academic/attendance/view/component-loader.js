import { tableComponent } from '../components/record-cards.js';
import { subjectFilterComponent } from '../components/subject-filter.js';
import { dateFilterComponent } from '../components/date-filter.js';
import { buttonComponent } from '../components/button.js';

const session = window.__SESSION__;

document.addEventListener('DOMContentLoaded', async () => {
    const attendance = document.getElementById('attndnc_logs_card');
    const subjectFilter = document.getElementById('subjectFilter');
    
    if (attendance && subjectFilter) {
        if(session.CATEGORY == 'STUDENT'){
            subjectFilter.innerHTML = await subjectFilterComponent();
        }

        subjectFilter.innerHTML += await dateFilterComponent();
        subjectFilter.innerHTML += await buttonComponent("Search");
        attendance.innerHTML = await tableComponent();

        document.getElementById('searchButton').addEventListener('click', async (e)=>{
            let params = [];
            const dateFilterStart = document.getElementById('dateFilterStart').value;
            const dateFilterEnd = document.getElementById('dateFilterEnd').value;

            if(session.CATEGORY == 'STUDENT'){
                const selectedSubject = document.getElementById('subjectSelect').value
                params = [selectedSubject, dateFilterStart, dateFilterEnd]

                if(!dateFilterStart || !dateFilterEnd){
                    params = [selectedSubject, null, null]
                }

                attendance.innerHTML = await tableComponent(...params);
            }else{
                params = [null, dateFilterStart, dateFilterEnd]
                attendance.innerHTML = await tableComponent(...params);
            }
        });
    }
});