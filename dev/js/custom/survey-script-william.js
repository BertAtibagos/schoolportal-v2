$(document).ready(function() 
{
	var surveyInfoDESC = [];
	var surveyInfoID = [];
	var surveyInfoCategory = [];
	var surveyInfoAnswer = [];
	var surveyTblID = [];
	var surveyTblNAME = [];
	var ctrlrequired = [];
	var ctrldiv = [];
	var ctrlcreated = [];
	var tblitemid = [];
	
	var btnview;
	var tblid;
	
	var tblrowid;
	
	function ViewListOfSurvey()
	{
		$('#div-process').html('loading');
		var html_tag = '';
		$.ajax({
			type:'GET',
			url: '../../model/forms/survey/survey-controller.php',
			data:{
				mode : 'SEARCH_DEPARTMENT'
			},
			beforeSend: function(){
					$('#section-surv-main').css("cursor", "progress");
					$('#div-process').html('loading');
			},
			success: function(result)
			{
				var rsstudJSON = JSON.parse(result);
				if (rsstudJSON.length > 0)
				{
					for(i = 0; i< rsstudJSON.length; i++)
					{    
						if(surveyInfoDESC.indexOf(rsstudJSON[i].SURVEY_INFO_DESC) == -1){
							surveyInfoDESC.push(rsstudJSON[i].SURVEY_INFO_DESC);
							surveyInfoID.push(rsstudJSON[i].SURVEY_INFO_ID);
						}
						if(surveyTblID.indexOf(rsstudJSON[i].ID) == -1){
							surveyTblID.push(rsstudJSON[i].ID);
							surveyTblNAME.push(rsstudJSON[i].DESC);
							surveyInfoAnswer.push(rsstudJSON[i].ANSWER);
						}
					}
					for(i = 0; i< surveyInfoDESC.length; i++)
					{
						html_tag += '<h4 style="padding-top: 1rem; color: black;text-decoration: underline;"><center>' + surveyInfoDESC[i] + '</center><h4>';
						html_tag += '<table id="survey-tab-' + surveyInfoID[i] + '" class="table table-hover table-responsive table-bordered" style="width: 40%;height: auto;">';
						html_tag += '<tbody id="nav-tab">';
						$.each(rsstudJSON, function(key, value) 
						{
							html_tag += '<tr>';
							html_tag += '<td  colspan="2" style="width: auto;height: auto;text-align: left;font-size: 16px;font-family: Roboto, sans-serif; font-weight: normal; text-decoration: none; color: black;" id="td-' + value.ID + '">';
							html_tag += value.DESC;
							html_tag += '</td>';
							if (value.ANSWER.length > 0)
							{
								html_tag += '<td class="img" style="width: auto;height: auto;">';
								//html_tag += '<button type="button"  style="font-size: 14px;font-family: Roboto, sans-serif; font-weight: normal; text-decoration: none;" class="btn btn-block btn-success btn-view-questions" id="' + icat_id_cat2[0].toString() + '" value="' + icat_id_cat2[0].toString() + '">View</button>';	
								html_tag += '</td>';
							} else {
								html_tag += '<td style="width: auto;height: auto;">';
								html_tag += '<button type="button"  style="font-size: 14px;font-family: Roboto, sans-serif; font-weight: normal; text-decoration: none;" class="btn btn-block btn-primary btn-view-questions" id="btnview-question-' + surveyInfoID[i] +"-" + value.ID + '" value="' + value.ID + '">Survey</button>';		
								html_tag += '</td>';
							}
							html_tag += '</tr>';
						});
						html_tag += '</tbody>';
						html_tag += '</table>';
					}
					$('#main-survey').html(html_tag);
					ViewListOfQuestion();
					$('#section-surv-main').css("cursor", "default");
					$('#div-process').html('');
				}
			},
			error:function(status){
				$('#errormessage').html('Error!');
				$('#section-surv-main').css("cursor", "default");
				$('#div-process').html('');
			}
		});
	}
	
	function ViewListOfQuestion()
	{
		$('.btn-view-questions').on('click',function() 
		{
			var id = $(this).val();//Department ID
			var name = $(this).attr('id');
			var name_arr = name.split('-');
			tblid = id;
			btnview = $(this);
			var html_question_choices = '';
			var html_question_tag = '';
			$('#div-survey-header').hide();
			$('#div-survey-content').show();
			var cRow=$(this).closest("tr");
			//tblrowid = $(this).closest("tr");
			tblrowid = cRow.find("td:eq(1)");
			$('#hd-survey-content').html(cRow.find("td:eq(0)").html());
			
			$.ajax({
				type:'GET',
				url: '../../model/forms/survey/survey-controller.php',
				data:{
					mode : 'SEARCH_QUESTIONAIRE',
					INFOID : name_arr[2],
					TBLID : id
				},
				success: function(result)
				{
					var rsstudJSON = JSON.parse(result);
					if (rsstudJSON.length > 0)
					{
						$.each(rsstudJSON, function(key, value) 
						{
							html_question_tag += '<div id="div-survey-' + value.ID + '">';
							html_question_tag += '<p>' + 
													value.RANK_NO + 
													'.   ' + 
													'' + value.QUESTIONS.replace('[||]',', ') + ' ' +
													'<span>' + (value.QUEST_IS_REQUIRED.toString() == '0' ? '' : ' (Required)') + '</span>';
							html_question_tag += '</p>';
							
							html_question_choices = '';
								var ac = value.QUEST_CHOICE_ANS.split(',');
								//var id = '';
								for(iac=0; iac < ac.length; iac++){
									var ac_id_desc = ac[iac].split('=');
										html_question_choices += (value.ANS_TYPE_CODE.toString() === 'SINGLE' ?
																	'<input type="radio" '
																	:
																	(value.ANS_TYPE_CODE.toString() === 'MULTIPLE' ?
																		'<input type="checkbox" '
																		:
																		'<textarea '));
										html_question_choices += 'id="' + name_arr[2] + 
																 '-' + value.ID + 
																 '-' + value.ANS_TYPE_ID.toString() + 
																 '-' + ac_id_desc[0].toString() + '" ';
										html_question_choices += (value.ANS_TYPE_CODE.toString() === 'SINGLE' ?
																	'name="' + value.ID + '" value="' + ac_id_desc[0].toString() + '"/><span>' + ac_id_desc[1].toString() + '   ' + ac_id_desc[2].toString() + '</span><br>'
																	:
																	(value.ANS_TYPE_CODE.toString() === 'MULTIPLE' ?
																		'name="' + value.ID + '-' + ac_id_desc[0].toString() + '" value="' + ac_id_desc[0].toString() + '"/><span>' + ac_id_desc[1].toString() + '   ' + ac_id_desc[2].toString() + '</span><br>'
																		:
																		'name="txtarea-' + value.ID + '" rows="4" cols="80" ' + (value.QUEST_IS_REQUIRED.toString() == '0' ? '' : 'required') + '></textarea><br>'));
									html_question_choices += '</br>';
								}
							html_question_tag = html_question_tag + html_question_choices;
							html_question_tag += '</div>';
							ctrlcreated.push((value.ANS_TYPE_CODE.toString() === 'SINGLE' ?
												'input[name="' + value.ID + '"]'
												:
												(value.ANS_TYPE_CODE.toString() === 'MULTIPLE' ?
													'input[name="' + value.ID + '"]'
													:
													'#txtarea-' + value.ID)));
							ctrldiv.push('#div-survey-' + value.ID);
							if (value.QUEST_IS_REQUIRED.toString() === '1'){
									ctrlrequired.push((value.ANS_TYPE_CODE.toString() == 'SINGLE' ?
												'input[name="' + value.ID + '"]'
												:
												(value.ANS_TYPE_CODE.toString() == 'MULTIPLE' ?
													'input[name="' + value.ID + '"]'
													:
													'#txtarea-' + value.ID)));
													//alert(icat_id_cat1[1].toString());
							}
						});
						$('#main-content').html(html_question_tag);
					} else {
						$('#main-content').html('<center><div style="font-size: 20px; font-weight: bold; font-style: italic; text-decoration: none;color: red;">NO ASSIGNED QUESTIONAIRE</div></center>');
					}
				},
				error:function(status){
					$('#errormessage').html('Error!');
				}
			});
			
			// if(questions.length > 0)
			// {
				// $.each(questions, function(key, value)
				// {
					// var que = value.QUESTIONAIRE.split(',');
					// for(icat1=0; icat1 < que.length; icat1++)
					// {
						// var icat_id_cat1 = que[icat1].split('=');
						
						// html_question_tag += '<div id="div-survey-' + icat_id_cat1[1].toString() + '">';
						// html_question_tag += '<p>' + 
												// (icat1 + 1) + 
												// '.   ' + 
												// '' + icat_id_cat1[2].toString().replace('[||]',', ') + ' ' +
												// '<span>' + (value.QUEST_IS_REQUIRED.toString() == '0' ? '' : ' (Required)') + '</span>';
						// html_question_tag += '</p>';
						
						// html_question_choices = '';
							// var ac = value.QUEST_CHOICE_ANS.split(',');
							// //var id = '';
							// for(iac=0; iac < ac.length; iac++){
								// var ac_id_desc = ac[iac].split('=');
									// html_question_choices += (value.ANS_TYPE_CODE.toString() === 'SINGLE' ?
																// '<input type="radio" '
																// :
																// (value.ANS_TYPE_CODE.toString() === 'MULTIPLE' ?
																	// '<input type="checkbox" '
																	// :
																	// '<textarea '));
									// html_question_choices += 'id="' + value.SURVEY_INFO_ID.toString() + 
															 // '-' + icat_id_cat1[1].toString() + 
															 // '-' + value.ANS_TYPE_ID.toString() + 
															 // '-' + ac_id_desc[0].toString() + '" ';
									// html_question_choices += (value.ANS_TYPE_CODE.toString() === 'SINGLE' ?
																// 'name="' + icat_id_cat1[1].toString() + '" value="' + ac_id_desc[0].toString() + '"/><span>' + ac_id_desc[1].toString() + '   ' + ac_id_desc[2].toString() + '</span><br>'
																// :
																// (value.ANS_TYPE_CODE.toString() === 'MULTIPLE' ?
																	// 'name="' + icat_id_cat1[1].toString() + '-' + ac_id_desc[0].toString() + '" value="' + ac_id_desc[0].toString() + '"/><span>' + ac_id_desc[1].toString() + '   ' + ac_id_desc[2].toString() + '</span><br>'
																	// :
																	// 'name="txtarea-' + icat_id_cat1[1].toString() + '" rows="4" cols="80" ' + (value.QUEST_IS_REQUIRED.toString() == '0' ? '' : 'required') + '></textarea><br>'));
								// html_question_choices += '</br>';
							// }
						// html_question_tag = html_question_tag + html_question_choices;
						// html_question_tag += '</div>';
						// ctrlcreated.push((value.ANS_TYPE_CODE.toString() === 'SINGLE' ?
											// 'input[name="' + icat_id_cat1[1].toString() + '"]'
											// :
											// (value.ANS_TYPE_CODE.toString() === 'MULTIPLE' ?
												// 'input[name="' + icat_id_cat1[1].toString() + '"]'
												// :
												// '#txtarea-' + icat_id_cat1[1].toString())));
						// ctrldiv.push('#div-survey-' + icat_id_cat1[1].toString());
						// if (value.QUEST_IS_REQUIRED.toString() === '1'){
								// ctrlrequired.push((value.ANS_TYPE_CODE.toString() == 'SINGLE' ?
											// 'input[name="' + icat_id_cat1[1].toString() + '"]'
											// :
											// (value.ANS_TYPE_CODE.toString() == 'MULTIPLE' ?
												// 'input[name="' + icat_id_cat1[1].toString() + '"]'
												// :
												// '#txtarea-' + icat_id_cat1[1].toString())));
												// //alert(icat_id_cat1[1].toString());
						// }
					// }
				// });
				// //html_question_tag += '<button type="button" style="margin-left: 4rem; padding-left: 2rem; padding-right: 2rem;width: auto; height: auto; font-size: 16px;font-family: Roboto, sans-serif; font-weight: bold; text-decoration: underline;" ';
				// //html_question_tag += 'class="btn btn-block btn-primary" id="btn-submit-survey" name="btn-submit-survey">';
				// //html_question_tag += 'Submit';
				// //html_question_tag += '</button>';
				// $('#main-content').html(html_question_tag);
			// } else {
				// $('#main-content').html('<center><div style="font-size: 20px; font-weight: bold; font-style: italic; text-decoration: none;color: red;">NO ASSIGNED QUESTIONAIRE</div></center>');
			// }
		});
	}
	
	$('#btn-close-survey-content').on('click',function()
	{
		$('#main-content').html('');
		$('#hd-survey-content').html('');
		$('#div-survey-content').hide();
		$('#div-survey-header').show();
	});
	
	$('#btn-submit-survey').click(function()
	{
		for(x = 0; x< ctrlrequired.length; x++)
		{
			if (!$(ctrlrequired[x]).is(':checked'))
			{
				$(ctrldiv[x]).css('border', '2px solid red');
				$(ctrlrequired[x]).focus();
				return;
			} else {
				$(ctrldiv[x]).css('border', '0px none transparent');
			}
		}
		var _finalans = '';
		var _survinfo_id = 0;
		for(x = 0; x< ctrlcreated.length; x++)
		{
			var iddiv = ctrldiv[x].split('-');
			_finalans = _finalans + ',' + iddiv[2].toString() + '=' + $(ctrlcreated[x] + ':checked').val();
			_survinfo_id = $(ctrlcreated[x]).attr('id');
		}
		$.ajax({
				async: false,
				mode:'GET',
				url: '../../model/forms/survey/survey-controller.php',
				data:{
					mode : 'INSERT',
					schlsurvans_answer: _finalans.substring(1, (_finalans.length)),
					schlsurvinfo_id : _survinfo_id,
					schlsurvinfo_tbl_id: tblid
				},
				success: function(result){
					//var _tdrw = tblrowid.find("td:eq(1)");
					//var _btnSurvey=_tdrw.find("button");
					var _btnSurvey= tblrowid.find("button");
					_btnSurvey.remove();
					//_btnSurvey.hide();
					tblrowid.addClass('img');
					$('#btn-close-survey-content').click();
					ctrlcreated = [];
					ctrldiv = [];
					ctrlrequired  = [];
					alert('Submit Successful.');
				},
				error: function(){
					alert('Failed to submit, Click the submit button again.');
				}
		});
	});
	
	function Initialized()
	{
		$('#div-process').html('loading');
		ViewListOfSurvey();
		$('#div-survey-content').hide();
	}
	
	Initialized();
	// $('#div-survey-header').ready(function()
	// {
		// ViewListOfSurvey();
		// BtnSubmitEventHandler();
	// });
	
	// function setRequiredAttribute(element_name){
		// //document.getElementById(element_name).required = 'Required';
		// $(element_name).attr('required', true);
	// }
	// function addImage(url,width,height){
		// $('<img />')
                        // .attr('src', "" + url + "") 
                            // .attr('title', 'Uploaded Image')
                            // .attr('alt', 'Image')
							// .width(width).height(height)
							// .appendTo($('#show_images'));
                            // //.width('113px').height('113px')
	// }
	// function getSurveyAnswer(infoID, catID,)
	// {
		// if (surveyInfoAnswer.length > 0)
			// {
				// for(sia=0; sia < surveyInfoAnswer.length; sia++){
					// var  sia_cat = surveyInfoAnswer[sia].split(':');
					// if (sia_cat.length > 1){
						// if (sia_cat[1].toString() == icat_id_cat2[0].toString()){
							// html_tag += '<td class="img disable" colspan="2" style="width: auto;height: auto;text-align: left;font-size: 16px;font-family: Roboto, sans-serif; font-weight: normal; text-decoration: none; color: black;" id="td-' + icat_id_cat2[0].toString() + '">';
							// html_tag += icat_id_cat2[1].toString();
							// html_tag += '</td>';
							// html_tag += '<td style="width: auto;height: auto;">';
							// html_tag += '<button type="button"  style="font-size: 14px;font-family: Roboto, sans-serif; font-weight: normal; text-decoration: none;" class="btn btn-block btn-success btn-view-questions" id="' + icat_id_cat2[0].toString() + '" value="' + icat_id_cat2[0].toString() + '">View</button>';	
						// } else {
							// html_tag += '<td class="" colspan="2" style="width: auto;height: auto;text-align: left;font-size: 16px;font-family: Roboto, sans-serif; font-weight: normal; text-decoration: none; color: black;" id="td-' + icat_id_cat2[0].toString() + '">';
							// html_tag += icat_id_cat2[1].toString();
							// html_tag += '</td>';
							// html_tag += '<td style="width: auto;height: auto;">';
							// html_tag += '<button type="button"  style="font-size: 14px;font-family: Roboto, sans-serif; font-weight: normal; text-decoration: none;" class="btn btn-block btn-primary btn-view-questions" id="' + icat_id_cat2[0].toString() + '" value="' + icat_id_cat2[0].toString() + '">Survey</button>';	
						// }
					// } else {
						// html_tag += '<td class="" colspan="2" style="width: auto;height: auto;text-align: left;font-size: 16px;font-family: Roboto, sans-serif; font-weight: normal; text-decoration: none; color: black;" id="td-' + icat_id_cat2[0].toString() + '">';
						// html_tag += icat_id_cat2[1].toString();
						// html_tag += '</td>';
						// html_tag += '<td style="width: auto;height: auto;">';
						// html_tag += '<button type="button"  style="font-size: 14px;font-family: Roboto, sans-serif; font-weight: normal; text-decoration: none;" class="btn btn-block btn-primary btn-view-questions" id="' + icat_id_cat2[0].toString() + '" value="' + icat_id_cat2[0].toString() + '">Survey</button>';		
					// }																			
				// }
			// } else {
				
			// }
	// }
});