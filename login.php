<?php //login.php
	
	require("login_bl.php");
	require("common.php");

	//initially blank, if login fails we can preserve their value though
	$submitted_username = '';

	$general_error = '';

	$allowed_to_continue = true;

	if(!empty($_POST))
	{
		$username = $_POST['username'];
		$password = $_POST['password'];

		$submitted_username = htmlentities($username, ENT_QUOTES, 'UTF-8');

		if(empty($username))
		{
			$allowed_to_continue = false;
			$general_error = 'Must enter username';
		}

		if(empty($password))
		{
			$allowed_to_continue = false;
			$general_error = 'Must enter password';
		}

		if($allowed_to_continue)
		{
			//first make sure user even exists
			$user_info = LoginBL::get_user_information($db, $username);
			$allowed_to_continue = ($user_info != null) ? true : false;

			$logon_success = false;

			if($allowed_to_continue)
			{
				//compute password hash
				$password_computed = LoginBL::compute_password_hash($password, $user_info['salt']);

				if($password_computed === $user_info['password'])
				{
					$logon_success = true;
				}
				else
				{
					$general_error = 'password was incorrect';
				}

				if($logon_success)
				{
					//scrub out sensitive info before storing to session variable for later use
					unset($user_info['password']);
					unset($user_info['salt']);

					$_SESSION['user_info'] = $user_info;

					header("Location: listings.php");
					die("Redirecting to: listings.php");
				}
			}
			else
			{
				$general_error = 'Username was incorrect';
			}
		}
	}

?>

<html>
<head>
	<title>CLBASE</title>
</head>
<body>
	<div id='div_login_elements'>
		<div id='div_login_title'>
			CLBASE LOGIN
		</div>
		<p>
		<div id='div_form_login'>
			<form id='form_login' action='login.php' method='post'>
				<label for='txtBox_username' id='lbl_username'>Username:</label>
				<input type='text' id='txtBox_username' name='username' value='<?php echo $submitted_username; ?>' />
				<br />
				<label for='txtBox_password' id='lbl_password'>Password:</label>
				<input type='password' id='txtBox_password' name='password' />
				<br />
				<input type='submit' value='Login' />
			</form>
			<span class='error' id='general_error'><?php echo $general_error; ?></span>
			<p>
			<a href='register.php'>Register here</a>
		</div>
	</div>
</body>
</html>