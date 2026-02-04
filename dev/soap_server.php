
<?php
// Used for Student files upload from ID assist.
ini_set("soap.wsdl_cache_enabled", "0");
error_reporting(E_ALL);

class FileUploadService {
    private $validUsername = "0x0200000029cc4746d3009e23cd5511bb84523b9254c203cf76cb721712cefa924bea4a47fd098d391b0e0da75679af8dc4eb86ea392a3053a90eca7edf0090122b03c516";
    private $validPassword = "0x02000000168f2bb0d01027604d0793cafbcf547dbeb3cb4abdc165b45ecfae976d5b1dc663100e6e6af435fdd484ef8c3618341c";
	
    public function UploadFile($username, $password, $fileName, $usertype, $userfiletype, $fileDataBase64) 
	{
		try 
		{
			if ($username !== $this->validUsername || $password !== $this->validPassword) {
				return "❌ Authentication failed.";
			}
			$uploadDir = __DIR__ . "/public/users";
			if ($usertype == 1) {//for employee
				if ($userfiletype == 1){ //employee image file
					$uploadDir = $uploadDir . "/employee/images/"; // directory for employee files
				} else { //2 = /employee document file
					$uploadDir = $uploadDir . "/employee/documents/"; // directory for employee documents
				}
			} else {//for student
				if ($userfiletype == 1){ //student image file
					$uploadDir = $uploadDir . "/student/images/"; // directory for student files
				} else { //2 = /employee document file
					$uploadDir = $uploadDir . "/student/documents/"; // directory for student documents
				}
			}

			if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

			$filePath = $uploadDir . basename($fileName);
			$fileData = base64_decode($fileDataBase64);

			if (file_put_contents($filePath, $fileData)) {
				return "✅ File uploaded successfully: " . htmlspecialchars($fileName);
			} else {
				return "❌ Failed to upload file.";
			}
		} catch (Exception $e) {
            return "❌ Error: " . $e->getMessage();
        }
    }
	public function IsRunning($username, $password) 
	{
        if ($username !== $this->validUsername || $password !== $this->validPassword) {
            return "failed";
        } else {
			return "success";	
		}
    }
}

$server = new SoapServer(null, ['uri' => "https://schoolportal.fcpc.edu.ph/soap_server.php"]);
$server->setClass("FileUploadService");
$server->handle();
?>