$(document).ready(function (){
	$('#btnAddComp').click(function(){ // add component
		$('#btnAddComp').prop('disabled', true);
		var length = $('.sl').length;
		var i = parseInt(length)+parseInt(1);
		
		$('#div-new-gs-container').append(`
			<table class="table-borderless mb-2" id="table`+i+`">
				<tbody id="dynamicadd`+i+`">
					<tr>
						<td id="gstbl">
							<input type="text" class="form-control form-control-sm sl" name="id[]" id="id" value="`+i+`" readonly="" hidden>
						</td>
						<td id="gstbl">
							<input type="text" name="comp_name[]" id="comp_name`+i+`" class="form-control form-control-sm" placeholder="Component Name">
						</td>
						<td id="gstbl">
							<input type="text" name="comp_code[]" id="comp_code`+i+`" class="form-control form-control-sm" placeholder="Code">
						</td>
						<td id="gstbl">
							<input type="text" name="comp_desc[]" id="comp_desc`+i+`" class="form-control form-control-sm" placeholder="Description">
							<label class="text-primary form-text ms-2"> This field is optional. </label>
						</td>
						<td id="gstbl">
							<input type="text" name="comp_percent[]" id="comp_percent`+i+`" class="form-control form-control-sm" placeholder="Percentage" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/^0[^.]/, '0');" maxlength="6">
						</td>
						<td class="col-md-1">
						</td>
						<td colspan='2' id="gstbl" style="text-align: end">
							<button type="button" id="btnAddSubComp" class="btn btn-sm btn-success btnAddSubComp"><i class="fa fa-plus"></i></button>
							<button type="button" id="`+i+`" class="btn btn-sm btn-warning remove_table"><i class="fa fa-trash-can" style="color: #ffffff;"></i></button>
						</td>
					</tr>
				</tbody>
			</table>
		`);

		var row_id = 'dynamicadd'+i;
		var parentid = '#'+row_id;
		var parentid2 = '#'+row_id+" tr";
		var tblid = row_id.slice(10, 11);
	
		var rowCount = $(parentid2).length;
		$(parentid).append(`
			<tr id="row`+tblid+rowCount+`">
				<td id="gstbl">
				</td>
				<td id="gstbl">
					<input type="text" class="form-control form-control-sm sl2" name="id2`+tblid+`[]" id="id2" value="`+tblid+rowCount+`" readonly="" hidden>
				</td>
				<td id="gstbl">
					<input type="text" name="subcomp_name`+tblid+`[]" id="subcomp_name`+tblid+`" class="form-control form-control-sm sc`+tblid+rowCount+`" placeholder="Subcomponent Name">
				</td>
				<td id="gstbl">
					<input type="text" name="subcomp_code`+tblid+`[]" id="subcomp_code`+tblid+`" class="form-control form-control-sm sc`+tblid+rowCount+`" placeholder="Code">
				</td>
				<td id="gstbl">
					<input type="text" name="subcomp_desc`+tblid+`[]" id="subcomp_desc`+tblid+`" class="form-control form-control-sm sc`+tblid+rowCount+`" placeholder="Description">
					<label class="text-primary form-text ms-2"> This field is optional. </label>
				</td>
				<td id="gstbl">
					<input type="text" name="subcomp_percent`+tblid+`[]" id="subcomp_percent`+rowCount+`" class="form-control form-control-sm sc`+tblid+rowCount+`" placeholder="%" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/^0[^.]/, '0');" maxlength="6">
				</td>
				<td colspan='2' id="gstbl" style="text-align: end">
					<button type="button" id="`+tblid+rowCount+`" class="btn btn-sm btn-danger remove_row"><i class="fa fa-trash-can" style="color: #ffffff;"></i></button>
				</td>
			</tr>
		`);

		$('#btnAddComp').prop('disabled', false);
	});
	
	$('body').on('click', '#btnAddSubComp', function(){// add subcomponent
		$('#btnAddSubComp').prop('disabled', true);
		var row_id = $(this).parents('tbody').attr("id");
	
		var parentid = '#'+row_id;
		var parentid2 = '#'+row_id+" tr";
		var tblid = row_id.slice(10, 11);
	
		var rowCount = $(parentid2).length;
		$(parentid).append(`
			<tr id="row`+tblid+rowCount+`">
				<td id="gstbl">
				</td>
				<td id="gstbl">
					<input type="text" class="form-control form-control-sm sl2" name="id2`+tblid+`[]" id="id2" value="`+tblid+rowCount+`" readonly="" hidden>
				</td>
				<td id="gstbl">
					<input type="text" name="subcomp_name`+tblid+`[]" id="subcomp_name`+tblid+`" class="form-control form-control-sm sc`+tblid+rowCount+`" placeholder="Subcomponent Name">
				</td>
				<td id="gstbl">
					<input type="text" name="subcomp_code`+tblid+`[]" id="subcomp_code`+tblid+`" class="form-control form-control-sm sc`+tblid+rowCount+`" placeholder="Code">
				</td>
				<td id="gstbl">
					<input type="text" name="subcomp_desc`+tblid+`[]" id="subcomp_desc`+tblid+`" class="form-control form-control-sm sc`+tblid+rowCount+`" placeholder="Description">
					<label class="text-primary form-text ms-2"> This field is optional. </label>
				</td>
				<td id="gstbl">
					<input type="text" name="subcomp_percent`+tblid+`[]" id="subcomp_percent`+rowCount+`" class="form-control form-control-sm sc`+tblid+rowCount+`" placeholder="%" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/^0[^.]/, '0');" maxlength="6">
				</td>
				<td colspan='2' id="gstbl" style="text-align: end">
					<button type="button" id="`+tblid+rowCount+`" class="btn btn-sm btn-danger remove_row"><i class="fa fa-trash-can" style="color: #ffffff;"></i></button>
				</td>
			</tr>
		`);
	
		$('#btnAddSubComp').prop('disabled', false);
	});
	
	$('body').on('click','.remove_table',function() { //remove component
		var tbl_id = $(this).attr("id");
		$('#table'+tbl_id).remove();
	});
	
	$('body').on('click','.remove_row',function() { //remove subcomponent
		var row_id = $(this).attr("id");
		$('#row'+row_id).remove();
	});
	
	var sc = '';
	$('#btnSubmitGS').click(function(){
		$('#btnSubmitGS').prop('disabled', true);
	
		var id = document.getElementsByName('id[]').length; //get only length
	
		for(let i = 1; i<=id; i++){
			var str = 'id2'+i+'[]';
			var id2 = document.getElementsByName(str).length; //get only length
			for(let j = 1; j<=id2; j++){
				var str1 = '.sc'+i+j;
				$(str1).each(function(){
					sc =  sc + this.value + ",";
					return;
				});
				sc = sc + id2;
				sc = sc + '|';
			}
			sc = sc + ':';
		}
	
		var gscale_code = $('#gscale_code').val();
		var gscale_name = $('#gscale_name').val();
		var details = $('#details').val();
		var pass_score = $('#pass_score').val();
	
		var levelid = $('#gs-acadlvl').val();
		var periodid = $('#gs-acadprd').val();
		var yearid = $('#gs-acadyr').val();
		var courseid = $('#gs-acadcrse').val();
		var deptid = $('#gs-acadcrse').find('option:selected').attr("name");
	
		var comp_percent = $("input[name='comp_percent[]']").map(function(){return $(this).val();}).get();//get values of array
		var id2 = document.getElementsByName('id2[]').length; //get only length
	
		comp_percent_sum = 0;
		$.each(comp_percent,function(){comp_percent_sum+=parseFloat(this) || 0;});
	
	
		if(gscale_name !== '' && gscale_code !== '' 
			// && details !==''
		){
			$("input[id='pass_score']").css('border-color', '#1fc779');
			$("input[id='pass_score']").css('box-shadow', '0 0 0 0.125rem rgb(13 253 100 / 25%)');
	
	
			if(pass_score > 0 && pass_score <= 100){
	
				if(comp_percent_sum > 0 && comp_percent_sum == 100){
	
					$("input[name='comp_percent[]']").css('border-color', '#1fc779');
					$("input[name='comp_percent[]']").css('box-shadow', '0 0 0 0.125rem rgb(13 253 100 / 25%)');
	
					var comp_name = $("input[name='comp_name[]']").map(function(){return $(this).val();}).get();//get values of array
					var comp_code = $("input[name='comp_code[]']").map(function(){return $(this).val();}).get();//get values of array
					var comp_desc = $("input[name='comp_desc[]']").map(function(){return $(this).val();}).get();//get values of array
					var num = 0;
					var num2 = 0;
	
					for(let j=0; j<id; j++){
						if(comp_name[j] !== '' && comp_code[j] !== ''
						// && comp_desc[j] !== ''
						){
							num2 = num2 + 1;
						} else {
							a = j + 1;
							sc = '';
							$("input[id='comp_name"+a+"']").css('border-color', '#fe8686');
							$("input[id='comp_name"+a+"']").css('box-shadow', '0 0 0 0.125rem rgb(253 13 13 / 25%)');
							$("input[id='comp_code"+a+"']").css('border-color', '#fe8686');
							$("input[id='comp_code"+a+"']").css('box-shadow', '0 0 0 0.125rem rgb(253 13 13 / 25%)');
							// $("input[id='comp_desc"+a+"']").css('border-color', '#fe8686');
							// $("input[id='comp_desc"+a+"']").css('box-shadow', '0 0 0 0.125rem rgb(253 13 13 / 25%)');
						}
					}
	
					for(let i=1; i<=id; i++){
						subcomp_percent_sum = 0;
						var subcomp_percent = $("input[name='subcomp_percent"+i+"[]']").map(function(){return $(this).val();}).get();//get values of array
						$.each(subcomp_percent,function(){subcomp_percent_sum+=parseFloat(this) || 0;});
	
						var comp_percent2 = $("input[id='comp_percent"+i+"']").val();
	
						if(subcomp_percent_sum == comp_percent2){
							$("input[name='subcomp_percent"+i+"[]']").css('border-color', '#1fc779');
							$("input[name='subcomp_percent"+i+"[]']").css('box-shadow', '0 0 0 0.125rem rgb(13 253 100 / 25%)');
	
							var subcomp_code = $("input[name='subcomp_code"+i+"[]']").map(function(){return $(this).val();}).get();//get values of array
							var subcomp_name = $("input[name='subcomp_name"+i+"[]']").map(function(){return $(this).val();}).get();//get values of array
							var subcomp_desc = $("input[name='subcomp_desc"+i+"[]']").map(function(){return $(this).val();}).get();//get values of array
	
							for(let j=0; j<subcomp_percent.length; j++){
								if(subcomp_code[j] !== '' && subcomp_name[j] !== ''
								// && subcomp_desc[j] !== ''
								){
									num = num + 1;
								} else {	
									sc = '';
									$("input[name='subcomp_name"+i+"[]']").css('border-color', '#fe8686');
									$("input[name='subcomp_name"+i+"[]']").css('box-shadow', '0 0 0 0.125rem rgb(253 13 13 / 25%)');
									$("input[name='subcomp_code"+i+"[]']").css('border-color', '#fe8686');
									$("input[name='subcomp_code"+i+"[]']").css('box-shadow', '0 0 0 0.125rem rgb(253 13 13 / 25%)');
									// $("input[name='subcomp_desc"+i+"[]']").css('border-color', '#fe8686');
									// $("input[name='subcomp_desc"+i+"[]']").css('box-shadow', '0 0 0 0.125rem rgb(253 13 13 / 25%)');
								}
							}
						} else {
							sc = '';
							alert('ERROR: Total Subcomponent Percentage #'+i+' should be equal to Component Percentage #'+i);
							$("input[name='subcomp_percent"+i+"[]']").css('border-color', '#fe8686');
							$("input[name='subcomp_percent"+i+"[]']").css('box-shadow', '0 0 0 0.125rem rgb(253 13 13 / 25%)');
						}
					}
	
					var idd = $("input[id='id2']").map(function(){return $(this).val();}).get();//get values of array
	
					if(num == idd.length){
						gscale_code = gscale_code.toString().toUpperCase();
						gscale_name = gscale_name.toString().toUpperCase();
						details = details.toString().toUpperCase();
						sc = sc.toString().toUpperCase();

						comp_name = comp_name.map(function(element) {return element.toUpperCase();});
						comp_code = comp_code.map(function(element) {return element.toUpperCase();});
						comp_desc = comp_desc.map(function(element) {return element.toUpperCase();});

						$.ajax({
							type: 'POST',
							url: '../class/grading-scale/gradingscale-create-controller.php',
							data: {
								gscale_code : gscale_code,
								gscale_name : gscale_name,
								details : details,
								pass_score : pass_score,
								levelid : levelid,
								yearid : yearid,
								periodid : periodid,
								courseid : courseid,
								id : id,
								deptid : deptid,
								comp_name : comp_name,
								comp_code : comp_code,
								comp_desc : comp_desc,
								comp_percent : comp_percent,
								sc : sc
							},
							success: function(data){
								sc="";
								if(!data.length){
									console.log(data);
									$(".remove_table").click();
									$('#gscale_code').val('');
									$('#gscale_name').val('');
									$('#details').val('');
									$('#pass_score').val('');
									$('#btnCreateGS').click()
									MyGradeScaleDisplay();
								} else {
									alert('UPLOAD ERROR: CONTACT FCPC ICT DEPARTMENT.')
									window.location.reload();
								}
							}
						});
					} else {
						sc = '';
					}
				} else {
					sc = '';
					alert('ERROR: Total Component Percentage should be = 100');
					$("input[name='comp_percent[]']").css('border-color', '#fe8686');
					$("input[name='comp_percent[]']").css('box-shadow', '0 0 0 0.125rem rgb(253 13 13 / 25%)');
				}
			} else {
				sc = '';
				alert('ERROR: Passing Score should be within 1 - 100 range');
				$("input[id='pass_score']").css('border-color', '#fe8686');
				$("input[id='pass_score']").css('box-shadow', '0 0 0 0.125rem rgb(253 13 13 / 25%)');
			}
		} else {
			sc = '';
			alert('There are BLANK INPUT/s!');
			$("input[id='gscale_name']").css('border-color', '#fe8686');
			$("input[id='gscale_name']").css('box-shadow', '0 0 0 0.125rem rgb(253 13 13 / 25%)');
			$("input[id='gscale_code']").css('border-color', '#fe8686');
			$("input[id='gscale_code']").css('box-shadow', '0 0 0 0.125rem rgb(253 13 13 / 25%)');
			// $("input[id='details']").css('border-color', '#fe8686');
			// $("input[id='details']").css('box-shadow', '0 0 0 0.125rem rgb(253 13 13 / 25%)');
		}
	
		$('#btnSubmitGS').prop('disabled', false);
	});
	
	$('#btnSubmitTag').click(function(){
		$('#btnSubmitTag').prop('disabled', true);
		var subjid = $("input[name='subjid[]']:checked").map(function(){return $(this).val();}).get();//get values of array
	
		var gscaleid = $('#td-crse-sec-sched2').text();
		
		if(subjid.length){
			$.ajax({
				type: 'POST',
				url: '../class/grading-scale/gradingscale-tagging-controller.php',
				data: {
					subjid : subjid,
					gscaleid : gscaleid
				},
				success: function(data){
					sc="";
					$('#div-offered-subject-list').fadeOut();
					// MyGradeScaleDisplay();
					
					$("#div-message").html("Subject Tagging Successful!");
					$("#div-message").fadeIn();
					$("#div-message").delay(1500).fadeOut();
				}
			});

		} else {
			$('#div-offered-subject-list').fadeOut();
		}
	
		$('#btnSubmitTag').prop('disabled', false);
	});
	
	$('.btnSaveChanges').click(function() {
		const req2save = confirm('Do you want to SAVE this Edited Grading Scale?');
		if(req2save) {
			var gscale_id = $('#gs-id').val();
			var gscale_code = $('#gs-code').val();
			var gscale_name = $('#gs-name').val();
			var gscale_desc = $('#gs-desc').val();
			var pass_score = $('#gs-pass-score').val();
	
			var levelid = $('#gs-acadlvl').val();
			var periodid = $('#gs-acadprd').val();
			var deptid = $('#gs-acaddept').val();
			var yearid = $('#gs-acadyr').val();
			var courseid = $('#gs-acadcrse').val();
	
			var modal_comp_name = $("input[name='modal_comp_name[]']").map(function(){return $(this).val();}).get();//get values of array
			var modal_comp_code = $("input[name='modal_comp_code[]']").map(function(){return $(this).val();}).get();//get values of array
			var modal_comp_desc = $("input[name='modal_comp_desc[]']").map(function(){return $(this).val();}).get();//get values of array
	
			var modal_comp_percent = $("input[name='modal_comp_percent[]']").map(function(){return $(this).val();}).get();//get values of array
			modal_comp_percent_sum = 0;
			$.each(modal_comp_percent,function(){modal_comp_percent_sum+=parseFloat(this) || 0;});
	
			var id = document.getElementsByName('scid[]').length; //get only length
	
			for(let i = 1; i<=modal_comp_percent.length; i++){
				var str = 'scid'+i+'[]';
				var id2 = document.getElementsByName(str).length; //get only length
				for(let j = 1; j<=id2; j++){
					var str1 = '.scomp'+i+j;
					$(str1).each(function(){
						sc =  sc + this.value + ",";
						return;
					});
					sc = sc + id2;
					sc = sc + '|';
				}
				sc = sc + ':';
			}
	
			var countrow = 0;
			var rowCount = $('#table-gscale tr[name="comp_row[]"]').length;

			if(pass_score > 0 && pass_score <= 100) {
				if(gscale_name !== '' && gscale_code !== '' 
				// && gscale_desc !==''
				){
					if(modal_comp_percent_sum == 100){
						for(let i=0; i<modal_comp_percent.length; i++){
							var tblid = i + 1;
							modal_subcomp_percent_sum = 0;
							modal_subcomp_percent = $("input[name='modal_subcomp_percent"+tblid+"[]']").map(function(){return $(this).val();}).get();//get values of array
							$.each(modal_subcomp_percent,function(){modal_subcomp_percent_sum+=parseFloat(this) || 0;});
	
							var modal_subcomp_name = $("input[name='modal_subcomp_name"+tblid+"[]']").map(function(){return $(this).val();}).get();//get values of array
							var modal_subcomp_code = $("input[name='modal_subcomp_code"+tblid+"[]']").map(function(){return $(this).val();}).get();//get values of array
							var modal_subcomp_desc = $("input[name='modal_subcomp_desc"+tblid+"[]']").map(function(){return $(this).val();}).get();//get values of array
	
							if(modal_subcomp_percent_sum == modal_comp_percent[i]){
								if(modal_comp_name[i] !== '' && modal_comp_code[i] !== ''
								//  && modal_comp_desc[i] !== ''
								 ){
									countrow = countrow + 1;
									for(let j=0; j<modal_subcomp_percent.length; j++){
										if(modal_subcomp_name[j] !== '' && modal_subcomp_code[j] !== ''
										//  && modal_subcomp_desc[j] !== ''
										 ){
											countrow = countrow + 1;
										} else {
											sc="";
											a = i + 1;
											alert('There are Blank Subcomponent Inputs');
											$("input[name='modal_subcomp_name"+a+"[]']").css('border-color', '#fe8686');
											$("input[name='modal_subcomp_name"+a+"[]']").css('box-shadow', '0 0 0 0.125rem rgb(253 13 13 / 25%)');
											$("input[name='modal_subcomp_code"+a+"[]']").css('border-color', '#fe8686');
											$("input[name='modal_subcomp_code"+a+"[]']").css('box-shadow', '0 0 0 0.125rem rgb(253 13 13 / 25%)');
											$("input[name='modal_subcomp_desc"+a+"[]']").css('border-color', '#fe8686');
											$("input[name='modal_subcomp_desc"+a+"[]']").css('box-shadow', '0 0 0 0.125rem rgb(253 13 13 / 25%)');
										}
									}
								} else {
									sc="";
									a = i + 1;
									alert('There are Blank Component Inputs');
									$("input[id='modal_comp_name"+a+"']").css('border-color', '#fe8686');
									$("input[id='modal_comp_name"+a+"']").css('box-shadow', '0 0 0 0.125rem rgb(253 13 13 / 25%)');
									$("input[id='modal_comp_code"+a+"']").css('border-color', '#fe8686');
									$("input[id='modal_comp_code"+a+"']").css('box-shadow', '0 0 0 0.125rem rgb(253 13 13 / 25%)');
									$("input[id='modal_comp_desc"+a+"']").css('border-color', '#fe8686');
									$("input[id='modal_comp_desc"+a+"']").css('box-shadow', '0 0 0 0.125rem rgb(253 13 13 / 25%)');
								}
							} else {
								sc="";
								a = i + 1;
								alert('ERROR: Total Subcomponent Percentage #'+a+' should be equal to Component Percentage #'+a);
								$("input[name='modal_subcomp_percent"+a+"[]']").css('border-color', '#fe8686');
								$("input[name='modal_subcomp_percent"+a+"[]']").css('box-shadow', '0 0 0 0.125rem rgb(253 13 13 / 25%)');
							}
						}
					} else {
						sc="";
						alert('ERROR: Total Component Percentage should be = 100');
						$("input[name='modal_comp_percent[]']").css('border-color', '#fe8686');
						$("input[name='modal_comp_percent[]']").css('box-shadow', '0 0 0 0.125rem rgb(253 13 13 / 25%)');
					}
	
					if(countrow == rowCount){
						gscale_code = gscale_code.toString().toUpperCase();
						gscale_name = gscale_name.toString().toUpperCase();
						gscale_desc = gscale_desc.toString().toUpperCase();
						sc = sc.toString().toUpperCase();

						modal_comp_name = modal_comp_name.map(function(element) {return element.toUpperCase();});
						modal_comp_code = modal_comp_code.map(function(element) {return element.toUpperCase();});
						modal_comp_desc = modal_comp_desc.map(function(element) {return element.toUpperCase();});

						$.ajax({
							type: 'POST',
							url: '../class/grading-scale/gradingscale-modal-update-controller.php',
							data: {
								gscale_id : gscale_id,
								gscale_code : gscale_code,
								gscale_name : gscale_name,
								gscale_desc : gscale_desc,
								pass_score : pass_score,
								levelid : levelid,
								yearid : yearid,
								periodid : periodid,
								courseid : courseid,
								modal_comp_name : modal_comp_name,
								modal_comp_code : modal_comp_code,
								modal_comp_desc : modal_comp_desc,
								modal_comp_percent : modal_comp_percent,
								sc : sc
							},
							success: function(data){
								$('.btnSaveChanges').prop('disabled', false);
								sc="";
								window.location.reload();
							}
						});
					} else {
						sc = '';
					}
				} else {
					sc = '';
					alert('There are BLANK GRADING SCALE DETAILS!');
					$("input[id='gs-name']").css('border-color', '#fe8686');
					$("input[id='gs-name']").css('box-shadow', '0 0 0 0.125rem rgb(253 13 13 / 25%)');
					$("input[id='gs-code']").css('border-color', '#fe8686');
					$("input[id='gs-code']").css('box-shadow', '0 0 0 0.125rem rgb(253 13 13 / 25%)');
					$("input[id='gs-desc']").css('border-color', '#fe8686');
					$("input[id='gs-desc']").css('box-shadow', '0 0 0 0.125rem rgb(253 13 13 / 25%)');
				}
			} else {
				sc = '';
				alert('ERROR: Passing Score should be within 1 - 100 range');
				$("input[id='gs-pass-score']").css('border-color', '#fe8686');
				$("input[id='gs-pass-score']").css('box-shadow', '0 0 0 0.125rem rgb(253 13 13 / 25%)');
			}
		} else {
			sc = '';
		}
	});

});