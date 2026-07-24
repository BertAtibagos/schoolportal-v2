GET_SUBJECTLIST();

document.getElementById("close_modal").addEventListener("click", function () {
    document.getElementById("tadiForm").reset();
    document.getElementById("error_alert").classList.add("d-none");
});

const button = document.getElementById("confirmBtn");
button.addEventListener("click", function (e) {
    const form = document.getElementById("tadiForm");

    form.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));

    let isValid = true;
    const lateSubmissionCheckbox = document.getElementById("chck_late_submt");
    const makeupDate = document.getElementById("session_type").value;
    let requiredFields =[];

    if(lateSubmissionCheckbox){
        if(lateSubmissionCheckbox.checked || makeupDate === "makeup"){
            requiredFields = ["instructor", "classDate", "learning_delivery_modalities", "session_type", "classStartDateTime", "classEndDateTime", "start_attach", "end_attach", "comments", "late_class_date", "late_reason", "makeup_class_date"];
        }else if(lateSubmissionCheckbox.checked){
            requiredFields = ["instructor", "classDate", "learning_delivery_modalities", "session_type", "classStartDateTime", "classEndDateTime", "start_attach", "end_attach", "comments", "late_class_date", "late_reason"];
        }
    }
    
    if(makeupDate === "makeup"){
        requiredFields = ["instructor", "classDate",  "learning_delivery_modalities", "session_type", "classStartDateTime", "classEndDateTime", "start_attach", "end_attach", "comments", "makeup_class_date"];
    }else{
        requiredFields = ["instructor", "classDate", "learning_delivery_modalities", "session_type", "classStartDateTime", "classEndDateTime", "start_attach", "end_attach", "comments"];
    }
    
    requiredFields.forEach(field => {
        const input = document.getElementById(field);
        if (!input.value) {
            input.classList.add("is-invalid");
            isValid = false;
        }
    });
    
    if (lateSubmissionCheckbox.checked) {
        const lateClassDate = document.getElementById("late_class_date");
        const lateReason = document.getElementById("late_reason");

        if (!lateClassDate.value) {
            lateClassDate.classList.add("is-invalid");
            isValid = false;
        }

        if (!lateReason.value) {
            lateReason.classList.add("is-invalid");
            isValid = false;
        }
    }

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

    if (comments.value.length < 1) {
        comments.classList.add("is-invalid");
        comments.nextElementSibling.textContent = "Comments must be at least 1 character long";
        isValid = false;
    }

    const dateInput = document.getElementById("classDate");
    const inputDate = new Date(dateInput.value);
    const today = new Date();

    today.setHours(0, 0, 0, 0);
    inputDate.setHours(0, 0, 0, 0);

    const maxPastDays = 2;

    const pastLimit = new Date(today);
    pastLimit.setDate(today.getDate() - maxPastDays);


    if (inputDate < pastLimit) {
        dateInput.classList.add("is-invalid");
        dateInput.nextElementSibling.textContent = `Date cannot be more than ${maxPastDays} days in the past`;
        isValid = false;
    } else if (inputDate > today) {
        dateInput.classList.add("is-invalid");
        dateInput.nextElementSibling.textContent = `Date cannot be in the future`;
        isValid = false;
    }

    if (isValid) {
        const confirmed = confirm("Are you sure you want to submit this TADI?");
        const submitBtn = document.querySelector(".submitTadi");
        if (confirmed) {
            const formData = new FormData(form);
            formData.append("type", "SUBMIT_TADI");

            const lateSubmissionCheckbox = document.getElementById("chck_late_submt");
            if (!lateSubmissionCheckbox.checked) {
                formData.delete("late_class_date");
                formData.delete("late_reason");
            }

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