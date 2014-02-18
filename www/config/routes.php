<?php
/**
 * Access from index.php:
 */
if(!defined("_access")) {
	die("Error: You don't have permission to access here...");
}

$routes = array(
	0 => array(
		"pattern"	  => "/^get/",
		"application" => "api",
		"controller"  => "api",
		"method"	  => "get",
		"params"	  => array(segment(1))
	),
	1 => array(
		"pattern"	  => "/^api-draw/",
		"application" => "api",
		"controller"  => "api",
		"method"	  => "getNearResultsDraw",
		"params"	  => array(segment(1))
	),
	2 => array(
		"pattern"	  => "/^api/",
		"application" => "api",
		"controller"  => "api",
		"method"	  => "getNearResults",
		"params"	  => array(segment(1), segment(2), segment(3), segment(4))
	),
	3 => array(
		"pattern"	  => "/^heat-map/",
		"application" => "api",
		"controller"  => "api",
		"method"	  => "getHeatMapDensity",
		"params"	  => array(segment(1), segment(2), segment(3))
	)
);
