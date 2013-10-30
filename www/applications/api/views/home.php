<div id='map-ui'>
    <ul>
		<?php if(is_array($metro)) { ?>
			<li><span class='filter-e active' id='filter-metro'>Metro</span></li>
        <?php } ?>
        
        <?php if(is_array($metrobus)) { ?>
			<li><span class="filter-e" id='filter-metrobus'>Metrobus</span></li>
        <?php } ?>
        
        <?php if(is_array($ste)) { ?>
			<li><span class="filter-e" id='filter-ste'>STE</span></li>
        <?php } ?>
        
        <?php if(is_array($sub)) { ?>
			<li><span class='filter-e' id='filter-sub'>SUB</span></li>
        <?php } ?>
    </ul>
</div>

<div id="info-marker">
	<p class="title-marker p-info-marker">Mapa de transporte de la ciudad de México</p>
</div>

<div id="info-marker2">
	<form name="search" action="" method="POST">
		<input class="text" type="text" name="text_search" value="<?php echo (isset($_POST["text_search"])) ? $_POST["text_search"] : "";?>">
		<input class="submit" type="submit" name="send" value="buscar">
	</form>
</div>

<?php if(!is_array($metro) and !is_array($metrobus) and !is_array($ste) and !is_array($sub)) { ?>
	<div id="info-marker3">
		<p class="title-marker p-info-marker color-red">No hay resultados con esta busqueda - <a href="<?php echo get("webURL");?>" title="Ver todos">Ver todos</a></p>
	</div>
<?php } ?>

<div id="connect">
	<img id="connect-button" src="<?php print $this->themePath; ?>/css/images/connect-black.png" alt="Foursquare" />
</div>

<div id='map'></div>

<script type='text/javascript'>
//Mapbox	
var map = L.mapbox.map('map', 'caarloshugo.map-1l67y9mj', { minZoom: 10, maxZoom:19, }).setView([19.41,-99.1], 12);

//Foursquare
var config = {
	apiKey: 'J5DRTJ3O5O2Z10SJ4MX4JTMDTGJZWG2LBD0HN44VC23KFKMD',
	authUrl: 'https://foursquare.com/',
	apiUrl: 'https://api.foursquare.com/'
};

if($.bbq.getState('access_token')) {	
	//If there is a token in the state, consume it
	var token = $.bbq.getState('access_token');
	$.bbq.pushState({}, 2);
} 
	
function foursquare_connect() {
	if($.bbq.getState('access_token')) {	
		//If there is a token in the state, consume it
		var token = $.bbq.getState('access_token');
		$.bbq.pushState({}, 2);
	} else if ($.bbq.getState('error')) {
	} else {
		doAuthRedirect();
	}
}
	
/* Attempt to retrieve access token from URL. */
function doAuthRedirect() {
	var redirect = window.location.href.replace(window.location.hash, '');
	var url = config.authUrl + 'oauth2/authenticate?response_type=token&client_id=' + config.apiKey +
	'&redirect_uri=' + encodeURIComponent(redirect) +
	'&state=' + encodeURIComponent($.bbq.getState('req') || 'users/self');
	window.location.href = url;
};

function getVenues(lat, lon) {
	$.getJSON(config.apiUrl + 'v2/venues/explore?ll=' + lat + ',' + lon + '&limit=15&radius=300&time=any&day=any&oauth_token=' + window.token + '&v=2013071', {}, function(data) {
      
      $(".foursquare-marker").remove();
      
      venues = data['response']['groups'][0]['items'];
    
      /* Place marker for each venue. */
      for(var i = 0; i < venues.length; i++) {
        /* Get marker's location */
        var latLng = new L.LatLng(
          venues[i]['venue']['location']['lat'],
          venues[i]['venue']['location']['lng']
        );
		
        var marker = new L.Marker(latLng, {icon: L.icon({ iconUrl: '<?php print $this->themePath; ?>/css/images/marker-icon.png', iconSize: [25, 41], iconAnchor: [0, 0], popupAnchor: [0, -25], className : 'foursquare-marker' })})
          .bindPopup(venues[i]['venue']['name'], { closeButton: false })
          .on('mouseover', function(e) { this.openPopup(); })
          .on('mouseout', function(e) { this.closePopup(); });
        map.addLayer(marker);
      }
    });
    
    map.setView([lat, lon], 16, {pan: {animate: true}});
    
    return false;
}



//Mapbox
// map.dragging.disable();
map.touchZoom.disable();
map.doubleClickZoom.disable();
// map.scrollWheelZoom.disable();

var marker = "";

<?php if(is_array($metro)) { ?>
	<?php foreach($metro as $route) { ?>
		
		<?php if($search) { ?>
			map.setView([<?php echo $route["stops"][0]["stop_lat"];?>, <?php echo $route["stops"][0]["stop_lon"];?>], 16, {pan: {animate: true}});
		<?php }?>
		
		<?php foreach($route["stops"] as $stop) { ?>
			marker = L.marker([<?php echo $stop["stop_lat"];?>, <?php echo $stop["stop_lon"];?>], {
				icon: L.icon({
					iconUrl     : '<?php print $this->themePath; ?>/css/renders/marker-stroked-24.png',
					iconSize    : [18, 18],
					iconAnchor  : [0, 0],
					popupAnchor : [0, 0],
					className   : 'agency <?php echo $route["agency_id"];?>'
				})
			}).addTo(map);
			
			marker.on('click', function(e) {
				$(".title-marker").html("<?php echo $stop["stop_name"];?>" + ' - <span class="foursquare-buttton" onclick="getVenues(<?php echo $stop["stop_lat"];?>,<?php echo $stop["stop_lon"];?>)">Lugares cercanos</span>');
			});
			
			marker.on('mouseover', function(e) {
				$(".title-marker").html("<?php echo $stop["stop_name"];?>");
			});
		<?php } ?>
	<?php } ?>
<?php } ?>

<?php if(is_array($metrobus)) { ?>
	<?php foreach($metrobus as $route) { ?>
		<?php foreach($route["stops"] as $stop) { ?>
			L.marker([<?php echo $stop["stop_lat"];?>, <?php echo $stop["stop_lon"];?>], {
				icon: L.icon({
					iconUrl     : '<?php print $this->themePath; ?>/css/renders/marker-stroked-24.png',
					iconSize    : [18, 18],
					iconAnchor  : [0, 0],
					popupAnchor : [0, 0],
					className   : 'agency <?php echo $route["agency_id"];?>'
				})
			}).addTo(map).bindPopup('<p><?php echo $stop["stop_name"];?></p>');
		<?php } ?>
	<?php } ?>
<?php } ?>

<?php if(is_array($ste)) { ?>
	<?php foreach($ste as $route) { ?>
		<?php foreach($route["stops"] as $stop) { ?>
			L.marker([<?php echo $stop["stop_lat"];?>, <?php echo $stop["stop_lon"];?>], {
				icon: L.icon({
					iconUrl     : '<?php print $this->themePath; ?>/css/renders/marker-stroked-24.png',
					iconSize    : [18, 18],
					iconAnchor  : [0, 0],
					popupAnchor : [0, 0],
					className   : 'agency <?php echo $route["agency_id"];?>'
				})
			}).addTo(map).bindPopup('<p><?php echo $stop["stop_name"];?></p>');
		<?php } ?>
	<?php } ?>
<?php } ?>

<?php if(is_array($sub)) { ?>
	<?php foreach($sub as $route) { ?>
		<?php foreach($route["stops"] as $stop) { ?>
			L.marker([<?php echo $stop["stop_lat"];?>, <?php echo $stop["stop_lon"];?>], {
				icon: L.icon({
					iconUrl     : '<?php print $this->themePath; ?>/css/renders/marker-stroked-24.png',
					iconSize    : [18, 18],
					iconAnchor  : [0, 0],
					popupAnchor : [0, 0],
					className   : 'agency <?php echo $route["agency_id"];?>'
				})
			}).addTo(map).bindPopup('<p><?php echo $stop["stop_name"];?></p>');
		<?php } ?>
	<?php } ?>
<?php } ?> 
</script>


