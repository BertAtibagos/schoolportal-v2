<?php
    /* This will give an error. Note the output
    * above, which is before the header() call */
    session_start();
    $_SESSION['maintenance'] = 0;
    // header('Location: http://localhost/schoolportal/dev/index.php'); // for local development

    header("Location: model/login-model.php"); // for live 
    exit();
?>