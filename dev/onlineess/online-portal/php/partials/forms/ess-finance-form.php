
<style type="text/css">
	#loading_payment {
		display: none;
		position: fixed;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
		background-color: rgba(255, 255, 255, 0.8);
		padding: 20px;
		border-radius: 10px;
		box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
		text-align: center;
	}
</style>

<div id="ess-finance-dashboard">
	
	<div class="container-fluid">
		<div class="card card-outline card-warning">
			<div class="card-body">
				<hr>
				<div align="center" class="header_div">
					<h2> ESS FINANCE DASHBOARD </h2>
				</div><hr>
			</div>
		</div>
	</div>

	<div class="container-fluid">
		<div class="card card-outline card-primary">
			<div class="card-body">

				<div id='finance-dasboard' class="container-fluid" style="overflow: auto;">
					<hr><br>
					<div align="center" class="headline">
						<h2> ENROLLMENT ( ONLINE PAYMENT SECTION ) </h2>
					</div><br><hr>

					<div class="row mb-3">
						<div class="col-md-3">
							<input type="text" id="name-text-finance" name="name-text-finance" placeholder="SEARCH ... "
								class="form-control " maxlength="50">
						</div>

						<div class="col-md-3" >
							<select	id="name-select-finance" name="name-select-finance" class="form-control " required>
								<option value="last_name"> LAST NAME </option>
								<option value="first_name"> FIRST NAME </option>
								<option value="middle_name"> MIDDLE NAME </option>

							</select>
						</div>

						<div class="col-md-3 academiclevel" id="academiclevel" >
							<select id="academiclevelid-finance" name="academiclevelid-finance" class="form-control academiclevelid">
								<option value="1"> BASIC EDUCATION </option>
								<option value="2"> TERTIARY </option>
								<option value="3"> GRADUATE SCHOOL </option>
							</select>	
						</div>

						<div class="col-md-3">
							<input type='button' class='btn btn-button-success' id="search_button_finance" name="search_button_finance" style='font-size: inherit;' value='SEARCH STUDENT/S'>
						</div>
					</div>

					<br><hr><br>

					<div align="center" class="headline">
						<h5> LIST OF REGISTERED STUDENTS </h5>
					</div><br><hr><br>

					<div id="loading_payment">
						<img src="../../image/Ripple-1s-287px.gif" alt="Loading ... ">
					</div>

					<div class="layout">

						<!-- TAB FOR STUDENTS WITHOUT PAYMENTS -->
						<input name="nav" type="radio" class="without-payment-radio" id="without-payment" />
						
						<div class="page without-payment-page">
							<div class="page-contents"><br>

								<button class='btn-design-warning'> <h4> LIST OF STUDENTS WITHOUT PAYMENT </h4> </button><br><br>

								<table id='finance-table-without-payment' class='paleBlueRows' style="font-size:12px;text-align: center; overflow: auto;"  width="100%">
									<thead>
										<tr>		
											<th scope='col' style='text-align:center;'>#</th>	 
											<th scope='col' style='text-align:center;'>REGISTRATION DATE</th>	 
											<th scope='col' style='text-align:center;'>STUDENT NAME</th>		
											<th scope='col' style='text-align:center;'>STUDENT TYPE</th>		
											<th scope='col' style='text-align:center;'>ACADEMIC LEVEL</th>	 
											<th scope='col' style='text-align:center;'>GRADE/YEAR LEVEL</th>
											<th scope='col' style='text-align:center;'>YEAR PERIOD</th>
											<th scope='col' style='text-align:center;'>STRAND/PROGRAM</th>	 
											<th scope='col' style='text-align:center;'>NUMBER OF PAYMENT/S</th>	
											<th scope='col' style='text-align:center;' colspan=2 >ACTION</th>	
										</tr>	 
									</thead>
									<tbody id='finance-records-without-payment' style="text-align: center;">
										<tr style="text-align: center;">
											<td colspan="10" style="text-align: center;">
												<button class='btn-design-danger'> NO RECORD FOUND </button> 
											</td>
										</tr>
									</tbody>
									<tfoot>
										<tr>		
											<th scope='col' style='text-align:center;'>#</th>	 
											<th scope='col' style='text-align:center;'>REGISTRATION DATE</th>	 
											<th scope='col' style='text-align:center;'>STUDENT NAME</th>		
											<th scope='col' style='text-align:center;'>STUDENT TYPE</th>		
											<th scope='col' style='text-align:center;'>ACADEMIC LEVEL</th>	 
											<th scope='col' style='text-align:center;'>GRADE/YEAR LEVEL</th>
											<th scope='col' style='text-align:center;'>YEAR PERIOD</th>
											<th scope='col' style='text-align:center;'>STRAND/PROGRAM</th>	 
											<th scope='col' style='text-align:center;'>NUMBER OF PAYMENT/S</th>	
											<th scope='col' style='text-align:center;'>ACTION</th>	
										</tr>	 
									</tfoot>

								</table>
							</div>
						</div>

						<label class="nav" for="without-payment">
							<span>
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
									<path fill="currentColor" d="M3 4.27L4.28 3L21 19.72L19.73 21l-3.67-3.67c-.62.67-1.52 1.22-2.56 1.49V21h-3v-2.18C8.47 18.31 7 16.79 7 15h2c0 1.08 1.37 2 3 2c1.13 0 2.14-.44 2.65-1.08l-2.97-2.97C9.58 12.42 7 11.75 7 9c0-.23 0-.45.07-.66L3 4.27m7.5.91V3h3v2.18C15.53 5.69 17 7.21 17 9h-2c0-1.08-1.37-2-3-2c-.37 0-.72.05-1.05.13L9.4 5.58l1.1-.4Z"/></svg>
								LIST OF STUDENTS WITHOUT PAYMENTS 
							</span>

							<p id="count_without_payment" style=" color: #fff; font-size: 20px; background-color: darkred; border-radius: 5px; padding: 3px; "> 0 </p>

							
						</label>

						<!-- TAB FOR STUDENTS WITH PAYMENTS -->
						<input name="nav" type="radio" class="nav with-payment-radio" id="with-payment" checked="checked"  />
						<div class="page with-payment-page">
							<div class="page-contents"><br>
							<button class='btn-design-info'> <h4> LIST OF STUDENTS WITH PAYMENT </h4> </button><br><br>

							<table id='finance-table-with-payment' class='paleBlueRows' style="font-size:12px;text-align: center; overflow: auto;"  width="100%">
								<thead>
									<tr>		
										<th scope='col' style='text-align:center;'>#</th>	 
										<th scope='col' style='text-align:center;'>REGISTRATION DATE</th>	 
										<th scope='col' style='text-align:center;'>STUDENT NAME</th>		
										<th scope='col' style='text-align:center;'>STUDENT TYPE</th>		
										<th scope='col' style='text-align:center;'>ACADEMIC LEVEL</th>	 
										<th scope='col' style='text-align:center;'>GRADE/YEAR LEVEL</th>
										<th scope='col' style='text-align:center;'>YEAR PERIOD</th>
										<th scope='col' style='text-align:center;'>STRAND/PROGRAM</th>	 
										<th scope='col' style='text-align:center;'>NUMBER OF PAYMENT/S</th>	
										<th scope='col' style='text-align:center;' colspan=2 >ACTION</th>	
									</tr>	 
								</thead>
								<tbody id='finance-records-with-payment' style="text-align: center;">
									<tr style="text-align: center;">
										<td colspan="10" style="text-align: center;">
											<button class='btn-design-danger'> NO RECORD FOUND </button> 
										</td>
									</tr>
								</tbody>
								<tfoot>
									<tr>		
										<th scope='col' style='text-align:center;'>#</th>	 
										<th scope='col' style='text-align:center;'>REGISTRATION DATE</th>	 
										<th scope='col' style='text-align:center;'>STUDENT NAME</th>		
										<th scope='col' style='text-align:center;'>STUDENT TYPE</th>		
										<th scope='col' style='text-align:center;'>ACADEMIC LEVEL</th>	 
										<th scope='col' style='text-align:center;'>GRADE/YEAR LEVEL</th>
										<th scope='col' style='text-align:center;'>YEAR PERIOD</th>
										<th scope='col' style='text-align:center;'>STRAND/PROGRAM</th>	 
										<th scope='col' style='text-align:center;'>NUMBER OF PAYMENT/S</th>	
										<th scope='col' style='text-align:center;' colspan=2 >ACTION</th>	
									</tr>	 
								</tfoot>

							</table>
							</div>
						</div>

						<label class="nav" for="with-payment">
							<span>
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M3 6h18v12H3V6m9 3a3 3 0 0 1 3 3a3 3 0 0 1-3 3a3 3 0 0 1-3-3a3 3 0 0 1 3-3M7 8a2 2 0 0 1-2 2v4a2 2 0 0 1 2 2h10a2 2 0 0 1 2-2v-4a2 2 0 0 1-2-2H7Z"/></svg>
								LIST OF STUDENTS WITH PAYMENTS
							</span>

							<p id="count_with_payment" style=" color: #fff; font-size: 20px; background-color: darkred; border-radius: 5px; padding: 3px; "> 0 </p>

						</label>

						<!-- TAB FOR STUDENTS THAT IS ENROLLED -->
						<input name="nav" type="radio" class="enrolled-radio" id="enrolled" />
						<div class="page enrolled-page">
							<div class="page-contents"><br>
							<button class='btn-design-success'> <h4> LIST OF ENROLLED STUDENTS </h4> </button><br><br>

							<table id='finance-table-with-enrolled' class='paleBlueRows' style="font-size:12px;text-align: center; overflow: auto;"  width="100%">
								<thead>
									<tr>		
										<th scope='col' style='text-align:center;'>#</th>	 
										<th scope='col' style='text-align:center;'>REGISTRATION DATE</th>	 
										<th scope='col' style='text-align:center;'>STUDENT NAME</th>		
										<th scope='col' style='text-align:center;'>STUDENT TYPE</th>		
										<th scope='col' style='text-align:center;'>ACADEMIC LEVEL</th>	 
										<th scope='col' style='text-align:center;'>GRADE/YEAR LEVEL</th>
										<th scope='col' style='text-align:center;'>YEAR PERIOD</th>
										<th scope='col' style='text-align:center;'>STRAND/PROGRAM</th>	 
										<th scope='col' style='text-align:center;'>NUMBER OF PAYMENT/S</th>	
										<th scope='col' style='text-align:center;' colspan=2 >ACTION</th>	
									</tr>	 
								</thead>
								<tbody id='finance-records-with-enrolled-students' style="text-align: center;">
									<tr style="text-align: center;">
										<td colspan="10" style="text-align: center;">
											<button class='btn-design-danger'> NO RECORD FOUND </button> 
										</td>
									</tr>
								</tbody>
								<tfoot>
									<tr>		
										<th scope='col' style='text-align:center;'>#</th>	 
										<th scope='col' style='text-align:center;'>REGISTRATION DATE</th>	 
										<th scope='col' style='text-align:center;'>STUDENT NAME</th>		
										<th scope='col' style='text-align:center;'>STUDENT TYPE</th>		
										<th scope='col' style='text-align:center;'>ACADEMIC LEVEL</th>	 
										<th scope='col' style='text-align:center;'>GRADE/YEAR LEVEL</th>
										<th scope='col' style='text-align:center;'>YEAR PERIOD</th>
										<th scope='col' style='text-align:center;'>STRAND/PROGRAM</th>	 
										<th scope='col' style='text-align:center;'>NUMBER OF PAYMENT/S</th>	
										<th scope='col' style='text-align:center;' colspan=2 >ACTION</th>	
									</tr>	 
								</tfoot>

							</table>
							</div>
						</div>

						<label class="nav" for="enrolled">
							<span>
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M19 17v2H7v-2s0-4 6-4s6 4 6 4m-3-9a3 3 0 1 0-3 3a3 3 0 0 0 3-3m3.2 5.06A5.6 5.6 0 0 1 21 17v2h3v-2s0-3.45-4.8-3.94M18 5a2.91 2.91 0 0 0-.89.14a5 5 0 0 1 0 5.72A2.91 2.91 0 0 0 18 11a3 3 0 0 0 0-6M7.34 8.92l1.16 1.41l-4.75 4.75l-2.75-3l1.16-1.16l1.59 1.58l3.59-3.58"/></svg>
								LIST OF ENROLLED STUDENTS
							</span>

							<p id="count_enrolled" style=" color: #fff; font-size: 20px; background-color: darkred; border-radius: 5px; padding: 3px; "> 0 </p>

							
						</label>
					</div>
				</div>
			</div>
		

			<div id="payment-trend" class="container-fluid" style="overflow: auto;" hidden>

				<div align="center" class="headline">
					<h2>STUDENT INFORMATION</h2>
				</div> <br><hr><br>

				<div class="row">
					<div class="col-md-3"><label class="form-label" for="view_trend_registration_id">REGISTRATION ID <span class="text-danger">*</span></label>
						<input readonly type="text" id="view_trend_registration_id" name="view_trend_registration_id" placeholder="REGISTRATION ID" class="form-control-plaintext viewing" maxlength="40">
					</div>
					

					<div class="col-md-3"><label class="form-label" for="view_trend_firstname">FIRST NAME <span class="text-danger">*</span></label>
						<input readonly type="text" id="view_trend_firstname" name="view_trend_firstname" placeholder="FIRST NAME" class="form-control-plaintext viewing" maxlength="40">
					</div>
					
					<div class="col-md-3"><label class="form-label" for="view_trend_middlename">MIDDLE NAME <span class="text-danger">*</span></label>
						<input readonly type="text" id="view_trend_middlename" name="view_trend_middlename" placeholder="MIDDLE NAME" class="form-control-plaintext viewing" maxlength="40">
					</div>

					<div class="col-md-3">
						<label class="form-label" for="view_trend_lastname">LAST NAME <span class="text-danger">*</span></label>
							<input readonly type="text" id="view_trend_lastname" name="view_trend_lastname" placeholder="LAST NAME" class="form-control-plaintext viewing" maxlength="40">
					</div>
				</div><br><hr><br>

				<div align="center" class="headline">
						<h2>PAYMENT TREND</h2>
				</div><br><hr><br>

				<table id='paymentTrend-table' class='paleBlueRows' style="font-size:12px;text-align: center;" width="100%">
					<thead>
						<tr>
							<th scope='col'>#</th>
							<th scope='col'>DATE</th>
							<th scope='col'>TRANSACTION DATE</th>
							<th scope='col'>TRANSACTION TYPE</th>
							<th scope='col'>TRANSACTION NUMBER</th>
							<th scope='col'>PAYMENT STATUS</th>
							<th colspan=3>
								<center>ACTIONS</center>
							</th>
						</tr>
					</thead>
					<tbody id='trend-records' style="text-align: center;">
						<tr>
							<td colspan="10">
								<input type='button' class='btn btn-outline-danger' style='font-size: inherit; pointer-events: none;' value='NO RECORD FOUND'>
							</td>
						</tr>
					</tbody>
					<tfoot>
						<tr>
							<th scope='col'>#</th>
							<th scope='col'>DATE</th>
							<th scope='col'>TRANSACTION DATE</th>
							<th scope='col'>TRANSACTION TYPE</th>
							<th scope='col'>AMOUNT RENDERED</th>
							<th scope='col'>PAYMENT STATUS</th>
							<th colspan=3>
								<center>ACTIONS</center>
							</th>
						</tr>
					</tfoot>
				</table> 
				<br><br>
				<center>
					<button type="button" id="returnFinanceDashboard" style='font-size: 30;' class='btn btn-secondary'>GO BACK TO BASHBOARD</button>
				</center><br><br>

			</div>

			<div id="payment-details" class="container-fluid" style="overflow: auto;" hidden>

				<div align="center" class="headline">
					<h2>STUDENT INFORMATION</h2>
				</div> <br><hr><br>

				<div class="row">

					<div class="col-md-3"><label class="form-label" for="view_details_registration_id">REGISTRATION ID <span class="text-danger">*</span></label>
						<input readonly type="text" id="view_details_registration_id" name="view_details_registration_id" placeholder="REGISTRATION ID" class="form-control-plaintext viewing" maxlength="40">
					</div>

					<div class="col-md-3"><label class="form-label" for="view_details_firstname">FIRST NAME <span class="text-danger">*</span></label>
						<input readonly type="text" id="view_details_firstname" name="view_details_firstname" placeholder="FIRST NAME" class="form-control-plaintext viewing" maxlength="40">
					</div>
					
					<div class="col-md-3"><label class="form-label" for="view_details_middlename">MIDDLE NAME <span class="text-danger">*</span></label>
						<input readonly type="text" id="view_details_middlename" name="view_details_middlename" placeholder="MIDDLE NAME" class="form-control-plaintext viewing" maxlength="40">
					</div>

					<div class="col-md-3"><label class="form-label" for="view_details_lastname">LAST NAME <span class="text-danger">*</span></label>
						<input readonly type="text" id="view_details_lastname" name="view_details_lastname" placeholder="LAST NAME" class="form-control-plaintext viewing" maxlength="40">
					</div>

				</div><br><hr><br>

				<div align="center" class="headline">
					<h2>PAYMENT DETAILS</h2>
				</div> <br><hr><br>

				<div class="row">
					<div class="col-md-4">
						<label>MOBILE NUMBER <span class="text-danger">*</span></label>
						<input type="text" id="mobilenumber" name="mobilenumber" placeholder="MOBILE NUMBER" class="form-control-plaintext viewing"
							maxlength="40" readonly>
					</div>
					<div class="col-md-4">
						<label>TELEPHONE NUMBER <span class="text-danger">*</span></label>
						<input type="text" id="telephone" name="telephone" placeholder="TELEPHONE NUMBER" class="form-control-plaintext viewing"
							maxlength="40" readonly>
					</div>
					<div class="col-md-4">
						<label>EMAIL ADDRESS <span class="text-danger">*</span></label>
						<input type="text" id="emailaddress" name="emailaddress" placeholder="EMAIL ADDRESS" value=""
							class="form-control-plaintext viewing" maxlength="40" readonly>
					</div>
				</div><br>

				<!-- PAYMENT INFORMATION -->

				<div class="row mb-3">
					<div class="col-md-4">
						<label for="transaction" class="form-label">TRANSACTION TYPE <span class="text-danger">*</span></label>
						<input type="text" id="transaction_type" name="transaction_type" placeholder="TRANSACTION TYPE"
							class="form-control-plaintext viewing" readonly>
					</div>
					<div class="col-md-4 amount bank">
						<label class="form-label" for="amount">AMOUNT PAID <span class="text-danger">*</span></label>
						<input type="text" id="amount" name="amount" placeholder="AMOUNT PAID" class="form-control-plaintext viewing bank amount"
							maxlength="40" readonly>
					</div>
					<div class="col-md-4 bank">
						<label for="bank" class="form-label">BANK NAME <span class="text-danger">*</span></label>
						<input type="text" id="bank" name="bank" placeholder="BANK NAME"
							class="form-control-plaintext viewing bank" maxlength="40" readonly>
					</div>
				</div><br>

				<div class="row mb-3">
					<div class="col-md-4 bank">
						<label class="form-label" for="referenceNumber">TRANSACTION REFERENCE NUMBER <span class="text-danger">*</span></label>
						<input type="text" id="referenceNumber" name="referenceNumber" placeholder="TRANSACTION REFERENCE NUMBER"
							class="form-control-plaintext viewing bank" maxlength="40" readonly>
					</div>
					<div class="col-md-4 bank">
						<label class="form-label" for="date">TRANSACTION DATE <span class="text-danger">*</span></label>
						<input type="text" id="transaction_date" name="transaction_date" placeholder="TRANSACTION DATE"
							class="form-control-plaintext viewing bank" readonly>
					</div>
				</div>
				<br><hr><br>

				<!-- PAYMENT DOCUMENT -->

				<div align="center" class="heading">
					<h2>PAYMENT DOCUMENTS </h2>
				</div><br><hr><br>

				<div class="row mb-3">
					<div class="col-md-12">
						<p style='font-size: 18; font-style: italic; font-weight: bold;' class="mt-3">
							<span class="text-danger">* Note : Below is/are document/s uploaded by the student </span>
						</p>
					</div>
				</div>

				<div id="payment-documents" center>

				</div>

				<br><br>

				<div class="div_view_payment" id="div_view_payment" hidden>
					<div class="container">
						<div align="center" style="background-color: lightblue;">
							<h2>VIEW PAYMENT DOCUMENT</h2>
						</div>
						<input type="button" id='close_viewDocument' class="btn btn-danger" style="font-size: 15px;" value="CLOSE"/><br>

						<div class="payment_div" id="payment_div"><br>
										
						</div>
					</div>
				</div>
				
				<br><br><hr>
				<center>

					<input type="button" id="btnverifyPayment" value="VERIFY PAYMENT" class="p-2 m-2 bg-primary text-white shadow rounded-2" style="font-size: 25px;" hidden>

					<button	class="p-2 m-2 bg-secondary text-white shadow rounded-2"	id="returnPaymentTrend" style="font-size: 25px; text-decoration: none;"> GO BACK TO PAYMENT TREND	</button>

				</center>
			</div>

		</div>
	</div>
	<br><br><br><br><br>

	<?php
		echo '<script src="../../js/EssFinance-functions.js"></script>';
		echo '<script src="../../js/EssFinance-script.js"></script>';
	?>

</section>