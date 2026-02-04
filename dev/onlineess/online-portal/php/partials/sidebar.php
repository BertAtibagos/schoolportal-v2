<header class="header" id="header">
	<div class="header_toggle"> <i class='bx bx-menu' id="header-toggle"></i> </div>
</header>
<div class="l-navbar" id="nav-bar">
	<nav class="nav">
		<div class="nav-item">
			<a href="main-dashboard.php" class="nav_logo">
				<i class='bx bx-layer nav_logo-icon'></i>
				<span class="nav_logo-name">Navigation</span>
			</a>

			<div class="nav_list">
				<a href="#" id="mnu-ess-staff" class="nav_link" hidden> 
					<i class='bx bx-group nav_icon'></i><span class="nav_name"> ESS STAFF </span> <span class="badge"><input type="text" name="staff-notif-badge" id="staff-notif-badge"></span>
				</a>
				<a href="#" id="mnu-ess-teamleader" name="mnu-ess-teamleader" class="nav_link" hidden>
					<i class='bx bx-male-female nav_icon'></i>  <span class="nav_name"> ESS TEAMLEADER </span> <span class="badge" id="teamleader-notif-badge" hidden>0</span>
				</a>
				<a href="#" id="mnu-ess-finance" name="mnu-ess-finance" class="nav_link" hidden>
					<i class='bx bx-dollar-circle nav_icon'></i><span class="nav_name"> ESS FINANCE </span> <span class="badge" id="finance-notif-badge" hidden>0</span>
				</a>
			</div>
		</div>
		<div class="nav-item">
			<a href="../controller/LogoutController.php">
				<label class="nav_link" id="btnlogout"> 
					<i class='bx bx-log-out nav_icon'></i>
					<span class="nav_name">Sign Out</span>
				</label>
			</a>
		</div>
	</nav>
</div>
