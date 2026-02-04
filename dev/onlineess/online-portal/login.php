<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Login Page</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=yes">
		<link rel="icon" href="image/icons/fcpc_logo.ico"/>
		<!--===============================================================================================-->	
			<link rel="icon" type="image/png" href="images/icons/favicon.ico"/>
		<!--===============================================================================================-->
			<link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
		<!--===============================================================================================-->
			<link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
		<!--===============================================================================================-->
			<link rel="stylesheet" type="text/css" href="fonts/iconic/css/material-design-iconic-font.min.css">
		<!--===============================================================================================-->
			<!-- <link rel="stylesheet" type="text/css" href="vendor/animate/animate.css"> -->
		<!--===============================================================================================-->	
			<!-- <link rel="stylesheet" type="text/css" href="vendor/css-hamburgers/hamburgers.min.css"> -->
		<!--===============================================================================================-->
			<!-- <link rel="stylesheet" type="text/css" href="vendor/animsition/css/animsition.min.css"> -->
		<!--===============================================================================================-->
			<!-- <link rel="stylesheet" type="text/css" href="vendor/select2/select2.min.css"> -->
		<!--===============================================================================================-->	
			<!-- <link rel="stylesheet" type="text/css" href="vendor/daterangepicker/daterangepicker.css"> -->
		<!--===============================================================================================-->
			<link rel="stylesheet" type="text/css" href="css/util.css">
			<link rel="stylesheet" type="text/css" href="css/main.css">
		<!--===============================================================================================-->
			<script src="js/functions.js"></script>
		<!--===============================================================================================-->

		<style>
			.alert{
				padding: 0.55rem 0.65rem 0.55rem 0.75rem;
				border-radius:1rem;
				/* min-width:400px; */

			}
			.content{
				display: flex;
				align-items:center;
			}
			.icon{
				padding: 0.5rem;
				margin-right: 1rem;
				border-radius:39% 61% 42% 58% / 50% 51% 49% 50%;
				box-shadow:
				0px 3.2px 13.8px rgba(0, 0, 0, 0.02),
				0px 7.6px 33.3px rgba(0, 0, 0, 0.028),
				0px 14.4px 62.6px rgba(0, 0, 0, 0.035),
				0px 25.7px 111.7px rgba(0, 0, 0, 0.042),
				0px 48px 208.9px rgba(0, 0, 0, 0.05),
				0px 115px 500px rgba(0, 0, 0, 0.07)
			}
			.close{
				background-color: transparent;
				border:none;
				outline:none;
				transition:all 0.2s ease-in-out;
				padding: 0.75rem;
				border-radius:0.5rem;
				cursor:pointer;
				display: flex;
				align-items:center;
				justify-content: center;
			}
			.close:hover{
				background-color: #fff;
			}

			.success{
				background-color: rgba(62, 189, 97);
				border:2px solid #3ebd61;
			}
			.success .icon{
				background-color:#3ebd61;
			}
			.info{
				background-color: rgba(0, 108, 227);
				border:2px solid #006CE3;
			}
			.info .icon{
				background-color: #006CE3;
			}
			.warning{
				background-color: rgba(239, 148, 0);
				border:2px solid #EF9400;
			}
			.warning .icon{
				background-color: #EF9400;
			}

			.danger{
				background-color: rgba(236, 77, 43);
				border:2px solid #EF9400;
			}
			.danger .icon{
				background-color: #EC4D2B;
			}

			#loading_registration {
				display: none;
			}
		</style>

	</head>
	<body>
		
		<div class="limiter">
			<div class="container-login100" style="background-image: url('image/Sarihumpay.jpg');">
				<div class="wrap-login100">
					<form class="login100-form validate-form">
						<span class="login100-form-logo">
							<!-- <i class="zmdi zmdi-landscape"></i> -->
							<img src="image/icons/fcpc_logo.ico"  alt="Image" height="150px" width="150px">
						</span>

						<span class="login100-form-title p-b-34 p-t-27">
							<h1>First City Providential College &nbsp;&nbsp;<br></h1>
							<h3 style="color: #ffc107;font-style: oblique;">Online ESS PORTAL </h3>
						</span>

						<div class="wrap-input100 validate-input" data-validate = "Enter username">
							<input class="input100" type="text" name="username" id="username" placeholder="Username">
							<span class="focus-input100" data-placeholder="&#xf207;"></span>
						</div>

						<div class="wrap-input100 validate-input" data-validate="Enter password">
							<input class="input100"  type="password" id="password" name="password" placeholder="Password">
							<span class="focus-input100" data-placeholder="&#xf191;"></span>
						</div>

						<div class="container-login100-form-btn btnlogin">
							<button class="login100-form-btn btnlogin" id="btnlogin" name="btnlogin" type="button">
								Login
							</button>
						</div><br>

						<div class="statusMsg" align="center" style="font-size: inherit;font-style: normal;">
						</div>

						<div id="loading_registration" style='text-align:center;'>
							<img src="image/Loader7.gif" style="border-radius: 5px; " alt="Loading ... ">
						</div>
				
						<!-- <div class="text-center p-t-90">
							<a class="txt1" href="#">
								Forgot Password?
							</a>
						</div> -->
					</form>
				</div>
			</div>
		</div>
		

		<div id="dropDownSelect1"></div>
		
	<!--===============================================================================================-->
		<script src="vendor/jquery/jquery-3.2.1.min.js"></script>
	<!--===============================================================================================-->
		<!-- <script src="vendor/animsition/js/animsition.min.js"></script> -->
	<!--===============================================================================================-->
		<script src="vendor/bootstrap/js/popper.js"></script>
		<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
	<!--===============================================================================================-->
		<!-- <script src="vendor/select2/select2.min.js"></script> -->
	<!--===============================================================================================-->
		<!-- <script src="vendor/daterangepicker/moment.min.js"></script>
		<script src="vendor/daterangepicker/daterangepicker.js"></script> -->
	<!--===============================================================================================-->
		<!-- <script src="vendor/countdowntime/countdowntime.js"></script> -->
	<!--===============================================================================================-->
		<!-- <script src="js/main.js"></script> -->
		<!-- <script src="js/login-script.js"></script> -->

		<div id="script_holder"></div>
		<script>
			var currentDate = new Date();
			var dateString = currentDate.toISOString().replace(/[^0-9]/g, ''); // Remove non-numeric characters
			_string = '<script src="js/login-script.js?d=' + dateString + '"></';
			_string2 = 'script>';
			$('#script_holder').html(_string + _string2);
		</script>

	</body>
</html>