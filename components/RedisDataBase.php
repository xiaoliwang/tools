<?php
namespace tomcao\tools\components;

class RedisDataBase
{
	protected $redis;

	public function __construct(array $config = [])
	{
		if (!extension_loaded('redis'))
			throw new \Exception('You should add extension redis');
		$config = $config + parse_ini_file(__DIR__ . '/../config/db.ini', true)['redis'];
		try {
			$host = $config['host'];
			$port = $config['port'] ?? 6379;
			$auth = $config['auth'] ?? '';
			$timeout = $config['timeout'] ?? 1;
			$this->redis = new \Redis();
			$this->redis->pconnect($host, $port, $timeout, NULL, 100);
			$auth && $this->redis->auth($auth);
			if ($this->redis->ping() !== '+PONG') {
				throw new \Exception('password is wrong');
			}
		} catch (\Exception $e) {
			echo 'Connection failed: ' . $e->getMessage();
		}
	}

	public function __call(string $func, array $args)
	{
		return call_user_func_array([&$this->redis, $func], $args);
	}

	public function scan(&$begin, string $patten, int $count = 100)
	{
		return $this->redis->scan($begin, $patten, $count);
	}

	public function __destruct()
	{
		$this->redis && $this->redis->close();
	}
}