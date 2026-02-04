<?php 
	$qry = " 	SELECT * 
				FROM `schoolacademiclevel` `ACAD_LVL`
				WHERE  `ACAD_LVL`.`schlacadlvl_status` = 1 AND
					`ACAD_LVL`.`schlacadlvl_isactive` = 1";

   	$rsreg = $dbConn->query($qry);
	$fetchacadlevel = $rsreg->fetch_ALL(MYSQLI_ASSOC);	
?>

