// document.addEventListener("DOMContentLoaded", function () {
    GET_SUBJECTLIST();

    document.getElementById("close_modal").addEventListener("click", function () {
        document.getElementById("tadiForm").reset();
        document.getElementById("error_alert").classList.add("d-none");
    });

    document.addEventListener("click", function (e) {
        if (e.target.classList.contains("submitTadi")) {
            e.preventDefault();

            const form = document.getElementById("tadiForm");
            
            form.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));

            let isValid = true;
            const requiredFields = ["instructor", "learning_delivery_modalities", "session_type", "classStartDateTime", "classEndDateTime", "comments"];

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

            if (isValid) {
                const formData = new FormData(form);
                formData.append("type", "SUBMIT_TADI");

                fetch("tadi/student/controller/index-post.php", {
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

                        const toast = new bootstrap.Toast(document.getElementById("successToast"));
                        document.getElementById("toastMessage").textContent = result.message;
                        toast.show();

                        if (result.count <= 3 && result.count > 0) {
                            if (confirm(`Would you like to submit another TADI? (${result.count}/3 submitted today)`)) {
                                form.reset();
                            }
                        }
                    } else {
                        document.getElementById("errorAlertMessage").textContent = result.message;
                        document.getElementById("error_alert").classList.remove("d-none");
                    }
                    
                })
                .catch(error => {
                    console.error("Submission error:", error);
                    alert("Error submitting TADI: " + error);
                });
            }
        }
    });
