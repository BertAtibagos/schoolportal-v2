
 // 

var ess_list = [];

$.ajax({
	type:'GET',
	url: '../controller/EssTeamleaderInfo.php',
	data:
	{
		type : 'GET_ESS',
	},
	dataType:'json',
	async: false,
	success: function(result)
	{   
		ess_list = result
	}
})

//########################### FORMAT DATE FOR SCHEDULE VIEWING 

function FORMAT_DATE_FOR_SCHEDULE(date){ 

	var formattedText = null;

	if(date)
	{
		// Split the input text by '=' to separate day and time information
		var parts = date.split('=');
		// Extract day and time
		var day = parts[1];
		var timeRange = parts[2];
		// Split the time range by '-' to get start and end times
		var times = timeRange.split('-');
		var startTime = times[0];
		var endTime = times[1];
		// Format the time
		startTime = startTime.replace(' PM', 'PM').replace(' AM', 'AM');
		endTime = endTime.replace(' PM', 'PM').replace(' AM', 'AM');
		// Construct the formatted text
		formattedText = day + ' ' + startTime + ' - ' + endTime;

	} else {

		formattedText = date 

	}
	return formattedText;
}

//########################### GET THE NUMBER OF STUDENT FOR ASSESSMENT

function GET_NO_OF_FOR_ASSESSMENT_STUDENTS(){

	var for_assessment = null;
	
	$.ajax({
		type:'GET',
		url: '../controller/EssTeamleaderInfo.php',
		data:
		{
			type : 'GET_NO_OF_FOR_ASSESSMENT_STUDENTS',
		},
		dataType:'json',
		async : false,
		success: function(result)
		{   
			for_assessment = result
		}
	})

	return for_assessment;

}

function ADD_NO_OF_STUDENTS_TO_BADGE(){

	var for_assessment = GET_NO_OF_FOR_ASSESSMENT_STUDENTS();

	$('#teamleader-notif-badge').text(for_assessment);

}

//########################### FOR GETTING REGISTERED STUDENT

function GET_REGISTERED_STUDENTS(){

	$("#loadingModal").fadeIn(); // ***** SHOW THE DIV FOR THE LOADING ANIMATION UPON GETTING THE STUDENTS LIST

	var stud_name 		= $('#name-text').val();
	var stud_name_type 	= $('#name-select').val();
	var lvlid 			= $('#academiclevelid').val();
	var adm_statusid	= $('#admissionstatusid').val();
  
	var student_arr = []; // ***** MAIN STUDENT ARRAY CONTAINER
	var count = 1;

	var ess = ess_list // **** GET THE LIST OF ESS STAFF AS AN ARRAY

	//// ***** IF THE TABLE IS ALREADY A `DATA TABLE` DESTROY THE TABLE FOR RE-INITIALIZATION
	if ($.fn.DataTable.isDataTable("#teamleader-table")) { $('#teamleader-table').DataTable().clear().destroy(); }

	// ***** INITIALIZE THE `DATA TABLE`
	$('#teamleader-table').DataTable({

		ajax: {
			type:'GET',
			url: '../controller/EssTeamleaderInfo.php',
			data:
			{
				type 	  		: 'GET_REGISTERED_STUDENTS',
				stud_name 		: stud_name,
				stud_name_type 	: stud_name_type,
				lvlid			: lvlid,
				adm_statusid 	: adm_statusid,
	 		},
			dataType:'json',
			success : function(result){

				if (result.length != 0) {
					$.each(result, function (key, value) {

						var child_arr = []; // ***** ARRAY FOR GETTING THE DATA PER ROW 

						// ***** FOR DISPLAYING & ADDING OF `ESS STAFF` 
						var essRecords = "";
						var status_column = "";
						var ess_select =	"<select id='assigned_ess' name='assigned_ess' class='form-control assigned_ess' style='font-size:inherit;'><option value='0,0'>UNASSIGNED</option>";

												$.each(ess, function(key, essinfo)
												{	
													
													// ###### DISPLAY ESS ASSIGNED ON LOAD OF TABLE ###### //

													if(essinfo.ID == value.ESS_ID)
													{	
														essRecords += "<option value='" + essinfo.ID + ',' + value.REG_ID + "' selected>" + essinfo.NAME + "</option>";
													}
													else
													{
														essRecords += "<option value='" + essinfo.ID + ',' + value.REG_ID + "'>" + essinfo.NAME + "</option>";
													}

												})
												ess_select += essRecords;
						ess_select +=		"</select>";

						// ***** FOR DISPLAYING THE `ADMISSION STATUS`
						if(parseInt(value.REG_STATUS) == 0){

							status_column = "<button class='btn-design-secondary'> TO BE ADMITTED </button>";

						} else if( (parseInt(value.REG_STATUS) == 1)){

							if(parseInt(value.REG_STATUS) == 1 && parseInt(value.ADM_STATUS) == 0) {

								status_column = "<button class='btn-design-info'> ADMITTED </button>";

							} else if(parseInt(value.REG_STATUS) == 1 && parseInt(value.ADM_STATUS) == 1) {

								status_column = "<button class='btn-design-success'> ASSESSED </button>";
							}
						}

						// ***** BUTTON FOR VIEWING THE REGISTRATION INFO OF STUDENTS
						var actions_btn = "<input type='button' id='" + value.REG_ID + "' name='viewAdmission' class='btn btn-button-primary' value='VIEW PROFILE' style = 'font-size: inherit;'></input>";
						
						var cancel_btn = " ";

						// -- ===== CANCEL ADMISSION IS ONLY FOR THE GRADUATE SCHOOL AS OF THE MOMENT THIS MODULE IS ADDED

						if(lvlid == 3){

							cancel_btn  = "<input type='button' id='" + value.REG_ID + "," + value.ADM_ID  + "' name='cancelAdmission' class='btn btn-button-danger' value='CANCEL ADMISSION' style = 'font-size: inherit;'></input>";
						}
					

						// ***** PUSH DATA AS A ROW FOR STUDENTS LIST
						child_arr.push(
							count,
							value.REG_DATE,
							value.NAME,
							value.REG_TYPE,
							value.SOURCE,
							value.CRSE_NAME,
							ess_select,
							status_column,
							actions_btn,
							cancel_btn,
						);

						student_arr.push(child_arr); // ***** ADD THE ROW OF STUDENT'S TO THE MAIN ARRAY 
						count++;

					})

					$('#teamleader-table').DataTable().clear().rows.add(student_arr).draw(); // ***** ADD THE ROWS TO THE DATA TABKE 


				} else {

					var child_arr = [];

					child_arr.push(count, "", "","", "<button class='btn-design-danger'>NO RECORD FOUND</button>", "", "", "", ""); // **** ADD A `NO RECORD` INDICATOR TO THE TABLE 

					student_arr.push(child_arr);
					$('#teamleader-table').DataTable().clear().rows.add(student_arr).draw();

				}


			},
			error: function (request, status, error) {
				console.log(request.responseText);
			},
			complete: function() {
				$("#loadingModal").fadeOut(); // 
				ADD_NO_OF_STUDENTS_TO_BADGE(); 

			},
		},
		data: student_arr,
		columns: [
			{ title: '#' },
			{ title: 'REGISTRATION DATE' },
			{ title: 'STUDENT NAME' },
			{ title: 'STUDENT TYPE' },
			{ title: 'SOURCE' },
			{ title: 'COURSE/STRAND' },
			{ title: 'ESS STAFF ASSIGNED' },
			{ title: 'STATUS' },
			{ title: 'ACTIONS' },
			{ title: '' },

		],
		columnDefs: [
			{"defaultContent": "-",
			"targets": "_all",
		  },
		],
		lengthMenu: [
			[5, 10, 25, 50, 100, -1],
			[5, 10, 25, 50, 100, "All"],
		],
		fixedColumns: {
			left: 1,
			right: 1,
		},
		order: [[9, 'desc']],
		pageLength: 50,
		scrollCollapse: true,
		stateSave: true,
		dom:'lrtip'
	})


	// $.ajax({
	// 	type:'GET',
	// 	url: '../controller/EssTeamleaderInfo.php',
	// 	data:
	// 	{
	// 		type 	  		: 'GET_REGISTERED_STUDENTS',
	// 		stud_name 		: stud_name,
	// 		stud_name_type 	: stud_name_type,
	// 		lvlid			: lvlid,
	// 		adm_statusid 	: adm_statusid,
	// 	},
	// 	dataType:'json',
	// 	async: false,
	// 	success: function(result)
	// 	{
	// 		console.log(result)
	// 	}
	// })
}

function GET_REGISTRATION_INFO(reg_id){ // ***** GET STUDENT REGISTRATION INFO 

	var lvlid = $('#academiclevelid').val();

	$("#loadingModal").fadeIn(); // ***** SHOW THE DIV FOR THE LOADING ANIMATION UPON GETTING THE STUDENTS LIST
	

	$.ajax({
		type:'GET',
		url: '../controller/EssTeamleaderInfo.php',
		data:
		{
			type : 'GET_REGISTRATION_INFORMATION',
			registration_id : reg_id,
			lvlid : lvlid,
		},
		dataType:'json',
		success: function(admInfo){


			// ##### ADD UNIQUE `NAME` TO ADMIT BUTTON

			var btnassess_name = admInfo.REG_ID + "," + admInfo.REG_TYPE + "," + admInfo.OC_ACC_ID +  "," + admInfo.REG_LVLID;

			$('#btnAssess').attr('name', btnassess_name);
			$('#btnCancel').attr('name', admInfo.REG_ID + "," + admInfo.ADM_ID);


			// ###### DISPLAY OR HIDE `VERIFY ADMISSION ` BUTTON BASED ON `ADM_STAT`

			if( admInfo.ADM_STAT == '0' ){

				showElementById('btnAssess')

			} else {

				hideElementById('btnAssess')
			}

			if(admInfo.REG_LVLID == '1'){

				enableControlByClassName('view_lrn_number_div');
				enableControlByClassName('esc_or_shs_div');

				var optESC_SHS ='<option value=""> -- SELECT ESC / SHS VOUCHER -- </option>';

				var ID = null;

				if(admInfo.REG_TYPE == "OLD"){

					ID = admInfo.REG_ID;

				} else {

					ID = admInfo.OC_ACC_ID

				}

				if( parseInt(admInfo.ADM_YRLVLID) >= 17 && parseInt(admInfo.ADM_YRLVLID) <= 20){

					optESC_SHS += 	"<option value='" + ID + "," + admInfo.REG_TYPE + ",WITHOUT ESC'> WITHOUT ESC </option>" + 
									"<option value='" + ID + "," + admInfo.REG_TYPE + ",WITH ESC'> WITH ESC </option>";

				}
				if( parseInt(admInfo.ADM_YRLVLID) == 21 ){

					optESC_SHS += 	"<option value='" + ID + ","+ admInfo.REG_TYPE + ",WITHOUT ESC CERT OR SHS VOUCHER'> WITHOUT ESC CERT OR SHS VOUCHER </option>" + 
									"<option value='" + ID + ","+ admInfo.REG_TYPE + ",WITH ESC CERT OR SHS VOUCHER'> WITH ESC CERT OR SHS VOUCHER </option>";

				}
				if( parseInt(admInfo.ADM_YRLVLID) == 5 ){

					optESC_SHS += 	"<option value='" + ID + "," + admInfo.REG_TYPE + ",WITHOUT SHS VOUCHER'> WITHOUT SHS VOUCHER </option>" + 
									"<option value='" + ID + "," + admInfo.REG_TYPE + ",WITH SHS VOUCHER PRIVATE'> WITH SHS VOUCHER PRIVATE </option>" +
									"<option value='" + ID + "," + admInfo.REG_TYPE + ",WITH SHS VOUCHER PUBLIC' > WITH SHS VOUCHER PUBLIC </option>";

				}

				$('#esc_or_shs option').remove();
				$('#esc_or_shs').append(optESC_SHS);

				enableControlByClassName('family-information-section');
				enableControlByClassName('special-arrangements');
				enableControlByClassName('last-school-information');

				disableControlByClassName('work-experience-information')
				disableControlByClassName('view_section_subjects');
				disableControlByClassName('cancel-btn-container');


			} else if(admInfo.REG_LVLID == '2'){

				disableControlByClassName('view_section_subjects');
				disableControlByClassName('work-experience-information');

				enableControlByClassName('last-school-information');
				enableControlByClassName('special-arrangements');
				enableControlByClassName('family-information-section');
				disableControlByClassName('view_lrn_number_div');
				disableControlByClassName('esc_or_shs_div');
				disableControlByClassName('cancel-btn-container');

			} else if(admInfo.REG_LVLID == '3'){
				
				enableControlByClassName('view_section_subjects');
				enableControlByClassName('work-experience-information');

				disableControlByClassName('last-school-information');
				disableControlByClassName('special-arrangements');
				disableControlByClassName('family-information-section');
				disableControlByClassName('view_lrn_number_div');
				disableControlByClassName('esc_or_shs_div');
				enableControlByClassName('cancel-btn-container');

				// ****** FOR GETTING SECTION AND SUBJECT SCHEDULE  // 

				var subject_list = admInfo.REG_SUBJ_LIST

				$.ajax({
					type:'GET',
					url: '../controller/EssTeamleaderInfo.php',
					data:
					{
						type : 'GET_SECTION_SUBJECTS_SCHED',
						subject_list : subject_list,
					},	
					dataType : 'json',
					success: function(result)
					{

						var optSection_Subject = "";
						if(result != null) {

							optSection_Subject = "";
							optSection_Subject += '<div class="row mb-3 subject_list_container" id="subject_list_container" name="subject_list_container"><br>';
							optSection_Subject += '			<table id="subject-table" class="paleBlueRows" style="font-size:12px;text-align: center; overflow: auto;"  width="100%">';
							optSection_Subject += '				<thead>';
							optSection_Subject += '					<tr>';
							optSection_Subject += "						<th scope='col' style='text-align:center;'>CODE</th>";
							optSection_Subject += "						<th scope='col' style='text-align:center;'>NAME</th>";
							optSection_Subject += "						<th scope='col' style='text-align:center;'>DESCRIPTION</th>";
							optSection_Subject += "						<th scope='col' style='text-align:center;'>UNITS</th>";
							optSection_Subject += "					</tr> ";	 
							optSection_Subject += "				</thead>";

							$.each(result, function(key, value) {
			
								optSection_Subject += "				<tbody id='subject-body' style='text-align: center;'>" +
																		"<tr>" + 
																			'<td style="text-align: center;"> <label>' + value.CODE  + '</label></td>' +
																			'<td style="text-align: center;"> <label>' + value.NAME  + '</label></td>' +
																			'<td style="text-align: center;"> <label>' + value.DESC  + '</label></td>' +
																			'<td style="text-align: center;"> <label>' + value.UNIT  + '</label></td>' +
																		"</tr>";
								
							})

							optSection_Subject += '			</tbody>';
							optSection_Subject += '		</table>';
							optSection_Subject += '&nbsp</div>';
						}
						$('#view_section_subjects_container div').remove();
						$('#view_section_subjects_container').append(optSection_Subject); 

					}
				});

				// ****** FOR DISPLAYING WORKING EXPERIENCE  ****** // 

				if(admInfo.STUD_REGSITE == 'ONLINE'){

					var working_exp = GET_WORKING_EXPERIENCE(admInfo.ENR_REG_ID, admInfo.REG_LVLID, admInfo.ADM_YRID, admInfo.ADM_PRDID	);

					var work_exp = "";

					$('#work-experience-container div').remove(); // ****** REMOVE EXISTING DIV FROM OF WORKING EXPERIENCE
					$('#work-experience-container br').remove();  // ****** REMOVE BR 

					var work_exp_count = 1;

					$.each(working_exp, function (i, item) {

						work_exp += "<br>";

						// work_exp += "<div class='row'>";
						// work_exp += "	<div class='col-md-5'><label for='view_document' class='form-label viewing'> Working Experience #: " + work_exp_count + "</label></div>";
						// work_exp += '</div>';

						work_exp += '<div class="row mb-3">';
						work_exp += '	<div class="col-md-6">';
						work_exp += '		<label class="form-label"> POSITION <span class="text-danger">*</span></label>';
						work_exp += '		<input type="text" value = "' + item['WORK_POSITION']  + ' " class="form-control-plaintext viewing" disabled>';
						work_exp += '	</div>';
						work_exp += '	<div class="col-md-6">';
						work_exp += '		<label class="form-label"> COMPANY / ORGANIZATION NAME <span class="text-danger">*</span></label>';
						work_exp += '		<input type="text" value = "' + item['WORK_COMPANY_NAME'] + '" class="form-control-plaintext viewing" disabled>';
						work_exp += '	</div>';
						work_exp += '</div>';

						work_exp += '<div class="row mb-3">';
						work_exp += '	<div class="col-md-6">';
						work_exp += '		<label for="work-experience-company-address" class="form-label"> COMPANY ADDRESS <span class="text-danger">*</span></label>';
						work_exp += '		<input type="text" value = "' + item['WORK_COMPANY_ADDRESS'] + '" class="form-control-plaintext viewing" disabled>';
						work_exp += '	</div>';

						work_exp += '	<div class="col-md-3">';
						work_exp += '		<label for="work-experience-start-date" class="form-label"> STARTING DATE OF EMPLOYMENT <span class="text-danger">*</span></label>';
						work_exp += '		<input type="text" value = "' +  item['WORK_EMPLOYED_FROM'] + ' " class="form-control-plaintext viewing"  disabled/>';
						work_exp += '	</div>';

						work_exp += '	<div class="col-md-3">';
						work_exp += '		<label for="work-experience-end-date" class="form-label"> ENDING DATE OF EMPLOYMENT </label>';
						work_exp += '		<input type="text" value = "' +  item['WORK_EMPLOYED_TO'] + ' " class="form-control-plaintext viewing" />';
						work_exp += '	</div>';

						work_exp += '</div>';

						//work_exp++;

					})

					$('#work-experience-container').append(work_exp);

				} else {

					$('#work-experience-container div').remove(); // ****** REMOVE EXISTING DIV FROM ADDING OF DOCUMENT FROM OTHER STUDENT
					$('#work-experience-container br').remove();

					$('#work-experience-container').append('<div><p class="alert alert-warning" style="text-align: center; font-weight: bold;"> ONSITE ENROLEE, NO WORKING EXPERIENCE SUBMITTED </p></div>');

				}
			}

			// ****** FOR DISPLAYING REGISTRATION INFO ****** //

			$("#view_lrn_number").val(admInfo.STUD_LRN);

			$("#view_academiclevelid").val(admInfo.REG_LVLNAME);
			$("#view_academicyearlevelid").val(admInfo.ADM_YRLVLNAME);
			$("#view_academiccourseid").val(admInfo.ADM_CRSESNAME);

			$("#view_admission_type").val(admInfo.ADM_TYPE);

			$("#view_academicperiodid").val(admInfo.ADM_PRDNAME);
			$("#view_academicyearid").val(admInfo.ADM_YRNAME);

			$("#view_student_type").val(admInfo.REG_TYPE);

			
			// ****** FOR DISPLAYING STUDENT INFO ****** // 

			$("#view_firstname").val(admInfo.STUD_FNAME.toUpperCase());
			$("#view_middlename").val(admInfo.STUD_MNAME.toUpperCase());
			$("#view_lastname").val(admInfo.STUD_LNAME.toUpperCase());
			$("#view_suffix").val(admInfo.STUD_SUFFX);
			$("#view_age").val(admInfo.STUD_AGE);
			$("#view_gender").val(admInfo.STUD_GENDR);

			var date = new Date(admInfo.STUD_BDATE).toLocaleDateString();

			$("#view_birthdate").val(date);
			$("#view_birthplace").val(admInfo.STUD_BPLACE);
			$("#view_nationality").val(admInfo.STUD_NATN);
			$("#view_religion").val(admInfo.STUD_RELGN);
			$("#view_mothertongue").val(admInfo.STUD_MTHRTNGE);
			$("#view_civilstatus").val(admInfo.STUD_CVLSTAT);
			$("#view_numberofsiblings").val(admInfo.STUD_SIBNO);

			// ****** FOR DISPLAYING CONTACT INFO ****** // 

			$("#view_mobilenumber").val(admInfo.STUD_MOBNO);
			$("#view_telephone").val(admInfo.STUD_TELNO);
			$("#view_emailaddress").val(admInfo.STUD_EMAIL);

			// ****** FOR DISPLAYING PERMANENT ADDRESS INFO ****** // 
			
			$("#view_permanentstreetaddress").val(admInfo.STUD_PERM_ADD);

			$("#view_permanentprovinceid").val(admInfo.STUD_PERM_PROVNAME);
			$("#view_permanentmunicipalityid").val(admInfo.STUD_PERM_MUNNAME);
			$("#view_permanentbarangayid").val(admInfo.STUD_PERM_BRGYNAME);

			$("#view_permanentzipcode").val(admInfo.STUD_PERM_ZIP);

			// ****** FOR DISPLAYING PRESENT ADDRESS INFO ****** // 
			
			$("#view_presentstreetaddress").val(admInfo.STUD_PRES_ADD);

			$("#view_presentprovinceid").val(admInfo.STUD_PRES_PROVNAME);
			$("#view_presentmunicipalityid").val(admInfo.STUD_PRES_MUNNAME);
			$("#view_presentbarangayid").val(admInfo.STUD_PRES_BRGYNAME);

			$("#view_presentzipcode").val(admInfo.STUD_PRES_ZIP);

			// ****** FOR DISPLAYING LAST SCHOOL INFO ****** // 
			
			$("#view_last_school_sector").val(admInfo.LAST_SCHL_SCTR);
			$("#view_last_school_name").val(admInfo.LAST_SCHL_NAME);
			$("#view_last_school_address").val(admInfo.LAST_SCHL_ADD);
			$("#view_last_school_date").val(admInfo.LAST_SCHL_DATE);
			$("#view_last_school_academiclevel").val(admInfo.LAST_SCHL_EDUC_LVL);
			$("#view_last_school_academicyearlevel").val(admInfo.LAST_SCHL_YR_LEVEL);
			$("#view_last_school_academiccourse").val(admInfo.LAST_SCHL_CRSE);

			// ****** FOR DISPLAYING PARENT INFO ****** // 

			$("#view_father_firstname").val(admInfo.FTHR_FNAME);
			$("#view_father_middlename").val(admInfo.FTHR_MNAME);
			$("#view_father_lastname").val(admInfo.FTHR_LNAME);
			$("#view_father_suffix").val(admInfo.FTHR_SFFX);
			$("#view_father_contact").val(admInfo.FTHR_CONTACT);
			$("#view_father_emailaddress").val(admInfo.FTHR_EMAIL);
			$("#view_father_occupation").val(admInfo.FTHR_OCCUPATION);

			$("#view_mother_firstname").val(admInfo.MTHR_FNAME);
			$("#view_mother_middlename").val(admInfo.MTHR_MNAME);
			$("#view_mother_lastname").val(admInfo.MTHR_LNAME);
			$("#view_mother_suffix").val(admInfo.MTHR_SFFX);
			$("#view_mother_contact").val(admInfo.MTHR_CONTACT);
			$("#view_mother_emailaddress").val(admInfo.MTHR_EMAIL);
			$("#view_mother_occupation").val(admInfo.MTHR_OCCUPATION);

			$("#view_parent_status").val(admInfo.PARENT_STAT);

			$("#view_guardian_firstname").val(admInfo.GRDN_FNAME);
			$("#view_guardian_middlename").val(admInfo.GRDN_MNAME);
			$("#view_guardian_lastname").val(admInfo.GRDN_LNAME);
			$("#view_guardian_suffix").val(admInfo.GRDN_SFFX);
			$("#view_guardian_contact").val(admInfo.GRDN_CONTACT);
			$("#view_guardian_emailaddress").val(admInfo.GRDN_EMAIL);
			$("#view_guardian_occupation").val(admInfo.GRDN_OCCUPATION);
			$("#view_guardian_relationship").val(admInfo.GRDN_RELATIONSHIP);

			// ****** FOR DISPLAYING SPECIAL ARRANGEMENTS AND CONDITIONS // 

			if(admInfo.COMMUTE == 1){
				$('#view_commute').prop('checked', true);
			} else {
				$('#view_commute').prop('checked', false);
			}

			if(admInfo.SHUTTLE == 1){
				$('#view_shuttle').prop('checked', true);
			} else {
				$('#view_shuttle').prop('checked', false);
			}

			if(admInfo.AUTHORIZED_PEOPLE == 1){
				$('#view_authorized_people').prop('checked', true);
			} else {
				$('#view_authorized_people').prop('checked', false);
			}

			if(admInfo.SPECIAL_NURSEMAID == 1){
				$('#view_special_nursemaid').prop('checked', true);
			} else {
				$('#view_special_nursemaid').prop('checked', false);
			}

			if(admInfo.PACKAGE == 1){
				$('#view_package').prop('checked', true);
			} else {
				$('#view_package').prop('checked', false);
			}

			if(admInfo.NOTIFICATIONS == 1)
			{
				$('#view_notifications').prop('checked', true);
			}

			$("#knowledge-about-fcpc :checkbox").prop("checked", false); // UNCHECK ALL PREVIOUSLY SELECT OPTIONS

			// // ****** FOR DISPLAYING KNOWLEDGE ABOUT FCPC INFO ****** // 

			if(admInfo.SPEC_REASON_FCPC != null){
			
				var reasons1 = admInfo.SPEC_REASON_FCPC.split(",");

				reasons1.forEach(function(item) {

					var incStr = item.includes('{|}');

					if(incStr == false){

						$('#' + item + "_reason" ).prop('checked', true);
						hideElementById("view_other_reason");
						$("#view_other_reason").val('');

					} else {

						var other_reason = item.split("{|}");

						$('#' + other_reason[0] + "_reason" ).prop('checked', true);

						showElementById("view_other_reason");

						$("#view_other_reason").val(other_reason[1]);
					}

				})	
			}

			// // ****** FOR DISPLAYING KNOWLEDGE ABOUT FCPC INFO ****** // 
			if(admInfo.SPEC_HOW_KNOW_FCPC != null){
				var reasons2 = admInfo.SPEC_HOW_KNOW_FCPC.split(",");

				reasons2.forEach(function(item) {

					var incStr = item.includes('{|}');

					if(incStr == false){

						$('#' + item + "_reason" ).prop('checked', true);
						hideElementById("view_other_source");
						$("#view_other_source").val('');

					} else {

						var other_reason = item.split("{|}");

						$('#' + other_reason[0] + "_reason" ).prop('checked', true);

						showElementById("view_other_source");
						$("#view_other_source").val(other_reason[1]);

					}

				})
			}

			// // ****** FOR DISPLAYING REGISTRATION REQUIREMENTS (UPLOADED DOCUMENTS) ****** // 

			var uploaded_docu = GET_UPLOADED_REGISTRATION_REQUIREMENTS(admInfo.ENR_REG_ID)

			var div_docu = "";

			$('#registration-requirements div').remove(); // ****** REMOVE EXISTING DIV FROM ADDING OF DOCUMENT FROM OTHER STUDENT
			$('#registration-requirements br').remove();  // ****** REMOVE BR 

			if(admInfo.STUD_REGSITE == 'ONLINE'){

				var req_list = GET_REGISTRATION_REQUIREMENTS() // ***** GET ALL REGISTRATION REQUIREMENTS

				$.each(uploaded_docu, function (i, item) {

					var doc_info = item.document_location.split("_"); // **** SPLIT DOC LOCATION TO ARRAY

					var doc_split  = doc_info[0].split('/'); // ***** SEPARATE REG ID AND REQ NAME

					var req_type = doc_split[1] // ***** GET REQUIREMENT NAME

					var req_label_name = '';

					$.each(req_list, function (i, value) { // ***** FIND REQUIREMENT NAME IN REQ LIST 

						if(req_type == value.req_code ){
							req_label_name = value.req_name; // ***** GET REQ NAME
						}

					});

					div_docu += "<br>";

					div_docu += "<div class='row'>";
					
					div_docu += "	<div class='col-md-5'><label for='view_document' class='form-label viewing'> Document Type : " + req_label_name + "</label></div>";


					if(admInfo.REG_LVLID == 1){ // ##### FOR LOOKING IN WHICH FOLDER TO LOOK 

						var file_levelid = 'basic';
				
					} else if( admInfo.REG_LVLID == 2){
				
						var file_levelid = 'tertiary';
				
					} else if( admInfo.REG_LVLID == 3){
				
						var file_levelid = 'graduate_school';
				
					} else {
				
						var file_levelid = '__'; // ##### DEFAULT FILE FOLDER
					}

					var doc_input = "";


					if(req_type == "url-fb-acc"){

						var url_info = item.document_location.split("__"); // **** SPLIT DOC LOCATION TO ARRAY

						doc_input  = 	"<div class='col-md-7'> " + 
											'<p class="alert alert-warning" style="font-size: inherit;font-weight: bold;"> ' + url_info[1]  + '</p> ' + 
										"</div>";

					} else {

						var registration_loc = 'registration_req/' + file_levelid + '/'  + item.document_location;

						doc_input  = "	<div class='col-md-7'> " + 
											"<a href='../controller/DownloadController.php?id_loc=" + registration_loc + "' target = '_blank'>" + 
												"<label class='btn btn-primary form-control download_link download_link' style = 'font-size: inherit;'>" + 
													"DOWNLOAD DOCUMENT <i class='bx bx-download nav_icon'></i>" + 
												"</label>" + 
											"</a>" + 
										"</div>";

					}

					div_docu += doc_input


					
					div_docu +="</div>";

				})

				$('#registration-requirements').append(div_docu);

			} else {

				$('#registration-requirements div').remove(); // ****** REMOVE EXISTING DIV FROM ADDING OF DOCUMENT FROM OTHER STUDENT
				$('#registration-requirements br').remove();

				$('#registration-requirements').append('<div><p class="alert alert-warning" style="text-align: center; font-weight: bold;"> ONSITE ENROLEE, NO UPLOADED DOCUMENTS </p></div>');

			}



		},
		error: function (request, status, error) {
			console.log(request.responseText);
		},
		complete: function() {
			$("#loadingModal").fadeOut(); // 
			showElementById('student-admission-info')

		},
	})
}

function GET_UPLOADED_REGISTRATION_REQUIREMENTS(reg_id) { // ***** GET ALL UPLOADED REGISTRATION REQUIREMENTS 

	var docu = [];

	$.ajax({
		type: 'GET',
		url: '../controller/EssTeamleaderInfo.php',
		dataType: 'json',
		data:
		{
			type: 'GET_UPLOADED_REGISTRATION_REQUIREMENTS',
			registration_id : reg_id,
		},
		dataType:'json',
		async: false,
		success: function(result)
		{
			docu = result;
		}
	})

	return docu;
}

function GET_WORKING_EXPERIENCE(reg_id, lvlid, yrid, prdid){

	var work_exp = [];

	$.ajax({
		type: 'GET',
		url: '../controller/EssTeamleaderInfo.php',
		dataType: 'json',
		data:
		{
			type: 'GET_WORKING_EXPERIENCE',
			reg_id : reg_id,
			lvlid  : lvlid,
			yrid   : yrid,
			prdid  : prdid
		},
		dataType:'json',
		async: false,
		success: function(result)
		{
			work_exp = result;
		}
	})

	return work_exp;

}

function GET_REGISTRATION_REQUIREMENTS(){ // ***** GET LIST OF REGISTRATION REQUIREMENTS AS ARRAY 

	var reg_list = [];

	$.ajax({
		type:'GET',
		url: '../controller/EssTeamleaderInfo.php',
		data:
		{
			type : 'GET_REGISTRATION_REQUIREMENTS',
		},
		dataType:'json',
		async: false,
		success: function(result)
		{   
			reg_list = result;
		}
	})

	return reg_list;
}