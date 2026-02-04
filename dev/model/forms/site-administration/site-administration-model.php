<?php 
    session_start();

    $_SESSION['role'] = 'admin';

    // Check if session exists and validate user role
    if (!isset($_SESSION['USERID']) || $_SESSION['role'] !== 'admin') {
        http_response_code(403);
        exit;
    }
?>

<section class="section-site-administration" style="padding-bottom: 5rem;">
    <style>
        .section-site-administration a {
            text-decoration: none; 
            color: #071976; 
            cursor: pointer;
        }
        
        .section-site-administration a:hover {
            text-decoration: underline;  
        }

        .section-site-administration h2, .section-site-administration h3 {
            color: #071976;
            padding-bottom: 1rem;
        }
        
        .section-site-administration #divPages {
            display: none;
        }
    </style>

    <div class="w-75 m-auto" id="divMain">
        <h2> Site Administration </h2>
        <hr>

        <div class="row py-4">
            <div class="col-sm-3">
                <h3> Accounts </h3>
            </div>
            <div class="col">
                <p class="mb-0"><a class="admin-module" id="" name="userlist">User list</a></p>
                <p class="mb-0"><a class="admin-module" id="" name="newuser">Add a new user</a></p>
                <p class="mb-0"><a class="admin-module" id="" name="permission">Permissions</a></p>
                <p class="mb-0"><a class="admin-module" id="" name="bulkuser">Bulk user actions</a></p>
            </div>
        </div>

        <hr>
        
        <div class="row py-4">
            <div class="col-sm-3">
                <h3> Plugins </h3>
            </div>
            <div class="col">
                <p class="mb-0"><a class="admin-module" id="" name="pluginlist">Plugin list</a></p>
                <p class="mb-0"><a class="admin-module" id="" name="pluginupdate">Update plugins</a></p>
            </div>
        </div>

        <hr>
        
        <div class="row py-4">
            <div class="col-sm-3">
                <h3> Appearance </h3>
            </div>
            <div class="col">
                <p class="mb-0"><a class="admin-module" id="" name="theme">Theme</a></p>
                <p class="mb-0"><a class="admin-module" id="" name="siteimages">Site Images</a></p>
                <p class="mb-0"><a class="admin-module" id="" name="metacontent">Meta content</a></p>
                <p class="mb-0"><a class="admin-module" id="" name="maintenance">Maintenance mode</a></p>
            </div>
        </div>

        <hr>
        
        <div class="row py-4">
            <div class="col-sm-3">
                <h3> Server </h3>
            </div>
            <div class="col">
                <p class="mb-0"><a class="admin-module" id="" name="smtp">SMTP</a></p>
                <p class="mb-0"><a class="admin-module" id="" name="oauth2">Oauth 2 Sevices</a></p>
                
            </div>
        </div>
    </div>

    <div class="w-75 m-auto" id="divPages">
        <button type="button" class="btn btn-outline-primary" id="btnBack"><i class="fa-solid fa-arrow-left"></i> Back</button>
        <div id="divLoad"></div>
    </div>
    <?php
        $script = '/../../../js/custom/site-administration-script.js';
        $path = __DIR__ . $script;
    ?>
    <script src="../../js/custom/site-administration-script.js?t=<?= file_exists($path) ? filemtime($path) : time() ?>"></script>


</section>