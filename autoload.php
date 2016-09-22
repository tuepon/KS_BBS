<?php

namespace KSBBS;

/**
 * autoload.php
 *
 * @author Kosuke Shibuya <kosuke@jlamp.net>
 * @since 2016/09/05
 */
function autoload_function($classname)
{
	$path = sprintf("%s%s.class.php"
		, 'classes/'
		, str_replace([__NAMESPACE__, '\\'], ['', '/'], $classname)
	);
	require $path;
}

spl_autoload_register("KSBBS\autoload_function");
