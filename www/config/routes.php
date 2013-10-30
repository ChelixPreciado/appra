<?php
/**
 * Access from index.php:
 */
if(!defined("_access")) {
	die("Error: You don't have permission to access here...");
}

$routes = array(
	0 => array(
		"pattern"	  => "/^schools/",
		"application" => "api",
		"controller"  => "api",
		"method"	  => "getNearSchools",
		"params"	  => array(segment(1), segment(2))
	)
);
