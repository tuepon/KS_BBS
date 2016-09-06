<?php

namespace KSBBS;

/**
 * BSS.class.php
 *
 * @author Kosuke Shibuya <kosuke@jlamp.net>
 * @since 2016/09/05
 */
class BBS
{

	/**
	 * get sreads
	 */
	public static function get($order = 'new', $id = null)
	{
		DB::createTable();

		$q = filter_input(INPUT_GET, 'q');

		$arr = [];

		$sql = "SELECT ";
		$sql .= "m.id";
		$sql .= ", m.parent_id";
		$sql .= ", m.username";
		$sql .= ", m.title";
		$sql .= ", m.comment";
		$sql .= ", m.create_at";
		$sql .= ", m.update_at";
		$sql .= ", m.delete_flag";
		$sql .= ", ifnull(max(m.update_at, s.update_at), m.update_at) as display_order";
		$sql .= ", count(s.id) as comment_count";
		$sql .= " FROM ksbbs m ";
		$sql .= "LEFT JOIN ksbbs s ON m.id = s.parent_id ";
		$sql .= "WHERE m.parent_id = 0 ";
		$sql .= "AND m.delete_flag = 0 ";

		if (!is_null($id)) {
			$sql .= "AND m.comment = :id ";
			$arr[':id'] = $id;
		}
		if (!is_null($q)) {
			$sql .= "AND (m.comment LIKE :q OR m.title LIKE :q OR s.comment LIKE :q OR s.title LIKE :q) ";
			$arr[':q'] = sprintf('%%%s%%', $q);
		}

		$sql .= "GROUP BY m.id ";

		// ソート
		if ($order == 'comment') {
			$sql .= "ORDER BY comment_count DESC";
		} else if ($order == 'new') {
			$sql .= "ORDER BY display_order DESC";
		} else {
			$sql .= "ORDER BY display_order DESC";
		}

		$rs = DB::select($sql, $arr, 10);

		foreach ($rs as $i => $r) {
			$parent_id = $r['id'];
			$sql_child = "SELECT * FROM ksbbs ";
			$sql_child .= "WHERE parent_id = ? ";
			$sql_child .= "ORDER BY update_at ASC";
			$rs_child = DB::select($sql_child, [$parent_id], -1);
			$rs[$i]['reply'] = $rs_child;
		}

		return $rs;
	}

	/**
	 * regist new Thread
	 */
	public static function newThread()
	{
		if (null == filter_input_array(INPUT_POST)) {
			return;
		}
		if (!CSRF::check()) {
			return ['errors' => ['csrf_token' => false]];
		}

		$validate = self::validate(__FUNCTION__);
		if (TRUE !== $validate) {
			return ['errors' => $validate];
		}

		$res = self::insert(0);
		if ($res) {
			return ['success' => true];
		}
	}

	/**
	 * reply to thread
	 */
	public static function reply()
	{
		if (null == filter_input_array(INPUT_POST)) {
			return;
		}

		if (!CSRF::check()) {
			return ['errors' => ['csrf_token' => false]];
		}

		$validate = self::validate(__FUNCTION__);
		if (TRUE !== $validate) {
			return ['errors' => $validate];
		}

		$parent_id = filter_input(INPUT_GET, 'id');
		$res = self::insert($parent_id);
		if ($res) {
			return ['success' => true];
		}
	}

	/**
	 * validate input data
	 */
	private static function validate($function)
	{
		$username = filter_input(INPUT_POST, 'username');
		$title = filter_input(INPUT_POST, 'title');
		$comment = filter_input(INPUT_POST, 'comment');

		$err = [];
		if (mb_strlen(trim($username)) == 0) {
			$err['username'] = 'お名前は入力必須です。';
		}

		if ($function == 'newThread') {
			if (mb_strlen(trim($title)) > 64 || mb_strlen(trim($title)) == 0) {
				$err['title'] = 'タイトルは入力必須（64文字以下）です。';
			}
		}

		if (mb_strlen(trim($comment)) > 1000 || mb_strlen(trim($comment)) == 0) {
			$err['comment'] = ' 内容は入力必須（1,000文字以下）です。';
		}

		if (0 < count($err)) {
			return $err;
		}

		return true;
	}

	private static function insert($parent_id)
	{
		$sql = "INSERT INTO ksbbs (";
		$sql .= "id";
		$sql .= ", parent_id";
		$sql .= ", username";
		$sql .= ", title";
		$sql .= ", comment";
		$sql .= ", create_at";
		$sql .= ", update_at";
		$sql .= ", delete_flag";
		$sql .= ") VALUES (";
		$sql .= "NULL";
		$sql .= ", :parent_id";
		$sql .= ", :username";
		$sql .= ", :title";
		$sql .= ", :comment";
		$sql .= ", :create_at";
		$sql .= ", :update_at";
		$sql .= ", 0";
		$sql .= ")";

		$now = (new \DateTime())->format('Y-m-d H:i:s');

		$arr = [];
		$arr[':parent_id'] = $parent_id;
		$arr[':username'] = filter_input(INPUT_POST, 'username');
		$arr[':title'] = filter_input(INPUT_POST, 'title');
		$arr[':comment'] = filter_input(INPUT_POST, 'comment');
		$arr[':create_at'] = $now;
		$arr[':update_at'] = $now;

		return DB::insert($sql, $arr);
	}

}
