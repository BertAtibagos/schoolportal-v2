<?php
// function extractModuleNames(string $accessRights): array {
//     return array_map(
//         fn($item) => explode(',', $item)[2] ?? null,
//         explode(';', $accessRights)
//     );
// }
// $mergedAccessRights = [];

$roles = ['EMPLOYEE', 'STUDENT'];

foreach ($roles as $role) {
    if (!empty($_SESSION[$role]['ACCESS_RIGHTS'])) {

        // Keep original string for merging
        $mergedAccessRights[] = $_SESSION[$role]['ACCESS_RIGHTS'];

        // // Replace ACCESS_RIGHTS with parsed module names
        // $_SESSION[$role]['MODULES'] = array_values(
        //     array_filter(extractModuleNames($_SESSION[$role]['ACCESS_RIGHTS']))
        // );
    }
}

// Final merged string (if needed)
$mergedAccessRights = implode(';', $mergedAccessRights);
?>


<!-- Sidebar -->
<div id="sidebar" class="sidebar p-2">
    <div id="divButtonContainer" class="w-100 text-center">
        <button id="toggleBtn" class="btn mb-3" title="Open menu" data-bs-placement="right">
            <i class="fa fa-bars"></i>
        </button>
    </div>

    <div class="module-container">
        <form action="" method="post">
            <ul class="nav nav-pills flex-column" id="module-list">
                <li class="nav-item">
                    <a href="session.php?page=dashboard" class="nav-link d-flex align-items-center rounded main-menu" title="Dashboard" data-bs-placement="right">
                        <div class="text-center"><i class="fa fa-chart-column"></i></div>
                        <div class="text-start m-2 module-name"><span>Dashboard</span></div>
                    </a>
                </li>
                <?php
                    function buildMenu($menuString, $notifications = []) {
                        // Parse menu items into associative array
                        $items = [];
                        $rows = explode(";", $menuString);

                        foreach ($rows as $row) {
                            $row = trim($row);
                            if (empty($row)) continue;

                            list($usertype, $id, $label, $link, $icon, $type, $parent) = array_map('trim', explode(",", $row));

                            $items[$id] = [
                                'usertype' => $usertype,
                                'id'       => $id,
                                'label'    => ucwords(strtolower($label)),
                                'link'     => $link,
                                'icon'     => strtolower($icon),
                                'type'     => strtoupper($type),
                                'parent'   => $parent,
                                'children' => []
                            ];
                        }

                        // Build hierarchical tree
                        $tree = [];
                        foreach ($items as $id => &$item) {
                            if ($item['parent'] != 0 && isset($items[$item['parent']])) {
                                $items[$item['parent']]['children'][] = &$item;
                            } else {
                                $tree[] = &$item;
                            }
                        }
                        unset($item);

                        // Recursive menu renderer
                        $renderMenuRecursive = function($items, $notifications, &$totalNotifications) use (&$renderMenuRecursive) {
                            $html = '';
                            $total = 0;

                            foreach ($items as $item) {
                                $selfCount = $notifications[strtolower($item['label'])] ?? 0;
                                $childTotal = 0;
                                $childHtml = '';

                                if (!empty($item['children'])) {
                                    $childHtml = $renderMenuRecursive($item['children'], $notifications, $childTotal);
                                }

                                $itemTotal = $selfCount + $childTotal;
                                $total += $itemTotal;

                                $notifDot = $itemTotal > 0 ? '<div class="rounded-circle notif-dot"></div>' : '';

                                if (!empty($item['children'])) {
                                    // Parent menu with collapse
                                    $collapseId = strtolower(str_replace(" ", "-", $item['label'])) . "-collapse-" . $item['id'];
                                    $html .= '<li class="nav-item">';
                                    $html .= '<a class="nav-link d-flex align-items-center rounded parent" data-bs-toggle="collapse" data-bs-target="#' . $collapseId . '" aria-expanded="false" title="' . htmlspecialchars($item['label']) . '" data-bs-placement="right">';
                                    if (!empty($item['icon'])) {
                                        $html .= '<div class="text-center"><i class="' . htmlspecialchars($item['icon']) . '"></i>' . $notifDot . '</div>';
                                    }
                                    $html .= '<div class="text-start m-2 module-name"><span>' . htmlspecialchars($item['label']) . '</span></div>';
                                    $html .= '<div class="text-end"><i class="fa fa-angle-down dropdown-icon"></i></div>';
                                    $html .= '</a>';
                                    $html .= '<div class="collapse" id="' . $collapseId . '" data-bs-parent="#module-list">';
                                    $html .= '<ul class="list-unstyled">' . $childHtml . '</ul></div></li>';
                                } else {
                                    // Leaf menu
                                    $classes = ($item['type'] === "MAIN") ? "nav-link d-flex align-items-center rounded main-menu" : "nav-link d-flex align-items-center rounded sub-menu";
                                    $html .= '<li><a href="session.php?page=' . strtolower($item['label']) . '" class="' . $classes . '" aria-expanded="false" title="' . htmlspecialchars($item['label']) . '" data-bs-placement="right">';
                                    if (!empty($item['icon'])) {
                                        $html .= '<div class="text-start">' . $notifDot . '<i class="' . htmlspecialchars($item['icon']) . '"></i></div>';
                                    }
                                    $html .= '<span class="ms-3 me-2">' . htmlspecialchars($item['label']) . '</span>';
                                    $html .= '</a></li>';
                                }
                            }

                            $totalNotifications = $total;
                            return $html;
                        };

                        $totalNotifications = 0;
                        $html = $renderMenuRecursive($tree, $notifications, $totalNotifications);

                        return [
                            'html' => $html,
                            'totalNotifications' => $totalNotifications
                        ];
                    }

                    $emp_id = isset($_SESSION['EMPLOYEE']['ID']) ? intval($_SESSION['EMPLOYEE']['ID']) : null;

                    $queryList = [
                        "grades submission" => [
                            "query" => "SELECT COUNT(*) 
                                        FROM `schoolenrollmentsubjectoffered` off

                                        LEFT JOIN `schoolstudentacademicrecord` rec
                                        ON off.`SchlEnrollSubjOffSms_ID` = rec.`SchlEnrollSubjOff_ID`

                                        WHERE off.`SchlEnrollSubjOff_STATUS` = 1
                                        AND off.`SchlEnrollSubjOff_ISACTIVE` = 1
                                        AND rec.`SchlStudAcadRec_STATUS` = 1
                                        AND rec.`SchlStudAcadRec_ISACTIVE` = 1
                                        AND (rec.`SchlStudAcadRec_REQ_STATUS` = 1 OR rec.`SchlStudAcadRec_REQ_STATUS` = 6)
                                        AND (rec.`SchlSign_ID` = ? AND rec.`SchlSign_UserID` = ?)
                                        ",
                            "data_types" => "ii",
                            "param" => [$emp_id, $emp_id]
                        ],
                        "class list" => [
                            "query" => "SELECT COUNT(*) 
                                        FROM `schoolenrollmentsubjectoffered` off

                                        LEFT JOIN `schoolstudentacademicrecord` rec
                                        ON off.`SchlEnrollSubjOffSms_ID` = rec.`SchlEnrollSubjOff_ID`

                                        WHERE rec.`SchlStudAcadRec_STATUS` = 1
                                        AND rec.`SchlStudAcadRec_ISACTIVE` = 1
                                        AND off.`SchlEnrollSubjOff_STATUS` = 1
                                        AND off.`SchlEnrollSubjOff_ISACTIVE` = 1
                                        AND off.`SchlProf_ID` = ?
                                        AND (rec.`SchlStudAcadRec_REQ_STATUS` = 0 OR rec.`SchlStudAcadRec_REQ_STATUS` = 2 OR rec.`SchlStudAcadRec_REQ_STATUS` = 7)
                                        ",
                            "data_types" => "i",
                            "param" => [$emp_id]
                        ]
                    ];

                    $menuarray = [];
                    $params = [];         // For all bound parameters
                    $dataTypes = "";      // For mysqli bind_param
                    $fetchNotif = "SELECT ";

                    // Loop through ACCESS_RIGHTS and dynamically build subqueries
                    // foreach (explode('; ', $_SESSION['ACCESS_RIGHTS']) as $menu) {
                    //     $menuinfo = explode(',', $menu);

                    //     if (in_array('CHILD', $menuinfo)) {
                    //         $name = strtolower(trim($menuinfo[1]));

                    //         if (array_key_exists($name, $queryList)) {
                    //             $q = $queryList[$name];
                    //             $menuarray[] = "( " . $q['query'] . " ) AS `$name`";

                    //             // Add parameter data types & values
                    //             $dataTypes .= $q['data_types'];
                    //             foreach ($q['param'] as $p) {
                    //                 $params[] = $p;
                    //             }
                    //         }
                    //     }
                    // }

                    // Finalize query
                    if (!empty($menuarray)) {
                        $fetchNotif .= implode(', ', $menuarray);
                    } else {
                        $fetchNotif .= "1 AS dummy"; // fallback
                    }

                    // Prepare statement
                    $stmt = $dbConn->prepare($fetchNotif);

                    // Bind parameters dynamically if we have any
                    if (!empty($params)) {
                        $stmt->bind_param($dataTypes, ...$params);
                    }

                    // Execute
                    // $stmt->execute();
                    // $result = $stmt->get_result();
                    // $row = $result->fetch_assoc();

                    // // Convert to notifications array
                    // $notifications = array_map('intval', $row);
                    $notifications = 0;
                    // $stmt->close();

                    // Example usage:
                    // Build menu
                    $menu = buildMenu($mergedAccessRights, $notifications);

                    // Output menu HTML
                    echo $menu['html'];

                    // Total notifications
                    // $totalNotifications = $menu['totalNotifications'];
                    // echo "<!-- Total Notifications: $totalNotifications -->";
                ?>

                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center rounded main-menu parent" data-bs-toggle="collapse" data-bs-target="#user-collapse" aria-expanded="false" title="My account" data-bs-placement="right">
                        <div class="text-center"><i class="fa-regular fa-user"></i></div>
                        <div class="text-start m-2 module-name"><span>My Account</span></div>
                        <div class="text-end"><i class="fa fa-angle-down dropdown-icon"></i></div>
                    </a>
                    <div class="collapse" id="user-collapse" data-bs-parent="#module-list">
                        <ul class="list-unstyled">
                            <li><a href="session.php?page=profile" class="nav-link rounded sub-menu"><span class="ms-3 me-2">Profile</span></a></li>
                            <!-- <li><a href="session.php?page=account settings" class="nav-link rounded sub-menu"><span>Account Settings</span></a></li> -->
                        </ul>
                    </div>
                </li>
            </ul>
        </form>
    </div>

    <div id="navLogoutContainer">
        <ul class="nav nav-pills flex-column">
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center rounded main-menu" href="masterpage-logout-controller.php" title="Log out" data-bs-placement="right">
                    <div class="text-center"><i class="fa fa-arrow-right-from-bracket"></i></div>
                    <div class="text-start"><span class="ms-2">Log out</span></div>
                </a>
            </li>
        </ul>
    </div>
</div>