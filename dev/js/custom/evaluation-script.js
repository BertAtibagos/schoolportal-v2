$(document).ready(function() 
{
	$('#div-eval-content').hide();
	$('#btn-close-eval-content').hide();
	$('#div-process').html('loading');
	var evalInfoDESC = [];
	var questionaireRANKNO = [];
	var questionaireID = [];
	var questionaire = [];
	var questionaireISREQUIRED = [];
	
	var choicesREMARKS = [];
	var choicesID = [];
	var choices = [];
	var answerTYPE = [];
	
	var categoryID = [];
	
	var ctrlrequired = [];
	var ctrldiv = [];
	var ctrlcreated = [];
	var evaluationID = [];
	var evaluationDESC = [];
	
	var tblrowid;
	var tblid;
	var tbluniqueid;
	var evaluationinfoid;
	
	$('#btn-close-eval-content').on('click',function()
	{
		$('#main-content').html('');
		$('#hd-eval-content').html('');
		$('#div-eval-content').hide();
		$('#div-eval-header').show();
		$(this).hide();
	});
	
	function Initialized(){
		var html_tag = '';
		$.ajax({
		    async: false,
			type:'GET',
			url: '../../model/forms/evaluation/evaluation-controller.php',
			data:{
				mode : 'SEARCH_EVALUATION_DETAILS'
			},
			beforeSend: function(){
					$('#section-eval-main').css("cursor", "progress");
					$('#div-process').html('loading');
			},
			success: function(result){
				var rsstudJSON = JSON.parse(result);
				if (rsstudJSON.length > 0)
				{
					html_tag = '<h4 style="padding-top: 1rem; color: black;"><center>' + rsstudJSON[0].EVAL_INFO_NAME + '</center><h4>';
					html_tag += '<table id="eval-tab" class="table table-hover table-responsive table-bordered" style="width: 90%;height: auto;">';
							
					html_tag += '<thead class="table-primary" style="font-size: 12px; font-family: Roboto, sans-serif; font-weight: normal; text-decoration: none; color: black;">';
					html_tag += '<tr>';
					html_tag += '<th scope="col" style="font-size: 13px;color: darkblue;">SUBJECT</th>';
					html_tag += '<th scope="col" style="font-size: 13px;color: darkblue;">INSTRUCTOR</th>';
					html_tag += '<th scope="col" style="font-size: 13px;color: darkblue;"></th>';
					html_tag += '</tr>';
					html_tag += '</thead>';					
					html_tag += '<tbody id="nav-tab">';
					
					for(i = 0; i< rsstudJSON.length; i++)
					{ 
						html_tag += '<tr>';
						html_tag += '<td style="width: auto;height: auto;text-align: left; font-size: 12px;font-family: Roboto, sans-serif; font-weight: normal; text-decoration: none; color: black;" id="td-' + rsstudJSON[i].TBL_UNIQUE_ID + '">';
						html_tag += (rsstudJSON[i].SUBJ_DESC === undefined? '' : rsstudJSON[i].SUBJ_DESC.replace('[||]',', '));
						html_tag += '</td>';
						html_tag += '<td style="width: auto;height: auto;text-align: left; font-size: 12px;font-family: Roboto, sans-serif; font-weight: normal; text-decoration: none; color: black;" id="td-' + rsstudJSON[i].TBL_ID + '">';
						html_tag += (rsstudJSON[i].EMP_NAME === undefined? '' : rsstudJSON[i].EMP_NAME.replace('[||]',', '));
						html_tag += '</td>';
						if (rsstudJSON[i].EMP_NAME != 'NONE')
						{
							if (rsstudJSON[i].ANSWER.length > 0)
							{
								html_tag += '<td class="img" style="width: auto;height: auto;">';
								html_tag += '</td>';
							} else {
								html_tag += '<td class="" style="width: auto;height: auto;">';
								html_tag += '<button type="button"  style="font-size: 14px;font-family: Roboto, sans-serif; font-weight: normal; text-decoration: none;" class="btn btn-block btn-primary btn-view-questions" id="btnevalid-' + rsstudJSON[i].EVAL_INFO_ID + '" name="btnevalname-'+ rsstudJSON[i].TBL_ID + '"  value="' + rsstudJSON[i].TBL_UNIQUE_ID + '">Evaluate</button>';		
								html_tag += '</td>';
							}
						} else {
							html_tag += '<td class="" style="width: auto;height: auto;font-style: italic;  font-size: 14px;font-family: Roboto, sans-serif; font-weight: normal; text-decoration: none; color: red;">';
							html_tag += 'No Instructor';	
							html_tag += '</td>';
						}
						html_tag += '</tr>';
					}
					html_tag += '</tbody>';
					html_tag += '</table>';
					$('#main-eval').html(html_tag);
					ViewQuestions();
				}  
				else 
				{
					$('#main-eval').html("No Evaluation Information!");
				}
				$('#section-eval-main').css("cursor", "default");
				$('#div-process').html('');
			},
			error:function(status){
				$('#errormessage').html('Error!');
				$('#section-eval-main').css("cursor", "default");
				$('#div-process').html('');
			}
		});
	}
	function addEventHandlerbtnSubmit(){
		$('#btn-submit').click(function() {
			for(x = 0; x< ctrlrequired.length; x++){
				if (!$(ctrlrequired[x]).is(':checked')){
					//alert($(ctrlrequired[x]).is(':checked') + ' ' + ctrlrequired[x]);
					//alert('Answer all required question.');
					var _crtldiv =  $(ctrlrequired[x]).closest('div');
					_crtldiv.css('border', '3px solid red');
					$(ctrlrequired[x]).focus();
					return;
				} else {
					$(ctrldiv[x]).css('border', '0px none transparent');
				}
			}
			var _finalans = '';
			var _evalinfo_id = 0;
			for(x = 0; x< ctrlcreated.length; x++){
				var iddiv = ctrldiv[x].split('-');
				//var evalinfo_id = $(ctrlcreated[x]).attr('id').split('-');
				_finalans = _finalans + ',' + iddiv[2].toString() + '=' + $(ctrlcreated[x] + ':checked').val().toString();
				_evalinfo_id = evaluationinfoid[1];
			}
			var _comments =  $('#txtarea-comments').val();
			$.ajax({
					async: false,
					mode:'GET',
					url: '../../model/forms/evaluation/evaluation-controller.php',
					data:{
						mode : 'INSERT',
						schlevalans_answer: _finalans.substring(1, (_finalans.length)),
						schlevalinfo_id : _evalinfo_id,
						schlevalinfo_tbl_id: tblid,
						schlevalinfo_tbl_unique_id: tbluniqueid,
						schlevalans_comments: _comments
					},
					beforeSend: function(){
						$('#section-eval-main').css("cursor", "progress");
					},
					success: function(result)
					{
						var _btnEval= tblrowid.find("button");
						_btnEval.remove();
						tblrowid.addClass('img');
						$('#btn-close-eval-content').click();
						ctrlcreated = [];
						ctrldiv = [];
						ctrlrequired  = [];
						alert('Submit Successful.');
						$('#div-message').html('Submit Successful.');
					},
					error: function(error){
						alert('Submit Failed, Please try to submit again.');
						$('#div-message').html('Submit Failed, Please try to submit again.');
					},
			});
		});
	}
	
	function ViewQuestions()
	{
		$('.btn-view-questions').on('click',function() 
		{
			$('#div-eval-header').hide();
			$('#div-eval-content').show();
			$('#btn-close-eval-content').show();
			
			var id = $(this).val();
			var str = $(this).attr('name').split('-');
			evaluationinfoid = $(this).attr('id').split('-');
			tbluniqueid = id;
			tblid = str[1];
			var html_question_choices = '';
			var html_question_tag = '';
			var cRow=$(this).closest("tr");
			//tblrowid = $(this).closest("tr");
			tblrowid = cRow.find("td:eq(2)");
			$('#hd-eval-content').html(cRow.find("td:eq(1)").html());
			
			$.ajax({
				mode:'GET',
				url: '../../model/forms/evaluation/evaluation-controller.php',
				data:{
					mode : 'SEARCH_EVALUATION_QUESTIONAIRE',
					schlevalinfo_id : evaluationinfoid[1]
				},
				beforeSend: function(){
					$('#section-eval-main').css("cursor", "progress");
				},
				success: function(result)
				{
					var rsQuestions = JSON.parse(result);
					if (rsQuestions.length > 0)
					{
						for(i = 0; i< rsQuestions.length; i++)
						{    
							if(questionaire.indexOf(rsQuestions[i].QUESTIONAIRE) == -1){
								questionaire.push(rsQuestions[i].QUESTIONAIRE);
								questionaireID.push(rsQuestions[i].QUESTIONAIREID);
								questionaireRANKNO.push(rsQuestions[i].QUEST_RANKNO);
								questionaireISREQUIRED.push(rsQuestions[i].QUEST_IS_REQUIRED);
								answerTYPE.push(rsQuestions[i].ANSTYPE_DESC);
								categoryID.push(rsQuestions[i].CATEGORY_ID);
							}
							if(evaluationDESC.indexOf(rsQuestions[i].CATEGORY) == -1){
								evaluationDESC.push(rsQuestions[i].CATEGORY);
								evaluationID.push(rsQuestions[i].CATEGORY_ID);
							}
							if(choices.indexOf(rsQuestions[i].CHOICES_DESC) == -1){
								choices.push(rsQuestions[i].CHOICES_DESC);
								choicesREMARKS.push(rsQuestions[i].CHOICES_REMARKS);
								choicesID.push(rsQuestions[i].CHOICES_ID);
							}
						}
						for(v=0; v < evaluationDESC.length; v++)
						{
							html_question_tag += '<div id="div-eval-category-' + evaluationID[v] + '" style="font-size: 18px; font-weight: bold; font-style: italic; color: green;">(' + evaluationDESC[v] + ')</div>';
							for(v1=0; v1 < questionaire.length; v1++)
							{
									if (categoryID[v1] === evaluationID[v]){
										
										html_question_tag += '<div id="div-eval-' + questionaireID[v1] + '">';
													html_question_tag += '<p>' + 
																			questionaireRANKNO[v1] + 
																			'.   ' + 
																			' ' + questionaire[v1].replace('[||]',', ') + ' ' +
																			//'' + icat_id_cat1[2].replace('[||]',', ') + ' ' +
																			'<span>' + (questionaireISREQUIRED[v1].toString() == '0' ? '' : ' (Required)') + '</span>';
													html_question_tag += '</p>';
													
													html_question_choices = '';
													
													//var ac = value.QUEST_CHOICE_ANS.split(',');
													//var id = '';
													for(iac=0; iac < choices.length; iac++){
														//var ac_id_desc = ac[iac].split('=');
															html_question_choices += (answerTYPE[v1].toString() === 'SINGLE' ?
																						'<input type="radio" '
																						:
																						(answerTYPE[v1].toString() === 'MULTIPLE' ?
																							'<input type="checkbox" '
																							:
																							'<textarea '));
															html_question_choices += 'id="ans-' + questionaireID[v1].toString() + 
																					 '-' + evaluationID[v].toString() + 
																					 '-' + choicesID[iac].toString()  + 
																					 '-' + tbluniqueid + '" ';
															html_question_choices += (answerTYPE[v1].toString() === 'SINGLE' ?
																						'name="ans-' + questionaireID[v1].toString() + '" value="' + choicesID[iac].toString() + '"/><span>' + choices[iac].toString() + '   ' + choicesREMARKS[iac].toString() + '</span><br>'
																						:
																						(answerTYPE[v1].toString() === 'MULTIPLE' ?
																							'name="ans-' + questionaireID[v1].toString() + '" value="' + choicesID[iac].toString() + '"/><span>' + choices[iac].toString() + '   ' + choicesREMARKS[iac].toString() + '</span><br>'
																							:
																							'name="txtarea-' + questionaireID[v1].toString() + '" rows="4" cols="80" ' + (questionaireISREQUIRED[v1].toString() == '0' ? '' : 'required') + '></textarea><br>'));
														html_question_choices += '</br>';
														
														ctrlcreated.push((answerTYPE[v1].toString() === 'SINGLE' ?
																		'input[name="ans-' +  questionaireID[v1].toString() + '"]'
																		:
																		(answerTYPE[v1].toString() === 'MULTIPLE' ?
																			'input[name="ans-' +  questionaireID[v1].toString() + '"]'
																			:
																			'#txtarea-' +  questionaireID[v1].toString())));
													ctrldiv.push('#div-eval-' + questionaireID[v1]);
													if (questionaireISREQUIRED[v1].toString() == '1'){
															ctrlrequired.push((answerTYPE[v1].toString() === 'SINGLE' ?
																		'input[name="ans-' +  questionaireID[v1].toString() + '"]'
																		:
																		(answerTYPE[v1].toString() === 'MULTIPLE' ?
																			'input[name="ans-' +  questionaireID[v1].toString() + '"]'
																			:
																			'#txtarea-' +  questionaireID[v1].toString())));
																			//alert(icat_id_cat1[1].toString());
													}
												}
											html_question_tag = html_question_tag + html_question_choices;
											html_question_tag += '</div>';
									}
							}
						}
						html_question_tag += '<br><p>COMMENTS</p><textarea id="txtarea-comments" name="txtarea-comments" rows="5" cols="100" maxlength="1000"></textarea>';
						html_question_tag += '<br><button type="button" style="margin-top: 2rem; margin-left: 4rem; padding-left: 2rem; padding-right: 2rem;width: auto; height: auto; font-size: 16px;font-family: Roboto, sans-serif; font-weight: bold; text-decoration: underline;" class="btn btn-block btn-primary" id="btn-submit">Submit</button>';
		
						$('#main-content').html(html_question_tag);
						addEventHandlerbtnSubmit();
					}
					$('#section-eval-main').css("cursor", "default");
				}
			});
		});
	}
	
	Initialized();
	
});