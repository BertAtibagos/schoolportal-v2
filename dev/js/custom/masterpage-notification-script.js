$(document).ready(function(){
	function GetApprovalRequest(){
		//try { 
			$.ajax({
				type:'GET',
				url: 'masterpage-notification-controller.php',
				data: { type:'GET_APPROVAL_REQUEST' },
				success: function(result){
					var ret = JSON.parse(result);
					var spannotifier = '';
					if(ret.length) {
						$.each(ret, function(key, value) {
							spannotifier += "<span class='notifier'>" + value.CNT + "</span>";
						});
					} else {
						spannotifier += "";
					}
					$('#span-notifier').html(spannotifier);
				}
			});
		//}
		//catch(e) {  //We can also throw from try block and catch it here
		//	console.error(e);
		//}
		//finally {
		//	console.log('We do cleanup here');
		//}
	}
	
	function Initialize() {
		GetAcademicLevel('ACADLEVEL',0);
		GetAcademicYear('ACADYEAR',$('#cbo-acadlvl').val());
		GetAcademicPeriod('ACADPERIOD',$('#cbo-acadlvl').val(),$('#cbo-acadyr').val());
		GetSubmittedRequestList('FOR_APPROVAL_REQUEST_LIST',$('#cbo-acadlvl').val(),$('#cbo-acadyr').val(),$('#cbo-acadprd').val());
	}
	Initialize();
});