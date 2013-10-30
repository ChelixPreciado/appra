<?php
/**
 * Access from index.php:
 */
if(!defined("_access")) {
	die("Error: You don't have permission to access here...");
}

$routes = array(
	0 => array(
		"pattern"	  => "/^api/",
		"application" => "api",
		"controller"  => "api",
		"method"	  => "getNearResults",
		"params"	  => array(segment(1), segment(2), segment(3))
	)
);
