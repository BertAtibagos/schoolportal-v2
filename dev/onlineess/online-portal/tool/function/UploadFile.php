<?php
//if (isset($_POST['requirementidhidden']) && isset($_POST['insertedid']))
//{	
	//$requirementidhidden = strval($_POST['requirementidhidden']);
	//$insertedid = 1;//intval($_POST['insertedid']);
	$target_dir= '../../UPLOADS/';
	$filename1 = $_FILES['doc1']['name'];
	$filename2 = $_FILES['doc2']['name'];
	$filename3 = $_FILES['doc3']['name'];
	$filename4 = $_FILES['doc4']['name'];
	
	//$fileNameCmps1 = explode(".", $filename1);
	//$fileNameCmps2 = explode(".", $filename2);
	//$fileNameCmps3 = explode(".", $filename3);
	//$fileNameCmps4 = explode(".", $filename4);
	
	//$fileExtension1 = strtolower(end($fileNameCmps1));
	//$fileExtension2 = strtolower(end($fileNameCmps2));
	//$fileExtension3 = strtolower(end($fileNameCmps3));
	//$fileExtension4 = strtolower(end($fileNameCmps4));
	
	//$rec_id = explode('[|]', $requirementidhidden);
	//$rec_id = '5';
	
	//$newFileName1 = $insertedid . '-' . $rec_id[0] . '.' . $fileExtension1;
	//$newFileName2 = $insertedid . '-' . $rec_id[1] . '.' . $fileExtension2;
	//$newFileName3 = $insertedid . '-' . $rec_id[2] . '.' . $fileExtension3;
	//$newFileName4 = $insertedid . '-' . $rec_id[3] . '.' . $fileExtension4;
	
	//$newFileName1 = $insertedid . '-' . $rec_id . '.' . $fileExtension1;
	//$newFileName2 = $insertedid . '-' . $rec_id . '.' . $fileExtension2;
	//$newFileName3 = $insertedid . '-' . $rec_id . '.' . $fileExtension3;
	//$newFileName4 = $insertedid . '-' . $rec_id . '.' . $fileExtension4;
	
	/* Location */
	//$location1 = $target_dir.$newFileName1;
	//$location2 = $target_dir.$newFileName2;
	//$location3 = $target_dir.$newFileName3;
	//$location4 = $target_dir.$newFileName4;
	$location1 = $target_dir.$filename1;
	$location2 = $target_dir.$filename2;
	$location3 = $target_dir.$filename3;
	$location4 = $target_dir.$filename4;
	
	$uploadOk = 1;
	if($uploadOk == 0){
	   echo 0;
	}else{
	   if(move_uploaded_file($_FILES['doc1']['tmp_name'], $location1)){
			if(move_uploaded_file($_FILES['doc2']['tmp_name'], $location2)){
			  if(move_uploaded_file($_FILES['doc3']['tmp_name'], $location3)){
				   if(move_uploaded_file($_FILES['doc4']['tmp_name'], $location4)){
						echo 1;
				   } else {
						echo 0;
				   }
			   }else{
				  echo 0;
			   }
		   }else{
			  echo 0;
		   }
	   }else{
		  echo 0;
	   }
	}
//}
?>
