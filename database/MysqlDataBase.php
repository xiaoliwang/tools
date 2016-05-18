<?php
namespace tomcao\tools\database;

use tomcao\tools\exception\SqlException;

class MysqlDataBase
{
	protected $dbh;

	public function __construct(array $config = [])
	{
		$config = $config + parse_ini_file(__DIR__ .'/../config/db.ini', true)['mysql'];
		try {
			$username = $config['username'];
			$password = $config['password'];
			unset($config['username']);
			unset($config['password']);
			$dsn = 'mysql:' . http_build_query($config, '', ';');
			$this->dbh = new \PDO($dsn, $username, $password, [
				\PDO::ATTR_PERSISTENT => true,
				\PDO::ATTR_TIMEOUT => 30
			]);
		} catch(\PDOException $e) {
			echo 'Connection failed: '. $e->getMessage();
		}
	}

	public function __call(string $func, array $args)
	{
		return call_user_func_array([&$this->dbh, $func], $args);
	}

	public function insertMultipleRows(string $table, array $fields, array $values)
	{
		$fields_count = count($fields);
		$values_count = count($values);
		$fields_str = '(' . implode(',', $fields) . ')';
		$value_str = '(' . $this->placeHolders('?', $fields_count) . ')';
		$values_str = $this->placeHolders($value_str, $values_count);
		$sql = "insert into $table $fields_str values $values_str";
		$stmt = $this->dbh->prepare($sql);
		$i = 0;
		foreach ($values as $value) {
			foreach ($value as $column) {
				$stmt->bindValue(++$i, $column);
			}
		}
		if (!$stmt->execute()) {
			throw new SqlException($stmt->errorInfo());
		}
	}

	private function placeHolders(string $text, Int $count = 0, string $separator = ','): string
	{
		return implode($separator, array_fill(0, $count, $text));
	}

	public function __destruct()
	{
		$this->dsn = null;
	}
}