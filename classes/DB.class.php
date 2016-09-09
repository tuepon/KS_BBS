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

	const DBNAME = 'bbsdata/ksbbs.sqlite3';

	/**
	 * DSN
	 * @var string
	 */
	private static $dsn = 'sqlite:%s';

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
			self::$instance = new \PDO(sprintf(self::$dsn, self::DBNAME));
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

		$stmtCnt = $dbh->prepare($sql);
		$stmtCnt->execute($arr);
		$rows = $stmtCnt->fetchAll();
		$cnt = count($rows);

		if (0 < $limit) {
			$page = filter_input(INPUT_GET, 'page');
			$start = ($page == 0) ? 1 : ($page - 1) * 5;
			$sql = $sql . sprintf(' LIMIT %d, %d', $start, $limit);
		}

		$stmt = $dbh->prepare($sql);
		$stmt->execute($arr);
		return ['count' => $cnt, 'rows' => $rows];
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

	/**
	 * テーブルを作成する
	 */
	public static function createTable()
	{
		$sqlbbs = "CREATE TABLE IF NOT EXISTS ksbbs ";
		$sqlbbs .= "(";
		$sqlbbs .= "id integer NOT NULL";
		$sqlbbs .= ", parent_id integer DEFAULT 0";
		$sqlbbs .= ", delete_key varchar(8)";
		$sqlbbs .= ", username varchar(255)";
		$sqlbbs .= ", title varchar(64)";
		$sqlbbs .= ", comment varchar(1000)";
		$sqlbbs .= ", create_at datetime";
		$sqlbbs .= ", update_at datetime";
		$sqlbbs .= ", delete_flag integer DEFAULT 1 ";
		$sqlbbs .= ", CONSTRAINT account_pky PRIMARY KEY (id)";
		$sqlbbs .= ")";

		$sqlimg = "CREATE TABLE IF NOT EXISTS ksimg ";
		$sqlimg .= "(";
		$sqlimg .= "id integer NOT NULL";
		$sqlimg .= ", post_id integer DEFAULT 0";
		$sqlimg .= ", filename varchar(255)";
		$sqlimg .= ", create_at datetime";
		$sqlimg .= ", update_at datetime";
		$sqlimg .= ", delete_flag integer DEFAULT 1 ";
		$sqlimg .= ", CONSTRAINT account_pky PRIMARY KEY (id)";
		$sqlimg .= ")";

		$dbh = self::getInstance();

		foreach ([$sqlbbs, $sqlimg] as $sql) {
			$stmt = $dbh->prepare($sql);
			$stmt->execute();
		}
	}

}
