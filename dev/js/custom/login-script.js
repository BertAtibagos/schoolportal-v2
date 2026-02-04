function toggleInteraction(state) {
    const elements = document.querySelectorAll('button, a, input, select, textarea');

    elements.forEach(el => {
        el.disabled = (state === 'disable');

        // if (state === 'disable') {
        //     el.disabled = true;       // disable buttons and inputs
        //     el.style.pointerEvents = 'none'; // disable clicks on links
        // } else if (state === 'enable') {
        //     el.disabled = false;
        //     el.style.pointerEvents = '';     // restore clicks
        // }
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

function switchContainer(doc, type) {
    // Hide all containers first
    doc.containerManual.style.display = 'none';
    doc.containerGoogle.style.display = 'none';
    doc.containerReset.style.display = 'none';

    // Show the selected container
    if (type === 'manual') {
        doc.containerManual.style.display = 'block';
    } else if (type === 'google') {
        doc.containerGoogle.style.display = 'block';
    } else if (type === 'reset') {
        doc.containerReset.style.display = 'block';
    }

    // Button visibility control (ignore reset button)
    if (type === 'manual') {
        doc.btnSwitchManual.style.display = 'none';
        doc.btnSwitchGoogle.style.display = 'inline-block';
    } else if (type === 'google') {
        doc.btnSwitchGoogle.style.display = 'none';
        doc.btnSwitchManual.style.display = 'inline-block';
    } else if (type === 'reset') {
        doc.btnSwitchManual.style.display = 'inline-block';
        doc.btnSwitchGoogle.style.display = 'inline-block';
    }
}

window.onload = () => {
    const doc = {
        btnSwitchManual: document.getElementById('btn-manual'),
        btnSwitchGoogle: document.getElementById('btn-google'),
        btnSwitchReset: document.getElementById('btn-reset'),

        containerManual: document.getElementById('manual-container'),
        containerGoogle: document.getElementById('google-container'),
        containerReset: document.getElementById('reset-container'),

        viewPass: document.getElementById('view-password'),
        userPassword: document.getElementById('userpassword'),
        userName: document.getElementById('username'),
        email: document.getElementById('email'),

        btnLogin: document.getElementById("btnLogin"),
        btnReset: document.getElementById("btnReset"),
        
        oldEnrollment: document.getElementById("old-enrollment"),
        btnGoogleLogin: document.getElementById("btnGoogleLogin")
    }

    const message = document.getElementById("message");

    doc.oldEnrollment.addEventListener('click', () => {
        doc.btnGoogleLogin.classList.toggle("shadow-lg");
        doc.btnGoogleLogin.classList.toggle("bg-primary-subtle");
        doc.userName.focus();

        message.classList.add("alert-danger")
        message.innerHTML = "For Old Students,<span class='fw-medium'> Sign In</span> to the School Portal first.";
        message.style.display = "block";
    })

    togglePasswordEvent(doc.viewPass, doc.userPassword);

    doc.btnSwitchManual.addEventListener('click', () => {
        switchContainer(doc, 'manual');
    })

    doc.btnSwitchGoogle.addEventListener('click', () => {
        switchContainer(doc, 'google');
    })

    doc.btnSwitchReset.addEventListener('click', () => {
        switchContainer(doc, 'reset');
    })

    doc.btnLogin.addEventListener("click", async () => {
        message.classList.toggle("alert-danger")
        message.innerHTML = "";
        message.style.display = "none";
		
        toggleInteraction("disable");

        const username = doc.userName.value.trim();
        const password = doc.userPassword.value.trim();

        if(!username){
            message.innerHTML = "Please enter your <strong>username</strong>.";
            message.classList.add('alert-danger');
            message.style.display = "block";
            doc.userName.focus();
            toggleInteraction("enable");
            return;
        }
        
        if(!password){
            message.innerHTML = "Please enter your <strong>password</strong>.";
            message.classList.add('alert-danger');
            message.style.display = "block";
            doc.userPassword.focus();
            toggleInteraction("enable");
            return;
        }

        try {
            const data = new FormData();
            data.append('type', "LOGIN");
            data.append('uemail', username);
            data.append('upass', password)

            const response = await fetch("login-controller.php", {
                method: "POST",
                headers: {
                    "Accept": "application/json"
                },
                body: data,
                credentials: "same-origin" // keeps cookies/session secure
            });

            if (!response.ok) {
                throw new Error("Network response was not ok");
            }

            const result = await response.json();

            const key = result.success ? 'success' : 'error';
            const alert_class = key === 'success' ? 'alert-success' : 'alert-danger';

            message.classList.add(alert_class);
            message.innerHTML = result.message || 'Invalid username or password.';
            message.style.display = "block";

            toggleInteraction("enable");

            if (result.success) {
                window.location.replace("forms/masterpage-model.php");
            }

        } catch (error) {  
            toggleInteraction("enable");

            console.error("Response parsing error:", error);

            message.classList.add('alert-danger');
            message.style.display = "block";
            message.innerHTML = "An unexpected error occurred. <br>Please try again.";
        }
    })

    doc.btnReset.addEventListener("click", async () => {
        message.classList.toggle("alert-danger")
        message.innerHTML = "";
        message.style.display = "none";
		
        toggleInteraction("disable");
        const email = doc.email.value.trim();

        if(!email){
            message.innerHTML = "Please enter your <strong>FCPC email</strong>.";
            message.classList.add('alert-danger');
            message.style.display = "block";
            doc.email.focus();
            toggleInteraction("enable");
            return;
        }

        // Get the reCAPTCHA response value
        const recaptchaResponse = document.querySelector('[name="g-recaptcha-response"]').value;
        if (!recaptchaResponse) {
            message.textContent = 'Please complete the reCAPTCHA.';
            message.classList.add('error');
            return;
        }

        try {
            const data = new FormData();
            data.append('type', "PASSWORD_RESET");
            data.append('email', email);
            data.append('g-recaptcha-response', recaptchaResponse);

            const response = await fetch("login-controller.php", {
                method: "POST",
                headers: {
                    "Accept": "application/json"
                },
                body: data,
                credentials: "same-origin" // keeps cookies/session secure
            });

            if (!response.ok) {
                throw new Error("Network response was not ok");
            }

            const result = await response.json();

            const key = result.success ? 'success' : 'error';
            const alert_class = key === 'success' ? 'alert-success' : 'alert-danger';

            message.classList.add(alert_class);
            message.innerHTML = result.message || 'Invalid username or password.';
            message.style.display = "block";

            toggleInteraction("enable");

        } catch (error) {
            toggleInteraction("enable");

            console.error("Response parsing error:", error);
            message.classList.add('alert-danger');
            message.style.display = "block";
            message.innerHTML = "An unexpected error occurred. Please try again.";
        }
    })
}