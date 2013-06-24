<?php //register.php

	require("common.php");
	require("login_bl.php");

	//to refill in broken submissions
	$current_username = '';
	$current_location = '';
	$current_email = '';

	$username_error = '';
	$password1_error = '';
	$password2_error = '';
	$location_error = '';
	$email_error = '';
	$general_error = '';

	//flag to tell the page something is not valid
	$should_die = false;

	if(!empty($_POST))
	{
		$username = $_POST['username'];
		$pw1 = $_POST['password1'];
		$pw2 = $_POST['password2'];
		$email = $_POST['email'];
		$location = $_POST['location'];

		$current_location = htmlentities($location, ENT_QUOTES, 'UTF-8');
		$current_username = htmlentities($username, ENT_QUOTES, 'UTF-8');
		$current_email = htmlentities($email, ENT_QUOTES, 'UTF-8');

		$allowed_to_continue = true;

		if(empty($username))
		{
			$username_error = 'Enter username';
			$allowed_to_continue = false;
		}

		if(empty($pw1))
		{
			$password1_error = 'Enter password';
			$allowed_to_continue = false;
		}

		if(empty($pw2))
		{
			$password2_error = 'Enter password';
			$allowed_to_continue = false;
		}

		if(empty($location))
		{
			$location_error = 'Enter location';
			$allowed_to_continue = false;
		}

		if(!filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			$email_error = 'Enter properly formatted email';
			$allowed_to_continue = false;
		}

		if($pw1 !== $pw2)
		{
			$password2_error = 'Must match previous password';
			$allowed_to_continue = false;
		}

		//Check the database and make sure there's no user using
		//this username already
		if($allowed_to_continue)
		{
			$password = $pw1;

			$query_user_unique = "SELECT 1 FROM users WHERE username = :username";

			$query_params = array(
				':username' => $username
			);

			$allowed_to_continue = !LoginBL::check_entity_exists($db, $query_user_unique, $query_params);
			if($allowed_to_continue)
			{
				//check email
				$query_email_unique = "SELECT 1 FROM users WHERE email = :email";
				$query_params = array(
					':email' => $email
				);

				$allowed_to_continue = !LoginBL::check_entity_exists($db, $query_email_unique, $query_params);
				if($allowed_to_continue)
				{
					//insert the user
					$query_insert_info = "
						INSERT INTO users (
							username,
							password,
							salt,
							email,
							location
						) VALUES (
							:username,
							:password,
							:salt,
							:email,
							:location
						)
					";

					$salt = LoginBL::generate_salt();

					$password = LoginBL::compute_password_hash($password, $salt);

					$query_params = array(
						':username' => $username,
						':password' => $password,
						':salt' => $salt,
						':email' => $email,
						':location' => $location
					);

					try
					{
						$stmt = $db->prepare($query_insert_info);
						$result = $stmt->execute($query_params);
					}
					catch(PDOException $ex)
					{
						die('database insertion fault: '. $ex->getMessage());
					}

					//if we got this far then tehre should be a login in the db
					header("Location: login.php");

					//call die so the rest of the script is not executed unnecessarily
					die("Redirecting to login.php");
				}
				else
				{
					$general_error = 'Email already exists';
				}
			}
			else
			{
				$general_error = 'Username already exists';
			}
		}
	}

?>

<html>
<head>
	<title>Register</title>
</head>

<body>
	<div id='div_register_title'>
		Register for CLBASE
	</div>
	<p>
	<div id='div_form_register'>
		<form id='form_register' method='post' action='register.php'>
			<label for='txtBox_username' id='lbl_username'>Username:</label>
			<input type='text' id='txtBox_username' name='username'value= '<?php echo $current_username; ?>' />
			<span class='error' id='username_error'><?php echo $username_error; ?></span>
			<br/>
			<label for='txtBox_password1' id='lbl_password1'>Password:</label>
			<input type='password' id='txtBox_password1' name='password1' />
			<span class='error' id='password1_error'><?php echo $password1_error; ?></span>
			<br />
			<label for='txtBox_password2' id='lbl_password2'>Password again:</label>
			<input type='password' id='txtBox_password2' name='password2' />
			<span class='error' id='password2_error'><?php echo $password2_error; ?></span>
			<br />
			<label for='txtBox_email' id='lbl_email'>Email:</label>
			<input type='text' id='txtBox_email' name='email' value='<?php echo $current_email; ?>'/>
			<span class='error' id='email_error'><?php echo $email_error; ?></span>
			<br />
			<label for='txtBox_location' id='lbl_location'>Location:</label>
			<input type='text' id='txtBox_location' name='location' value='<?php echo $current_location; ?>'/>
			<span class='error' id='location_error'><?php echo $location_error; ?></span>
			<br />
			<input type='submit' id='btn_submit'/>
		</form>
		<div id='div_general_error'>
			<span class='error' id='general_error'><?php echo $general_error; ?></span>
		</div>
	</div>


</body>
</html>