import { fetchData } from '../custom/dropdown.js';
import { toggleLoader } from '../custom/loader.js';

async function Initialize(url) {
    toggleLoader('disable'); // disables all

    let data = await fetchData(url);
    const els = ["IDNO", "INFO", "SECTION", "MOBILE", "TELEPHONE", "BIRTHDATE", "NATIONALITY", "RELIGION", "PRESENT_ADD", "PERMANENT_ADD"];

    if (data && data.length > 0) {
        const record = data[0];
        els.map(key => {
            const el = document.getElementById(`user_${key.toLowerCase()}`);
            if (el) {
                let value = record[key] ?? '--';
                value = value.toString().trim();

                if (value === '') value = '--';

                // Skip title-casing for SECTION
                if (key !== 'SECTION' && value !== '--' && !value.includes("ICT")) {
                    value = value.toLowerCase().replace(/\b\w/g, c => c.toUpperCase());
                }

                el.innerHTML = value;
            }
        });
    }
    toggleLoader('enable');  // enables all
}

window.onload = () => {
    const url = '../../model/forms/myaccount/profile/profile-controller.php';
    Initialize(url);
}