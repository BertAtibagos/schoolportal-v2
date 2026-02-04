$(document).ready(function(){
	var global_subjoffid;
	var global_gsdetpercentage;
	var global_gspassscore;
	var global_studacadavg;
	var global_studacadavgprcnt;
	var global_currentstudacadavgstatus; 
	var global_tdprocess;
	var global_gscaleid;
	var global_studid;
	var global_assid;
	var global_studacadrecdetrec;
	var global_studacadrecdetid;
	var global_studacadrecid;
	var global_resulttype; 
	var global_txtinputvalue;
	var global_txtinputvalueid;
	var global_tdinputvaluepercentage;
	var global_tdsubavgnextid;
	var global_request_status;
	var global_signid;
	
	const cboacadlvl = $('#cbo-acadlvl');
	const cboacadyr = $('#cbo-acadyr');
	const cboacadprd = $('#cbo-acadprd');
	const btnsearch = $('#btnsearch');
	const btnBack = $('#btnBack');
	const navforapprovaltab = $('#nav-for-approval-tab');
	const navapprovedtab = $('#nav-approved-tab');
	const navdeniedtab = $('#nav-denied-tab');
	const btnmodalprocessdenied = $('#btnmodalprocessdenied');
	const btnmodalprocessapproved = $('#btnmodalprocessapproved');
	
	const divmessage = $('#div-message');
	const tbodyapproved = $('#tbody-approved');
	const tbodydenied = $('#tbody-denied');
	const submittedrequest = $('#submitted-request');
	const secstudentlist = $('#sec-student-list');
	const notifiersubmittedgradescount = $('#notifier-submitted-grades-count');
	const notifier = $('.notifier');
    const tbodyforapproval = $('#tbody-for-approval');
	const navtabsbuttonfirst = $('.nav-tabs button:first');
	const spansubmittedgradesnotification = $('#spansubmittedgradesnotification');
	const spannotifier = $('#span-notifier');
	const tblheaderstudentlist = $('#tbl-header-student-list');
	
	const tbodystudent = $('#tbody-student');
	
	const divtblgscale = $('#div-tbl-gscale');
	const submittedgradesmastermodal = $('#submitted-grades-master-modal');
	
	const btnmainprocessapproved = $('.btnmainprocessapproved');
	const btnmainprocessdenied = $('.btnmainprocessdenied');
	
	function ReplaceSpecialCharacters(_str) {
		try { 
			var ns = _str;
		  // Just remove commas and periods - regex can do any chars
		  ns = ns.replace(/([-,.€~!@#$%^&*()+=`{}\[\]\|\\:;'<>])+/g, "_");
		  ns = ns.replaceAll(" ", "_");
		  ns = ns.replaceAll("/", "_");
		  return ns;
		}
		catch(e) {
			console.error(e);
		}
		finally {
			console.log('We do cleanup here');
		}
	}
	
	function TableTemplateDisplayNoRecord(_tblname){
		var _template = '';
		if (_tblname == 'SUBMITTED_REQUEST_GRADES')
		{
			_template = "<tr><td colspan='99' class='text-center text-danger'> No Record Found </td></tr>";
		}
		return _template;
	}
	
	function GetAcademicLevel(_type)
	{
	    try {
    		$.ajax({
    			type: 'POST',
    			url: '../../model/forms/academic/submittedgrades/submitted-grades-controller.php',
    			data:{
    				type : _type
    			},
    			success: function(result){
        			var ret = JSON.parse(result);
        			var cboLevel = '';
        			if(ret.length) {
        				$.each(ret, function(key, value) {
        					cboLevel += "<option value='" + value.ID + "'>" + value.NAME + "</option>";
        				});
        			} else {
        				cboLevel += "<option value='0'>None</option>";
        			}
        			cboacadlvl.html(cboLevel);
        			GetAcademicYear('ACADYEAR',cboacadlvl.val());
    			}
    		});
	    }
		catch(e) {
			console.error(e);
		}
		finally {
			console.log('We do cleanup here');
		}
	}
	
	function GetAcademicYear(_type,_lvlid)
	{
	    try 
	    {
    		$.ajax({
    			type: 'POST',
    			url: '../../model/forms/academic/submittedgrades/submitted-grades-controller.php',
    			data:{
    				type : _type,
    				levelid : _lvlid
    			},
    			success: function(result){
    			    if (JSON.parse(result))
    			    {
        				var ret = JSON.parse(result);
        				var cboYear = '';
        				if(ret.length) {
        					$.each(ret, function(key, value) {
        						cboYear += "<option value='" + value.ID + "'>" + value.NAME + "</option>";
        					});
        				} else {
        					cboYear += "<option value='0'>None</option>";
        				}
        				cboacadyr.html(cboYear);
        				GetAcademicPeriod('ACADPERIOD',cboacadlvl.val(),cboacadyr.val());
    			    }
    			}
    		});
    	     }
    	     catch(e) {
    		  console.error(e);
    	     }
    	     finally {
    	          console.log('We do cleanup here');
    	   }
	}
	function GetAcademicPeriod(_type,_lvlid,_yrid)
	{
	    try
	    {
    		$.ajax({
    			type:'POST',
    			url: '../../model/forms/academic/submittedgrades/submitted-grades-controller.php',
    			data:{
    				type : _type,
    				levelid : _lvlid,
    				yearid: _yrid
    			},
    			success: function(result){
    			    if (JSON.parse(result))
        			{
        				var ret = JSON.parse(result);
        				var cboPeriod = '';
        				if(ret.length) {
        					$.each(ret, function(key, value) {
        						cboPeriod += "<option value='" + value.ID + "'>" + value.NAME + "</option>";
        					});
        					
        				} else {
        					cboPeriod += "<option value='0'>None</option>";
        				}
        				
        				cboacadprd.html(cboPeriod);
        				GetSubmittedRequestList('FOR_APPROVAL_REQUEST_LIST',cboacadlvl.val(),cboacadyr.val(),cboacadprd.val());
        			}
    			}
    		});
	    } catch(e) {
			console.error(e);
    	} finally {
			console.log('We do cleanup here');
    	}
	}
	
	cboacadlvl.on('change',function(e){
		GetAcademicYear('ACADYEAR',$(this).val());
	});
	cboacadyr.on('change',function(){
		GetAcademicPeriod('ACADPERIOD',cboacadlvl.val(),$(this).val());
	});
	
	cboacadprd.on("change",function(e){
		GetSubmittedRequestList("FOR_APPROVAL_REQUEST_LIST",cboacadlvl.val(),cboacadyr.val(),cboacadprd.val());
	});
	
	btnsearch.on('click',function(e){
		GetSubmittedRequestList('FOR_APPROVAL_REQUEST_LIST',cboacadlvl.val(),cboacadyr.val(),cboacadprd.val());
	});
	btnBack.on('click',function(e){
		$('#submitted-request').show();
		secstudentlist.hide();
	});
	navforapprovaltab.on('click',function(){
		btnsearch.click();
		CheckSubmittedGrades();
	});
	navapprovedtab.on('click',function(e){
		GetSubmittedRequestHistory(1,cboacadlvl.val(),cboacadyr.val(),cboacadprd.val());
	});
	navdeniedtab.on('click',function(){
		GetSubmittedRequestHistory(0,cboacadlvl.val(),cboacadyr.val(),cboacadprd.val());
	});
	
	btnmodalprocessdenied.on('click',function(e)
	{
	    try
	    {
        		$.ajax({
        			type:'POST',
        			url: '../../model/forms/academic/submittedgrades/submitted-grades-controller.php',
        			data:{
        				type : 'MANAGE_SUBMITTED_REQUEST',
        				mode: 'PROCESS_SUBMITTED_GRADES',
        				schlenrollasssmsid: 0,
        				schlstudid: 0,
        				schlacadgradscaleid: parseInt(global_gscaleid),
        				schlenrollsubjoffid : parseInt(global_subjoffid),
        				schlstudacadrecid: parseInt(global_studacadrecid),
        				schlstudacadrecdetid: 0,
        				schlstudacadrecdetresulttype: '',
        				schlsignid: parseInt(global_signid),
        				schlsignuserid: 0,
        				schlstudacadrecdetrecords: '',
        				reqstatus: 0
        			},
        			success: function(result){
        				var ret = result;
        				if(parseInt(ret) > 0) {
							btnsearch.click();
        					CheckSubmittedGrades();
        					btnBack.click();
        					divmessage.html('');
        				} else {
        					divmessage.html("<p style='color: red; font-size: 12px; font-style: italic; font-weight: bold; none;color: black;padding: 0;margin: 0;'>Approved Successfully</p>");
        				}
        			}
        		});
    		submittedrequest.show();
    		secstudentlist.hide();
	    }
        catch(e) {
    		console.error(e);
    	}
    	finally {
    	    console.log('We do cleanup here');
    	}
	});
	
	btnmodalprocessapproved.on('click',function(e)
    {
	    try
	    {
    		var isapproved=1;
    		$.ajax({
    			type:'POST',
    			url: '../../model/forms/academic/submittedgrades/submitted-grades-controller.php',
    			data:{
    				type : 'MANAGE_SUBMITTED_REQUEST',
    				mode: 'PROCESS_SUBMITTED_GRADES',
    				schlenrollasssmsid: 0,
    				schlstudid: 0,
    				schlacadgradscaleid: parseInt(global_gscaleid),
    				schlenrollsubjoffid : parseInt(global_subjoffid),
    				schlstudacadrecid: parseInt(global_studacadrecid),
    				schlstudacadrecdetid: 0,
    				schlstudacadrecdetresulttype: '',
    				schlsignid: parseInt(global_signid),
    				schlsignuserid: 0,
    				schlstudacadrecdetrecords: '',
    				reqstatus: parseInt(isapproved)
    			},
    			success: function(result){
    				var ret = result;
    				if(parseInt(ret) > 0) {
						btnsearch.click();
    					CheckSubmittedGrades();
    					btnBack.click();
    					divmessage.html('');
    				} else {
    					divmessage.html("<p style='color: red; font-size: 12px; font-style: italic; font-weight: bold;'>Approved Successfully!</p>");
    				}
    			}
    		});
    		submittedrequest.show();
    		secstudentlist.hide();
	    }
        catch(e) {
    		console.error(e);
    	}
    	finally {
    	    console.log('We do cleanup here');
    	}
	});
	
	btnmainprocessapproved.on('click',function()
	{
	    try
	    {
    		var schlstudacadrecid = $(this).attr('name');// studrecid
    		var subjoffidname = $(this).attr('id');// subjofferedidname
    		var schlenrollsubjoffid;// subjofferedid
    		var isapproved=1;
    		var reqstatus;
    		var schlacadgradscaleid;
    		var subjoff_id_arr = subjoffidname.split('-');
    		for(i=0; i < subjoff_id_arr.length; i++){
    			schlenrollsubjoffid = subjoff_id_arr[1];
    			schlacadgradscaleid = subjoff_id_arr[2];
    			reqstatus = subjoff_id_arr[3];
    		}
    		var tr = $(this).closest('tr');
    		var td=tr.find("td:eq(7)");
    		var hidden_arr = td.find('input:hidden').attr('id').split('-');
    		var signid = hidden_arr[2];
    		$.ajax({
    			type:'POST',
    			url: '../../model/forms/academic/submittedgrades/submitted-grades-controller.php',
    			data:{
    				type : 'MANAGE_SUBMITTED_REQUEST',
    				mode: 'PROCESS_SUBMITTED_GRADES',
    				schlenrollasssmsid: 0,
    				schlstudid: 0,
    				schlacadgradscaleid: parseInt(schlacadgradscaleid),
    				schlenrollsubjoffid : parseInt(schlenrollsubjoffid),
    				schlstudacadrecid: parseInt(schlstudacadrecid),
    				schlstudacadrecdetid: 0,
    				schlstudacadrecdetresulttype: '',
    				schlsignid: parseInt(signid),
    				schlsignuserid: 0,
    				schlstudacadrecdetrecords: '',
    				reqstatus: parseInt(isapproved)
    			},
    			success: function(result){
    				var ret = result;
    				if(parseInt(ret) > 0) {
						btnsearch.click();
    					CheckSubmittedGrades();
    					divmessage.html('');
    				} else {
    					divmessage.html("<p style='color: red; font-size: 12px; font-style: italic; font-weight: bold;'>Approved Successfully</p>; none;color: black;padding: 0;margin: 0;'>None</p>");
    				}
    			}
    		});
	    }
        catch(e) {
    		console.error(e);
    	}
    	finally {
    	    console.log('We do cleanup here');
    	}
	});
	
	btnmainprocessdenied.on('click',function()
	{
	    try
	    {
    		var schlstudacadrecid = $(this).attr('name');// studrecid
    		var subjoffidname = $(this).attr('id');// subjofferedidname
    		var schlenrollsubjoffid;// subjofferedid
    		var isapproved=0;
    		var schlacadgradscaleid;
    		var subjoff_id_arr = subjoffidname.split('-');
    		for(i=0; i < subjoff_id_arr.length; i++){
    			schlenrollsubjoffid = subjoff_id_arr[1];
    			schlacadgradscaleid = subjoff_id_arr[2];
    		}
    		var tr = $(this).closest('tr');
    		var td=tr.find("td:eq(7)");
    		var hidden_arr = td.find('input:hidden').attr('id').split('-');
    		var signid = hidden_arr[2];
    		
    		$.ajax({
    			type:'POST',
    			url: '../../model/forms/academic/submittedgrades/submitted-grades-controller.php',
    			data:{
    				type : 'MANAGE_SUBMITTED_REQUEST',
    				mode: 'PROCESS_SUBMITTED_GRADES',
    				schlenrollasssmsid: 0,
    				schlstudid: 0,
    				schlacadgradscaleid: parseInt(schlacadgradscaleid),
    				schlenrollsubjoffid : parseInt(schlenrollsubjoffid),
    				schlstudacadrecid: parseInt(schlstudacadrecid),
    				schlstudacadrecdetid: 0,
    				schlstudacadrecdetresulttype: '',
    				schlsignid: parseInt(signid),
    				schlsignuserid: 0,
    				schlstudacadrecdetrecords: '',
    				reqstatus: parseInt(isapproved)
    			},
    			success: function(result){
    				var ret = result;
    				if(parseInt(ret) > 0) {
						btnsearch.click();
    					CheckSubmittedGrades();
    					divmessage.html('');
    				} else {
    					divmessage.html("<p style='color: red; font-size: 12px; font-style: italic; font-weight: bold; none;color: black;padding: 0;margin: 0;'>Approved Successfully</p>");
    				}
    			}
    		});
	    }
        catch(e) {
    		console.error(e);
    	}
    	finally {
    	    console.log('We do cleanup here');
    	}
	});
	
	function GetSubmittedRequestHistory(isapproved,lvlid,yrid,prdid)
	{
	    try
	    {
    		$.ajax({
    			type:'POST',
    			url: '../../model/forms/academic/submittedgrades/submitted-grades-controller.php',
    			data: { 
    				type: 'GET_SUBMITTED_REQUEST_HISTORY',
    				isapproved: isapproved,
    				levelid : lvlid,
    				yearid: yrid,
    				periodid: prdid
    			},
    			success: function(result){
    			    if (JSON.parse(result))
    			    {
        				let rowno=1;
        				var ret = JSON.parse(result);
        				var approvedreqlist;
                        // const wrapper = document.getElementById('table-wrapper');
                        // wrapper.innerHTML = '';

                        // // Map JSON objects to arrays for Grid.js
                        // const rows = ret.map(record => [
                        //     record.DATE,
        				// 	record.NAME,
        				// 	record.SUBJECT,
        				// 	record.CRSE_SEC,
        				// 	record.STATUS_NAME,
        				// 	record.GRADING_SCALE,
        				// 	record.STATUS_NAME
                        // ]);

                        // // Initialize Grid.js
                        // new gridjs.Grid({
                        //     columns: ['Date & Time', 'Name', 'Subject Code', 'Course & Section', 'Status', 'Grading Scale', 'Actions'],
                        //         data: rows,
                        //         pagination: {
                        //         enabled: true,
                        //         limit: 10
                        //     },
                        //     sort: true,
                        //     className: {
                        //         th: 'table-primary'
                        //     }
                        // }).render(wrapper);
                        
        				if(ret.length) {
        					$.each(ret, function(key, value) {
        						approvedreqlist += "<tr>" +
        										"<td class='text-center'>" + rowno++ + "</td>" +
        										"<td>" + value.DATE + "</td>" +
        										"<td>" + value.NAME + "</td>" +
        										"<td>" + value.SUBJECT + "</td>" +
        										"<td>" + value.CRSE_SEC + "</td>" +
        										"<td>" + value.STATUS_NAME + "</td>" +
        										"<td>" + value.GRADING_SCALE + "</td>" +
        										"<td>" + value.STATUS_NAME + "</td>" +
        									"</tr>";
        					});
        				} else {
        					approvedreqlist += "<tr><td colspan='99' class='text-center text-danger'> No Record Found </td></tr>";
        				}
        				if (isapproved == 1) {
        					tbodyapproved.html(approvedreqlist);
        				} else {
        					tbodydenied.html(approvedreqlist);
        				}
    			    }
    			}
    		});
	    }
        catch(e) {
    		console.error(e);
    	}
    	finally {
    	    console.log('We do cleanup here');
    	}
	}
	function CheckSubmittedGrades()
	{
	    try
	    {
    		$.ajax({
    			type:'POST',
    			url: '../../model/forms/checksubmittedrequest-controller.php',
    			data: { 
    				str: 'INITIALIZE',
    				type: 'CHECK_SUBMITTED_REQUEST_LIST'
    			},
    			success: function(result){
        			var ret = JSON.parse(result);
        			$.each(ret, function(key, value) {
        				if (parseInt(value.NOTIF) > 0){
							notifiersubmittedgradescount.addClass("notifier");
							spansubmittedgradesnotification.addClass("notifier");
							notifiersubmittedgradescount.html(parseInt(value.NOTIF));
							spansubmittedgradesnotification.html(parseInt(value.NOTIF));
						} else {
							notifiersubmittedgradescount.removeClass("notifier");
							spansubmittedgradesnotification.removeClass("notifier");
							notifiersubmittedgradescount.html('');
							spansubmittedgradesnotification.html('');
						}
        			});
    			}
    		});
	    }
        catch(e) {
    		console.error(e);
    	}
    	finally {
    	    console.log('We do cleanup here');
    	}
	}
	function GetStudentAcademicRecordDetail(inputdata,
									inputdataparent,
									inputvaluepercentage,
									schlstudid,
									schlenrollasssmsid,
									subjofferedid)
	{	
		try
		{
			$.ajax({
				async: false,
				type:'POST',
				url: '../../model/forms/academic/submittedgrades/submitted-grades-controller.php',
				data:{
					type : 'GET_STUDENT_GRADES_DETAIL',
					action: 'FETCH',
					schlstudid : schlstudid,
					schlenrollasssmsid: schlenrollasssmsid,
					subjofferedid: subjofferedid
				},
				success: function(result){
					if (JSON.parse(result))
					{
						var data = JSON.parse(result);
						var rec_gsdetlbl = '';
						var rec_gsdetcode = '';
						var rec_gsid = '';
						var rec_gsdetid = '';
						var rec_gradpctval = '';
						var gresulttype = '';
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
									global_studacadrecdetrec = value.STUD_ACAD_DET_REC;
									global_studacadrecdetid = value.STUD_ACAD_REC_DET_ID;
									//global_resulttype = value.RESULT_TYPE;
									gresulttype = value.RESULT_TYPE;//global_resulttype;
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
										$(inputvaluepercentage_arr[d]).html((parseFloat($(rec_arr[d]).val()) * (parseFloat(parent_rec_final_arr[4]) / 100)).toString() + ' %');
									}
								}
								ttlfinalavg += ttlsubavg;
								$(parent_arr[i]).html(ttlsubavg.toString() + '%');
							}
							
							$('#b-final-average').html(ttlfinalavg.toString() + '%');
							
							if ($.trim(gresulttype).length > 0) {
								$('#p-final-average-status').html(gresulttype.toString());
							} else {
								if (parseFloat(global_gspassscore) > 0) {
									if (parseFloat(ttlfinalavg) >= parseFloat(global_gspassscore)){
										$('#p-final-average-status').html('PASSED');
									} else {
										$('#p-final-average-status').html('FAILED');
									}
								} else {
									$('#p-final-average-status').html('PASSING SCORE NOT SET');
								}
							}
							divmessage.html('');
						} else {
							global_studacadrecdetrec = 0;
							global_studacadrecdetid = 0;
							$('#p-final-average-status').html('');
							$('#b-final-average').html('0%');
							divmessage.html("<p style='color: black; font-size: 12px; font-style: italic; font-weight: bold;'>No Record Found!</p>");
						}
					}
				}
			});
		} catch(e) {
    		console.error(e);
    	} finally {
    	    console.log('We do cleanup here');
    	}
	}
	
	function GetGradingScaleDetailPercentage(gscaleid,subjofferedid)
	{
		try
		{
			$.ajax({
				async: false,
				type:'POST',
				url: '../../model/forms/academic/submittedgrades/submitted-grades-controller.php',
				data:{
					type : 'GRADING_SCALE_INFO',
					action: 'FETCH',
					subjofferedid : subjofferedid,
					gradingscaleid : gscaleid
				},
				success: function(result){
					global_gsdetpercentage = result;
				}
			});
		}
        catch(e) {
    		console.error(e);
    	}
    	finally {
    	    console.log('We do cleanup here');
    	}
	}
	
	function GetSubmittedRequestList(type,lvlid,yrid,prdid)
	{
		try
		{
			$.ajax({
				type:'POST',
				url: '../../model/forms/academic/submittedgrades/submitted-grades-controller.php',
				data:{
					type : type,
					levelid : lvlid,
					yearid: yrid,
					periodid: prdid
				},
				beforeSend: function (status) {
					divmessage.html('');
					tbodyforapproval.html(TableTemplateDisplayNoRecord('SUBMITTED_REQUEST_GRADES'));
				},
				success: function(result){
					if (JSON.parse(result))
					{
						var ret = JSON.parse(result);
						let rowno=1;
						var forapprovalreqlist = '';
						var notification = '';
						if(ret.length) {
							$.each(ret, function(key, value) {
								forapprovalreqlist += "<tr>" +
												"<td class='text-center'>" + rowno++ + "</td>" +
												"<td>" + value.DATE + "</td>" +
												"<td>" + value.NAME + "</td>" +
												"<td>" + value.SUBJECT + "</td>" +
												"<td>" + value.CRSE_SEC + "</td>" +
												"<td>" + value.STATUS_NAME + "</td>" +
												"<td>" + value.GRADING_SCALE + "</td>" +
												"<td class='text-center'>" +
													"<button type='button' id='btngradingscale-" + value.OFFERED_SUBJ_SMS_ID + "-"  + value.GS_ID + "-" + value.STATUS + "' name='" + value.STUD_REC_ID + 
                                                        "' data-backdrop='static' data-keyboard='false' class='btn btn-sm btn-primary btnviewstudent'>" + 
														"Grades</button>" +
														"<input type='hidden' id='inputhiddengspassscore-" + value.OFFERED_SUBJ_SMS_ID.toString() + "-"  + value.SIGN_ID.toString() + "' name='" + value.GS_ID + "' value='" + value.GS_PASS_SCORE + "'/>" +
												"</td>" +
												"<td>" +
													"<button type='button' id='btnmainprocessapproved-" + value.OFFERED_SUBJ_SMS_ID + "-"  + value.GS_ID + "-" + value.STATUS + "' name='" + value.STUD_REC_ID + 
														"' data-backdrop='static' data-keyboard='false' class='me-2 mb-2 btn btn-success btn-sm btnmainprocessapproved'>" + 
														"Approved</button>" +
													"<button type='button' id='btnmainprocessdenied-" + value.OFFERED_SUBJ_SMS_ID + "-"  + value.GS_ID + "-" + value.STATUS + "' name='" + value.STUD_REC_ID + 
														"' data-backdrop='static' data-keyboard='false' class='me-2 mb-2 btn btn-danger btn-sm btnmainprocessdenied'>" + 
														"Denied</button>" +
												"</td>" +
											"</tr>";
							});
						} else {
							forapprovalreqlist = "<tr><td colspan='99' class='text-center text-danger'> No Record Found </td></tr>";
						}
						tbodyforapproval.html(forapprovalreqlist);
						navtabsbuttonfirst.tab('show');
						
						const btnmainprocessapproved = $('.btnmainprocessapproved');
						btnmainprocessapproved.on('click',function(){
							var schlstudacadrecid = $(this).attr('name');// studrecid
							var subjoffidname = $(this).attr('id');// subjofferedidname
							var schlenrollsubjoffid;// subjofferedid
							var isapproved=1;
							var reqstatus;
							var schlacadgradscaleid;
							var subjoff_id_arr = subjoffidname.split('-');
							for(i=0; i < subjoff_id_arr.length; i++){
								schlenrollsubjoffid = subjoff_id_arr[1];
								schlacadgradscaleid = subjoff_id_arr[2];
								reqstatus = subjoff_id_arr[3];
							}
							var tr = $(this).closest('tr');
							var td=tr.find("td:eq(7)");
							
							var hiddenid = td.find('input:hidden').attr('id');
							var hiddenid_arr = hiddenid.split('-');
							var signid = hiddenid_arr[2];
							$.ajax({
								type:'POST',
								url: '../../model/forms/academic/submittedgrades/submitted-grades-controller.php',
								data:{
									type : 'MANAGE_SUBMITTED_REQUEST',
									mode: 'PROCESS_SUBMITTED_GRADES',
									schlenrollasssmsid: 0,
									schlstudid: 0,
									schlacadgradscaleid: parseInt(schlacadgradscaleid),
									schlenrollsubjoffid : parseInt(schlenrollsubjoffid),
									schlstudacadrecid: parseInt(schlstudacadrecid),
									schlstudacadrecdetid: 0,
									schlstudacadrecdetresulttype: '',
									schlsignid: parseInt(signid),
									schlsignuserid: 0,
									schlstudacadrecdetrecords: '',
									reqstatus: parseInt(isapproved)
								},
								success: function(result){
									var ret = result;
									if(parseInt(ret) > 0) {
										btnsearch.click();
										CheckSubmittedGrades();
										divmessage.html('');
									} else {
										divmessage.html("<p style='color: red; font-size: 12px; font-style: italic; font-weight: bold; none;color: black;padding: 0;margin: 0;'>Approved Successfully</p>");
									}
								},
								error:function(status){
									divmessage.html("<p style='color: red; font-size: 12px; font-style: italic; font-weight: bold;'>Connection Error!</p>");
								}
							});
						});
						const btnmainprocessdenied = $('.btnmainprocessdenied');
						btnmainprocessdenied.on('click',function(){
							var schlstudacadrecid = $(this).attr('name');// studrecid
							var subjoffidname = $(this).attr('id');// subjofferedidname
							var schlenrollsubjoffid;// subjofferedid
							var isapproved=0;
							var reqstatus;
							var schlacadgradscaleid;
							var subjoff_id_arr = subjoffidname.split('-');
							for(i=0; i < subjoff_id_arr.length; i++){
								schlenrollsubjoffid = subjoff_id_arr[1];
								schlacadgradscaleid = subjoff_id_arr[2];
								reqstatus = subjoff_id_arr[3];
							}
							var tr = $(this).closest('tr');
							var td=tr.find("td:eq(7)");
							var hidden_arr = td.find('input:hidden').attr('id').split('-');
							var signid = hidden_arr[2];
							
							$.ajax({
								type:'POST',
								url: '../../model/forms/academic/submittedgrades/submitted-grades-controller.php',
								data:{
									type : 'MANAGE_SUBMITTED_REQUEST',
									mode: 'PROCESS_SUBMITTED_GRADES',
									schlenrollasssmsid: 0,
									schlstudid: 0,
									schlacadgradscaleid: parseInt(schlacadgradscaleid),
									schlenrollsubjoffid : parseInt(schlenrollsubjoffid),
									schlstudacadrecid: parseInt(schlstudacadrecid),
									schlstudacadrecdetid: 0,
									schlstudacadrecdetresulttype: '',
									schlsignid: parseInt(signid),
									schlsignuserid: 0,
									schlstudacadrecdetrecords: '',
									reqstatus: parseInt(isapproved)
								},
								success: function(result){
									var ret = result;
									if(parseInt(ret) > 0) {
										btnsearch.click();
										CheckSubmittedGrades();
										divmessage.html('');
									} else {
										divmessage.html("<p style='color: red; font-size: 12px; font-style: italic; font-weight: bold; none;color: black;padding: 0;margin: 0;'>Approved Successfully</p>");
									}
								},
								error:function(status){
									divmessage.html("<p style='color: red; font-size: 12px; font-style: italic; font-weight: bold;'>Connection Error!</p>");
								}
							});
						});
						
						const btnviewstudent = $('.btnviewstudent');
						btnviewstudent.on('click',function() {
							secstudentlist.show();
							submittedrequest.hide();
							var lvlid = cboacadlvl.val();
							var yrid = cboacadyr.val();
							var prdid = cboacadprd.val();
							
							var currentRow=$(this).closest("tr");
							var tdgspassscore=currentRow.find("td:eq(7)");
							
							global_gspassscore = tdgspassscore.find('input:hidden').val();
							var inputhidden_arr = tdgspassscore.find('input:hidden').attr('id').split('-');
							global_signid = inputhidden_arr[2];
							
							var btnviewstudent_arr = $(this).attr('id').split('-');
							global_subjoffid = btnviewstudent_arr[1];
							global_gscaleid = btnviewstudent_arr[2];
							global_request_status = btnviewstudent_arr[3];
							var studacadrecid = $(this).attr('name');
							global_studacadrecid = $(this).attr('name');
							let rowNo = 1;
							$.ajax({
								type:'POST',
								url: '../../model/forms/academic/submittedgrades/submitted-grades-controller.php',
								data:{
									type : 'STUDENT_LIST',
									action: 'FETCH',
									gsid: global_gscaleid,
									levelid : lvlid,
									yearid: yrid,
									periodid: prdid,
									//courseid: crseid,
									subjofferedid: global_subjoffid,
					                studacadrecid: studacadrecid
								},
								beforeSend: function (status) {
									divmessage.html('');
									tblheaderstudentlist.hide();
								},
								success: function(result){
									var ret = JSON.parse(result);
									var tblstudent = '';
									if(ret.length) {
										GetGradingScaleDetailPercentage(parseInt(global_gscaleid),parseInt(global_subjoffid));
										var data = JSON.parse(global_gsdetpercentage);//$.session.get('GSDETPERCENTAGE')
										$.each(ret, function(key, value) {
											if (global_gscaleid > 0) {
												if (value.STATUS == 'ENROLLED'){
													if (value.RECORDS.length > 0) {																											
														var final_stud_avg = '0';
														var final_stud_avg_status = '';
														var searchpar = new RegExp('0','i');
														if(data.length) {
															$.each(data, function(ky, val) {
																if (val.GSD_PARENT_ID.search(searchpar) != -1){
																	$.each(data, function(k, v) {
																		var paramdetid = new RegExp(val.GSD_DET_ID,'i');
																		if (v.GSD_PARENT_ID.search(paramdetid) != -1){
																			var initialarr = value.RECORDS.split(',');
																			for(i=0; i < initialarr.length; i++){
																				var finalarr = initialarr[i].split(':');
																				if (parseInt(v.GSD_DET_ID) == parseInt(finalarr[0])){
																					final_stud_avg = parseFloat(final_stud_avg) + (parseFloat(finalarr[1]) * parseFloat((parseFloat(v.GSD_PERCENTAGE) / 100)));
																				}
																			}
																		}
																	});
																}
															});
															
															if (final_stud_avg.length <= 0 || $.trim(final_stud_avg) == '' || $.trim(final_stud_avg) == '0'){
																final_stud_avg = '0';
															}
															
															if (global_gspassscore.length <= 0 || global_gspassscore == '' || global_gspassscore == '0' || global_gspassscore == 0){
																global_gspassscore = '0';
																final_stud_avg_status = 'PASSING SCORE NOT SET';
																
															} else {
																if (parseFloat(final_stud_avg) >= parseFloat(global_gspassscore)){
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
														tblstudent += "<tr>" + 
																  "<td class='text-center'>" + rowNo++ + "</td>" + 
																  "<td>" + value.NAME + "</td>" + 
																  "<td>" + value.GENDER + "</td>" + 
																  "<td>" + value.SECTION + "</td>" +
																  "<td>" + value.STATUS + "</td>" +
																  "<td class='text-center' id='tdfinalaverage-" + value.STATUS + "-" + value.ID + "' name='" + value.ASS_ID + "'>" + final_stud_avg.toString() + "%</td>" +
																  "<td class='text-center' id='tdfinalstatus-" + value.STATUS + "-" + value.ID + "' name='" + value.ASS_ID + "'>" + final_stud_avg_status.toString() + "</td>" +
																  "<td class='text-center'>" +
																  "<button type='button' id='" + value.ID + "' name='" + value.ASS_ID + "' data-backdrop='static' data-keyboard='false' class='btn btn-sm btn-primary edit' value='" + value.ID + "'>" + 
																	"Grades</button>" +
																  "</td>" + 
																  "</tr>";
													} else {
														tblstudent += "<tr>" + 
																	  "<td class='text-center'>" + rowNo++ + "</td>" + 
																	  "<td>" + value.NAME + "</td>" + 
																	  "<td>" + value.GENDER + "</td>" + 
																	  "<td>" + value.SECTION + "</td>" +
																	  "<td>" + value.STATUS + "</td>" +
																	  "<td class='text-center' id='tdfinalaverage-" + value.STATUS + "-" + value.ID + "' name='" + value.ASS_ID + "'>0%</td>" +
																	  "<td class='text-center' id='tdfinalstatus-" + value.STATUS + "-" + value.ID + "' name='" + value.ASS_ID + "'>NO ENCODED GRADES</td>" +
																	  "<td class='text-center'>" +
																	  "<button type='button' id='" + value.ID + "' name='" + value.ASS_ID + "' data-backdrop='static' data-keyboard='false' class='btn btn-sm btn-primary edit' value='" + value.ID + "'>" + 
																		"Grades</button>" +
																	  "</td>" + 
																	  "</tr>";
													}
												} else {
													tblstudent += "<tr>" + 
														  "<td>" + rowNo++ + "</td>" + 
														  "<td>" + value.NAME + "</td>" + 
														  "<td>" + value.GENDER + "</td>" + 
														  "<td>" + value.SECTION + "</td>" +
														  "<td>" + value.STATUS + "</td>" +
														  "<td id='tdfinalaverage-" + value.STATUS + "-" + value.ID + "' name='" + value.ASS_ID + "'>0%</td>" +
														  "<td id='tdfinalstatus-" + value.STATUS + "-" + value.ID + "' name='" + value.ASS_ID + "'></td>" +
														  "<td>" +
														  "</td>" + 
														  "</tr>";
												}
											} 
											else 
											{
												tblstudent += "<tr>" + 
													  "<td>" + rowNo++ + "</td>" + 
													  "<td>" + value.NAME + "</td>" + 
													  "<td>" + value.GENDER + "</td>" + 
													  "<td>" + value.SECTION + "</td>" +
													  "<td>" + value.STATUS + "</td>" +
													  "<td id='tdfinalaverage-" + value.STATUS + "-" + value.ID + "' name='" + value.ASS_ID + "'>0%</td>" +
													  "<td id='tdfinalstatus-" + value.STATUS + "-" + value.ID + "' name='" + value.ASS_ID + "'>NO GRADING SCALE</td>" +
													  "<td>" +
													  "</td>" + 
													  "</tr>";
											}
										});
									} else {
										tblstudent += "<tr><td colspan='99' class='text-center text-danger'> No Record Found </td></tr>";
									}
									
									tbodystudent.html(tblstudent);
									const edit = $('.edit');
									edit.on('click',function() 
									{
										global_studid = $(this).val();
										global_assid = $(this).attr('name');
										var lvlid = cboacadlvl.val();
										var yrid = cboacadyr.val();
										var yrname = cboacadyr.text();
										var prdid = cboacadprd.val();
										var prdname = cboacadprd.text();
										
										var currentRow = $(this).closest("tr");
										var name=currentRow.find("td:eq(1)").html();
										global_studacadavg=currentRow.find("td:eq(5)").attr('id');
										global_studacadavgprcnt=currentRow.find("td:eq(5)").html();
										global_currentstudacadavgstatus =currentRow.find("td:eq(6)").attr('id');
										
										$.ajax({
											type:'POST',
											url: '../../model/forms/academic/submittedgrades/submitted-grades-controller.php',
											data:{
												type : 'GRADING_SCALE_PERCENTAGE',
												action: 'FETCH',
												levelid : lvlid,
												yearid: yrid,
												periodid: prdid,
												//courseid: crseid,
												subjofferedid: global_subjoffid,
												studentid: global_studid,
												gradingscaleid: global_gscaleid
											},
											success: function(result){
												if (JSON.parse(result))
												{
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
												
												$.ajax({
													type:'POST',
													url: '../../model/forms/academic/submittedgrades/submitted-grades-controller.php',
													data:{
														type : 'GRADING_SCALE_INFO',
														action: 'FETCH',
														gradingscaleid : global_gscaleid,
														subjofferedid : global_subjoffid
													},
													success: function(result){
														var gtxtinputvalue;
														var gtdinputvaluepercentage = global_tdinputvaluepercentage;
														var gtdsubavgnextid = global_tdsubavgnextid = '';
														var data = JSON.parse(result);
														var tbl = '';
														var regex = new RegExp('0','i');
														var cnt = parseInt('0');
														var tdinputvaluepercentage = '';
														global_txtinputvalueid = '';
														if(data.length) {
															$.each(data, function(key, value) {
																if (value.GSD_PARENT_ID.search(regex) != -1){
																	tbl += "<table id='table-" + value.GSD_NAME + "' class='table table-responsive table-bordered'>" + 
																		   "<thead class='table-primary'>" +
																		   "<tr>" +
																		   "<th scope='col' colspan='99' class='text-primary text-decoration-underline text-center' id='th-" + value.GSD_CODE + "-" + value.GS_ID + "-" + value.GSD_DET_ID + "' name='" + value.GSD_PERCENTAGE + "'>" + value.GSD_NAME + " (" + value.GSD_PERCENTAGE + " %)</th>" +							   
																		   "</tr>" +
																		   "<tr>";
																			$.each(data, function(keys, val) {
																				var regex1 = new RegExp(value.GSD_DET_ID,'i');
																				if (val.GSD_PARENT_ID.search(regex1) != -1){
																					tbl += "<th scope='col' id='th-" + val.GSD_CODE + "-" + val.GS_ID + "-" + val.GSD_DET_ID + "' name='" + val.GSD_PERCENTAGE + "' class='text-center'>" + val.GSD_CODE + " (" + val.GSD_PERCENTAGE + " %)</th>";
																				}
																			});
																	tbl += 	"<th scope='col' class='text-danger text-center'>AVERAGE</th>";
																	tbl += 	"</tr></thead>";
																	tbl +=  "<tbody><tr>";
																			$.each(data, function(k, v) {
																				var regex2 = new RegExp(value.GSD_DET_ID,'i');
																			   if (v.GSD_PARENT_ID.search(regex2) != -1){
																					tbl += "<td class='m-0 p-0'>" + 
																						"<input type='text' id='txt-" + v.GSD_CODE + "-" + v.GS_ID + "-" + v.GSD_DET_ID + "-" + v.GSD_PERCENTAGE + "-" + v.GSD_PARENT_ID + "' name='" + v.GSD_PERCENTAGE + "' class='form-control border-0 rounded-0 text-center' value='0' required disabled/></td>";
																					gtxtinputvalue = "#txt-" + v.GSD_CODE + "-" + v.GS_ID + "-" + v.GSD_DET_ID + "-" + v.GSD_PERCENTAGE + "-" + v.GSD_PARENT_ID + ",";
																					global_txtinputvalueid += gtxtinputvalue;
																			   }
																			});
																	tbl += "<td rowspan='2' id='tdnext-" + value.GSD_CODE + "-" + value.GS_ID + "-" + value.GSD_DET_ID + "-" + value.GSD_PERCENTAGE + "-" + value.GSD_PARENT_ID + "' class='table-primary text-danger text-center fw-medium align-middle'>" +
																		   "0</td>";
																	tbl += "</tr>";
																	tbl += "<tr>";
																			$.each(data, function(k1, v1) {
																				var regex3 = new RegExp(value.GSD_DET_ID,'i');
																			   if (v1.GSD_PARENT_ID.search(regex3) != -1){
																				   tbl += "<td id='tdsub-" + v1.GSD_CODE + "-" + v1.GS_ID + "-" + v1.GSD_DET_ID + "-" + v1.GSD_PERCENTAGE + "-" + v1.GSD_PARENT_ID + "' name='" + v1.GSD_PERCENTAGE + "' class='table-primary text-danger text-center p-1' style='font-size: .75rem'></td>";
																				   gtdinputvaluepercentage += "#tdsub-" + v1.GSD_CODE + "-" + v1.GS_ID + "-" + v1.GSD_DET_ID + "-" + v1.GSD_PERCENTAGE + "-" + v1.GSD_PARENT_ID + ",";
																			   }
																			});
																	tbl += "</tr>" +
																		   "</tbody>" +
																		   "</table>";
																gtdsubavgnextid += "#tdnext-" + value.GSD_CODE + "-" + value.GS_ID + "-" + value.GSD_DET_ID + "-" + value.GSD_PERCENTAGE + "-" + value.GSD_PARENT_ID + ",";														
																}
															});
															global_txtinputvalueid = global_txtinputvalueid.substring(0, (global_txtinputvalueid.length - 1));
															//gtxtinputvalueid = gtxtinputvalue.substring(0, (gtxtinputvalue.length - 1));
															gtdinputvaluepercentage = gtdinputvaluepercentage.substring(0, (gtdinputvaluepercentage.length - 1));
															gtdsubavgnextid = gtdsubavgnextid.substring(0, (gtdsubavgnextid.length - 1));
															divtblgscale.html(tbl);
															GetStudentAcademicRecordDetail(global_txtinputvalueid,
																								gtdsubavgnextid,
																								gtdinputvaluepercentage,
																								parseInt(global_studid),
																								parseInt(global_assid),
																								parseInt(global_subjoffid));
														} else {
															divtblgscale.html("<p style='font-size: 13px;font-family: Roboto, sans-serif;font-weight: normal;text-decoration: none;color: black;padding: 0;margin: 0;'>None</p>");
														}
													}
												});
												submittedgradesmastermodal.modal({backdrop: 'static', keyboard: false});														
												submittedgradesmastermodal.modal('show');
												}
											}
										});
									});
								}
							});
						});
					}
				}
			});
		} catch(e) {
    		console.error(e);
    	} finally {
    	    console.log('We do cleanup here');
    	}
	}
	
	function Initialize() {
		secstudentlist.hide();
		GetAcademicLevel('ACADLEVEL');
		CheckSubmittedGrades();
	}
	Initialize();
});