<?php

class DatabaseResult
{
	private $successFlag;
	private $reason;
	//payload can be anything retrieved from the database
	private $payload;

	function DatabaseResult($successFlag, $reason)
	{
		$this->successFlag = $successFlag;
		$this->reason = $reason;
		$this->payload = null;
	}

	function GetSuccessFlag()
	{
		return $this->successFlag;
	}

	function GetReason()
	{
		return $this->reason;
	}

	function GetPayload()
	{
		return $this->payload;
	}

	function SetSuccessFlag($flag)
	{
		if($flag === true)
		{
			$this->SetReason('Execution succeeded');
		}

		$this->successFlag = $flag;
	}

	function SetReason($reason)
	{
		$this->reason = $reason;
	}

	function SetPayload($payload)
	{
		$this->payload = $payload;
	}
}


?>