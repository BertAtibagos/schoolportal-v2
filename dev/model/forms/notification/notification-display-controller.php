<?php
	session_start();
	include_once '../../configuration/connection-config.php';

	if(isset($_POST['type'])){
		if ($_POST['type'] == 'GET_NOTIF'){
            $qry = "SELECT `SchlNotif_TITLE` `TITLE`,
                        `SchlNotif_CONTENT` `CONTENT`,
                        `SchlNotif_WITH_LINK` `WITH_LINK`,
                        `SchlNotif_LINK_TEXT` `LINK_TEXT`,
                        `SchlNotif_LINK_URL` `LINK_URL`,
                        `SchlNotif_START_DATE` `START_DATE`,
                        `SchlNotif_END_DATE` `END_DATE`,
                        `SchlNotif_DATE_CREATED` `CREATE_DATE`,
                        `SchlNotif_ISACTIVE` `ACTIVE`,
                        `SchlNotif_STATUS` `STATUS`,
                        `SchlNotif_AUDIENCE` `AUDIENCE`
                    FROM
                        `schoolnotification` 
                    
                    WHERE `SchlNotif_ISACTIVE` = 1
                        AND `SchlNotif_STATUS` = 1
                    ";
            $rsreg = $dbConn->query($qry);	
            $fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);
		} else {
			echo '<script>alert("POST NOT FOUND. CONTACT ICT DEPARTMENT")</script>';
		}
		
	} else {
    	echo '<script>alert("POST NOT SET. CONTACT ICT DEPARTMENT")</script>';
	}

	$rsreg->free_result();
	$dbConn->close();
	echo json_encode($fetch);
?>