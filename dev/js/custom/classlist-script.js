$(document).ready(function(){
	var global_CONFIRMATION_MESSAGE = 0;
	var global_deptsignatories;
	var global_STUDACADRECREQSTATUS;
	var global_GSDETPERCENTAGE;
	var global_STUDACADRECDETREC;
	var global_STUDACADRECDETID;
	var global_RESULTTYPE;
	var global_GSCALEPASSSCOREID;
	var global_SUBJOFFID;
	var global_STUDACADRECID;
	var global_SIGNID;
	var global_SIGNUSERID;
	var global_GSCALEID;
	var global_NOOFSTUDENTENCODED;
	var global_PNOTYETENCODED;
	var global_BTSTUDENTID;
	var global_BTSTUDENTNAME;
	var global_INPUTNOOFSTUDENTENCODED;
	var global_TTLNOOFENROLLEDSTUDENT;
	var global_TDCOLSTATUS;
	var global_TDPROCESS;
	var global_CURRENTSTUDACADAVG;
	var global_CURRENTSTUDACADAVGSTATUS;
	var global_CLSTUDID;
	var global_STUDASSID;
	var global_TXTINPUTVALUE;
	var global_TDINPUTVALUEPERCENTAGE;
	var global_TDSUBAVGNEXTID;
	
	const confirmation = $('#confirmation');
	const divstudent = $('#div-student');
	const confirmationno = $('#confirmation-no');
	const confirmationyes = $('#confirmation-yes');
	const btnsearch = $('#btnsearch');
	const btnBack = $('#btnBack');
	const btncancel = $('.cancel');
	const btnclose = $('.close');
	const btnsave = $('.save');
	const cboacadlvl = $('#cbo-acadlvl');
	const cboacadyr = $('#cbo-acadyr');
	const cboacadprd = $('#cbo-acadprd');
	const cboacadcrse = $('#cbo-acadcrse');
	//const btnsubmit = $('.btnsubmit');
	const tbodystudent = $('#tbody-student');
	const tbodyofferedsubject = $('#tbody-offered-subject');
	const btnedit = $('.edit');
	const divmessage = $('#div-message');
	//const btnviewstudent = $('.btnviewstudent');
	
	divstudent.hide();
	
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
	
	function ViewStudent()
	{
		//const btnviewstudent = $('.btnviewstudent');
		$('.btnviewstudent').click(function() {
			$('#div-student').show();
			$('#div-header').hide();
			var lvlid = cboacadlvl.val();
			var yrid = cboacadyr.val();
			var prdid = cboacadprd.val();
			var crseid = cboacadcrse.val();
			
			var currentRow=$(this).closest("tr");
			var code=currentRow.find("td:eq(1)").html();
			var desc=currentRow.find("td:eq(2)").html();
			var sec=currentRow.find("td:eq(5)").html();
			var sched=currentRow.find("td:eq(6)").html();
			var gradingscale=currentRow.find("td:eq(7)").html();
			var ttlnoofenrolledstudent=currentRow.find("td:eq(8)").html();
			//var ttlnoofenrolledstudent=currentRow.find("td:eq(9)").html();
			var coltdstatus=currentRow.find("td:eq(9)").attr('id');
			var tdgspassscore=currentRow.find("td:eq(10)");
			var btnnotyetencoded=tdgspassscore.find("button").attr('id');
			var gspassscore = tdgspassscore.find('input:hidden').val();
			var btnstudentname=tdgspassscore.find("button").attr('name');
			var noofstudentencoded = tdgspassscore.find('input:hidden').attr('name');
			
			var tdnotyetencoded=currentRow.find("td:eq(11)");
			
			var tdprocess;
			if (tdnotyetencoded.find("div").length > 0) {
				tdprocess=tdnotyetencoded.find("div").attr('id');
			} else {
				tdprocess=tdnotyetencoded.find("button").attr('id');
			}

			var notyetencoded = tdprocess;//tdnotyetencoded.find("div").attr('id');
			//var notyetencoded = parseInt(ttlnoofenrolledstudent) - parseInt(noofstudentencoded);
			$('#td-subj').html(code + ' - ' + desc);
			$('#td-crse-sec-sched').html(sec + ' / ' + sched);
			var btnviewstudent_arr = $(this).attr('id').split('-');
			var subjoffid = btnviewstudent_arr[1];
			var studacadrecsignid = btnviewstudent_arr[2];
			var studacadrecreqstatus = btnviewstudent_arr[3];
			var studacadrecsignuserid = btnviewstudent_arr[4];
			var studacadrecid = btnviewstudent_arr[5];
			var gscaleid = $(this).val();
			global_SUBJOFFID = subjoffid;
			global_STUDACADRECID = studacadrecid;
			global_SIGNID = studacadrecsignid;
			global_SIGNUSERID = studacadrecsignuserid;
			global_STUDACADRECREQSTATUS = studacadrecreqstatus;
			global_GSCALEID = gscaleid;
			global_GSCALEPASSSCOREID = gspassscore;
			global_NOOFSTUDENTENCODED = noofstudentencoded;
			global_PNOTYETENCODED = notyetencoded;
			global_BTSTUDENTID = btnnotyetencoded;
			global_BTSTUDENTNAME = btnstudentname;
			global_INPUTNOOFSTUDENTENCODED = tdgspassscore.find('input:hidden').attr('id');
			global_TTLNOOFENROLLEDSTUDENT = ttlnoofenrolledstudent;
			global_TDCOLSTATUS = coltdstatus;
			global_TDPROCESS = tdprocess;
			
			if (gscaleid > 0){
				$('#p-title').html('List of Enrolled Student (' + gradingscale.toString() + ')');
			} else {
				$('#p-title').html('List of Enrolled Student (No Grading Scale)');
			}
			
			let rowNo = 1;
			//GetStudentList(gscaleid,lvlid,yrid,prdid,subjoffid,studacadrecreqstatus);
			//alert(subjoffid);
			$.ajax({
			    async: false,
				type:'GET',
				url: '../../model/forms/academic/classlist/classlist-controller.php',
				data:{
					type : 'STUDENT_LIST',
					action: 'FETCH',
					gsid: gscaleid,
					levelid : lvlid,
					yearid: yrid,
					periodid: prdid,
					//courseid: crseid,
					subjofferedid: subjoffid,
					studacadrecid: studacadrecid
				},
				beforeSend: function (status) {
					$('#div-message').html('');
					$('#tbl-header-student-list').hide();
					tbodystudent.html("<tr><td colspan='99' class='text-danger text-center'> No Record Found </td></tr>");
												//btnviewstudent.click();
				},
				success: function(result){
						var ret = JSON.parse(result);
						var tblstudent = '';
						if(ret.length > 0) {
							GetGradingScaleDetailPercentage(parseInt(gscaleid),parseInt(subjoffid));
							//alert(global_GSDETPERCENTAGE.length);
							//if (global_GSDETPERCENTAGE.length > 0){
								//alert(global_GSDETPERCENTAGE);
								var data = JSON.parse(global_GSDETPERCENTAGE);
								$.each(ret, function(key, value) {
								    //if (value.STUD_ACAD_REC_ID == studacadrecid){
    									var parentid = [];
    									var childid = [];
    									if (gscaleid > 0) {
    										if (value.STATUS == 'ENROLLED'){
    											if (value.RECORDS.length > 0) {
    												var retval = JSON.parse(global_GSDETPERCENTAGE, function(keys, values) { 
    												if (values.GSD_PARENT_ID === "0" ) parentid.push(values); 
    												return values;})
    												
    												var final_stud_avg = '0';
    												var final_stud_avg_status = '';
    												var searchpar = new RegExp('0','i');
    												var gspassscore = global_GSCALEPASSSCOREID;
    											if(data.length) {
    												$.each(parentid, function(keys, vals) {
    													childid = [];
    													JSON.parse(global_GSDETPERCENTAGE, function(k, val) { 
    														if (val.GSD_PARENT_ID === vals.GSD_DET_ID)childid.push(val);
    															return val;})
    															$.each(childid, function(k, v) {
    																var initialarr = value.RECORDS.split(',');
    																for(i=0; i < initialarr.length; i++){
    																	var finalarr = initialarr[i].split(':');
    																	if (parseInt(v.GSD_DET_ID) == parseInt(finalarr[0])){
    																		final_stud_avg = parseFloat(final_stud_avg) + (parseFloat(finalarr[1]) * parseFloat((parseFloat(v.GSD_PERCENTAGE) / 100)));
    																	}
    																}
    															});
    												});
    												
    															if (final_stud_avg.length <= 0 || $.trim(final_stud_avg) == '' || $.trim(final_stud_avg) == '0'){
    																final_stud_avg = '0';
    															}
    															if (gspassscore.length <= 0 || gspassscore == '' || gspassscore == '0' || gspassscore == 0){
    																gspassscore = '0';
    																final_stud_avg_status = 'PASSING SCORE NOT SET';
    															} else {
    																if (parseFloat(final_stud_avg) >= parseFloat(gspassscore)){
    																	//final_stud_avg_status = 'PASSED';
    																	//final_stud_avg_status = value.STUD_ACAD_REC_DET_RESULT_TYPE
    																	if ($.trim(value.STUD_ACAD_REC_DET_RESULT_TYPE).length > 0){
    																		final_stud_avg_status = $.trim(value.STUD_ACAD_REC_DET_RESULT_TYPE);
    																	} else {
    																		final_stud_avg_status = 'PASSED';
    																	}
    																} else {
    																	if ($.trim(value.STUD_ACAD_REC_DET_RESULT_TYPE).length > 0){
    																		final_stud_avg_status = $.trim(value.STUD_ACAD_REC_DET_RESULT_TYPE);
    																	} else {
    																		final_stud_avg_status = 'FAILED';
    																	}
    																}
    															}
    														}
    														else {
    															final_stud_avg_status = 'NO ENCODED GRADES';
    															final_stud_avg = '0';
    														}
    												if (studacadrecreqstatus == 0 || studacadrecreqstatus == 2 || studacadrecreqstatus == 8) {
    													tblstudent += "<tr>" + 
    															  "<td class='text-center'>" + rowNo++ + "</td>" + 
    															  "<td>" + value.NAME + "</td>" + 
    															  "<td>" + value.GENDER + "</td>" + 
    															  "<td>" + value.SECTION + "</td>" +
    															  "<td>" + value.STATUS + "</td>" +
    															  "<td class='text-center' id='tdfinalaverage-" + value.STATUS + "-" + value.ID + "' name='" + value.ASS_ID + "'>" + parseFloat(final_stud_avg).toFixed(2).toString() + "%</td>" +
    															  "<td class='text-center' id='tdfinalstatus-" + value.STATUS + "-" + value.ID + "' name='" + value.ASS_ID + "'>" + final_stud_avg_status.toString() + "</td>" +
    															  "<td>" +
    															  "<button type='button' " +
    																"id='" + value.ID + "' name='" + value.ASS_ID + "' data-backdrop='static' data-keyboard='false' class='btn btn-sm btn-success edit' value='" + value.ID + "'>" + 
    																"Grades</button>" +
    															  "</td>" + 
    															  "</tr>";
    												} else if (studacadrecreqstatus == 3 || studacadrecreqstatus == 6) {
    													tblstudent += "<tr>" + 
    															  "<td class='text-center'>" + rowNo++ + "</td>" + 
    															  "<td>" + value.NAME + "</td>" + 
    															  "<td>" + value.GENDER + "</td>" + 
    															  "<td>" + value.SECTION + "</td>" +
    															  "<td>" + value.STATUS + "</td>" +
    															  "<td class='text-center' id='tdfinalaverage-" + value.STATUS + "-" + value.ID + "' name='" + value.ASS_ID + "'>" + parseFloat(final_stud_avg).toFixed(2).toString() + "%</td>" +
    															  "<td class='text-center' id='tdfinalstatus-" + value.STATUS + "-" + value.ID + "' name='" + value.ASS_ID + "'>" + final_stud_avg_status.toString() + "</td>" +
    															  "<td>" +
    															  "ON PROCESS" +
    															  "</td>" + 
    															  "</tr>";
    												} else if (studacadrecreqstatus == 5) {
    													tblstudent += "<tr>" + 
    															  "<td class='text-center'>" + rowNo++ + "</td>" + 
    															  "<td>" + value.NAME + "</td>" + 
    															  "<td>" + value.GENDER + "</td>" + 
    															  "<td>" + value.SECTION + "</td>" +
    															  "<td>" + value.STATUS + "</td>" +
    															  "<td class='text-center' id='tdfinalaverage-" + value.STATUS + "-" + value.ID + "' name='" + value.ASS_ID + "'>" + parseFloat(final_stud_avg).toFixed(2).toString() + "%</td>" +
    															  "<td class='text-center' id='tdfinalstatus-" + value.STATUS + "-" + value.ID + "' name='" + value.ASS_ID + "'>" + final_stud_avg_status.toString() + "</td>" +
    															  "<td>" +
    															  "APPROVED" +
    															  "</td>" + 
    															  "</tr>";
    												} else {
    													tblstudent += "<tr>" + 
    															  "<td class='text-center'>" + rowNo++ + "</td>" + 
    															  "<td>" + value.NAME + "</td>" + 
    															  "<td>" + value.GENDER + "</td>" + 
    															  "<td>" + value.SECTION + "</td>" +
    															  "<td>" + value.STATUS + "</td>" +
    															  "<td class='text-center' id='tdfinalaverage-" + value.STATUS + "-" + value.ID + "' name='" + value.ASS_ID + "'>" + parseFloat(final_stud_avg).toFixed(2).toString() + "%</td>" +
    															  "<td class='text-center' id='tdfinalstatus-" + value.STATUS + "-" + value.ID + "' name='" + value.ASS_ID + "'>" + final_stud_avg_status.toString() + "</td>" +
    															  "<td>" +
    															  "" +
    															  "</td>" + 
    															  "</tr>";
    												}
    											} else {
    												tblstudent += "<tr>" + 
    															  "<td class='text-center'>" + rowNo++ + "</td>" + 
    															  "<td>" + value.NAME + "</td>" + 
    															  "<td>" + value.GENDER + "</td>" + 
    															  "<td>" + value.SECTION + "</td>" +
    															  "<td>" + value.STATUS + "</td>" +
    															  "<td class='text-center' id='tdfinalaverage-" + value.STATUS + "-" + value.ID + "' name='" + value.ASS_ID + "'>0%</td>" +
    															  "<td class='text-center' id='tdfinalstatus-" + value.STATUS + "-" + value.ID + "' name='" + value.ASS_ID + "'>NO ENCODED GRADES</td>" +
    															  "<td>" +
    															  "<button type='button' " +
    																"id='" + value.ID + "' name='" + value.ASS_ID + "' data-backdrop='static' data-keyboard='false' class='btn btn-sm btn-success edit' value='" + value.ID + "'>" + 
    																"Grades</button>" +
    															  "</td>" + 
    															  "</tr>";
    											}
    										} else {
    											tblstudent += "<tr>" + 
    												  "<td class='text-center'>" + rowNo++ + "</td>" + 
    												  "<td>" + value.NAME + "</td>" + 
    												  "<td>" + value.GENDER + "</td>" + 
    												  "<td>" + value.SECTION + "</td>" +
    												  "<td>" + value.STATUS + "</td>" +
    												  "<td class='text-center' id='tdfinalaverage-" + value.STATUS + "-" + value.ID + "' name='" + value.ASS_ID + "'>0%</td>" +
    												  "<td class='text-center' id='tdfinalstatus-" + value.STATUS + "-" + value.ID + "' name='" + value.ASS_ID + "'></td>" +
    												  "<td>" +
    												  "</td>" + 
    												  "</tr>";
    										}
    									} 
    									else 
    									{
    										tblstudent += "<tr>" + 
    											  "<td class='text-center'>" + rowNo++ + "</td>" + 
    											  "<td>" + value.NAME + "</td>" + 
    											  "<td>" + value.GENDER + "</td>" + 
    											  "<td>" + value.SECTION + "</td>" +
    											  "<td>" + value.STATUS + "</td>" +
    											  "<td class='text-center' id='tdfinalaverage-" + value.STATUS + "-" + value.ID + "' name='" + value.ASS_ID + "'>0%</td>" +
    											  "<td class='text-center' id='tdfinalstatus-" + value.STATUS + "-" + value.ID + "' name='" + value.ASS_ID + "'>NO GRADING SCALE</td>" +
    											  "<td>" +
    											  "</td>" + 
    											  "</tr>";
    									}
								    //} else {
								        
								        
								    //}
								});
							
						} else {
							tblstudent += "<tr><td colspan='99' class='text-danger text-center'> No Record Found </td></tr>";
						}
						tbodystudent.html(tblstudent);
						//btnviewstudent.click();
						const btnedit = $('.edit');
						btnedit.click(function() {
							var studid = $(this).val();
							var assid = $(this).attr('name');
							var lvlid = cboacadlvl.val();
							var yrid = cboacadyr.val();
							var yrname = $('#cbo-acadyr option:selected').text();
							var prdid = cboacadprd.val();
							var prdname =  $('#cbo-acadprd option:selected').text();
							var crseid = cboacadcrse.val();
							var subjoffid = global_SUBJOFFID;
							var currentRow = $(this).closest("tr");
							var name=currentRow.find("td:eq(1)").html();
							var studacadavg=currentRow.find("td:eq(5)").attr('id');
							var studacadavgstatus=currentRow.find("td:eq(6)").attr('id');
							
							global_CURRENTSTUDACADAVG = studacadavg;
							global_CURRENTSTUDACADAVGSTATUS = studacadavgstatus;
							
							global_CLSTUDID = studid;
							global_STUDASSID = assid;
							
							$.ajax({
							    async: false,
								type:'GET',
								url: '../../model/forms/academic/classlist/classlist-controller.php',
								data:{
									type : 'GRADING_SCALE_PERCENTAGE',
									action: 'FETCH',
									levelid : lvlid,
									yearid: yrid,
									periodid: prdid,
									courseid: crseid,
									subjofferedid: subjoffid,
									studentid: studid,
									gradingscaleid: gscaleid
								},
								success: function(result){
									var ret = JSON.parse(result);
									if(ret.length) {
										$.each(ret, function(key, value) {
											$('#th-student-name').html(name);
											$('#th-year-period').html(yrname + " " + prdname);
										});
									} else {
										$('#th-student-name').html('');
										$('#th-year-period').html('');
									}
									
									var gsid = global_GSCALEID;
									var clstudid = global_CLSTUDID;
									
									$.ajax({
									    async: false,
										type:'GET',
										url: '../../model/forms/academic/classlist/classlist-controller.php',
										data:{
											type : 'GRADING_SCALE_INFO',
											action: 'FETCH',
											gradingscaleid : gsid,
											subjofferedid : subjoffid
										},
										success: function(retdata){
											var parentid = [];
											var childid = [];
											var ret = JSON.parse(retdata, function(key, value) { 
												if (value.GSD_PARENT_ID === "0" ) parentid.push(value); 
												return value;})
											var data = JSON.parse(retdata);
											var tbl = '';
											var regex = new RegExp('0','i');
											var cnt = parseInt('0');
											var txtinputvalue = '';
											var tdinputvaluepercentage = '';
											var tdsubavgnextid = '';
											if(data.length) {
													$.each(parentid, function(key, value) {
														childid = [];
														JSON.parse(retdata, function(keys, val) { 
														    if (val.GSD_PARENT_ID === value.GSD_DET_ID) childid.push(val);
															return val;
                                                        })
														tbl += "<table id='table-" + ReplaceSpecialCharacters(value.GSD_NAME) + "' class='table table-hover table-responsive table-bordered'>" + 
															   "<thead class='table-primary'>" +
															   "<tr>" +
															   "<th scope='col' colspan='" + (childid.length + 1) + "' class='text-primary text-decoration-underline text-center' id='th-" + ReplaceSpecialCharacters(value.GSD_CODE) + "-" + value.GS_ID + "-" + value.GSD_DET_ID + "' name='" + value.GSD_PERCENTAGE + "'>" + value.GSD_NAME + " (" + value.GSD_PERCENTAGE + "%)</th>" +							   
															   "</tr>" +
															   "<tr>";
																$.each(childid, function(k, v) {
																		tbl += "<th scope='col' id='th-" + ReplaceSpecialCharacters(v.GSD_CODE) + "-" + v.GS_ID + "-" + v.GSD_DET_ID + "' name='" + v.GSD_PERCENTAGE + "' class='text-center'>" + v.GSD_CODE + " (" + v.GSD_PERCENTAGE + "%)</th>";
																});
														tbl += 	"<th scope='col' class='text-danger text-center'>AVERAGE</th>";
														tbl += 	"</tr></thead>";
														tbl +=  "<tbody><tr>";
																$.each(childid, function(keys, v) {
																	tbl += "<td class='m-0 p-0'>" + 
																			"<input type='text' id='txt-" + ReplaceSpecialCharacters(v.GSD_CODE) + "-" + v.GS_ID + "-" + v.GSD_DET_ID + "-" + v.GSD_PERCENTAGE + "-" + v.GSD_PARENT_ID + "' name='" + v.GSD_PERCENTAGE + "' class='form-control border-0 rounded-0 text-center' value='0' required='_required'/></td>";
																	txtinputvalue += "#txt-" + ReplaceSpecialCharacters(v.GSD_CODE) + "-" + v.GS_ID + "-" + v.GSD_DET_ID + "-" + v.GSD_PERCENTAGE + "-" + v.GSD_PARENT_ID + ",";
																})
														tbl += "<td rowspan='2' id='tdnext-" + ReplaceSpecialCharacters(value.GSD_CODE) + "-" + value.GS_ID + "-" + value.GSD_DET_ID + "-" + value.GSD_PERCENTAGE + "-" + value.GSD_PARENT_ID + "' class='table-primary text-danger text-center fw-medium align-middle'>" +
															   "0</td>";
														tbl += "</tr>";
														tbl += "<tr>";
																$.each(childid, function(keys, v) {
																	tbl += "<td id='tdsub-" + ReplaceSpecialCharacters(v.GSD_CODE) + "-" + v.GS_ID + "-" + v.GSD_DET_ID + "-" + v.GSD_PERCENTAGE + "-" + v.GSD_PARENT_ID + "' name='" + v.GSD_PERCENTAGE + "' class='table-primary text-danger text-center p-1' style='font-size: .75rem'></td>";
																	tdinputvaluepercentage += "#tdsub-" + ReplaceSpecialCharacters(v.GSD_CODE) + "-" + v.GS_ID + "-" + v.GSD_DET_ID + "-" + v.GSD_PERCENTAGE + "-" + v.GSD_PARENT_ID + ",";
																})
														tbl += "</tr>" +
															   "</tbody>" +
															   "</table>";
														tdsubavgnextid += "#tdnext-" + ReplaceSpecialCharacters(value.GSD_CODE) + "-" + value.GS_ID + "-" + value.GSD_DET_ID + "-" + value.GSD_PERCENTAGE + "-" + value.GSD_PARENT_ID + ",";
													});
												var txtinputvalueid = txtinputvalue.substring(0, (txtinputvalue.length - 1));
												tdinputvaluepercentage = tdinputvaluepercentage.substring(0, (tdinputvaluepercentage.length - 1));
												tdsubavgnextid = tdsubavgnextid.substring(0, (tdsubavgnextid.length - 1));
												
												global_TXTINPUTVALUE = txtinputvalueid;
												global_TDINPUTVALUEPERCENTAGE = tdinputvaluepercentage;
												global_TDSUBAVGNEXTID = tdsubavgnextid;
												
												$('#div-tbl-gscale').html(tbl);
												$('#p-final-average-status').hide();
												$('#cbo-final-average-status').show();
												GetStudentAcademicRecordDetail(txtinputvalueid,
																					tdsubavgnextid,
																					tdinputvaluepercentage,
																					parseInt(global_CLSTUDID),
																					parseInt(global_STUDASSID),
																					parseInt(global_SUBJOFFID));
												const txtinputtext = $('input:text');
												txtinputtext.keyup(function(event){
													if (event.shiftKey == true) {
														event.preventDefault();
													}
													if ((event.keyCode >= 48 && event.keyCode <= 57) || 
														(event.keyCode >= 96 && event.keyCode <= 105) || 
														event.keyCode == 13 ||
														event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 37 ||
														event.keyCode == 39 || event.keyCode == 46 || event.keyCode == 190) {
														if (event.keyCode == 8) {
															if ($(this).val().length < 1){
																$(this).val(0);
																$(this).select();
															}
														}
														var row = $(this).closest('tr');
														var rownext = row.next('tr');
														var col=row.find('td');
														var cnt= col.length - 1;
														var finalvaluenext = 0;
														for(x = 0; x <= cnt; x++ ) 
														{
															if (x == cnt){
																var inval = row.find('td:eq(' + (x).toString() + ')');
																inval.html(finalvaluenext.toFixed(2).toString() + "%");
																
															} else {
																var col=row.find('td:eq(' + (x).toString() + ')');
																var inval=col.find('input:text');
																var colnext=rownext.find('td:eq(' + (x).toString() + ')');
																var invalpct = inval.attr('id');
																var invalpctarr = invalpct.split('-');
																var colnextpct = ((parseFloat(invalpctarr[4]) / 100) * parseFloat(inval.val()));
																finalvaluenext = (parseFloat(finalvaluenext) + parseFloat(colnextpct));
																colnext.html(colnextpct.toFixed(2).toString() + ' %');
															}
														}
														var tdsubavgnextstr = global_TDSUBAVGNEXTID;
														//var tdsubavgnextstr = sessionsubavgnextid.substring(0, (sessionsubavgnextid.length - 1));
														var subavgnextidarr = tdsubavgnextstr.split(',');
														var finalavgnext = 0;
														for(i=0; i < subavgnextidarr.length; i++){
															finalavgnext = (parseFloat(finalavgnext) + parseFloat($(subavgnextidarr[i]).html()));
														}
														$('#b-final-average').html(finalavgnext.toFixed(2).toString() + "%");
														
														if (parseFloat(global_GSCALEPASSSCOREID) > 0) {
															if (parseFloat(finalvaluenext) >= parseFloat(global_GSCALEPASSSCOREID)){
																//$('#p-final-average-status').show();
																//$('#cbo-final-average-status').hide();
																//$('#p-final-average-status').html('PASSED');
																$('#p-final-average-status').hide();
																$('#p-final-average-status').html('');
																$('#cbo-final-average-status').show();
																//$('#cbo-final-average-status option:selected').text($.session.get('RESULTTYPE'));
																//$("#cbo-final-average-status option:contains(" + $.session.get('RESULTTYPE').toString() + ")").attr('selected', 'selected');
																
															} else {
																$('#p-final-average-status').hide();
																$('#cbo-final-average-status').show();
																//$('#cbo-final-average-status option:selected').text($.session.get('RESULTTYPE'));
																//$("#cbo-final-average-status option:contains(" + $.session.get('RESULTTYPE').toString() + ")").attr('selected', 'selected');
																$('#p-final-average-status').html('');
															}
															
															$("#cbo-final-average-status option:contains('" + global_RESULTTYPE.toString() + "')").attr('selected', 'selected');
															global_RESULTTYPE = $('#cbo-final-average-status option:selected').text();
															
														} else {
															//$('#p-final-average-status').show();
															$('#cbo-final-average-status').hide();
															$('#p-final-average-status').show();
															//$('#cbo-final-average-status option:selected').text($.session.get('RESULTTYPE'));
															$('#p-final-average-status').html('PASSING SCORE NOT SET');
															global_RESULTTYPE = '';
														}
													} else {
														event.preventDefault();
													}

													if($(this).val().indexOf('.') !== -1 && event.keyCode == 190){
														event.preventDefault();
													}
												});
												txtinputtext.keydown(function(event){
													if (event.shiftKey == true) {
														event.preventDefault();
													}
													if ((event.keyCode >= 48 && event.keyCode <= 57) || 
														(event.keyCode >= 96 && event.keyCode <= 105) ||
														event.keyCode == 13 ||
														event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 37 ||
														event.keyCode == 39 || event.keyCode == 46 || event.keyCode == 190) {
														
													} else {
														event.preventDefault();
													}

													if($(this).val().indexOf('.') !== -1 && event.keyCode == 190){
														event.preventDefault();
													}
												});
											} else {
												$('#div-tbl-gscale').html("<p style='font-size: 13px;font-family: Roboto, sans-serif;font-weight: normal;text-decoration: none;color: black;padding: 0;margin: 0;'>None</p>");
											}
										}
									});
									$('#master-modal').modal({backdrop: 'static', keyboard: false});														
									$('#master-modal').modal('show');
								}
							});
						});
						$('#tbl-header-student-list').show();
				}
			})
		})
	}
	
	$('#confirmation').on('hidden.bs.modal', function () {
		try { 
			//$('#confirmation-yes').off();
			$('#confirmation-msg').empty();
			$('#confirmation-yes').text('Yes');
			$('#confirmation-no').text('No');
			$('#confirmation-cancel').text('Cancel');
		}
		catch(e) {  //We can also throw from try block and catch it here
			console.error(e);
		}
		finally {
			console.log('We do cleanup here');
		}
    });
	
	confirmationno.on('click',function(e) {
		confirmationno.modal('hide');
	});
	confirmationyes.on('click',function(e) {
		confirmationno.modal('hide');
	});
	btnsearch.on('click',function(e) {
		//$('#confirmation').modal({backdrop: 'static', keyboard: false});	
		//$('#confirmation').modal('show');
		cboacadcrse.change();
	});
	btnBack.on('click',function(e) {
		$('#div-student').hide();
		$('#div-header').show();
		$('#btnsearch').click();
	});
	btnclose.on('click',function(e) {
		$('#master-modal').modal('hide');
	});
	btncancel.on('click',function(e) {
		$('#master-modal').modal('hide');
	});
	btnsave.on('click',function() 
	{
		var schlacadgradscaleid = parseInt(global_GSCALEID);
		var schlstudid = parseInt(global_CLSTUDID);
		var schlenrollsubjoffid = parseInt(global_SUBJOFFID);
		var schlstudacadrecdetinputvalueid = global_TXTINPUTVALUE;
		var schlstudacadrecid = parseInt(global_STUDACADRECID);
		var schlstudacadrecdetid = parseInt(global_STUDACADRECDETID);
		var schlenrollasssmsid = parseInt(global_STUDASSID);
		
		global_RESULTTYPE = $('#cbo-final-average-status option:selected').text();
		//alert($('#cbo-final-average-status option:selected').text());
		var schlstudacadrecdetresulttype = $('#cbo-final-average-status option:selected').text();//$.session.get('RESULTTYPE');
		var schlsignid = parseInt(global_SIGNID);
		
		var schlsignuserid = parseInt(global_SIGNUSERID);
		var txtinputvaluearr = schlstudacadrecdetinputvalueid.split(',');
		var schlstudacadrecdetrecords = '';
		for(i=0; i < txtinputvaluearr.length; i++){
			var gsdetid = txtinputvaluearr[i].split('-');
			schlstudacadrecdetrecords += gsdetid[3] + ':' + $(txtinputvaluearr[i]).val() + ',';
		}
		
		schlstudacadrecdetrecords = schlstudacadrecdetrecords.substring(0, (schlstudacadrecdetrecords.length - 1));
		$.ajax({
			type: 'GET',
			url: '../../model/forms/academic/classlist/classlist-controller.php',
			data:{
				type : 'MANAGE_STUDENT_GRADES',
				action: 'MANAGE',
				mode: 'MANAGE',
				schlstudid: schlstudid,
				schlacadgradscaleid: schlacadgradscaleid,
				schlenrollsubjoffid: schlenrollsubjoffid,
				schlenrollasssmsid: schlenrollasssmsid,
				schlstudacadrecid: schlstudacadrecid,
				schlstudacadrecdetid: schlstudacadrecdetid,
				schlstudacadrecdetresulttype: schlstudacadrecdetresulttype,
				schlsignid: schlsignid,
				schlsignuserid: schlsignuserid,
				schlstudacadrecdetrecords: schlstudacadrecdetrecords,
				reqstatus: 0
			},
			success: function(result)
			{
				var data = JSON.parse(result);
				if(data.length) {
					$.each(data, function(key, value) {
							global_STUDACADRECID = value.ACADRECID;
							global_STUDACADRECDETID = value.ACADRECDETID;
							schlstudacadrecid = global_STUDACADRECID;
							schlstudacadrecdetid = global_STUDACADRECDETID;
					});
				}
				var ret = result;
				var colstat = global_TDCOLSTATUS;
				var tdproc = global_TDPROCESS;
				var tdbtnviewstudent = global_BTSTUDENTID;
				$('#' + global_CURRENTSTUDACADAVG).html($('#b-final-average').html());
				if (parseFloat(global_GSCALEPASSSCOREID) > 0) {
					if (parseFloat($('#b-final-average').html()) >= parseFloat(global_GSCALEPASSSCOREID)){
						//$('#' + $.session.get('CURRENTSTUDACADAVGSTATUS')).html($('#p-final-average-status').html());
						$('#' + global_CURRENTSTUDACADAVGSTATUS).html($('#cbo-final-average-status option:selected').text());
						//alert($('#b-final-average').html() + ' : ' + $.session.get('GSCALEPASSSCOREID').toString());
					} else {
						$('#' + global_CURRENTSTUDACADAVGSTATUS).html($('#cbo-final-average-status option:selected').text());
						//alert($('#b-final-average').html() + ' : ' + $.session.get('GSCALEPASSSCOREID').toString() + ' | ' + $('#cbo-final-average-status option:selected').text());
					}
				} else {
					$('#' + global_CURRENTSTUDACADAVGSTATUS).html($('#p-final-average-status').html());
				}
				if (schlstudacadrecdetid.length <= 0 || parseInt(schlstudacadrecdetid) <= 0){
					var ttlencoded = (parseInt(global_NOOFSTUDENTENCODED) + 1);
					var ttlnotencoded = (parseInt(global_TTLNOOFENROLLEDSTUDENT) - (parseInt(global_NOOFSTUDENTENCODED) + 1));
					$('#' + global_INPUTNOOFSTUDENTENCODED).prop('name',ttlencoded);
					global_NOOFSTUDENTENCODED = ttlencoded;
					
					if (parseFloat(ttlnotencoded) > 0){
						$('#' + global_PNOTYETENCODED).html(ttlnotencoded + ' STUDENT NOT YET ENCODED');
					} else {
						var tdprocess = '';
						var elementidarr = global_BTSTUDENTID.split('-');
						if (parseInt(elementidarr[2]) == 0) {
							_ret = '1';
						} else if (parseInt(elementidarr[2]) == 2) {
							_ret = '1';
						} else if (parseInt(elementidarr[2]) == 5) {
							_ret = '6';
						} else if (parseInt(elementidarr[2]) == 7) {
							_ret = '6';
						} else {
							_ret = '0';
						}
						global_STUDACADRECREQSTATUS = _ret;
						var elementid = elementidarr[0] + '-' + elementidarr[1] + '-' + _ret + '-' + elementidarr[3];
						if (global_GSCALEID > 0){
							if (global_GSCALEPASSSCOREID.length <= 0 || global_GSCALEPASSSCOREID == '' || global_GSCALEPASSSCOREID == '0' || global_GSCALEPASSSCOREID == 0){
									tdprocess = "<div class='text-danger' " +
														   "id='" + elementid + "' " +
														   "name='" + global_BTSTUDENTNAME + "'>" +
														   "PASSING SCORE NOT SET" +
														"</div>";
							} else {
								if (parseInt(elementidarr[2]) == 0) {
									tdprocess = "<button id='" + elementid + "' class='btn btn-sm btn-success btnsubmit' " +
															"name='" + global_BTSTUDENTNAME + "'>Submit" +																
														"</button>";
									$('#' + colstat).html('FOR SUBMISSION');
									//colstat.html('FOR SUBMISSION');
								} else if (parseInt(elementidarr[2]) == 1) {
									tdprocess = "<div class='text-danger' " +
													   "id='" + elementid + "' " +
													   "name='" + global_BTSTUDENTNAME + "'>" +
													"Submitted" +
												"</div>";
									$('#' + colstat).html('FOR APPROVAL');
									//colstat.html('FOR APPROVAL');
								} else if (parseInt(elementidarr[2]) == 2) {
									tdprocess = "<button id='" + elementid + "' class='btn btn-sm btn-danger btnsubmit' " +
															"name='" + global_BTSTUDENTNAME + "'>Re-Submit" +																
														"</button>";
									$('#' + colstat).html('DENIED');
									//colstat.html('DENIED');
								} else if (parseInt(global_STUDACADRECREQSTATUS) >= 3 && parseInt(global_STUDACADRECREQSTATUS) <= 4) {
									tdprocess = "<div class='text-danger' " +
													   "id='" + elementid + "' " +
													   "name='" + global_BTSTUDENTNAME + "'>" +
													"On Process" +
												"</div>";
									$('#' + colstat).html('ON PROGRESS');
									//colstat.html('ON PROGRESS');
								} else if (parseInt(elementidarr[2]) == 5) {
									tdprocess = "<button id='" + elementid + "' class='btn btn-sm btn-success btnsubmit' " +
															"name='" + global_BTSTUDENTNAME + "'>Request(Edit Grades)" +																
														"</button>";
									$('#' + colstat).html('APPROVED');
									//colstat.html('APPROVED');
								} else if (parseInt(elementidarr[2]) == 6) {
									tdprocess = "<div class='text-danger' " +
													   "id='" + elementid + "' " +
													   "name='" + global_BTSTUDENTNAME + "'>" +
													"Request(Edit Grades) Submitted" +
												"</div>";
												//alert(tdprocess);
									$('#' + colstat).html('FOR APPROVAL(EDIT GRADES)');
									//colstat.html('FOR APPROVAL(EDIT GRADES)');
								} else if (parseInt(elementidarr[2]) == 7) {
									tdprocess = "<button id='" + elementid + "' class='btn btn-sm btn-danger btnsubmit' " +
														"name='" + global_BTSTUDENTNAME + "'>Re-Submit Request(Edit Grades)" +																
													"</button>";
									$('#' + colstat).html('REQUEST DENIED(EDIT GRADES)');
									//colstat.html('REQUEST DENIED(EDIT GRADES)');
								} else {
									tdprocess = "<button id='" + elementid +  "' class='btn btn-sm btn-success btnsubmit' " +
														"name='" + global_BTSTUDENTNAME + "'>Submit" +																
													"</button>";
									$('#' + colstat).html('FOR SUBMISSION');
									//colstat.html('FOR SUBMISSION');
								}
							}
						} else {
							tdprocess = "<div class='text-danger' " +
												"id='" + elementid + "' " +
												"name='" + global_BTSTUDENTNAME + "'>" +
												"NO ASSIGNED (GS)" +
											"</div>";
						}
										
						$('#' + tdproc).html(tdprocess);
						//ViewStudent();
					}
				} 
				//$("#div-offered-subject").load(location.href + " #div-offered-subject");
				
				$('#b-final-average').html('0%');
				divmessage.html('');
				$('#master-modal').modal('hide');
			}
		});
	});
	function GetDepartmentSignatories(schlenrollsubjoffid)
	{	
		$.ajax({
			async: false,
			type:'GET',
			url: '../../model/forms/academic/classlist/classlist-controller.php',
			data:{
				type : 'GET_DEPARTMENT_SIGNATORIES',
				action: 'FETCH',
				schlenrollsubjoffid : schlenrollsubjoffid
			},
			success: function(result){
				var data = JSON.parse(result);
				if(data.length) {
					global_deptsignatories = '';
					$.each(data, function(key, value) {
							global_deptsignatories = value.SIGNATORIES_ID.trim();
					});
				} else {
					global_deptsignatories = '';
				}
			}
		});
	}
	function BtnSubmitClick()
	{
		const btnsubmit = $('.btnsubmit');
		btnsubmit.click(function() {
			var _ret;
			var currentRow=$(this).closest("tr");
			var coltdstatus=currentRow.find("td:eq(9)");
			var coltdviewstudent = currentRow.find("td:eq(10)");
			var coltdviewstudentbutton= coltdviewstudent.find(".btnviewstudent");
			var coltdviewstudentinputhidden = coltdviewstudent.find('input:hidden');
			var coltdviewstudentinputhiddenid = coltdviewstudentinputhidden.attr('id');
			var coltdviewstudentinputhiddenname = coltdviewstudentinputhidden.attr('name');
			var coltdviewstudentinputhiddenvalue = coltdviewstudentinputhidden.val();
			var coltdprocess=currentRow.find("td:eq(11)");
			var coltdprocessbutton=$(this);
			//alert(coltdprocess.html);
			//var coltdprocessparagraph=$.session.get('TDDIVNOTYETENCODED');//coltdprocess.find("div");
			var btnreqstatus = coltdprocess.find(".btnsubmit");
			var btnreqstatusarr = btnreqstatus.attr('id').split('-');
			var currentreqstatus = btnreqstatusarr[2];
			var colstatus;
			var tdprocess = '';
			var tdviewstudent = '';
			var btnviewstudid = coltdviewstudentbutton.attr('id');
			var btnviewstudname = coltdviewstudentbutton.attr('name');
			var btnviewstudval = coltdviewstudentbutton.val();
			var viewstudbuttonarr = btnviewstudid.split('-');//viewstudbutton.split('-');
			var signatoriesarr;
			GetDepartmentSignatories(parseInt(viewstudbuttonarr[1]));
			if (global_deptsignatories != undefined){	
				if (global_deptsignatories.length <= 0 || global_deptsignatories == ''){
					// NO SIGNATORIES
					signatoriesarr = '';
					alert('NO ASSIGN APPROVER');
					return;
				} else {
					signatoriesarr = global_deptsignatories.split(',');
				}
			} else {
				// NO SIGNATORIES
				signatoriesarr = '';
				alert('NO ASSIGN APPROVER');
				return;
			}
			$.ajax({
				async: false,
				type:'GET',
				url: '../../model/forms/academic/classlist/classlist-controller.php',
				data:{
					type : 'GET_STUDENT_GRADES_STATUS',
					action: 'FETCH',
					mode: 'PROCESS',
					schlstudid: '0',
					schlacadgradscaleid: '0',
					schlenrollsubjoffid: parseInt(viewstudbuttonarr[1]),
					schlenrollasssmsid: '0',
					schlstudacadrecid: parseInt(viewstudbuttonarr[5]),
					schlstudacadrecdetid: '0',
					schlstudacadrecdetresulttype: '',
					schlsignid: '0',
					schlsignuserid: '0',
					schlstudacadrecdetrecords: '',
					reqstatus: '0'
				},
				success: function(result){
					if (JSON.parse(result))
					{
						var data = JSON.parse(result);
						var schlsignid;
						var schlsignuserid;
						if(data.length) {
							$.each(data, function(key, value) {
								if (parseInt(currentreqstatus) != parseInt(value.REQ_STATUS)) {
									if (parseInt(value.REQ_STATUS) == 3 || parseInt(value.REQ_STATUS) == 2)
									{
										alert('Failed to cancel, Already approved/Denied by your approver. Please refresh your Web Browser!');
										exit();
									}
								}
								if (parseInt(value.REQ_STATUS) == 0) {
									_ret = '1';
									if (signatoriesarr.length > 0){
										schlsignid = parseInt(signatoriesarr[0]);
										schlsignuserid = parseInt(signatoriesarr[0]);
									} else {
										schlsignid = 0;
										schlsignuserid = 0;
									}
								} else if (parseInt(value.REQ_STATUS) == 1) {
									_ret = '0';
									schlsignid = 0;
									schlsignuserid = 0;
								} else if (parseInt(value.REQ_STATUS) == 2) {
									_ret = '1';
									if (signatoriesarr.length > 0){
										schlsignid = parseInt(signatoriesarr[0]);
										schlsignuserid = parseInt(signatoriesarr[0]);
									} else {
										schlsignid = 0;
										schlsignuserid = 0;
									}
								} else if (parseInt(value.REQ_STATUS) == 5) {
									_ret = '6';
									if (signatoriesarr.length > 0){
										schlsignid = parseInt(signatoriesarr[0]);
										schlsignuserid = parseInt(signatoriesarr[0]);
									} else {
										schlsignid = 0;
										schlsignuserid = 0;
									}
								} else if (parseInt(value.REQ_STATUS) == 7) {
									_ret = '6';
									schlsignid = parseInt(signatoriesarr[0]);
									schlsignuserid = parseInt(signatoriesarr[0]);
								} else {
									_ret = '0';
								}
								global_STUDACADRECREQSTATUS = _ret;
								//var btnviewstudidarr = btnviewstudid.split('-');
								//$('#' + btnviewstudid).prop("id", btnviewstudidarr[0] + "-" + btnviewstudidarr[1] + "-"  + btnviewstudidarr[2] + "-" + _ret + "-"  + btnviewstudidarr[4] + "-"  + btnviewstudidarr[5]);
								$.ajax({
									type:'GET',
									url: '../../model/forms/academic/classlist/classlist-controller.php',
									data:{
										type : 'MANAGE_STUDENT_GRADES',
										action: 'MANAGE',
										mode: 'PROCESS',
										schlstudid: 0,
										schlacadgradscaleid: 0,
										schlenrollsubjoffid: parseInt(viewstudbuttonarr[1]),
										schlenrollasssmsid: 0,
										schlstudacadrecid: parseInt(viewstudbuttonarr[5]),
										schlstudacadrecdetid: 0,
										schlstudacadrecdetresulttype: '',
										schlsignid: parseInt(schlsignid),
										schlsignuserid: parseInt(schlsignuserid),
										schlstudacadrecdetrecords: '',
										reqstatus: parseInt(_ret)
									},
									success: function(result){
										//var ret = result;
										var btnid = coltdprocessbutton.attr('id');
										var btnname = coltdprocessbutton.attr('name');
										var btnarrid = btnid.split('-');
										colstatus = btnarrid[2];
										if (parseInt(_ret) == 0) {
											tdprocess += "<button id='btnsubmit-" + btnarrid[1] + "-" + _ret + "-"  + btnarrid[3] + "' class='btn btn-sm btn-success btnsubmit' " +
																	"name='" + btnname + "'>Submit" +																
																"</button>";
											coltdstatus.html('FOR SUBMISSION');
										} else if (parseInt(_ret) == 1) {
											tdprocess += "<button id='btnsubmit-" + btnarrid[1] + "-" + _ret + "-"  + btnarrid[3] + "' class='btn btn-sm btn-danger btnsubmit' " +
																	"name='" + btnname + "'>Cancel" +																
																"</button>";
											coltdstatus.html('FOR APPROVAL');
										} else if (parseInt(_ret) == 2) {
											tdprocess += "<button id='btnsubmit-" + btnarrid[1] + "-" + _ret + "-"  + btnarrid[3] + "' class='btn btn-sm btn-danger btnsubmit' " +
																	"name='" + btnname + "'>Re-Submit" +																
																"</button>";
											coltdstatus.html('DENIED');
										} else if (parseInt(_ret) >= 3 && parseInt(_ret) <= 4) {
											tdprocess += "<div class='text-danger' " +
															   "id='premainingstudnotencoded-" + btnarrid[1] + "-" + _ret + "-"  + btnarrid[3] + "' " +
															   "name='" + btnname + "'>" +
															"On Process" +
														"</div>";
											coltdstatus.html('ON PROGRESS');
										} else if (parseInt(_ret) == 5) {
											tdprocess += "<button " +
																	"id='btnsubmit-" + btnarrid[1] + "-" + _ret + "-"  + btnarrid[3] + "' class='btn btn-sm btn-success btnsubmit' " +
																	"name='" + btnname + "'>Request(Edit Grades)" +																
																"</button>";
											coltdstatus.html('APPROVED');
										} else if (parseInt(_ret) == 6) {
											tdprocess += "<div class='text-danger' " +
															   "id='premainingstudnotencoded-" + btnarrid[1] + "-" + _ret + "-"  + btnarrid[3] + "' " +
															   "name='" + btnname + "'>" +
															   "Request Submitted(Edit Grades)" +
														"</div>";
											coltdstatus.html('FOR APPROVAL(EDIT GRADES)');
										} else if (parseInt(_ret) == 7) {
											tdprocess += "<button id='btnsubmit-" + btnarrid[1] + "-" + ret + "-"  + btnarrid[3] + "' class='btn btn-sm btn-danger btnsubmit' " +
																"name='" + btnname + "'>Re-Submit Request(Edit Grades)" +																
															"</button>";
											coltdstatus.html('REQUEST DENIED(EDIT GRADES)');
										} else {
											tdprocess += "<button id='btnsubmit-" + btnarrid[1] + "-" + ret + "-"  + btnarrid[3] + "' class='btn btn-sm btn-success btnsubmit' " +
																"name='" + btnname + "'>Submit" +																
															"</button>";
											coltdstatus.html('FOR SUBMISSION');
										}
										coltdprocess.html(tdprocess);
										tbodystudent.html("<tr><td colspan='99' class='text-danger text-center'> No Record Found </td></tr>");
										$('#btnsearch').click();
									}
									
								});
							});
						}
					}
				}
			});
		});
	}
	
	//Get Grading Scale Detail Percentage
	function GetGradingScaleDetailPercentage(gscaleid,subjofferedid)
	{
		//global_GSDETPERCENTAGE = '';
		$.ajax({
			async: false,
			type:'GET',
			url: '../../model/forms/academic/classlist/classlist-controller.php',
			data:{
				type : 'GRADING_SCALE_INFO',//'CLASSLIST_GRADING_SCALE_INFO',
				action: 'FETCH',
				subjofferedid : subjofferedid,
				gradingscaleid : gscaleid
			},
			success: function(result){
				global_GSDETPERCENTAGE = result;
			}
		});
	}
	//Get Student Academic Records(Grades)
	function GetStudentAcademicRecordDetail(inputdata,
									inputdataparent,
									inputvaluepercentage,
									schlstudid,
									schlenrollasssmsid,
									subjofferedid)
	{
		$.ajax({
			async: false,
			type:'GET',
			url: '../../model/forms/academic/classlist/classlist-controller.php',
			data:{
				type : 'GET_STUDENT_GRADES_DETAIL',
				action: 'FETCH',
				schlstudid : schlstudid,
				schlenrollasssmsid: schlenrollasssmsid,
				subjofferedid: subjofferedid
			},
			success: function(result)
			{
				const data = JSON.parse(result);
				var rec_gsdetlbl = '';
				var rec_gsdetcode = '';
				var rec_gsid = '';
				var rec_gsdetid = '';
				var rec_gradpctval = '';
				
				if(data.length) {
					var rec_arr = inputdata.split(',');
					for(i=0; i < rec_arr.length; i++){
						var rec_final_arr = rec_arr[i].split('-');
						rec_gsdetlbl = rec_final_arr[0];
						rec_gsdetcode = rec_final_arr[1];
						rec_gsid = parseInt(rec_final_arr[2]);
						rec_gsdetid = parseInt(rec_final_arr[3]);
						rec_gradpctval = parseInt(rec_final_arr[4]);
						
						$.each(data, function(key, value) {
							global_STUDACADRECDETREC = value.STUD_ACAD_DET_REC;
							global_STUDACADRECDETID = value.STUD_ACAD_REC_DET_ID;
							global_RESULTTYPE = value.RESULT_TYPE;
							var rec_db_arr = value.STUD_ACAD_DET_REC.split(',');
							for(d=0; d < rec_db_arr.length; d++){
								var rec_db_final_arr = rec_db_arr[d].split(':');
								if (parseInt(rec_db_final_arr[0]) == parseInt(rec_gsdetid)){
									$(rec_arr[i]).val(parseFloat(rec_db_final_arr[1]));
								}
							}
						});
					}
					let ttlfinalavg = 0;
					var inputvaluepercentage_arr = inputvaluepercentage.split(',');
					var parent_arr = inputdataparent.split(',');
					for(i=0; i < parent_arr.length; i++){
						let ttlsubavg = 0;
						var parent_final_arr = parent_arr[i].split('-');
						for(d=0; d < rec_arr.length; d++){
							var parent_rec_final_arr = rec_arr[d].split('-');
							if (parseInt(parent_final_arr[3]) == parseInt(parent_rec_final_arr[5])){
								ttlsubavg += (parseFloat($(rec_arr[d]).val()) * (parseFloat(parent_rec_final_arr[4]) / 100));
								$(inputvaluepercentage_arr[d]).html((parseFloat($(rec_arr[d]).val()) * (parseFloat(parent_rec_final_arr[4]) / 100)).toFixed(2).toString() + ' %');
							}
						}
						ttlfinalavg += ttlsubavg;
						$(parent_arr[i]).html(ttlsubavg.toFixed(2).toString() + '%');
					}
					
					$('#b-final-average').html(ttlfinalavg.toFixed(2).toString() + '%');
					
					if (parseFloat(global_GSCALEPASSSCOREID) > 0) {
						if (parseFloat(ttlfinalavg) >= parseFloat(global_GSCALEPASSSCOREID)){
							//$('#p-final-average-status').show();
							//$('#cbo-final-average-status').hide();
							//$('#p-final-average-status').html('PASSED');
							$('#p-final-average-status').hide();
							$('#p-final-average-status').html('');
							$('#cbo-final-average-status').show();
							//$('#cbo-final-average-status option:selected').text($.session.get('RESULTTYPE'));
							//$("#cbo-final-average-status option:contains(" + $.session.get('RESULTTYPE').toString() + ")").attr('selected', 'selected');
							
						} else {
							$('#p-final-average-status').hide();
							$('#cbo-final-average-status').show();
							//$('#cbo-final-average-status option:selected').text($.session.get('RESULTTYPE'));
							//$("#cbo-final-average-status option:contains(" + $.session.get('RESULTTYPE').toString() + ")").attr('selected', 'selected');
							$('#p-final-average-status').html('');
						}
						
						$("#cbo-final-average-status option:contains('" + global_RESULTTYPE.toString() + "')").attr('selected', 'selected');
						global_RESULTTYPE = $('#cbo-final-average-status option:selected').text();
						
					} else {
						//$('#p-final-average-status').show();
						$('#cbo-final-average-status').hide();
						$('#p-final-average-status').show();
						//$('#cbo-final-average-status option:selected').text($.session.get('RESULTTYPE'));
						$('#p-final-average-status').html('PASSING SCORE NOT SET');
						global_RESULTTYPE = '';
					}
					$('#div-message').html('');
				} else {
					global_STUDACADRECDETREC = 0;
					global_STUDACADRECDETID = 0;
					global_RESULTTYPE = '';
					$('#b-final-average').html('0%');
				}
			}
		});
	}
	
	function Initialize() 
	{
		$('#tbl-header-student-list').hide();
		$('#div-student-academic-grades').hide();
		
		$.ajax({
			type:'GET',
			url: '../../model/forms/academic/classlist/classlist-controller.php',
			data:{
				type : 'ACADLEVEL',
				action: 'FETCH'
			},
			beforeSend: function (status) {
				$('#p-title').html('List of Enrolled Student');
				divmessage.html('');
				tbodystudent.html("<tr><td colspan='99' class='text-danger text-center'> No Record Found </td></tr>");
			},
			success: function(result){
				var ret = JSON.parse(result);
				if(ret.length) {
					var cboLevel = '';
					$.each(ret, function(key, value) {
						cboLevel += "<option value='" + value.ID + "'>" + value.NAME + "</option>";
					});
				} else {
					cboLevel += "<option value='0'>None</option>";
				}
				cboacadlvl.html(cboLevel);
				
				var lvlid = cboacadlvl.val();
				$.ajax({
					type:'GET',
					url: '../../model/forms/academic/classlist/classlist-controller.php',
					data:{
						type : 'ACADYEAR',
						action: 'FETCH',
						levelid : lvlid
					},
					beforeSend: function (status) {
						$('#p-title').html('List of Enrolled Student');
						divmessage.html('');
						tbodystudent.html("<tr><td colspan='99' class='text-danger text-center'> No Record Found </td></tr>");
					},
					success: function(result){
						var ret = JSON.parse(result);
						if(ret.length) {
							var cboYear = '';
							$.each(ret, function(key, value) {
								cboYear += "<option value='" + value.ID + "'>" + value.NAME + "</option>";
							});
						} else {
							cboYear += "<option value='0'>None</option>";
						}
						
						cboacadyr.html(cboYear);
						
						var yrid = cboacadyr.val();
						$.ajax({
							type:'GET',
							url: '../../model/forms/academic/classlist/classlist-controller.php',
							data:{
								type : 'ACADPERIOD',
								action: 'FETCH',
								levelid : lvlid,
								yearid: yrid
							},
							beforeSend: function (status) {
								$('#p-title').html('List of Enrolled Student');
								divmessage.html('');
								tbodystudent.html("<tr><td colspan='99' class='text-danger text-center'> No Record Found </td></tr>");
							},
							success: function(result){
								var ret = JSON.parse(result);
								if(ret.length) {
									var cboPeriod = '';
									$.each(ret, function(key, value) {
										cboPeriod += "<option value='" + value.ID + "'>" + value.NAME + "</option>";
									});
								} else {
									cboPeriod += "<option value='0'>None</option>";
								}
								
								cboacadprd.html(cboPeriod);
								
								var prdid = cboacadprd.val();
								$.ajax({
									type:'GET',
									url: '../../model/forms/academic/classlist/classlist-controller.php',
									data:{
										type : 'ACADCOURSE',
										action: 'FETCH',
										levelid : lvlid,
										yearid: yrid,
										periodid: prdid
									},
									beforeSend: function (status) {
										$('#p-title').html('List of Enrolled Student');
										divmessage.html('');
										tbodystudent.html("<tr><td colspan='99' class='text-danger text-center'> No Record Found </td></tr>");
									},
									success: function(result){
										var ret = JSON.parse(result);
										if(ret.length) {
											var cboCourse = '';
											$.each(ret, function(key, value) {
												cboCourse += "<option value='" + value.ID + "'>" + value.NAME + "</option>";
											});
										} else {
											cboCourse += "<option value='0'>None</option>";
										}
										cboacadcrse.html(cboCourse);
										
										var crseid = cboacadcrse.val();
										let lineNo = 1;
										
										// ------ OFFERED SUBJECT --------
										
										//$.session.set('ISREFRESH',0);
										$.ajax({
											async: false,
											type:'GET',
											url: '../../model/forms/academic/classlist/classlist-controller.php',
											data:{
												type : 'OFFERED_SUBJECT',
												action: 'FETCH',
												levelid : lvlid,
												yearid: yrid,
												periodid: prdid,
												courseid:crseid
											},
											beforeSend: function (status) {
												$('#p-title').html('List of Enrolled Student');
												divmessage.html('');
												tbodystudent.html("<tr><td colspan='99' class='text-danger text-center'> No Record Found </td></tr>");
												$('#tbl-header-student-list').hide();
											},
											success: function(result){
												var ret = JSON.parse(result);
												var tblOffSubject = '';
												
												if(ret.length) {
													
													$.each(ret, function(key, value) {
														
														if (value.NO_OF_STUDENT > 0){
														    var final_sched = '';
                            							    if (value.SCHEDULE === null)
                            							    {
                            							        final_sched = '';
                            							    } else {
                            							        var sched = value.SCHEDULE.replace(']||[',':').replace('[||]','-').replace(']||[',':').split('=');
                            							        final_sched = sched[1] + ' ' + sched[2];
                            							    }
															tblOffSubject += "<tr>" + 
																	  "<td class='text-center'>" + lineNo++ + "</td>" + 
																	  "<td>" + value.CODE + "</td>" + 
																	  "<td>" + value.DESC + "</td>" + 
																	  "<td class='text-center'>" + value.UNIT + "</td>" + 
																	  "<td>" + value.COURSE + "</td>" + 
																	  "<td>" + value.SECTION + "</td>" + 
																	  "<td>" + final_sched + "</td>" + 
																	  "<td>" + value.GRADING_SCALE + "</td>" + 
																	  "<td class='text-center'>" + value.NO_OF_STUDENT + "</td>" + 
																	  "<td>" + value.REQ_STATUS_NAME + "</td>" + 
																	  "<td>" +
																	  "<button type='button' " +
																					"id='btnviewstudent-" + value.OFFERED_SUBJ_SMS_ID + "-"  + value.SIGN_ID + "-" + value.REQ_STATUS + "-"  + value.SIGN_USERID + "-"  + value.STUD_ACAD_REC_ID + "' name='" + value.GSCALE_ID + "' data-backdrop='static' data-keyboard='false' class='btn  btn-sm btn-primary btnviewstudent' value='" + value.GSCALE_ID + "'>" + 
																					"Student</button>" +
																					"<input type='hidden' id='inputhiddengspassscore-'" + value.OFFERED_SUBJ_SMS_ID + "-"  + value.SIGN_ID + "' name='" + value.NO_OF_ENCODED_STUDENT + "' value='" + value.GS_PASS_SCORE + "'/>" +
																	  "</td>" +				
																	  "<td>";
																	  
																	if (value.GS_ID > 0){
																		if (value.DEPT_APPROVER.length){
																			if (value.GS_PASS_SCORE.length <= 0 || value.GS_PASS_SCORE == '' || value.GS_PASS_SCORE == '0' || value.GS_PASS_SCORE == 0){
																					tblOffSubject += "<div class='text-danger' " + 
																										   "id='premainingstudnotencoded-" + value.SIGN_ID + "-" + value.REQ_STATUS + "-"  + value.SIGN_USERID + "' " +
																										   "name='" + value.STUD_ACAD_REC_ID + "'>" +
																										   "PASSING SCORE NOT SET" +
																										"</div>";
																			} else {
																				if (parseInt(value.NO_OF_STUDENT) == parseInt(value.NO_OF_ENCODED_STUDENT)){
																					if (value.REQ_STATUS == 0) { // Submit
																						tblOffSubject += "<button " +
																												"id='btnsubmit-" + value.SIGN_ID + "-" + value.REQ_STATUS + "-"  + value.SIGN_USERID + "' class='btn btn-sm btn-success btnsubmit' " +
																												"name='" + value.STUD_ACAD_REC_ID + "'>Submit" +																
																											"</button>";
																					} else if (value.REQ_STATUS == 1) { // Submitted
																						tblOffSubject += "<button " +	
																												"id='btnsubmit-" + value.SIGN_ID + "-" + value.REQ_STATUS + "-"  + value.SIGN_USERID + "' class='btn btn-sm btn-danger btnsubmit' " +
																												"name='" + value.STUD_ACAD_REC_ID + "'>" + 
																												"Cancel" +																
																											"</button>";
																					} else if (value.REQ_STATUS == 2) { // Denied (Re-Submit)
																						tblOffSubject += "<button " +	
																												"id='btnsubmit-" + value.SIGN_ID + "-" + value.REQ_STATUS + "-"  + value.SIGN_USERID + "' class='btn btn-sm btn-danger btnsubmit' " +
																												"name='" + value.STUD_ACAD_REC_ID + "'>" + 
																												"Re-Submit" +																
																											"</button>";
																					} else if (value.REQ_STATUS >= 3 && value.REQ_STATUS <= 4) { // On Process
																						tblOffSubject += "<div class='text-danger' " + 
																										   "id='premainingstudnotencoded-" + value.SIGN_ID + "-" + value.REQ_STATUS + "-"  + value.SIGN_USERID + "' " +
																										   "name='" + value.STUD_ACAD_REC_ID + "'>" +
																										   "On Process" +
																										 "</div>";
																					} else if (value.REQ_STATUS == 5) { // Approved
																						tblOffSubject += "<button " +
																												"id='btnsubmit-" + value.SIGN_ID + "-" + value.REQ_STATUS + "-"  + value.SIGN_USERID + "' class='btn btn-sm btn-success btnsubmit' " +
																												"name='" + value.STUD_ACAD_REC_ID + "'>Request(Edit Grades)" +																
																											"</button>";
																					} else if (value.REQ_STATUS == 6) { // Request Submitted
																						tblOffSubject += "<div class='text-danger' " + 
																										   "id='premainingstudnotencoded-" + value.SIGN_ID + "-" + value.REQ_STATUS + "-"  + value.SIGN_USERID + "' " +
																										   "name='" + value.STUD_ACAD_REC_ID + "'>" +
																										"Request Submitted(Edit Grades)" +
																									"</div>";
																					} else if (value.REQ_STATUS == 7) { // Denied (Request Re-Submit)
																						tblOffSubject += "<button " +
																											"id='btnsubmit-" + value.SIGN_ID + "-" + value.REQ_STATUS + "-"  + value.SIGN_USERID + "' class='btn btn-sm btn-danger btnsubmit' " +
																											"name='" + value.STUD_ACAD_REC_ID + "'>" + 
																											"Re-Submit Request(Edit Grades)" +																
																										"</button>";
																					} else { // 8 Approved(For Edit Grades) Reset to 1)
																						tblOffSubject += "<button " +
																											"id='btnsubmit-" + value.SIGN_ID + "-" + value.REQ_STATUS + "-"  + value.SIGN_USERID + "' class='btn btn-sm btn-success btnsubmit' " +
																											"name='" + value.STUD_ACAD_REC_ID + "'>Submit" +																
																										"</button>";
																					}
																				} else {
																					 tblOffSubject += "<div class='text-danger' " + 
																										   "id='premainingstudnotencoded-" + value.SIGN_ID + "-" + value.REQ_STATUS + "-"  + value.SIGN_USERID + "' " +
																										   "name='" + value.STUD_ACAD_REC_ID + "'>";
																					 tblOffSubject += (parseInt(value.NO_OF_STUDENT) - parseInt(value.NO_OF_ENCODED_STUDENT)).toString() + " STUDENT " +
																										"NOT YET ENCODED" +
																										"</div>";
																				}
																			}
																		} else {
																				tblOffSubject += "<div class='text-danger' " + 
																										   "id='premainingstudnotencoded-" + value.SIGN_ID + "-" + value.REQ_STATUS + "-"  + value.SIGN_USERID + "' " +
																										   "name='" + value.STUD_ACAD_REC_ID + "'>" +
																										"NO ASSIGNED (APPROVER)" +
																									"</div>";
																		}
																	} else {
																			tblOffSubject += "<div class='text-danger' " + 
																									   "id='premainingstudnotencoded-" + value.SIGN_ID + "-" + value.REQ_STATUS + "-"  + value.SIGN_USERID + "' " +
																									   "name='" + value.STUD_ACAD_REC_ID + "'>" +
																									"NO ASSIGNED (GS)" +
																								"</div>";
																	}
															tblOffSubject += "</td>" + 																		  
																	  "</tr>";
														}
													});
												} else {
													tblOffSubject += "<tr><td colspan='99' class='text-danger text-center'> No Record Found </td></tr>";
												}
												
												tbodyofferedsubject.html(tblOffSubject);
												BtnSubmitClick();
												ViewStudent();
											},
											complete: function(status) {
												//Fires event once process is ompleted
											},
											error:function(status){
												$('#div-message').append('Error!');
											}
										})
									}
								})
							}
						})
					}
				})
			}
		})
	}
	
	cboacadlvl.change(function(){
		var lvlid = $(this).val();
		$.ajax({
			async: false,
			type: 'GET',
			url: '../../model/forms/academic/classlist/classlist-controller.php',
			data:{
				type : 'ACADYEAR',
				action: 'FETCH',
				levelid : lvlid
			},
			beforeSend: function (status) {
				$('#p-title').html('List of Enrolled Student');
				divmessage.html('');
				tbodystudent.html("<tr><td colspan='99' class='text-danger text-center'> No Record Found </td></tr>");
			},
			success: function(result){
				var ret = JSON.parse(result);
				if(ret.length) {
					var cboYear = '';
					$.each(ret, function(key, value) {
						cboYear += "<option value='" + value.ID + "'>" + value.NAME + "</option>";
					});
				} else {
					cboYear += "<option value='0'>None</option>";
				}
				
				cboacadyr.html(cboYear);
				cboacadyr.change();
			}
		});
	});
	cboacadyr.change(function(){
		var lvlid = cboacadlvl.val();
		var yrid = $(this).val();
		$.ajax({
			async: false,
			type:'GET',
			url: '../../model/forms/academic/classlist/classlist-controller.php',
			data:{
				type : 'ACADPERIOD',
				action: 'FETCH',
				levelid : lvlid,
				yearid: yrid
			},
			beforeSend: function (status) {
				$('#p-title').html('List of Enrolled Student');
				divmessage.html('');
				tbodystudent.html("<tr><td colspan='99' class='text-danger text-center'> No Record Found </td></tr>");
			},
			success: function(result){
				var ret = JSON.parse(result);
				if(ret.length) {
					var cboPeriod = '';
					$.each(ret, function(key, value) {
						cboPeriod += "<option value='" + value.ID + "'>" + value.NAME + "</option>";
					});
				} else {
					cboPeriod += "<option value='0'>None</option>";
				}
				
				cboacadprd.html(cboPeriod);
				cboacadprd.change();
			}
		});
	});
	cboacadprd.change(function(){
		var lvlid = cboacadlvl.val();
		var yrid = cboacadyr.val();
		var prdid = $(this).val();
		$.ajax({
			async: false,
			type:'GET',
			url: '../../model/forms/academic/classlist/classlist-controller.php',
			data:{
				type : 'ACADCOURSE',
				action: 'FETCH',
				levelid : lvlid,
				yearid: yrid,
				periodid: prdid
			},
			beforeSend: function (status) {
				$('#p-title').html('List of Enrolled Student');
				divmessage.html('');
				tbodystudent.html("<tr><td colspan='99' class='text-danger text-center'> No Record Found </td></tr>");
			},
			success: function(result){
				var ret = JSON.parse(result);
				if(ret.length) {
					var cboCourse = '';
					$.each(ret, function(key, value) {
						cboCourse += "<option value='" + value.ID + "'>" + value.NAME + "</option>";
					});
				} else {
					cboCourse += "<option value='0'>None</option>";
				}
				
				cboacadcrse.html(cboCourse);
				cboacadcrse.change();
			}
		});
	});
	cboacadcrse.change(function()
	{
		var lvlid = cboacadlvl.val();
		var yrid = cboacadyr.val();
		var prdid = cboacadprd.val();
		var crseid = $(this).val();
		let lineNo = 1;
		// ------ OFFERED SUBJECT --------
		//$.session.set('ISREFRESH',0);
		$.ajax({
			async: false,
			type:'GET',
			url: '../../model/forms/academic/classlist/classlist-controller.php',
			data:{
				type : 'OFFERED_SUBJECT',
				action: 'FETCH',
				levelid : lvlid,
				yearid: yrid,
				periodid: prdid,
				courseid:crseid
			},
			beforeSend: function (status) {
				$('#p-title').html('List of Enrolled Student');
				divmessage.html('');
				tbodystudent.html("<tr><td colspan='99' class='text-danger text-center'> No Record Found </td></tr>");
				$('#tbl-header-student-list').hide();
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
									  "<td class='text-center'>" + lineNo++ + "</td>" + 
									  "<td>" + value.CODE + "</td>" + 
									  "<td>" + value.DESC + "</td>" + 
									  "<td class='text-center'>" + value.UNIT + "</td>" + 
									  "<td>" + value.COURSE + "</td>" + 
									  "<td>" + value.SECTION + "</td>" + 
									  "<td>" + final_sched + "</td>" + 
									  "<td>" + value.GRADING_SCALE + "</td>" + 
									  "<td class='text-center'>" + value.NO_OF_STUDENT + "</td>" + 
									  "<td>" + value.REQ_STATUS_NAME + "</td>" + 
									  "<td>" +
									  "<button type='button' " +
													"id='btnviewstudent-" + value.OFFERED_SUBJ_SMS_ID + "-"  + value.SIGN_ID + "-" + value.REQ_STATUS + "-"  + value.SIGN_USERID + "-"  + value.STUD_ACAD_REC_ID + "' name='" + value.GSCALE_ID + "' data-backdrop='static' data-keyboard='false' class='btn btn-sm btn-primary btnviewstudent' value='" + value.GSCALE_ID + "'>" + 
													"Student</button>" +
													"<input type='hidden' id='inputhiddengspassscore-'" + value.OFFERED_SUBJ_SMS_ID + "-"  + value.SIGN_ID + "' name='" + value.NO_OF_ENCODED_STUDENT + "' value='" + value.GS_PASS_SCORE + "'/>" +
									  "</td>" +				
									  "<td>";
									if (value.GS_ID > 0){
										if (value.DEPT_APPROVER.length){
											if (value.GS_PASS_SCORE.length <= 0 || value.GS_PASS_SCORE == '' || value.GS_PASS_SCORE == '0' || value.GS_PASS_SCORE == 0){
													tblOffSubject += "<div class='text-danger' " +
																		   "id='premainingstudnotencoded-" + value.SIGN_ID + "-" + value.REQ_STATUS + "-"  + value.SIGN_USERID + "' " +
																		   "name='" + value.STUD_ACAD_REC_ID + "'>" +
																		   "PASSING SCORE NOT SET" +
																		"</div>";
											} else {
												if (parseInt(value.NO_OF_STUDENT) == parseInt(value.NO_OF_ENCODED_STUDENT)){
													if (value.REQ_STATUS == 0) { // Submit
														tblOffSubject += "<button id='btnsubmit-" + value.SIGN_ID + "-" + value.REQ_STATUS + "-"  + value.SIGN_USERID + "' class='btn btn-sm btn-success btnsubmit' " +
																				"name='" + value.STUD_ACAD_REC_ID + "'>Submit" +																
																			"</button>";
													} else if (value.REQ_STATUS == 1) { // Submitted
														tblOffSubject += "<button " +
																				"id='btnsubmit-" + value.SIGN_ID + "-" + value.REQ_STATUS + "-"  + value.SIGN_USERID + "' class='btn btn-sm btn-danger btnsubmit' " +
																				"name='" + value.STUD_ACAD_REC_ID + "'>" + 
																				"Cancel" +																
																			"</button>";
													} else if (value.REQ_STATUS == 2) { // Denied (Re-Submit)
														tblOffSubject += "<button " +
																				"id='btnsubmit-" + value.SIGN_ID + "-" + value.REQ_STATUS + "-"  + value.SIGN_USERID + "' class='btn btn-sm btn-danger btnsubmit' " +
																				"name='" + value.STUD_ACAD_REC_ID + "'>" + 
																				"Re-Submit" +																
																			"</button>";
													} else if (value.REQ_STATUS >= 3 && value.REQ_STATUS <= 4) { // On Process
														tblOffSubject += "<div class='text-danger' " +
																		   "id='premainingstudnotencoded-" + value.SIGN_ID + "-" + value.REQ_STATUS + "-"  + value.SIGN_USERID + "' " +
																		   "name='" + value.STUD_ACAD_REC_ID + "'>" +
																		   "On Process" +
																		 "</div>";
													} else if (value.REQ_STATUS == 5) { // Approved
														tblOffSubject += "<button " +
																				"id='btnsubmit-" + value.SIGN_ID + "-" + value.REQ_STATUS + "-"  + value.SIGN_USERID + "' class='btn btn-sm btn-success btnsubmit' " +
																				"name='" + value.STUD_ACAD_REC_ID + "'>Request(Edit Grades)" +																
																			"</button>";
													} else if (value.REQ_STATUS == 6) { // Request Submitted
														tblOffSubject += "<div class='text-danger' " +
																		   "id='premainingstudnotencoded-" + value.SIGN_ID + "-" + value.REQ_STATUS + "-"  + value.SIGN_USERID + "' " +
																		   "name='" + value.STUD_ACAD_REC_ID + "'>" +
																		"Request Submitted(Edit Grades)" +
																	"</div>";
													} else if (value.REQ_STATUS == 7) { // Denied (Request Re-Submit)
														tblOffSubject += "<button " +
																			"id='btnsubmit-" + value.SIGN_ID + "-" + value.REQ_STATUS + "-"  + value.SIGN_USERID + "' class='btn btn-sm btn-danger btnsubmit' " +
																			"name='" + value.STUD_ACAD_REC_ID + "'>" + 
																			"Re-Submit Request(Edit Grades)" +																
																		"</button>";
													} else { // 8 Approved(For Edit Grades) Reset to 1)
														tblOffSubject += "<button " +
																			"id='btnsubmit-" + value.SIGN_ID + "-" + value.REQ_STATUS + "-"  + value.SIGN_USERID + "' class='btn btn-sm btn-success btnsubmit' " +
																			"name='" + value.STUD_ACAD_REC_ID + "'>Submit" +																
																		"</button>";
													}
												} else {
													 tblOffSubject += "<div class='text-danger' " +
																		   "id='premainingstudnotencoded-" + value.SIGN_ID + "-" + value.REQ_STATUS + "-"  + value.SIGN_USERID + "' " +
																		   "name='" + value.STUD_ACAD_REC_ID + "'>";
													 tblOffSubject += (parseInt(value.NO_OF_STUDENT) - parseInt(value.NO_OF_ENCODED_STUDENT)).toString() + " STUDENT " +
																		"NOT YET ENCODED" +
																		"</div>";
												}
											}
										} else {
											tblOffSubject += "<div class='text-danger' " +
																	   "id='premainingstudnotencoded-" + value.SIGN_ID + "-" + value.REQ_STATUS + "-"  + value.SIGN_USERID + "' " +
																	   "name='" + value.STUD_ACAD_REC_ID + "'>" +
																	"NO ASSIGNED (APPROVER)" +
																"</div>";
										}
									} else {
											tblOffSubject += "<div class='text-danger' " +
																	   "id='premainingstudnotencoded-" + value.SIGN_ID + "-" + value.REQ_STATUS + "-"  + value.SIGN_USERID + "' " +
																	   "name='" + value.STUD_ACAD_REC_ID + "'>" +
																	"NO ASSIGNED (GS)" +
																"</div>";
									}
							tblOffSubject += "</td>" + 																		  
									  "</tr>";
						}
					});
				} else {
					tblOffSubject += "<tr><td colspan='99' class='text-danger text-center'> No Record Found </td></tr>";
				}
				tbodyofferedsubject.html(tblOffSubject);
				BtnSubmitClick();
				ViewStudent();
			}
		});
	})
	
	Initialize();
	
});