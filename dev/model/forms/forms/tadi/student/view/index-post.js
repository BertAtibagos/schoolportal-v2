GET_SUBJECTLIST();

document.getElementById("close_modal").addEventListener("click", function () {
    document.getElementById("tadiForm").reset();
    document.getElementById("error_alert").classList.add("d-none");
});

function parseDateOnly(s) {
    if (!s) return null;
    const m = String(s).match(/^(\d{4})-(\d{2})-(\d{2})/);
    if (m) return new Date(Number(m[1]), Number(m[2]) - 1, Number(m[3]));
    if (/^\d+$/.test(String(s))) return new Date(Number(s));
    return new Date(s);
}

const button = document.getElementById("confirmBtn");
button.addEventListener("click", function (e) {
    const form = document.getElementById("tadiForm");

    form.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));

    let isValid = true;
    const makeupDate = document.getElementById("session_type").value;
    let requiredFields =[];

    if(makeupDate === "makeup"){
        requiredFields = ["instructor", "classDate",  "learning_delivery_modalities", "session_type", "classStartDateTime", "classEndDateTime", "makeup_class_date"];
    }else{
        requiredFields = ["instructor", "classDate", "learning_delivery_modalities", "session_type", "classStartDateTime", "classEndDateTime"];
    }
    
    requiredFields.forEach(field => {
        const input = document.getElementById(field);
        if (!input.value) {
            input.classList.add("is-invalid");
            isValid = false;
        }
    });

    const startTime = document.getElementById("classStartDateTime").value;
    const endTime = document.getElementById("classEndDateTime").value;
    if (startTime && endTime && endTime <= startTime) {
        const endInput = document.getElementById("classEndDateTime");
        endInput.classList.add("is-invalid");
        endInput.nextElementSibling.textContent = "Class end time must be later than start time";
        isValid = false;
    }

    const comments = document.getElementById("comments");
    comments.value = comments.value.replace(/[\r\n]+/g, " ");
    const specialCharsRegex = /[<>{}[\]\/;()&$#@!%^*+=|`]/;

    if (specialCharsRegex.test(comments.value)) {
        comments.classList.add("is-invalid");
        comments.nextElementSibling.textContent = "Comments cannot contain special characters.";
        isValid = false;
    }

    const dateInput = document.getElementById("classDate");
    const inputDate = parseDateOnly(dateInput.value);
    const today = new Date();

    today.setHours(0, 0, 0, 0);

    const maxPastDays = 3;

    const pastLimit = new Date(today);
    pastLimit.setDate(today.getDate() - maxPastDays);

    if (!inputDate || isNaN(inputDate.getTime())) {
        dateInput.classList.add("is-invalid");
        dateInput.nextElementSibling.textContent = `Invalid date`;
        isValid = false;
    } else {
        inputDate.setHours(0, 0, 0, 0);
        if (inputDate < pastLimit) {
            dateInput.classList.add("is-invalid");
            dateInput.nextElementSibling.textContent = `Date cannot be more than ${maxPastDays} days in the past`;
            isValid = false;
        } else if (inputDate > today) {
            dateInput.classList.add("is-invalid");
            dateInput.nextElementSibling.textContent = `Date cannot be in the future`;
            isValid = false;
        }
    }

    const classInstChecked = document.querySelectorAll('input[name="classInst[]"]:checked');
    const classInstSection = document.getElementById("classInstSection");
    if (classInstChecked.length === 0) {
        classInstSection.classList.add("is-invalid");
        isValid = false;
    }

    if (isValid) {
        const confirmed = confirm("Are you sure you want to submit this TADI?");
        const submitBtn = document.querySelector(".submitTadi");
        if (confirmed) {
            const formData = new FormData(form);
            formData.append("type", "SUBMIT_TADI");

            submitBtn.disabled = true;
            submitBtn.innerHTML = ``;
            submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...`;

            fetch("forms/tadi/student/controller/index-post.php", {
                method: "POST",
                body: formData
            })
                .then(res => res.json())
                .then(result => {
                    if (result.success) {
                        const modal = bootstrap.Modal.getInstance(document.getElementById("modal"));
                        modal.hide();
                        form.reset();
                        document.getElementById("error_alert").classList.add("d-none");

                        showToastMessage(result.message, "success", "Success");

                        GET_SUBJECTLIST();

                        submitBtn.disabled = false;
                        submitBtn.innerHTML = ``;
                        submitBtn.innerHTML = `Submit`;
                    } else {
                        if(result.isoverlap){
                            const errMessage = document.getElementById("error_alert");

                            const ovelapStud = document.createElement("p");
                            ovelapStud.textContent = `Student Name: ${result.overlap_details.student_name}`;
                            const overlapSection = document.createElement("p");
                            overlapSection.textContent = `Section: ${result.overlap_details.section}`;
                            const overlapSubject = document.createElement("p");
                            overlapSubject.textContent = `Subject: ${result.overlap_details.subject_name}`;
                            const overlapdate = document.createElement("p");
                            overlapdate.textContent = `Date: ${result.overlap_details.date}`;
                            const overlapTime = document.createElement("p");
                            overlapTime.textContent = `Time: ${result.overlap_details.time_in} - ${result.overlap_details.time_out}`;
                            

                            
                            errMessage.appendChild(ovelapStud);
                            errMessage.appendChild(overlapSection);
                            errMessage.appendChild(overlapSubject);
                            errMessage.appendChild(overlapdate);
                            errMessage.appendChild(overlapTime);
                        }

                        document.getElementById("errorAlertMessage").textContent = result.message;
                        document.getElementById("error_alert").classList.remove("d-none");
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = ``;
                        submitBtn.innerHTML = `Submit`;
                    }
                })
                .catch(error => {
                    alert("Something Went Wrong. Please Try Again.");
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = ``;
                    submitBtn.innerHTML = `Submit`;
                });
        }
    }
});