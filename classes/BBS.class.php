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

	private static $arrImageType = [
		IMAGETYPE_JPEG, IMAGETYPE_JPEG2000, IMAGETYPE_PNG
	];

	public static function all()
	{
		$sql = "SELECT ";
		$sql .= "m.id";
		$sql .= ", m.parent_id";
		$sql .= ", m.username";
		$sql .= ", m.title";
		$sql .= ", m.comment";
		$sql .= ", m.create_at";
		$sql .= ", m.update_at";
		$sql .= ", m.delete_flag";
		$sql .= " FROM ksbbs m ";
		$res = DB::select($sql, []);
		return $res;
	}

	/**
	 * get sreads
	 */
	public static function get($order = 'new', $id = null)
	{
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
		$sql .= ", m.delete_key";
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

		$res = DB::select($sql, $arr, 5);

		foreach ($res['rows'] as $i => $r) {
			$parent_id = $r['id'];
			$sql_child = "SELECT * FROM ksbbs ";
			$sql_child .= "WHERE parent_id = :parent_id ";
			$sql_child .= "ORDER BY update_at ASC";
			$rs_child = DB::select($sql_child, [':parent_id' => $parent_id], -1);
			$res['rows'][$i]['reply'] = $rs_child;
		}

		return $res;
	}

	public static function recentComment()
	{
		$sql = "SELECT ";
		$sql .= "m.id";
		$sql .= ", m.parent_id";
		$sql .= ", m.username";
		$sql .= ", ('Re:' || c.title) as title";
		$sql .= ", m.comment";
		$sql .= ", m.create_at";
		$sql .= ", m.update_at";
		$sql .= ", m.delete_flag";
		$sql .= " FROM ksbbs m ";
		$sql .= "LEFT JOIN ksbbs c ON c.id = m.parent_id ";
		$sql .= "WHERE m.parent_id > 0 ";
		$sql .= "AND m.delete_flag = 0 ";
		$sql .= "ORDER BY m.update_at DESC ";
		$sql .= "LIMIT 10 ";
		$res = DB::select($sql, []);
		return $res;
	}

	public static function getImages($postId)
	{
		$sql_img = "SELECT * FROM ksimg ";
		$sql_img .= "WHERE post_id = :post_id ";
		$sql_img .= "ORDER BY id ASC";
		$imgs = DB::select($sql_img, [':post_id' => $postId], -1);

		return $imgs['rows'];
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

		DB::transaction();

		$insertId = self::insert(0);
		if ($insertId &&
			(!BBS_FUNC_IMAGE || self::setImages($insertId, self::images()))) {
			DB::commit();
			return ['success' => true];
		}

		DB::rollback();
		return false;
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

		DB::transaction();

		$insertId = self::insert($parent_id);

		var_dump('$insertId:' . $insertId);

		if ($insertId &&
			(!BBS_FUNC_IMAGE || self::setImages($insertId, self::images()))) {
			DB::commit();
			return ['success' => true];
		}

		DB::rollback();
		return false;
	}

	/**
	 * validate input data
	 */
	private static function validate($function)
	{
		$delete_key = filter_input(INPUT_POST, 'delete_key');
		$username = filter_input(INPUT_POST, 'username');
		$title = filter_input(INPUT_POST, 'title');
		$comment = filter_input(INPUT_POST, 'comment');
		$images = (isset($_FILES['images'])) ? $_FILES['images'] : [];

		$err = [];
		if (mb_strlen(trim($username)) == 0) {
			$err['username'] = 'お名前は入力必須です。';
		}

		if (mb_strlen(trim($delete_key)) > 8 || mb_strlen(trim($delete_key)) < 4) {
			$err['delete_key'] = '削除キーは入力必須（4〜8文字）です。';
		}

		if ($function == 'newThread') {
			if (mb_strlen(trim($title)) > 64 || mb_strlen(trim($title)) == 0) {
				$err['title'] = 'タイトルは入力必須（64文字以下）です。';
			}
		}

		if (mb_strlen(trim($comment)) > 1000 || mb_strlen(trim($comment)) == 0) {
			$err['comment'] = '内容は入力必須（1,000文字以下）です。';
		}

		if (isset($images['error'])) {
			foreach ($images['error'] as $i => $errors) {
				if ($errors == UPLOAD_ERR_OK) {
					$tmp_name = $images['tmp_name'][$i];
					$arrSize = getimagesize($tmp_name);
					if (!in_array($arrSize[2], self::$arrImageType)) {
						$err['images'][$i] = '選択したファイル形式は受け付けられません。';
					}
				} else if ($errors == UPLOAD_ERR_NO_FILE) {

				} else {
					$err['images'][$i] = '選択したファイルは受け付けられません。';
				}
			}
		}

		if (0 < count($err)) {
			return $err;
		}

		return true;
	}

	private static function images()
	{
		$images = (isset($_FILES['images'])) ? $_FILES['images'] : null;

		$arrImages = [];
		if (isset($images['error'])) {
			foreach ($images['tmp_name'] as $tmp_name) {
				if (!empty($tmp_name) && file_exists($tmp_name)) {

					$arrSize = getimagesize($tmp_name);
					switch ($arrSize[2]) {
						case IMAGETYPE_PNG:
							$extension = 'png';
							break;
						case IMAGETYPE_JPEG:
						case IMAGETYPE_JPEG2000:
							$extension = 'jpg';
							break;
					}
					$new_filename = sprintf('upfiles/%s.%s', sha1_file($tmp_name), $extension);
					if (rename($tmp_name, $new_filename) &&
						IMAGE::resize($new_filename, 200)) {
						$arrImages[] = basename($new_filename);
					}
				}
			}
		}
		return $arrImages;
	}

	private static function insert($parent_id)
	{
		$sql = "INSERT INTO ksbbs (";
		$sql .= "id";
		$sql .= ", parent_id";
		$sql .= ", delete_key";
		$sql .= ", username";
		$sql .= ", title";
		$sql .= ", comment";
		$sql .= ", create_at";
		$sql .= ", update_at";
		$sql .= ", delete_flag";
		$sql .= ") VALUES (";
		$sql .= "NULL";
		$sql .= ", :parent_id";
		$sql .= ", :delete_key";
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
		$arr[':delete_key'] = filter_input(INPUT_POST, 'delete_key');
		$arr[':username'] = filter_input(INPUT_POST, 'username');
		$arr[':title'] = filter_input(INPUT_POST, 'title');
		$arr[':comment'] = filter_input(INPUT_POST, 'comment');
		$arr[':create_at'] = $now;
		$arr[':update_at'] = $now;

		return DB::insert($sql, $arr);
	}

	private static function setImages($insertId, $images = [])
	{
		if (is_null($images)) {
			return true;
		}

		$sql = 'INSERT INTO ksimg (';
		$sql .= 'id';
		$sql .= ', post_id';
		$sql .= ', filename';
		$sql .= ', create_at';
		$sql .= ', update_at';
		$sql .= ', delete_flag';
		$sql .= ') VALUES (';
		$sql .= 'NULL';
		$sql .= ', :post_id';
		$sql .= ', :filename';
		$sql .= ', :create_at';
		$sql .= ', :update_at';
		$sql .= ', 0';
		$sql .= ')';

		$now = (new \DateTime())->format('Y-m-d H:i:s');

		foreach ($images as $path) {
			$arr = [];
			$arr[':post_id'] = $insertId;
			$arr[':filename'] = $path;
			$arr[':create_at'] = $now;
			$arr[':update_at'] = $now;
			if (!DB::insert($sql, $arr)) {
				return false;
			}
		}
		return true;
	}

	public static function delete($id, $delkey)
	{
		$sql = "UPDATE ksbbs ";
		$sql .= "SET ";
		$sql .= "delete_flag = 1 ";
		$sql .= "WHERE id = :id ";
		$sql .= "AND delete_key = :delkey ";
		return DB::update($sql, [':id' => $id, ':delkey' => $delkey]);
	}

}
