$(document).ready( function () {
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
		var zoom = map._zoom;
		
		if(zoom > 14) {
			var type = e.layerType,
				layer = e.layer;

			if (type === 'marker') {
				layer.bindPopup('A popup!');
			}
			
			removeLayers();
			drawnItems.addLayer(layer);
			
			var geoJSON = layer.toGeoJSON();
			//console.log(geoJSON);
			
			/*each layers show*/
			var layers = "";
			$.each($(".l-show"), function(key, value) { layers = layers + $(this).attr("id") + ","; });
			layers = layers.replace(/,+$/,'');
			
			$.ajax({
				cache: false,
				type: 'POST',              
				url: '/rahabit/index.php/api-draw/' + layers,
				dataType: 'json',
				data: geoJSON,
				success: function (d) {
					printResults(d);
					
					$(".loading").hide();
				},
				error: function (response) {
					$(".loading").hide();
				}
			});
		} else {
			$(".loading").hide();
		}
	});
});
