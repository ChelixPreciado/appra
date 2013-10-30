$(document).ready( function () {
	$("#filter-metro").click( function() {
		$(".filter-e").removeClass("active");
		$("#filter-metro").addClass("active");
		
		$(".agency").hide();
		$(".METRO").show();
	});
	
	$("#filter-metrobus").click( function() {
		$(".filter-e").removeClass("active");
		$("#filter-metrobus").addClass("active");
		
		$(".agency").hide();
		$(".MB").show();
	});
	
	$("#filter-rtp").click( function() {
		$(".filter-e").removeClass("active");
		$("#filter-rtp").addClass("active");
		
		$(".agency").hide();
		$(".RTP").show();
	});
	
	$("#filter-ste").click( function() {
		$(".filter-e").removeClass("active");
		$("#filter-ste").addClass("active");
		
		$(".agency").hide();
		$(".STE").show();
	});
	
	$("#filter-sub").click( function() {
		$(".filter-e").removeClass("active");
		$("#filter-sub").addClass("active");
		
		$(".agency").hide();
		$(".SUB").show();
	});
	
	$(".agency").hide();
	$(".METRO").show();
	
	//Foursquare
	$("#connect-button").click( function() {
		foursquare_connect();
	});
});
