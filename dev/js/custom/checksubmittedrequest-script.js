$(document).ready(function(){
    if ($('#user-type').text() != 'STUDENT' && $('#user-type').text().length > 0){
        $('#container-input-hidden-div').html('<div id="input-hidden-div" hidden></div>');
        display_ct();
    }
    
	$("#input-hidden-div").on('DOMSubtreeModified', function() {
		var _valdiv = $("#input-hidden-div").html();
		if ((parseInt(_valdiv) === 30) || (parseInt(_valdiv) === 59)){
			$.ajax({
				type:'GET',
				url: 'checksubmittedrequest-controller.php',
				data: { 
					str: 'INITIALIZE',
					type: 'CHECK_SUBMITTED_REQUEST_LIST'
				},
				success: function(result){
					var ret = JSON.parse(result);
					$.each(ret, function(key, value) {
						if (parseInt(value.NOTIF) > 0){
							$('#notifier-submitted-grades-count').addClass("notifier");
							//$('.notifier').show();
							$('.notifier').html(parseInt(value.NOTIF));
						} else {
							$('#notifier-submitted-grades-count').removeClass("notifier");
							//$('.notifier').hide();
							//$('.notifier').html('');
						}
					});
				}
			});
		}
	});
	function display_ct() {
		var x = new Date()
		document.getElementById('input-hidden-div').innerHTML = x.getSeconds();
		display_c();
	}
	function display_c(){
		var refresh=1000; // Refresh rate in milli seconds
		mytime=setTimeout('display_ct()',refresh)
	}
	$(".main").click(function(){
		$(".list").removeClass("active");
	});
	$(".list").click(function(){
		$(".main").removeClass("active");
	});
});