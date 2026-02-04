$(document).ready(function() {


	$("#message").hide();

	$('#username').keypress(function(event){
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if(keycode == '13'){
		   //alert('You pressed a "enter" key in textbox, here submit your form'); 
		   $("#btnlogin").click()
		}
	});

	$('#password').keypress(function(event){
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if(keycode == '13'){
		   //alert('You pressed a "enter" key in textbox, here submit your form'); 
		   $("#btnlogin").click()
		}
	});


	$("#btnlogin").click(function(){

		var uname = sanitizeInput($("#username").val().trim());
		var upass = sanitizeInput($("#password").val().trim());

		if (uname == ''){
			// $("#message").show()
			// $("#username").focus()
			// $("#message").html('Type your Username!')

			$('.statusMsg p').remove();
			$('.statusMsg').html('<p class="alert alert-danger"> Please Enter Your Username. </p>');
			$(".statusMsg p").focus();
		};

		if (upass == ''){

			// $("#message").show()
			// $("#password").focus()
			// $("#message").html('Type your Password!')

			$('.statusMsg p').remove();
			$('.statusMsg').html('<p class="alert alert-danger"> Please Enter Your Password. </p>');
			$(".statusMsg p").focus();
		};

		if( uname != '' && upass != ''){

			$.ajax({
				type: 'POST',
				url: 'php/controller/LoginController.php',
				data: 	
				{
					uname:uname,
					upass:upass
				},

				beforeSend: function(){

					disableControlByClassName('btnlogin');
					$("#loading_registration").fadeIn(); // ***** SHOW THE DIV FOR THE LOADING ANIMATION UPON GETTING THE STUDENTS LIST
					$('.statusMsg').html('');


				},
				success: function(response)
				{
					var login = JSON.parse(response)

					console.log(login);
						
					$(document.body).css({ 'cursor': 'auto' })

					if(login.status == 0) {// WRONG EMAIL OR PASSWORD
					 
						var message = 	'<div class="danger alert">'+
										'<div class="content">'+
											'<div class="icon">'+
												'<svg height="50" viewBox="0 0 512 512" width="50" xmlns="http://www.w3.org/2000/svg"><path fill="#fff" d="M449.07,399.08,278.64,82.58c-12.08-22.44-44.26-22.44-56.35,0L51.87,399.08A32,32,0,0,0,80,446.25H420.89A32,32,0,0,0,449.07,399.08Zm-198.6-1.83a20,20,0,1,1,20-20A20,20,0,0,1,250.47,397.25ZM272.19,196.1l-5.74,122a16,16,0,0,1-32,0l-5.74-121.95v0a21.73,21.73,0,0,1,21.5-22.69h.21a21.74,21.74,0,0,1,21.73,22.7Z"/></svg>'+
											'</div>'+
											'<p> ' + login.message + ' </p>'+
										'</div>'+
									'</div>';

						enableControlByClassName('btnlogin');
						$("#loading_registration").fadeOut(); // ***** SHOW THE DIV FOR THE LOADING ANIMATION UPON GETTING THE STUDENTS LIST
						$('.statusMsg p').remove();
						$('.statusMsg').html(message);
						$(".statusMsg p").focus();


					} else if(login.status == 1) {	

						var message = 	'<div class="success alert">'+
											'<div class="content">'+
												'<div class="icon">'+
													'<svg width="50" height="50" id="Layer_1" style="enable-background:new 0 0 128 128;" version="1.1" viewBox="0 0 128 128" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g><circle fill="#fff" cx="64" cy="64" r="64"/></g><g><path fill="#3EBD61" d="M54.3,97.2L24.8,67.7c-0.4-0.4-0.4-1,0-1.4l8.5-8.5c0.4-0.4,1-0.4,1.4,0L55,78.1l38.2-38.2   c0.4-0.4,1-0.4,1.4,0l8.5,8.5c0.4,0.4,0.4,1,0,1.4L55.7,97.2C55.3,97.6,54.7,97.6,54.3,97.2z"/></g></svg>'+
												'</div>'+
												'<p> ' + login.message + ' Redirecting ... </p>'+
											'</div>'+
										'</div>';
			
						$("#loading_registration").fadeOut(); // ***** SHOW THE DIV FOR THE LOADING ANIMATION UPON GETTING THE STUDENTS LIST
						$('.statusMsg p').remove();
						$('.statusMsg').html(message);
						$(".statusMsg p").focus();
						delayedRedirect();
					
					} else {

						var message = 	'<div class="danger alert">'+
										'<div class="content">'+
											'<div class="icon">'+
												'<svg height="50" viewBox="0 0 512 512" width="50" xmlns="http://www.w3.org/2000/svg"><path fill="#fff" d="M449.07,399.08,278.64,82.58c-12.08-22.44-44.26-22.44-56.35,0L51.87,399.08A32,32,0,0,0,80,446.25H420.89A32,32,0,0,0,449.07,399.08Zm-198.6-1.83a20,20,0,1,1,20-20A20,20,0,0,1,250.47,397.25ZM272.19,196.1l-5.74,122a16,16,0,0,1-32,0l-5.74-121.95v0a21.73,21.73,0,0,1,21.5-22.69h.21a21.74,21.74,0,0,1,21.73,22.7Z"/></svg>'+
											'</div>'+
											'<p> ' + login.message + ' </p>'+
										'</div>'+
									'</div>';

						enableControlByClassName('btnlogin');
						$("#loading_registration").fadeOut(); // ***** SHOW THE DIV FOR THE LOADING ANIMATION UPON GETTING THE STUDENTS LIST
						$('.statusMsg p').remove();
						$('.statusMsg').html(message);
						$(".statusMsg p").focus();
					}

				}
			})

		}

	})	

})

function delayedRedirect() {
	setTimeout(function() {
		window.location.replace("php/partials/main-dashboard.php")
	}, 3000); // 3000 milliseconds = 3 seconds
}
