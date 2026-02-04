//$(document).ready(function(cboacadlvl, cboacadyr, cboacadprd, cboacadcrse){
$(document).ready(
	function()
	{
		var global_SUBJOFFID;
		var global_BTSTUDENTID;
		var global_BTSTUDENTNAME;
		var global_TTLNOOFENROLLEDSTUDENT;
		var global_TDCOLSTATUS;
		var global_TDPROCESS;
		var global_CLSTUDID;
		var global_STUDASSID;
		//var global_ACADEXAMPRD;
		var global_ACADLVL = 0;
		var global_ACADYR = 0;
		var global_ACADPRD = 0;
		var global_ACADCRSE = 0;
		
		const divstudent = $('#div-student');
		const btnsearch = $('#btnsearch');
		//let btnviewstudent = $('.btnviewstudent');
		const btnback = $('#btnBack');
		const cboacadlvl = $('#cbo-acadlvl');
		const cboacadyr = $('#cbo-acadyr');
		const cboacadprd = $('#cbo-acadprd');
		const cboacadcrse = $('#cbo-acadcrse');
		const tbodystudent = $('#tbody-student');
		const theadstudent = $('#thead-student');
		const tbodyofferedsubject = $('#tbody-offered-subject');
		const divmessage = $('#div-message');
		
		//const btnviewstudent = $('.btnviewstudent');
		$('#p-title').html('LIST OF ENROLLED STUDENT EXAMINATION PERMIT');
		
		function changebackgroundimage(_selector, _attribute, _imageurl)
		{
			//var imageUrl = "/examples/images/sky.jpg";
			$(_selector).css(_attribute, "url(" + _imageurl + ")");
		}
		
		function InitializeTable()
		{
			theadstudent.empty();
			tbodystudent.empty();
			theadstudent.html(`<tr>
                        <th scope='col' class="text-center">#</th> 
                        <th scope='col'>Name</th> 
                        <th scope='col'>Gender</th> 
                        <th scope='col'>Course & Section</th> 
                        <th scope='col'>Status</th>
					</tr>`);
			tbodystudent.html(`<tr>
                        <td colspan='99' class="text-center text-danger"> No Record Found </td>
                    </tr>`);
		}
		
		function ReplaceSpecialCharacters(_str) 
		{
			try { 
				var ns = _str;
				// Just remove commas and periods - regex can do any chars
				ns = ns.replace(/([-,.€~!@#$%^&*()+=`{}\[\]\|\\:;'<>])+/g, "_");
				ns = ns.replaceAll(" ", "_");
				ns = ns.replaceAll("/", "_");
				return ns;
			}
			catch(e) {  //We can also throw from try block and catch it here
				console.error(e);
			}
			finally {
				console.log('We do cleanup here');
			}
		}
		function GetAcademicExaminationPeriod(_lvlid, _yrid, _prdid)
		{
			//var _no_error = false;
			var _result = null;
			$.ajax({
				async: false,
				type: "POST",
				url: '../../model/forms/academic/examinationpermit/examinationpermit-controller.php',
				data:{
					type : 'ACADEXAMPERIOD',
					action: 'FETCH',
					levelid : _lvlid,
					yearid : _yrid,
					periodid : _prdid
				},
				success: function(result){
					//var ret = JSON.parse(result);
					//_result = global_ACADEXAMPRD = result;
					_result = result;
				},
				error:function(status){
					divmessage.html('Examination Period Error!');
				}
			});
			return _result;
		}
		
		function ViewStudent()
		{
			$('.btnviewstudent').on('click',function() {
				$('#master-modal').modal('show');
				$('#div-student').show();
				$('#div-header').hide();
				InitializeTable();
				var lvlid = cboacadlvl.val();
				var yrid = cboacadyr.val();
				var prdid = cboacadprd.val();
				var crseid = cboacadcrse.val();
				
				var currentRow=$(this).closest("tr");
				var code=currentRow.find("td:eq(1)").html();
				var desc=currentRow.find("td:eq(2)").html();
				var sec=currentRow.find("td:eq(5)").html();
				var sched=currentRow.find("td:eq(6)").html();
				var ttlnoofenrolledstudent=currentRow.find("td:eq(8)").html();
				var tdprocess;
				$('#td-subj').html(code + ' - ' + desc);
				$('#td-crse-sec-sched').html(sec + ' - ' + sched);
				var btnviewstudent_arr = $(this).attr('id').split('-');
				var subjoffid = btnviewstudent_arr[1];
				global_SUBJOFFID = subjoffid;
				global_TTLNOOFENROLLEDSTUDENT = ttlnoofenrolledstudent;
				global_TDPROCESS = tdprocess;
				
				let rowNo = 1;
				//GetAcademicExaminationPeriod(lvlid,yrid,prdid);
				//var ret_acadexamprd = JSON.parse(global_ACADEXAMPRD);
				var ret_acadexamprd = JSON.parse(GetAcademicExaminationPeriod(lvlid,yrid,prdid));
				$.each(ret_acadexamprd, function(key, value) {
					$("#table-student tr").append("<th scope='col' class='table-primary text-center'>" + value.NAME + "</th>");
				});
				$.ajax({
					async: false,
					type:"POST",
					url: '../../model/forms/academic/examinationpermit/examinationpermit-controller.php',
					data:{
						type : 'STUDENT_LIST',
						action: 'FETCH',
						levelid : lvlid,
						yearid: yrid,
						periodid: prdid,
						subjofferedid: subjoffid,
					},
					beforeSend: function (status) {
						$('#div-message').html('');
						$('#tbl-header-student-list').hide();
						tbodystudent.html("<tr><td colspan='99' class='text-center text-danger'> No Record Found </td></tr>");
					},
					success: function(result){
						var ret = JSON.parse(result);
						var tblstudent = '';
						if(ret.length > 0) 
						{
							$.each(ret, function(key, value) 
							{
								if (value.STATUS == 'ENROLLED')
								{
									tblstudent += "<tr>" + 
										  "<td class='text-center'>" + rowNo++ + "</td>" + 
										  "<td>" + value.NAME + "</td>" + 
										  "<td>" + value.GENDER + "</td>" + 
										  "<td>" + value.SECTION + "</td>" +
										  "<td>" + value.STATUS + "</td>";
										  $.each(ret_acadexamprd, function(key, value1) 
										  {		
										        var examprdarr = value.ENROLLEXAMPERMITPRD_ID.split(',');
							                    var exampermitpromiarr = value.EXAMPERMIT_PRD_PROMI.split(',');
												if (examprdarr.length > 1)
												{
													for(i=0; i < examprdarr.length; i++)
													{
														var finalexamprdarr = examprdarr[i].split('=');
														if (parseInt(value1.ID) == parseInt(finalexamprdarr[0]))
														{
															if (parseFloat(finalexamprdarr[1]) == 0)
															{
																tblstudent += "<td class='text-center'><i class='fa-solid fa-check text-success'></i></td>";
																break;
															} else
															{
																if (exampermitpromiarr.length > 1)
																{
																    var has_match = false;
																	for(i1=0; i1 < exampermitpromiarr.length; i1++){
																		var finalexampermitpromiarr = exampermitpromiarr[i1].split('=');
																		if (parseInt(value1.ID) == parseInt(finalexampermitpromiarr[0]))
																		{
																		    has_match = true;
																			if (parseInt(finalexampermitpromiarr[2]) == 0)
																			{
																				tblstudent += "<td class='text-center'><i class='fa-solid fa-xmark text-danger'></i></td>";
																				break;
																			} else if (parseInt(finalexampermitpromiarr[2]) == 1) {
																				tblstudent += "<td class='text-info-emphasis'> Promi </td>";
																				break;
																			} else {
            															        tblstudent += "<td class='text-center'><i class='fa-solid fa-xmark text-danger'></i></td>";
            															        break;
            														        }
    																	}
																	}
                                                                    
																	if (has_match === false){
                                                                        tblstudent += "<td class='text-center'><i class='fa-solid fa-xmark text-danger'></i></td>";
																	}
																} else {
															        tblstudent += "<td class='text-center'><i class='fa-solid fa-xmark text-danger'></i></td>";
														        }
															}
														}
													}
												} else {
													tblstudent += "<td class='text-center'><i class='fa-solid fa-xmark text-danger'></i></td>";
												}
										  });
									tblstudent += "</tr>";
								} else {
									tblstudent += "<tr>" + 
										  "<td class='text-center'>" + rowNo++ + "</td>" + 
										  "<td>" + value.NAME + "</td>" + 
										  "<td>" + value.GENDER + "</td>" + 
										  "<td>" + value.SECTION + "</td>" +
										  "<td>" + value.STATUS + "</td>";
									tblstudent += "<td colspan='99'></td>";
									tblstudent += "</tr>";
								}
							});
						} else {
							tblstudent += "<tr><td colspan='99' class='text-center text-danger'> No Record Found </td></tr>";
						}
						tbodystudent.html(tblstudent);
						$('#tbl-header-student-list').show();
					}
				})
				setTimeout(function (e) {
						$('#master-modal').modal('hide');
					}, 1000);
			})
		}
		
		function ViewAcademicSubjectOffered(_ctrlparent, _lvlid, _yrid, _prdid, _crseid)
		{
			let lineNo = 1;
			$.ajax({
				async: false,
				type:"POST",
				url: '../../model/forms/academic/examinationpermit/examinationpermit-controller.php',
				data:{
					type : 'OFFERED_SUBJECT',
					action: 'FETCH',
					levelid : _lvlid,
					yearid: _yrid,
					periodid: _prdid,
					courseid: _crseid
				},
				success: function(result){
					var ret = JSON.parse(result);
					var tblOffSubject = '';
					if(ret.length) {
						$.each(ret, function(key, value) {
							if (value.NO_OF_STUDENT > 0)
							{
							    var final_sched = '';
							    if (value.SCHEDULE === null)
							    {
							        final_sched = '';
							    } else {
							        var sched = value.SCHEDULE.replace(']||[',':').replace('[||]','-').replace(']||[',':').split('=');
							        final_sched = sched[1] + ' ' + sched[2];
							    }
							    
								tblOffSubject += "<tr>" + 
										  "<td>" + lineNo++ + "</td>" + 
										  "<td>" + value.CODE + "</td>" + 
										  "<td style='text-align:left;'>" + value.DESC + "</td>" + 
										  "<td>" + value.UNIT + "</td>" + 
										  "<td style='text-align:left;'>" + value.COURSE + "</td>" + 
										  "<td>" + value.SECTION + "</td>" + 
										  "<td>" + final_sched + "</td>" + 
										  "<td>" + value.GRADING_SCALE + "</td>" + 
										  "<td class='text-center'>" + value.NO_OF_STUDENT + "</td>" + 
										  "<td>" + value.REQ_STATUS_NAME + "</td>" + 
										  "<td>" +
										  "<button type='button' id='btnviewstudent-" + value.OFFERED_SUBJ_SMS_ID + "-"  + value.SIGN_ID + "-" + value.REQ_STATUS + "-"  + value.SIGN_USERID + "-"  + value.STUD_ACAD_REC_ID + "' name='" + value.GSCALE_ID + "' data-backdrop='static' data-keyboard='false' class='btn btn-sm btn-primary btnviewstudent' value='" + value.GSCALE_ID + "'>" + 
														"Student</button>" +
														"<input type='hidden' id='inputhiddengspassscore-'" + value.OFFERED_SUBJ_SMS_ID + "-"  + value.SIGN_ID + "' name='" + value.NO_OF_ENCODED_STUDENT + "' value='" + value.GS_PASS_SCORE + "'/>" +
										  "</td>" +																	  
										  "</tr>";
							}
						});
					} else {
						tblOffSubject += "<tr><td colspan='99' class='text-center text-danger'> No Record Found </td></tr>>";
					}
					_ctrlparent.html(tblOffSubject);
					ViewStudent();
				},
				error:function(status){
					$('#div-message').append('Error!');
					//alert(status);
				}
			});
		}
		
		btnsearch.on('click',function(e) {
			$('#master-modal').modal('show');
			ViewAcademicSubjectOffered(tbodyofferedsubject, cboacadlvl.val(), cboacadyr.val(), cboacadprd.val(), cboacadcrse.val());
			//cboacadcrse.change();
			setTimeout(function (e) {
				$('#master-modal').modal('hide');
			}, 1000);
		});
		btnback.on('click',function(e) {
			$('#div-student').hide();
			$('#div-header').show();
			//$('#btnsearch').click();
			//btnsearch.click();
		});
		
		//$("#yourElement").off('change').on('change', function() {
		//// Your code here
		//});
		
		function GetAcademicLevel(_cbo, _ctrldisplaymessage)
		{
			var _no_error = false;
			$.ajax({
				async: false,
				type:"POST",
				//contentType: "application/json; charset=utf-8",
				url: '../../model/forms/academic/examinationpermit/examinationpermit-controller.php',
				data:{
					type : 'ACADLEVEL',
					action: 'FETCH'
				},
				//dataType: "Json",
				success: function(result){
					var ret = JSON.parse(result);
					if(ret.length) {
						var _str = '';
						$.each(ret, function(key, value) {
							_str += "<option value='" + value.ID + "'>" + value.NAME + "</option>";
						});
					} else {
						_str += "<option value='0'>None</option>";
					}
					//cboacadlvl.html(_str);
					//_ctrldisplaymessage.empty();
					_cbo.html(_str);
					_no_error = true;
				//},
				//complete: function(status) {
					//Fires event once process is ompleted
					//_ctrldisplaymessage.html('Complete');
				},
				error:function(status){
					//$('#div-message').append('Error!');
					//_ctrldisplaymessage.html('Error');
					_cbo.html("<option value='-1'>Error</option>");
					_no_error = false;
				}
			})
			return _no_error;
		}
		function GetAcademicYear(_cbo, _lvlid, _ctrldisplaymessage)
		{
			var _no_error = false;
			$.ajax({
				async: false,
				type:"POST",
				url: '../../model/forms/academic/examinationpermit/examinationpermit-controller.php',
				data:{
					type : 'ACADYEAR',
					action: 'FETCH',
					levelid : _lvlid
				},
				//dataType: "Json",
				success: function(result){
					var ret = JSON.parse(result);
					if(ret.length) {
						var _str = '';
						$.each(ret, function(key, value) {
							_str += "<option value='" + value.ID + "'>" + value.NAME + "</option>";
						});
					} else {
						_str += "<option value='0'>None</option>";
					}
					//cboacadlvl.html(_str);
					//_ctrldisplaymessage.empty();
					_cbo.html(_str);
					_no_error = true;
				//},
				//complete: function(status) {
					//Fires event once process is ompleted
					//_ctrldisplaymessage.html('Complete');
				},
				error:function(status){
					//$('#div-message').append('Error!');
					//_ctrldisplaymessage.html('Error');
					_cbo.html("<option value='-1'>Error</option>");
					_no_error = false;
				}
			})
			return _no_error;
		}
		function GetAcademicPeriod(_cbo, _lvlid, _yrid, _ctrldisplaymessage)
		{
			var _no_error = false;
			$.ajax({
				async: false,
				type:"POST",
				url: '../../model/forms/academic/examinationpermit/examinationpermit-controller.php',
				data:{
					type : 'ACADPERIOD',
					action: 'FETCH',
					levelid : _lvlid,
					yearid: _yrid
				},
				//dataType: "Json",
				success: function(result){
					var ret = JSON.parse(result);
					if(ret.length) {
						var _str = '';
						$.each(ret, function(key, value) {
							_str += "<option value='" + value.ID + "'>" + value.NAME + "</option>";
						});
					} else {
						_str += "<option value='0'>None</option>";
					}
					//cboacadlvl.html(_str);
					//_ctrldisplaymessage.empty();
					_cbo.html(_str);
					_no_error = true;
				//},
				//complete: function(status) {
					//Fires event once process is ompleted
					//_ctrldisplaymessage.html('Complete');
				},
				error:function(status){
					//$('#div-message').append('Error!');
					//_ctrldisplaymessage.html('Error');
					_cbo.html("<option value='-1'>Error</option>");
					_no_error = false;
				}
			})
			return _no_error;
		}
		function GetAcademicCourse(_cbo, _lvlid, _yrid, _prdid, _ctrldisplaymessage)
		{
			var _no_error = false;
			$.ajax({
				async: false,
				type:"POST",
				url: '../../model/forms/academic/examinationpermit/examinationpermit-controller.php',
				data:{
					type : 'ACADCOURSE',
					action: 'FETCH',
					levelid : _lvlid,
					yearid: _yrid,
					periodid: _prdid
				},
				//dataType: "Json",
				success: function(result){
					var ret = JSON.parse(result);
					if(ret.length) {
						var _str = '';
						$.each(ret, function(key, value) {
							_str += "<option value='" + value.ID + "'>" + value.NAME + "</option>";
						});
					} else {
						_str += "<option value='0'>None</option>";
					}
					//cboacadlvl.html(_str);
					//_ctrldisplaymessage.empty();
					_cbo.html(_str);
					_no_error = true;
				//},
				//complete: function(status) {
					//Fires event once process is ompleted
					//_ctrldisplaymessage.html('Complete');
				},
				error:function(status){
					//$('#div-message').append('Error!');
					//_ctrldisplaymessage.html('Error');
					_cbo.html("<option value='-1'>Error</option>");
					_no_error = false;
				}
			})
			return _no_error;
		}
		function document_load() 
		{
			$('#tbl-header-student-list').hide();
			$('#div-student-academic-grades').hide();
		}
		
		function Initialize() 
		{
			$('#tbl-header-student-list').hide();
			$('#div-student-academic-grades').hide();
			
			//AcademicLevelOnChanged(cboacadlvl, cboacadyr, false);
			
			GetAcademicLevel(cboacadlvl, divmessage);
			LinkAndSaveControlUniqueValue(null, $('#cbo-acadlvl'));
			AcademicLevelOnChanged(cboacadlvl, cboacadyr, true);
			
			GetAcademicYear(cboacadyr, cboacadlvl.val(), divmessage);
			LinkAndSaveControlUniqueValue(null, $('#cbo-acadyr'));
			AcademicYearOnChanged(cboacadyr, cboacadprd, cboacadlvl.val(), true);
			
			GetAcademicPeriod(cboacadprd, cboacadlvl.val(), cboacadyr.val(), divmessage);
			LinkAndSaveControlUniqueValue(null, $('#cbo-acadprd'));
			AcademicPeriodOnChanged(cboacadprd, cboacadcrse, cboacadlvl.val(), cboacadyr.val(), true);
			
			GetAcademicCourse(cboacadcrse, cboacadlvl.val(), cboacadyr.val(), cboacadprd.val(), divmessage);
			LinkAndSaveControlUniqueValue(null, $('#cbo-acadcrse'));
			AcademicCourseOnChanged(cboacadcrse, tbodyofferedsubject, cboacadlvl.val(), cboacadyr.val(), cboacadprd.val(), true);
			
			ViewAcademicSubjectOffered(tbodyofferedsubject, cboacadlvl.val(), cboacadyr.val(), cboacadprd.val(), cboacadcrse.val());
		}
		function AcademicLevelOnChanged(_cboparent, _cbochild, _is_on)
		{
			if (_is_on)
			{
				$('#cbo-acadlvl').on('change',function(){
					$('#master-modal').modal('show');
					AcademicYearOnChanged($('#cbo-acadyr'), null, 0, false);
					GetAcademicYear($('#cbo-acadyr'), $(this).val());
					LinkAndSaveControlUniqueValue($('#cbo-acadlvl'), $('#cbo-acadyr'));
					AcademicYearOnChanged($('#cbo-acadyr'), null, $(this).val(), true);
					$('#cbo-acadyr').change();
					setTimeout(function (e) {
						$('#master-modal').modal('hide');
					}, 1000);
				});
			} else {
				$('#cbo-acadlvl').off('change');
			}
		}
		function AcademicYearOnChanged(_cboparent, _cbochild, _lvlid, _is_on)
		{
			if (_is_on)
			{
				$('#cbo-acadyr').on('change',function(){
					$('#master-modal').modal('show');
					AcademicPeriodOnChanged($('#cbo-acadprd'), null, 0, 0, false);
					GetAcademicPeriod($('#cbo-acadprd'), _lvlid, $(this).val());
					LinkAndSaveControlUniqueValue($('#cbo-acadyr'), $('#cbo-acadprd'));
					AcademicPeriodOnChanged($('#cbo-acadprd'), null, _lvlid, $(this).val(), true);
					$('#cbo-acadprd').change();
					setTimeout(function (e) {
						$('#master-modal').modal('hide');
					}, 1000);
				});
			} else {
				$('#cbo-acadyr').off('change');
			}
		}
		function AcademicPeriodOnChanged(_cboparent, _cbochild, _lvlid, _yrid, _is_on)
		{
			if (_is_on)
			{
				$('#cbo-acadprd').on('change',function(){
					$('#master-modal').modal('show');
					AcademicCourseOnChanged($('#cbo-acadcrse'), null, 0, 0, 0, false);
					GetAcademicCourse($('#cbo-acadcrse'), _lvlid, _yrid, $(this).val());
					LinkAndSaveControlUniqueValue($('#cbo-acadprd'), $('#cbo-acadcrse'));
					AcademicCourseOnChanged($('#cbo-acadcrse'), null, _lvlid, _yrid, $(this).val(), true);
					$('#cbo-acadcrse').change();
					setTimeout(function (e) {
						$('#master-modal').modal('hide');
					}, 1000);
				});
			} else {
				$('#cbo-acadprd').off('change');
			}
		}
		function AcademicCourseOnChanged(_cboparent, _cbochild, _lvlid, _yrid, _prdid, _is_on)
		{
			if (_is_on)
			{
				$('#cbo-acadcrse').on('change',function(){
					$('#master-modal').modal('show');
					LinkAndSaveControlUniqueValue($('#cbo-acadcrse'), null);
					ViewAcademicSubjectOffered(tbodyofferedsubject, cboacadlvl.val(), cboacadyr.val(), cboacadprd.val(), cboacadcrse.val());
					setTimeout(function (e) {
						$('#master-modal').modal('hide');
					}, 1000);
				});
			} else {
				$('#cbo-acadcrse').off('change');
			}
		}
		
		function LinkAndSaveControlUniqueValue(_sender, _receiver)
		{
			if (_sender != null)
			{
				//global_ACADLVLID = (_sender.data('tag') != null || _sender.data('tag') != '' || _sender.data('tag').length > 0) ? _sender.data('tag') : "";
				//_sender = _sender == null ? "" : _sender.text();
				switch(_sender.attr("name")) {
				  case 'cbo-acadlvl':
						global_ACADLVL = _sender == null ? 0 : _sender.find(":selected").val();
					break;
				  case 'cbo-acadyr':
						global_ACADYR = _sender == null ? 0 : _sender.find(":selected").val();
					break;
				  case 'cbo-acadprd ':
						global_ACADPRD = _sender == null ? 0 : _sender.find(":selected").val();
					break;
				  case 'cbo-acadcrse':
						global_ACADCRSE = _sender == null ? 0 : _sender.find(":selected").val();
					break;
				  default:
					global_ACADLVL = 0;
					global_ACADYR = 0;
					global_ACADPRD = 0;
					global_ACADCRSE = 0;
				}
			}				
			if (_receiver != null)
			{
				//if (_receiver.val() == null)
				//{
					switch(_receiver.attr("name")) {
					  case 'cbo-acadlvl':
							if (global_ACADLVL == null || global_ACADLVL == 0)
							{
								global_ACADLVL = _receiver == null ? 0 : _receiver.find(":selected").val();
							} else {
								_receiver.val(global_ACADLVL);
							}
						break;
					  case 'cbo-acadyr':
							if (global_ACADYR == null || global_ACADYR == 0)
							{
								global_ACADYR = _receiver == null ? 0 : _receiver.find(":selected").val();
							} else {
								_receiver.val(global_ACADYR);
							}
						break;
					  case 'cbo-acadprd ':
							if (global_ACADPRD == null || global_ACADPRD == 0)
							{
								global_ACADPRD = _receiver == null ? 0 : _receiver.find(":selected").val();
							} else {
								_receiver.val(global_ACADPRD);
							}
						break;
					  case 'cbo-acadcrse':
							if (global_ACADCRSE == null || global_ACADCRSE == 0)
							{
								global_ACADCRSE = _receiver == null ? 0 : _receiver.find(":selected").val();
							} else {
								_receiver.val(global_ACADCRSE);
							}
						break;
					  default:
						global_ACADLVL = 0;
						global_ACADYR = 0;
						global_ACADPRD = 0;
						global_ACADCRSE = 0;
					}
				//}
			} else {
				//if (_sender.val() == null)
				//{
					switch(_sender.attr("name")) {
					  case 'cbo-acadlvl':
							if (global_ACADLVL == null || global_ACADLVL == 0)
							{
								global_ACADLVL = _sender == null ? 0 : _sender.find(":selected").val();
							} else {
								_sender.val(global_ACADLVL);
							}
						break;
					  case 'cbo-acadyr':
							if (global_ACADYR == null || global_ACADYR == 0)
							{
								global_ACADYR = _sender == null ? 0 : _sender.find(":selected").val();
							} else {
								_sender.val(global_ACADYR);
							}
						break;
					  case 'cbo-acadprd ':
							if (global_ACADPRD == null || global_ACADPRD == 0)
							{
								global_ACADPRD = _sender == null ? 0 : _sender.find(":selected").val();
							} else {
								_sender.val(global_ACADPRD);
							}
						break;
					  case 'cbo-acadcrse':
							if (global_ACADCRSE == null || global_ACADCRSE == 0)
							{
								global_ACADCRSE = _sender == null ? 0 : _sender.find(":selected").val();
							} else {
								_sender.val(global_ACADCRSE);
							}
						break;
					  default:
						global_ACADLVL = 0;
					global_ACADYR = 0;
					global_ACADPRD = 0;
					global_ACADCRSE = 0;
					}
				//}
			}
			//var theDiv = $('#' + divID).data('winWidth', value);
			
			//_ctrl.val() = _retval;
			//return _retval > 0 ? true : false;
		}
		
		Initialize();
	}
);