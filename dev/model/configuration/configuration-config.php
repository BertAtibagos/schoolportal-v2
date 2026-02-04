<?php
    // define("DB_HOST", "localhost");
    // define("DB_USER", "schoolportal_fcpc_edu_ph_user");
    // define("DB_PASS", "sDKmksGWX#R7qScX62c4");
    // define("DB_NAME", "schoolportal_fcpc_edu_ph");
    
	// define("DB_CHARSET", "utf8mb4");
	
    define("DB_HOST", "46.21.150.116");
    define("DB_USER", "schoolportal_fcpc_edu_ph_remote_user");
    define("DB_PASS", '6$Sf&i3@8!Nx5GcKYbjP');
    define("DB_NAME", "schoolportal_fcpc_edu_ph");
    define("DB_PORT", "3306");

    // define("DB_HOST", "localhost");
    // define("DB_USER", "root");
    // define("DB_PASS", "sacred");
    // define("DB_NAME", "schoolportal_fcpc_edu_ph");
	// define("DB_PORT", "3307");

    
    define("SMTP_HOST", "smtp.gmail.com");
    // define("SMTP_USER", "schoolportalnotification@fcpc.edu.ph"); // changed to 1: 04-15-2025
    define("SMTP_USER", "schoolportalnotification1@fcpc.edu.ph");
    define("SMTP_PASS", "zruu gsle orhx iirh");
    define("SMTP_PORT", 587); // or 465 for SSL
    define("SMTP_FROM", "no-reply@fcpc.edu.ph");
    define("SMTP_FROM_NAME", "FCPC ICT Department");

    // GOOGLE RECAPTCHA
    define('RECAPTCHA_SECRET', '6LfweHorAAAAAFx4tlpPMzqSRfJuHGwL-Dforik7');
    
    // GOOGLE OAUTH v2.0 Connection Config
    define("CLIENT_ID", "709083651862-84inbfq6hamku9mepqhvknl9bb9etb4a.apps.googleusercontent.com");
    define("CLIENT_SECRET", "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");
    // define("CLIENT_REDIRECT", "http://localhost/schoolportal/dev/model/oauth.php");
    define("CLIENT_REDIRECT", "https://schoolportal.fcpc.edu.ph/model/oauth.php");

    
    // Data encryption and decryption
    // ✅ Put your secret key somewhere safe (like an .env file)
    define('SECRET_KEY', 'b39fb02ea5551fa6b099d80dc8dadb2e041249aa000b0610e7afc07b8871ffdf'); // 32 bytes for AES-256
    define('CIPHER_METHOD', 'aes-256-cbc');

    // Password Hashing Config
    // define('MEMORY_COST', 1 << 18);
    // define('TIME_COST', 4);
    // define('THREADS', 2);

    define('MEMORY_COST', 1 << 16); // 65536 KiB = 64 MB (lower than 256 MB)
	define('TIME_COST', 2);         // Keep time cost reasonable
	define('THREADS', 1);

    $options = [
        'memory_cost' => MEMORY_COST,
        'time_cost'   => TIME_COST,
        'threads'     => THREADS,
    ];

    // $link = "http://localhost/schoolportal";
?>