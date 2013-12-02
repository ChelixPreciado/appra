var map     = L.mapbox.map('map', 'examples.map-9ijuk24y').setView([19.4297430000517, -99.1283830003488], 15);
var bounds  = map.getBounds();

var densityGroup   = new L.LayerGroup();
var schoolsGroup   = new L.LayerGroup();
var tianguisGroup  = new L.LayerGroup();
var resultsGroup   = new L.LayerGroup();
var markersResults = new L.MarkerClusterGroup();

map.on('movestart',       function (e) { removeLayers(); });
map.on('moveend',         function (e) { getResults(map.getBounds(), e.target._zoom); });
/*
map.on('dragstart',       function (e) { console.log('    [ dragstart'); });
map.on('dragend',         function (e) { console.log('    ] dragend'); });
map.on('zoomstart',       function (e) { console.log('    ( zoomstart'); });
map.on('zoomend',         function (e) { console.log('    ) zoomend'); });
map.on('viewreset',       function (e) { console.log('      viewreset'); });
map.on('autopanstart',    function (e) { console.log('      autopanstart'); });
*/

function removeLayers() {
	$(".loading").show();
	
	resultsGroup.clearLayers();
	markersResults.clearLayers();
	densityGroup.clearLayers();
	schoolsGroup.clearLayers();
	tianguisGroup.clearLayers();
}
 
function getResults(bounds, zoom) {
	if(zoom > 14) {
		$(".loading").show();
		
		$.ajax({
			url: '/appra/index.php/api/'+bounds._southWest.lat+','+bounds._northEast.lng+'/'+bounds._northEast.lat+','+bounds._southWest.lng+'/tianguis,schools,density',
			dataType: 'json',
			contentType: "application/json; charset=utf-8",
			success: function load(d) {	
				$(".loading").hide();
				
				/*HeatMap*/	
				var density = d.density;
				var heatmap =  L.geoJson(density, {
					style: function(feature) {
						densidad = feature.properties.densidad;
						
						if(densidad > -1    && densidad < 1000)  return { weight: 0, color: "#ffebd6" };
						if(densidad > 999   && densidad < 2000)  return { weight: 0, color: "#f5cbae" };
						if(densidad > 1999  && densidad < 5000)  return { weight: 0, color: "#eba988" };
						if(densidad > 4999  && densidad < 10000) return { weight: 0, color: "#e08465" };
						if(densidad > 9999  && densidad < 20000) return { weight: 0, color: "#d65d45" };
						if(densidad > 19999 && densidad < 30000) return { weight: 0, color: "#cc3527" };
						if(densidad > 29999) return { weight: 0, color: "#c40a0a" };
					}
				});
				
				densityGroup.addLayer(heatmap);
				densityGroup.addTo(map);
				
				/*Results*/
				var resultIcon = L.icon({
					iconUrl: 'icons/rocket-24.png',
					iconRetinaUrl: 'icons/rocket-24@2x.png',
					iconSize: [24, 24]
				});
				
				var results = d.results;
				for (x in results) {
					marker = L.marker([results[x].lat, results[x].lon], {icon: resultIcon}).bindPopup(results[x].address);
					markersResults.addLayer(marker);
				}
				
				markersResults.addTo(map);
				
				
				/*Schools*/
				var schoolIcon = L.icon({
					iconUrl: 'icons/school-24.png',
					iconRetinaUrl: 'icons/school-24@2x.png',
					iconSize: [24, 24]
				});
				
				var schools = d.schools;
				for (x in schools) {
					marker = L.marker([schools[x].lat, schools[x].lon], {icon: schoolIcon}).bindPopup(schools[x].title);
					schoolsGroup.addLayer(marker);
				}
				
				schoolsGroup.addTo(map);
				
				/*Tianguis*/
				var tianguisIcon = L.icon({
					iconUrl: 'icons/grocery-24.png',
					iconRetinaUrl: 'icons/grocery-24@2x.png',
					iconSize: [24, 24]
				});
				
				var tianguis = d.tianguis;
				for (x in tianguis) {
					marker = L.marker([tianguis[x].lat, tianguis[x].lon], {icon: tianguisIcon}).addTo(map).bindPopup(tianguis[x].title);
					tianguisGroup.addLayer(marker);
				}
				
				tianguisGroup.addTo(map);
			}
		});
	} else {
		$(".loading").hide();
	}
}

$(document).ready( function () {
	$(".loading").hide();
	getResults(bounds, 15);
});
