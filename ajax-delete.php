<?php

/**
 * ajax-delete.php
 *
 * @author Kosuke Shibuya <kosuke@jlamp.net>
 * @since 2016/09/10
 */

namespace KSBBS;

require 'common.php';

$request_method = filter_input(INPUT_SERVER, 'REQUEST_METHOD');

switch ($request_method) {
	case 'POST':
		$method = INPUT_POST;
		break;
	case 'GET':
		$method = INPUT_GET;
		break;
}

$id = filter_input($method, 'id');
$delkey = filter_input($method, 'delkey');

header('Content-type: application/json');

$res = [
	'response' => BBS::delete($id, $delkey)
];

echo json_encode($res);
