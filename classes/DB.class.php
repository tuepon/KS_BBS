<?php

namespace KSBBS;

/**
 * DB.class.php
 *
 * @author Kosuke Shibuya <kosuke@jlamp.net>
 * @since 2016/09/05
 */
class DB
{

	/**
	 * DSN
	 * @var string
	 */
	private static $dsn = 'sqlite:bbsdata/ksbbs.sqlite3';

	/**
	 * instance
	 * @var \PDO
	 */
	private static $instance = null;

	/**
	 * Get Instance PDO Object
	 * @return \PDO
	 */
	public static function getInstance()
	{
		if (is_null(self::$instance)) {
			self::$instance = new \PDO(self::$dsn);
			self::$instance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			self::$instance->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
		}
		return self::$instance;
	}

	/**
	 * execute select query
	 * @param string $sql
	 * @param array $arr
	 */
	public static function select($sql, array $arr = [], $limit = -1)
	{
		$dbh = self::getInstance();
		$sql = $sql . sprintf(' LIMIT %d, %d', 0, $limit);
		$stmt = $dbh->prepare($sql);
		$stmt->execute($arr);
		return $stmt->fetchAll();
	}

	/**
	 * execute update query
	 * @param string $sql
	 * @param array $arr
	 * @return bool
	 */
	public static function update($sql, array $arr = [])
	{
		$dbh = self::getInstance();
		$stmt = $dbh->prepare($sql);
		return $stmt->execute($arr);
	}

	/**
	 * execute insert query
	 * @param string $sql
	 * @param array $arr
	 * @return int lastInsertid
	 */
	public static function insert($sql, array $arr = [])
	{
		$dbh = self::getInstance();
		$stmt = $dbh->prepare($sql);
		$stmt->execute($arr);
		return $dbh->lastInsertId();
	}

	/**
	 * transaction
	 * @return void
	 */
	public static function transaction()
	{
		$dbh = self::getInstance();
		$dbh->beginTransaction();
	}

	/**
	 * commit
	 * @return void
	 */
	public static function commit()
	{
		$dbh = self::getInstance();
		$dbh->commit();
	}

	/**
	 * rollback
	 * @return void
	 */
	public static function rollback()
	{
		$dbh = self::getInstance();
		$dbh->rollBack();
	}

	public static function createTable()
	{
		$sql = "CREATE TABLE IF NOT EXISTS ksbbs ";
		$sql .= "(";
		$sql .= "id integer NOT NULL";
		$sql .= ", parent_id integer DEFAULT 0";
		$sql .= ", username varchar(255)";
		$sql .= ", title varchar(64)";
		$sql .= ", comment varchar(1000)";
		$sql .= ", create_at datetime";
		$sql .= ", update_at datetime";
		$sql .= ", delete_flag integer DEFAULT 1 ";
		$sql .= ", CONSTRAINT account_pky PRIMARY KEY (id)";
		$sql .= ")";
		$dbh = self::getInstance();
		$stmt = $dbh->prepare($sql);
		$stmt->execute([]);
	}

}
