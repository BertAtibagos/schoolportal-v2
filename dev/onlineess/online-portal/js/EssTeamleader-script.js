$(document).ready(function() {

	//########################### TO UPPER CASE ALL INPUT TYPE TEXT //
	$('input[type="text"]').keyup(function(){
		$(this).val($(this).val().toUpperCase());
	});

	$('#name-text').keypress(function(event){
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if(keycode == '13'){
		   
		   $("#search_button").click(); //alert('You pressed a "enter" key in textbox, here submit your form'); 
		}
	});

    $('#name-select').keypress(function(event){
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if(keycode == '13'){
		   
		   $("#search_button").click(); //alert('You pressed a "enter" key in textbox, here submit your form'); 
		}
	});

    $('#academiclevelid').keypress(function(event){
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if(keycode == '13'){
		   
		   $("#search_button").click(); //alert('You pressed a "enter" key in textbox, here submit your form'); 
		}
	});

	 $('#admissionstatusid').keypress(function(event){
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if(keycode == '13'){
		   
		   $("#search_button").click(); //alert('You pressed a "enter" key in textbox, here submit your form'); 
		}
	});

	// ########################### `CLICK` TO GET REGISTERED STUDENTS
	$("#search_button").click(function(e){ 

		GET_REGISTERED_STUDENTS();

	})

	$(document).on('change', 'select[name="assigned_ess"]', function(){

		let text = this.value;
		const myArray = text.split(",");

		var ess_id = myArray[0];
		var reg_id = myArray[1];

		$.ajax({
			type: 'POST',
			url: '../controller/EssTeamleaderPost.php',
			data: {

				type: 'POST_ESS_STAFF_ASSIGNED',
				ess_id: ess_id,
				reg_id: reg_id,
			},
			success: function () {

			},
			error: function (request, status, error) {
				console.log(request.responseText);
			}
				
		});
	})

	// ########################### SHOW STUDENT ADMISSION //
	$(document).on('click', 'input[name="viewAdmission"]', function(){

		GET_REGISTRATION_INFO(this.id);
		hideElementById('teamleader-div-dashboard');
	})

	// ########################### CLICK T0 'RETURN TO DASHBOARD'
	$("#viewDashboard").click(function(e){

		showElementById('teamleader-div-dashboard');
		hideElementById('student-admission-info');
		$("#search_button").focus();

	})

	// **** SHOW DOCUMENT DOWNLOAD SECTION **** //

	$(document).on('click', 'input[name="view_document"]', function(){

		showElementById('div_view_document')

		$('#document_div div').remove();

		var div_docu = ''

		div_docu += '<div class="row mb-3">' + 
                     	"<a href='../controller/DownloadController.php?id_loc=" + this.id + "'>" + 
                                "<label class='btn btn-info form-control download_link download_link' style = 'font-size: 20px;'>" + 
                                    "<span class='iconify' data-icon='bx:download'></span> Download Document" + 
                                "</label>" + 
                            "</a>" + 
                    "</div>"


		$('#document_div').append(div_docu);
	})


	// **** FOR PROCESSING STUDENT ASSESSMENT **** //

	$(document).on('click', 'input[id="btnAssess"]', function(){

		$("#loadingModal").fadeIn();

		let text = this.name;

		const myArray = text.split(",");

		var reg_id    = myArray[0];
		var reg_type  = myArray[1];
		var oc_acc_id = myArray[2];	
		var lvlid 	  = myArray[3];	

		$.ajax({
			type: 'POST',
			url: '../controller/EssTeamleaderPost.php',
			dataType: 'json',
			data:
			{
				type: 'POST_ASSESSMENT',
				reg_id : reg_id,
				reg_type : reg_type,
				oc_acc_id : oc_acc_id,
				lvlid     : lvlid
			},
			success: function (result) {

				if(result.status == 1){

					var message = 	'<div class="success alert">'+
										'<div class="content">'+
											'<div class="icon">'+
												'<svg width="50" height="50" id="Layer_1" style="enable-background:new 0 0 128 128;" version="1.1" viewBox="0 0 128 128" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g><circle fill="#fff" cx="64" cy="64" r="64"/></g><g><path fill="#3EBD61" d="M54.3,97.2L24.8,67.7c-0.4-0.4-0.4-1,0-1.4l8.5-8.5c0.4-0.4,1-0.4,1.4,0L55,78.1l38.2-38.2   c0.4-0.4,1-0.4,1.4,0l8.5,8.5c0.4,0.4,0.4,1,0,1.4L55.7,97.2C55.3,97.6,54.7,97.6,54.3,97.2z"/></g></svg>'+
											'</div>'+
											'<p> ' + result.message + ' </p>'+
										'</div>'+
									'</div>';
				} else {

					var message = 	'<div class="danger alert">'+
										'<div class="content">'+
											'<div class="icon">'+
												'<svg height="50" viewBox="0 0 512 512" width="50" xmlns="http://www.w3.org/2000/svg"><path fill="#fff" d="M449.07,399.08,278.64,82.58c-12.08-22.44-44.26-22.44-56.35,0L51.87,399.08A32,32,0,0,0,80,446.25H420.89A32,32,0,0,0,449.07,399.08Zm-198.6-1.83a20,20,0,1,1,20-20A20,20,0,0,1,250.47,397.25ZM272.19,196.1l-5.74,122a16,16,0,0,1-32,0l-5.74-121.95v0a21.73,21.73,0,0,1,21.5-22.69h.21a21.74,21.74,0,0,1,21.73,22.7Z"/></svg>'+
											'</div>'+
											'<p> ' + response.message + ' </p>'+
										'</div>'+
									'</div>';
				}


				$("#loadingModal").fadeOut();
				showAlert(message, 'fail');

				showElementById('teamleader-div-dashboard')
				hideElementById('student-admission-info')
				$("#search_button").click();

			},
			error: function (request, status, error) {
				console.log(request.responseText);
			}
		});
	})

	// ***** UPDATING ESC OR SHS VOUCHER  

	$(document).on('change', 'select[name="esc_or_shs"]', function(){

		let text = this.value;
		const myArray = text.split(",");

		var id 			= myArray[0];
		var reg_type 	= myArray[1];
		var esc_voucher = myArray[2];

		$.ajax({
			type:'POST',
			url: '../controller/EssTeamleaderPost.php',
			data:
			{
				type : 'POST_ESC_SHS_VOUCHER',
				id : id,
				reg_type : reg_type,
				esc_voucher : esc_voucher,
			},
			dataType:'json',
			success: function()
			{

			}
		})

	})

	// ***** CANCELLING STUDENT ADMISSION IN `LIST OF REGISTERED STUDENTS`

	$(document).on('click', 'input[name="cancelAdmission"]', function(){

		let text = this.id;
		const myArray = text.split(",");

		var reg_id		= myArray[0];
		var adm_id  	= myArray[1]

		
		$.ajax({
			type:'POST',
			url: '../controller/EssTeamleaderPost.php',
			data:
			{
				type : 'POST_CANCEL_ADMISSION',
				reg_id : reg_id,
				adm_id : adm_id,
			},
			dataType:'json',
			success: function(response)
			{
				if(response.status == 1){

					var message = 	'<div class="success alert">'+
										'<div class="content">'+
											'<div class="icon">'+
												'<svg width="50" height="50" id="Layer_1" style="enable-background:new 0 0 128 128;" version="1.1" viewBox="0 0 128 128" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g><circle fill="#fff" cx="64" cy="64" r="64"/></g><g><path fill="#3EBD61" d="M54.3,97.2L24.8,67.7c-0.4-0.4-0.4-1,0-1.4l8.5-8.5c0.4-0.4,1-0.4,1.4,0L55,78.1l38.2-38.2   c0.4-0.4,1-0.4,1.4,0l8.5,8.5c0.4,0.4,0.4,1,0,1.4L55.7,97.2C55.3,97.6,54.7,97.6,54.3,97.2z"/></g></svg>'+
											'</div>'+
											'<p> ' + response.message + ' </p>'+
										'</div>'+
									'</div>';

				} else {

					var message = 	'<div class="danger alert">'+
										'<div class="content">'+
											'<div class="icon">'+
												'<svg height="50" viewBox="0 0 512 512" width="50" xmlns="http://www.w3.org/2000/svg"><path fill="#fff" d="M449.07,399.08,278.64,82.58c-12.08-22.44-44.26-22.44-56.35,0L51.87,399.08A32,32,0,0,0,80,446.25H420.89A32,32,0,0,0,449.07,399.08Zm-198.6-1.83a20,20,0,1,1,20-20A20,20,0,0,1,250.47,397.25ZM272.19,196.1l-5.74,122a16,16,0,0,1-32,0l-5.74-121.95v0a21.73,21.73,0,0,1,21.5-22.69h.21a21.74,21.74,0,0,1,21.73,22.7Z"/></svg>'+
											'</div>'+
											'<p> ' + response.message + ' </p>'+
										'</div>'+
									'</div>';

				}
				
				$("#loadingModal").fadeOut();
				showAlert(message, 'fail');

				showElementById('teamleader-div-dashboard')
				hideElementById('student-admission-info')
				$("#search_button").click();
			}
		})
		
	})

	// ***** CANCELLING STUDENT ADMISSION IN `STUDENT INFORMATON`
	$("#btnCancel").click(function(e){

		let text = this.name;
		const myArray = text.split(",");

		var reg_id		= myArray[0];
		var adm_id  	= myArray[1]

		$.ajax({
			type:'POST',
			url: '../controller/EssTeamleaderPost.php',
			data:
			{
				type : 'POST_CANCEL_ADMISSION',
				reg_id : reg_id,
				adm_id : adm_id,
			},
			dataType:'json',
			success: function(response)
			{
				if(response.status == 1){

					alert(response.message)
					showElementById('teamleader-div-dashboard')
					hideElementById('student-admission-info')
					$("#search_button").click();

				} else {

					alert(response.message)

				}
			}
		})
	})




})

