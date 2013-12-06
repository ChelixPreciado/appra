<?php 
	if(isset($_POST["geometry"])) {
		$geometry    = $_POST["geometry"];
		$coordinates = $geometry["coordinates"];
		
		foreach($coordinates as $point) {
			var_dump($point);
		}
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Leaflet.draw drawing and editing tools</title>

	<link rel="stylesheet" href="libs/leaflet.css" />
	<link rel="stylesheet" href="dist/leaflet.draw.css" />
	
	<script src="http://codeorigin.jquery.com/jquery-2.0.3.min.js"></script>
	<script src="libs/leaflet-src.js"></script>

	<script src="src/Leaflet.draw.js"></script>

	<script src="src/edit/handler/Edit.Poly.js"></script>
	<script src="src/edit/handler/Edit.SimpleShape.js"></script>
	<script src="src/edit/handler/Edit.Circle.js"></script>
	<script src="src/edit/handler/Edit.Rectangle.js"></script>

	<script src="src/draw/handler/Draw.Feature.js"></script>
	<script src="src/draw/handler/Draw.Polyline.js"></script>
	<script src="src/draw/handler/Draw.Polygon.js"></script>
	<script src="src/draw/handler/Draw.SimpleShape.js"></script>
	<script src="src/draw/handler/Draw.Rectangle.js"></script>
	<script src="src/draw/handler/Draw.Circle.js"></script>
	<script src="src/draw/handler/Draw.Marker.js"></script>

	<script src="src/ext/LatLngUtil.js"></script>
	<script src="src/ext/GeometryUtil.js"></script>
	<script src="src/ext/LineUtil.Intersect.js"></script>
	<script src="src/ext/Polyline.Intersect.js"></script>
	<script src="src/ext/Polygon.Intersect.js"></script>

	<script src="src/Control.Draw.js"></script>
	<script src="src/Tooltip.js"></script>
	<script src="src/Toolbar.js"></script>

	<script src="src/draw/DrawToolbar.js"></script>
	<script src="src/edit/EditToolbar.js"></script>
	<script src="src/edit/handler/EditToolbar.Edit.js"></script>
	<script src="src/edit/handler/EditToolbar.Delete.js"></script>
</head>
<body>
	<div id="map" style="width: 800px; height: 600px; border: 1px solid #ccc"></div>
	<script>
		var cloudmadeUrl = 'http://{s}.tile.cloudmade.com/BC9A493B41014CAABB98F0471D759707/997/256/{z}/{x}/{y}.png',
			cloudmade = new L.TileLayer(cloudmadeUrl, {maxZoom: 18}),
			map = new L.Map('map', {layers: [cloudmade], center: new L.LatLng(-37.7772, 175.2756), zoom: 15 });

		var drawnItems = new L.FeatureGroup();
		map.addLayer(drawnItems);

		// Set the title to show on the polygon button
		L.drawLocal.draw.toolbar.buttons.polygon = 'Dibuja un poligono sexy!';

		var drawControl = new L.Control.Draw({
			position: 'topright',
			draw: {
				polyline: false,
				polygon: {
					allowIntersection: false,
					showArea: true,
					drawError: {
						color: '#b00b00',
						timeout: 1000
					},
					shapeOptions: {
						color: '#bada55'
					}
				},
				circle: false,
				marker: false
			},
			edit: {
				featureGroup: drawnItems,
				remove: false
			}
		});
		
		map.addControl(drawControl);

		map.on('draw:created', function (e) {
			
			var type = e.layerType,
				layer = e.layer;

			if (type === 'marker') {
				layer.bindPopup('A popup!');
			}
			
			drawnItems.clearLayers();
			drawnItems.addLayer(layer);
			
			var geoJSON = layer.toGeoJSON();
			console.log(geoJSON);
			
			$.ajax({
				cache: false,
				type: 'POST',              
				url: 'index.php',
				dataType: 'json',
				data: geoJSON,
				success: function () { alert("success"); },
				error: function (response) {
					alert("error: " + response.responseText);
				}
			});
		});

	</script>
</body>
</html>
