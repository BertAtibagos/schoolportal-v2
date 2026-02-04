<?php
session_start();
$sessionId = session_id();
?>
<a href="../../onlineregistration/online-campus/login.php?PHPSESSID=<?= $sessionId?>"> click me!</a>
