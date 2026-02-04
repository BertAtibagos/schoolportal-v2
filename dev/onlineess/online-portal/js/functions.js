function EnterOnlyNumberKey(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;
}

function checkEmail() {
    var email = document.getElementById('emailaddress');
    var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if (!filter.test(email.value)) {
        alert('Please provide a valid email address');
        email.focus;
        return false;
    }
}

function validateEmail(email) {
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}

function removeOptionsFromSelect(select, text) {
    $('#' + select)
        .find('option')
        .remove()
        .end()
        .append('<option value="0"> -- select ' + text + ' -- </option>')
        .val('0');
}

function validatePasswords(first, second) {
    return first === second
}

function updateSelect(data, selectName, valueCol, textCol, newSelectValue = null) {
    if (data) {

        data = JSON.parse(data)
        $.each(data, function (i, item) {
            $('#' + selectName).append($('<option>', {
                value: item[valueCol],
                text: item[textCol]
            }));
        });
    }
    if (newSelectValue !== null) {
        $("#" + selectName).val(newSelectValue).change()
    }
}

function removeRequirements() {
    $('#registration-requirements')
        .find('div')
        .remove()
        .end();
}

function processRequirements(data) {

    removeRequirements()
    if (data) {
        data = JSON.parse(data)
        $.each(data, function (i, item) {
            $('#registration-requirements').append(
                $('<div>', {
                    class: 'mb-3'
                }).append(
                    $('<label>', {
                        class: 'form-label',
                        for: item['req_code'],
                        html: item['req_name']
                    })).append(
                    $('<input>', {
                        class: 'form-control',
                        type: 'file',
                        id: item['req_code'],
                        name: item['req_code'],
                        required: true,
                        accept: "image/jpeg,image/jpg,image/png,application/pdf,application/msword,application/vnd.ms-excel"
                    })
                ));

        });
    }
}

function switchHiddenAttr(hide, show) {
    hide.forEach(element => {
        $("#" + element).attr('hidden', true);
    })
    $("#" + show).removeAttr("hidden");
}

function hideElementById(element) {
    $("#" + element).attr('hidden', true);
}

function showElementById(element) {
    $("#" + element).removeAttr('hidden');
}

function checkPaymentSubmission() {
    $.ajax({
        type: 'GET',
        url: baseDir + 'php/API/payment.php',
        data: {
            is_payment_submitted: true
        },
        success: function (data) {
            if (JSON.parse(data)) {
                switchHiddenAttr(["payment-form"], "payment-information")
            } else {
                switchHiddenAttr(["payment-information"], "payment-form")
            }
        },
    });
}

function checkRegistrationSubmission() {
    $.ajax({
        type: 'GET',
        url: baseDir + 'php/API/auth.php',
        data: {
            is_registration_submitted: true
        },
        success: function (data) {
            if (JSON.parse(data)) {
                switchHiddenAttr(["enrollment-registration-form"], "registration-information")
            } else {
                switchHiddenAttr(["registration-information"], "enrollment-registration-form")
            }
        },
    });
}

function enableControlByClassName(name) {
    $("." + name).removeAttr("hidden");
    $("." + name).removeAttr("disabled");
    $("." + name).attr('required', true);
}

function showControlByClassName(name) {
    $("." + name).removeAttr("hidden");
    $("." + name).removeAttr("disabled");
}

function disableControlByClassName(name) {
    $("." + name).removeAttr("required");
    $("." + name).attr('disabled', true)
    $("." + name).attr('hidden', true);
}

function enableFormByFormId(name) {
    $("*", "#" + name).removeAttr('disabled');
    $("*", "#" + name).attr('required', true);
    $(".non-required", "#" + name).removeAttr('required');
}

function disableFormByFormId(name) {
    $("*", "#" + name).attr('disabled', true);
    $("*", "#" + name).removeAttr('required');
}

function fillDropdowns(data) {

    $("#student_type").val(data['student_type'])
    $("#academiclevelid").val(data['acadlvl_id']).change()
    // $("#academiccourseid").val(data['acadcrse_id']).change()
    $("#permanentprovinceid").val(data['philarealocprov_permanent_id']).change()
    // $("#permanentmunicipalityid").val(data['philarealocmun_permanent_id']).change()
    // $("#permanentbarangayid").val(data['philarealocbrgy_permanent_id'])
    $("#presentprovinceid").val(data['philarealocprov_present_id']).change()
    // $("#presentmunicipalityid").val(data['philarealocmun_present_id']).change()
    // $("#presentbarangayid").val(data['philarealocbrgy_present_id'])
    $("#agentid").val(data['enrollagent_id'])
    $("#gender").val(data['schlenrollprereg_gender'])
    $("#civilstatus").val(data['schlenrollprereg_civilstatus'])

}

function sanitizeInput(input) {
	// Step 1: Replace spaces with '@' symbol
	let sanitized = input.replace(/ /g, '@');  // Replace spaces with '@'
	
	// Step 2: Remove special characters, but allow valid email characters
	sanitized = sanitized.replace(/[^a-zA-Z0-9_.@\-]/g, '');  // Remove invalid characters
	
	// Step 3: Remove common SQL keywords (case-insensitive)
	let keywords = ["SELECT", "DROP", "DELETE", "INSERT", "UPDATE", "TABLE", "FROM", "WHERE"];
	let pattern = new RegExp("\\b(" + keywords.join("|") + ")\\b", "gi");
	sanitized = sanitized.replace(pattern, '');

	// Trim any extra spaces
	sanitized = sanitized.trim();

	return sanitized;
}

// Function to show the alert modal with success or fail message
function showAlert(message, type) {
	var $modal = $('#alertModal');

	var $alertMessage = $('#alertMessage');

	// Update the message and style based on success or fail
	$alertMessage.html(message);

	// Show the modal
	$modal.fadeIn();

	// Automatically hide the modal after 3 seconds
	setTimeout(function() {
		$modal.fadeOut();
	}, 3000);
}