
<?php require_once("./includes/config.php");?>
<?php
	if(isset($_POST['usernamecheck']))
	{
		$uname = sanitizeString($_POST['usernamecheck']);
		$q = getDBconn()->prepare("SELECT Count(username) FROM users WHERE username='$uname'");
		
		if($q->execute())
			echo $q->fetchColumn();
		else
			echo 'Err';

		unset($_POST['usernamecheck']);
		exit;
	}
	
?>



<!DOCTYPE HTML>
<html>
	<head>
		<title>Intragram-SignUp</title>
		<?php require_once("./includes/includers.php"); ?>

	</head>
	<body>
		<?php require_once("./includes/header.php");?>
		

		<?php
		if(isset($_SESSION['user'])){
			header('location: index.php');
		}
		else{
		?>
		<section class="forms">
			<br /><br /><br /><br /><br />

			<?php
			$signupResponse = ''; 
			if(isset($_POST['signup_submit'])){
				unset($_POST['signup_submit']);                
				
				$name = sanitizeString($_POST['signupName']);
				$username = str_replace(' ','', sanitizeString($_POST['signupUsername']));
			    $email = sanitizeString($_POST['signupEmail']);
			    $password = md5(sanitizeString($_POST['signupPassword']));
			    $contact = sanitizeString($_POST['signupContact']);
				$about = sanitizeString($_POST['signupAbout']);

				
				$profilepic = "https://api.adorable.io/avatars/512/".sanitizeString($username);
				
				$chk_eml = getDBconn()->prepare("SELECT COUNT(email) FROM users WHERE email= :email");
				$chk_eml->execute([":email"=>$email]);

				$chk_cnct = getDBconn()->prepare("SELECT COUNT(contact) FROM users WHERE contact= :contact");
				$chk_cnct->execute([":contact"=>$contact]);

				$chk_uname = getDBconn()->prepare("SELECT COUNT(username) FROM users WHERE username= :username");
				$chk_uname->execute([":username"=>$username]);
				
				if($chk_uname->fetchColumn() != 0)
				{
					$signupResponse = '<p class="text-danger">Username Not Available.</p>';
				}
				else if($chk_eml->fetchColumn() !=0){
					$signupResponse = '<p class="text-danger">Email Id Already Taken.</p>';
			    }
			    else if($chk_cnct->fetchColumn() !=0){
					$signupResponse = '<p class="text-danger">Contact No. Already Exist.</p>';
				}
			    else{
			        $quer = "INSERT INTO users (name, username, email, password, contact, profile_pic, about) ";
			        $quer .= "VALUES (?,?,?,?,?,?,?)";
					
					$addUser = getDBconn()->prepare($quer);
					
					if($addUser->execute([$name, $username, $email, $password, $contact, $profilepic, $about]))
					{
						$user = checkUser(getDBconn(), $email, $password);
						$_SESSION['user'] = $user->fetch();
						$signupResponse = '<p class="text-success">Account Created Succesfully</p>';
						header('refresh: 1');  
					}
					else
					{
						$signupResponse = '<p class="text-danger">Unknown Error Occurred</p>';
					}
					
			    }
			    unset($quer);
			    unset($chk_eml);
				unset($chk_cnct);
				unset($uname);
			}


			?>
			<div class="container p-4 rounded form-inner">
				
				<div class="page-header text-center">
					<h1 class="heading">Sign Up</h1>
					<div class="litline mx-auto"></div>
					<br/>
				</div>
				
				<p class="text-theme">Enjoy our services by Creating an Account</p>
				<form id="signupForm" method="POST">
					
					<div class="form-group">
						<label for="signupName" class="control-label">Name :</label>
						<input type="text" name="signupName" class="form-control" placeholder="Full Name" required>
						
					</div>

					<div class="form-group">
						<label for="signupEmail" class="control-label">Email :</label>
						<input type="email" name="signupEmail" class="form-control" placeholder="Email" required>
					</div>
					
					<div class="form-group">
						<label for="signupUsername" class="control-label">Username :</label>
						<input type="text" name="signupUsername" class="form-control" pattern=".{6,}" placeholder="Think a Username" required>
						<div id="error-username" class="invalid-feedback">Username Already In Use</div>
						
					</div>


					<div class="form-group">
						<label for="signupPassword" class="control-label">Password :</label>
						<input type="password" name="signupPassword" class="form-control" placeholder="Password" required>
					</div>
					
					<div class="form-group">
						<label class="control-label" for="signupContact">Contact No. :</label>
						<input type="tel" name="signupContact" class="form-control" placeholder="Contact" required>
					</div>
					
					
					<label class="control-label" for="signupAbout">About :</label>
					<div class="form-group"><textarea name="signupAbout" wrap="hard" Placeholder="Tell Us About Yourself" initial=" " class="form-control" ></textarea></div>
					
					
					<div><?php echo $signupResponse;?></div><br />
					<div class="d-flex flex-row justify-content-between">
						<div><a class="text-info" href="./login.php">Already Have An Account</a></div><br />
						<div><button type="submit" name="signup_submit" class="btn btn-theme" style="color: #1A1A1D;">Sign up</button></div>
					</div>
				</form>
				
				
			</div>
			<?php
			}
			?>
			<br><br>
			<br><br>
			<br><br>
		</section>
		<?php require_once("./includes/footer.php");?>
	
		<script>

		$('input[name="signupUsername"]').on('input', function(e) {
     		
			$('button[name="signup_submit"]').attr('disabled', true);	
			if($(this).val().length<6)
			{
				$(this).removeClass("is-valid").addClass("is-invalid");
				$('#error-username').html('Username should be more than six characters');
					
			}
			else{
				$.ajax({
					type: 'post',
					data: {usernamecheck: $(this).val()},
					success: (response)=>{
						if(response >= 1) 
						{
							$(this).removeClass("is-valid").addClass("is-invalid");					
							$('#error-username').html('Username Already in Use');
						}
						else if(response == 0 )
						{
							$(this).removeClass("is-invalid").addClass("is-valid");
							$('button[name="signup_submit"]').attr('disabled', false);	
						}
					}
					
				});
			}
		});

		
	</script>
	
	</body>


	


</html>