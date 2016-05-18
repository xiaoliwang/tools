<?php
namespace tomcao\tools\exception;
class SqlException extends \Exception
{
	private $more_details;

	public function __construct($more_details, Int $code = 0, \Exception $previous = null)
	{
		$this->more_details;
		parent::__construct($more_details[2], $code, $previous);
	}

	public function getDetails()
	{
		return $this->more_details;
	}
}