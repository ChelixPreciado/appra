<!DOCTYPE html>
<html lang="<?php print get("webLang"); ?>">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<title><?php print $this->getTitle(); ?></title>
		
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript" id="jquery"></script>
		<script src='//api.tiles.mapbox.com/mapbox.js/v1.2.0/mapbox.js'></script>
		<script src="<?php print $this->themePath; ?>/js/apisamples.js"></script>
		<script src="<?php print $this->themePath; ?>/js/jquery_functions.js"></script>
		<script src="<?php print $this->themePath; ?>/js/jquery.ba-bbq.js"></script>
	    <link href='//api.tiles.mapbox.com/mapbox.js/v1.2.0/mapbox.css' rel='stylesheet' />
	    <link href='<?php print $this->themePath; ?>/css/style.css' rel='stylesheet' />
	    <!--[if lte IE 8]>
			<link href='//api.tiles.mapbox.com/mapbox.js/v1.2.0/mapbox.ie.css' rel='stylesheet' >
		<![endif]-->
	</head>
	<body>
