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
	die(sprintf('PHP Version %s is too OLD! You should use 5.4 above.'
			, phpversion()
		)
	);
}

if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

require 'autoload.php';
require 'config.php';

// DBが存在しない時に作成する
if (!file_exists(DB::DBNAME)) {
	DB::createTable();
}

/**
 * Escape against XSS
 * @param string $string
 * @return string
 */
function h($string)
{
	return htmlspecialchars($string, ENT_QUOTES, 'utf-8');
}

/**
 * pagination
 * @param int $intPage
 * @param int $total
 * @return string
 */
function pagination($total)
{
	$rowCount = 5;
	if ($total < 1) {
		return;
	}
	$intPage = filter_input(INPUT_GET, 'page');
	$query = (is_array(filter_input_array(INPUT_GET))) ?
		filter_input_array(INPUT_GET) : array();
	if (isset($query['page'])) {
		unset($query['page']);
	}
	$querystring = http_build_query($query);
	$limit = $rowCount;
	$placeholder = "<a href=\"?page=%d&%s\" class=\"btn%s\">%s</a> ";

	$page = ($intPage == 0) ? 1 : $intPage;

	// 最大ページ数
	$maxPage = ceil($total / $limit);

	$html = '';

	$html .= sprintf($placeholder
		, 1
		, $querystring
		, ($page > 1) ? ' btn-primary' : ' btn-default disabled'
		, '&laquo;'
	);
	$html .= sprintf($placeholder
		, $page - 1
		, $querystring
		, ($page > 1) ? ' btn-primary' : ' btn-default disabled'
		, PREV
	);
	$html .= sprintf($placeholder
		, $page + 1
		, $querystring
		, ($page < $maxPage) ? ' btn-primary' : ' btn-default disabled'
		, NEXT
	);
	$html .= sprintf($placeholder
		, $maxPage
		, $querystring
		, ($page < $maxPage) ? ' btn-primary' : ' btn-default disabled'
		, '&raquo;'
	);

	return $html;
}
