<?php

namespace KSBBS;

/**
 * CSRF.class.php
 *
 * @author Kosuke Shibuya <kosuke@jlamp.net>
 * @since 2016/09/07
 */
class CSRF
{

	private static $token = null;

	/**
	 * トークンを取得する
	 * @return string
	 */
	public static function get()
	{
		if (is_null(self::$token)) {
			self::$token = hash('sha256', uniqid());
		}

		$_SESSION['crsf_token'] = self::$token;
		return self::$token;
	}

	/**
	 * トークンをチェックする
	 * @return bool
	 */
	public static function check()
	{
		return (isset($_SESSION['crsf_token']) &&
			$_SESSION['crsf_token'] == filter_input(INPUT_POST, 'crsf_token'));
	}

}
