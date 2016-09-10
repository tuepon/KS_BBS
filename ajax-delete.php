<?php

/**
 * ajax-delete.php
 *
 * @author Kosuke Shibuya <kosuke@jlamp.net>
 * @since 2016/09/10
 */

namespace KSBBS;

require 'common.php';

$id = filter_input(INPUT_GET, 'id');
$delkey = filter_input(INPUT_GET, 'delkey');

header('Content-type: application/json');

$res = [
	'response' => BBS::delete($id, $delkey)
];

echo json_encode($res);
