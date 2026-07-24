<?php
    session_start();

    // $_SESSION['EMPLOYEE'] = [
    //     'ACCESS_RIGHTS' => "1,1,ACADEMICS,,fa-solid fa-book-open,PARENT,0;1,5,SCHEDULE,,,CHILD,1;1,6,CLASS LIST,,,CHILD,1;1,7,EXAM PERMIT,,,CHILD,1;1,20,FORMS,,fa-solid fa-file-lines,PARENT,0;1,26,TADI,,,CHILD,20;4,1,ACADEMICS,,fa-solid fa-book-open,PARENT,0;4,8,GRADING SCALE,,,CHILD,1;4,9,GRADES SUBMISSION,,,CHILD,1;4,10,ENCODING STATUS,,,CHILD,1;10,28,TADI - DEAN,,,CHILD,20",
    //     'CATEGORY' => "INSTRUCTOR",
    //     'CRSEID' => 0,
    //     'DEPID' => 6,
    //     'ID' => 312,
    //     'IDNO' => 2050,
    //     'INFO' => "COLLEGE OF COMPUTER STUDIES - PROFESSOR",
    //     'LVLID' => 0,
    //     'PRDID' => 0,
    //     'SECID' => 0,
    //     'YRID' => 0,
    //     'YRLVLID' => 0
    //     ];

    $_SESSION['STUDENT'] = [
        'ACCESS_RIGHTS' => "2,1,ACADEMICS,,fa-solid fa-book-open,PARENT,0;2,5,SCHEDULE,,,CHILD,1;2,11,FEES,,fa-solid fa-wallet,PARENT,0;2,12,TUITION FEE,,,CHILD,11;2,14,ENROLLMENT,,fa-regular fa-address-card,PARENT,0;2,15,ONLINE ENROLLMENT,,,CHILD,14;2,20,FORMS,,fa-solid fa-file-lines,PARENT,0;2,21,SURVEY,,,CHILD,20;7,26,TADI,,,CHILD,20;9,3,GRADES,,,CHILD,1",
        'CATEGORY' => "STUDENT",
        'CRSEID' => 19,
        'DEPID' => 6,
        'ID' => 957,
        'IDNO' => 22-00341,
        'INFO' => "BACHELOR OF SCIENCE IN INFORMATION TECHNOLOGY - 3RD YEAR, 2ND SEM, 2025-2026, BSIT 3B",
        'LVLID' => 2,
        'PRDID' => 6,
        'SECID' => 2311,
        'YRID' => 19,
        'YRLVLID' => 8
        ];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Employee Attendance</title>
    <link rel="stylesheet" href="css.css?t=<?php echo time(); ?>">
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet"
    />
</head>
<body class="bg-light">

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card p-3 mb-4 shadow-sm">
                <div class="row g-3" id="subjectFilter">
                
                </div>
            </div>

            <div class="card p-3 shadow-sm attendance-card">
                <div id="attndnc_logs_card">
                
                </div>
            </div>
        </div>
    </div>
</div>

</body>

<script>
    window.__SESSION__ = <?php echo json_encode($_SESSION['STUDENT']); ?>;
</script>

<script type="module" src="view/component-loader.js?t=<?php echo time(); ?>"></script>
</html>
