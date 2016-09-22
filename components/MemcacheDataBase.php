<?php
namespace tomcao\tools\components;

use stdClass;
use Exception;

class MemcacheDataBase
{
	protected $memcache;

	public function __construct(array $config = [])
	{
		$config = $config + parse_ini_file(__DIR__ . '/../config/db.ini', true)['memcache'];
		try {
			$host = $config['host'];
			$port = $config['port'];
			$username = $config['username'];
			$password = $config['password'];
			$memcache = new MemcacheSASL;
			$memcache->addServer($host, $port);
			if ($username && $password) {
				$memcache->setSaslAuthData($username, $password);
			}

			$this->memcache = $memcache;
		} catch (\Exception $e) {
			echo 'Connection failed:' . $e->getMessage();
		}
	}

	public function __call(string $func, array $args)
	{
		return call_user_func_array([&$this->memcache, $func], $args);
	}
}