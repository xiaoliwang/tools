<?php
namespace tomcao\tools\database;

class RedisDataBase
{
	protected $redis;

	public function __construct(array $config = [])
	{
		$config = $config + parse_ini_file(__DIR__ . '/../config/db.ini', true)['redis'];
		try {
			$host = $config['host'];
			$port = $config['port'] ?? 6379;
			$auth = $config['auth'] ?? '';
			$this->redis = new \Redis();
			$this->redis->connect($host, $port, 1, NULL, 100);
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

	public function __destruct()
	{
		$this->redis && $this->redis->close();
	}
}