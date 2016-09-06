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

function pagination($page, $total)
{
	$rowCount = 5;
	if ($total < 1) {
		//return;
	}
	$query = (is_array(filter_input_array(INPUT_GET))) ?
		filter_input_array(INPUT_GET) : array();
	if (isset($query['page'])) {
		unset($query['page']);
	}
	$querystring = http_build_query($query);
	$limit = $rowCount;
	$placeholder = "<a href=\"?page=%d&%s\" class=\"btn btn-primary%s\">%s</a> ";

	// 最大ページ数
	$maxPage = ceil($total / $limit);

	$html = '';

	$html .= sprintf($placeholder
		, 1
		, $querystring
		, ($page > 1) ? '' : ' disabled'
		, '&laquo;'
	);
	$html .= sprintf($placeholder
		, $page - 1
		, $querystring
		, ($page > 1) ? '' : ' disabled'
		, '前へ'
	);
	$html .= sprintf($placeholder
		, $page + 1
		, $querystring
		, ($page < $maxPage) ? '' : ' disabled'
		, '次へ'
	);
	$html .= sprintf($placeholder
		, $maxPage
		, $querystring
		, ($page < $maxPage) ? '' : ' disabled'
		, '&raquo;'
	);

	return $html;
}
