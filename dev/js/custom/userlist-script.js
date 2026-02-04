async function fetchData(type, url, data) {
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
    const result = type == 'text' ? response.text() : response.json(); 
    return result;
}

(() => {
    const url = '../../model/forms/site-administration/module/userlist/userlist-controller.php';
    const doc  = {
        btnSearch: document.getElementById('btnSearch'),
        userType: document.getElementById('usertype'),
        inputType: document.getElementById('inputtype'),
        inputText: document.getElementById('inputtext'),
        userTableBody: document.querySelector('#userTable tbody'),

    }

    doc.btnSearch.addEventListener('click', async ()=>{
        let usertype = doc.userType.value.trim();
        let inputtype = doc.inputType.value.trim();
        let inputtext = doc.inputText.value.trim();

        let data = await fetchData('text', url, { type: 'SEARCH', usertype, inputtype, inputtext});
        doc.userTableBody.innerHTML = data;

        const btnAction = document.querySelectorAll('.btnAction')

        btnAction.forEach( button => {
            button.addEventListener('click', () => {
                const id = button.closest('ul').id;
                console.log(id);
            })

        })
    })


})();