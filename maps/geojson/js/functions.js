$(document).ready( function () {
	$("#logo").click( function () {
		$("#sidebar").toggleClass("sidebar-change");
		$(".leaflet-control").toggleClass("leaflet-control-change");
		$("#logo").toggleClass("logo-change");
	});
	
	$(".ul-layers li").click( function () {
		$(this).toggleClass("l-show");
		getResults(map.getBounds(), map._zoom);
	});
});
