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
		success: function () {  },
		error: function (response) {
			
		}
	});
});
