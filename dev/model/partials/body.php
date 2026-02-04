<body>
    <div class="d-flex">
        <?php include_once 'sidebar.php';?>

        <!-- Main Content -->
        <div class="d-flex flex-column w-100" id="contentContainer">
            <?php include_once 'header.php';?>

            <div class="flex-grow-1" id="divContent">
                <?php include_once 'dashboard.php';?>
            </div>
            
            <?php include_once 'footer.php';?>
        </div>
    </div>
</body>