var map     = L.mapbox.map('map', 'examples.map-9ijuk24y').setView([19.4297430000517, -99.1283830003488], 15);
var bounds  = map.getBounds();


//Base Layers
var baseLayer   = L.mapbox.tileLayer('examples.map-9ijuk24y');
var otherLayer1 = L.mapbox.tileLayer('caarloshugo.gedde4dk');
var otherLayer2 = L.mapbox.tileLayer('examples.map-y7l23tes');

var baseLayers = {
	"Base" : baseLayer,
	"Dark" : otherLayer2,
	"Other": otherLayer1
};

L.control.layers(baseLayers).addTo(map);


//Layer Groups
var densityGroup     = new L.LayerGroup();
var schoolsGroup     = new L.LayerGroup();
var tianguisGroup    = new L.LayerGroup();
var mallsGroup       = new L.LayerGroup();
var resultsGroup     = new L.LayerGroup();
var restaurantsGroup = new L.LayerGroup();
var marketsGroup     = new L.LayerGroup();
var fireGroup        = new L.LayerGroup();
var markersResults   = new L.MarkerClusterGroup({ disableClusteringAtZoom: 18 });

map.on('movestart',       function (e) { removeLayers(); });
map.on('moveend',         function (e) { getResults(map.getBounds(), e.target._zoom); });

/*Remove layers*/
function removeLayers() {
	$(".loading").show();
	
	resultsGroup.clearLayers();
	markersResults.clearLayers();
	densityGroup.clearLayers();
	schoolsGroup.clearLayers();
	tianguisGroup.clearLayers();
	mallsGroup.clearLayers();
	restaurantsGroup.clearLayers();
	marketsGroup.clearLayers();
	fireGroup.clearLayers();
}
 
function getResults(bounds, zoom) {
	if(zoom > 14) {
		$(".loading").show();
		
		/*each layers show*/
		var layers = "";
		$.each($(".l-show"), function(key, value) { layers = layers + $(this).attr("id") + ","; });
		layers = layers.replace(/,+$/,'');
		
		$.ajax({
			url: '/appra/index.php/api/'+bounds._southWest.lat+','+bounds._northEast.lng+'/'+bounds._northEast.lat+','+bounds._southWest.lng + '/' + layers,
			dataType: 'json',
			contentType: "application/json; charset=utf-8",
			success: function load(d) {	
				$(".loading").hide();
				
				/*HeatMap*/	
				var density = d.density;
				var heatmap =  L.geoJson(density, {
					style: function(feature) {
						densidad = feature.properties.densidad;
						
						if(densidad > -1    && densidad < 1000)  return { fillOpacity: 0.7, opacity: 0.9, weight: 0, color: "#ffebd6" };
						if(densidad > 999   && densidad < 2000)  return { fillOpacity: 0.7, opacity: 0.9, weight: 0, color: "#f5cbae" };
						if(densidad > 1999  && densidad < 5000)  return { fillOpacity: 0.7, opacity: 0.9, weight: 0, color: "#eba988" };
						if(densidad > 4999  && densidad < 10000) return { fillOpacity: 0.7, opacity: 0.9, weight: 0, color: "#e08465" };
						if(densidad > 9999  && densidad < 20000) return { fillOpacity: 0.7, opacity: 0.9, weight: 0, color: "#d65d45" };
						if(densidad > 19999 && densidad < 30000) return { fillOpacity: 0.7, opacity: 0.9, weight: 0, color: "#cc3527" };
						if(densidad > 29999) return { fillOpacity: 0.7, opacity: 0.9, weight: 0, color: "#c40a0a" };
					}
				});
				
				densityGroup.addLayer(heatmap);
				densityGroup.addTo(map);
				
				/*Results*/
				var resultIcon = L.icon({
					iconUrl: 'icons/home-26.png',
					iconRetinaUrl: 'icons/home-26.png',
					iconSize: [26, 26]
				});
				
				var results = d.results;
				
				if(zoom > 17) {
					for (x in results) {
						marker = L.marker([results[x].lat, results[x].lon]).bindPopup(results[x].address);
						resultsGroup.addLayer(marker);
					}
					
					resultsGroup.addTo(map);
				} else {
					for (x in results) {
						marker = L.marker([results[x].lat, results[x].lon]).bindPopup(results[x].address);
						markersResults.addLayer(marker);
					}
					
					markersResults.addTo(map);
				}
				
				
				/*Schools*/
				var schoolIcon = L.icon({
					iconUrl: 'icons/college-24.png',
					iconRetinaUrl: 'icons/college-24@2x.png',
					iconSize: [24, 24]
				});
				
				var schools = d.schools;
				for (x in schools) {
					marker = L.marker([schools[x].lat, schools[x].lon], {icon: schoolIcon});
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
					marker = L.marker([tianguis[x].lat, tianguis[x].lon], {icon: tianguisIcon});
					tianguisGroup.addLayer(marker);
				}
				
				tianguisGroup.addTo(map);
				
				
				/*Malls*/
				var mallsIcon = L.icon({
					iconUrl: 'icons/shop-24.png',
					iconRetinaUrl: 'icons/shop-24@2x.png',
					iconSize: [24, 24]
				});
				
				var malls = d.malls;
				for (x in malls) {
					marker = L.marker([malls[x].lat, malls[x].lon], {icon: mallsIcon});
					mallsGroup.addLayer(marker);
				}
				
				mallsGroup.addTo(map);
				
				
				/*Markets*/
				var marketsIcon = L.icon({
					iconUrl: 'icons/grocery-24.png',
					iconRetinaUrl: 'icons/grocery-24@2x.png',
					iconSize: [24, 24]
				});
				
				var markets = d.markets;
				for (x in markets) {
					marker = L.marker([markets[x].lat, markets[x].lon], {icon: marketsIcon});
					marketsGroup.addLayer(marker);
				}
				
				marketsGroup.addTo(map);
				
				
				/*Restaurants*/
				var restaurantsIcon = L.icon({
					iconUrl: 'icons/restaurant-24.png',
					iconRetinaUrl: 'icons/restaurant-24@2x.png',
					iconSize: [24, 24]
				});
				
				var restaurants = d.restaurants;
				for (x in restaurants) {
					marker = L.marker([restaurants[x].lat, restaurants[x].lon], {icon: restaurantsIcon});
					restaurantsGroup.addLayer(marker);
				}
				
				restaurantsGroup.addTo(map);
				
				
				/*Fire stations*/
				var fireIcon = L.icon({
					iconUrl: 'icons/fire-station-24.png',
					iconRetinaUrl: 'icons/fire-station-24@2x.png',
					iconSize: [24, 24]
				});
				
				var fire_stations = d.fire_stations;
				for (x in fire_stations) {
					marker = L.marker([fire_stations[x].lat, fire_stations[x].lon], {icon: fireIcon});
					fireGroup.addLayer(marker);
				}
				
				fireGroup.addTo(map);
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
