<section>
	<?php 
		session_start();
		echo "<input type='hidden' id='userid' value='".$_SESSION['SYSUSERSMSID']."'/>";
		echo "<input type='hidden' id='usertype' value='".$_SESSION['USERTYPE']."'/>";
		echo "<input type='hidden' id='usertype' value='".$_SESSION['USERID']."'/>";
	?>
	<div class="container" style="width: 40%; margin-top: 1rem;">
		<div class='form-control' style="border: darkgray solid 1px; padding: 20px; box-shadow: 5px 5px 10px #80808050;">
			<form id='myform'>
				<div style="width:100%; background-image: linear-gradient(to left, #0d6efd 1%, rgb(13 110 253 / 25%) 90%); border-radius: 5px; border: #8888ff solid 1px;
						text-align: center;">
					<h2>Change Password</h2>
				</div>

				<hr>

				<div id="messcont" style="margin-bottom: 1rem;">
					<div id="errormessage">
					</div>
				</div>

				<div class="form-group first form-floating mb-1 " style="margin-top: 10px">
					<label for="password" style="margin-top : -10px"><i class="zmdi zmdi-lock"></i> Old Password</label>
					<input type="password" class="form-control" name="old_pass" id="old_pass" required="_required"/>   
				</div>

				<div class="form-group first form-floating mb-1 " style="margin-top: 10px">
					<label for="password" style="margin-top : -10px"><i class="zmdi zmdi-lock"></i> New Password</label>
					<input type="password" class="form-control" name="new_pass" id="new_pass" required="_required"/>    
				</div>

				<div class="form-group first form-floating mb-1 " style="margin-top: 10px">
					<label for="password" style="margin-top : -10px"><i class="zmdi zmdi-lock"></i> Repeat New Password</label>
					<input type="password" class="form-control" name="conf_new_pass" id="conf_new_pass" required="_required"/>    
				</div>
				
				<div class="" style="padding-inline: 5px; margin-top: 1rem;">
					<input type='button' class="btn btn-block btn-primary" id='btnChange' name='btnChange' value='Change' style='width: 49%; margin-right: 2%;'>
					<input type='button' class="btn btn-block btn-primary" id='btnCancel' name='btnCancel' value='Cancel' style='width: 49%; float: right'>
				</div>
			</form>
		</div>
	</div>
</section>

<script>
$(document).ready(function(){
	$('#btnChange').click(function(){
			$("#errormessage").hide();

			var old_pass = $('#old_pass').val().trim();
			var new_pass = $('#new_pass').val().trim();
			var conf_new_pass = $('#conf_new_pass').val().trim();
			var userid = $('#userid').val().trim();
			var usertype = $('#usertype').val().trim();

			if (old_pass == '' || new_pass == '' || conf_new_pass == ''){
				$("#errormessage").show();
				$("#messcont").css("border", "#ff7171 solid 3px");
				$("#messcont").css("padding", "5px");
				$("#messcont").css("border-radius", "10px");
				$("#messcont").css("background-color", "#ff505030")
				
				if (old_pass == ''){
					$("#errormessage").html('Old Password is a required field!');
				} else if (new_pass == ''){
					$("#errormessage").html('New Password is a required field!');
				} else if (conf_new_pass == ''){
					$("#errormessage").html('Confirm New Password is a required field!');
				}
				exit;
			} else if(new_pass !== conf_new_pass) {
				$("#errormessage").show();
				$("#messcont").css("border", "#ff7171 solid 3px");
				$("#messcont").css("padding", "5px");
				$("#messcont").css("border-radius", "10px");
				$("#messcont").css("background-color", "#ff505030")
				$("#errormessage").html('New Password and Confirm New Password does not match!');
				exit;
			}else{
				let isExecuted = confirm("Are you sure to change your password?");

				if(isExecuted == true){
					$.ajax({
						type: "POST",
						url: "myaccount/change-password-controller.php",
						data:{
							old_pass : old_pass,
							new_pass : new_pass,
							conf_new_pass : conf_new_pass,
							userid : userid,
							usertype : usertype
						},
						success: function(result){
							$("#errormessage").show();
							$("#errormessage").html(result);
						}
					});
				}
				
				if (isExecuted == false) {
					$("#messcont").hide();
					$('#old_pass').val('');
					$('#new_pass').val('');
					$('#conf_new_pass').val('');
				}
			}	
	});
	$('#btnCancel').click(function(){
		$("#messcont").hide();
		$('#old_pass').val('');
		$('#new_pass').val('');
		$('#conf_new_pass').val('');

		window.location.reload();

	});
});
	
</script>