$(document).ready(function(){
    var formtype = $('#form').val();
    var formtype_abbr = $('#form option:selected').attr('name');

    // THIS IS CALLED ON PAGE LOAD
    $('*').css({'cursor' : 'wait'});
    $('button, select').attr('disabled', true);

    $.ajax({
        type:'GET',
        url: '../../model/forms/surveymonitoring/surveymonitoring-controller.php',
        data:{
            type : 'ACADLEVEL',
            formtype_abbr : formtype_abbr,
            formtype : formtype
        },
        success: function(result){
            MyDropdown(result, "#acadlevel");
            var lvlid = $('#acadlevel').val();

            $.ajax({
                type:'GET',
                url: '../../model/forms/surveymonitoring/surveymonitoring-controller.php',
                data:{
                    type : 'ACADYEAR',
                    formtype_abbr : formtype_abbr,
                    formtype : formtype,
                    levelid: lvlid
                },
                success: function(result){
                    MyDropdown(result, "#acadyear");
                    var yrid = $('#acadyear').val();

                    $.ajax({
                        type:'GET',
                        url: '../../model/forms/surveymonitoring/surveymonitoring-controller.php',
                        data:{
                            type : 'ACADPERIOD',
                            formtype_abbr : formtype_abbr,
                            formtype : formtype,
                            levelid: lvlid,
                            yearid : yrid
                        },
                        success: function(result){
                            MyDropdown(result, "#acadperiod");
                            var prdid = $('#acadperiod').val();

                            $.ajax({
                                type:'GET',
                                url: '../../model/forms/surveymonitoring/surveymonitoring-controller.php',
                                data:{
                                    type : 'FORM',
                                    formtype : formtype,
                                    formtype_abbr : formtype_abbr,
                                    levelid: lvlid,
                                    yearid : yrid,
                                    periodid : prdid
                                },
                                success: function(result){
                                    MyDropdown(result, "#formname");
                                    MyProcess();

                                },
                                error:function(status){
                                    $('#errormessage').html('Error!');
                                }
                            });
                        },
                        error:function(status){
                            $('#errormessage').html('Error!');
                        }
                    });
                },
                error:function(status){
                    $('#errormessage').html('Error!');
                }
            });
        }
    });

    $('#convert').click(function(){
        var table_content = '<table>';
        table_content += $('#table_content').html();
        table_content += '</table>';
        $('#file_content').val(table_content);
        $('#convert_form').submit();
    });
    
    $('#form').change(function(){
        var formtype = $('#form').val();
        var formtype_abbr = $('#form option:selected').attr('name');
        $('*').css({'cursor' : 'wait'});
        $('button, select').attr('disabled', true);

        $.ajax({
            type:'GET',
            url: '../../model/forms/surveymonitoring/surveymonitoring-controller.php',
            data:{
                type : 'ACADLEVEL',
                formtype_abbr : formtype_abbr,
                formtype : formtype
            },
            success: function(result){
                MyDropdown(result, "#acadlevel");

                var lvlid = $('#acadlevel').val();

                $.ajax({
                    type:'GET',
                    url: '../../model/forms/surveymonitoring/surveymonitoring-controller.php',
                    data:{
                        type : 'ACADYEAR',
                        formtype_abbr : formtype_abbr,
                        formtype : formtype,
                        levelid: lvlid
                    },
                    success: function(result){
                        MyDropdown(result, "#acadyear");

                        var yrid = $('#acadyear').val();

                        $.ajax({
                            type:'GET',
                            url: '../../model/forms/surveymonitoring/surveymonitoring-controller.php',
                            data:{
                                type : 'ACADPERIOD',
                                formtype_abbr : formtype_abbr,
                                formtype : formtype,
                                levelid: lvlid,
                                yearid : yrid
                            },
                            success: function(result){
                                MyDropdown(result, "#acadperiod");

                                var prdid = $('#acadperiod').val();

                                $.ajax({
                                    type:'GET',
                                    url: '../../model/forms/surveymonitoring/surveymonitoring-controller.php',
                                    data:{
                                        type : 'FORM',
                                        formtype : formtype,
                                        formtype_abbr : formtype_abbr,
                                        levelid: lvlid,
                                        yearid : yrid,
                                        periodid : prdid
                                    },
                                    success: function(result){
                                        MyDropdown(result, "#formname");
                                        $('*').css({'cursor' : 'default'});
                                        $('button, select').attr('disabled', false);

                                    },
                                    error:function(status){
                                        $('#errormessage').html('Error!');
                                    }
                                });
                            },
                            error:function(status){
                                $('#errormessage').html('Error!');
                            }
                        });
                    },
                    error:function(status){
                        $('#errormessage').html('Error!');
                    }
                });
            }
        });
    });

    $('#acadlevel').change(function(){
        var formtype = $('#form').val();
        var formtype_abbr = $('#form option:selected').attr('name');
        var lvlid = $('#acadlevel').val();
        $('*').css({'cursor' : 'wait'});
        $('button, select').attr('disabled', true);

        $.ajax({
            type:'GET',
            url: '../../model/forms/surveymonitoring/surveymonitoring-controller.php',
            data:{
                type : 'ACADYEAR',
                formtype_abbr : formtype_abbr,
                formtype : formtype,
                levelid: lvlid
            },
            success: function(result){
                MyDropdown(result, "#acadyear");

                var yrid = $('#acadyear').val();

                $.ajax({
                    type:'GET',
                    url: '../../model/forms/surveymonitoring/surveymonitoring-controller.php',
                    data:{
                        type : 'ACADPERIOD',
                        formtype_abbr : formtype_abbr,
                        formtype : formtype,
                        levelid: lvlid,
                        yearid : yrid
                    },
                    success: function(result){
                        MyDropdown(result, "#acadperiod");
                        var prdid = $('#acadperiod').val();

                        $.ajax({
                            type:'GET',
                            url: '../../model/forms/surveymonitoring/surveymonitoring-controller.php',
                            data:{
                                type : 'FORM',
                                formtype : formtype,
                                formtype_abbr : formtype_abbr,
                                levelid: lvlid,
                                yearid : yrid,
                                periodid : prdid
                            },
                            success: function(result){
                                MyDropdown(result, "#formname");
                                $('*').css({'cursor' : 'default'});
                                $('button, select').attr('disabled', false);

                            },
                            error:function(status){
                                $('#errormessage').html('Error!');
                            }
                        });
                    },
                    error:function(status){
                        $('#errormessage').html('Error!');
                    }
                });
            },
            error:function(status){
                $('#errormessage').html('Error!');
            }
        });
    });

    $('#acadyear').change(function(){
        var formtype = $('#form').val();
        var formtype_abbr = $('#form option:selected').attr('name');
        var lvlid = $('#acadlevel').val();
        var yrid = $('#acadyear').val();
        $('*').css({'cursor' : 'wait'});
        $('button, select').attr('disabled', true);

        $.ajax({
            type:'GET',
            url: '../../model/forms/surveymonitoring/surveymonitoring-controller.php',
            data:{
                type : 'ACADPERIOD',
                formtype_abbr : formtype_abbr,
                formtype : formtype,
                levelid: lvlid,
                yearid : yrid
            },
            success: function(result){
                MyDropdown(result, "#acadperiod");
                var prdid = $('#acadperiod').val();

                $.ajax({
                    type:'GET',
                    url: '../../model/forms/surveymonitoring/surveymonitoring-controller.php',
                    data:{
                        type : 'FORM',
                        formtype : formtype,
                        formtype_abbr : formtype_abbr,
                        levelid: lvlid,
                        yearid : yrid,
                        periodid : prdid
                    },
                    success: function(result){
                        MyDropdown(result, "#formname");
                        $('*').css({'cursor' : 'default'});
                        $('button, select').attr('disabled', false);

                    },
                    error:function(status){
                        $('#errormessage').html('Error!');
                    }
                });
            },
            error:function(status){
                $('#errormessage').html('Error!');
            }
        });
    });

    $('#acadperiod').change(function(){
        var formtype = $('#form').val();
        var formtype_abbr = $('#form option:selected').attr('name');
        var lvlid = $('#acadlevel').val();
        var yrid = $('#acadyear').val();
        var prdid = $('#acadperiod').val();
        $('*').css({'cursor' : 'wait'});
        $('button, select').attr('disabled', true);

        $.ajax({
            type:'GET',
            url: '../../model/forms/surveymonitoring/surveymonitoring-controller.php',
            data:{
                type : 'FORM',
                formtype : formtype,
                formtype_abbr : formtype_abbr,
                levelid: lvlid,
                yearid : yrid,
                periodid : prdid
            },
            success: function(result){
                MyDropdown(result, "#formname");
                $('*').css({'cursor' : 'default'});
                $('button, select').attr('disabled', false);

            },
            error:function(status){
                $('#errormessage').html('Error!');
            }
        });
    });

    
    $("#btnSearchForm").click(function(){
        $('*').css({'cursor' : 'wait'});
        $('button, select').attr('disabled', true);
        MyProcess();
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

function MyProcess(){
    $('#search_result').hide();
    var level = $('#acadlevel option:selected').text();
    var year = $('#acadyear option:selected').text();
    var period = $('#acadperiod option:selected').text();
    var formname = $('#formname option:selected').text();
    
    var formtype = $('#form').val();
    var formtype_abbr = $('#form option:selected').attr('name');
    var lvlid = $('#acadlevel').val();
    var yrid = $('#acadyear').val();
    var prdid = $('#acadperiod').val();

    $('#table_content tr').remove();

    $.ajax({
        type:'GET',
        url: '../../model/forms/surveymonitoring/surveymonitoring-controller.php',
        data:{
            type : 'SEARCH',
            levelid: lvlid,
            yearid : yrid,
            periodid : prdid
        },
        success: function(result){
            var ret = JSON.parse(result);

            var table_builder = "";
            table_builder += "<tr>";
            table_builder += "<th rowspan='2' style='width: 12.5rem; text-align: center; border: 1px black solid; padding-inline: 1rem; padding-block: .25rem; background-color: #8DB4E2; color: black;'>YEAR LEVEL</th>";
            table_builder += "<th rowspan='2' style='width: 12.5rem; text-align: center; border: 1px black solid; padding-inline: 1rem; padding-block: .25rem; background-color: #8DB4E2; color: black;'>SECTION</th>";
            table_builder += "<th colspan='3' style='width: 12.5rem; text-align: center; border: 1px black solid; padding-inline: 1rem; padding-block: .25rem; background-color: #8DB4E2; color: black;'>TOTAL ENROLLED</th>";
            table_builder += "<th colspan='2' style='width: 12.5rem; text-align: center; border: 1px black solid; padding-inline: 1rem; padding-block: .25rem; background-color: #8DB4E2; color: black;'>ANSWERED</th>";
            table_builder += "<th colspan='2' style='width: 12.5rem; text-align: center; border: 1px black solid; padding-inline: 1rem; padding-block: .25rem; background-color: #8DB4E2; color: black;'>HAVE YET TO ANSWER</th>";
            table_builder += "</tr>";
            table_builder += "<tr>";
            table_builder += "<th style='width: 12.5rem; text-align: center; border: 1px black solid; padding-inline: 1rem; padding-block: .25rem; background-color: #8DB4E2; color: black;'>Female</th>";
            table_builder += "<th style='width: 12.5rem; text-align: center; border: 1px black solid; padding-inline: 1rem; padding-block: .25rem; background-color: #8DB4E2; color: black;'>Male</th>";
            table_builder += "<th style='width: 12.5rem; text-align: center; border: 1px black solid; padding-inline: 1rem; padding-block: .25rem; background-color: #8DB4E2; color: black;'>Total</th>";
            table_builder += "<th style='width: 12.5rem; text-align: center; border: 1px black solid; padding-inline: 1rem; padding-block: .25rem; background-color: #8DB4E2; color: black;'>Count (#)</th>";
            table_builder += "<th style='width: 12.5rem; text-align: center; border: 1px black solid; padding-inline: 1rem; padding-block: .25rem; background-color: #8DB4E2; color: black;'>Percentage (%)</th>";
            table_builder += "<th style='width: 12.5rem; text-align: center; border: 1px black solid; padding-inline: 1rem; padding-block: .25rem; background-color: #8DB4E2; color: black;'>Count (#)</th>";
            table_builder += "<th style='width: 12.5rem; text-align: center; border: 1px black solid; padding-inline: 1rem; padding-block: .25rem; background-color: #8DB4E2; color: black;'>Percentage (%)</th>";
            table_builder += "</tr>";

            if(ret.length){
                $.each(ret, function(key, value) {
                    table_builder += "<tr>";
                    // table_builder += "<td>" + value.CRSE + "</td>";
                    table_builder += "<td style='width: 12.5rem; text-align: left; border: 1px black solid; padding-inline: 1rem; padding-block: .25rem;'>" + value.YEARLEVEL + "</td>";
                    table_builder += "<td style='width: 12.5rem; text-align: left; border: 1px black solid; padding-inline: 1rem; padding-block: .25rem;'>" + value.NAME + "</td>";
                    table_builder += "<td id='enrolled-female-" + value.SEC_ID + "' style='width: 12.5rem; text-align: center; border: 1px black solid; padding-block: .25rem;'>" + value.FEMALE + "</td>";
                    table_builder += "<td id='enrolled-male-" + value.SEC_ID + "' style='width: 12.5rem; text-align: center; border: 1px black solid; padding-block: .25rem;'>" + value.MALE + "</td>";
                    table_builder += "<td id='total-enrolled-" + value.SEC_ID + "' style='width: 12.5rem; text-align: center; border: 1px black solid; padding-block: .25rem;'>" + value.TOTAL_ENROLLED + "</td>";
                    table_builder += "<td id='total-answered-" + value.SEC_ID + "' style='width: 12.5rem; text-align: center; border: 1px black solid; padding-block: .25rem;'>" + "0" + "</td>";
                    table_builder += "<td id='answered-percentage-" + value.SEC_ID + "' style='width: 12.5rem; text-align: center; border: 1px black solid; padding-block: .25rem;'>" + "-" + "</td>";
                    table_builder += "<td id='total-not-answered-" + value.SEC_ID + "' style='width: 12.5rem; text-align: center; border: 1px black solid; padding-block: .25rem;'>" + value.TOTAL_ENROLLED + "</td>";
                    table_builder += "<td id='not-answered-percentage-" + value.SEC_ID + "' style='width: 12.5rem; text-align: center; border: 1px black solid; padding-block: .25rem;'> 100 %</td>";
                    table_builder += "</tr>";
                });

                table_builder += "<tr>";
                table_builder += "<td colspan='5' style='width: 12.5rem; text-align: right; border: 1px black solid; padding-inline: 1rem; padding-block: .25rem;'>TOTAL ANSWER COUNT:</td>";
                table_builder += "<td style='width: 12.5rem; text-align: center; border: 1px black solid; padding-block: .25rem;' id='total_answer_count'>-</td>";
                table_builder += "<td style='border: 1px black solid; padding-block: .25rem;'>-</td>";
                table_builder += "<td style='width: 12.5rem; text-align: center; border: 1px black solid; padding-block: .25rem;'>-</td>";
                table_builder += "<td style='border: 1px black solid; padding-block: .25rem;'>-</td>";
                table_builder += "</tr>";
        
                $('#table_content').append(table_builder);
                
                $.ajax({
                    type:'GET',
                    url: '../../model/forms/surveymonitoring/surveymonitoring-controller.php',
                    data:{
                        type : 'SEARCH_SURVEY_ANSWERED',
                        formtype : formtype,
                        formtype_abbr : formtype_abbr,
                        levelid: lvlid,
                        yearid : yrid,
                        periodid : prdid
                    },
                    success: function(result){
                        var ret = JSON.parse(result);
                        if(ret.length){
                            var total_answer_count = 0;
                            $.each(ret, function(key, item) {
                                var _answered = '#total-answered-' + item.SEC_ID.toString();
                                var _not_answered = '#total-not-answered-' + item.SEC_ID.toString();
                                var _not_answered_percentage = '#not-answered-percentage-' + item.SEC_ID.toString();
                                var _ans_percent = '#answered-percentage-' + item.SEC_ID.toString();
                                var _enrolled = '#total-enrolled-' + item.SEC_ID.toString();
                                total_answer_count += parseInt(item.ANSWERED_COUNT);

                                var total_enrolled = $(_enrolled).text();
                                answered_percentage = (parseFloat(item.ANSWERED_COUNT) / parseFloat(total_enrolled)) * 100;
                                not_answered = parseFloat(total_enrolled) - parseFloat(item.ANSWERED_COUNT);
                                not_answered_percentage = (not_answered / total_enrolled) * 100;

                                $(_answered).html(item.ANSWERED_COUNT.toString());
                                $(_ans_percent).html(answered_percentage.toFixed(2) + ' %');

                                $(_not_answered).html(not_answered);
                                $(_not_answered_percentage).html(not_answered_percentage.toFixed(2) + ' %');

                                if(answered_percentage.toFixed(2) >= 90 || parseFloat(total_enrolled) == parseFloat(item.ANSWERED_COUNT)){
                                    $(_answered).closest('tr').css('background-color', 'forestgreen');
                                } else {
                                    $(_answered).closest('tr').css('background-color', '#8DB4E2');
                                }

                                $(_answered).closest('tr').children('td').addClass('text-white');
                            });

                            $('#total_answer_count').html(total_answer_count);
                        }

                        $('#search_result').show();
                        $('*').css({'cursor' : 'default'});
                        $('button, select').attr('disabled', false);
                    },
                    error:function(status){
                        $('#errormessage').html('Error!');
                    }
                });

            } else {
                table_builder += "<tr>";
                table_builder += "<td style='text-decoration: underline; text-align: center; border: 1px black solid; padding-block: .25rem; color: red;'>NO RESULTS FOUND</td>";
                table_builder += "</tr>";
                $('#table_content').append(table_builder);
                $('#search_result').show();
                $('*').css({'cursor' : 'default'});
                $('button, select').attr('disabled', false);
            }

            
        },
        error:function(status){
            $('#errormessage').html('Error!');
        }
    });
}