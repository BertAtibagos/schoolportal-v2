<?php 
   session_start();.
   session_unset();
   
   $_SESSION['SYSUSER_ID'] = NULL;
   $_SESSION['SYSUSER_NAME'] = NULL;
   header("Location: index.php");        
?>