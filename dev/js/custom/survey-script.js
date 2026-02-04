import {toggleLoader} from '../custom/loader.js';

$(document).ready(function () {
    var ctrlrequired = [];
    var ctrldiv = [];
    var ctrlcreated = [];

    var tblid;
    var tblrowid;
    var tbluniqueid;

    var evaltblrowid;
    var evaluationinfoid;

    function ViewListOfSurvey() {
		toggleLoader('disable')// disables all
        $.ajax({
            type: 'POST',
            url: '../../model/forms/forms/survey/survey-controller.php',
            data: {
                mode: 'SEARCH_SURVEY'
            },
            beforeSend: function () {
                // $('*').css("cursor", "wait");
            },
            success: function (result) {
                var rsstudJSON = JSON.parse(result);
                var survey_builder = '';

                if (rsstudJSON.length > 0) {
                    $.each(rsstudJSON, function (key, value) {
                        survey_builder += `<tr>
                                <td id="td-${value.SURVEY_INFO_ID}">${value.SURVEY_INFO_DESC}</td>
                                <td class="text-center w-25">
                                    <button type="button" class="btn btn-sm btn-primary btn-view-survey" id="btnview-question-${value.SURVEY_INFO_ID}" value="${value.SURVEY_INFO_ID}--${value.SURVEY_TBL_NAME}--${value.SURVEY_COL_NAME}--${value.SURVEY_COL_DESC}--${value.SURVEY_TBL_ID}">View</button>
                                </td>
                            </tr>`;
                    });
                } else {
                    survey_builder += `<tr><td colspan="99" class="text-center text-danger">No Available Survey</td></tr>`;
                }

                survey_builder += '</table>';

                $('#list-survey-tbody').html(survey_builder);
                toggleLoader('enable')// enables all

                $('body').on('click', '.btn-view-survey', function () {
    				toggleLoader('disable'); // disables all
					
                    var arr_qry = $(this).val().split('--');
                    var info_id = arr_qry[0];
                    var tbl_name = arr_qry[1];
                    var col_name = arr_qry[2];
                    var col_desc = arr_qry[3];
                    var tbl_id = arr_qry[4];

                    $.ajax({
                        type: 'POST',
                        url: '../../model/forms/forms/survey/survey-controller.php',
                        data: {
                            mode: 'SEARCH_PER_SURVEY',
                            survey_info_id: info_id,
                            survey_tbl_name: tbl_name,
                            survey_col_name: col_name,
                            survey_col_desc: col_desc,
                            survey_tbl_id: tbl_id
                        },
                        beforeSend: function () {
                            // $('*').css("cursor", "wait");
                        },
                        success: function (result) {
                            var surveyJSON = JSON.parse(result);

                            var survey_builder_2 = '';
                            for (var i = 0; i < surveyJSON.length; i++) {
                                var item = surveyJSON[i];

                                survey_builder_2 += `<tr><td id="td-${item.ID}">${item.DESC}</td>`;

                                if (item.ANSWER.length > 0) {
                                    survey_builder_2 += `<td class="text-center text-success"><i class="fa-solid fa-check"></i> Completed</td>`;
                                } else {
                                    survey_builder_2 += `<td class="text-center">
                                        <button type="button" class="btn btn-sm btn-primary btn-survey-questions" id="btnview-question-${info_id}-${item.ID}" value="${item.ID}">Survey</button>
                                    </td>`;
                                }
                                survey_builder_2 += '</tr>';
                            };
                            survey_builder_2 += '</table>';

                            $('#main-survey-tbody').html(survey_builder_2);
                            toggleLoader('enable')// enables all
                            ViewListOfSurveyQuestion();

                            $('#div-list').hide();
                            $('#main-survey').show();
                        },
                        error: function (status) {
                            $('#errormessage').html('Error!');
                            toggleLoader('enable')// enables all
                            // $('*').css("cursor", "default");

                        }
                    });
                });
            },
            error: function (status) {
                $('#errormessage').html('Error!');
                toggleLoader('enable')// enables all
                // $('*').css("cursor", "default");
            }
        });

        $.ajax({
            type: 'POST',
            url: '../../model/forms/forms/survey/survey-controller.php',
            data: {
                mode: 'SEARCH_EVALUATION'
            },
            beforeSend: function () {
                // $('*').css("cursor", "wait");
            },
            success: function (result) {
                var rsstudJSON = JSON.parse(result);

                var evaluation_builder = "";
                if (rsstudJSON.length > 0) {
                    $.each(rsstudJSON, function (key, value) {
                        evaluation_builder += `<tr>
                        	<td id="td-${value.EVAL_INFO_ID}">${value.EVAL_INFO_DESC}</td>
                        	<td class="text-center w-25">
                        		<button type="button" class="btn btn-sm btn-primary btn-view-evaluation" id="btnview-question-${value.EVAL_INFO_ID}" value="${value.EVAL_INFO_ID}">View</button>
                        	</td>
                        </tr>`;
                    });
                    evaluation_builder += '</table>';

                } else {
                    evaluation_builder += `<tr><td colspan="99" class="text-center text-danger">No Available Survey</td></tr>`;
                }
                // $('*').css("cursor", "default");
                $('#list-evaluation-tbody').html(evaluation_builder);
                toggleLoader('enable')// enables all

                $('body').on('click', '.btn-view-evaluation', function () {
					toggleLoader('disable'); // disables all
                    let infoid = $(this).val();
                    $.ajax({
                        type: 'POST',
                        url: '../../model/forms/forms/survey/survey-controller.php',
                        data: {
                            mode: 'SEARCH_PER_EVALUATION',
                            infoid: infoid
                        },
                        success: function (result) {
                            var rsstudJSON = JSON.parse(result);

                            var evaluation_builder_2 = '';
                            for (let i = 0; i < rsstudJSON.length; i++) {
                                evaluation_builder_2 += `<tr>
                                    <td id="td-${rsstudJSON[i].TBL_UNIQUE_ID}">${(rsstudJSON[i].SUBJ_CODE === undefined ? '' : rsstudJSON[i].SUBJ_CODE.replace('[||]', ', '))}</td>
                                    <td id="td-${rsstudJSON[i].TBL_UNIQUE_ID}">${(rsstudJSON[i].SUBJ_DESC === undefined ? '' : rsstudJSON[i].SUBJ_DESC.replace('[||]', ', '))}</td>
                                    <td id="td-${rsstudJSON[i].TBL_ID}">${(rsstudJSON[i].EMP_NAME === undefined ? '' : rsstudJSON[i].EMP_NAME.replace('[||]', ', '))}</td>`;

                                if (rsstudJSON[i].EMP_NAME != 'NONE') {
                                    if (rsstudJSON[i].ANSWER.length > 0) {
                                        evaluation_builder_2 += '<td class="text-center text-success"><i class="fa-solid fa-check"></i> Completed</td>';
                                    } else {
                                        evaluation_builder_2 += `<td class="text-center">
                                                <button type="button" class="btn btn-sm btn-primary btn-evaluation-questions" id="btnevalid-${rsstudJSON[i].EVAL_INFO_ID}" name="btnevalname-${rsstudJSON[i].TBL_ID}" value="${rsstudJSON[i].TBL_UNIQUE_ID}">Evaluate</button>
                                            </td>`;
                                    }
                                } else {
                                    evaluation_builder_2 += '<td class="text-center text-danger"> No Instructor</td>';
                                }
                                evaluation_builder_2 += '</tr>';
                            }

                            $('#main-evaluation-tbody').html(evaluation_builder_2);
                            toggleLoader('enable')// enables all
                            ViewListOfEvaluationQuestion();

                            $('#div-list').hide();
                            $('#main-evaluation').show();
                            // $('*').css("cursor", "default");
                        },
                        error: function (status) {
                            $('#errormessage').html('Error!');
                            toggleLoader('enable')// enables all
                            // $('*').css("cursor", "default");

                        }
                    });
                });
            },
            error: function (status) {
                $('#errormessage').html('Error!');
                toggleLoader('enable')// enables all
                // $('*').css("cursor", "default");

            }
        });
		
		toggleLoader('enable');  // enables all
    }

    function ViewListOfSurveyQuestion() {
        $('.btn-survey-questions').on('click', function () {
			toggleLoader('disable'); // disables all
			
            var id = $(this).val(); //Department ID
            var name = $(this).attr('id');
            var info_id = name.split('-')[2];
            tblid = id;
            let btnview = $(this);
            var html_question_choices = '';
            var html_question_tag = '';

            var cRow = $(this).closest("tr");
            tblrowid = cRow.find("td:eq(1)");
            $('#hd-survey-content').html(cRow.find("td:eq(0)").html());
            $('#hd-survey-description').html('Greetings FCPCians! To improve the quality of services we provide, we would like you to accomplish the survey as honest and constructive as possible. Your responses will be treated confidential.');

            $.ajax({
                type: 'POST',
                url: '../../model/forms/forms/survey/survey-controller.php',
                data: {
                    mode: 'SEARCH_SURVEY_QUESTIONAIRE',
                    INFOID: info_id,
                    TBLID: id
                },
                success: function (result) {
                    var rsstudJSON = JSON.parse(result);
                    if (rsstudJSON.length > 0) {
                        $.each(rsstudJSON, function (key, value) {
                            html_question_tag += `<div id="div-survey-${value.ID}">
                            <p class='fw-bold'>${value.RANK_NO != 0 ? value.RANK_NO + '. ' : ''}${value.QUESTIONS.replace('[||]', ', ')}
                                <span class='text-danger fw-medium'>${(value.QUEST_IS_REQUIRED.toString() == '0' ? '' : '*')}</span>
                            </p>`;

                            html_question_choices = '';
                            var ac = value.QUEST_CHOICE_ANS.split(',');

                            for (let iac = 0; iac < ac.length; iac++) {
                                var ac_id_desc = ac[iac].split('=');

                                const ansType = value.ANS_TYPE_CODE.toString();
                                const elementId = `${info_id}-${value.ID}-${value.ANS_TYPE_ID}-${ac_id_desc[0]}`;
                                const isRequired = value.QUEST_IS_REQUIRED.toString() !== '0';

                                html_question_choices += `<div class="d-inline-flex align-middle">`;

                                if (ansType === 'SINGLE') {

                                    html_question_choices += `<input type="radio" id="${elementId}" name="${value.ID}" value="${ac_id_desc[0]}" class="mb-2 mx-2"/>
                                            <label for="${elementId}" class="mb-2 me-4">
                                                <span class="mb-2 me-2">${ac_id_desc[1]}</span>${ac_id_desc[2]}
                                            </label>`;

                                } else if (ansType === 'MULTIPLE') {
                                    html_question_choices += `<input type="checkbox" id="${elementId}" name="${value.ID}-${ac_id_desc[0]}" value="${ac_id_desc[0]}" class="mb-2 mx-2"/>
                                            <label for="${elementId}" class="mb-2 me-4">
                                                <span class="mb-2 me-2">${ac_id_desc[1]}</span>${ac_id_desc[2]}
                                            </label>`;

                                } else {
                                    html_question_choices += `<textarea id="${elementId}" name="txtarea-${value.ID}" rows="4" cols="80" ${isRequired ? 'required' : ''}></textarea>`
                                }
                                
                                html_question_choices += `</div>`;
                            }
                            html_question_tag = html_question_tag + html_question_choices;
                            html_question_tag += '</div>';
                            html_question_tag += '<hr>';
                            
                            const ansType = value.ANS_TYPE_CODE.toString();
                            const inputSelector = (ansType === 'SINGLE' || ansType === 'MULTIPLE')
                                ? `input[name="${value.ID}"]`
                                : `#txtarea-${value.ID}`;

                            // created controls
                            ctrlcreated.push(inputSelector);

                            // container div
                            ctrldiv.push(`#div-survey-${value.ID}`);

                            // required controls
                            if (value.QUEST_IS_REQUIRED.toString() === '1') {
                                ctrlrequired.push(inputSelector);
                            }
                        });
                        html_question_tag += '<h5 class="text-success">Comments & Suggestions:</h5><textarea id="txtarea-comments" name="txtarea-comments" maxlength="1000" placeholder="Write your comments here.."></textarea>';
                        html_question_tag += '<button type="button" class="btn btn-sm btn-primary" id="btn-submit-survey" name="btn-submit-survey">Submit</button>';
                        $('#main-survey').hide();
                        $('#div-survey-content').show();
                        $('#main-survey-content').html(html_question_tag);
                        toggleLoader('enable')// enables all

                        // SubmitSurvey();
                        $('#btn-submit-survey').click(function () {
							toggleLoader('disable'); // disables all
                            for (let x = 0; x < ctrlrequired.length; x++) {
                                if (!$(ctrlrequired[x]).is(':checked')) {
                                    $(ctrldiv[x]).addClass('alert alert-danger');
									
									setTimeout(() => {
										$(ctrlrequired[x]).focus();
									}, 50);
									
									toggleLoader('enable'); // enables all
                                    return;
                                } else {
                                    $(ctrldiv[x]).removeClass('alert alert-danger');
                                }
                            }
                            var _finalans_arr = [];
                            var _finalans = '';
                            var _survinfo_id = 0;
                            for (let x = 0; x < ctrlcreated.length; x++) {
                                var iddiv = ctrldiv[x].split('-');
                                let _perquestion = iddiv[2].toString() + '=' + $(ctrlcreated[x] + ':checked').val().replace(/[^a-zA-Z0-9. ]/g, '');
                                _survinfo_id = $(ctrlcreated[x]).attr('id').split('-')[0];

                                if (!_finalans_arr.includes(_perquestion)) {
                                    _finalans_arr.push(_perquestion);
                                }
                            }
							
                            var _comments = $('#txtarea-comments').val().replace(/[^a-zA-Z0-9. ]/g, '');
                            _finalans = _finalans_arr.join(',');

                            $.ajax({
                                async: false,
                                type: 'POST',
                                url: '../../model/forms/forms/survey/survey-controller.php',
                                data: {
                                    mode: 'INSERT_SURVEY',
                                    schlsurvans_answer: _finalans,
                                    schlsurvinfo_id: _survinfo_id,
                                    schlsurvinfo_tbl_id: tblid,
                                    schlsurvinfo_comments: _comments
                                },
                                success: function (result) {
                                    console.log(result);
                                    var _btnSurvey = tblrowid.find("button");
                                    tblrowid.html(`<span class="text-center text-success"><i class="fa-solid fa-check"></i> Completed</span>`);
                                    
									setTimeout(() => {
                                    	_btnSurvey.remove();
										$('#btn-close-survey-content').click();
									}, 50);
									
                                    ctrlcreated = [];
                                    ctrldiv = [];
                                    ctrlrequired = [];
                                    // alert('Submit Successful.');
                                },
                                error: function () {
                                    alert('Failed to submit, Click the submit button again.');
                                    toggleLoader('enable')// enables all
                                },
								complete: function (){
                    				toggleLoader('enable')// enables all
								}
                            });
                        });
                    } else {
                        $('#main-survey-content').html('<div style="font-size: 20px; font-weight: bold; font-style: italic; text-decoration: none;color: red;">NO ASSIGNED QUESTIONAIRE</div>');
                    }
                },
                error: function (status) {
                    $('#errormessage').html('Error!');
                    toggleLoader('enable')// enables all
                }
            });
        });
    }

    function ViewListOfEvaluationQuestion() {
        $('.btn-evaluation-questions').on('click', function () {
			toggleLoader('disable'); // disables all
            var id = $(this).val();
            var str = $(this).attr('name').split('-');
            evaluationinfoid = $(this).attr('id').split('-');
            tbluniqueid = id;
            tblid = str[1];
            var html_question_tag = '';
            var cRow = $(this).closest("tr");
            //tblrowid = $(this).closest("tr");
            evaltblrowid = cRow.find("td:eq(3)");
            $('#hd-survey-content').html(cRow.find("td:eq(1)").html());
            $('#hd-survey-description').html('As part of the continuous improvement culture of our school, we would like you to accomplish this evaluation instrument as honest and constructive as possible. Rest assured that your responses will be treated with confidentiality. Kindly rate the following items according to the degree of your agreement.');

            $.ajax({
                type: 'POST',
                url: '../../model/forms/forms/survey/survey-controller.php',
                data: {
                    mode: 'SEARCH_EVALUATION_QUESTIONAIRE',
                    schlevalinfo_id: evaluationinfoid[1]
                },
                beforeSend: function () {
                    // $('*').css("cursor", "progress");
                },
                success: function (result) {
                    var rsQuestions = JSON.parse(result);
                    if (rsQuestions.length > 0) {
                        var groupedData = {};

                        $.each(rsQuestions, function (index, item) {
                            var category = item.CATEGORY;

                            if (!groupedData[category]) {
                                groupedData[category] = [];
                            }
                            groupedData[category].push(item);
                        });

                        $.each(groupedData, function (categoryId, categoryItems) {
                            html_question_tag += `<div id="div-eval-category" class="mb-4 pb-4"><h5 class="text-success fw-bold">${categoryId}</h5>`;

                            $.each(categoryItems, function (index, value) {
                                //do the code here

                                html_question_tag += `<div id="div-eval-${value.QUESTIONAIREID}">
                                    <p class='fw-bold'>${value.QUEST_RANKNO != 0 ? value.QUEST_RANKNO + '. ' : ''}${value.QUESTIONAIRE.replace('[||]', ', ')}
                                        <span class='text-danger fw-medium'>${(value.QUEST_IS_REQUIRED.toString() == '0' ? '' : '*')}</span>
                                    </p>`;

                                var choices_arr = value.QUEST_CHOICE_ANS.split(',');

                                $.each(choices_arr, function (key, item) {
                                    const [choices_id, choices_desc, choices_remarks] = choices_arr[key].split('=');

                                    const ansType = value.ANSTYPE_DESC.toString();
                                    const isRequired = value.QUEST_IS_REQUIRED.toString() === '1';
                                    const elementId = `ans-${value.QUESTIONAIREID}-${value.CATEGORY_ID}-${choices_id}-${tbluniqueid}`;

                                    let html_question_choices = '<div class="d-inline-flex">';
                                    /* ---------- INPUT / TEXTAREA CREATION ---------- */
                                    if (ansType === 'SINGLE') {
                                        html_question_choices += `<input type="radio" id="${elementId}" name="ans-${value.QUESTIONAIREID}" value="${choices_id}" class="mb-2 mx-2"/>
                                                <label for="${elementId}" class="mb-2 me-4">
                                                    <span class="mb-2 me-2">${choices_desc}</span>${choices_remarks}
                                                </label>`;
                                    } else if (ansType === 'MULTIPLE') {
                                        html_question_choices += `<input type="checkbox" id="${elementId}" name="ans-${value.QUESTIONAIREID}" value="${choices_id}" class="mb-2 mx-2"/>
                                                <label for="${elementId}" class="mb-2 me-4">
                                                    <span class="mb-2 me-2">${choices_desc}</span>${choices_remarks}
                                                </label>`;
                                    } else {
                                        html_question_choices = `<textarea id="${elementId}" name="txtarea-${value.QUESTIONAIREID}" maxlength="1000" class="txtarea-comments" ${isRequired ? 'required' : ''}></textarea>`;
                                    }
                                    html_question_choices += `</div>`;

                                    /* ---------- SELECTORS ---------- */
                                    const controlSelector = (ansType === 'SINGLE' || ansType === 'MULTIPLE') ? `input[name="ans-${value.QUESTIONAIREID}"]` : `textarea[name="txtarea-${value.QUESTIONAIREID}"]`;

                                    ctrlcreated.push(controlSelector);
                                    ctrldiv.push(`#div-eval-${value.QUESTIONAIREID}`);

                                    if (isRequired) {
                                        ctrlrequired.push(controlSelector);
                                    }

                                    html_question_tag += html_question_choices;
                                });

                                html_question_tag += `</div><hr>`;
                            });

                            html_question_tag += '</div>';

                        });
                        
                        html_question_tag += '<h5 class="text-success">Comments & Suggestions:</h5><textarea id="txtarea-comments" name="txtarea-comments" maxlength="1000" placeholder="Write your comments here.."></textarea>';
                        html_question_tag += '<button type="button" class="btn btn-sm btn-primary" id="btn-submit-evaluation" name="btn-submit-evaluation">Submit</button>';
                        
                        // $('*').css("cursor", "default");
                        $('#main-evaluation').hide();
                        $('#div-evaluation-content').show();
                        $('#main-evaluation-content').html(html_question_tag);
                        toggleLoader('enable')// enables all

                        $('#btn-submit-evaluation').click(function () {
							toggleLoader('disable'); // disables all
							
                            // console.log(ctrldiv);
                            for (let x = 0; x < ctrlrequired.length; x++) {
                                if (ctrlrequired[x].includes('input')) {
                                    if (!$(ctrlrequired[x]).is(':checked')) {
                                        var _crtldiv = $(ctrlrequired[x]).closest('div').parent('div');
                                        
                                        _crtldiv.addClass('alert alert-danger');
										
										setTimeout(() => {
											$(ctrlrequired[x]).focus();
										}, 50);
										
										toggleLoader('enable')// enables all
                                        return;
                                    } else {
                                        var _crtldiv = $(ctrlrequired[x]).closest('div').parent('div');
                                        _crtldiv.removeClass('alert alert-danger');

                                    }


                                } else if (ctrlrequired[x].includes('textarea')) {
                                    if ($(ctrlrequired[x]).val().trim() === '') {
                                        var _crtldiv = $(ctrlrequired[x]).closest('div').parent('div');
                                        _crtldiv.addClass('alert alert-danger');
										
										setTimeout(() => {
											$(ctrlrequired[x]).focus();
										}, 50);
										
										toggleLoader('enable')// enables all
                                        return;
                                    } else {
                                        var _crtldiv = $(ctrlrequired[x]).closest('div').parent('div');
                                        _crtldiv.removeClass('alert alert-danger');
                                    }
                                }


                            }
                            var _finalans_arr = [];
                            var _finalans = '';

                            for (let x = 0; x < ctrlcreated.length; x++) {
                                var iddiv = ctrldiv[x].split('-');
                                //var evalinfo_id = $(ctrlcreated[x]).attr('id').split('-');
								var _text = '';
                                if (ctrlcreated[x].includes('input')) {
                                    _text = ctrlcreated[x] + ':checked';
                                } else if (ctrlcreated[x].includes('textarea')) {
                                    _text = ctrlcreated[x];
                                }

                                var _perquestion = iddiv[2].toString() + '=' + $(_text).val().toString().replace(/[^a-zA-Z0-9. ]/g, '');

                                // console.log(_perquestion);
                                if (!_finalans_arr.includes(_perquestion)) {
                                    _finalans_arr.push(_perquestion);
                                }
                            }

                            var _comments = $('#txtarea-comments').val().replace(/[^a-zA-Z0-9. ]/g, '');
                            _finalans = _finalans_arr.join(',');
                            
                            $.ajax({
                                async: false,
                                type: 'POST',
                                url: '../../model/forms/forms/survey/survey-controller.php',
                                data: {
                                    mode: 'INSERT_EVALUATION',
                                    schlevalans_answer: _finalans,
                                    schlevalinfo_id: evaluationinfoid[1],
                                    schlevalinfo_tbl_id: tblid,
                                    schlevalinfo_tbl_unique_id: tbluniqueid,
                                    schlevalans_comments: _comments
                                },
                                beforeSend: function () {
                                    // $('*').css("cursor", "progress");
                                },
                                success: function (result) {
                                    var _btnEval = evaltblrowid.find("button");
									setTimeout(() => {
                                    	_btnEval.remove();
										$('#btn-close-evaluation-content').click();
									}, 50);
									
                                    evaltblrowid.html(`<span class="text-center text-success"><i class="fa-solid fa-check"></i> Completed</span>`);
									
                                    ctrlcreated = [];
                                    ctrldiv = [];
                                    ctrlrequired = [];
                                    // alert('Submit Successful.');
                                    // $('#div-message').html('Submit Successful.');
                                    
                                    // $('*').css("cursor", "default");
                                },
                                error: function (error) {
                                    alert('Submit Failed, Please try to submit again.');
                                    $('#div-message').html('Submit Failed, Please try to submit again.');
                                },
                                complete: function () {
                                    // $('*').css("cursor", "default");
                                    toggleLoader('enable')// enables all
                                },
                            });
                        });
                    } else {
                        $('#main-evaluation-content').html('<div style="font-size: 20px; font-weight: bold; font-style: italic; text-decoration: none;color: red;">NO ASSIGNED QUESTIONAIRE</div>');
                        // $('*').css("cursor", "default");
                    }
                },
                complete: function () {
                    toggleLoader('enable')// enables all
                    // $('*').css("cursor", "default");
                },
            });
        });
    }

    $('#btn-close-survey-content').on('click', function () {
        $('#main-survey').show();
        $('#div-survey-content').hide();
    });

    $('#btn-close-evaluation-content').on('click', function () {
        $('#main-evaluation').show();
        $('#div-evaluation-content').hide();
    });

    $('body').on('click', '#btn-close-list', function () {
        $('#main-survey, #main-evaluation, #div-survey-content').hide();
        $('#div-list').show();
    });

    function Initialized() {
        $('#main-survey, #main-evaluation, #div-survey-content, #div-evaluation-content').hide();
        ViewListOfSurvey();
    }

    Initialized();
});