$(document).ready(function() {

	//########################### TO UPPER CASE ALL INPUT TYPE TEXT //
	$('input[type="text"]').keyup(function(){
		$(this).val($(this).val().toUpperCase());
	});

    $('#name-text-finance').keypress(function(event){
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if(keycode == '13'){
		   
		   $("#search_button_finance").click(); //alert('You pressed a "enter" key in textbox, here submit your form'); 
		}
	});

    $('#name-select-finance').keypress(function(event){
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if(keycode == '13'){
		   
		   $("#search_button_finance").click(); //alert('You pressed a "enter" key in textbox, here submit your form'); 
		}
	});

    $('#academiclevelid-finance').keypress(function(event){
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if(keycode == '13'){
		   
		   $("#search_button_finance").click(); //alert('You pressed a "enter" key in textbox, here submit your form'); 
		}
	});


	//################# GET ASSESSED STUDENTS BASED ON SELECTED FILTERS	
	$("#search_button_finance").click(function(e){

		GET_REGISTERED_STUDENTS_FINANCE()	
	})

	// ################# VIEW STUDENT PAYMENT TREND  //
	$(document).on('click', 'input[name="viewPaymentTrend"]', function(){

		var reg_id = this.id
        GET_STUDENT_PAYMENT_TREND(reg_id)
	})

	// ################# FOR CANCELLING PAYMENT AND UPDATE PAYMENT TREND VIEW   //
	$(document).on('click', 'input[name="payment_cancel"]', function(){

		var reg_id = $('#view_trend_registration_id').val();

		var pay_id = this.id;

		var payment_status = CHECK_IF_PAYMENT_IS_CANCEL(pay_id)

		if(payment_status == 0){
			
			$.ajax({
				type:'POST',
				url: '../controller/EssFinancePost.php',
				data:
				{
					type : 'POST_PAYMENT_CANCEL',
					payment_id : pay_id,
				},
				dataType:'json',
				success: function(result)
				{
        			GET_STUDENT_PAYMENT_TREND(reg_id)

				}
			})

		} else {

			alert('PAYMENT IS ALREADY CANCELLED.')

		}	
	})

	// ################# FOR UNCANCELLING PAYMENT AND UPDATE PAYMENT TREND VIEW   //
	$(document).on('click', 'input[name="payment_uncancel"]', function(){

		var reg_id = $('#view_trend_registration_id').val();

		var pay_id = this.id;

		var payment_status = CHECK_IF_PAYMENT_IS_CANCEL(pay_id)

		if(payment_status == 0){
			
			$.ajax({
				type:'POST',
				url: '../controller/EssFinancePost.php',
				data:
				{
					type : 'POST_PAYMENT_UNCANCEL',
					payment_id : pay_id,
				},
				dataType:'json',
				success: function(result)
				{
        			GET_STUDENT_PAYMENT_TREND(reg_id)
				}
			})

		} else {

			alert('PAYMENT IS ALREADY CANCELLED.')

		}	
	})

	// ################# FROM PAYMENT TREND SHOW DASHBOARD //
	$("#returnFinanceDashboard").click(function(e){

		GET_REGISTERED_STUDENTS_FINANCE() // SHOW UPDATED ASSESSED STUDENTS

		// *~~~ REMOVE VALUES BEFORE HIDING DIV //

		$("#view_trend_firstname") .val("");
		$("#view_trend_middlename").val("");
		$("#view_trend_lastname")  .val("");


		hideElementById('payment-trend')
		showElementById('finance-dasboard')
	})

	// ################# FOR VIEWING STUDENT PAYMENT DETAILS  //

	$(document).on('click', 'input[name="viewPaymentDetails"]', function(){
		
		var pay_id = this.id

		GET_PAYMENT_DETAILS(pay_id)

        hideElementById('payment-trend')
        showElementById('payment-details')
	})

	$("#returnPaymentTrend").click(function(e){

		// ################# UPDATE PAYMENT TREND BASED ON ANY CHANGES IN PAYMENT DETAILS //

		$('#trend-records tr').remove();

		GET_STUDENT_PAYMENT_TREND(this.name)

		$('#payment-documents div').remove();  

		hideElementById('payment-details')
        showElementById('payment-trend')

	})

	$("#btnverifyPayment").click(function(e){

		var pay_id = this.name
		
		$.ajax({
			type:'POST',
			url: '../controller/EssFinancePost.php',
			data:
			{
				type : 'POST_VERIFY_PAYMENT',
				pay_id : pay_id,
			},
			dataType:'json',
			success: function(result){

				if(result.status == 1){

					alert(result.message)
					hideElementById('btnverifyPayment')

				} else {

					alert(result.message)
					showElementById('btnverifyPayment')
				}

			},
			error: function (request, status, error) {

				console.log(request.responseText);
			},
		})


	})


})