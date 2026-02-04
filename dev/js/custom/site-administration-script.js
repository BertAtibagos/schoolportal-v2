const siteadministration = {
    btnBack: document.getElementById('btnBack'),
    adminModule: document.querySelectorAll('.admin-module'),
    divMain: document.getElementById('divMain'),
    divPages: document.getElementById('divPages'),
    divLoad: document.getElementById('divLoad'),
}

const script_id = 'moduleScript';

siteadministration.btnBack.addEventListener('click', () => {
    siteadministration.divMain.style.display = 'block';
    siteadministration.divPages.style.display = 'none';
    siteadministration.divLoad.innerHTML = '';
    
    const oldScript = document.getElementById(script_id);
    if (oldScript) oldScript.remove();
})

siteadministration.adminModule.forEach((button) => {
    button.addEventListener('click', () => {
        let text = button.name;

        siteadministration.divMain.style.display = 'none';
        siteadministration.divPages.style.display = 'block';
        
        const api_url = `site-administration/module/${text}-model.php`;
        const script_url = `../../js/custom/${text}-script.js?t=${Date.now()}`;

        fetch(api_url).then(response => {
            if (!response.ok) throw new Error("Network error");
            return response.text();
        })
        .then(data => {
            siteadministration.divLoad.innerHTML = data;

            // Now load the JavaScript AFTER the HTML is inserted
            const existingScript = document.getElementById(script_id);

            const newScript = document.createElement('script');
            newScript.type = 'module';
            newScript.id = script_id;
            newScript.src = script_url;

            if (existingScript && existingScript.parentNode) {
                existingScript.parentNode.replaceChild(newScript, existingScript);
            } else {
                document.body.appendChild(newScript);
            }
        })
        .catch(error => {
            siteadministration.divLoad.innerHTML = 'Error in Loading File.';
            console.error(error)
        });
    });
});