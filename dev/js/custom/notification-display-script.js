$(document).ready(function() {
    var div_popup = "<div id='popup' class='col-md-3' style='position: fixed; bottom: 1rem; right: 0; padding-inline: 1rem; overflow-y: auto; max-height: 100vh;'></div>";
    $('body').append(div_popup);

	var ifMasterPage = $('#ifMasterPage').val();

	var ajaxurl = '';
	var picurl = '';
	if(ifMasterPage == '' || ifMasterPage == null){
		ajaxurl = '../model/forms/notification/notification-display-controller.php';
		picurl = '../images/COLLEGE_LOGO-PNG.png';

	} else if(ifMasterPage === '1'){
		ajaxurl = '../../model/forms/notification/notification-display-controller.php';
		picurl = '../../images/COLLEGE_LOGO-PNG.png';
	}

    $.ajax({
        type:'POST',
        url: ajaxurl,
        data:{
            type : 'GET_NOTIF'
        },
        success: function(result){
            var ret = JSON.parse(result);
            // console.log(ret);
            if(ret.length) {
                var create_popup = "";
                $.each(ret, function(key, value) {

                    var today = new Date();
                    var create_date = new Date(value.CREATE_DATE);
                    var diffInMs = today - create_date;
                    var diffInDays = (diffInMs / (1000 * 60 * 60 * 24)).toFixed(0);

                    if(diffInDays == 1){
                        var day_text = ' day'
                    } else {
                        var day_text = ' days'
                    }

                    create_popup += "<div class='toast' role='alert' aria-live='assertive' aria-atomic='true' data-bs-autohide='false' style='margin-top: .5rem; background-color: rgba(255, 255, 255, 0.95); width: 100%;'>" +
                                        "<div class='toast-header'>" +
                                            "<img src='" + picurl + "' class='me-2' style='width: auto; height: 2rem;'>" +
                                            "<strong class='me-auto'>FCPC | School Portal</strong>" +
                                            "<button type='button' class='btn-close' data-bs-dismiss='toast' aria-label='Close'></button>" +
                                        "</div>" +
                                        "<div class='toast-header'>" +
                                            "<strong class='me-auto'>" + value.TITLE + "</strong>" +
                                            "<small class='text-muted'>" + diffInDays + day_text + " ago</small>" +
                                        "</div>" +
                                        "<div class='toast-body'>" +
                                            "<div>" + value.CONTENT + "</div>";

                    if (value.LINK_URL == null || value.LINK_URL == '' || value.LINK_TEXT == null || value.LINK_TEXT == '') {
                        create_popup += "</div>" +
                                        "</div>";
                    } else {
                        create_popup += "<div><a href='" + value.LINK_URL + "'>" + value.LINK_TEXT + "</a></div>" +
                                            "</div>" +
                                        "</div>";
                    }
                });

                $('#popup').append(create_popup);
                $('.toast').toast('show');

                $('.toast').each(function(index) {
                    setTimeout(function() {
                        $(this).fadeOut('slow');
                        $(this).remove();
                    }.bind(this), 10000 + index * 500);
                });
            } else {

            }
        }
    });
});