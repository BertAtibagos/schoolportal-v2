<?php
    // ✅ Secure cookie flags (must be set before session_start)
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 1);
    ini_set('session.cookie_samesite', 'Strict');

    // PHP error handling
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);

    session_start();
    include('../../../../../configuration/connection-config.php');

	function sanitizeText(?string $value): string {
        $value = trim((string)$value);
        $value = strip_tags($value);
        return preg_replace('/\s+/', ' ', $value) ?? '';
    }

	$fetch = [];

	function rateLimit(int $window, int $max, string $key){
        $rateLimitWindowSec = $window;
        $rateLimitMax = $max;
        $rateLimitKey = $key;
        $now = time();

        if (!isset($_SESSION[$rateLimitKey])) {
            $_SESSION[$rateLimitKey] = ['count' => 1, 'start' => $now];
        } else {
            $elapsed = $now - $_SESSION[$rateLimitKey]['start'];
            if ($elapsed > $rateLimitWindowSec) {
                $_SESSION[$rateLimitKey] = ['count' => 1, 'start' => $now];
            } else {
                $_SESSION[$rateLimitKey]['count']++;
                if ($_SESSION[$rateLimitKey]['count'] > $rateLimitMax) {
                    http_response_code(429);
                    $fetch['message'] = 'Too many submissions. Please try again later.';
                    echo json_encode($fetch);
                    exit;
                }
            }
        }
    }
	
	$yearId = 19;
	$programs = [
		'COCS' => [749],
		'COLA' => [],
		'COAM' => [957],
		'COCJ' => [],
		'COE' => [],
		'COA' => [11986],
		'COBM' => [],
		'COED' => []
	];


	function asstHeadSwitch($user, $programs) {
		$forHead = " AND `SchlDeptHead_ID` = ?";
		$isAssistant = false;
		$crsCode = "";

		foreach ($programs as $code => $ids) {
			if (in_array($user, $ids)) {
				$isAssistant = true;
				$crsCode = $code;
				$forHead = " AND `SchlDept_CODE` = ?";
				break;
			}
		}
		return [$forHead, $isAssistant, $crsCode];
	}

	$type = $_POST['type'];
	$queryType = ['GET_ACADEMIC_LEVEL', 'GET_ACADEMIC_YEAR_LEVEL', 'GET_ACADEMIC_PERIOD', 'GET_ACAD_YEAR', 'GET_INSTRUCTOR_LIST', 'GET_SECTION_LIST', 'GETALL_TADI_RECORDS', 'GET_SUBJECT_BY_INSTRUCTOR', 'SEARCH_SUBJECT_BY_INSTRUCTOR', 'GET_IMAGE', 'GET_TEACHER_TADI_REPORT', 'APPROVE_TADI_REQUEST', 'REJECT_TADI_REQUEST','GET_SUBMITTED_REC'];
	if((isset($_SESSION['STUDENT']) || isset($_SESSION['EMPLOYEE'])) && in_array($type, $queryType, true)){
		switch($type){
			case 'GET_ACADEMIC_LEVEL':

				$qry = "SELECT DISTINCT
							acad_lvl.`SchlAcadLvl_ID`,
							acad_lvl.`SchlAcadLvl_NAME`,
							acad_lvl.`SchlAcadLvl_DESC` 
						FROM
							`schoolacademiclevel` acad_lvl 
						LEFT JOIN `schoolenrollmentsubjectoffered` subj_off 
							ON acad_lvl.`SchlAcadLvlSms_ID` = subj_off.`SchlAcadLvl_ID` 
						LEFT JOIN `schooldepartment` `schl_dept` 
							ON acad_lvl.`SchlAcadLvlSms_ID` = `schl_dept`.`SchlAcadLvl_ID`
						WHERE `SchlAcadLvl_ISACTIVE` = 1
						AND acad_lvl.`SchlAcadLvl_ID` = 2";

				$stmt = $dbConn->prepare($qry);                                
				$stmt->execute();
				$result = $stmt->get_result(); 
				$fetch = $result->fetch_all(MYSQLI_ASSOC);
				$stmt->close();
				$dbConn->close();
				break;

			case 'GET_ACADEMIC_YEAR_LEVEL':
				if (!isset($_POST['lvl_id'])) {
					http_response_code(400);
					echo json_encode(['success' => false, 'error' => 'Missing required parameter: lvl_id']);
					exit;
				}

				$lvlid = $_POST['lvl_id'];

				$qry = "SELECT 
							`SchlAcadYrLvlSms_ID` `ACAD_YRLVL_ID`,
							`SchlAcadYrLvl_NAME` `ACAD_YRLVL_NAME`
						FROM  
							`schoolacademicyearlevel`
						WHERE `SchlAcadYrLvl_STATUS` = 1 
						AND `SchlAcadYrLvl_ISACTIVE` = 1 
						AND `SchlAcadLvl_ID` = ?
						ORDER BY `SchlAcadYrLvl_RANKNO`";
				
				$stmt = $dbConn->prepare($qry);
				$stmt->bind_param("i",$lvlid);
				$stmt->execute();
				$result = $stmt->get_result();
				$fetch = $result->fetch_all(MYSQLI_ASSOC);
				$stmt->close();
				$dbConn->close();
				break;

			case 'GET_ACADEMIC_PERIOD':
				if (!isset($_POST['lvl_id'])) {
					http_response_code(400);
					echo json_encode(['success' => false, 'error' => 'Missing required parameter: lvl_id']);
					exit;
				}
				$lvlid = $_POST['lvl_id'];

				$qry = " SELECT DISTINCT
								`schl_acad_prd`.`SchlAcadPrdSms_ID` AS `acad_prd_id`,
								`schl_acad_prd`.`SchlAcadPrd_NAME` AS `acad_prd_name`,
								`schl_acad_yr_prd`.`SchlAcadYrPrd_ISOPEN` AS `is_current`
							FROM `schoolacademicyearperiod` AS `schl_acad_yr_prd`
							LEFT JOIN `schoolacademicperiod` AS `schl_acad_prd`
								ON `schl_acad_yr_prd`.`SchlAcadPrd_ID` =  `schl_acad_prd`.`SchlAcadPrdSms_ID`
							WHERE `schl_acad_yr_prd`.`SchlAcadLvl_ID` = ?
							AND `schl_acad_yr_prd`.SchlAcadYr_ID = ?
							AND `schl_acad_yr_prd`.`SchlAcadYrPrd_ISACTIVE` = 1";

				$stmt = $dbConn->prepare($qry);
				$stmt->bind_param("ii",$lvlid,$yearId);
				$stmt->execute();
				$result = $stmt->get_result();
				$fetch = $result->fetch_all(MYSQLI_ASSOC);
				$stmt->close();
				$dbConn->close();
				break;

			case 'GET_ACAD_YEAR':
				$qry = " SELECT DISTINCT 
							`schl_acad_yr_prd`.`SchlAcadLvl_ID` `YEAR_ID`,
							`schl_yr`.`SchlAcadYr_DESC` `YEAR_NAME`, `SchlAcadYrSms_ID`
						FROM `schoolacademicyearperiod` `schl_acad_yr_prd`					
						LEFT JOIN `schoolacademicyear` `schl_yr`  
							ON `schl_acad_yr_prd`.`SchlAcadYr_ID` = `schl_yr`.`SchlAcadYrSms_ID`
						WHERE 
							`schl_acad_yr_prd`.`SchlAcadYrPrd_ISACTIVE` = 1 
						AND
							`schl_acad_yr_prd`.`SchlAcadLvl_ID` = 2 
						ORDER BY YEAR_NAME DESC";

				$rreg = $dbConn->query($qry);
				$fetch = $rreg->fetch_ALL(MYSQLI_ASSOC);
				$dbConn->close();
				break;

			case 'GET_INSTRUCTOR_LIST':
				rateLimit(60, 10, 'get_instructor_list_rate_limit');

				if (!isset($_POST['lvl_id'], $_POST['prd_id'], $_POST['yr_id'], $_POST['yrlvl_id'])) {
					http_response_code(400);
					echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
					exit;
				}

				$user = $_SESSION['EMPLOYEE']['ID'] ?? $_SESSION['STUDENT']['ID'] ?? 0;
				$lvlid = $_POST['lvl_id'];
				$prdid = $_POST['prd_id'];
				$yrid = $_POST['yr_id'];
				$yrlvlid = $_POST['yrlvl_id'];
				
				[$forHead, $isAssistant, $crsCode] = asstHeadSwitch($user, $programs);

				$qry = "SELECT DISTINCT 
							`schl_enr_subj_off`.`SchlProf_ID`,
							emp.`SchlEmpSms_ID` AS single_prof_id,
							CONCAT(
								emp.SchlEmp_LNAME,
								', ',
								emp.SchlEmp_FNAME,
								' ',
								emp.SchlEmp_MNAME
							) AS prof_name,
							(SELECT 
								COUNT(*) 
							FROM
								schooltadi st 
							INNER JOIN schoolenrollmentsubjectoffered seso 
								ON st.schlenrollsubjoff_id = seso.SchlEnrollSubjOffSms_ID
							LEFT JOIN `schoolacademiccourses` `sac` 
								ON `seso`.`SchlAcadCrses_ID` = `sac`.`SchlAcadCrseSms_ID`
							LEFT JOIN `schooldepartment` `sd` 
								ON `sac`.`SchlDept_ID` = `sd`.`SchlDeptSms_ID` 
							WHERE st.SchlProf_ID = emp.`SchlEmpSms_ID`
								AND seso.SchlAcadLvl_ID = ?
								AND seso.SchlAcadYr_ID = ?
								AND seso.SchlAcadPrd_ID = ?
								AND `seso`.`SchlAcadYrLvl_ID` = ?
								AND st.schltadi_status = 1
								AND st.`schltadi_isconfirm` = 0
								AND st.schltadi_isactive = 1
								AND st.`schltadi_date` >= CURDATE() - INTERVAL 3 DAY
								$forHead) AS unverified_count 
						FROM
							`schoolenrollmentsubjectoffered` `schl_enr_subj_off` 
						LEFT JOIN `schoolacademiccourses` `schl_acad_crses` 
							ON `schl_enr_subj_off`.`SchlAcadCrses_ID` = `schl_acad_crses`.`SchlAcadCrseSms_ID` 
						LEFT JOIN `schooldepartment` `schl_dept` 
							ON `schl_acad_crses`.`SchlDept_ID` = `schl_dept`.`SchlDeptSms_ID` 
						LEFT JOIN schoolemployee AS emp 
							ON FIND_IN_SET(emp.`SchlEmpSms_ID`, `schl_enr_subj_off`.`SchlProf_ID`) > 0
						WHERE `schl_enr_subj_off`.`SchlAcadLvl_ID` = ?
							AND `schl_enr_subj_off`.`SchlAcadYr_ID` = ?
							AND `schl_enr_subj_off`.`SchlAcadPrd_ID` = ?
							AND `schl_enr_subj_off`.`SchlAcadYrLvl_ID` = ?
							$forHead
							AND `schl_enr_subj_off`.`SchlEnrollSubjOff_ISACTIVE` = 1 
							AND emp.`SchlEmp_ID` IS NOT NULL 
						GROUP BY 
							emp.`SchlEmpSms_ID`,
							emp.SchlEmp_LNAME,
							emp.SchlEmp_FNAME,
							emp.SchlEmp_MNAME 
						ORDER BY prof_name ASC";

				$stmt = $dbConn->prepare($qry);

				if($isAssistant){
					$stmt->bind_param("iiiisiiiis", $lvlid, $yrid, $prdid, $yrlvlid, $crsCode, $lvlid, $yrid, $prdid, $yrlvlid, $crsCode);
				}else{
					$stmt->bind_param("iiiiiiiiii", $lvlid, $yrid, $prdid, $yrlvlid, $user, $lvlid, $yrid, $prdid, $yrlvlid, $user);
				}

				$stmt->execute();
				$result = $stmt->get_result();
				$fetch = $result->fetch_all(MYSQLI_ASSOC);
				$stmt->close();
				$dbConn->close();
				break;

			case 'GET_SECTION_LIST':

				if (!isset($_POST['prof_id'], $_POST['lvlid'], $_POST['prdid'], $_POST['yrid'], $_POST['yrlvlid'])) {
					http_response_code(400);
					echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
					exit;
				}

				$profId = $_POST['prof_id'];
				$lvlid = $_POST['lvlid'];
				$prdid = $_POST['prdid'];
				$yrid = $_POST['yrid'];
				$yrlvlid = $_POST['yrlvlid'];
				$user = $_SESSION['EMPLOYEE']['ID'] ?? $_SESSION['STUDENT']['ID'] ?? 0;

				$forHead = "AND schl_dept.`SchlDeptHead_ID` = ?";
				[$forHead, $isAssistant, $crsCode] = asstHeadSwitch($user, $programs);

				$qry = "SELECT DISTINCT
							`schl_enr_subj_off`.`SchlProf_ID` AS `prof_id`,
							`schl_enr_subj_off`.`SchlEnrollSubjOff_ISACTIVE` AS `subj_act`,
							`schl_acad_sec`.`SchlAcadSec_NAME` AS `section_name`, 
								`schl_acad_subj`.`SchlAcadSubj_desc` AS `subj_desc`,
							`schl_enr_subj_off`.`SchlEnrollSubjOffSms_ID` AS `subj_id`
						FROM `schoolenrollmentsubjectoffered` AS `schl_enr_subj_off`

						LEFT JOIN `schoolacademicsubject` AS `schl_acad_subj` 
							ON `schl_enr_subj_off`.`SchlAcadSubj_ID` = `schl_acad_subj`.`SchlAcadSubjSms_ID`
						LEFT JOIN `schoolacademicsection` AS `schl_acad_sec` 
							ON `schl_enr_subj_off`.`SchlAcadSec_ID` = `schl_acad_sec`.`SchlAcadSecSms_ID`
						LEFT JOIN `schoolacademiccourses` AS `schl_acad_crses` 
							ON `schl_enr_subj_off`.`SchlAcadCrses_ID` = `schl_acad_crses`.`SchlAcadCrseSms_ID`
						LEFT JOIN `schooldepartment` AS `schl_dept` 
							ON `schl_acad_crses`.`SchlDept_ID` = `schl_dept`.`SchlDeptSms_ID`
						LEFT JOIN `schoolacademicyearperiod` AS `schl_acad_yr_prd` 
							ON `schl_enr_subj_off`.`SchlAcadYr_ID` = `schl_acad_yr_prd`.`SchlAcadYr_ID`
						LEFT JOIN `schoolacademicyear` AS `schl_yr` 
							ON `schl_acad_yr_prd`.`SchlAcadYr_ID` = `schl_yr`.`SchlAcadYrSms_ID`

						WHERE `schl_enr_subj_off`.`SchlEnrollSubjOff_ISACTIVE` = 1 
						AND `schl_enr_subj_off`.`SchlProf_ID` = ?
						AND `schl_enr_subj_off`.`SchlAcadLvl_ID` = ?
						AND `schl_enr_subj_off`.`SchlAcadPrd_ID` = ?
						AND `schl_enr_subj_off`.`SchlAcadYr_ID` = ?
						AND `schl_enr_subj_off`.`SchlAcadYrLvl_ID` = ?
						$forHead";

				$stmt = $dbConn->prepare($qry);

				if($isAssistant){
					$stmt->bind_param("iiiiis",$profId ,$lvlid, $prdid, $yrid, $yrlvlid,$crsCode);
				}else{
					$stmt->bind_param("iiiiii",$profId ,$lvlid, $prdid, $yrid, $yrlvlid, $user);
				}
				
				$stmt->execute();
				$result = $stmt->get_result();
				$fetch = $result->fetch_all(MYSQLI_ASSOC);
				$stmt->close();
				$dbConn->close();
				break;
			case 'GETALL_TADI_RECORDS':

				if (!isset($_POST['prof_id'], $_POST['subj_off_id'])) {
					http_response_code(400);
					echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
					exit;
				}

				$profId = $_POST['prof_id'];
				$subj_off_id = $_POST['subj_off_id'];

				$qry = "SELECT 
							CONCAT(`SchlEnrollRegStudInfo_LAST_NAME`, ', ', `SchlEnrollRegStudInfo_FIRST_NAME`,' ',`SchlEnrollRegStudInfo_MIDDLE_NAME`) AS stud_name,
							schl_tadi.`schltadi_id` AS schltadi_ID,
							schl_tadi.`schltadi_date` AS tadi_date,
							CONCAT(schl_tadi.`schltadi_mode`, ' ', schl_tadi.`schltadi_type`) AS tadi_modeType,
							schl_tadi.`schltadi_timein` AS tadi_timeIn,
							schl_tadi.`schltadi_timeout` AS tadi_timeOut,
							schl_tadi.`schltadi_activity` AS tadi_act,
							schl_tadi.`schltadi_status` AS tadi_status,
							schl_tadi.`startschltadi_filepath` AS tadi_filepath,
							schl_tadi.schlenrollsubjoff_id AS sub_off_id,
							schl_tadi.schltadi_late_status AS late_status,
							schl_tadi.SchlProf_ID,
							schl_tadi.schltadi_mkup_date AS mkup_date,
							schl_tadi.schltadi_isconfirm AS approved,
							schl_tadi.tadi_verified_date AS date_approved
						FROM `schooltadi` AS schl_tadi 
						
						LEFT JOIN `schoolstudent` AS schl_stud 
							ON schl_tadi.`schlstud_id` = schl_stud.`SchlStudSms_ID` 

						LEFT JOIN `schoolenrollmentregistration` AS schl_enr_reg 
							ON schl_stud.`SchlEnrollRegColl_ID` = schl_enr_reg.`SchlEnrollRegSms_ID` 

						LEFT JOIN `schoolenrollmentregistrationstudentinformation` AS schl_reg_stud 
							ON schl_enr_reg.`SchlEnrollRegSms_ID` = `schl_reg_stud`.`SchlEnrollReg_ID` 

						WHERE `schlprof_id` =  ?
							AND `schlenrollsubjoff_id` =  ?
							AND schltadi_status = 1
							AND schl_tadi.schltadi_isactive = 1
						ORDER BY schl_tadi.`schltadi_date` DESC, schl_tadi.`schltadi_timein` DESC";

				$stmt = $dbConn->prepare($qry);
				$stmt->bind_param("ii",$profId ,$subj_off_id);
				$stmt->execute();
				$result = $stmt->get_result();
				$fetch = $result->fetch_all(MYSQLI_ASSOC);
				$stmt->close();
				$dbConn->close();
				break;

			
			case 'GET_SUBJECT_BY_INSTRUCTOR':

				if (!isset($_POST['prof_id'], $_POST['lvl_id'], $_POST['prd_id'], $_POST['yr_id'], $_POST['yrlvl_id'])) {
					http_response_code(400);
					echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
					exit;
				}

				$profId = $_POST['prof_id'];
				$lvlid = $_POST['lvl_id'];
				$prdid = $_POST['prd_id'];
				$yrid = $_POST['yr_id'];
				$yrlvlid = $_POST['yrlvl_id'];
				$user = $_SESSION['EMPLOYEE']['ID'] ?? $_SESSION['STUDENT']['ID'] ?? 0;

				[$forHead, $isAssistant, $crsCode] = asstHeadSwitch($user, $programs);

				$qry = "SELECT DISTINCT
							CONCAT(emp.SchlEmp_LNAME, ', ', emp.SchlEmp_FNAME, ' ', emp.SchlEmp_MNAME) AS prof_name,
							schl_enr_subj_off.`SchlEnrollSubjOffSms_ID` AS sub_off_id,
							`schl_acad_sec`.`SchlAcadSec_NAME` AS schl_sec,
							`schl_acad_subj`.`SchlAcadSubj_CODE` AS `subj_code`,
							`schl_acad_subj`.`SchlAcadSubj_desc` AS `subj_desc`,
							`schl_acad_subj`.`SchlAcadSubj_NAME` AS `subj_name`,
							? AS SchlProf_ID,
							`schl_enr_subj_off`.`SchlAcadLvl_ID` AS lvlid,
							`schl_enr_subj_off`.`SchlAcadYr_ID` AS yrid,
							`schl_enr_subj_off`.`SchlAcadPrd_ID` AS prdid,
							`schl_enr_subj_off`.`SchlAcadYrLvl_ID` AS yrlvlid,
							`schl_enr_subj_off`.`SchlEnrollSubjOff_ISACTIVE` AS `subj_act`,
							(
							SELECT 
								COUNT(*) 
							FROM
								`schooltadi` AS t 
							WHERE t.`schltadi_status` = 1
								AND t.`schlprof_id` = ?
								AND t.`schltadi_isconfirm` = 0
								AND t.schltadi_isactive = 1
								AND t.`schlenrollsubjoff_id` = `schl_enr_subj_off`.`SchlEnrollSubjOffSms_ID`
								AND t.`schltadi_date` >= CURDATE() - INTERVAL 3 DAY) AS unverified_count
						FROM `schoolenrollmentsubjectoffered` AS `schl_enr_subj_off`

						LEFT JOIN `schoolacademicsubject` AS `schl_acad_subj`
							ON `schl_enr_subj_off`.`SchlAcadSubj_ID` = `schl_acad_subj`.`SchlAcadSubjSms_ID`
						LEFT JOIN `schoolacademicsection` AS `schl_acad_sec` 
							ON `schl_enr_subj_off`.`SchlAcadSec_ID` = `schl_acad_sec`.`SchlAcadSecSms_ID`
						LEFT JOIN `schoolacademiccourses` AS `schl_acad_crses`
							ON `schl_enr_subj_off`.`SchlAcadCrses_ID` = `schl_acad_crses`.`SchlAcadCrseSms_ID`
						LEFT JOIN `schooldepartment` AS `schl_dept` 
							ON `schl_acad_crses`.`SchlDept_ID` = `schl_dept`.`SchlDeptSms_ID`
						LEFT JOIN `schoolacademicyearperiod` AS `schl_acad_yr_prd` 
							ON `schl_enr_subj_off`.`SchlAcadYr_ID` = `schl_acad_yr_prd`.`SchlAcadYr_ID`
						LEFT JOIN `schoolacademicyear` AS `schl_yr` 
							ON `schl_acad_yr_prd`.`SchlAcadYr_ID` = `schl_yr`.`SchlAcadYrSms_ID`
						LEFT JOIN schoolemployee AS emp 
							ON emp.`SchlEmpSms_ID` = ?
						
						WHERE `schl_enr_subj_off`.`SchlEnrollSubjOff_ISACTIVE` = 1
						AND FIND_IN_SET(?, `schl_enr_subj_off`.`SchlProf_ID`)
						AND `schl_enr_subj_off`.`SchlAcadLvl_ID` = ?
						AND `schl_enr_subj_off`.`SchlAcadYr_ID` = ?
						AND `schl_enr_subj_off`.`SchlAcadPrd_ID` = ?
						AND `schl_enr_subj_off`.`SchlAcadYrLvl_ID` = ?
						$forHead";

				$stmt = $dbConn->prepare($qry);
				if($isAssistant){
					$stmt->bind_param("iiiiiiiis", $profId, $profId, $profId, $profId, $lvlid, $yrid, $prdid, $yrlvlid,$crsCode);
				}else{
					$stmt->bind_param("iiiiiiiii", $profId, $profId, $profId, $profId, $lvlid, $yrid, $prdid, $yrlvlid, $user);
				}
				
				$stmt->execute();
				$result = $stmt->get_result();
				$fetch = $result->fetch_all(MYSQLI_ASSOC);
				$stmt->close();
				$dbConn->close();
				break;
			case 'SEARCH_SUBJECT_BY_INSTRUCTOR':
				rateLimit(60, 10, 'search_subject_by_instructor_rate_limit');

				if (!isset($_POST['lvlid'], $_POST['prdid'], $_POST['yrid'], $_POST['yrlvlid'], $_POST['prof_id'], $_POST['subjDesc'], $_POST['subjCode'], $_POST['section'])) {
					http_response_code(400);
					echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
					exit;
				}

				$lvlid = $_POST['lvlid'];
				$prdid = $_POST['prdid'];
				$yrid = $_POST['yrid'];
				$yrlvlid = $_POST['yrlvlid'];
				$prof_id = $_POST['prof_id'];
				$subjDesc = $_POST['subjDesc'];
				$subjCode = $_POST['subjCode'];
				$section = $_POST['section'];
				$user = $_SESSION['EMPLOYEE']['ID'] ?? $_SESSION['STUDENT']['ID'] ?? 0;

				[$forHead, $isAssistant, $crsCode] = asstHeadSwitch($user, $programs);

				$qry = "SELECT DISTINCT 
							CONCAT(emp.SchlEmp_LNAME,',',emp.SchlEmp_FNAME,' ',emp.SchlEmp_MNAME) AS prof_name,
							schl_enr_subj_off.`SchlEnrollSubjOffSms_ID` AS sub_off_id,
							`schl_acad_sec`.`SchlAcadSec_NAME` AS schl_sec,
							`schl_acad_subj`.`SchlAcadSubj_CODE` AS `subj_code`,
							`schl_acad_subj`.`SchlAcadSubj_desc` AS `subj_desc`,
							`schl_acad_subj`.`SchlAcadSubj_NAME` AS `subj_name`,
							`schl_enr_subj_off`.`SchlProf_ID`,
							`schl_enr_subj_off`.`SchlAcadLvl_ID` AS lvlid,
							`schl_enr_subj_off`.`SchlAcadYr_ID` AS yrid,
							`schl_enr_subj_off`.`SchlAcadPrd_ID` AS prdid,
							`schl_enr_subj_off`.`SchlAcadYrLvl_ID` AS yrlvlid,
							`schl_enr_subj_off`.`SchlEnrollSubjOff_ISACTIVE` AS `subj_act`,
							(SELECT 
								COUNT(*) 
							FROM
								`schooltadi` AS t 
							WHERE t.`schltadi_status` = 1
								AND t.schltadi_isactive = 1
								AND t.`schlprof_id` = `schl_enr_subj_off`.`SchlProf_ID` 
								AND t.`schlenrollsubjoff_id` = `schl_enr_subj_off`.`SchlEnrollSubjOffSms_ID`) AS verified_count

						FROM `schoolenrollmentsubjectoffered` AS `schl_enr_subj_off`

						LEFT JOIN `schoolacademicsubject` AS `schl_acad_subj` 
							ON `schl_enr_subj_off`.`SchlAcadSubj_ID` = `schl_acad_subj`.`SchlAcadSubjSms_ID` 
						LEFT JOIN `schoolacademicsection` AS `schl_acad_sec` 
							ON `schl_enr_subj_off`.`SchlAcadSec_ID` = `schl_acad_sec`.`SchlAcadSecSms_ID` 
						LEFT JOIN `schoolacademiccourses` AS `schl_acad_crses` 
							ON `schl_enr_subj_off`.`SchlAcadCrses_ID` = `schl_acad_crses`.`SchlAcadCrseSms_ID` 
						LEFT JOIN `schooldepartment` AS `schl_dept` 
							ON `schl_acad_crses`.`SchlDept_ID` = `schl_dept`.`SchlDeptSms_ID` 
						LEFT JOIN `schoolacademicyearperiod` AS `schl_acad_yr_prd` 
							ON `schl_enr_subj_off`.`SchlAcadYr_ID` = `schl_acad_yr_prd`.`SchlAcadYr_ID` 
						LEFT JOIN `schoolacademicyear` AS `schl_yr` 
							ON `schl_acad_yr_prd`.`SchlAcadYr_ID` = `schl_yr`.`SchlAcadYrSms_ID` 
						LEFT JOIN schoolemployee AS emp 
							ON `schl_enr_subj_off`.`SchlProf_ID` = emp.`SchlEmpSms_ID` 
							
						WHERE `schl_enr_subj_off`.`SchlProf_ID` = ? 
							AND 
								`schl_enr_subj_off`.`SchlAcadLvl_ID` = ?
							AND
								`schl_enr_subj_off`.`SchlAcadYr_ID` = ? 
							AND
								`schl_enr_subj_off`.`SchlAcadPrd_ID` = ?
							$forHead
							AND
								`schl_enr_subj_off`.`SchlAcadYrLvl_ID` = ?
							AND `schl_acad_subj`.`SchlAcadSubj_CODE` LIKE ?
							AND `schl_acad_subj`.`SchlAcadSubj_desc` LIKE ?
							AND `schl_acad_sec`.`SchlAcadSec_NAME` LIKE ?";

				$stmt = $dbConn->prepare($qry);
				

				if ($stmt) {
					
					$srchSubCode = "%" . $subjCode . "%";
					$srchSubDesc = "%" . $subjDesc . "%";
					$srchSection = "%" . $section . "%";

					if($isAssistant){
						$stmt->bind_param("iiiisisss",$prof_id, $lvlid, $yrid, $prdid, $crsCode, $yrlvlid, $srchSubCode, $srchSubDesc, $srchSection);
					}else{
						$stmt->bind_param("iiiiiisss",$prof_id, $lvlid, $yrid, $prdid, $user, $yrlvlid, $srchSubCode, $srchSubDesc, $srchSection);
					}
					
					$stmt->execute();
					$result = $stmt->get_result();
					$fetch = $result->fetch_all(MYSQLI_ASSOC);
					$stmt->close();
					$dbConn->close();
				} else {
					http_response_code(500);
					echo json_encode(["error" => "Failed to prepare SQL statement."]);
				}
				break;
			
			case 'GET_TADI_RECORDS':

				if (!isset($_POST['prof_id'], $_POST['subj_off_id'], $_POST['strtDateSearch'], $_POST['endDateSearch'], $_POST['tadiStatus'])) {
					http_response_code(400);
					echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
					exit;
				}

				$profId = $_POST['prof_id'];
				$strtDateSearch = $_POST['strtDateSearch'] ?? null;
				$endDateSearch = $_POST['endDateSearch'] ?? null;
				$subj_off_id = $_POST['subj_off_id'];
				$tadiStatus = $_POST['tadiStatus'] ?? null;

				$qry = "SELECT 
							CONCAT(`SchlEnrollRegStudInfo_LAST_NAME`, ', ', `SchlEnrollRegStudInfo_FIRST_NAME`, ' ', `SchlEnrollRegStudInfo_MIDDLE_NAME`) AS stud_name,
							schl_tadi.`schltadi_id` AS schltadi_ID,
							schl_tadi.`schltadi_date` AS tadi_date,
							CONCAT(schl_tadi.`schltadi_mode`, ' ', schl_tadi.`schltadi_type`) AS tadi_modeType,
							schl_tadi.`schltadi_timein` AS tadi_timeIn,
							schl_tadi.`schltadi_timeout` AS tadi_timeOut,
							schl_tadi.`schltadi_activity` AS tadi_act,
							schl_tadi.`schltadi_status` AS tadi_status,
							schl_tadi.`startschltadi_filepath` AS tadi_filepath,
							schl_tadi.schlenrollsubjoff_id AS sub_off_id,
							schl_tadi.SchlProf_ID,
							section.`SchlAcadSec_NAME`
						FROM `schooltadi` AS schl_tadi

						LEFT JOIN `schoolstudent` AS schl_stud 
							ON schl_tadi.`schlstud_id` = schl_stud.`SchlStudSms_ID`
						LEFT JOIN `schoolenrollmentregistration` AS schl_enr_reg 
							ON schl_stud.`SchlEnrollRegColl_ID` = schl_enr_reg.`SchlEnrollRegSms_ID`
						LEFT JOIN `schoolenrollmentregistrationstudentinformation` AS schl_reg_stud 
							ON schl_enr_reg.`SchlEnrollRegSms_ID` = schl_reg_stud.`SchlEnrollReg_ID`
						LEFT JOIN `schoolenrollmentsubjectoffered` AS schl_subjoff
							ON schl_tadi.`schlenrollsubjoff_id` = schl_subjoff.`SchlEnrollSubjOffSms_ID`
						LEFT JOIN `schoolacademicsection` AS section
							ON schl_subjoff.`SchlAcadSec_ID` = section.`SchlAcadSecSms_ID`
						WHERE schl_tadi.`SchlProf_ID` = ?
						AND schl_tadi.`schlenrollsubjoff_id` = ?
						AND schl_tadi.`schltadi_isactive` = 1";

				$params = [];
				$types  = "ii"; 

				if (!empty($tadiStatus)) {
					$qry .= " AND schl_tadi.`schltadi_status` = ?";
					$types  .= "i";
					$params[] = $tadiStatus;
				}

				if (!empty($strtDateSearch) && !empty($endDateSearch)) {
					$qry .= " AND schl_tadi.`schltadi_date` BETWEEN ? AND ?";
					$types  .= "ss";
					$params[] = $strtDateSearch;
					$params[] = $endDateSearch;
				}

				$qry .= " ORDER BY schl_tadi.`schltadi_date` DESC";

				$stmt = $dbConn->prepare($qry);

				if (!$stmt) {
					die("Prepare failed: " . $dbConn->error);
				}

				$bindValues = array_merge([$types, $profId, $subj_off_id], $params);
				$stmt->bind_param(...$bindValues);

				$stmt->execute();
				$result = $stmt->get_result();

				$fetch = $result->fetch_all(MYSQLI_ASSOC);

				$stmt->close();
				$dbConn->close();
				break;
			case 'GET_TEACHER_TADI_REPORT':
				rateLimit(60, 5, 'get_teacher_tadi_report_rate_limit');

				if (!isset($_POST['lvl_id'], $_POST['prd_id'], $_POST['yr_id'], $_POST['yrlvl_id'])) {
					http_response_code(400);
					echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
					exit;
				}

				$user = $_SESSION['EMPLOYEE']['ID'] ?? $_SESSION['STUDENT']['ID'] ?? 0;
				$lvlid = $_POST['lvl_id'];
				$prdid = $_POST['prd_id'];
				$yrid = $_POST['yr_id'];
				$yrlvlid = $_POST['yrlvl_id'];
				$startDate = trim((string)($_POST['startDate'] ?? ''));
				$endDate = trim((string)($_POST['endDate'] ?? ''));

				if (($startDate !== '' && $endDate === '') || ($startDate === '' && $endDate !== '')) {
					http_response_code(400);
					echo json_encode(['success' => false, 'error' => 'Both startDate and endDate are required together']);
					exit;
				}

				[$forHead, $isAssistant, $crsCode] = asstHeadSwitch($user, $programs);

				$qry = "SELECT  
							CONCAT(emp.`SchlEmp_LNAME`, ', ', emp.`SchlEmp_FNAME`) AS prof_name,
							subj.`SchlAcadSubj_CODE` AS subject_code,
							subj.`SchlAcadSubj_DESC` AS subject_desc,
							sec.`SchlAcadSec_NAME` AS section_name,
							tadi.`schltadi_id`,
							tadi.`schltadi_date` AS tadi_date,
							tadi.`schltadi_timein` AS time_in,
							tadi.`schltadi_timeout` AS time_out,
							TIMEDIFF(tadi.schltadi_timeout, tadi.schltadi_timein) AS duration,
							tadi.`schltadi_mode` AS mode,
							tadi.`schltadi_type` AS type,
							tadi.`schltadi_activity` AS activity,
							tadi.`schltadi_status` AS status,
							tadi.`schltadi_late_status` AS late_status,
							tadi.`schltadi_mkup_date` AS mkup_date,
							CONCAT(info.`SchlEnrollRegStudInfo_LAST_NAME`, ', ', info.`SchlEnrollRegStudInfo_FIRST_NAME`) AS student_name

						FROM schooltadi tadi

						LEFT JOIN schoolstudent stud
							ON tadi.`schlstud_id` = stud.`SchlStudSms_ID`
						LEFT JOIN schoolenrollmentregistrationstudentinformation info
							ON stud.`SchlEnrollRegColl_ID` = info.`SchlEnrollReg_ID`
						LEFT JOIN schoolenrollmentsubjectoffered off
							ON tadi.`schlenrollsubjoff_id` = off.`SchlEnrollSubjOffSms_ID`
						LEFT JOIN schoolacademicsubject subj
							ON off.`SchlAcadSubj_ID` = subj.`SchlAcadSubjSms_ID`
						LEFT JOIN schoolacademicsection sec
							ON off.`SchlAcadSec_ID` = sec.`SchlAcadSecSms_ID`
						LEFT JOIN schoolacademiccourses crse
							ON off.`SchlAcadCrses_ID` = crse.`SchlAcadCrseSms_ID`
						LEFT JOIN schooldepartment dept
							ON crse.`SchlDept_ID` = dept.`SchlDeptSms_ID`
						LEFT JOIN schoolemployee emp
							ON tadi.`schlprof_id` = emp.`SchlEmpSms_ID`

						WHERE off.`SchlAcadLvl_ID` = ?
							AND off.`SchlAcadYr_ID` = ?
							AND off.`SchlAcadPrd_ID` = ?
							AND off.`SchlAcadYrLvl_ID` = ?
							AND tadi.schltadi_status = 1 
							AND tadi.schltadi_isactive = 1
							$forHead";

				if ($startDate && $endDate) {
					$qry .= " AND tadi.schltadi_date BETWEEN ? AND ?";
				}

				$qry .= " ORDER BY 
					emp.SchlEmp_LNAME, 
					subj.SchlAcadSubj_CODE,
					tadi.schltadi_date,
					tadi.schltadi_timein";

				$stmt = $dbConn->prepare($qry);
				
				if ($startDate && $endDate) {
					if($isAssistant){
						$stmt->bind_param("iiiisss", $lvlid, $yrid, $prdid, $yrlvlid, $crsCode, $startDate, $endDate);
					}else{
						$stmt->bind_param("iiiiiss", $lvlid, $yrid, $prdid, $yrlvlid, $user, $startDate, $endDate);
					}     
				} else {
					if($isAssistant){
						$stmt->bind_param("iiiis", $lvlid, $yrid, $prdid, $yrlvlid, $crsCode);
					}else{
						$stmt->bind_param("iiiii", $lvlid, $yrid, $prdid, $yrlvlid, $user);
					} 
				}
				
				$stmt->execute();
				$result = $stmt->get_result();
				$fetch = $result->fetch_all(MYSQLI_ASSOC);
				$stmt->close();
				$dbConn->close();
				break;
			case 'APPROVE_TADI_REQUEST':
				$user =$_SESSION['EMPLOYEE']['ID'] ?? $_SESSION['STUDENT']['ID'] ?? 0;
				if (empty($user) && $_SESSION['EMPLOYEE']['ID'] == $user) {
					$fetch = ['status' => 'failed', 'error' => 'Session expired'];
					echo json_encode($fetch);
					exit;
				}
				
				if (!isset($_POST['tadi_id'], $_POST['prof_id'], $_POST['subj_id'])) {
					echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
					exit;
				}

				$user = $_SESSION['EMPLOYEE']['ID'] ?? $_SESSION['STUDENT']['ID'] ?? 0;
				$tadId = $_POST['tadi_id'];
				$profId = $_POST['prof_id'];
				$subjId = $_POST['subj_id'];

				$dueDateQuery = "SELECT t.tadi_verified_date
								FROM schooltadi t
								WHERE t.schlprof_id = ?
								AND t.schltadi_id = ?
								AND t.schlenrollsubjoff_id = ?
								AND t.schltadi_isconfirm = 0
								AND t.schltadi_status = 1
								AND t.schltadi_isactive = 1";

				$dueStmt = $dbConn->prepare($dueDateQuery);

				if (!$dueStmt) {
					echo json_encode(['success' => false, 'error' => 'Prepare failed: ' . $dbConn->error]);
					exit;
				}
				$dueStmt->bind_param("iii", $profId, $tadId, $subjId);
				$dueStmt->execute();
				$dueResult = $dueStmt->get_result();
				$dueRow = $dueResult->fetch_assoc();
				$dueStmt->close();

				if (!$dueRow) {
					echo json_encode(['success' => false, 'error' => 'Record not found or already processed']);
					exit;
				}
				
				$recordDate = new DateTime($dueRow['tadi_verified_date']);
				$today = new DateTime();
				$today->setTime(0, 0, 0);
				$recordDate->setTime(0, 0, 0);

				$pastLimit = clone $today;
				$pastLimit->modify('-3 days');

				if ($recordDate < $pastLimit) {
					echo json_encode(['success' => false, 'error' => 'This record is past the 3-day verification window and can no longer be updated']);
					exit;
				}

				if ($recordDate > $today) {
					echo json_encode(['success' => false, 'error' => 'This record is dated in the future and cannot be approved']);
					exit;
				}
				[$forHead, $isAssistant, $crsCode]=asstHeadSwitch($user, $programs);

				$qry =	"UPDATE `schooltadi`
						INNER JOIN `schoolenrollmentsubjectoffered` AS `off`
							ON `schooltadi`.`schlenrollsubjoff_id` = `off`.`SchlEnrollSubjOffSms_ID`
						INNER JOIN `schoolacademiccourses` AS `crse`
							ON `off`.`SchlAcadCrses_ID` = `crse`.`SchlAcadCrseSms_ID`
						INNER JOIN `schooldepartment` AS `dept`
							ON `crse`.`SchlDept_ID` = `dept`.`SchlDeptSms_ID`
						SET `schltadi_isconfirm` = 1,
							tadi_who_approved = ?,
							tadi_approved_date = NOW()
						WHERE `schltadi_id` = ?
						AND `schltadi_status` = 1
						AND `schltadi_isactive` = 1
						AND `schltadi_isconfirm` = 0
						AND schooltadi.`schlprof_id` = ?
						AND schooltadi.`schlenrollsubjoff_id` = ?
						$forHead";

				$stmt = $dbConn->prepare($qry);
				if($isAssistant){
					$stmt->bind_param("iiiis",$user, $tadId, $profId, $subjId, $crsCode);
				}else{
					$stmt->bind_param("iiiii",$user, $tadId, $profId, $subjId, $user);
				}
				
				$stmt->execute();
				$affectedRows = $stmt->affected_rows;
				$stmt->close();
				$dbConn->close();

				if($affectedRows > 0){
					$fetch = ['status' => 'success'];
				} else {
					$fetch = ['status' => 'failed'];
				}
				break;
			case 'REJECT_TADI_REQUEST':
				$user =$_SESSION['EMPLOYEE']['ID'] ?? $_SESSION['STUDENT']['ID'] ?? 0;
				if (empty($user) && $_SESSION['EMPLOYEE']['ID'] == $user) {
					$fetch = ['status' => 'failed', 'error' => 'Session expired'];
					echo json_encode($fetch);
					exit;
				}
				
				if (!isset($_POST['tadi_id'], $_POST['prof_id'], $_POST['subj_id'])) {
					echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
					exit;
				}

				$user = $_SESSION['EMPLOYEE']['ID'] ?? $_SESSION['STUDENT']['ID'] ?? 0;
				$tadId = $_POST['tadi_id'];
				$profId = $_POST['prof_id'];
				$subjId = $_POST['subj_id'];

				$dueDateQuery = "SELECT t.tadi_verified_date
								FROM schooltadi t
								WHERE t.schlprof_id = ?
								AND t.schltadi_id = ?
								AND t.schlenrollsubjoff_id = ?
								AND t.schltadi_isconfirm = 0
								AND t.schltadi_status = 1
								AND t.schltadi_isactive = 1";

				$dueStmt = $dbConn->prepare($dueDateQuery);

				if (!$dueStmt) {
					echo json_encode(['success' => false, 'error' => 'Prepare failed: ' . $dbConn->error]);
					exit;
				}
				$dueStmt->bind_param("iii", $profId, $tadId, $subjId);
				$dueStmt->execute();
				$dueResult = $dueStmt->get_result();
				$dueRow = $dueResult->fetch_assoc();
				$dueStmt->close();

				if (!$dueRow) {
					echo json_encode(['success' => false, 'error' => 'Record not found or already processed']);
					exit;
				}
				
				$recordDate = new DateTime($dueRow['tadi_verified_date']);
				$today = new DateTime();
				$today->setTime(0, 0, 0);
				$recordDate->setTime(0, 0, 0);

				$pastLimit = clone $today;
				$pastLimit->modify('-3 days');

				if ($recordDate < $pastLimit) {
					echo json_encode(['success' => false, 'error' => 'This record is past the 3-day verification window and can no longer be updated']);
					exit;
				}

				if ($recordDate > $today) {
					echo json_encode(['success' => false, 'error' => 'This record is dated in the future and cannot be rejected']);
					exit;
				}
				[$forHead, $isAssistant, $crsCode]=asstHeadSwitch($user, $programs);

				$qry =	"UPDATE `schooltadi`
						INNER JOIN `schoolenrollmentsubjectoffered` AS `off`
							ON `schooltadi`.`schlenrollsubjoff_id` = `off`.`SchlEnrollSubjOffSms_ID`
						INNER JOIN `schoolacademiccourses` AS `crse`
							ON `off`.`SchlAcadCrses_ID` = `crse`.`SchlAcadCrseSms_ID`
						INNER JOIN `schooldepartment` AS `dept`
							ON `crse`.`SchlDept_ID` = `dept`.`SchlDeptSms_ID`
						SET `schltadi_status` = 0,
							tadi_who_rejected = ?,
							tadi_rejected_date = NOW()
						WHERE `schltadi_id` = ?
						AND `schltadi_status` = 1
						AND `schltadi_isactive` = 1
						AND `schltadi_isconfirm` = 0
						AND schooltadi.`schlprof_id` = ?
						AND schooltadi.`schlenrollsubjoff_id` = ?
						$forHead";

				$stmt = $dbConn->prepare($qry);
				if($isAssistant){
					$stmt->bind_param("iiiis",$user, $tadId, $profId, $subjId, $crsCode);
				}else{
					$stmt->bind_param("iiiii",$user, $tadId, $profId, $subjId, $user);
				}
				
				$stmt->execute();
				$affectedRows = $stmt->affected_rows;
				$stmt->close();
				$dbConn->close();

				if($affectedRows > 0){
					$fetch = ['status' => 'success'];
				} else {
					$fetch = ['status' => 'failed'];
				}
				break;
			case 'GET_SUBMITTED_REC':
                if (!isset($_POST['subj_Id'], $_POST['tadiId'], $_POST['profId'])) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
                    exit;
                }

                $subj_Id = $_POST['subj_Id'];
                $tadi_id = $_POST['tadiId'];
                $profId = $_POST['profId'];

                $qry = "SELECT 
                            schl_tadi.`schltadi_id` AS schltadi_ID,
                            schl_tadi.`schltadi_date` AS tadi_date,
                            CONCAT(schl_tadi.`schltadi_mode`, ' ', schl_tadi.`schltadi_type`) AS tadi_modeType,
                            schl_tadi.`schltadi_timein` AS tadi_timeIn,
                            schl_tadi.`schltadi_timeout` AS tadi_timeOut,
                            schl_tadi.`schltadi_activity` AS tadi_act,
                            schl_tadi.`schltadi_status` AS tadi_status,
                            schl_tadi.schlenrollsubjoff_id AS sub_off_id,
                            schl_tadi.SchlProf_ID,
                            schl_tadi.schlstud_id,
                            CONCAT(schl_reg_stud.SchlEnrollRegStudInfo_LAST_NAME, ', ', schl_reg_stud.SchlEnrollRegStudInfo_FIRST_NAME) AS stud_name,
                            acad_sec.SchlAcadSec_DESC AS section,
                            schl_tadi.schltadi_class_instruct AS class_instruction
                        FROM `schooltadi` AS schl_tadi 
                        
                        LEFT JOIN `schoolstudent` AS schl_stud 
                            ON schl_tadi.`schlstud_id` = schl_stud.`SchlStudSms_ID` 

                        LEFT JOIN `schoolenrollmentregistration` AS schl_enr_reg 
                            ON schl_stud.`SchlEnrollRegColl_ID` = schl_enr_reg.`SchlEnrollRegSms_ID` 

                        LEFT JOIN `schoolenrollmentregistrationstudentinformation` AS schl_reg_stud 
                            ON schl_enr_reg.`SchlEnrollRegSms_ID` = `schl_reg_stud`.`SchlEnrollReg_ID` 

                        LEFT JOIN schoolenrollmentsubjectoffered AS subj_off
                            ON schl_tadi.schlenrollsubjoff_id = subj_off.SchlEnrollSubjOffSms_ID

                        LEFT JOIN schoolacademicsection AS acad_sec
                            ON subj_off.SchlAcadSec_ID = acad_sec.SchlAcadSecSms_ID

                        WHERE schl_tadi.`schlprof_id` IN (?)
                        AND schl_tadi.`schlenrollsubjoff_id` =  ?
                        AND schltadi_isactive = 1
                        AND `schl_tadi`.`schltadi_id` = ?
                        ORDER BY schl_tadi.`schltadi_date`, schl_tadi.`schltadi_timein`";
                
                $stmt = $dbConn->prepare($qry);
                $stmt->bind_param("iii",$profId,$subj_Id,$tadi_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $fetch = $result->fetch_all(MYSQLI_ASSOC);
                $stmt->close();

                foreach ($fetch as &$row) {
                    $row['tadi_act'] = sanitizeText($row['tadi_act'] ?? '');
                    $row['tadi_modeType'] = sanitizeText($row['tadi_modeType'] ?? '');
                }
                unset($row);
                break;
			default:
				http_response_code(400);
                echo json_encode(["error" => "Invalid request type."]);
				exit;
		}
	}
	echo json_encode($fetch); 
?>