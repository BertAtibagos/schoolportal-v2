$(document).ready(function(){
	$('#subjidall').click(function () {  
		$('input:checkbox').not(":disabled").prop('checked', this.checked);    
	});

    $('#btnCreateGS').click(function(){
		$("#div-create-gs").toggle();
		$('#div-offered-subject-list').hide();
	});

	$('#gs-acadlvl').change(function(){
		$("#div-create-gs").hide();
		$('#div-offered-subject-list').hide();
		// $('#master-modal').modal('show');

		var lvlid = $(this).val();
		$.ajax({
			type: 'POST',
			url: '../../model/forms/academic/gradingscale/gradingscale-controller.php',
			data:{
				type : 'ACADYEAR',
				levelid : lvlid
			},
			success: function(result){
				MyDropdown(result, "#gs-acadyr");
				
				var yrid = $('#gs-acadyr').val();
				$.ajax({
					type:'POST',
					url: '../../model/forms/academic/gradingscale/gradingscale-controller.php',
					data:{
						type : 'ACADPERIOD',
						levelid : lvlid,
						yearid: yrid
					},
					success: function(result){
						MyDropdown(result, "#gs-acadprd");

						var prdid = $('#gs-acadprd').val();
						$.ajax({
							type:'POST',
							url: '../../model/forms/academic/gradingscale/gradingscale-controller.php',
							data:{
								type : 'ACADCOURSE',
								levelid : lvlid,
								yearid: yrid,
								periodid: prdid
							},
							success: function(result){
								var ret = JSON.parse(result);
								if(ret.length) {
									var aCourse = '';
									$.each(ret, function(key, value) {
										aCourse += "<option value='" + value.ID + "' name='" + value.DEPT_ID + "'>" + value.NAME + "</option>";
									});
								} else {
									aCourse = "<option value='0'>NONE</option>";
								}
								$('#gs-acadcrse').html(aCourse);

								MyGradeScaleDisplay();
							},
							error:function(status){
								$('#div-message').html('Error!');
							}
						});
					},
					error:function(status){
						$('#div-message').html('Error!');
					}
				});
			},
			error:function(status){
				$('#div-message').html('Error!');
			}
		});
	});

	$('#gs-acadyr').change(function(){
		$("#div-create-gs").hide();
		$('#div-offered-subject-list').hide();
		// $('#master-modal').modal('show');

		var lvlid = $('#gs-acadlvl').val();
		var yrid = $(this).val();
		$.ajax({
			type:'POST',
			url: '../../model/forms/academic/gradingscale/gradingscale-controller.php',
			data:{
				type : 'ACADPERIOD',
				levelid : lvlid,
				yearid: yrid
			},
			success: function(result){
				MyDropdown(result, "#gs-acadprd");
				
				var prdid = $('#gs-acadprd').val();
				$.ajax({
					type:'POST',
					url: '../../model/forms/academic/gradingscale/gradingscale-controller.php',
					data:{
						type : 'ACADCOURSE',
						levelid : lvlid,
						yearid: yrid,
						periodid: prdid
					},
					success: function(result){
						var ret = JSON.parse(result);
						if(ret.length) {
							var aCourse = '';
							$.each(ret, function(key, value) {
								aCourse += "<option value='" + value.ID + "' name='" + value.DEPT_ID + "'>" + value.NAME + "</option>";
							});
						} else {
							aCourse = "<option value='0'>NONE</option>";
						}
						$('#gs-acadcrse').html(aCourse);

						MyGradeScaleDisplay();
					},
					error:function(status){
						$('#div-message').html('Error!');
					}
				});
			},
			error:function(status){
				$('#div-message').html('Error!');
			}
		});
	});

	$('#gs-acadprd').change(function(){
		$("#div-create-gs").hide();
		$('#div-offered-subject-list').hide();
		// $('#master-modal').modal('show');

		var lvlid = $('#gs-acadlvl').val();
		var yrid = $('#gs-acadyr').val();
		var prdid = $(this).val();
		$.ajax({
			type:'POST',
			url: '../../model/forms/academic/gradingscale/gradingscale-controller.php',
			data:{
				type : 'ACADCOURSE',
				levelid : lvlid,
				yearid: yrid,
				periodid: prdid
			},
			success: function(result){
				var ret = JSON.parse(result);
				if(ret.length) {
					var aCourse = '';
					$.each(ret, function(key, value) {
						aCourse += "<option value='" + value.ID + "' name='" + value.DEPT_ID + "'>" + value.NAME + "</option>";
					});
				} else {
					aCourse = "<option value='0'>NONE</option>";
				}
				$('#gs-acadcrse').html(aCourse);

				MyGradeScaleDisplay();
			},
			error:function(status){
				$('#div-message').html('Error!');
			}
		});
	});

	$('#gs-acadcrse').change(function(){
		$("#div-create-gs").hide();
		$('#div-offered-subject-list').hide();
		// $('#master-modal').modal('show');

		MyGradeScaleDisplay();
	});

	// $('#master-modal').modal('show');
	$('#borger').click();

	$.ajax({
		type:'POST',
		url: '../../model/forms/academic/gradingscale/gradingscale-controller.php',
		data:{
			type : 'ACADLEVEL'
		},
		success: function(result){
			MyDropdown(result, "#gs-acadlvl");
			
			//Academic year Dropdown
			var lvlid = $('#gs-acadlvl').val();
			$.ajax({
				type:'POST',
				url: '../../model/forms/academic/gradingscale/gradingscale-controller.php',
				data:{
					type : 'ACADYEAR',
					levelid : lvlid
				},
				success: function(result){
					MyDropdown(result, "#gs-acadyr");

					//Academic period Dropdown
					var yrid = $('#gs-acadyr').val();
					$.ajax({
						type:'POST',
						url: '../../model/forms/academic/gradingscale/gradingscale-controller.php',
						data:{
							type : 'ACADPERIOD',
							levelid : lvlid,
							yearid: yrid
						},
						success: function(result){
							MyDropdown(result, "#gs-acadprd");

							//Academic course Dropdown
							var prdid = $('#gs-acadprd').val();
							$.ajax({
								type:'POST',
								url: '../../model/forms/academic/gradingscale/gradingscale-controller.php',
								data:{
									type : 'ACADCOURSE',
									levelid : lvlid,
									yearid: yrid,
									periodid: prdid
								},
								success: function(result){
									var ret = JSON.parse(result);
									if(ret.length) {
										var aCourse = '';
										$.each(ret, function(key, value) {
											aCourse += "<option value='" + value.ID + "' name='" + value.DEPT_ID + "'>" + value.NAME + "</option>";
										});
									} else {
										aCourse = "<option value='0'>NONE</option>";
									}
									$('#gs-acadcrse').html(aCourse);
									
									MyGradeScaleDisplay();
								},
								error:function(status){
									$('#div-message').html('Error!');
								}
							});
						},
						error:function(status){
							$('#div-message').html('Error!');
						}
					});
				},
				error:function(status){
					$('#div-message').html('Error!');
				}
			});
		},
		complete: function(status) {
			//Fires event once process is ompleted
		},
		error:function(status){
			$('#div-message').html('Error!');
		}
	});
});

function MyDropdown(result, id){
    var ret = JSON.parse(result);
    if(ret.length) {
        var opt = '';
        $.each(ret, function(key, value) {
            opt += "<option value='" + value.ID + "'>" + value.NAME + "</option>";
        });
    } else {
        opt = "<option value='0'>NONE</option>";
    }
    $(id).html(opt);
}

function MyGradeScaleDisplay(){
	var lvlid = $('#gs-acadlvl').val();
	var yrid = $('#gs-acadyr').val();
	var prdid = $('#gs-acadprd').val();
	var crseid = $('#gs-acadcrse').val();
	let lineNo = 1;
	$('#tbody-subject tr').remove();
	$.ajax({
		type:'POST',
		url: '../../model/forms/academic/gradingscale/gradingscale-controller.php',
		data:{
			type : 'OFFERED_SUBJECT',
			levelid : lvlid,
			yearid: yrid,
			periodid: prdid,
			courseid:crseid
		},
		beforeSend: function (status) {
			$(document.body).css({ 'cursor': 'wait' });
		},
		success: function(result){
			$(document.body).css({ 'cursor': 'auto' });
			var ret = JSON.parse(result);
			if(ret.length) {
				var tblOffSubject = '';
				$.each(ret, function(key, value) {
					tblOffSubject += "<tr>" + 
					"<td class='text-center'>" + lineNo++ + "</td>" + 
					"<td>" + value.CODE + "</td>" + 
					"<td>" + value.NAME + "</td>" + 
					"<td>" + value.DESC + "</td>" + 
					"<td hidden>" + value.PASS_SCORE + "</td>" + 			  
					"<td>" +
                        "<input type='button' id='"  + value.GS_ID + "' name='"  + value.GS_ID + "' class='btn btn-sm btn-primary me-2 mb-2 btnTag'" + "value='Tag'/>" +
                        "<input type='button' id='"  + value.GS_ID + "' name='"  + value.GS_ID + "' class='btn btn-sm btn-outline-primary me-2 mb-2 btnModalView' data-bs-toggle='modal' data-bs-target='#viewModal'" + "value='View'/>" +
                        "<input type='button' id='"  + value.GS_ID + "' name='"  + value.GS_ID + "' class='btn btn-sm btn-outline-secondary mb-2 btnModalEdit' data-bs-toggle='modal' data-bs-target='#editModal'"+ "value='Edit'/>" +
					"</td>" + 										  
					"</tr>";
				});
			} else {
				tblOffSubject = "<tr><td colspan='99' class='text-center'><span class='text-danger fw-medium'> No Record Found </span></td></tr>";
			}
			$('#tbody-grading-scale tr').remove();
			$('#tbody-grading-scale').html(tblOffSubject);
		},
		complete: function(status) {
			$(document.body).css({ 'cursor': 'auto' });

			$('.btnTag').click(function() {
				$('#div-create-gs').hide();
				$('#div-offered-subject-list').show();

				var lvlid = $('#gs-acadlvl').val();
				var yrid = $('#gs-acadyr').val();
				var prdid = $('#gs-acadprd').val();
				//var crseid = $('#gs-acadcrse').val();

				var currentRow=$(this).closest("tr");
				var code=currentRow.find("td:eq(1)").html();
				var name=currentRow.find("td:eq(2)").html();
				var desc=currentRow.find("td:eq(3)").html();
				var pass_score=currentRow.find("td:eq(4)").html();
				var sched=$(this).attr('id');
				
				$('#td-subj').html(code);
				$('#td-crse-sec-sched').html(name);
				$('#td-crse-sec-sched1').html(desc);
				$('#td-crse-sec-sched2').html(sched);
				$('#td-crse-sec-sched3').html(pass_score);

				let rowNo = 1;
				$.ajax({
					type:'POST',
					url: '../../model/forms/academic/gradingscale/gradingscale-controller.php',
					data:{
						type : 'STUDENT_LIST',
						levelid : lvlid,
						yearid: yrid,
						periodid: prdid,
						courseid: crseid
					},
					beforeSend: function (status) {
						$(document.body).css({ 'cursor': 'wait' });
						$('.btnTag').prop('disabled', true);
					},
					success: function(result){
						$(document.body).css({ 'cursor': 'auto' });
						var ret = JSON.parse(result);
						if(ret.length) {
							var tblstudent = '';
							$.each(ret, function(key, value) {
								tblstudent += "<tr>" + 
									"<td> <input type='checkbox' name='subjid[]' value='"+ value.ID +"'> </td>" + 
									"<td class='text-center'>" + rowNo++ + "</td>" + 
									"<td>" + value.CODE + "</td>" + 
									"<td>" + value.DESC + "</td>" + 
									"<td class='text-center'>" + value.UNIT + "</td>" + 
									"<td>" + value.SEC + "</td>" + 
									"<td class='text-center'>" + value.STUDENT_COUNT + "</td>" + 
									"<td>" + value.SCHED + "</td>" + 
									"<td id='"+ value.PROF +"'>" + value.NAME + "</td>" + 
									"</tr>";
							});
						} else {
							tblstudent = "<tr><td colspan='99' class='text-center'><span class='text-danger fw-medium'> No Record Found </span></td></tr>";
						}
						$('#tbody-subject tr').remove();
						$('#tbody-subject').html(tblstudent);
						$('.btnTag').prop('disabled', false);
						$('#tbl-header-subject-list').show();
						$.ajax({
							type:'POST',
							url: '../../model/forms/academic/gradingscale/gradingscale-controller.php',
							data:{
								type : 'TAGGED_SUBJ',
								levelid : lvlid,
								yearid: yrid,
								periodid: prdid,
								courseid: crseid,
								gscaleid: sched
							},
							success: function(result){
								var ret_tag = JSON.parse(result);

								if(ret.length > ret_tag.length){
									$.each(ret, function(key, val) {
										$.each(ret_tag, function(key, value) {
											if(val.ID === value.SUBOFF_ID){
												aa = "input[value='"+val.ID+"']";
												$(aa).prop( "checked", true );
											} 
										});
									});
								} else {
									$.each(ret_tag, function(key, value) {
										$.each(ret, function(key, val) {
											if(val.ID === value.SUBOFF_ID){
												aa = "input[value='"+val.ID+"']";
												$(aa).prop( "checked", true );
											} 
										});
									});
								}

								$.ajax({
									type:'POST',
									url: '../../model/forms/academic/gradingscale/gradingscale-controller.php',
									data:{
										type : 'ENCODED_SUBJ',
										levelid : lvlid,
										yearid: yrid,
										periodid: prdid,
										courseid: crseid,
										gscaleid: sched
									},
									success: function(result){
										var ret_encode = JSON.parse(result);
										$.each(ret_encode, function(key, val) {
												if(val.COUNT > 0){
													aa = "input[value='"+val.SUBOFF_ID+"']";
													$(aa).prop( "disabled", true );
													$(aa).prop( "readonly", true );
													// $(aa).prop( "checked", false );
												} 
										});
									},
									complete: function(status) {
										$(document.body).css({ 'cursor': 'auto' });
										$('.btnTag').prop('disabled', false);
										$('#tbl-header-subject-list').show();
										
									},
									error:function(status){
										$('#div-message').html('Error!');
										$(document.body).css({ 'cursor': 'auto' });
										$('.btnTag').prop('disabled', false);
										$('#tbl-header-subject-list').hide();
									}
								});
							},
							complete: function(status) {
								$(document.body).css({ 'cursor': 'auto' });
								$('.btnTag').prop('disabled', false);
								$('#tbl-header-subject-list').show();
								
							},
							error:function(status){
								$('#div-message').html('Error!');
								$(document.body).css({ 'cursor': 'auto' });
								$('.btnTag').prop('disabled', false);
								$('#tbl-header-subject-list').hide();
							}
						});

					},
					complete: function(status) {
						$(document.body).css({ 'cursor': 'auto' });
						$('.btnTag').prop('disabled', false);
						$('#tbl-header-subject-list').show();
						
					},
					error:function(status){
						$('#div-message').html('Error!');
						$(document.body).css({ 'cursor': 'auto' });
						$('.btnTag').prop('disabled', false);
						$('#tbl-header-subject-list').hide();
					}
				})
			});
			
			$('.btnModalEdit').click(function() {

				var lvlid = $('#gs-acadlvl').val();
				var yrid = $('#gs-acadyr').val();
				var prdid = $('#gs-acadprd').val();
				var crseid = $('#gs-acadcrse').val();

				var gsid=$(this).attr('id');

				$.ajax({
					type:'POST',
					url: '../../model/forms/academic/gradingscale/gradingscale-controller.php',
					data:{
						type : 'GSCALE_DISPLAY',
						levelid : lvlid,
						yearid: yrid,
						periodid: prdid,
						courseid: crseid,
						gscaleid: gsid
					},
					beforeSend: function (status) {
						$(document.body).css({ 'cursor': 'wait' });
						$('.btnModalEdit').prop('disabled', true);
					},
					success: function(result){
						$(document.body).css({ 'cursor': 'auto' });

						var ret = JSON.parse(result);
						if(ret.length) {
							var tblgscale = '';
							var tblid = 1;
							var rowid = 1;
							$.each(ret, function(key, value) {

								$('#gs-name').val(value.NAME);
								$('#gs-id').val(value.ID);
								$('#gs-code').val(value.CODE);
								$('#gs-desc').val(value.DESC);
								$('#gs-pass-score').val(value.PASS_SCORE);
								if(value.PARENT_ID == 0){
									tblgscale +=
									"<tr>" +
										"<td colspan='10'>" +
											"<hr class='mt-0'>" +
										"</td>" +
									"</tr>" +
									"<tr name='comp_row[]'>" + 
										"<td hidden>" + 
											"<input type='text' value='"+value.COMP_ID+"' readonly>" + 
										"</td>" +
										"<td hidden>" + 
											"<input type='text' value='"+value.PARENT_ID+"' readonly>" + 
										"</td>" +
										"<td> "+
											"<input type='text' name='modal_comp_name[]' id='modal_comp_name"+tblid+"' class='form-control form-control-sm editcomp"+tblid+" edit' value='"+value.COMP_NAME+"'>" + 
										"</td>" +
										"<td> "+
											"<input type='text' name='modal_comp_code[]' id='modal_comp_code"+tblid+"' class='form-control form-control-sm editcomp"+tblid+" edit' value='"+value.COMP_CODE+"'>" + 
										"</td>" +
										"<td> "+
											"<input type='text' name='modal_comp_desc[]' id='modal_comp_desc"+tblid+"' class='form-control form-control-sm editcomp"+tblid+" edit' value='"+value.COMP_DESC+"'>" + 
											"<label class='text-primary form-text ms-2'> This field is optional. </label>" +
										"</td>" +
										"<td> "+
											"<input type='text' name='modal_comp_percent[]' class='form-control form-control-sm editcomp"+tblid+" edit' value='"+value.COMP_PERCENT+"'>" + 
										"</td>" +
									"</tr>";
									tblid = tblid + 1;
									rowid = 1;
								} else if(value.PARENT_ID != 0) {
									tblid2 = tblid - 1;
									tblgscale += 
									"<tr name='comp_row[]'>" + 
										"<td hidden>" + 
											"<input type='text' value='"+value.COMP_ID+"' readonly>" + 
										"</td>" +
										"<td hidden>" + 
											"<input type='text' value='"+value.PARENT_ID+"' class='scomp"+tblid2+rowid+"'readonly>" + 
										"</td>" +
										"<td colspan='1'>" + 
											"<input type='text' value='"+rowid+"' name='scid"+tblid2+"[]' readonly hidden>" + 
										"</td>" +
										"<td>" + 
											"<input type='text' name='modal_subcomp_name"+tblid2+"[]' class='form-control form-control-sm editcomp"+tblid2+" edit scomp"+tblid2+rowid+"' value='"+value.COMP_NAME+"'>" + 
										"</td>" +
										"<td>" + 
											"<input type='text' name='modal_subcomp_code"+tblid2+"[]' class='form-control form-control-sm editcomp"+tblid2+" edit scomp"+tblid2+rowid+"' value='"+value.COMP_CODE+"'>" + 
										"</td>" +
										"<td>" + 
											"<input type='text' name='modal_subcomp_desc"+tblid2+"[]' class='form-control form-control-sm editcomp"+tblid2+" edit scomp"+tblid2+rowid+"' value='"+value.COMP_DESC+"'>" + 
											"<label class='text-primary form-text ms-2'> This field is optional. </label>" +
										"</td>" +
										"<td>" + 
											"<input type='text' name='modal_subcomp_percent"+tblid2+"[]' class='form-control form-control-sm editcomp"+tblid2+" edit scomp"+tblid2+rowid+"' value='"+value.COMP_PERCENT+"'>" + 
										"</td>" +
									"</tr>";
									rowid = rowid + 1;
								};
							});
						} else {
							tblgscale = "<tr>" +
											"<td colspan='9' " + 
												"style='font-size: 18px;" + 
												"font-family: Roboto, sans-serif;" + 
												"font-weight: normal;" + 
												"text-decoration: none;" + 
												"color: red;'>" +
											"No Record Found" +
											"</td>" +
											"</tr>";
						}
						$('#tbody-gscale tr').remove();
						$('#tbody-gscale').html(tblgscale);

						$('.btnModalEdit').prop('disabled', false);
						
					},
					complete: function(status) {
						$(document.body).css({ 'cursor': 'auto' });
						$('.btnTag').prop('disabled', false);
						$('#tbl-header-subject-list').show();
						
					},
					error:function(status){
						$('#div-message').html('Error!');
						$(document.body).css({ 'cursor': 'auto' });
						$('.btnTag').prop('disabled', false);
						$('#tbl-header-subject-list').hide();
					}
				})

			});

			$('.btnModalView').click(function() {

				var lvlid = $('#gs-acadlvl').val();
				var yrid = $('#gs-acadyr').val();
				var prdid = $('#gs-acadprd').val();
				var crseid = $('#gs-acadcrse').val();

				var gsid=$(this).attr('id');

				$.ajax({
					type:'POST',
					url: '../../model/forms/academic/gradingscale/gradingscale-controller.php',
					data:{
						type : 'GSCALE_DISPLAY',
						levelid : lvlid,
						yearid: yrid,
						periodid: prdid,
						courseid: crseid,
						gscaleid: gsid
					},
					beforeSend: function (status) {
						$(document.body).css({ 'cursor': 'wait' });
						$('.btnModalEdit').prop('disabled', true);
					},
					success: function(result){
						$(document.body).css({ 'cursor': 'auto' });

						var ret = JSON.parse(result);
						if(ret.length) {
							var tblgsview = '';
							$.each(ret, function(key, value) {

								$("p[name='gsview-name']").text(value.NAME);
								$("p[name='gsview-pass-score']").text('('+value.PASS_SCORE+'%)');
								if(value.PARENT_ID == 0){
									tblgsview += "<tr><td colspan='99'><div class='mt-4 fs-5 fw-medium'>"+value.COMP_NAME+" ("+value.COMP_PERCENT+"%)</div></td></tr>";
								} else if(value.PARENT_ID != 0) {
									tblgsview += "<tr><td colspan='99'><span class='ps-4'>"+value.COMP_NAME+" ("+value.COMP_PERCENT+"%)</span></td></tr>";
								};
							});
						} else {
							tblgsview = "<tr><td colspan='99'><span class='text-danger text-center'> No Record Found </span></td></tr>";
						}
						$('#tbody-gsview tr').remove();
						$('#tbody-gsview').html(tblgsview);

						$('.btnModalEdit').prop('disabled', false);
						
					},
					complete: function(status) {
						$(document.body).css({ 'cursor': 'auto' });
						$('.btnTag').prop('disabled', false);
						$('#tbl-header-subject-list').show();
						
					},
					error:function(status){
						$('#div-message').html('Error!');
						$(document.body).css({ 'cursor': 'auto' });
						$('.btnTag').prop('disabled', false);
						$('#tbl-header-subject-list').hide();
					}
				})

			});

		},
		error:function(status){
			$('#div-message').html('Error!');
			$(document.body).css({ 'cursor': 'auto' });
		}
	});
}