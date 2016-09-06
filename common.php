<?php

/**
 * common.php
 *
 * @author Kosuke Shibuya <kosuke@jlamp.net>
 * @since 2016/09/05
 */

namespace KSBBS;

date_default_timezone_set('Asia/Tokyo');

if (version_compare(phpversion(), '5.4.0', '<')) {
	die('PHP Version is too OLD!');
}

if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

require 'autoload.php';

function h($string)
{
	return htmlspecialchars($string, ENT_QUOTES, 'utf-8');
}
