$(document).ready(function(){
	const titlelabel = $('#title-label');
	const divmessage = $('#div-message');
	const tbodyenrollment = $('#tbody-enrollment');
	const cboacadlvl = $('#stud-acadlvl');
	const cboacadyr = $('#stud-acadyr');
	const cboacadprd = $('#stud-period');
	const cboacadyrlvl = $('#cbo-acadyrlvl');
	const tryrlvlcountrows = $('#yrlvlcounttblbody');
	const tdttlNEW = $('#tdttlNEW');
	const tdttlTRANS = $('#tdttlTRANS');
	const tdttlOLD = $('#tdttlOLD');
	const tdttlRETURN = $('#tdttlRETURN');
	const tdttlofferedcrse = $('#tdttlofferedcrse');
	const tdttlenrollstud = $('#tdttlenrollstud');
	const thchk = $('#th-chk');
	const tablesummarytd = $('#table-summary td');
	const tablecategorytrhastd = $('#table-category tr:has(td)');
	const btnsearch = $('#btnSearch');
	const emojiloading = '<p class="rotating-text">↻</p>';
	const screenWidth = window.innerWidth;
	const percentage = 5;
	const colwidth = 'width: 5%;';
	var initialLoadSuccess = false;
	var prevMsg = '';
	let myChart;
	const reportType = $('#report-type');




	function GetAcademicLevel(type)
	{
		//console.log("screen px: " + screenWidth);
		hideCountTable();
		destroyChart();
		
		try { 
			$.ajax({
				type:'GET',
				url: '../../model/forms/enrollment/enrollment-monitoring-controller.php',
				data:{
					type : type
				},
				beforeSend: function (status) {
					tbodyenrollment.html("<tr>" +
											"<td colspan='6' " + 
											"style='font-size: 16px;" + 
											"font-family: Roboto, sans-serif;" + 
											"font-weight: normal;" + 
											"text-decoration: none;" + 
											"color: red;'>" +
											"No Record Found" +
											"</td>" +
											"</tr>");
				},
				success: function(result){
					var ret = JSON.parse(result);
					//console.log(ret);
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
			hideLoading();
			showMessage("error-msg", "An error has occured! Please click the 'Search' button or Refresh the page.");

		}
		finally {
			// console.log('We do cleanup here');
		}
	}
	function GetAcademicYear(type,lvlid){
		document.getElementById("dropdown-academic-year").style.display = "none";
		document.getElementById("dropdown-academic-period").style.display = "none";
		document.getElementById("dropdown-report-type").style.display = "none";
		document.getElementById("btnSearch-cont").style.display = "none";
		hideCountTable();
		try { 
			$.ajax({
				type: 'GET',
				url: '../../model/forms/enrollment/enrollment-monitoring-controller.php',
				data:{
					type : type,
					levelid : lvlid
				},
				beforeSend: function (status) {
					tbodyenrollment.html("<tr>" +
											   "<td colspan='6' " + 
													  "style='font-size: 16px;" + 
													  "font-family: Roboto, sans-serif;" + 
													  "font-weight: normal;" + 
													  "text-decoration: none;" + 
													  "color: red;'>" +
													"No Record Found" +
											   "</td>" +
											   "</tr>");
				},
				success: function(result){
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
					document.getElementById("dropdown-academic-year").style.display = "block";
					GetAcademicPeriod('ACADPERIOD',cboacadlvl.val(),cboacadyr.val());
					
				}
			});
		}
		catch(e) {
			console.error(e);
			hideLoading();
			showMessage("error-msg", "An error has occured! Please click the 'Search' button or Refresh the page.");

		}
		finally {
			// console.log('We do cleanup here');
		}
	}
	function GetAcademicPeriod(type,lvlid,yrid){
		hideCountTable();
		document.getElementById("dropdown-academic-period").style.display = "none";
		document.getElementById("btnSearch-cont").style.display = "none";
		try { 
			$.ajax({
				type:'GET',
				url: '../../model/forms/enrollment/enrollment-monitoring-controller.php',
				data:{
					type : type,
					levelid : lvlid,
					yearid: yrid
				},
				beforeSend: function (status) {
					tbodyenrollment.html("<tr>" +
											"<td colspan='6' " + 
											"style='font-size: 16px;" + 
											"font-family: Roboto, sans-serif;" + 
											"font-weight: normal;" + 
											"text-decoration: none;" + 
											"color: red;'>" +
											"No Record Found" +
											"</td>" +
											"</tr>");
				},
				success: function(result){
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
					document.getElementById("dropdown-academic-period").style.display = "block";
					document.getElementById("dropdown-report-type").style.display = "block";
					document.getElementById("btnSearch-cont").style.display = "flex";
					
					initialLoadSuccess = true;
					if (!initialLoadSuccess){
						GetEnrollmentStudentCount('ENROLLMENTSTUDENTCOUNT', cboacadlvl.val(),cboacadyr.val(),cboacadprd.val());
						initialLoadSuccess = true;
						//hideLoading();
					} else {
						hideLoading();
						showMessage("info-msg", "Click the 'Search' button to see the result...");
					}
					//GetAcademicCourse('ACADCOURSE',cboacadlvl.val(),cboacadyr.val(),cboacadprd.val());
				}
			});
		}
		catch(e) {
			console.error(e);
			hideLoading();
			showMessage("error-msg", "An error has occured! Please click the 'Search' button or Refresh the page.");

		}
		finally {
			// console.log('We do cleanup here');
		}
	}

	function a_(){

	}

	function GetAcademicCourse(type,lvlid,yrid,prdid){
		hideCountTable();
		try { 
			$.ajax({
				type:'GET',
				url: '../../model/forms/enrollment/enrollment-monitoring-controller.php',
				data:{
					type : type,
					levelid : lvlid,
					yearid: yrid, 
					periodid: prdid
				},
				beforeSend: function (status) {
					tbodyenrollment.html("<tr>" +
											   "<td colspan='6' " + 
													  "style='font-size: 16px;" + 
													  "font-family: Roboto, sans-serif;" + 
													  "font-weight: normal;" + 
													  "text-decoration: none;" + 
													  "color: red;'>" +
													"No Record Found" +
											   "</td>" +
											   "</tr>");
				},
				success: function(result){
					var courses_offered = JSON.parse(result);
				}
			});
		}
		catch(e) {
			console.error(e);
			hideLoading();
			showMessage("error-msg", "An error has occured! Please click the 'Search' button or Refresh the page.");

		}
		finally {
			// console.log('We do cleanup here');
		}
	} function GetEnrollmentStudentCount(type,lvlid,yrid,prdid){
		showLoading();
		hideCountTable();
		try { 
			console.log([lvlid,yrid,prdid]);
			$.ajax({
				type:'GET',
				url: '../../model/forms/enrollment/enrollment-monitoring-controller.php',
				data:{
					type : type,
					levelid : lvlid,
					yearid: yrid,
					periodid: prdid
				},
				beforeSend: function (status) {
					tbodyenrollment.html("<tr>" +
											   "<td colspan='6' " + 
													  "style='font-size: 16px;" + 
													  "font-family: Roboto, sans-serif;" + 
													  "font-weight: normal;" + 
													  "text-decoration: none;" + 
													  "color: red;'>" +
													"No Record Found" +
											   "</td>" +
											   "</tr>");
				},
				success: function(result){
					try {
						var enrollment_count = JSON.parse(result);
						if (enrollment_count.length == 0){
							hideLoading();
							showMessage("info-msg", "No data available...");
						} else {
							console.log(enrollment_count);
							//showChart(enrollment_count);
							const categ_names = ["NEW",
												"OLD",
												"TRANSFEREE",
												"RETURNEE",
												"TOTAL"];
												
							const categ_count_ttl = [0,0,0,0,0];
							let crs = ""; tryrlvlcountrows.html('<th>loading...</th>');
							let unq_year_levels = [...new Set(enrollment_count.map(value => value.SchlAcadYrLvl_NAME ))];
							let unq_yr_lvl_ttl = Array.from({ length: unq_year_levels.length }, (_, i) => 0);
							let unq_crs_names = [...new Set(enrollment_count.map(value => value.SchlAcadCrses_CODE ))];

							let courseOccurrences = {};

							enrollment_count.forEach(value => {
								let courseCode = value.SchlAcadCrses_CODE;
								let val_ttl = parseInt(value.TOTAL);
								if (courseCode in courseOccurrences){
									courseOccurrences[courseCode] = courseOccurrences[courseCode] + val_ttl;
								}else{
									courseOccurrences[courseCode] = val_ttl;
								}
							});

							const sortedArray = Object.entries(courseOccurrences).sort(([, valueA], [, valueB]) => valueB - valueA);
							courseOccurrences = Object.fromEntries(sortedArray);

							showChart(courseOccurrences);
														
							// create dictionary of courses with initial count value of 0
							let unq_crs_names_count = {};
							for (let count = 0; count < unq_crs_names.length; count ++) {
								let crs_code = unq_crs_names[count];
								unq_crs_names_count[crs_code] = 0;
							}

							// Function to create a deep copy of the courses count object
							function deepCopyCoursesCount() {
								return JSON.parse(JSON.stringify(unq_crs_names_count));
							}

							// display year levels with sub dictionary of courses with initial count of 0
							let yr_lvl_counts = {};
							for (let count = 0; count < unq_year_levels.length; count ++) {
								let lvl_name = unq_year_levels[count];
								yr_lvl_counts[lvl_name] = {
															"NEW":deepCopyCoursesCount(),
															"OLD":deepCopyCoursesCount(),
															"TRANSFEREE":deepCopyCoursesCount(),
															"RETURNEE":deepCopyCoursesCount(),
															"TOTAL":deepCopyCoursesCount()};
								let ttblname = "lvltotal_" + lvl_name.replace(" ", "_") ;
								crs += "<tr><th id='"+ lvl_name +"' style='width:auto;text-align:right;font-size: 11px;color: green;' class='table-primary'>" + lvl_name + "</th> <td id='" + ttblname + "' style='width:10%;font-size: 12px;color: blue;'>" + emojiloading +"</td> <tr>";
								//window[ttblname] = $('#' + ttblname); // for creating a global variable for year levels html tag
							}
							tryrlvlcountrows.html(crs);


							// Function to update count based on year level, category, and count
							function addCount(yrlvl, categ, crsname, count) {
								yr_lvl_counts[yrlvl][categ][crsname] = yr_lvl_counts[yrlvl][categ][crsname] + count;
							}

							//process the total of old, freshmen, transferee, and returnee
							let crs_name = "";

							

							$.each(enrollment_count, function(key, value) {
								crs_name = value.SchlAcadCrses_CODE;
								let lvl_name = value.SchlAcadYrLvl_NAME;
								const categ_count = [parseInt(value[categ_names[0]]),			// NEW
															parseInt(value[categ_names[1]]),	// OLD
															parseInt(value[categ_names[2]]),	// TRANSFEREE
															parseInt(value[categ_names[3]]),	// RETURNEE
															parseInt(value[categ_names[4]])]	// TOTAL

								
								for (var pstn = 0; pstn < categ_count_ttl.length; pstn++){
									categ_count_ttl[pstn] = categ_count_ttl[pstn] + categ_count[pstn];
									addCount(lvl_name, categ_names[pstn], crs_name, categ_count[pstn]);
								}

								let pos = unq_year_levels.indexOf(value.SchlAcadYrLvl_NAME);
								unq_yr_lvl_ttl[pos] = unq_yr_lvl_ttl[pos] + categ_count[4]
							});

							updateDisplayCount(categ_count_ttl[0],
											categ_count_ttl[1],
											categ_count_ttl[2],
											categ_count_ttl[3],
											categ_count_ttl[4],
											unq_crs_names.length
							);
							document.getElementById("yr_lvl_tbl").style.display = "block";

							// update count diplay per year level
							for (var count = 0; count < unq_year_levels.length; count++){
								let ttblname = "lvltotal_" + unq_year_levels[count].replace(" ", "_");
								document.getElementById(ttblname).textContent = unq_yr_lvl_ttl[count];
							}

							
							// remove the column heads
							const brkdwn_heads = document.getElementById('brkdwn_heads');
							while (brkdwn_heads.firstChild) {
								brkdwn_heads.removeChild(brkdwn_heads.firstChild);
							}
							
							// remove the row data
							const brkdwn_bdy = document.getElementById('brkdwn_bdy');
							while (brkdwn_bdy.firstChild) {
								brkdwn_bdy.removeChild(brkdwn_bdy.firstChild);
							}
							

							// create table based on the dictionary structure of the reporting in excel
							let pos = 0;
							$.each(yr_lvl_counts, function(key, value) {
								const brkdwn_heads = document.getElementById('brkdwn_heads');
								const brkdwn_heads_upper = document.createElement('tr');
								brkdwn_heads_upper.id = "brkdwn_heads_upper";
								const col_name = document.createElement('th');
								const row_vals = document.createElement('td');
								
								if (pos == 0) {
									// add crs name columns as the 1st column
									const brkdwn_heads = document.getElementById('brkdwn_heads');
									const col_name = document.createElement('th');
									col_name.textContent = 'Course Program';
									col_name.id = 'Course Program';
									col_name.style = colwidth;
									col_name.rowSpan = 2;
									brkdwn_heads_upper.appendChild(col_name);
									brkdwn_heads.appendChild(brkdwn_heads_upper);
								}
								
								//add yr level columns as upper head
								col_name.textContent = key;
								col_name.colSpan = Object.keys(yr_lvl_counts[key]).length;
								if ((pos != (yr_lvl_counts.length - 1))){
									const brkdwn_heads_upper = document.getElementById("brkdwn_heads_upper");
									brkdwn_heads_upper.appendChild(col_name);
									brkdwn_heads.appendChild(brkdwn_heads_upper);
								}
								pos++;
							});


							//add sub heads
							$.each(yr_lvl_counts, function(key, value) {
								const yr_levels_cat = value;
								const yr_levels = key;
								$.each(yr_levels_cat, function(key, value) {
									const col_name = document.createElement('th');
									//check if the sub head id is already existing
									if (!(document.getElementById('brkdwn_heads_sub'))){
										const brkdwn_heads = document.getElementById('brkdwn_heads');
										const brkdwn_heads_sub = document.createElement('tr');
										brkdwn_heads_sub.id = "brkdwn_heads_sub";
										col_name.textContent = key;
										col_name.id = key;
										col_name.style = colwidth;
										brkdwn_heads_sub.appendChild(col_name);
										brkdwn_heads.appendChild(brkdwn_heads_sub);
									}else{
										//since the first sub head was successfully added below the col head
										//the cols will be added aligned with the first sub head
										const brkdwn_heads_sub = document.getElementById("brkdwn_heads_sub");
										col_name.textContent = key;
										col_name.id = key;
										col_name.style = colwidth;
										brkdwn_heads_sub.appendChild(col_name);
									}
								});
							});
							
							//add courses and count to the breakdown table
							$.each(unq_crs_names, function(key, value) {
								const brkdwn_bdy = document.getElementById('brkdwn_bdy');
								const row_name = document.createElement('tr');
								var crs_name = value; 
								row_name.id = "row_" + crs_name;

								//yr names
								for (var yr_pos = 0; yr_pos < unq_year_levels.length; yr_pos++){
										var yr_name = unq_year_levels[yr_pos];
										if (yr_pos == 0){
											const row_data = document.createElement('td');
											row_data.id = "crs_name_" + crs_name;
											row_data.style = colwidth;
											row_data.textContent = crs_name;
											row_name.appendChild(row_data);
										}

										//categ names
										for (var ctg_pos = 0; ctg_pos < categ_names.length; ctg_pos++){
											var ctg_name = categ_names[ctg_pos];
											const row_data = document.createElement('td');
											var yr_lvl_counts_value = yr_lvl_counts[yr_name][ctg_name][crs_name];
											row_data.id = "count_" + crs_name + "_" + yr_name + "_" + ctg_name;
											if (isNaN(yr_lvl_counts_value)){
												row_data.textContent = "";
											}else {
												row_data.textContent = yr_lvl_counts_value;
											}
											row_data.style = colwidth;
											row_name.appendChild(row_data);
										}
								}
								//add all the data of each subj row
								brkdwn_bdy.appendChild(row_name);
								document.getElementById("enrollment_count_tbl").style.display = "block";
								document.getElementById("table-summary-cont").style.display = "block";
								
							});
							hideLoading();
							showMessage("success-msg", "Grades Successfully loaded!");
						}
					} catch (e) {
						console.error(e);
						hideLoading();
						showMessage("error-msg", "An error has occured! Please click the 'Search' button or Refresh the page.");
					}
				}
			});
		}
		catch(e) {  
			console.error(e);
			hideLoading();
			showMessage("error-msg", "An error has occured! Please click the 'Search' button or Refresh the page.");

		}
		finally {

		}
	}
	function updateDisplayCount(_tdttlnew = emojiloading
								,_tdttlold = emojiloading
								,_tdttltrans = emojiloading
								,_tdttlreturn = emojiloading
								,_tdttlenrollstud = emojiloading
								,_tdttlofferedcrse = emojiloading){

		//for displaying the enrollee count
		tdttlNEW.html(_tdttlnew);
		tdttlOLD.html(_tdttlold);
		tdttlTRANS.html( _tdttltrans);
		tdttlRETURN.html(_tdttlreturn);
		tdttlenrollstud.html(_tdttlenrollstud);
		tdttlofferedcrse.html(_tdttlofferedcrse);

	} function hideCountTable(){
		//for removing the data from the count tables and 
		//hiding the count tables when the calculation is
		//not yet finished
		updateDisplayCount();
		document.getElementById("yr_lvl_tbl").style.display = "none";
		document.getElementById("enrollment_count_tbl").style.display = "none";
		document.getElementById("table-summary-cont").style.display = "none";
	}
	function GetAcademicYearLevel(type,lvlid,yrid,prdid,crseid){
		try { 
			$.ajax({
				type:'GET',
				url: '../../model/forms/enrollment/enrollment-monitoring-controller.php',
				data:{
					type : type,
					levelid : lvlid,
					yearid: yrid,
					periodid: prdid,
					courseid: crseid
				},
				beforeSend: function (status) {
					tbodyenrollment.html("<tr>" +
											   "<td colspan='6' " + 
													  "style='font-size: 16px;" + 
													  "font-family: Roboto, sans-serif;" + 
													  "font-weight: normal;" + 
													  "text-decoration: none;" + 
													  "color: red;'>" +
													"No Record Found" +
											   "</td>" +
											   "</tr>");
				},
				success: function(result){
					var ret = JSON.parse(result);
					var cboYearLevel = '';
					if(ret.length) {
						cboYearLevel += "<option value='0'>All</option>";
						$.each(ret, function(key, value) {
							cboYearLevel += "<option value='" + value.ID + "'>" + value.NAME + "</option>";
						});
					} else {
						cboYearLevel += "<option value='0'>None</option>";
					}
					
					cboacadyrlvl.html(cboYearLevel);
				}
			});
		}
		catch(e) {
			console.error(e);
			hideLoading();
			showMessage("error-msg", "An error has occured! Please click the 'Search' button or Refresh the page.");

		}
		finally {
			// console.log('We do cleanup here');
		}
	}

	function fadeOutDiv(div_name) {
        let opacity = 1;
        const timer = setInterval(function () {
            if (opacity <= 0.1) {
                clearInterval(timer);
                div_name.style.display = 'none';
            }
            div_name.style.opacity = opacity;
            opacity -= opacity * 0.1;
        }, 250);
    }

	function showMessage(msg_type, msg_content){
		const msg_container = document.getElementById(msg_type);
		msg_container.appendChild(document.createTextNode("  " + msg_content));
		msg_container.style.display = "inline-block";
		prevMsg = msg_type;
		if (msg_type == "success-msg") {
			fadeOutDiv(msg_container);
		}
	}

	function hideMessage(){
		if (prevMsg != '') {
			const msg_container = document.getElementById(prevMsg);
			let icon_logo = '';
			msg_container.textContent =  "";
			const icon_img = document.createElement('i');
			if (prevMsg == "info-msg") {
				icon_logo = "fa fa-info-circle";
			} else if (prevMsg == "success-msg"){
				icon_logo = "fa fa-check";
			} else if (prevMsg == "warning-msg"){
				icon_logo = "fa fa-warning";
			} else if (prevMsg == "error-msg"){
				icon_logo = "fa fa-times-circle";
			}
			icon_img.className = icon_logo;
			msg_container.appendChild(icon_img);
			msg_container.style.display = "none";
			//console.log(msg_container);
		}
	}

	function showChart(unq_crs_ttl_){
		const ctx = document.getElementById('myChart').getContext('2d');

		destroyChart()

		const data = {
			labels: Object.keys(unq_crs_ttl_),
			datasets: [{
				label: 'Total Enrollees Per Course / Strand',
				data: Object.values(unq_crs_ttl_),
				// backgroundColor: [
				// 	'rgba(255, 99, 132, 0.2)',
				// 	'rgba(54, 162, 235, 0.2)',
				// 	'rgba(255, 206, 86, 0.2)',
				// 	'rgba(75, 192, 192, 0.2)'
				// ],
				// borderColor: [
				// 	'rgba(255, 99, 132, 1)',
				// 	'rgba(54, 162, 235, 1)',
				// 	'rgba(255, 206, 86, 1)',
				// 	'rgba(75, 192, 192, 1)'
				// ],
				borderWidth: 1
			}]
		};

		 myChart = new Chart(ctx, {
			type: 'bar',
			data: data,
			options: {
				indexAxis: 'y',
				scales: {
					x: {
						beginAtZero: true
					}
				}
			}
			 // ,plugins: {
             //    datalabels: {
             //        anchor: 'end',   
             //        align: 'end',     
             //        formatter: (value) => value,
             //        color: '#000',   
             //        font: {
             //            weight: 'bold',
             //            size: 12  
             //        }
             //    }
			// },plugins: [ChartDataLabels]
		});
	}

	function destroyChart() {
		if (myChart) {
			myChart.destroy();
			myChart = null;
		}
	}

	function showLoading(){
		destroyChart()
		hideMessage()
		document.getElementById("loading-display").style.display = "flex";
	}

	function hideLoading(){
		document.getElementById("loading-display").style.display = "none";
	}

	function createEvents(){
		cboacadlvl.on('change',function(){
			showLoading()
			GetAcademicYear('ACADYEAR',$(this).val());
		});
		cboacadyr.on('change',function(){
			showLoading()
			GetAcademicPeriod('ACADPERIOD',cboacadlvl.val(),$(this).val());
		});
		cboacadprd.on('change',function(){
			hideCountTable()
		});
		btnsearch.on('click',function(){
			let selected_report = reportType.val();
	
			if (selected_report == "all_report"){
				showLoading();
				GetEnrollmentStudentCount('ENROLLMENTSTUDENTCOUNT', cboacadlvl.val(),cboacadyr.val(),cboacadprd.val());
			} else if (selected_report == "course_report"){
				console.log("Course report");
			} else if (selected_report == "yr_lvl_report"){
				console.log("Yr Lvl report");
			} else if (selected_report == "clsf_report"){
				console.log("Classification report");
			}
		});
		tablesummarytd.on('click',function() {
			var id = $(this).attr("id");
		});
		thchk.on('click',function() {
			var isChecked = $(this).prop("checked");
			tablecategorytrhastd.find("input[type='checkbox']").prop('checked', isChecked);
		});
	}


	function Initialize() {
		GetAcademicLevel('ACADLEVEL');
		createEvents();
	}

	Initialize();
	
});