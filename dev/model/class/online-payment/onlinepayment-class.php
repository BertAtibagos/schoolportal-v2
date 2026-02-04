<?php
	
	require('../../configuration/connection-config.php');
	
	if( isset($_POST['transaction']) || isset($_POST['amount']) || isset($_POST['bank']) || isset($_POST['referenceNumber']) || isset($_POST['date']) || isset($_POST['file']))
    {

    	$uploadDir = 'uploads/'; 
     	$userid = '22-000001';

     	$targetFilePath = $uploadDir . $userid . "/" . $_POST['file'];

		$qry =	"	INSERT INTO `schoolonlinepayment` (`schlpayment_transtype`, `schlpayment_amountpaid`, `schlpayment_bankname`, `schlpayment_transno`, `schlpayment_transdate`,`schlpayment_receipt`) 
					VALUES ('" . $_POST['transaction'] . "', '" . $_POST['amount'] . "', '" . $_POST['bank'] . "', '" . $_POST['referenceNumber'] . "', '" . $_POST['date'] ."', '" . $targetFilePath ."' )
				"; 	

		// echo $qry;	

		mysqli_query($dbConn, $qry) or die(mysqli_error($dbConn));

		$count = mysqli_affected_rows($dbConn);

        $createDropDown      = "<table id='regtable' class='table table-bordered'>";
        $createDropDown     .= "    <thead class='table table-primary'>";
        $createDropDown     .= "        <tr>";
        $createDropDown     .= "            <th scope='col' style='text-align:center;'>#</th>";
        $createDropDown     .= "            <th scope='col' style='text-align:center;'>Transaction Type</th>";
        $createDropDown     .= "            <th scope='col' style='text-align:center;'>Amount Paid</th>";
        $createDropDown     .= "            <th scope='col' style='text-align:center;'>Bank Name</th>";
        $createDropDown     .= "            <th scope='col' style='text-align:center;'>Transaction Reference Number</th>";
        $createDropDown     .= "            <th scope='col' style='text-align:center;'>Transaction Date</th>";
        $createDropDown     .= "            <th scope='col' style='text-align:center;'>Receipt</th>";
        $createDropDown     .= "        </tr>";
        $createDropDown     .= "    </thead>";

    

        $qry = "    SELECT * 
                    FROM `schoolonlinepayment`
                ";
        $rsreg = $dbConn->query($qry);
        $fetchDatareg = $rsreg->fetch_all(MYSQLI_ASSOC);
        $rsreg->close();

        $createDropDown     .= "    <tbody>";
        $count = 1;

        foreach($fetchDatareg as $regitem)
        {
            $createDropDown  .= "<tr>";
            $createDropDown  .= "   <td style='text-align:center;'><label type='label'>". $count++ ."</label></td>";
            $createDropDown  .= "   <td style='text-align:center;'><label type='label'>". $regitem['schlpayment_transtype'] ."</label></td>";
            $createDropDown  .= "   <td style='text-align:center;'><label type='label'>". $regitem['schlpayment_amountpaid'] ."</label></td>";
            $createDropDown  .= "   <td style='text-align:center;'><label type='label'>". $regitem['schlpayment_bankname'] ."</label></td>";
            $createDropDown  .= "   <td style='text-align:center;'><label type='label'>". $regitem['schlpayment_transno'] ."</label></td>";
            $createDropDown  .= "   <td style='text-align:center;'><label type='label'>". $regitem['schlpayment_transdate'] ."</label></td>";
            $createDropDown  .= "   <td style='text-align:center;'><label type='label'>". $regitem['schlpayment_receipt'] ."</label></td>";

            //$createDropDown  .=   "<td style='text-align:center;'><label type='label'><button type='button' class='btn btn-success' value = ".$regitem['SUBJ_ID']."> View Class List </button></label></td>";

            $createDropDown  .= "</tr>";

        }
        $createDropDown     .= "    </tbody>";
        $createDropDown     .= "</table>";

		echo $createDropDown;

	mysqli_close($dbConn);
	
    }


    
?>