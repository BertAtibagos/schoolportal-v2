function toggleInteraction(state) {
    const elements = document.querySelectorAll('button, a, input, select, textarea');

    elements.forEach(el => {
        el.disabled = (state === 'disable');
    });
}

function togglePasswordEvent(button, input) {
    const icon = button.querySelector('i');

    button.addEventListener('click', () => {
        const isText = input.type === 'text';
        input.type = isText ? 'password' : 'text';
        icon.className = `fa-solid ${isText ? 'fa-eye' : 'fa-eye-slash'}`;
    });
}

function updateHint(id, bool) {
    const passLengthElem = document.getElementById(id);
    const iconElem = passLengthElem.querySelector('i');

    passLengthElem.classList.toggle('text-success', bool);
    passLengthElem.classList.toggle('text-danger', !bool);
    iconElem.className = `fa-solid ${bool ? 'fa-check' : 'fa-xmark'}`;
}

function containsEightCharacter(str){
    return str.length >= 8 ? true : false;
}

function containsNumber(str) {
    const regex = /\d/; // Matches any digit (0-9)
    return regex.test(str);
}

function containsSpecialCharacter(str){
    const specialChars = /[`!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~]/;
    return specialChars.test(str);
}

function validatePasswords() {
    const pass = doc.password.value;
    const confirm = doc.confirm_password.value;

    const passlength = containsEightCharacter(pass);
    const hasnumber = containsNumber(pass);
    const haschar   = containsSpecialCharacter(pass);
    const match     = pass === confirm;

    updateHint('passlength', passlength);
    updateHint('passnum', hasnumber);
    updateHint('passchar', haschar);
    updateHint('passmatch', match);

    doc.btnsubmit.disabled = !(passlength && hasnumber && haschar && match);
}

const doc = {
    btnpass: document.getElementById('view-pass'),
    btnconfpass: document.getElementById('view-confpass'),
    btnnewpass: document.getElementById('view-newpass'),
    btncopynewpass: document.getElementById('copy-newpass'),

    password: document.getElementById('password'),
    confirm_password: document.getElementById('confirm_password'),
    new_password: document.getElementById('new_password'),

    copytext: document.querySelector('.copy-text'),
    btnsubmit: document.getElementById('btnSubmit'),
    btncancel: document.getElementById('btnCancel'),

    username: document.getElementById('username'),
    divchange: document.getElementById('divChange'),
    divview: document.getElementById('divView'),
}


togglePasswordEvent(doc.btnpass, doc.password);
togglePasswordEvent(doc.btnconfpass, doc.confirm_password);
togglePasswordEvent(doc.btnnewpass, doc.new_password);

doc.btncopynewpass.addEventListener('click', ()=>{
    let text = doc.new_password.value;
    text.length ? navigator.clipboard.writeText(text) : doc.copytext.innerHTML = '<small class="text-danger">Password field empty</small>';

    doc.copytext.classList.remove('d-none');
})

doc.btncancel.addEventListener('click', ()=>{
    window.close();
})

doc.btnsubmit.disabled = true;
let passlength = false;
let hasnumber = false;
let haschar = false;

// Attach to both fields
doc.password.addEventListener('keyup', validatePasswords);
doc.confirm_password.addEventListener('keyup', validatePasswords);

doc.btnsubmit.addEventListener('click', async () => {
    toggleInteraction("disable");

    if (!window.confirm("Are you sure you want to submit?")) {
        toggleInteraction("enable");
        return;
    }

    let password = doc.password.value;              // ❌ NO TRIM
    let confirm_password = doc.confirm_password.value;

    if (!password || !confirm_password) {
        toggleInteraction("enable");
        alert("Password fields cannot be empty.");
        return;
    }

    // Extract token from URL
    const urlParams = new URLSearchParams(window.location.search);
    let token = urlParams.get('token');

    // ✅ RECOMPUTE VALIDATION (FIX)
    let passlength = containsEightCharacter(password);
    let hasnumber  = containsNumber(password);
    let haschar    = containsSpecialCharacter(password);

    // Validate all conditions again (safety check)
    let isOkay = passlength && haschar && hasnumber && (password === confirm_password) && !!token;

    if (isOkay) {
        try {
            const data = new FormData();
            data.append('type', "PASSWORD_RESET");
            data.append('password', password);
            data.append('token', token);

            const response = await fetch('change-password-controller.php', {
                method: 'POST',
                headers: { 'Accept': 'application/json' }, 
                body: data });

            if (!response.ok) throw new Error(`HTTP error: ${response.status}`);

            const result = await response.json();

            // Check PHP's response structure
            if (result.success === true) {
                const { email, password } = result;
                doc.username.value = email;
                doc.new_password.value = password;

                doc.divchange.style.display = 'none';
                doc.divview.style.display = 'block';
            } else {
                alert("ERROR: Promptly contact the FCPC ICT Department for assistance.");
            }
        } catch (err) {
            console.error("Fetch error:", err);
        }
    } else {
        alert("Invalid Password: Please follow our password requirements.");
    }

    toggleInteraction("enable");
});
