<?php

class LoginBL
{
	public static function check_entity_exists($db, $query, $params)
	{
		try
		{
			$stmt = $db->prepare($query);

			$result = $stmt->execute($params);
		}
		catch(PDOException $ex)
		{
			die('Error: '. $ex->getMessage());
		}

		$row = $stmt->fetch();

		if($row)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public static function get_user_information($db, $username)
	{
		$query = "select username, email, location, salt, password from users where username = :username";
		$query_params = array(
			':username' => $username
		);

		$stmt = $db->prepare($query);
		$result = $stmt->execute($query_params);

		$row = $stmt->fetch();

		$user_info = array(
			'salt' => $row['salt'],
			'password' => $row['password'],
			'location' => $row['location'],
			'email' => $row['email'],
			'username' => $row['username']
		);

		return $user_info;
	}

	public static function generate_salt()
	{
		$salt = dechex(mt_rand(0, 2147483647)) . dechex(mt_rand(0, 2147483647));

		return $salt;
	}

	public static function compute_password_hash($password, $salt)
	{
		$password = hash('sha256', $password.$salt);

		//hash 65536 more times...
		for($round = 0; $round<65536; $round++)
		{
			$password = hash('sha256', $password.$salt);
		}
		
		return $password;
	}
}