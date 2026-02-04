<?php 
	session_start();

	if(isset($_SESSION['user_id']))
	{	
		header('location: php/partials/main-dashboard.php');
		exit();
	}	
?>

<!doctype html>
<html lang="en">
	<head>
		<!-- Required meta tags -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<link href="https://fonts.googleapis.com/css?family=Roboto:300,400&display=swap" rel="stylesheet">

		<link rel="stylesheet" href="fonts/icomoon/style.css">
		<link rel="stylesheet" href="fonts/material-icon/css/material-design-iconic-font.min.css">
		<link rel="stylesheet" href="style/owl.carousel.min.css">

		<!-- BOOTSTRAP CSS -->
		<link rel="stylesheet" href="style/bootstrap.min.css">
		
		<!-- STYLE -->
		<link rel="stylesheet" href="style/style.css">
		<link rel="icon" href="image/fcpc_logo.ico">

		<!-- JS -->

		<link href="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"/>

		<title>ESS LOGIN PAGE</title>
	</head>
	<body>
	
		<div class="content">
			<div class="container">
				<div class="row">
				<div class="col-md-6">
					<img src="image/FCPC LOGO.jpg" style="border-radius: 100%;"	alt="Image" class="img-fluid" style="">
				</div>
				<div class="col-md-6 contents">
					<div class="row justify-content-center">
						<div class="col-md-8">
							<div class="mb-4">
								<h3> FIRST CITY PROVIDENTIAL COLLEGE</h3>
					 			<br>
								<h4> <i> ESS MANAGEMENT LOGIN PAGE </i> </h4>
							</div>
							<form method="POST" action="login.php" id="login-form">

								<div id="message" style="color: red; font-style: italic;text-align: center;padding: 10 10 10 10;"></div>
								<div class="statusMsg" align="center" style="font-size: 16px;font-style: normal;"></div>

								<div class="form-group first">

									<label for="username"><i class="zmdi zmdi-account material-icons-name"></i> USERNAME</label>
									<input type="text" class="form-control" name="username" id="username" required="_required"/>

								</div>
								<div class="form-group last mb-4">
									<label for="password"><i class="zmdi zmdi-lock"></i> PASSWORD</label>
									<input type="password" class="form-control" name="password" id="password" required="_required"/>
								</div>
									
								<div class="d-flex mb-5 align-items-center">
									<label class="control control--checkbox mb-0" hidden><span class="caption">Remember me</span>
										<input type="checkbox" checked="checked"/>
										<div class="control__indicator"></div>
									</label>
									<span class="ml-auto" hidden><a href="#" class="forgot-pass">Forgot Password</a></span> 
								</div>

								<input type="button" name="btnlogin" id="btnlogin" class="btn btn-block btn-primary" value="LOGIN"/>

								<div class="social-login" hidden>
									<a href="#" class="facebook">
										<span class="icon-facebook mr-3"></span> 
									</a>
									<a href="#" class="twitter">
										<span class="icon-twitter mr-3"></span> 
									</a>
									<a href="#" class="google">
										<span class="icon-google mr-3"></span> 
									</a>
								</div>
							</form>
							<br>
							<?php 
								include 'footer.html'; 
							?>
						</div>
					</div>
					
				</div>
				
				</div>
			</div>
		</div>
		
		<script src="js/jquery-3.3.1.min.js"></script>
		<script src="js/popper.min.js"></script>
		<!-- <script src="js/bootstrap.min.js"></script> -->
		<script src="js/main.js"></script>
		<script src="js/login-script.js"></script>

		</body>
</html>