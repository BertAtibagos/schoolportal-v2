$(document).ready(function() {
    $(document).on('click', '.remove, .confirm', function(e) {
        e.preventDefault();
        var link = this;
        $('#confirmation-msg').html($(this).data('message'));
        $('#confirmation-yes').on('click', function(e) {
            var callback = $(link).data('callback');
            if(typeof window[callback] == 'function') {
                window[callback](link);
            } else {
                window.location = $(link).data('url');
            }
            $('#confirmation').modal('hide');
        });
        if(yesBtn = $(this).data('yes-btn')) {
            $('#confirmation-yes').html(yesBtn);
        }
        if(noBtn = $(this).data('no-btn')) {
            $('#confirmation-no').html(noBtn);
        }
		if(cancelBtn = $(this).data('cancel-btn')) {
            $('#confirmation-cancel').html(cancelBtn);
        }
        $('#confirmation').modal('show');
    });
    $('#confirmation').on('hidden.bs.modal', function () {
        //$('#confirmation-yes').off();
        $('#confirmation-msg').empty();
        $('#confirmation-yes').text('Yes');
        $('#confirmation-no').text('No');
		$('#confirmation-cancel').text('Cancel');
    });
	$('#confirmation-no').click(function() {
		$('#confirmation').modal('hide');
	});
	$('#confirmation-yes').click(function() {
		$('#confirmation').modal('hide');
	});
	$('#confirmation-cancel').click(function() {
		$('#confirmation').modal('hide');
	});
});