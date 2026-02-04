$(document).ready(function() {

	$.ajax({
		type: 'GET',
		url: '../controller/DashboardInfo.php',
		data: 	
		{
			type : 'GET_ACCESS_LEVEL'
		},
		dataType : 'json',
		success: function(response)
		{

			response.forEach(function(response) {

    			showElementById(response)
    			// if(response == 'mnu-ess-staff'){

				// 	showElementById('ess-staff-div');

				// 	hideElementById('ess-teamleader-div');
				// 	hideElementById('ess-finance-div');	

    			// }else if(response == 'mnu-ess-teamleader'){

				// 	showElementById('ess-teamleader-div');

				// 	hideElementById('ess-staff-div');
				// 	hideElementById('ess-finance-div');

    			// } else if(response == 'mnu-ess-finance'){

				// 	showElementById('ess-finance-div');
		
				// 	hideElementById('ess-staff-div');
				// 	hideElementById('ess-teamleader-div');
    			// }
			});
		}
	})

	$.ajax({
		type: 'GET',
		url: '../controller/EssTeamleaderInfo.php',
		data: 	
		{
			type : 'GET_ESS_NAME'
		},
		dataType : 'json',
		success: function(result)
		{
			if(result.firstname === "Shien" && result.lastname === "Martinez" ){ // ADD NOTIFICATION BADGE FOR MAAM SHIEN ONLY

				$('#mnu-ess-teamleader').addClass('notification');
				$('#mnu-ess-finance').addClass('notification');

				showElementById('teamleader-notif-badge');
				showElementById('finance-notif-badge');


				ADD_NO_OF_STUDENTS_TO_BADGE();
				ADD_NO_OF_STUDENTS_WITH_PAYMENTS();

			} else {

				$('#mnu-ess-teamleader').removeClass('notification');
				$('#mnu-ess-finance').removeClass('notification');

				hideElementById('teamleader-notif-badge');
				hideElementById('finance-notif-badge');

				if(result.firstname === "Gerald" && result.lastname === "Garcia" ){ // LIMIT THE OPTIONS FOR SEARCHING FOR SIR GED GARCIA

					staff_academiclevel

					$('#staff_academiclevelid option').remove();
					$('#staff_academiclevelid').append("<option value='3' readonly> GRADUATE SCHOOL </option>");

				}
				

			}


		}
	});



	$("#mnu-ess-staff").click(function(e){

		showElementById('ess-staff-div');

		hideElementById('ess-home-div');
		hideElementById('ess-teamleader-div');
		hideElementById('ess-finance-div');	
	});

	$('#mnu-ess-teamleader').click(function(e){

		showElementById('ess-teamleader-div');

		hideElementById('ess-home-div');
		hideElementById('ess-staff-div');
		hideElementById('ess-finance-div');
	});

	$('#mnu-ess-finance').click(function(e){

		showElementById('ess-finance-div');
		
		hideElementById('ess-home-div');
		hideElementById('ess-staff-div');
		hideElementById('ess-teamleader-div');
	});
});