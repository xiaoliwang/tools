<?php
namespace tomcao\tools\util;

class Date
{
	private $month = [
		'January', 'February', 'March', 'April',
		'May', 'June', 'July', 'August',
		'September', 'October', 'November', 'December'
	];



	public function test()
	{
		echo date('Y-m-d H:i:s', strtotime('last month midnight')) ;
	}
}

$date = new Date();
$date->test();