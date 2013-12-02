var map     = L.mapbox.map('map', 'examples.map-9ijuk24y').setView([19.4297430000517, -99.1283830003488], 14);
var bounds  = map.getBounds();
var heatmap = "";

map.on('movestart',       function (e) { removeLayers(); });
map.on('moveend',         function (e) { getResults(map.getBounds()); });
map.on('dragstart',       function (e) { console.log('    [ dragstart'); });
map.on('dragend',         function (e) { console.log('    ] dragend'); });
map.on('zoomstart',       function (e) { console.log('    ( zoomstart'); });
map.on('zoomend',         function (e) { console.log('    ) zoomend'); });
map.on('viewreset',       function (e) { console.log('      viewreset'); });
map.on('autopanstart',    function (e) { console.log('      autopanstart'); });

function removeLayers() {
	map.removeLayer(heatmap);
}
 
function getResults(bounds) {
	$.ajax({
		url: '/appra/index.php/api/'+bounds._southWest.lat+','+bounds._northEast.lng+'/'+bounds._northEast.lat+','+bounds._southWest.lng+'/tianguis,schools,density',
		dataType: 'json',
		contentType: "application/json; charset=utf-8",
		success: function load(d) {	
			/*HeatMap*/	
			var density = d.density;
			heatmap =  L.geoJson(density, {
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
			}).addTo(map);
			
			
			/*Results*/
			var resultIcon = L.icon({
				iconUrl: 'icons/rocket-24.png',
				iconRetinaUrl: 'icons/rocket-24@2x.png',
				iconSize: [24, 24]
			});
			
			/*
			var results = d.results;
			for (x in results) {
				L.marker([results[x].lat, results[x].lon], {icon: resultIcon}).addTo(map).bindPopup(results[x].address);
			}
			*/
			
			/*Schools*/
			var schoolIcon = L.icon({
				iconUrl: 'icons/school-24.png',
				iconRetinaUrl: 'icons/school-24@2x.png',
				iconSize: [24, 24]
			});
			
			var schools = d.schools;
			for (x in schools) {
				L.marker([schools[x].lat, schools[x].lon], {icon: schoolIcon}).addTo(map).bindPopup(schools[x].title);
			}
			
			/*Tianguis*/
			var tianguisIcon = L.icon({
				iconUrl: 'icons/grocery-24.png',
				iconRetinaUrl: 'icons/grocery-24@2x.png',
				iconSize: [24, 24]
			});
			
			var tianguis = d.tianguis;
			for (x in tianguis) {
				L.marker([tianguis[x].lat, tianguis[x].lon], {icon: tianguisIcon}).addTo(map).bindPopup(tianguis[x].title);
			}
		}
	});
}

$(document).ready( function () {
	getResults(bounds);
});
