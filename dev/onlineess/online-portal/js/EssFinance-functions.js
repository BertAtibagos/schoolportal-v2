//########################### GET THE NUMBER OF STUDENT FOR FINANCE

function GET_NO_OF_STUDENTS_WITH_PAYMENTS(){

	var with_payments = null;
	
	$.ajax({
		type:'GET',
		url: '../controller/EssFinanceInfo.php',
		data:
		{
			type : 'GET_NO_OF_STUDENTS_WITH_PAYMENT',
		},
		dataType:'json',
		async : false,
		success: function(result)
		{   
			with_payments = result
		}
	})

	return with_payments;

}

function ADD_NO_OF_STUDENTS_WITH_PAYMENTS(){

	var with_payments = GET_NO_OF_STUDENTS_WITH_PAYMENTS();

	$('#finance-notif-badge').text(with_payments);

}


//########################### FOR GETTING STUDENTS WITH PAYMENTS
function GET_REGISTERED_STUDENTS_FINANCE(){

	$("#loadingModal").fadeIn();

	var stud_name 		= $('#name-text-finance').val();
	var stud_name_type 	= $('#name-select-finance').val();
	var lvlid 			= $('#academiclevelid-finance').val();

	$.ajax({
		type:'GET',
		url: '../controller/EssFinanceInfo.php',
		data:
		{
			type 	  		: 'GET_REGISTERED_STUDENTS',
			stud_name 		: stud_name,
			stud_name_type 	: stud_name_type,
			lvlid			: lvlid,
		},
		dataType:'json',
		async: false,
		success: function(result)
		{   

			console.log(result)

			let count = 1;
			var stud_with_pay = "";
			var stud_without_pay = "";
			var enr_stud = "";
			var length = result.length

			var count_without_pay = 0;
			var count_with_pay = 0;
			var count_enrolled = 0;

			var default_tr = "<tr style='text-align: center;''>" +
									"<td colspan='10' style='text-align: center;'>" +
										"<button class='btn-design-danger'> NO RECORD FOUND </button>" +
									"</td>" +
								"</tr>";

			$('#finance-records-without-payment tr').remove();
			$('#finance-records-with-payment tr').remove();
			$('#finance-records-with-enrolled-students tr').remove();

			$('#count_without_payment').text('0');
			$('#count_with_payment').text('0');
			$('#count_enrolled').text('0');


			if( length === 0 ){

				$('#finance-records-without-payment ').append(default_tr);
				$('#finance-records-with-payment ').append(default_tr);
				$('#finance-records-with-enrolled-students ').append(default_tr);
				

			} else {

				$.each(result, function(key, studinfo){

					var studRecords = 	"";


					studRecords +=	"<tr style='text-align: center;'>";

					studRecords +=		"<td>" + count++ 			 + "</td>" + 
										"<td>" + studinfo.REG_DATE	 + "</td>" + 
										"<td>" + studinfo.NAME		 + "</td>" + 
										"<td>" + studinfo.REG_TYPE 	 + "</td>" +
										"<td>" + studinfo.LVL_NAME	 + "</td>" +
										"<td>" + studinfo.YRLVL_NAME + "</td>" + 
										"<td>" + studinfo.PRD_NAME + "</td>" + 
										"<td width='15%'>" + studinfo.CRSE_NAME	 + "</td>";
					studRecords +=		"</td>" + 
										"<td>";
							
											var action_btn = "";
											var verified_statements = '';

											if(studinfo.NO_PAYMENTS == 0){

												verified_statements = "<button class='btn-design-warning'> NO PAYMENT </button>";

											} else {
												verified_statements = "<button class='btn-design-info'> " + studinfo.NO_PAYMENTS + " PAYMENT/S </button>";

												action_btn = "<input type='button' class='btn btn-button-primary' style='font-size: inherit;' id='" + studinfo.REG_ID +  "' name='viewPaymentTrend' value='VIEW PAYMENT TREND'>";

											}
											studRecords += verified_statements 

											
					studRecords +=		"</td>" + 
										"<td>" + action_btn + "</td>" + 
									"</tr>";

					if(studinfo.ASMT_STATUS === '0' ){

						if(studinfo.NO_PAYMENTS === '0'){

							stud_without_pay += studRecords;
							count_without_pay++;
							
						} else {

							stud_with_pay += studRecords;
							count_with_pay++;
							
						}

					} else {

						enr_stud += studRecords;
						count_enrolled++;
					}
				})

				$('#count_without_payment').text(count_without_pay);
				$('#count_with_payment').text(count_with_pay);
				$('#count_enrolled').text(count_enrolled);


				if(enr_stud === ""){ 

					$('#finance-records-with-enrolled-payment').append(default_tr);

				} else { 

					$('#finance-records-with-enrolled-students').append(enr_stud);
				}

				if(stud_with_pay === ""){ 

					$('#finance-records-with-payment').append(default_tr); 

				} else {

					$('#finance-records-with-payment').append(stud_with_pay); 
				}


				if(stud_without_pay === ""){ 

					$('#finance-records-without-payment').append(default_tr);

				 } else { 

					$('#finance-records-without-payment').append(stud_without_pay); 
				}

			}


			
		},		
		error: function(xhr, status, error) {
			// Handle errors
			console.error(xhr.responseText);
		},
		complete: function() {
			// Hide loading animation regardless of success or error
			$("#loadingModal").fadeOut();
		}
	})

	ADD_NO_OF_STUDENTS_WITH_PAYMENTS()
}

//########################### FOR GETTING STUDENT PAYMENT TREND
function GET_STUDENT_PAYMENT_TREND(reg_id){

	$.ajax({
		type:'GET',
		url: '../controller/EssFinanceInfo.php',
		data:
		{
			type : 'GET_PAYMENT_TREND',
			registration_id : reg_id
		},
		dataType:'json',
		success: function(result)
		{

			var trendRecords = "";
			let count = 1;

			if(result.length != 0)
			{	
				$("#view_trend_registration_id").val(result[0]['REG_ID']);
				$("#view_trend_firstname") 		.val(result[0]['FNAME'].toUpperCase());
				$("#view_trend_middlename")		.val(result[0]['MNANE'].toUpperCase());
				$("#view_trend_lastname")  		.val(result[0]['LNAME'].toUpperCase());

				$.each(result, function(key, trendinfo){

					trendRecords +=	"<tr  style='text-align: center;'>";
					trendRecords +=		"<td>" + count++			 + "</td>" + 
										"<td>" + trendinfo.PAY_DATE	 + "</td>" + 
										"<td>" + trendinfo.TR_DATE.toUpperCase()	 + "</td>" + 
										"<td>" + trendinfo.TR_TYPE.toUpperCase() 	 + "</td>" + 
										"<td class=''><span>" + trendinfo.REF_NO.toUpperCase()	 + "</span></td>";

										var status_column = "";
										var status_cancel = "";

										if(parseInt(trendinfo.PAY_ISCANCEL) == 0) { // PAYMENT IS NOT CANCELLED

											if(trendinfo.PAY_STATUS == 0) { // PAYMENT IS NOT VERIFIED

												status_column = "<button class='btn-design-danger'>NOT VERIFIED</button>";
												status_cancel = "<input type='button' id='" + trendinfo.PAY_ID + "' name='payment_cancel' class='btn btn-button-danger' value='CANCEL PAYMENT' style = 'font-size: inherit;'>";

											} else if(trendinfo.PAY_STATUS == 1) { // PAYMENT IS VERIFIED
	
												status_column = "<button class='btn-design-success'> VERIFIED </button>";
											}
	
										} else if(parseInt(trendinfo.PAY_ISCANCEL) == 1) { // PAYMENT IS CANCELLED

											status_column = "<button class='btn-design-danger'>CANCELLED</button>";
										}

					trendRecords +=		"<td>" + status_column + "</td>";
					trendRecords +=		"<td><input type='button' class='btn btn-button-primary' style='font-size: inherit;' id='" + trendinfo.PAY_ID +  "' name='viewPaymentDetails' value='VIEW DETAILS'></td>" ;
					trendRecords +=		"<td>" + status_cancel + "</td>";

					trendRecords += "</tr>" ;

				})

				$('#trend-records tr').remove();
				$('#trend-records').append(trendRecords);

			} else {

				trendRecords += "<tr style='text-align: center;''>" +
									"<td colspan='10' style='text-align: center;'>" +
										"<p class='alert alert-danger'> NO RECORD FOUND </p>" +
									"</td>" +
								"</tr>";

				$('#trend-records tr').remove();
				$('#trend-records').append(trendRecords);
			}

		}
	})

	$('#returnPaymentTrend').attr('name', reg_id);
	hideElementById('finance-dasboard')
	showElementById('payment-trend')
}

//########################### CHECK PAYMENT CANCEL STATUS
function CHECK_IF_PAYMENT_IS_CANCEL(pay_id){

	var status = 0;

	$.ajax({
		type:'POST',
		url: '../controller/EssFinanceInfo.php',
		data:
		{
			type : 'GET_PAYMENT_ISCANCEL',
			pay_id : pay_id,
		},
		dataType:'json',
		success: function(result)
		{
			var paymentInfo = JSON.parse(result)
			status = paymentInfo.isCancel
		}
	})

	return status
}

//########################### FOR GETTING STUDENT PAYMENT DETAILS
function GET_PAYMENT_DETAILS(pay_id){

	var lvlid = $('#academiclevelid-finance').val();

	$.ajax({
		type:'GET',
		url: '../controller/EssFinanceInfo.php',
		data:
		{
			type : 'GET_PAYMENT_DETAILS',
			payment_id : pay_id,
			lvlid : lvlid
		},
		dataType:'json',
		success: function(pay_details)
		{
			$("#view_details_registration_id") .val(pay_details.REG_ID);
			$("#view_details_firstname") .val(pay_details.FNAME.toUpperCase());
			$("#view_details_middlename").val(pay_details.MNANE.toUpperCase());
			$("#view_details_lastname")  .val(pay_details.LNAME.toUpperCase());

			$("#mobilenumber")  .val(pay_details.STUD_MOBNO);
			$("#telephone")  	.val(pay_details.STUD_TELNO);
			$("#emailaddress")  .val(pay_details.STUD_EMAIL.toUpperCase());

			$("#transaction_type")  .val(pay_details.TR_TYPE.toUpperCase());
			$("#amount")  			.val(pay_details.AMNT);
			$("#bank")  			.val(pay_details.BANK.toUpperCase());
			$("#referenceNumber")  	.val(pay_details.REF_NO.toUpperCase());
			$("#transaction_date")  .val(pay_details.TR_DATE);

			var div_docu = "";

			div_docu += "<div class='row'>";

			div_docu += "<br>";
			div_docu += "	<div class='col-md-4'><label for='view_document' class='form-label viewing'> Docuement Type : Payment Receipt </label></div>";

			var doc_info = pay_details.DOC_LOC.split("_"); // **** SPLIT DOC LOCATION TO ARRAY
			
			if(parseInt(doc_info[1]) == 1){ // ##### FOR LOOKING IN WHICH FOLDER TO LOOK 

				var file_levelid = 'basic';
		
			} else if(parseInt(doc_info[1]) == 2){
		
				var file_levelid = 'tertiary';
		
			} else if(parseInt(doc_info[1]) == 3){
		
				var file_levelid = 'graduate_school';
		
			} else {
		
				var file_levelid = '__'; // ##### DEFAULT FILE FOLDER
			}

			var payment_loc = 'payment_doc/' + file_levelid + '/'  + pay_details.DOC_LOC;


			div_docu += "	<div class='col-md-4'> " + 
								"<a href='../controller/DownloadController.php?id_loc=" + payment_loc + "' target = '_blank'>" + 
									"<label class='btn btn-button-primary form-control download_link download_link' style = 'font-size: inherit;'>" + 
										"DOWNLOAD DOCUMENT <i class='bx bx-download nav_icon'></i>" + 
									"</label>" + 
								"</a>" + 
							"</div>";
			div_docu += "</div>";

			$('#payment-documents').append(div_docu);

			if(pay_details.PAY_STATUS == 0){

				$('#btnverifyPayment').attr('name', pay_details.PAY_ID);
				showElementById('btnverifyPayment')

			} else if(pay_details.PAY_STATUS == 1 ){

				hideElementById('btnverifyPayment')
			}
		}
	})
}



