<?php

    $uploadDir = 'uploads/'; 
     
    // Allowed file types 
    $allowTypes = array('pdf', 'doc', 'docx', 'jpg', 'png', 'jpeg'); 
     
    // Default response 
    $response = array( 
        'status' => 0, 
        'message' => 'Form submission failed, please try again.'
    ); 

    $userid = '22-000001';


    if (isset($_FILES) && !empty($_FILES))
    {   

        $uploadStatus = 1;          
        // Upload file 
        $uploadedFile = '';

        if(!empty($_FILES["file"]["name"]))
        {
            $fileName = basename($_FILES["file"]["name"]); 

            $targetFilePath = $uploadDir . $userid . "/" . $fileName; 

            $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

            if (!file_exists($uploadDir . $userid))/* Check folder exists or not */
            {
                @mkdir($uploadDir . $userid, 0777);/* Create folder by using mkdir function */
            }

            if(in_array($fileType, $allowTypes))
            { 
                // Upload file to the server 
                if(move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath))
                { 
                    $uploadedFile = $fileName; 
                    $response['message'] = 'File Uploaded successfully'; 
                    $response['status']  = $uploadStatus;
                }
                else
                { 
                    $uploadStatus = 0; 
                    $response['message'] = 'Sorry, there was an error uploading your file.'; 
                } 
            }
            else
            { 
                $uploadStatus = 0; 
                $response['message'] = 'Sorry, only '.implode('/', $allowTypes).' files are allowed to upload.'; 
            }


            $response['status'] = $uploadStatus; 

        }

    }
    else
    {
        $response['message'] = ' NO FILE HERE. '; 
    }

    echo json_encode($response);

?>